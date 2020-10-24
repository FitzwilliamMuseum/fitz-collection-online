@extends('layouts.layout')
@section('content')
@foreach($filtered as $record)
@include('includes.elements.media-single')
@include('includes.elements.exif')
@include('includes.elements.palette')
@endforeach
@endsection
