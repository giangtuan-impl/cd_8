@extends('layouts.app')

@section('title', trans('messages.change_password_title'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-sm4 col-xs-12">
                <div class="panel panel-default">
                    <h2 class="h4 text-gray-900 mb-4 text-center">{{ trans('messages.change_password_title') }}</h2>
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
                <form class="form-horizontal" method="POST" action="{{ route('change_password') }}">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                        <label for="curent_password">{{ trans('messages.old_password') }}</label>
                        <input id="current_password" type="password" class="form-control" name="current_password">
                        @if ($errors->has('current_password'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('current_password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                        <label for="new_password">{{ trans('messages.new_password') }}</label>
                        <input id="new_password" type="password" class="form-control" name="new_password">
                        @if ($errors->has('new_password'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('new_password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirm">{{ trans('messages.confirm_password') }}</label>
                        <input id="new_password_confirm" type="password" class="form-control" name="new_password_confirm">
                        @if ($errors->has('new_password_confirm'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('new_password_confirm') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">{{ trans('messages.change_password_button') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection