@extends('layouts.layout')
@section('title', 'Places represented in the Fitzwilliam Collection')
@section('description', 'A list of all the places represented in the Fitzwilliam Collection')

@section('content')
    <ul>
        @foreach($paginator->items() as $place)
            <li class="agents-list">
                <a href="{{ route('terminology', $place['key']) }}">
                    {{ $place['place']['hits']['hits'][0]['_source']['lifecycle']['creation'][0]['places'][0]['summary_title'] }}
                    - {{ $place['doc_count'] }} records
                </a>
            </li>
        @endforeach
    </ul>
@endsection

@section('pagination')
    @if($paginator->total() > 50)
        <div class="container-fluid bg-grey mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $paginator->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
