@if(array_key_exists('measurements', $data))
    <div class="row">
        <div class="col-md-6">
            <h3 class="lead collection">Measurements and weight</h3>
            <p>
                @foreach($data['measurements']['dimensions'] as $dim)
                    @if(array_key_exists('dimension',$dim) && array_key_exists('units',$dim))
                        {{ $dim['dimension'] }}: {{ $dim['value'] }} {{ $dim['units'] }}<br/>

            @else
                @section('dims-message')
                    <p class="text-info">
                        At the moment, this record does not display units or type of
                        measurements. We will rectify this as soon as possible.
                    </p>
                @endsection
                {{ $dim['value'] }}
                @if(!$loop->last)
                    <br/>
                    @endif
                    @endif
                    @endforeach
                    </p>
        </div>
        @endif
        @yield('dims-message')
    </div>
