@extends('layouts.app')

@section('title', 'Home')
@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-6 col-md-12">
                <div class="panel panel-default time-line">
                    <h2 class="h4 text-gray-900 mb-4 text-center">{{ trans('messages.application_list') }}</h2>
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                @if(Agent::isMobile())
                    <div class="member-content">
                        <a href="{{ route('profile') }}">
                            <img src="{{ asset('image' . '/' . auth()->user()->getOriginal('avatar')) }}" class="avatar-icon rounded-circle" alt="image" onerror="this.src='{{ asset('assets/image/avatar.png') }}'">
                        </a>
                        <div class="mem-text">
                            <ul>
                                <li>
                                    <span>{{ Auth::user()->name }}</span>
                                </li>
                                <li>
                                    <span>{{ Agent::device() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success" role="alert" style="text-align: center;">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="member-list mb-4">
                    @foreach($applications as $application)
                        <a href="{{ route('apps.show', ['app' => $application->id ]) }}">
                            <div class="member-content">
                                <div class="mem-text">
                                    <h4>{{ $application->app_name }}</h4>
                                    <ul>
                                        <li>
                                            <span>{{ trans('messages.ios') }}</span>&nbsp&nbsp&nbsp
                                            @if(($application->buildNumbers()->latestIOSBuild())->exists())
                                                <span>#{{ $application->buildNumbers()->latestIosBuild()->build_number }}</span>
                                            @else
                                                <span>{{ trans('messages.not_available') }}</span>
                                            @endif
                                        </li>
                                        <li>
                                            <span>{{ trans('messages.android') }}</span>&nbsp&nbsp&nbsp
                                            @if(($application->buildNumbers()->latestAndroidBuild())->exists())
                                                <span>#{{ $application->buildNumbers()->latestAndroidBuild()->build_number }}</span>
                                            @else
                                                <span>{{ trans('messages.not_available') }}</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                @if($application->buildNumbers()->latestAppIcon()->exists())
                                    <div class="mem-text-right">
                                        <img src="{{ ($application->buildNumbers()->latestAppIcon()->app_icon) }}" class="avatar-icon" alt="image" onerror="this.src='{{ asset('assets/image/img-icon.png') }}'">
                                    </div>
                                @else
                                    <div class="mem-text-right">
                                        <img src="{{ asset('assets/image/img-icon.png') }}" class="avatar-icon" alt="image" onerror="this.src='{{ asset('assets/image/img-icon.png') }}'">
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
                @if(! Agent::isMobile())
                    @if(auth()->user()->role == 0)
                        <div class="text-center time-line">
                            <a href="{{ route('apps.create') }}" type="button" class="btn btn-primary">{{ trans('messages.create_new_application') }}</a>
                        </div>
                    @endif
                @else
                    <div class="text-center time-line">
                        <a href="{{ route('logout') }}" 
                        type="button" 
                        class="btn btn-secondary w-50"  
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                            {{ trans('messages.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                @endif
            </div>
            @if(! Agent::isMobile())
                <div class="col-lg-6 col-md-12 time-line">
                    <div class="panel panel-default">
                        <h2 class="h4 text-gray-900 mb-4 text-center">{{ trans('messages.timeline_list') }}</h2>
                    </div>
                    <div class="member-list member-list-event mb-4">
                        <!-- <div class="member-content">
                            <img src="{{ asset('assets/image/img-icon.png') }}" class="avatar-icon" alt="image">
                            <div class="mem-text">
                                <h4>Event 1</h4>
                                <span>By user 1</span>
                            </div>
                            <span class="mem-text-right">1 hour ago</span>
                        </div> -->
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
