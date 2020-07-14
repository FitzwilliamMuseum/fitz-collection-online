@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
@foreach($data as $agent)
@section('title', ucfirst($agent['_source']['summary_title']))




  <h3>Connected records</h3>
    <div class="shadow-sm p-3 mx-auto mb-3 rounded">

    </div>
  @endforeach
@endsection
