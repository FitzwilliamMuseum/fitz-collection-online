@extends('layouts.qr')
@section('content')
<div class="center-text">
<img src="data:image/png;base64,{!!QrCode::size(500)->format('png')->margin(10)->generate($data['admin']['uri']);!!}" class="img-fluid">
</div>
@endsection
