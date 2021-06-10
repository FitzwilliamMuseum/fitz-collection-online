<!DOCTYPE html>
<html lang="en" dir="ltr" prefix="content: http://purl.org/rss/1.0/modules/content/  dc: http://purl.org/dc/terms/  foaf: http://xmlns.com/foaf/0.1/  og: http://ogp.me/ns#  rdfs: http://www.w3.org/2000/01/rdf-schema#  schema: http://schema.org/  sioc: http://rdfs.org/sioc/ns#  sioct: http://rdfs.org/sioc/types#  skos: http://www.w3.org/2004/02/skos/core#  xsd: http://www.w3.org/2001/XMLSchema# ">
<head>

    @include('includes.structure.meta')

    @include('includes.css.css')

    @hasSection('map')
      @mapstyles
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
    @yield('machine')
  @hasSection('map')
  <div class="container-fluid map-box mb-3">
    @yield('map')
  </div>
  @endif

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
    @mapscripts
    @include('includes.scripts.mapjs')
  @endif

  @hasSection('timeline')
    @include('includes.scripts.timeline-js')
  @endif

</body>
</html>
