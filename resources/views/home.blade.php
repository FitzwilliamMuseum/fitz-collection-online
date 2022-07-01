@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Two Factor Authentication') }}</div>
                    @dump(request()->bearerToken())
                    <div class="card-body">
                        @if (session('status') == "two-factor-authentication-disabled")
                            <div class="alert alert-danger" role="alert">
                                Two factor authentication has been disabled
                            </div>
                        @endif

                        @if (session('status') == "two-factor-authentication-enabled")
                            <div class="alert alert-success" role="alert">
                                Two factor authentication has been enabled
                            </div>
                        @endif
                        <form method="post" action="/user/two-factor-authentication">
                            @csrf


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
