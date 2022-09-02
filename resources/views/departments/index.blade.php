@extends('layouts.layout')
@section('title', 'Departments represented in the Fitzwilliam Collection')
@section('description', 'A list of all the departments represented in the Fitzwilliam Collection')

@section('content')
    <div class="row">
        @foreach($departments['aggregations']['department']['buckets'] as $department)
            <x-department-card :department="$department"></x-department-card>
        @endforeach
    </div>
@endsection
