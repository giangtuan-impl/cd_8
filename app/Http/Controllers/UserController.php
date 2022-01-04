<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Helpers\CustomAlert;

class UserController extends Controller
{
    protected $userService;
    protected $customAlert;

    public function __construct(
        UserService $userService,
        CustomAlert $customAlert)
    {
        $this->middleware('auth');
        $this->userService = $userService;
        $this->customAlert = $customAlert;
    }

    public function profile()
    {
        return view('users.profile');
    }

    public function update(UserRequest $request)
    {

        $this->userService->updateProfile($request);

        return redirect()->back()->with($this->customAlert::alert('success', trans('messages.update_profile_successfully')));
    }
}
