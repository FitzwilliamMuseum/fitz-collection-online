@section('content')
    <div id="mirador" class="mirador"></div>
@endsection
@push('body-scripts')
    @once
        <script src="https://unpkg.com/mirador@latest/dist/mirador.min.js"></script>
    @endonce
    @once
        <script>
            $(function () {
                let myMiradorInstance = Mirador.viewer({
                    id: "mirador",
                    selectedTheme: 'dark',
                    windows: [
                        {
                            "manifestId": "https://api.fitz.ms/data-distributor/iiif/{{$object['admin']['id']}}/manifest"
                        }
                    ],
                    "catalog": [
                        {"manifestId": "https:\/\/api.fitz.ms\/data-distributor\/iiif\/{{$object['admin']['id']}}\/manifest"},
                    ]
                });
            });
        </script>
    @endonce
@endpush
