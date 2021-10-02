@section('content')
  <div id="mirador" class="mirador"></div>
@endsection
@push('body-scripts')
  @once
  <script src="https://cdn.jsdelivr.net/npm/mirador@3.1.1/dist/mirador.min.js" integrity="sha256-kgsl88ooIyFxWsB8GWBeWDt+qbAklTRuCD0rT7w14p0=" crossorigin="anonymous"></script>
  @endonce
  @once
  <script>
  $(function() {
    var myMiradorInstance = Mirador.viewer({
      id: "mirador",
      selectedTheme:'dark',
      windows: [
        {
          "manifestId": "https://api.fitz.ms/data-distributor/iiif/{{$object['admin']['id']}}/manifest"
        }
      ],
      "catalog": [
        {"manifestId":"https:\/\/api.fitz.ms\/data-distributor\/iiif\/{{$object['admin']['id']}}\/manifest"},
      ]
    });
  });
</script>
@endonce
@endpush
