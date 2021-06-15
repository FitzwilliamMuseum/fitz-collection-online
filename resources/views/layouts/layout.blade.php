<!DOCTYPE html>
<html lang="en" dir="ltr" prefix="content: http://purl.org/rss/1.0/modules/content/  dc: http://purl.org/dc/terms/  foaf: http://xmlns.com/foaf/0.1/  og: http://ogp.me/ns#  rdfs: http://www.w3.org/2000/01/rdf-schema#  schema: http://schema.org/  sioc: http://rdfs.org/sioc/ns#  sioct: http://rdfs.org/sioc/types#  skos: http://www.w3.org/2004/02/skos/core#  xsd: http://www.w3.org/2001/XMLSchema# ">
<head>

    @include('includes.structure.meta')

    @include('includes.css.css')

    @hasSection('map')
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" type="text/css">
      <link rel="stylesheet" href="http://localhost:8000/vendor/maps/css/index.css?id=81569dd7736e102f4342" type="text/css">
    @endif

    @include('includes.structure.manifest')
    @yield('jsonld')
    @hasSection('iiif')
      @yield('iiif')
    @endif
    @include('googletagmanager::head')

</head>
<body class="doc-body">
  @include('googletagmanager::body')


  @include('includes.structure.accessibility')

  @include('includes.structure.nav')
  @include('includes.structure.head')
  @hasSection('timeline')
    @include('includes.css.timeline-css')
  @endif
  @hasSection('360')
    @include('includes.css.photosphere-css')
  @endif
  <div class="container-fluid bg-white">
    @include('includes.structure.breadcrumb')
  </div>
  @hasSection('media-files')
    @yield('media-files')
  @endif
  <div class="container mt-3">

    @yield('content')
  </div>
    @yield('connected-images')
    @yield('search-box')
    @yield('search-results')
    @yield('connected')
    @yield('pagination')
    @yield('exif-palette')
    @yield('machine')

    @yield('sketchfab')
    @yield('mlt')

  @include('includes.structure.emailsignup')
  @include('includes.structure.share')

  @include('includes.structure.footer')

  @include('includes.structure.modal')

  @hasSection('lookanswers')
    @yield('lookanswers')
  @endif

  @hasSection('thinkanswers')
    @yield('thinkanswers')
  @endif

  @hasSection('doanswers')
    @yield('doanswers')
  @endif

  @include('includes.scripts.javascript')

  @hasSection('360')
    @include('includes.scripts.photosphere-js')
  @endif

  @hasSection('map')
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin="" type="text/javascript"></script>
    <script src="http://localhost:8000/vendor/maps/js/index.js?id=1e6f34e45ce1f8e9666f" type="text/javascript"></script>
    @include('includes.scripts.mapjs')
  @endif

  @hasSection('timeline')
    @include('includes.scripts.timeline-js')
  @endif

</body>
</html>
