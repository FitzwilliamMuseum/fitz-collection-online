@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
@foreach($data as $term)
@section('title', ucfirst($term['_source']['summary_title']))

    <div class="shadow-sm p-3 mx-auto mb-3 rounded">
      @if(array_key_exists('description', $term['_source']))
      @foreach($term['_source']['description'] as $description)
      <h3>{{ ucfirst($description['type']) }}</h3>
      <p>
      {{ ucfirst($description['value']) }}
      </p>
      @endforeach
      @else
      <p>No supplementary data available</p>
      @endif
    </div>

    <h3>Connected records</h3>
    <div class="shadow-sm p-3 mx-auto mb-3 rounded">

    </div>
  @endforeach
@endsection
