@if(array_key_exists('measurements', $record['_source']))


  <div class="row">


    <div class="col-md-6">
      <h3 class="lead collection">
        Measurements and weight
      </h3>
      <p>
        @foreach($record['_source']['measurements']['dimensions'] as $dim)
          @if(array_key_exists('dimension',$dim) && array_key_exists('units',$dim))
            {{ $dim['dimension'] }}: {{ $dim['value'] }} {{ $dim['units'] }}<br/>
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
    </div>
    @php
    $dimensions = $record['_source']['measurements']['dimensions'];
    $dims = [];
    $width = [];
    $depth = [];
    foreach($dimensions as $dim){
      if($dim['dimension'] == 'Height')
      {
        $dims['height'] = $dim['value'];
      }
      if($dim['dimension'] == 'Length'){
        array_push($width, $dim['value']);
      }
      if($dim['dimension'] == 'Width'){
        array_push($width, $dim['value']);
      }
      if($dim['dimension'] == 'Depth'){
        array_push($depth, $dim['value']);
      }
      if($dim['dimension'] == 'Thickness'){
        array_push($depth, $dim['value']);
      }
    }
    if(!empty($width)){
      $w = max($width);
    }
    if(count($depth) >= 1) {
      $d = max($depth);
    } else {
      $d = 0.001;
    }
  @endphp

  @if(array_key_exists('height', $dims) && !empty($w) && array_key_exists('units',$record['_source']['measurements']['dimensions'][0]))
    <x-Dimension-Drawer
    :height="$dims['height']"
    :width="$w"
    :depth="$d"
    :units="$record['_source']['measurements']['dimensions'][0]['units']"
    :viewWidth="400"
    :viewHeight="320"
    :scale=1
    />
  @endif
@endif
@yield('dims-message')
</div>
