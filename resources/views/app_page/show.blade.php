@extends('layouts.app')

@section('title', trans('messages.application'))
@section('style')
{{-- <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}"> --}}
@endsection
@section('content')
    <div class="container-fluid main">
    @include('layouts.alert')
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h2 class="title">{{ $application->app_name }}</h2>
            @if(Auth::user()->isAdmin())
                <div class="navbar navbar-expand-md setting-dropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown no-arrow" >
                            <a class="nav-link dropdown-toggle" href="#" id="settingDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <button class="d-none d-sm-inline-block btn btn-outline-secondary"><i class="fas fa-cog"></i><span>&nbsp;&nbsp;&nbsp;</span>{{ trans('messages.settings') }}</button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="settingDropdown">
                                <a class="dropdown-item">
                                    @if(Auth::user()->isAdmin())
                                        <span data-toggle="modal" data-target="#invitemember" style="cursor: pointer;">{{ trans('messages.add_new_member') }}</span>
                                    @endif
                                </a>
                                <a class="dropdown-item" href="{{ route('apps.build_numbers.show', ['app' => $application->id]) }}">{{ trans('messages.previous_version') }}</a>

                                <button class="dropdown-item btn btn-outline-secondary" data-toggle="modal" data-target="#showUUIDs">{{ trans('messages.show_all_uuids') }}</button>

                                <a class="dropdown-item" href="{{ route('apps.edit', $application->id) }}">{{ trans('messages.edit_application') }}</a>
                            </div>
                        </li>
                    </ul>
                </div>
           @endif
        </div>
        <div class="row mb-4">
            <div class="col-lg-6 col-md-12 info-app IOSApp">
                <img src="" class="icon-app" alt="image" onerror="this.onerror=null;this.src='@if(isset($iosBuild->app_icon)) {{ $iosBuild->app_icon }} @else {{ asset(config('constants.DEFAULT_IMAGE_APP')) }} @endif';">
                <div class="app-index">
                    <h5>{{ trans('messages.ios') }}</h5>
                    <ul>
                        @if(isset($iosBuild))
                            <li>
                                {{ isset($iosBuild->bundle_id) ? $iosBuild->bundle_id : trans('messages.bundle_id') }}
                            </li>
                            <li>
                                {{ trans('messages.build_number') }}: @if(isset($iosBuild->build_number)) {{ $iosBuild->build_number }} @endif
                            </li>
                            <li>
                                {{ trans('messages.updated_at') }}: @if(isset($iosBuild->build_date)) {{ Carbon\Carbon::parse($iosBuild->build_date)->format('Y/m/d H:i:s') }} @endif
                            </li>
                        @else
                            <li>
                                {{ trans('messages.no_build') }}
                            </li>
                        @endif
                    </ul>
                </div>
                @if(isset($iosBuild))
                    <button type="button" class="btn btn-outline-secondary qr-code" data-toggle="modal" data-target="#qrcodeios">
                        {{ trans('messages.show_qr_code') }}</button>
                    <a class="btn btn-outline-secondary link-download" href="@if(isset($iosBuild->id)) {{ config('constants.IOS_PARAMS.PLIST_DOWNLOAD_LINK') . route('build.plist-download', ['id' => $iosBuild->id]) }} @endif" target="_blank">{{ trans('messages.click_here_to_download') }}</a>
                @endif
                <div class="modal fade" id="qrcodeios" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{ trans('messages.qr_code') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body qr_code">
                                @if (isset($iosBuild->id))
                                {!! QrCode::size(150)->generate(route('apps.show', ['app' => $application->id])); !!}
                                @else
                                {!! QrCode::size(150)->generate('QR Code'); !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(Agent::isMobile())
                <div class="IOSApp w-100">
                    <h5 class="mt-5 mb-2 ml-3">{{ trans('messages.previous_version') }}</h5>
                    <div class="member-list mb-2">
                        @if(isset($previousIosBuilds))
                            @foreach($previousIosBuilds as $iosBuild)
                                <a class="previous-version-items" href="{{ route('apps.show', ['app' => $application->id, 'buildNumberVersion' => $iosBuild->id]) }}">
                                    <div class="member-content">
                                        <img src="" class="avatar-icon" alt="android_icon" onerror="this.onerror=null;this.src='@if(isset($iosBuild->app_icon)) {{ $iosBuild->app_icon }} @else {{ url(config('constants.DEFAULT_IMAGE_APP')) }} @endif';">
                                        <div class="mem-text">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h5>#{{ $iosBuild->build_number }}</h5>&nbsp;
                                                <span>{{ $iosBuild->version_number }}</span>&nbsp;
                                                <span>{{ $iosBuild->version_code_number ? '(' . $iosBuild->version_code_number . ')' : ''}}</span>
                                            </div>

                                            <span>{{ Carbon\Carbon::parse($iosBuild->build_date)->format('Y/m/d H:i:s') }}</span>
                                        </div>
                                        <span class="mem-text-right">
                                            <h4><i class="fas fa-chevron-right"></i></h4>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
            <div class="col-lg-6 col-md-12 info-app AndroidApp">
                <img src="" class="icon-app" alt="android_icon" onerror="this.onerror=null;this.src='@if(isset($androidBuild->app_icon)) {{ $androidBuild->app_icon }} @else {{ url(config('constants.DEFAULT_IMAGE_APP')) }} @endif';">
                <div class="app-index">
                    <h5>{{ trans('messages.android') }}</h5>
                    <ul>
                        @if(isset($androidBuild))
                            <li>
                                {{ isset($androidBuild->bundle_id) ? $androidBuild->bundle_id : trans('messages.bundle_id') }}
                            </li>
                            <li>
                                {{ trans('messages.build_number') }}: @if(isset($androidBuild->build_number)) {{ $androidBuild->build_number }} @endif
                            </li>
                            <li>
                                {{ trans('messages.updated_at') }}: @if(isset($androidBuild->build_date)) {{ Carbon\Carbon::parse($androidBuild->build_date)->format('Y/m/d H:i:s') }} @endif
                            </li>
                        @else
                            <li>
                                {{ trans('messages.no_build') }}
                            </li>
                        @endif
                    </ul>
                </div>
                @if(isset($androidBuild))
                    <button type="button" class="btn btn-outline-secondary qr-code" data-toggle="modal" data-target="#qrcodeandroid">{{ trans('messages.show_qr_code') }}</button>
                    <a class="btn btn-outline-secondary link-download" href="@if(isset($androidBuild->id)) {{ route('build.download', ['id' => $androidBuild->id]) }} @endif" target="_blank">{{ trans('messages.click_here_to_download') }}</a>
                @endif
                <div class="modal fade" id="qrcodeandroid" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{ trans('messages.qr_code') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body qr_code">
                                @if (isset($androidBuild->id))
                                {!! QrCode::size(150)->generate(route('apps.show', ['app' => $application->id])); !!}
                                @else
                                {!! QrCode::size(150)->generate('QR Code'); !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(Agent::isMobile())
                <div class="AndroidApp w-100">
                    <h5 class="mt-5 mb-2 ml-3">{{ trans('messages.previous_version') }}</h5>
                    <div class="member-list mb-2">
                        @if(isset($previousAndroidBuilds))
                            @foreach($previousAndroidBuilds as $androidBuild)
                                <a class="previous-version-items" href="{{ route('apps.show', ['app' => $application->id, 'buildNumberVersion' => $androidBuild->id]) }}">
                                    <div class="member-content">
                                        <img src="" class="avatar-icon" alt="android_icon" onerror="this.onerror=null;this.src='@if(isset($androidBuild->app_icon)) {{ $androidBuild->app_icon }} @else {{ url(config('constants.DEFAULT_IMAGE_APP')) }} @endif';">
                                        <div class="mem-text">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h5>#{{ $androidBuild->build_number }}</h5>&nbsp;
                                                <span>{{ $androidBuild->version_number }}</span>&nbsp;
                                                <span>{{ $androidBuild->version_code_number ? '(' . $androidBuild->version_code_number . ')' : ''}}</span>
                                            </div>

                                            <span>{{ Carbon\Carbon::parse($androidBuild->build_date)->format('Y/m/d H:i:s') }}</span>
                                        </div>
                                        <span class="mem-text-right">
                                            <h4><i class="fas fa-chevron-right"></i></h4>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>
        @if(! Agent::isMobile())
            <div class="show-member">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h3>{{ trans('messages.member') }}</h3>
                </div>
                <div class="member-list mb-4" id ="memberList" style="height: 50vh !important;">
                    @foreach ($members as $member)
                        <div class="select-member" data-id="{{$member->id}}">
                            <img src="{{$member->avatar}}" class="avatar-icon" alt="image">
                            <div class="mem-text">
                                <h5>{{$member->name}}</h5>
                                <span>{{$member->email}}</span>
                            </div>
                            @if(Auth::user()->isAdmin())
                                <!-- userLogged cannot remove themselves from list -->
                                @if(Auth::user()->id != $member->id)
                                    <button type="button" class="close remove-invited-member" data-member-name="{{$member->name}}" data-route-delete="{{ route('apps.remove-member', ['app' => $application->id, 'member' => $member->id]) }}">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <!-- Popup show UUIDs -->
    <div class="modal fade" id="showUUIDs" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('messages.ios_device_uuids_list') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul>
                        @if (isset($iosBuild->uuid_list))
                            @foreach ($iosBuild->uuid_list as $uuid)
                            <li>
                                {{ $uuid }}
                            </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Popup invite member -->
    <div class="modal fade" id="invitemember" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('messages.invite_members') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body body-search">
                    <div class="member-search">
                        <form>
                            <i class="fas fa-search"></i>
                            <input type="hidden" id="appId" value="{{$application->id}}">
                            <input type="text" name="search" placeholder="{{ trans('messages.search') }}" id="search" autocomplete="off"/>
                        </form>
                        <div class="wrapper-search-result">
                            <ul class="search-result" id="searchResult">
                            </ul>
                        </div>
                    </div>
                    <div class="scroll-mem">
                    </div>
                </div>
                <div class="modal-footer footer-search">
                    <button type="button" class="btn btn-secondary" id="btnSubmitInvite">{{ trans('messages.invite_members_button') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Popup delete member -->
    <div class="modal fade" id="deleteMember" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('messages.delete_member_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ trans('messages.delete_member_title') }}
                    <span id="confirmDeleteMemberAlert"></span> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.close') }}</button>
                    <form action="" method="POST" id="frmRemoveMember">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary">{{ trans('messages.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    let selectedMemberId = [];
    let selectedMember = [];
    const appId = $("#appId").val();

    function debounce(func, wait) {
        var timeout;

        return function() {
            var context = this,
                args = arguments;

            var executeFunction = function() {
                func.apply(context, args);
            };

            clearTimeout(timeout);
            timeout = setTimeout(executeFunction, wait);
        };
    };

    var handleSearch = debounce(function (e) {
        // do stuff here
        const key = $(this).val();

        $("#searchResult").empty();

        if (!key) {
            $(".wrapper-search-result").hide();
            return;
        }
        $(".wrapper-search-result").show();

        axios.get(`/members/search?app_id=${appId}&key=${key}`)
        .then(function (response) {
            if (response.data.length == 0) {
                const user = `<li>{{trans('messages.no_result_for_keyword')}}: "${key}"</li>`;

                $("#searchResult").append(user);
            }
            $.each(response.data, (index, item) => {
                const user = `<li class="invite" data-id=${item.id} data-img=${item.avatar} data-name="${item.name}" data-email=${item.email}>${item.email}</li>`;

                $("#searchResult").append(user);
            });
            $("#searchResult").show();
        })
        .catch(function (error) {
        })
    }, 500);

    $("#search").keydown(handleSearch);
    $("#search").on('click', function () {
        if ($("#search").val()) {
            $(".wrapper-search-result").show();
        }
    });

    $(document).click(function(e) {
        var $target = $(e.target);
        if(!$target.closest('.member-search').length) {
            $(".wrapper-search-result").hide();
        }
    });

    $(document).on('click', '.invite', function() {
        const memberId = $(this).attr("data-id");
        const memberAvatar = $(this).attr("data-img");
        const memberEmail = $(this).attr("data-email");
        const memberName = $(this).attr("data-name");

        if (selectedMemberId.includes(memberId)) {
            $(".wrapper-search-result").hide();
            return;
        }
        selectedMemberId.push(memberId);

        const selectMember =   $(`<div class="select-member" data-id="${memberId}">
                                    <img src="${memberAvatar}" class="avatar-icon" alt="image">
                                    <div class="mem-text">
                                        <h5>${memberName}</h5>
                                        <span>${memberEmail}</span>
                                    </div>
                                    <button type="button" class="close remove-selected-member" data-id="${memberId}">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>`);
        selectedMember.push({
            id: memberId,
            avatar: memberAvatar,
            email: memberEmail,
            name: memberName,
        });
        $(".scroll-mem").prepend(selectMember);
        $(".wrapper-search-result").hide();
    });

    $("#btnSubmitInvite").click(function (e) {
        e.preventDefault();
        if (selectedMemberId.length == 0) {
            swal("{{ trans('messages.warning') }}","{{ trans('messages.please_select_at_least_one_member') }}", "warning");
            return;
        }
        axios.post(`/apps/invite`, {
            selectedMemberId,
            appId: $("#appId").val()
        })
        .then(function (response) {
            const { inviteMember } = response.data;
            swal("{{ trans('messages.success') }}","{{ trans('messages.invite_member_successfully') }}", "success");

            $.each(selectedMember, (index, item) =>  {
                if (inviteMember.includes(item.id)) {
                    const member = $(`<div class="select-member">
                                <img src="${item.avatar}" class="avatar-icon" alt="image">
                                <div class="mem-text">
                                    <h5>${item.name}</h5>
                                    <span>${item.email}</span>
                                </div>
                                <button type="button" class="close remove-invited-member" data-member-name="${item.name}" data data-route-delete="/apps/${appId}/members/${item.id}">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>`);
                    $("#memberList").prepend(member);
                }
            });
            $(".scroll-mem").empty();
        })
        .catch(function (error) {
            swal("{{ trans('messages.error') }}", "{{ trans('messages.invite_member_failed') }}", "error");
        })
    });

    $(document).on('click', '.remove-selected-member', function () {
        const removeId = $(this).attr("data-id");
        $(this).parent("div.select-member").remove();
        selectedMemberId = selectedMemberId.filter(e => e !== removeId);
        selectedMember = selectedMember.filter(e => e.id !== removeId);
    });

    $(document).on('click', '.remove-invited-member', function () {
        const route = $(this).attr("data-route-delete");
        let memberName = $(this).attr("data-member-name");
        $("#frmRemoveMember").attr("action", route);
        $("#deleteMember").modal("toggle");
        $("#confirmDeleteMemberAlert").text(memberName);
    });

    function getMobileOperatingSystem() {
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Windows Phone must come first because its UA also contains "Android"
        if (/windows phone/i.test(userAgent)) {
            return "Windows Phone";
        }

        if (/android/i.test(userAgent)) {
            return "Android";
        }

        // iOS detection from: http://stackoverflow.com/a/9039885/177710
        if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
            return "IOS";
        }

        return "unknown";
    }

    $(document).ready(function () {
        const device = getMobileOperatingSystem();
        switch (device) {
            case "IOS":
                $(".IOSApp").show();
                $(".AndroidApp").hide();
                break;
            case "Android":
                $(".AndroidApp").show();
                $(".IOSApp").hide();
                break;
            default:
                $(".AndroidApp").show();
                $(".IOSApp").show();
                break;
        }
        console.log(device);
    });
</script>
@endsection
