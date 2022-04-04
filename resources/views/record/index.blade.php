@extends('layouts.layout')
@section('content')

    <div class="row">

    @foreach($data as $record)
        @if(array_key_exists('multimedia', $record['_source']))
            @if(array_key_exists('title', $record['_source']))
                @section('hero_image_title', ucfirst($record['_source']['title'][0]['value']))
    @else
        @section('hero_image_title', ucfirst($record['_source']['summary_title']))

    @endif
    @if(array_key_exists('mid',$record['_source']['multimedia'][0]['processed']))
        @section('hero_image', env('APP_URL') . '/imagestore/' . $record['_source']['multimedia'][0]['processed']['mid']['location'])
    @endif
    @else
        @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
    @section('hero_image_title', "The inside of our Founder's entrance")
    @endif

        <!-- multimedia section start -->
    @include('includes/elements/media')
    <!-- multimedia section end -->

        <div class="col-md-12 mb-3 object-info">
            <h2 class="sr-only">Object information</h2>
            <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">

                <div class="">

                    @include('includes/elements/descriptive')
                    @include('includes/elements/legal')
                    @include('includes/elements/measurements')
                    @include('includes/elements/geo')
                    @hasSection('map')
                        <div class="map-box container mb-3">
                            @yield('map')
                        </div>
                    @endif
                    @include('includes/elements/lifecycle')

                    @include('includes/elements/expander')
                    <div id="expand-more" class="collapse">
                        @include('includes/elements/agents-subjects')
                        @include('includes/elements/medium')
                        @include('includes/elements/component')
                        @include('includes/elements/materials')
                        @include('includes/elements/techniques')
                        @include('includes/elements/inscriptions')
                        @include('includes/elements/publications')
                        @include('includes/elements/exhibitions')
                        @include('includes/elements/identification')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('machine')
    @include('includes/elements/machine-cite')
    @endforeach
    @include('includes/elements/morelike')

@endsection
