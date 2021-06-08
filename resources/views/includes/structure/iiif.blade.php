@section('content')
{{-- <div id="uv" class="uv"></div>
<script>
  var uv = UV.init(
    "uv",
    {
      manifestUri: "https://api.fitz.ms/data-distributor/iiif/{{$record['_source']['admin']['id']}}/manifest",
      configUri: "{{ url('/') }}/config.json",
    },
    new UV.URLDataProvider()
  );

  uv.on("created", function () {
    uv.resize();
  });
</script> --}}
@endsection
