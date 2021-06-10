@extends('layouts.layout')

@foreach($filtered as $record)
@section('content')
  @include('includes.elements.media-single')
@endsection
@section('exif-palette')
<div class="container-fluid bg-grey">
  <div class="container">
  
  @include('includes.elements.exif')
  @include('includes.elements.palette')
  </div>
</div>
@endsection
@endforeach
