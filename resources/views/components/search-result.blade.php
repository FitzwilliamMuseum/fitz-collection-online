@php
    $pris = Arr::pluck($record['_source']['identifier'],'priref');
    $pris = array_filter($pris);
    $pris= Arr::flatten($pris);
@endphp
<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="mx-auto">
            @if(array_key_exists('multimedia', $record['_source']))
                <a href="/id/object/{{ $pris[0] }}"><img class="card-image-top"
                                                         src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                                                         loading="lazy"
                                                         alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                    /></a>
            @else
                <a href="/id/object/{{ $pris[0] }}"><img class="card-image-top"
                                                         src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-medium-crop"
                                                         alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
            @endif
        </div>
        <div class="card-body ">
            <div class="contents-label mb-3">
                <h3 class="lead ">
                    @if(array_key_exists('title',$record['_source'] ))
                        <a href="/id/object/{{ $pris[0] }}">{{ ucfirst($record['_source']['title'][0]['value']) }}</a>
                    @else
                        <a href="/id/object/{{ $pris[0] }}">{{ ucfirst($record['_source']['summary_title']) }}</a>
                    @endif
                </h3>
                @if(array_key_exists('accession_number', $record['_source']['identifier'][0]))
                    <p class="text-info">
                        {{ $record['_source']['identifier'][0]['accession_number'] }}
                    </p>
                @endif
                @include('includes.elements.makers')
            </div>
        </div>
    </div>
</div>
