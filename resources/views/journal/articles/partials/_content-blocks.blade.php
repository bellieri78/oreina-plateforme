{{-- Main Content --}}
@if($submission->content_blocks && is_array($submission->content_blocks) && count($submission->content_blocks) > 0)
<section style="margin-bottom:40px">
    @php
        // Compteur h2 dédié : seules les sections de niveau 2 reçoivent un numéro
        // visible. h3/h4 restent non numérotés pour ne pas créer de sauts.
        $h2Counter = 0;
        // Compteurs propres par type : Figure N ne compte que les images,
        // Tableau N ne compte que les tables, indépendants de la position globale du bloc.
        $figureCounter = 0;
        $tableCounter = 0;
    @endphp
    @foreach($submission->content_blocks as $blockIndex => $block)
        @php $blockType = $block['type'] ?? 'paragraph'; @endphp

        @if($blockType === 'heading')
            @php
                $headingLevel = ltrim((string) ($block['level'] ?? 'h2'), 'h');
            @endphp
            @if($headingLevel === '2')
                @php $h2Counter++; @endphp
                <h2 id="section-{{ $h2Counter }}">{{ $h2Counter }}. {{ $block['content'] ?? '' }}</h2>
            @elseif($headingLevel === '4')
                <h4>{{ $block['content'] ?? '' }}</h4>
            @else
                <h3>{{ $block['content'] ?? '' }}</h3>
            @endif

        @elseif($blockType === 'paragraph')
            <p>{!! $block['content'] ?? '' !!}</p>

        @elseif($blockType === 'image')
            @php
                $imgSrc = $block['url'] ?? $block['src'] ?? '';
                $imgCaption = $block['caption'] ?? '';
                $figureCounter++;
                $figureNum = $figureCounter;
            @endphp
            @if($imgSrc)
            <figure>
                <div class="figure-preview" onclick="openLightbox('{{ $imgSrc }}', 'Figure {{ $figureNum }}. {{ addslashes($imgCaption) }}')">
                    <img src="{{ $imgSrc }}" alt="{{ $imgCaption }}">
                    <div class="figure-actions">
                        <button class="figure-action-btn" title="Agrandir" onclick="event.stopPropagation(); openLightbox('{{ $imgSrc }}', 'Figure {{ $figureNum }}. {{ addslashes($imgCaption) }}')">
                            <i data-lucide="maximize-2" style="width:16px;height:16px;"></i>
                        </button>
                        <a class="figure-action-btn" href="{{ $imgSrc }}" download title="Télécharger" onclick="event.stopPropagation();">
                            <i data-lucide="download" style="width:16px;height:16px;"></i>
                        </a>
                    </div>
                </div>
                <figcaption>
                    <strong>Figure {{ $figureNum }}.</strong>@if($imgCaption) {{ $imgCaption }}@endif
                </figcaption>
            </figure>
            @endif

        @elseif($blockType === 'table')
            @php
                $tableData = $block['data'] ?? [];
                $tableCounter++;
                $tableNum = $tableCounter;
            @endphp
            @if(count($tableData) > 0)
            <div class="content-table">
                <p class="table-caption">
                    <strong>Tableau {{ $tableNum }}.</strong>@if(!empty($block['caption'])) {{ $block['caption'] }}@endif
                </p>
                <div class="table-scroll">
                    <table>
                        <thead>
                            @if(isset($tableData[0]))
                            <tr>
                                @foreach($tableData[0] as $cell)
                                <th>{{ $cell }}</th>
                                @endforeach
                            </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach(array_slice($tableData, 1) as $row)
                            <tr>
                                @foreach($row as $cell)
                                <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        @elseif($blockType === 'list')
            @php
                $listItems = $block['items'] ?? [];
                $isOrdered = $block['ordered'] ?? false;
            @endphp
            @if(count($listItems) > 0)
            <ul class="content-list">
                @foreach($listItems as $item)
                <li>
                    <span class="bullet">•</span>
                    <span>{{ $item }}</span>
                </li>
                @endforeach
            </ul>
            @endif

        @elseif($blockType === 'quote')
            <blockquote>
                <p>{{ $block['content'] ?? '' }}</p>
                @if(!empty($block['source']))
                <cite>— {{ $block['source'] }}</cite>
                @endif
            </blockquote>
        @endif
    @endforeach
</section>
@elseif($submission->content_html)
<section style="margin-bottom:40px">
    {!! $submission->content_html !!}
</section>
@endif
