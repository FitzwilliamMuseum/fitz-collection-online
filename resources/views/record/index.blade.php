@extends('layouts.layout')
@section('content')

    <div class="row">

        @include('includes.elements.media')

        <div class="col-md-12 mb-3 object-info">
            <h2 class="visually-hidden">Object information</h2>
            <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">

                <div class="">
                    <x-display-status :location="$location"></x-display-status>
                    @include('includes.elements.descriptive')
                    @include('includes.elements.legal')
                    @include('includes.elements.measurements')
                    @include('includes.elements.geo')

                    @hasSection('map')
                        <div class="map-box container mb-3">
                            @yield('map')
                        </div>
                    @endif

                    @include('includes.elements.lifecycle')

                    @include('includes.elements.expander')

                    <div id="expand-more" class="collapse">
                        @include('includes.elements.agents-subjects')
                        @include('includes.elements.medium')
                        @include('includes.elements.component')
                        @include('includes.elements.materials')
                        @include('includes.elements.techniques')
                        @include('includes.elements.inscriptions')
                        @include('includes.elements.publications')
                        @include('includes.elements.exhibitions')
                        @include('includes.elements.identification')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('machine')
    @include('includes.elements.machine-cite')
@endsection

{{-- @section('mlt')
    <x-more-like-this-objects :data="$data"></x-more-like-this-objects>
    <x-more-like-this-shopify :data="$data"></x-more-like-this-shopify>
    <x-more-like-this-research :data="$data"></x-more-like-this-research>
@endsection --}}
