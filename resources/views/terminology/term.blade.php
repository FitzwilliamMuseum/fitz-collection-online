@extends('layouts.layout')
@section('title', ucfirst($data['summary_title']))
@section('description', 'A term description and associated records for '. ucfirst($data['summary_title']))
@section('content')
    @if(array_key_exists('description', $data))
        @foreach($data['description'] as $description)
            <h3 class="lead collection">
                {{ ucfirst($description['type']) }}
            </h3>
            <p>
                {{ ucfirst($description['value']) }}
            </p>
        @endforeach
    @endif

    @if($connected->total() > 0)
        <p>
            This term has <strong>{{ number_format($connected->total())}}</strong> records attributed within our system.
        </p>
    @endif

    @if(!empty($axiell))
        <x-axiell-term-display :axiell="$axiell" :identifiers="$identifiers"></x-axiell-term-display>
    @endif

    @if(!empty($identifiers))
        @dd($identifiers)
        @if(!empty($identifiers['aat_id']))
            <x-aat-getty-lookup :aatID="$identifiers['aat_id']"></x-aat-getty-lookup>
        @endif

        @if(!empty($identifiers['tgn_id']))
            <x-tgn-getty-lookup :tgnID="$identifiers['tgn_id']"></x-tgn-getty-lookup>
        @endif

        <x-close-match-identifiers :identifiers="$identifiers"></x-close-match-identifiers>

        @if(!is_null($identifiers['nomisma_id']))
            <x-nomisma-lookup :nomismaID="$identifiers['nomisma_id']"></x-nomisma-lookup>
        @endif
    @endif

@endsection

@if(!empty($connected) && $connected->total() > 0)
    @section('connected')
        <div class="container-fluid bg-pastel py-2">
            <div class="container">
                <h3 class="lead collection">Connected records</h3>
                <div class="row">
                    @foreach($connected as $record)
                        <x-search-result :record="$record"></x-search-result>
                    @endforeach
                </div>
            </div>
        </div>
    @endsection
@endif

@section('machine')
    @include('includes.elements.machine-cite-term')
@endsection


@section('pagination')
    @if($connected->total() > 24)
        <div class="container-fluid bg-white mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $connected->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
