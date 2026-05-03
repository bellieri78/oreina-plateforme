<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\MemberDirectoryService;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function __construct(private MemberDirectoryService $service) {}

    public function index(Request $request)
    {
        $member = $request->attributes->get('current_member');

        return view('member.directory.index', [
            'member' => $member,
            'groups' => Member::DIRECTORY_GROUPS,
        ]);
    }

    public function data(Request $request)
    {
        $member = $request->attributes->get('current_member');

        $params = [
            'departments' => $this->validateDepartments($request->input('dept')),
            'groups' => $this->validateGroups($request->input('groups')),
            'q' => $this->normalizeSearch($request->input('q')),
        ];

        $members = $this->service->filter($params, $member);

        return response()->json([
            'count' => $members->count(),
            'members' => $members->map(fn ($m) => $this->service->toJsonRow($m))->values(),
        ]);
    }

    public function show(Request $request, Member $member)
    {
        if (!$member->isInDirectory()) {
            abort(404);
        }

        $self = $request->attributes->get('current_member');
        if ($member->id === $self->id) {
            abort(404);
        }

        return view('member.directory._modal', [
            'member' => $member,
            'phone' => ($member->directory_phone_visible && !empty($member->mobile)) ? $member->mobile : null,
            'groups' => $member->directory_groups ?? [],
        ]);
    }

    /**
     * Filtre les départements à un format métropole/DOM (ex: 75, 2A, 974).
     */
    private function validateDepartments(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }
        return collect(explode(',', $raw))
            ->map(fn ($d) => strtoupper(trim($d)))
            ->filter(fn ($d) => preg_match('/^([0-9]{2}[AB]?|9[78][0-9])$/', $d))
            ->values()
            ->all();
    }

    /**
     * Whitelist les groupes contre les constantes Member.
     */
    private function validateGroups(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }
        $allowed = array_keys(Member::DIRECTORY_GROUPS);
        return collect(explode(',', $raw))
            ->map(fn ($g) => trim($g))
            ->filter(fn ($g) => in_array($g, $allowed, true))
            ->values()
            ->all();
    }

    private function normalizeSearch(?string $raw): ?string
    {
        if (empty($raw)) {
            return null;
        }
        $trimmed = trim($raw);
        return $trimmed === '' ? null : mb_substr($trimmed, 0, 64);
    }
}
