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
foreach($dimensions as $dim){

  if($dim['dimension'] == 'Height')
  {
    $dims['height'] = $dim['value'];
  }
  if($dim['dimension'] == 'Width'){
    $dims['width'] = $dim['value'];
  }
  if($dim['dimension'] == 'Depth'){
    $dims['depth'] = $dim['value'];
  }
  if($dim['dimension'] == 'Thickness'){
    $dims['depth'] = $dim['value'];
  }
  if($dim['dimension'] == 'Length'){
    $dims['width'] = $dim['value'];
  }
}
@endphp
@if(array_key_exists('height', $dims) && array_key_exists('width', $dims))
  @php
  if(!array_key_exists('depth', $dims)){
    $dims['depth'] = 0.01;
  }
  @endphp
  <x-Dimension-Drawer
  :height="$dims['height']"
  :width="$dims['width']"
  :depth="$dims['depth']"
  :units="$record['_source']['measurements']['dimensions'][0]['units']"
  :viewWidth="400"
  :viewHeight="320"
  :scale=1
  />
@endif
@endif
@yield('dims-message')
</div>
