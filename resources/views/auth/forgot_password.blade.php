@extends('layouts.app')

@section('title', trans('messages.forgot_password'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-sm4 col-xs-12">
                <div class="panel panel-default">
                    <h2 class="h4 text-gray-900 mb-4 text-center">{{ trans('messages.forgot_password') }}</h2>
                    <p> {{ trans('messages.forgot_password_instructure') }}</p>
                </div>
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password_remind') }}">
                    @csrf
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">{{ trans('messages.email') }}</label>
                        <div class="col-sm-10">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" autocomplete="email" autofocus>
                            @error('email')
                                <span class="text-danger ">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">{{ trans('messages.send_request') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection