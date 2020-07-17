@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
@section('title', $name)
<div class="shadow-sm p-3 mx-auto mb-3 rounded">
<p>This department has <strong>{{ number_format($use['count'])}}</strong> records attributed within our system.</p>
</div>

@endsection
