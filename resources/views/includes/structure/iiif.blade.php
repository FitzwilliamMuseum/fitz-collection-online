@section('content')
    <div id="uv" class="uv"></div>
    <script>
        var urlAdaptor = new UV.IIIFURLAdaptor();
        const data = urlAdaptor.getInitialData({
            manifest: "https://api.fitz.ms/data-distributor/iiif/{{$object['admin']['id']}}/manifest",
            embedded: true, // needed for codesandbox frame
            configUri: "{{ url('/') }}/config.json",
        });
        uv = UV.init("uv", data);
        urlAdaptor.bindTo(uv);
    </script>

@endsection
