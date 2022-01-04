<header class="navbar navbar-expand-md navbar-light header-background mb-5">
    @php
        $languages = App\Models\User::LANGUAGES;
    @endphp
    <i class="fas fa-angle-left fa-3x nav-icon-left backButton"></i>
    <nav class="container-fluid logo">
        <a href="{{ route('index') }}" class="navbar-branch center-logo">
            <img src="{{ asset('assets/image/logo.png') }}" alt="image" height="50px">
        </a>
        <ul class="navbar-nav position-absolute language-nav">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @foreach($languages as $key => $item)
                        <span>@if(Config::get('app.locale') == $item) {{ $key }} @endif</span>
                    @endforeach
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="languageDropdown">
                    @foreach($languages as $key => $item)
                        <a class="dropdown-item" href="{{ route('lang',['lang' => $item]) }}">{{ $key }}</a>
                    @endforeach
                </div>
            </li>
        </ul>
        @if(!Agent::isMobile())
            <ul class="navbar-nav ml-auto d-flex align-items-baseline">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @foreach($languages as $key => $item)
                            <span>@if(Config::get('app.locale') == $item) {{ $key }} @endif</span>
                        @endforeach
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="languageDropdown">
                        @foreach($languages as $key => $item)
                            <a class="dropdown-item" href="{{ route('lang',['lang' => $item]) }}">{{ $key }}</a>
                        @endforeach
                    </div>
                </li>
                @if(Auth::check())
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline">{{ auth()->user()->name }}</span>
                            <img class="img-profile rounded-circle" src="{{ asset('image' . '/' . auth()->user()->getOriginal('avatar')) }}" alt="avatar" onerror="this.src='{{ asset('assets/image/avatar.png') }}'">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profile') }}">{{ trans('messages.my_account') }}</a>
                            @if(auth()->user()->role == 0)
                                <a class="dropdown-item" href="{{ route('members.index') }}">{{ trans('messages.member_manage') }}</a>
                            @endif
                            <div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                    {{ trans('messages.logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </li>
                @endif
            </ul>
        @endif
    </nav>
</header>
