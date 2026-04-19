{{-- resources/views/journal/articles/partials/_sidebar-toc.blade.php --}}
@if(!empty($toc))
<nav class="sidebar-section sidebar-toc" aria-label="Table des matières">
    <h3 class="sidebar-title">Sommaire</h3>
    <ol>
        @foreach($toc as $entry)
            <li>
                <a href="#{{ $entry['anchor'] }}" data-toc-target="{{ $entry['anchor'] }}">
                    {{ $entry['number'] }}. {{ $entry['label'] }}
                </a>
            </li>
        @endforeach
    </ol>
</nav>
@endif
