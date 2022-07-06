<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: url('https://content.fitz.ms/fitz-website/assets/large_PD_8_1979_1_201709.jpg') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            background-size: cover;
            -o-background-size: cover;
        }
    </style>
    @include('googletagmanager::head')

</head>
<body>
@include('googletagmanager::body')

<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top mb-5">
    <div class="container">
        <a class="navbar-brand" href="{{ route('data.home') }}">
            <img src="{{asset("/images/logos/FitzLogo.svg")}}"
                 alt="The Fitzwilliam Museum Logo"
                 height="60"
                 width="66.66"
                 class="ml-1 mr-1"
            />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('data.home') }}">{{ __('Collection pages') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('api.index') }}">{{ __('API overview') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('l5-swagger.default.api') }}">{{ __('API docs') }}</a>
                </li>
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="d-flex flex-column min-vh-100 min-vw-100">
    <div class="container">
        <div class="card ">
            <div class="card-body p-5">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<button
    type="button"
    class="btn btn-floating btn-lg"
    id="btn-back-to-top"
    aria-label="Return to the top of the page"
>
    <svg height="48" viewBox="0 0 48 48" width="64" xmlns="http://www.w3.org/2000/svg"><path fill="black" id="scrolltop-bg" d="M0 0h48v48h-48z"/><path fill="white" id="scrolltop-arrow" d="M14.83 30.83l9.17-9.17 9.17 9.17 2.83-2.83-12-12-12 12z"/></svg>
</button>
<script src="{{ mix('js/app.js') }}"></script>
<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('APP_GOOGLE_ANALYTICS') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ env('APP_GOOGLE_ANALYTICS') }}', { cookie_flags: 'SameSite=None;Secure' });
</script>
</body>
</html>
