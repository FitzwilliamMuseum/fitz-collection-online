@section('sketchfab')
    @php
        $pris = Arr::pluck($data['_source']['identifier'],'priref');
        $pris = array_filter($pris);
        $pris= Arr::flatten($pris);
    @endphp
    <div class="container-fluid bg-grey">

        <div class="container">
            <h2>
                3d scans attached to this object
            </h2>
        </div>
        <div class="text-center container">
            <a class="btn btn-dark d-block" href="{{ route('record', [$pris[0]]) }}">Return to record</a>
        </div>
    </div>
    @foreach($data['_source']['identifier'] as $id)
        @isset($id['type'])
            @if($id['type'] === 'Online 3D model')
                <div class="container-fluid bg-grey">
                    <div class="container">
                        <div class="col-12 shadow-sm p-3 mx-auto rounded">
                            <div class="ratio ratio-1x1">
                                <iframe title="A 3D model of {{ $data['_source']['summary_title'] }}"
                                        src="https://sketchfab.com/models/{{ $id['value']}}/embed?"
                                        allow="autoplay; fullscreen; vr" mozallowfullscreen="true"
                                        webkitallowfullscreen="true" width="100%" height="800"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endisset
    @endforeach
@endsection
