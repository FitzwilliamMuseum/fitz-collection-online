@extends('layouts.slow')
@section('content')
<div id="osd-viewer">
    <div id="modal" class="modal">
      <div class="modal__inner">
        <div class="modal__text">
          <div class="modal__header">
            <h1 class="modal__title">Slow looking</h1>
          </div>

          <div class="modal__body">
            <p>Inspired by Cogapp's original slow looking, you are about to slowly immerse yourself in this image. </p>
            <p>To exit, just click anywhere.</p>
          </div>

          <div class="modal__actions">
            <a id="back" class="modal__button" href="/id/image/{{ Request::get('image') }}">Return to the record</a>
            <a id="slowlooking-start" class="modal__button" href="#">Try it now</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="intro">
@endsection
