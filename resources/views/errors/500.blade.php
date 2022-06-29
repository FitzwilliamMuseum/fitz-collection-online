@extends('layouts.error')
@section('title', 'Page not found')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/cupidpsychelarge.jpg')
@section('hero_image_title', 'Cupid and Psyche - del Sallaio')
@section('content')
    <h2 class="lead">{{$exception->getStatusCode()}} Error</h2>

    <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded ">
        <figure class="figure">
            <img class="img-fluid" src="https://fitz-cms-images.s3.eu-west-2.amazonaws.com/searle_cat.jpg"
                 alt="Searle's grumpy cat"/>
        </figure>
        <p>Sorry, we have a problem with our code. This problem has been noted and,
            we will see if we can fix it.</p>
    </div>
@endsection
