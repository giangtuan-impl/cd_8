@extends('layouts.app')

@section('title', trans('messages.create_new_application'))
@section('content')
    <main class="container">
        <div class="row panel-row">
            <div class="col-lg-6 col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <h2 class="panel-heading">{{ trans('messages.create_new_application') }}</h2>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('apps.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="app_name" class="control-label">{{ trans('messages.app_name') }}</label>
                                <input type="text" id="app_name" class="form-control" name="app_name" value="{{ old('app_name') }}">
                                @error('app_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ios_name" class="control-label">{{ trans('messages.ios_build_name') }}</label>
                                <input type="text" name="ios_name" class="form-control" id="ios_name" value="{{ old('ios_name') }}">
                                @error('ios_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="android_name" class="control-label">{{ trans('messages.android_build_name') }}</label>
                                <input type="text" name="android_name" class="form-control" id="android_name" value="{{ old('android_name') }}">
                                @error('android_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-sm-flex align-items-center justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-custom">{{ trans('messages.create') }}</button>
                                    <a href="{{ route('index') }}" type="button" class="btn btn-outline-secondary btn-custom">{{ trans('messages.cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
