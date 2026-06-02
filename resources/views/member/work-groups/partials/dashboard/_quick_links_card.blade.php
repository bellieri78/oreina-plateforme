@if(count($quickLinks))
<div class="card panel">
    <div class="panel-head"><div><h2>Liens rapides</h2></div></div>
    @foreach($quickLinks as $link)
    <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="gt-quick-item">
        <span style="display:inline-flex;align-items:center;gap:10px;"><i data-lucide="{{ $link['icon'] }}"></i>{{ $link['label'] }}</span>
        <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--muted);"></i>
    </a>
    @endforeach
</div>
@endif
