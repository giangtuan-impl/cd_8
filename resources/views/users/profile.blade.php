@extends('layouts.app')

@section('title', trans('messages.profile'))
@section('style')
    <link rel="stylesheet" href="{{ mix('assets/css/responsive.css') }}" type="text/css">
@endsection
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <h2 class="h4 text-gray-900 mb-4 text-center">{{ trans('messages.profile') }}</h2>
            @if (session('success'))
                <div class="alert alert-success" role="alert" style="text-align: center;">
                    {{ session('success') }}
                </div>
            @endif
        </div>
        <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-4 col-md-12 order-md-last avatar-wrap">
                    <div class="avatar">
                        <input hidden id="cat-image" type="file" name="avatar" accept="image/*" onchange="readURL(this);">
                        <label for="cat-image">
                            <img id="file-image" src="{{ auth()->user()->avatar }}" class="av-section-color-overlay" alt="avatar" onerror="this.src='{{ asset('assets/image/avatar.png') }}'">
                            <div id="start">
                                <span id="file-upload-btn" class="avatar__btn">{{ trans('messages.edit') }}</span>
                            </div>
                            @error('avatar')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </label>

                    </div>
                </div>
                <div class="col-lg-6 col-md-12 order-md-first">
                    <div class="form-group">
                        <label for="name">{{ trans('messages.name') }}</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ auth()->user()->name }}">
                        @error('name')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">{{ trans('messages.email') }}</label>
                        <input type="text" class="form-control" name="email" id="email" value="{{ auth()->user()->email }}" readonly>
                    </div>
                    @php
                        $languages = App\Models\User::LANGUAGES;
                    @endphp
                    <div class="form-group">
                        <label for="language">{{ trans('messages.language') }}: </label>
                        <select name="language">
                            @foreach ($languages as $key => $value)
                                <option value="{{$value}}" @if( $value == auth()->user()->language ) selected @endif>{{ $key }}</option>
                            @endforeach
                        </select>
                        @error('language')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group btn-mobile">
                        <button type="submit" class="btn btn-outline-secondary btn-profile col-6 col-md-4">{{ trans('messages.update_profile') }}</button><br><br>
                        <a href="{{ route('change_password') }}" class="btn btn-outline-secondary btn-profile col-6 offset-md-4 col-md-4">{{ trans('messages.change_password_title') }}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#file-image').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#cat-image").change(function(){
            readURL(this);
        });
    </script>
@endsection
