
<!DOCTYPE html>
<html lang="en" dir="ltr" prefix="content: http://purl.org/rss/1.0/modules/content/  dc: http://purl.org/dc/terms/  foaf: http://xmlns.com/foaf/0.1/  og: http://ogp.me/ns#  rdfs: http://www.w3.org/2000/01/rdf-schema#  schema: http://schema.org/  sioc: http://rdfs.org/sioc/ns#  sioct: http://rdfs.org/sioc/types#  skos: http://www.w3.org/2004/02/skos/core#  xsd: http://www.w3.org/2001/XMLSchema# ">
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/openseadragon/2.4.2/openseadragon.min.js" integrity="sha512-qvQYH6mPuE46uFcWLI8BdGaJpB5taX4lltbSIw5GF4iODh2xIgyz5ii1WpuzPFUknHCps0mi4mFGR44bjdZlZg==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="/css/viewer.css">
    @include('googletagmanager::head')
</head>
<body class="doc-body">
  @include('googletagmanager::body')

  @include('includes.structure.accessibility')

    @yield('content')

  @include('includes.structure.slowjs')

</body>

</html>
