<div class="card panel">
    <div class="panel-head"><div><h2>Accueil du groupe</h2></div></div>
    <p style="color:var(--text);line-height:1.7;">Bienvenue dans l'espace collaboratif {{ $workGroup->name }}.</p>
    @if($workGroup->usage_help)
    <div style="background:var(--surface-soft,#f1f5f9);border-radius:12px;padding:14px 16px;margin-top:14px;">
        <strong style="display:flex;align-items:center;gap:8px;"><i data-lucide="help-circle" style="width:18px;height:18px;color:var(--blue);"></i>Comment utiliser cet espace ?</strong>
        <div style="white-space:pre-line;line-height:1.65;color:var(--text);margin-top:8px;font-size:14px;">{{ $workGroup->usage_help }}</div>
    </div>
    @endif
</div>
