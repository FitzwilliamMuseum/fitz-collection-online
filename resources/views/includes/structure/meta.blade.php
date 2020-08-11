    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta content="initial-scale=1, shrink-to-fit=no, width=device-width" name="viewport">

    <title>The Fitzwilliam Museum - @yield('title')</title>
    <meta name="description" content="@yield('description')" />
@hasSection('keywords')
  <meta name="keywords" content="@yield('keywords')" />
@endif
    <!-- Canonical link -->
    <link rel="canonical" href="{{ URL::current() }}" />

    <!-- Open graph -->
    <meta property="og:locale" content="{{ app()->getLocale() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:url" content="{{ URL::current() }}" />
    <meta property="og:site_name" content="The Fitzwilliam Museum" />

@hasSection('hero_image')
  <meta property="og:image" content="@yield('hero_image')" />
  <meta name="twitter:image" content="@yield('hero_image')" />
@else
  <meta property="og:image" content="https://fitz-cms-images.s3.eu-west-2.amazonaws.com/fitzwilliam-museum-main-entrance-2018_3.jpg" />
  <meta name="twitter:image" content="https://fitz-cms-images.s3.eu-west-2.amazonaws.com/fitzwilliam-museum-main-entrance-2018_3.jpg" />
@endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Twitter card -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:description" content="@yield('description')" />
    <meta name="twitter:title" content="@yield('title')" />
    <meta name="twitter:site" content="@yield('twitter_id', '@fitzmuseum_uk')" />
    <meta name="twitter:creator" content="@yield('twitter_id', '@fitzmuseum_uk')" />

    <!-- DNS prefetches -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="dns-prefetch" href="//s3.amazonaws.com">
    <link rel="dns-prefetch" href="//use.fontawesome.com">
    <!--
  d888888P dP                  88888888b oo   dP                       oo dP dP oo                        8888ba.88ba
   88    88                    88             88                          88 88                           88  `8b  `8b
   88    88d888b. .d8888b.    a88aaaa    dP d8888P d888888b dP  dP  dP dP 88 88 dP .d8888b. 88d8b.d8b.    88   88   88 dP    dP .d8888b. .d8888b. dP    dP 88d8b.d8b.
   88    88'  `88 88ooood8     88        88   88      .d8P' 88  88  88 88 88 88 88 88'  `88 88'`88'`88    88   88   88 88    88 Y8ooooo. 88ooood8 88    88 88'`88'`88
   88    88    88 88.  ...     88        88   88    .Y8P    88.88b.88' 88 88 88 88 88.  .88 88  88  88    88   88   88 88.  .88       88 88.  ... 88.  .88 88  88  88
   dP    dP    dP `88888P'     dP        dP   dP   d888888P 8888P Y8P  dP dP dP dP `88888P8 dP  dP  dP    dP   dP   dP `88888P' `88888P' `88888P' `88888P' dP  dP  dP
    -->

    <!-- Humans text -->
    <link type="text/plain" rel="author" href="{{ URL::to('/humans.txt') }}" />
    <!-- end of humans.txt -->
    <link href="{{ URL::to('/news/feed') }}" rel="alternate"
    type="application/atom+xml" title="Latest news from the museum"/>

    <script type="application/ld+json">
    {"publisher":{"@type":"Organization",
    "logo":{"@type":"ImageObject",
    "url":"{{ URL::to('/images/logos/FV.png') }}"}},
    "headline":"The Fitzwilliam Museum","@type":"WebSite","url":"{{ URL::to('/')}}",
    "name":"The Fitzwilliam Museum",
    "description":"The Fitzwilliam Museum is the principal museum of the University of Cambridge",
    "@context":"https://schema.org"}
    </script>
    <script type="text/javascript" src="https://www.bugherd.com/sidebarv2.js?apikey=akxmjgmostfy0dnzbyr92g" async="true"></script>
