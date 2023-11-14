@extends('layouts.layout')
@section('title', 'Image gallery for ' . $data['identifier'][0]['accession_number'])
@section('connected-images')
    <div class="container-fluid bg-grey mb-3">
        <div class="container mb-3">
            <a class="mt-3 mb-3 btn btn-dark" href="{{ route('record', [$data['identifier'][1]['priref']]) }}">
                Return to record
            </a>
            <div class="row mb-3">
                @foreach($paginate as $media)
                    <div class="col-md-4 mt-3 mb-3">
                        <div class="card card-body h-100">
                            <a href="{{ route('image.single', $media['admin']['id']) }}">
                                <x-image-place-holder :path="$media['processed']['preview']['location']" :altText="''" :classes="'img-fluid mx-auto d-block'"></x-image-place-holder>
                            </a>
                            <div>
                                    <a href="{{ route('image.single', $media['admin']['id']) }}" class="m-1 mt-3 mb-3 p-2 btn btn-dark" >
                                        @svg('fas-eye',['width' => 15]) View this image
                                    </a>
                                    <x-image-download-link :path="$media['processed']['original']['location']"></x-image-download-link>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('pagination')
    <div class="container-fluid mb-5 p-4 text-center">
        <nav aria-label="Page navigation">
            {{ $paginate->appends(request()->except('page'))->links() }}
        </nav>
    </div>
@endsection
