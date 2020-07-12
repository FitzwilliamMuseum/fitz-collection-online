@section('iiif')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/mootools/1.6.0/mootools.min.js"></script>
<script type="text/javascript" src="/js/iipmooviewer-2.0-min.js"></script>

<script type="text/javascript">

    // IIPMooViewer options: See documentation at http://iipimage.sourceforge.net for more details

    // The *full* image path on the server. This path does *not* need to be in the web
    // server root directory. On Windows, use Unix style forward slash paths without
    // the "c:" prefix
    var image = '/{{ $record["_source"]["multimedia"][0]["processed"]["zoom"]["location"] }}';

    // Copyright or information message
    var credit = 'fitzwilliam Museum';

    // Create our iipmooviewer object
    new IIPMooViewer( "viewer", {
  image: image,
  credit: credit,
  server: 'https://api.fitz.ms/iipsrv/iipsrv.fcgi',
  viewport: {resolution:3}
    });

  </script>
@endsection
<div id="viewer"></div>
