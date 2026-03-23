<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    /**
     * Profile type constants for the map legend
     */
    public const PROFILE_ADHERENT = 'adherent';
    public const PROFILE_ANCIEN_ADHERENT = 'ancien_adherent';
    public const PROFILE_CONTACT = 'contact';
    public const PROFILE_ORGANISME = 'organisme';

    /**
     * Get profile types with labels for filters
     */
    public static function getProfileTypes(): array
    {
        return [
            self::PROFILE_ADHERENT => 'Adhérent (à jour)',
            self::PROFILE_ANCIEN_ADHERENT => 'Ancien adhérent',
            self::PROFILE_CONTACT => 'Contact',
            self::PROFILE_ORGANISME => 'Organisme',
        ];
    }

    /**
     * Determine the profile type for a member
     */
    public function getProfileType(Member $member): string
    {
        // Check if it's an organization (not individual)
        if ($member->contact_type && $member->contact_type !== Member::TYPE_INDIVIDUEL) {
            return self::PROFILE_ORGANISME;
        }

        // Check membership status
        if ($member->isCurrentMember()) {
            return self::PROFILE_ADHERENT;
        }

        if ($member->wasEverMember()) {
            return self::PROFILE_ANCIEN_ADHERENT;
        }

        return self::PROFILE_CONTACT;
    }

    /**
     * Display the interactive map.
     */
    public function index()
    {
        // Profile types for filter
        $profileTypes = self::getProfileTypes();

        // Get distinct departments (first 2 digits of postal code)
        $departments = Member::whereNotNull('postal_code')
            ->where('postal_code', '!=', '')
            ->selectRaw("DISTINCT LEFT(postal_code, 2) as dept")
            ->orderBy('dept')
            ->pluck('dept')
            ->filter()
            ->values();

        // Stats
        $stats = [
            'total' => Member::count(),
            'geolocated' => Member::whereNotNull('latitude')->whereNotNull('longitude')->count(),
            'not_geolocated' => Member::where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            })->count(),
        ];

        return view('admin.map.index', compact('profileTypes', 'departments', 'stats'));
    }

    /**
     * Get members data for the map (JSON API).
     */
    public function members(Request $request)
    {
        // We need memberships for profile type calculation
        $query = Member::with('memberships')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0);

        // Filter by department
        if ($request->filled('department')) {
            $depts = explode(',', $request->get('department'));
            $query->where(function ($q) use ($depts) {
                foreach ($depts as $dept) {
                    if ($dept === 'other') {
                        $q->orWhereNull('postal_code')
                          ->orWhere('postal_code', '');
                    } else {
                        $q->orWhere('postal_code', 'like', $dept . '%');
                    }
                }
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Radius search
        if ($request->filled('lat') && $request->filled('lng') && $request->filled('radius')) {
            $lat = (float) $request->get('lat');
            $lng = (float) $request->get('lng');
            $radius = (float) $request->get('radius'); // in km

            // Haversine formula for PostgreSQL
            $query->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) <= ?
            ", [$lat, $lng, $lat, $radius]);
        }

        // If radius search, add distance calculation
        $hasRadius = $request->filled('lat') && $request->filled('lng') && $request->filled('radius');

        if ($hasRadius) {
            $lat = (float) $request->get('lat');
            $lng = (float) $request->get('lng');

            $query->selectRaw("
                members.*,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) as distance
            ", [$lat, $lng, $lat])
            ->orderBy('distance');
        }

        $members = $query->get();

        // Filter by profile type (post-query since it's calculated)
        $profileTypeFilter = $request->get('profile_type');

        $result = $members->map(function ($m) use ($hasRadius) {
            $profileType = $this->getProfileType($m);

            $data = [
                'id' => $m->id,
                'name' => trim($m->first_name . ' ' . $m->last_name) ?: $m->last_name ?: 'Sans nom',
                'email' => $m->email,
                'phone' => $m->phone,
                'address' => $m->address,
                'postal_code' => $m->postal_code,
                'city' => $m->city,
                'country' => $m->country,
                'profile_type' => $profileType,
                'contact_type' => $m->contact_type,
                'status' => $m->status,
                'lat' => (float) $m->latitude,
                'lng' => (float) $m->longitude,
            ];

            if ($hasRadius && isset($m->distance)) {
                $data['distance'] = round($m->distance, 1);
            }

            return $data;
        });

        // Apply profile type filter if set
        if ($profileTypeFilter) {
            $types = explode(',', $profileTypeFilter);
            $result = $result->filter(fn($m) => in_array($m['profile_type'], $types));
        }

        return response()->json([
            'count' => $result->count(),
            'members' => $result->values(),
        ]);
    }

    /**
     * Geocode an address and return coordinates.
     */
    public function geocode(Request $request)
    {
        $request->validate(['address' => 'required|string']);

        $address = $request->get('address');

        try {
            $response = $this->nominatimRequest([
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'fr',
            ]);

            if ($response && count($response) > 0) {
                $result = $response[0];
                return response()->json([
                    'success' => true,
                    'lat' => (float) $result['lat'],
                    'lng' => (float) $result['lon'],
                    'display_name' => $result['display_name'],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de geocodage: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'Adresse non trouvee',
        ], 404);
    }

    /**
     * Make a request to Nominatim API.
     */
    private function nominatimRequest(array $params): ?array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'OREINA-Platform/1.0'
        ])
        ->withOptions([
            'verify' => false, // Disable SSL verification for local development
        ])
        ->timeout(10)
        ->get('https://nominatim.openstreetmap.org/search', $params);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Geocode a single member.
     */
    public function geocodeMember(Member $member)
    {
        if (empty($member->address) && empty($member->city)) {
            return response()->json([
                'success' => false,
                'message' => 'Adresse incomplete',
            ], 400);
        }

        $address = implode(', ', array_filter([
            $member->address,
            $member->postal_code,
            $member->city,
            $member->country ?: 'France',
        ]));

        try {
            $response = $this->nominatimRequest([
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response && count($response) > 0) {
                $result = $response[0];
                $member->update([
                    'latitude' => $result['lat'],
                    'longitude' => $result['lon'],
                ]);

                return response()->json([
                    'success' => true,
                    'lat' => (float) $result['lat'],
                    'lng' => (float) $result['lon'],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'Geocodage echoue',
        ], 404);
    }

    /**
     * Bulk geocode members without coordinates.
     */
    public function bulkGeocode(Request $request)
    {
        $limit = min((int) $request->get('limit', 50), 100);

        $members = Member::where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            })
            ->where(function ($q) {
                $q->whereNotNull('city')->where('city', '!=', '');
            })
            ->limit($limit)
            ->get();

        $geocoded = 0;
        $failed = 0;

        foreach ($members as $member) {
            $address = implode(', ', array_filter([
                $member->address,
                $member->postal_code,
                $member->city,
                $member->country ?: 'France',
            ]));

            try {
                $response = $this->nominatimRequest([
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ]);

                if ($response && count($response) > 0) {
                    $result = $response[0];
                    $member->update([
                        'latitude' => $result['lat'],
                        'longitude' => $result['lon'],
                    ]);
                    $geocoded++;
                } else {
                    $failed++;
                }

                // Rate limiting - Nominatim requires 1 request per second
                usleep(1100000);
            } catch (\Exception $e) {
                $failed++;
            }
        }

        $remaining = Member::where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            })
            ->where(function ($q) {
                $q->whereNotNull('city')->where('city', '!=', '');
            })
            ->count();

        return response()->json([
            'success' => true,
            'geocoded' => $geocoded,
            'failed' => $failed,
            'remaining' => $remaining,
        ]);
    }

    /**
     * Get statistics by department for the map sidebar.
     */
    public function stats()
    {
        // Get members grouped by department (first 2 digits of postal code)
        $stats = Member::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('postal_code')
            ->where('postal_code', '!=', '')
            ->selectRaw("LEFT(postal_code, 2) as department, COUNT(*) as count")
            ->groupBy('department')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        // French department names
        $deptNames = [
            '01' => 'Ain', '02' => 'Aisne', '03' => 'Allier', '04' => 'Alpes-de-Haute-Provence',
            '05' => 'Hautes-Alpes', '06' => 'Alpes-Maritimes', '07' => 'Ardeche', '08' => 'Ardennes',
            '09' => 'Ariege', '10' => 'Aube', '11' => 'Aude', '12' => 'Aveyron',
            '13' => 'Bouches-du-Rhone', '14' => 'Calvados', '15' => 'Cantal', '16' => 'Charente',
            '17' => 'Charente-Maritime', '18' => 'Cher', '19' => 'Correze', '21' => 'Cote-d\'Or',
            '22' => 'Cotes-d\'Armor', '23' => 'Creuse', '24' => 'Dordogne', '25' => 'Doubs',
            '26' => 'Drome', '27' => 'Eure', '28' => 'Eure-et-Loir', '29' => 'Finistere',
            '2A' => 'Corse-du-Sud', '2B' => 'Haute-Corse', '30' => 'Gard', '31' => 'Haute-Garonne',
            '32' => 'Gers', '33' => 'Gironde', '34' => 'Herault', '35' => 'Ille-et-Vilaine',
            '36' => 'Indre', '37' => 'Indre-et-Loire', '38' => 'Isere', '39' => 'Jura',
            '40' => 'Landes', '41' => 'Loir-et-Cher', '42' => 'Loire', '43' => 'Haute-Loire',
            '44' => 'Loire-Atlantique', '45' => 'Loiret', '46' => 'Lot', '47' => 'Lot-et-Garonne',
            '48' => 'Lozere', '49' => 'Maine-et-Loire', '50' => 'Manche', '51' => 'Marne',
            '52' => 'Haute-Marne', '53' => 'Mayenne', '54' => 'Meurthe-et-Moselle', '55' => 'Meuse',
            '56' => 'Morbihan', '57' => 'Moselle', '58' => 'Nievre', '59' => 'Nord',
            '60' => 'Oise', '61' => 'Orne', '62' => 'Pas-de-Calais', '63' => 'Puy-de-Dome',
            '64' => 'Pyrenees-Atlantiques', '65' => 'Hautes-Pyrenees', '66' => 'Pyrenees-Orientales',
            '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '69' => 'Rhone', '70' => 'Haute-Saone',
            '71' => 'Saone-et-Loire', '72' => 'Sarthe', '73' => 'Savoie', '74' => 'Haute-Savoie',
            '75' => 'Paris', '76' => 'Seine-Maritime', '77' => 'Seine-et-Marne', '78' => 'Yvelines',
            '79' => 'Deux-Sevres', '80' => 'Somme', '81' => 'Tarn', '82' => 'Tarn-et-Garonne',
            '83' => 'Var', '84' => 'Vaucluse', '85' => 'Vendee', '86' => 'Vienne',
            '87' => 'Haute-Vienne', '88' => 'Vosges', '89' => 'Yonne', '90' => 'Territoire de Belfort',
            '91' => 'Essonne', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis', '94' => 'Val-de-Marne',
            '95' => 'Val-d\'Oise', '971' => 'Guadeloupe', '972' => 'Martinique', '973' => 'Guyane',
            '974' => 'La Reunion', '976' => 'Mayotte',
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats->map(function ($item) use ($deptNames) {
                return [
                    'department' => $item->department,
                    'name' => $deptNames[$item->department] ?? $item->department,
                    'count' => $item->count,
                ];
            }),
        ]);
    }

    /**
     * Export members within radius as CSV.
     */
    public function exportRadius(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        $lat = (float) $request->get('lat');
        $lng = (float) $request->get('lng');
        $radius = (float) $request->get('radius');

        $members = Member::with('memberships')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) <= ?
            ", [$lat, $lng, $lat, $radius])
            ->orderByRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                ))
            ", [$lat, $lng, $lat])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="contacts_rayon_' . $radius . 'km_' . date('Y-m-d') . '.csv"',
        ];

        $controller = $this;
        $callback = function () use ($members, $lat, $lng, $controller) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Nom', 'Prenom', 'Email', 'Telephone', 'Adresse', 'CP', 'Ville', 'Profil', 'Distance (km)'], ';');

            $profileLabels = self::getProfileTypes();

            foreach ($members as $m) {
                $distance = 6371 * acos(
                    cos(deg2rad($lat)) * cos(deg2rad($m->latitude)) *
                    cos(deg2rad($m->longitude) - deg2rad($lng)) +
                    sin(deg2rad($lat)) * sin(deg2rad($m->latitude))
                );

                $profileType = $controller->getProfileType($m);
                $profileLabel = $profileLabels[$profileType] ?? $profileType;

                fputcsv($file, [
                    $m->last_name,
                    $m->first_name,
                    $m->email,
                    $m->phone,
                    $m->address,
                    $m->postal_code,
                    $m->city,
                    $profileLabel,
                    number_format($distance, 1),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
