@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title', 'Image gallery for ' . $data[0]['_source']['identifier'][0]['accession_number'])
@section('connected-images')
  <div class="container-fluid bg-grey mb-3">
    <div class="container mb-3">
      <a class="mt-3 mb-3 btn btn-dark" href="{{ route('record', [$data[0]['_source']['identifier'][1]['priref']]) }}">Return to record</a>
      <div class="row mb-3">
        @foreach($paginate as $media)
          <div class="col-md-4 mt-3 mb-3">
            <div class="card card-body h-100">
              <a href="/id/image/{{ $media['admin']['id']}}"><img class="img-fluid mx-auto d-block" src="{{ env('APP_URL')}}/imagestore/{{ $media['processed']['preview']['location'] }}"
                loading="lazy" alt="An image of "
                /></a>
                <div>
                  @if(array_key_exists('zoom', $media['processed']))
                    <span class="btn btn-wine m-1 p-2 share ">
                      <a href="/id/image/iiif/{{ $media['admin']['id']}}" ><img src="/images/logos/iiif.svg" width="20px" /></a>
                    </span>
                  @endif
                  <span class="btn btn-wine m-1 mt-3 mb-3 p-2 share">
                    <a href="/id/image/{{ $media['admin']['id']}}"><i class="fas fa-eye"></i></a>
                  </span>
                  <span class="btn btn-wine m-1 mt-3 mb-3 p-2 share">
                    <a href="{{ env('APP_URL')}}/imagestore/{{ $media['processed']['original']['location'] }}" target="_blank"
                    download="{{ basename($media['processed']['original']['location'] ) }}"><i class="fas fa-download"></i>
                  </a>
                  </span>
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
      <nav aria-label="Page navigation" >
        {{ $paginate->appends(request()->except('page'))->links() }}
      </nav>
    </div>
  @endsection
