@extends('layouts.slow')
@section('title','Take a slow look at ' . $object['title'][0]['value'] ?? $object['summary_title'] )
@section('description', 'A slow looking IIIF image of '. $object['title'][0]['value'])
@section('hero_image_title', ucfirst($object['title'][0]['value']))
@section('hero_image', env('APP_URL') . '/imagestore/' . $object['multimedia'][0]['processed']['mid']['location'])
@section('content')
<div id="osd-viewer">
    <div id="modal" class="modal">
      <div class="modal__inner">
        <div class="modal__text">
          <div class="modal__header">
            <h1 class="modal__title">Slow looking</h1>
          </div>

          <div class="modal__body">
            <p>
                Inspired by Cogapp's original slow looking, you are about to slowly immerse yourself in this image.
            </p>
            <p>
                To exit, just click anywhere.
            </p>
          </div>

          <div class="modal__actions">
            <a id="back" class="modal__button" href="{{ route('image.single', Request::get('image')) }}">Return to the record</a>
            <a id="slowlooking-start" class="modal__button" href="#">Try it now</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
