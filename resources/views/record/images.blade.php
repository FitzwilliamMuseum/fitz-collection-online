@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title', 'Image gallery for ' . $data[0]['_source']['identifier'][0]['accession_number'])
@section('content')
  <div class="row ">
    @foreach($paginate as $media)
      <div class="col-md-4 mt-3">
        <div class="card card-body h-100">
          <a href="/id/image/{{ $media['admin']['id']}}"><img class="img-fluid mx-auto d-block" src="https://api.fitz.ms/mediaLib/{{ $media['processed']['preview']['location'] }}"
            loading="lazy" alt="An image of "
            /></a>
            <span class="btn btn-wine m-1 mt-3 mb-3 p-2 share">
              <a href="https://api.fitz.ms/mediaLib/{{ $media['processed']['original']['location'] }}" target="_blank"
              download="{{ basename($media['processed']['original']['location'] ) }}"><i class="fas fa-download mr-2"></i>  Download this image</a>
            </span>
          </div>
        </div>
      @endforeach
    </div>
    <nav aria-label="Page navigation" class="mt-3">
      {{ $paginate->links() }}
    </nav>
@endsection
