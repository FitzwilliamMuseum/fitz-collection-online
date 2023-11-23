@extends('layouts.error')
@section('title', 'An error has been recorded')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/cupidpsychelarge.jpg')
@section('hero_image_title', 'Cupid and Psyche - del Sallaio')
@section('content')

<div class="col-12 shadow-sm p-3 mx-auto mb-3 mt-3">
    <div class="row">
        <div class="col-md-4">
            <figure class="figure">
                <img alt="An image of a very grumpy cat"
                     class="img-fluid"
                     width="416"
                     height="416"
                     src="https://fitz-cms-images.s3.eu-west-2.amazonaws.com/searle_cat.jpg"
                />
                <figcaption class="figure-caption">
                    One of Ronald Searle's cats, given to the Fitzwilliam Museum in 2014 by his family.
                </figcaption>
            </figure>
        </div>
        <div class="col-md-8">
                <h2 class="lead">Internal server error {{$exception->getStatusCode()}}</h2>
            <p>Sorry, we have a problem with our code. This problem has been noted and,
                we will see if we can fix it.</p>
        </div>
    </div>
</div>
@endsection

