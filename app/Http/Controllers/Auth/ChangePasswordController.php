<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\CustomAlert;

class ChangePasswordController extends Controller
{
    protected $customAlert;

    public function __construct(CustomAlert $customAlert)
    {
        $this->customAlert = $customAlert;
    }

    public function index()
    {
        return view('auth.change_password');
    } 
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!(Hash::check($request->get('current_password'), auth()->user()->password))) {
            return redirect()->back()->with($this->customAlert::alert('error',trans('messages.password_not_match')));
        }

        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            return redirect()->back()->with($this->customAlert::alert('error',trans('messages.please_choose_diff_password')));
        }

        $user = auth()->user();
        $user->password = $request->get('new_password');
        $user->is_must_change_password = User::MUST_NOT_CHANGE_PASSWORD;
        $user->save();

            return redirect()->route('profile')->with($this->customAlert::alert('success',trans('messages.change_password_successfully')));
    }
}