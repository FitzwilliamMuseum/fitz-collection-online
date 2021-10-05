@extends('layouts.qr')
@section('content')
<div class="container-fluid">
  <div class="text-center">
    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($data['admin']['uri'])) !!}" class="py-5 img-fluid">
  </div>
</div>
@endsection
