<tr style="{{ ! $item->is_active ? 'opacity: 0.5;' : '' }}">
    <td>
        <form action="{{ route('admin.menus.reorder', ['menu' => $item->id, 'direction' => 'up']) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.125rem 0.375rem;" title="Monter">↑</button>
        </form>
        <form action="{{ route('admin.menus.reorder', ['menu' => $item->id, 'direction' => 'down']) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.125rem 0.375rem;" title="Descendre">↓</button>
        </form>
    </td>
    <td>
        @if($depth > 0)
            <span style="color: #9ca3af; margin-right: 0.5rem;">└─</span>
        @endif
        <strong>{{ $item->label }}</strong>
    </td>
    <td><code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.8125rem;">{{ $item->url }}</code></td>
    <td>
        @if($item->is_active)
            <span class="badge badge-success">Actif</span>
        @else
            <span class="badge badge-secondary">Inactif</span>
        @endif
        @if($item->open_in_new_tab)
            <span class="badge badge-info" style="margin-left: 0.25rem;">↗</span>
        @endif
    </td>
    <td>
        <a href="{{ route('admin.menus.edit', $item) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem;" title="Modifier">✏</a>
        <form action="{{ route('admin.menus.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet item{{ $item->children->count() ? ' et ses ' . $item->children->count() . ' sous-items' : '' }} ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; color: #dc2626;" title="Supprimer">🗑</button>
        </form>
    </td>
</tr>
