@extends('layouts.layout')
@section('title', 'Terminology represented in the Fitzwilliam Collection')
@section('description', 'A list of all the Terminology represented in the Fitzwilliam Collection')
@section('content')
    <ul>
        @foreach($paginator->items() as $agent)
            <li class="agents-list">
                <a href="{{ route('terminology', $agent['_source']['admin']['id']) }}">
                    {{ $agent['_source']['summary_title'] }}
                </a>
            </li>
        @endforeach
    </ul>
@endsection

@section('pagination')
    @if($paginator->total() > 50)
        <div class="container-fluid bg-grey mb-5 p-4 text-center">
            <nav aria-label="Page navigation ">
                {{ $paginator->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
