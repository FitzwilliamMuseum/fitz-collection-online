<!DOCTYPE html>
<html lang="en" dir="ltr" prefix="content: http://purl.org/rss/1.0/modules/content/  dc: http://purl.org/dc/terms/  foaf: http://xmlns.com/foaf/0.1/  og: http://ogp.me/ns#  rdfs: http://www.w3.org/2000/01/rdf-schema#  schema: http://schema.org/  sioc: http://rdfs.org/sioc/ns#  sioct: http://rdfs.org/sioc/types#  skos: http://www.w3.org/2004/02/skos/core#  xsd: http://www.w3.org/2001/XMLSchema# ">
<head>

    @include('includes.structure.metaIIIF')

    @include('includes.css.cssIIIF')

    @hasSection('map')
      @mapstyles
    @endif

    @include('includes.structure.manifest')
    @yield('jsonld')

    <link rel="stylesheet" type="text/css" href="/css/uv.css" />
    <!-- include this if using 3D: https://github.com/UniversalViewer/universalviewer/issues/716 -->
    <script src="https://unpkg.com/resize-observer-polyfill@1.5.1/dist/ResizeObserver.js"></script>
    <!-- must include jQuery and jsViews for the UV for now -->
    <script src="{{ url('/') }}/uv-assets/js/bundle.js"></script>
    <script src="{{ url('/') }}/uv-dist-umd/UV.js"></script>
    <style>
    .navbar-dark .navbar-nav .nav-link {
      color: #f5f5f5!important;
    }
    .bg-black {
      background-color: #000000!important;
    }
      body {
        margin: 0;
      }
      #uv {
        width: 100vw;
        height: 87vh;
        margin-bottom: 0px;
      }
      .objectInfo {
          margin-top: 55px;
          margin-left: -15px;
          margin-right: -15px;
      }
      .objectInfo p {
        margin-top: 5px;
      }
    </style>
    @include('googletagmanager::head')

</head>
<body class="doc-body">
  @include('googletagmanager::body')

  @include('includes.structure.accessibility')

  @include('includes.structure.nav')


    @yield('objectInfo')
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script><!-- Back to top script -->

</body>

</html>
