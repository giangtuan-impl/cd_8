@extends('layouts.app')

@section('title', trans('messages.member_manage'))
@section('content')
    <div class="container-fluid main">
        @include('layouts.alert')
        <div class="panel panel-default">
            <h3 class="h2 text-gray-900 mb-4 text-center">{{ trans('messages.member_manage') }}</h3>
        </div>
        <div class="show-member">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h3 class="h3 text-gray-900 mb-4">{{ trans('messages.member') }}</h3>
                <a href="{{ route('members.create') }}" class="d-none d-sm-inline-block btn btn-outline-secondary">{{ trans('messages.add_new_member') }}</a>
            </div>
            @php
                $roles = App\Models\User::ROLES;
            @endphp
            <div class="member-list mb-4">
                @foreach($users as $user)
                    <div class="member-content">
                        <img src="{{ $user->avatar }}" class="avatar-icon" alt="avatar" onerror="this.src='{{ asset('assets/image/avatar.png') }}'">
                        <div class="mem-text">
                            <h5>{{ $user->name }}</h5>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="mem-text-right">
                            <div class="role-member">
                                <label for="role">{{ __('Role:') }} </label>
                                <select class="form-control roleBox" name="role" data-id="{{ $user->id }}">
                                    @foreach ($roles as $key => $value)    
                                        <option value="{{$value}}" @if($value == $user->role) selected @endif>{{ trans('messages.' . $key) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="close btnDeleteMemeber" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-route="{{ route('members.destroy', ['member' => $user->id]) }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endforeach
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
                    {{ trans('messages.delete_this_member') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.close') }}</button>
                    <form action="" method="POST" id="frmDeleteMember">
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
<script type="text/javascript">
    $(document).ready(function() {
        $(".roleBox").on("change", function() {
            var role = $(this).val();
            var member = $(this).data('id');
            
            $.ajax({
                url: "/members/" + member,
                method: "POST",
                data: {
                    role: role,
                    "_token": "{{ csrf_token() }}"
                },
                success: function (data) {
                    swal("{{ trans('messages.success') }}", "{{ trans('messages.change_role_successfully') }}", "success");
                },
                error: function (data) {
                    swal("{{ trans('messages.error') }}", "{{ trans('messages.change_role_failed') }}", "error");
                }
            });
        });
        $(".btnDeleteMemeber").click(function () {
            $("#deleteMember").modal("toggle");
            $("#frmDeleteMember").attr("action", $(this).attr('data-route'));
        });
    });
</script>
@endsection
