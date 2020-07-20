@extends('layouts/layout')
@section('title', 'Our objects and works of art')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/cupidpsychelarge.jpg')
@section('hero_image_title', 'Cupid and Psyche - del Sallaio')
@section('description','A search page for our highlight objects')
@section('keywords', 'search,highlights, objects')
@section('content')

<div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
  {{ \Form::open(['url' => url('/search/results'),'method' => 'GET']) }}
  <div class="form-group">
    <input type="text" id="query" name="query" value="" class="form-control input-lg mr-4"
    placeholder="Search our collection" required value="{{ old('query') }}">
  </div>
  <div class="form-group form-check ">
    <input type="checkbox" class="form-check-input" id="images" name="images">
    <label class="form-check-label" for="images">Images</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="sort" id="sort" value="desc">
    <label class="form-check-label" for="sort">
      Descending
    </label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="sort" id="sort" value="asc" checked>
    <label class="form-check-label" for="exampleRadios1">
      Ascending 
    </label>
  </div>

  <div class="form-group">
  <button type="submit" class="btn btn-dark">Submit</button>
</div>
</div>
@if(count($errors))
<div class="form-group">
  <div class="alert alert-danger">
    <ul>
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
</div>
@endif
{!! Form::close() !!}
</div>
@endsection
