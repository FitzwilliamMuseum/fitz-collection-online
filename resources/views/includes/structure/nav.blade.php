<!-- Nav bars -->
<nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top">
    <a class="navbar-brand" href="{{ route('home') }}">
        <img src="/images/logos/Fitz_logo_white.png" alt="The Fitzwilliam Museum Logo" height="60" width=""
             class="ml-1 mr-1">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL')) }}">Home <span
                        class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL') . '/visit-us/') }}">Visit</a>
            </li>

            <li class="nav-item ">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL') . '/events/') }}">Events & tickets</a>
            </li>


            <li class="nav-item active">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL') . '/objects-and-artworks/') }}">
                    Our Collection</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL') . '/learning') }}">
                    Learning</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL') . '/about-us') }}">About</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="{{ URL::to(env('MAIN_URL') . '/research') }}">
                    Research</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link"
                   href="{{ URL::to('https://curatingcambridge.co.uk/collections/the-fitzwilliam-museum') }}">
                    Shop</a>
            </li>
        </ul>
        {{ Form::open(['url' => url('search/results'),'method' => 'GET', 'class' => 'form-inline ml-auto']) }}
        <label for="search" class="sr-only">Search: </label>
        <input id="query" name="query" type="text" class="form-control mr-sm-2"
               placeholder="Search our site" required value="{{ old('query') }}" aria-label="Your query">
        <button type="submit" class="btn btn-outline-light" id="searchButton" aria-label="Submit your search">Search
        </button>
        {!! Form::close() !!}
    </div>
</nav>
