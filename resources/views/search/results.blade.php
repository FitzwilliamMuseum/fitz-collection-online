@extends('layouts.layout')
@section('title','Search results')
@section('description', 'Search results from our collection')
@section('keywords', 'search,results,collection,highlights,fitzwilliam')
@php
    $base = Request::query();
    if(array_key_exists('page', $base)){
      unset($base['page']);
    }
    $query = http_build_query($base)
@endphp
@section('search-box')
    <div class="container-fluid ">
        <div class="container">
            <div class="col-12 p-3 mx-auto mb-3 rounded">
                <p>
                    Your search for <strong>{{ $queryString }}</strong> returned
                    <strong>{{ number_format($records->total()) }}</strong> results.
                </p>

                @if($records->total() > 9999)
                    <p>You may want to refine your search for better results</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('search-results')
    <div class="container-fluid bg-gbdo">
        <div class="container p-3">
            <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
                {{ Form::open(['url' => url(route('results')),'method' => 'GET']) }}
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="query" class="visually-hidden">Your query</label>
                        <input type="text" id="query" name="query" class="form-control input-lg mr-4"
                               placeholder="Search our collection" required value="{{ $queryString }}">
                    </div>
                </div>

                <div class="row my-2">
                    <div class="col">
                        <h3 class="lead collection">Visual results</h3>
                        <div class="form-group form-check ">
                            <input type="checkbox" class="form-check-input" id="images" name="images"
                                   @if(request()->has('images'))
                                       checked
                                @endif
                            >
                            <label class="form-check-label" for="images">Only with images?</label>
                        </div>
                        <div class="form-group form-check ">
                            <input type="checkbox" class="form-check-input" id="iiif" name="iiif"
                                   @if(request()->has('iiif'))
                                       checked
                                @endif
                            >
                            <label class="form-check-label" for="iiif">Deep zoom enabled?</label>
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="lead collection">Operand for your search</h3>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="operator" id="operator" value="AND"
                                   checked>
                            <label class="form-check-label" for="operator">
                                AND
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="operator" id="operator" value="OR">
                            <label class="form-check-label" for="operator">
                                OR
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="collection lead">Sort by last update</h3>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sort" id="sort" value="desc" checked>
                            <label class="form-check-label" for="sort">
                                Descending
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sort" id="sort" value="asc">
                            <label class="form-check-label" for="sort">
                                Ascending
                            </label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-dark">Search</button>
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                data-bs-target="#exampleModal2">
                            Filter your search
                        </button>
                    </div>
                </div>
                @if(count($errors))
                    <div class="form-group">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('connected')
    <div class="container-fluid bg-white p-3">
        <div class="container">
            @if(!empty($records))
                <h3 class="lead">
                    Your Search Results
                </h3>
                <div class="row">
                    @foreach($records->items() as $record)
                        <x-search-result :record="$record"></x-search-result>
                    @endforeach
                </div>
            @endif


            <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-slideout" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Filter your search</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="d-flex">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if(!empty($facets))
                                <div class="filters">
                                    @if(array_key_exists('department', $facets) && !empty($facets['department']['buckets']) )
                                        <!-- Departments -->
                                        <div class="col mb-3">

                                            <div class="contents-label mb-3">
                                                <h5 class="lead">
                                                    <a data-bs-toggle="collapse" href="#department">Department</a>
                                                </h5>

                                                @if(array_key_exists('department', $facets))
                                                    <ul class="collapse" id="department">
                                                        @foreach ($facets['department']['buckets'] as $bucket)
                                                            <li>
                                                                <a href="{{ route('results') }}?{{ $query  }}&department={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}
                                                                    : {{ number_format($bucket['doc_count']) }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                    <!-- End of departments -->


                                    @if(array_key_exists('department', $facets) && !empty($facets['department']['buckets']) )

                                        <!-- Makers -->
                                        <div class="col mb-3">
                                            <div>
                                                <div>

                                                    <div class="contents-label mb-3">
                                                        <h5 class="lead">
                                                            <a data-bs-toggle="collapse" href="#maker">Maker</a>
                                                        </h5>

                                                        @if(array_key_exists('maker', $facets))
                                                            <ul class="collapse" id="maker">
                                                                @foreach ($facets['maker']['buckets'] as $bucket)
                                                                    <li>
                                                                        <a href="{{ route('results') }}?{{ $query  }}&maker={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}
                                                                            : {{ number_format($bucket['doc_count']) }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- Materials -->

                                    @if(array_key_exists('material', $facets) && !empty($facets['material']['buckets']) )
                                        <div class="col mb-3">

                                            <div class="contents-label mb-3">
                                                <h5 class="lead">
                                                    <a data-bs-toggle="collapse" href="#material">Material</a>
                                                </h5>

                                                @if(array_key_exists('material', $facets))
                                                    <ul class="collapse" id="material">
                                                        @foreach ($facets['material']['buckets'] as $bucket)
                                                            <li>
                                                                <a href="{{ route('results') }}?{{ $query  }}&material={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}
                                                                    : {{ number_format($bucket['doc_count'])}}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </div>

                                        </div>
                                    @endif
                                    <!-- End of materials -->

                                    <!-- Periods -->
                                    @if(array_key_exists('period', $facets) && !empty($facets['period']['buckets']) )

                                        <div class="col mb-3">

                                            <div class="contents-label mb-3">
                                                <h5 class="lead">
                                                    <a data-bs-toggle="collapse" href="#period">Period</a>
                                                </h5>

                                                @if(array_key_exists('period', $facets))
                                                    <ul class="collapse" id="period">
                                                        @foreach ($facets['period']['buckets'] as $bucket)
                                                            <li>
                                                                <a href="{{ route('results') }}?{{ $query  }}&period={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}
                                                                    : {{ number_format($bucket['doc_count']) }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </div>

                                        </div>
                                    @endif
                                    <!-- End of periods -->
                                    <!-- object_type -->
                                    @if(array_key_exists('object_type', $facets) && !empty($facets['object_type']['buckets']) )

                                        <div class="col mb-3">

                                            <div class="contents-label mb-3">
                                                <h5 class="lead">
                                                    <a data-bs-toggle="collapse" href="#object_type">Object Type</a>
                                                </h5>

                                                @if(array_key_exists('object_type', $facets))
                                                    <ul class="collapse" id="object_type">
                                                        @foreach ($facets['object_type']['buckets'] as $bucket)
                                                            <li>
                                                                <a href="{{ route('results') }}?{{ $query  }}&object_type={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}
                                                                    : {{ number_format($bucket['doc_count']) }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </div>

                                        </div>
                                    @endif
                                    <!-- End of object_type -->

                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('pagination')
    @if($records->total() > 24)
        <div class="container-fluid bg-grey mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $records->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
