@if(array_key_exists('measurements', $record['_source']))
<h3 class="lead collection">
  Measurements and weight
</h3>

<p>
  @foreach($record['_source']['measurements']['dimensions'] as $dim)
    @if(array_key_exists('dimension',$dim) && array_key_exists('units',$dim))
      {{ $dim['dimension'] }}: {{ $dim['value'] }} {{  $dim['units'] }}<br/>
    @else
      @section('dims-message')
          <p class="text-info">
            At the moment, this record does not display units or type of
            measurements. We will rectify this as soon as possible.
          </p>
      @endsection
        {{ $dim['value'] }}<br/>
    @endif
  @endforeach
</p>
@endif
@yield('dims-message')
