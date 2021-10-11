@extends('layouts.qr')
@section('content')
<div class="container-fluid">
  <div class="text-center py-5  ">
    {!! QrCode::format('svg')->size(500)->generate($data['admin']['uri']) !!}
  </div>
</div>
@endsection
