@php
    $currentCaps = $user->capabilities()->pluck('capability')->all();
    $canManage = app(\App\Policies\SubmissionPolicy::class)->manageCapabilities(auth()->user(), $user);
@endphp

<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h2 class="text-lg font-bold text-oreina-dark mb-4">Capacités éditoriales Chersotis</h2>

    @if(!$canManage)
        <p class="text-sm text-gray-600 mb-4">Vous n'avez pas les droits pour modifier ces capacités.</p>
    @endif

    <form method="POST" action="{{ route('admin.users.capabilities.update', $user) }}" class="space-y-3">
        @csrf
        @method('PUT')

        @foreach(\App\Models\EditorialCapability::ALL as $cap)
            <label class="flex items-center gap-3">
                <input type="checkbox"
                       name="capabilities[]"
                       value="{{ $cap }}"
                       @checked(in_array($cap, $currentCaps))
                       @disabled(!$canManage)
                       class="h-4 w-4 text-oreina-green border-gray-300 rounded focus:ring-oreina-green">
                <span class="text-sm">
                    <strong>{{ \App\Models\EditorialCapability::labels()[$cap] }}</strong>
                    <span class="text-gray-500">({{ $cap }})</span>
                </span>
            </label>
        @endforeach

        @if($canManage)
            <div class="pt-3">
                <button type="submit" class="bg-oreina-green text-white px-4 py-2 rounded hover:bg-oreina-dark">
                    Enregistrer les capacités
                </button>
            </div>
        @endif
    </form>
</div>
