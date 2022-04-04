@extends('layouts.iiif')
@section('title', 'An IIIF Universal view of ' . ucfirst($object['summary_title'] . ' : ' . $object['identifier'][0]['accession_number'] ))
@section('objectInfo')
    <div class="container-fluid">
        <div class="bg-dark text-white p-2 objectInfo text-center row">
            <div class="mx-auto">{{ ucfirst($object['summary_title']) }}
                : {{  $object['identifier'][0]['accession_number'] }} <a
                    href="/id/object/{{  $object['identifier'][1]['value'] }}"
                    class="btn btn-outline-light btn-sm ml-2">Return to record</a></div>
        </div>
    </div>
@endsection
@section('description')
    An IIIF Universal view of  {{ ucfirst($object['summary_title']) }} : {{  $object['identifier'][0]['accession_number'] }}
@endsection
@if(array_key_exists('multimedia', $object))
    @section('hero_image', env('APP_URL') . '/imagestore/' . $object['multimedia'][0]['processed']['original']['location'])
@else
    @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@endif
@section('iiif')
    @include('includes/structure/iiif')
@endsection
