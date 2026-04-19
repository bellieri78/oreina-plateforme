{{-- Lightbox --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">
        <i data-lucide="x" style="width:20px;height:20px;"></i>
    </button>
    <a class="lightbox-download" id="lightbox-download" href="#" download onclick="event.stopPropagation();">
        <i data-lucide="download" style="width:16px;height:16px;"></i>
        Télécharger
    </a>
    <img id="lightbox-img" src="" alt="" onclick="event.stopPropagation();">
    <div class="lightbox-caption" id="lightbox-caption"></div>
</div>
