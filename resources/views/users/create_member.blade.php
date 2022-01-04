@extends('layouts.app')

@section('title', trans('messages.create_new_member'))
@section('content')
    <main class="container">
        @include('layouts.alert')
        <div class="row panel-row">
            <div class="col-lg-6 col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel panel-default">
                        <h3 class="h2 text-gray-900 mb-4 text-center">{{ trans('messages.create_new_member') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('members.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{ trans('messages.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">{{ trans('messages.email') }}</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{old('email')}}">
                                @error('email')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @php
                                $roles = App\Models\User::ROLES;
                                $languages = App\Models\User::LANGUAGES;
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">{{ trans('messages.role') }}: </label>
                                        <select name="role">
                                            @foreach ($roles as $key => $value)    
                                                <option value="{{$value}}" @if( $value == old('role') ) selected @endif>{{ trans('messages.' . $key) }}</option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language">{{ trans('messages.language') }}: </label>
                                        <select name="language">
                                            @foreach ($languages as $key => $value) 
                                                <option value="{{$value}}" @if( $value == old('language') ) selected @endif>{{ $key }}</option>
                                            @endforeach
                                        </select>
                                        @error('language')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="d-sm-flex align-items-center justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-custom">{{ trans('messages.create') }}</button>
                                    <a href="{{ route('members.index') }}" type="button" class="btn btn-outline-secondary btn-custom">{{ trans('messages.cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
