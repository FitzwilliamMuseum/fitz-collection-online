@section('iiif')
@if(array_key_exists('zoom', $record["_source"]["multimedia"][0]["processed"]))
<script type="text/javascript" src="/js/mootools-core-1.6.0-compressed.js"></script>
<script type="text/javascript" src="/js/iipmooviewer-2.0-min.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="/css/iip.css" />
<script type="text/javascript">

// IIPMooViewer options: See documentation at http://iipimage.sourceforge.net for more details

// The *full* image path on the server. This path does *not* need to be in the web
// server root directory. On Windows, use Unix style forward slash paths without
// the "c:" prefix
var image = '/{{ $record["_source"]["multimedia"][0]["processed"]["zoom"]["location"] }}';

// Create our iipmooviewer object
new IIPMooViewer( "viewer", {
  image: image,
  server: '/iipsrv/iipsrv.fcgi',
  showNavWindow: true,
  prefix: "/images/",
  preload: true,
  enableFullscreen: false
});

</script>
@endif
@endsection

@if(array_key_exists('zoom', $record["_source"]["multimedia"][0]["processed"]))
<div class="col-md-12 mb-3">
  <h2>Deep zooming IIIF image</h2>
  <div id="viewer"></div>
</div>
@endif
