@extends('layouts.mirador')
@section('objectInfo')
    <div class="container-fluid">
        <div class="bg-black text-white p-2 objectInfo text-center row">
            <div class="mx-auto">{{ ucfirst($object['summary_title']) }}
                : {{  $object['identifier'][0]['accession_number'] }} <a
                    href="{{ route('record', $object['identifier'][1]['value']) }}"
                    class="btn btn-outline-light btn-sm ml-2">Return to record</a></div>
        </div>
    </div>
@endsection
@include('includes.structure.mirador')
