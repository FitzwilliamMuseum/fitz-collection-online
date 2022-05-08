<!DOCTYPE html>
<html lang="en" dir="ltr"
      prefix="content: https://purl.org/rss/1.0/modules/content/  dc: https://purl.org/dc/terms/  foaf: https://xmlns.com/foaf/0.1/  og: https://ogp.me/ns#  rdfs: https://www.w3.org/2000/01/rdf-schema#  schema: https://schema.org/  sioc: https://rdfs.org/sioc/ns#  sioct: https://rdfs.org/sioc/types#  skos: https://www.w3.org/2004/02/skos/core#  xsd: https://www.w3.org/2001/XMLSchema# ">
<head>
    @include('includes.structure.metaIIIF')
    @include('includes.css.cssIIIF')
    @include('includes.structure.manifest')
    @yield('jsonld')

    <link rel="stylesheet" type="text/css" href="/css/uv.css"/>
    <script src="{{ url('/') }}/umd/UV.js"></script>
    @include('includes.css.css')

    <link rel="stylesheet" type="text/css" href="/css/iiif.css"/>

    @include('googletagmanager::head')

</head>
<body class="doc-body">
@include('googletagmanager::body')

@include('includes.structure.accessibility')

@include('includes.structure.nav')


@yield('objectInfo')
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
        crossorigin="anonymous"></script><!-- Back to top script -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('APP_GOOGLE_ANALYTICS') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', '{{ env('APP_GOOGLE_ANALYTICS') }}');
</script>
</body>

</html>
