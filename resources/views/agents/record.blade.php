@extends('layouts.layout')

@section('title', 'Details for ' . ucfirst($data['summary_title']))

@section('description', 'Details for ' . ucfirst($data['summary_title']) . ' an agent used in the Fitzwilliam Collection')

@section('content')
    <p>Full name: {{ $data['name'][0]['value'] }}</p>
    <p>
        This person or thing (agent) has been used <strong>{{ number_format($connected->total()) }}</strong> times
        within our collections systems.
    </p>

    <p>
        @if($makers > 0)
            As a maker of objects: {{number_format($makers)}} times.<br/>
        @endif

        @if($acquisition > 0)
            As an agent in the acquisition process: {{number_format($acquisition)}} times.<br/>
        @endif

        @if($owner > 0)
            As an owner of objects: {{number_format($owner)}} times.<br/>
        @endif

    </p>

    @if(!empty($axiell))
        <x-axiell-term-display :axiell="$axiell" :identifiers="$identifiers"></x-axiell-term-display>
    @endif

    @if(!empty($identifiers))
        <x-close-match-identifiers :identifiers="$identifiers"></x-close-match-identifiers>

        @if(!is_null($identifiers['ulan_id']))
            <x-ulan-biography :ulanID="$identifiers['ulan_id']" :agentID="$identifiers['axiell_id']"></x-ulan-biography>
        @endif

        @if(!is_null($identifiers['tgn_id']))
            <x-tgn-getty-lookup :tgnID="$identifiers['tgn_id']"></x-tgn-getty-lookup>
        @endif

        @if(!is_null($identifiers['nomisma_id']))
            <x-nomisma-lookup :nomismaID="$identifiers['nomisma_id']"></x-nomisma-lookup>
{{--            <x-nomisma-entity-lookup :nomismaID="$identifiers['nomisma_id']"></x-nomisma-entity-lookup>--}}
        @endif

    @endif
@endsection

@section('machine')
    @include('includes.elements.machine-cite-person')
@endsection

@section('connected')
    <div class="container-fluid bg-white">
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

@section('pagination')
    @if($connected->total() > 24)
        <div class="container-fluid bg-white mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $connected->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection

@section('map')
    @map([
        'lat' => 24.716667,
        'lng' => 46.716667,
        'zoom' => 6,
        'markers' => [
            [
                'title' => 'Place of origin',
                'lat' => 24.716667,
                'lng' => 46.716667,
                'popup' => 'Place of origin'
            ],
        ]
    ])
@endsection
