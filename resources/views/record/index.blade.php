@extends('layouts/layout')


@section('content')
<div class="row">
  @foreach($data as $record)
  @if(array_key_exists('multimedia', $record['_source']))
  @section('hero_image_title', ucfirst($record['_source']['summary_title']))
  @section('hero_image','https://api.fitz.ms/mediaLib/' . $record['_source']['multimedia'][0]['processed']['original']['location'])
  @else
  @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
  @section('hero_image_title', "The inside of our Founder's entrance")
  @endif

  <!-- multimedia section start -->
  @include('includes/elements/media')
  <!-- multimedia section end -->

  @if(array_key_exists('multimedia', $record['_source']))
  <div class="col-md-12 mb-3">
  @else
  <div class="col-md-12 mb-3">
  @endif
    <h2>Object information</h2>
    <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
      <div class="container">

        @include('includes/elements/descriptive')

        @include('includes/elements/legal')

        @include('includes/elements/lifecycle')

        @include('includes/elements/measurements')

        @include('includes/elements/agents-subjects')

        @include('includes/elements/medium')

        @include('includes/elements/techniques')

        @include('includes/elements/inscriptions')

        @include('includes/elements/department')

        @include('includes/elements/publications')

        @include('includes/elements/identification')

        @include('includes/elements/institutions')

        @include('includes/elements/formats')

        </div>
      </div>

    </div>

    @include('includes/elements/sketchfab')


    @endforeach
  </div>
@endsection
