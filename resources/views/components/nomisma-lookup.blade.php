<div class="mb-2 row">

    @if(!empty($definitions))
        <div class="col-md-6 mb-2">
            <h3 class="collection lead">Nomisma Definition</h3>
            @foreach($definitions as $definition)
                @markdown($definition)
            @endforeach
        </div>
    @endif
    @if(!empty($scopeNote))
        <div class="col-md-6 mb-2">
            <h3 class="collection lead">Nomisma scope note</h3>
            <p>
                {{ $scopeNote }}
            </p>
        </div>
    @endif
    @if(!empty($labels))
        <div class="col-md-6 mb-2">
            <h3 class="collection lead">Labels in other languages sourced via Nomisma</h3>
            @foreach(array_slice($labels,0,10) as $label)
                {{ $label }}
                @if(!$loop->last)
                    <br/>
                @endif
            @endforeach
        </div>
    @endif
    @if(!is_null($type))
        <div class="col-md-3">
            <h3 class="collection lead">Concept type</h3>
            <p>
                <a href="{{ $type }}">{{ str_replace('http://nomisma.org/ontology#','',$type) }}</a>
            </p>
        </div>
    @endif

</div>


@if(!empty($lat))
    @section('map')
        <!-- add map -->
    @endsection
    <h3 class="collection lead my-1">Map via Nomisma</h3>
    <div class="my-2">
        @map(
        [
        'lat' => $lat,
        'lng' => $long,
        'zoom' => 12,
        'minZoom' => 6,
        'maxZoom' => 18,
        'markers' => [
        [
        'title' => 'Place depicted',
        'lat' => $lat,
        'lng' => $long,
        'popup' => 'Place depicted',
        ],
        ],
        ]
        )
    </div>
    <script>
        window.addEventListener('LaravelMaps:MapInitialized', function (event) {
            // load GeoJSON from an external file
            const map = event.detail.map;

            fetch("https://nomisma.org/id/{{ $nomismaID }}.geojson")
                .then(response => response.json())
                .then(data => L.geoJson(data).addTo(map));
            map.scrollWheelZoom.disable();
        });
    </script>
@endif
