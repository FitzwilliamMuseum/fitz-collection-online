@extends('layouts.qr')
@section('content')
<div class="center-text">
<img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->margin(10)->generate($data['admin']['uri']);!!}" class="img-fluid">
</div>
@endsection
