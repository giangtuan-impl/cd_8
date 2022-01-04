<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Helpers\CustomAlert;
use App\Mail\ResetPasswordMail;
use App\Models\PasswordReset;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    protected $customAlert;

    public function __construct(CustomAlert $customAlert)
    {
        $this->customAlert = $customAlert;
    }

    public function index()
    {
        return view('auth.forgot_password');
    }

    public function password(ForgotPasswordRequest $request)
    {
        $user = User::whereEmail($request->email)->first();

        if ($user == null) {
            return redirect()->back()->with($this->customAlert::alert('error', trans('messages.email_does_not_exist')));
        }
        
        $token = config('constants.RESET_PASSWORD_TOKEN');
        $resetPasswordLink = route('reset_password', ['token' => $token]);

        PasswordReset::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $this->sendEmail($user, $resetPasswordLink);
        
        return redirect()->back()->with($this->customAlert::alert('success', trans('messages.new_password_send_to_your_email')));
    }

    public function sendEmail($user, $resetPasswordLink)
    {
        $resetPasswordMail = new ResetPasswordMail($resetPasswordLink);
        Mail::to($user->email)->locale($user->language)->send($resetPasswordMail);
    }
}

