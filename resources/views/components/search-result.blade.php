<div class="col-md-4 mb-3">
    <div class="card h-100 card-fitz shadow-sm">
        <div class="mx-auto">
            @if(array_key_exists('multimedia', $record['_source']))
                <a href="{{ route('record', $record['_source']['identifier'][1]['priref']) }}">
                    <img class="card-image-top"
                         src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                         loading="lazy"
                         alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                    />
                </a>
            @else
                <a href="{{ route('record', $record['_source']['identifier'][1]['priref']) }}">
                    @svg('fas-image', ['width' => 250])
                </a>
            @endif
        </div>
        <div class="card-body">
            <div class="contents-label mb-3">
                <h3 class="lead ">
                    @if(array_key_exists('title', $record['_source'] ))
                        <a href="{{ route('record', $record['_source']['identifier'][1]['priref']) }}">{{ ucfirst($record['_source']['title'][0]['value']) }}</a>
                    @else
                        <a href="{{ route('record', $record['_source']['identifier'][1]['priref']) }}">{{ ucfirst($record['_source']['summary_title']) }}</a>
                    @endif
                </h3>
{{--                @if(is_array($accession))--}}
{{--                    <p class="text-info">--}}
{{--                        {{ $accession['0'] }}--}}
{{--                    </p>--}}
{{--                @endif--}}
                @include('includes.elements.makers', ['data' => $record['_source']])
            </div>
        </div>
    </div>
</div>
