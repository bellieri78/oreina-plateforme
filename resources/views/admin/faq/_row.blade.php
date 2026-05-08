<tr style="{{ ! $faq->is_visible ? 'opacity: 0.55;' : '' }}">
    <td style="white-space: nowrap;">
        <form action="{{ route('admin.faq.reorder', ['faq' => $faq->id, 'direction' => 'up']) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.125rem 0.375rem;" title="Monter">↑</button>
        </form>
        <form action="{{ route('admin.faq.reorder', ['faq' => $faq->id, 'direction' => 'down']) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.125rem 0.375rem;" title="Descendre">↓</button>
        </form>
    </td>
    <td>
        <strong>{!! strip_tags($faq->question, '<em>') !!}</strong>
    </td>
    <td>
        @if($faq->is_visible)
            <span class="badge badge-success">Visible</span>
        @else
            <span class="badge badge-secondary">Cachée</span>
        @endif
    </td>
    <td style="white-space: nowrap;">
        <form action="{{ route('admin.faq.toggle-visible', $faq) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem;" title="{{ $faq->is_visible ? 'Cacher' : 'Afficher' }}">
                {{ $faq->is_visible ? '🙈' : '👁' }}
            </button>
        </form>
        <a href="{{ route('admin.faq.edit', $faq) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem;" title="Modifier">✏</a>
        <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette question ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; color: #dc2626;" title="Supprimer">🗑</button>
        </form>
    </td>
</tr>
