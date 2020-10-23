@if(array_key_exists('measurements', $record['_source']))
<h4>Measurements and weight</h4>

<ul>
  @foreach($record['_source']['measurements']['dimensions'] as $dim)
    @if(array_key_exists('dimension',$dim) && array_key_exists('units',$dim))
      <li>{{ $dim['dimension'] }}: {{ $dim['value'] }} {{  $dim['units'] }}</li>
    @else
      @section('dims-message')
        <div class="alert alert-dark" role="alert">
          <p>
            At the moment, this record does not display units or type of
            measurements. We will rectify this as soon as possible.
          </p>
        </div>
      @endsection
        <li>{{ $dim['value'] }}</li>
    @endif
  @endforeach
</ul>
@endif
@yield('dims-message')
