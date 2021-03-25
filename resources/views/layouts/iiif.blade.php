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

    <link rel="stylesheet" type="text/css" href="/css/uv.css" />
    <!-- include this if using 3D: https://github.com/UniversalViewer/universalviewer/issues/716 -->
    <script src="https://unpkg.com/resize-observer-polyfill@1.5.1/dist/ResizeObserver.js"></script>
    <!-- must include jQuery and jsViews for the UV for now -->
    <script src="{{ url('/') }}/uv-assets/js/bundle.js"></script>
    <script src="{{ url('/') }}/uv-dist-umd/UV.js"></script>
    <style>
      body {
        margin: 0;
      }
      #uv {
        width: 100vw;
        height: 100vh;
      }
    </style>
</head>
<body class="doc-body">
  @include('includes.structure.accessibility')

  @include('includes.structure.nav')



    @yield('content')

</body>
</html>
