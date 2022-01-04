@extends('layouts.app')

@section('title', trans('messages.edit_application'))
@section('content')
    <main class="container">
        @include('layouts.alert')
        <div class="row panel-row">
            <div class="col-lg-6 col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <h2 class="panel-heading">{{ trans('messages.edit_application') }}</h2>
                    <div class="panel-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert" style="text-align: center;">
                            {{ session('success') }}
                        </div>
                    @endif
                        <form class="form-horizontal" action="{{ route('apps.update', ['app' => $app->id])}}" method="POST" >
                            @method('PUT')
                            @csrf
                            <div class="form-group">
                                <label for="app_name" class="control-label">{{ trans('messages.app_name') }}</label>
                                <input type="text" id="app_name" class="form-control" name="app_name" value="{{ old('app_name',$app->app_name) }}" autofocus>
                                @error('app_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ios_name" class="control-label">{{ trans('messages.ios_build_name') }}</label>
                                <input type="text" class="form-control" id="ios_name" name="ios_name" value="{{ old('ios_name',$app->ios_name) }}">
                                @error('ios_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="android_name" class="control-label">{{ trans('messages.android_build_name') }}</label>
                                <input type="text" class="form-control" id="android_name" name="android_name" value="{{ old('android_name',$app->android_name) }}">
                                @error('android_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-sm-flex align-items-center justify-content-between">
                                    <button type="submit" class="btn btn-outline-secondary btn-custom">{{ trans('messages.edit') }}</button>
                                    <a href="{{ route('apps.show',['app' => $app->id])}}" type="button" class="btn btn-outline-secondary btn-custom">{{ trans('messages.cancel') }}</a>
                                    <button type="button" class="btn btn-danger btn-custom" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#deleteApp">{{ trans('messages.delete') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Popup delete app -->
    <div class="modal fade" id="deleteApp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="delete-member">{{ trans('messages.delete_application_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ trans('messages.delete_this_application') }}
                </div>
                <div class="modal-footer">
                    <form action="{{ route('apps.destroy', $app->id)}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="delete" class="btn btn-primary">{{ trans('messages.confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
