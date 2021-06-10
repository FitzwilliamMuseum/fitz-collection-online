@if(array_key_exists('measurements', $record['_source']))
<h3>
  Measurements and weight
</h3>

<p>
  @foreach($record['_source']['measurements']['dimensions'] as $dim)
    @if(array_key_exists('dimension',$dim) && array_key_exists('units',$dim))
      <li>{{ $dim['dimension'] }}: {{ $dim['value'] }} {{  $dim['units'] }}</li>
    @else
      @section('dims-message')
          <p class="text-info">
            At the moment, this record does not display units or type of
            measurements. We will rectify this as soon as possible.
          </p>
      @endsection
        <li>{{ $dim['value'] }}</li>
    @endif
  @endforeach
</p>
@endif
@yield('dims-message')
