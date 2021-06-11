@section('sketchfab')
@php
$pris = Arr::pluck($data['_source']['identifier'],'priref');
$pris = array_filter($pris);
$pris= Arr::flatten($pris);
@endphp
<div class="container-fluid bg-grey">

<div class="container">
  <h2 class="lead">
    3d scans attached to this object
  </h2>
  <a class="mt-3 mb-3 btn btn-dark" href="{{ route('record', [$pris[0]]) }}">Return to record</a>
</div>
</div>
@foreach($data['_source']['identifier'] as $id)
  @isset($id['type'])
    @if($id['type'] === 'Online 3D model')
        <div class="container-fluid bg-grey">
          <div class="container p-3">
            <h3 class="lead collection">3D scan</h3>

            <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
              <div class="embed-responsive embed-responsive-1by1">
                <iframe title="A 3D model of {{ $data['_source']['summary_title'] }}" class="embed-responsive-item"
                src="https://sketchfab.com/models/{{ $id['value']}}/embed?"
                frameborder="0" allow="autoplay; fullscreen; vr" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
              </div>
            </div>
          </div>
        </div>
    @endif
  @endisset
@endforeach
@endsection
