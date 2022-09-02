@extends('layouts.layout')
@section('title', 'Periods represented in the Fitzwilliam Collection')
@section('description', 'A list of all the periods represented in the Fitzwilliam Collection')
@section('content')
    <ul>
        @foreach($paginator->items() as $period)
            <li class="agents-list">
                <a href="{{ route('terminology', $period['key']) }}">
                    {{ $period['period']['hits']['hits'][0]['_source']['lifecycle']['creation'][0]['periods'][0]['summary_title'] }}
                    - {{ $period['doc_count'] }} records
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
