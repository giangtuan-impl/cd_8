@extends('layouts.app')

@section('title', trans('messages.login'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-sm4 col-xs-12">
                <div class="panel panel-default">
                    <h2 class="h4 text-gray-900 mb-4 text-center">{{ trans('messages.login') }}</h2>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">{{ trans('messages.email') }}</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" autocomplete="email" autofocus value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">{{ trans('messages.password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group form-check text-center">
                        <input type="checkbox" class="form-check-input" id="keepMeLogin" name="keepMeLogin">
                        <label class="form-check-label" for="keepMeLogin">{{ trans('messages.keep_me_logged_in') }}</label>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">{{ trans('messages.login') }}</button>
                    </div>
                </form>
                <div class="text-center">
                    <a href="{{ route('password_remind')}}">{{ trans('messages.forgot_password') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection