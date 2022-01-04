<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\CustomAlert;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $customAlert;

    public function __construct(CustomAlert $customAlert)
    {
        $this->customAlert = $customAlert;
    }

    public function index($token)
    {
        $tokenData = $this->getPWResetByToken($token);
        
        if ($tokenData && $this->isTokenValid($tokenData)) {
            $verifiedToken = $tokenData->token;
            return view('auth.reset_password', compact('verifiedToken'));
        }

        return redirect()->route('password_remind')->with($this->customAlert::alert('error', trans('messages.invalid_url')));
    }

    public function reset(ResetPasswordRequest $request)
    {
        $tokenData = $this->getPWResetByToken($request->verified_token);
        $resetPasswordAndLogin = $this->resetPasswordAndLogin($request, $tokenData->email);
        if ($resetPasswordAndLogin) {
            PasswordReset::where('email', $tokenData->email)->delete();
            return redirect()->route('index')->with($this->customAlert::alert('success', trans('messages.reset_password_successfully')));
        }
        return redirect()->route('password_remind')->with($this->customAlert::alert('error', trans('messages.email_does_not_exist')));
    }

    public function getPWResetByToken($token) {
        return PasswordReset::where('token', $token)->first();
    }

    public function isTokenValid($tokenData) {
        return $tokenData->created_at < Carbon::parse($tokenData->created_at)->addHour(User::RESET_PASSWORD_TOKEN_EXPIRED_HOUR);
    }

    public function resetPasswordAndLogin($request, $email) {
        $user = $this->isEmailExist($email);
        if (!$user) {
            return false;
        } 
        $user->password = $request->new_password;
        $user->is_must_change_password = User::MUST_NOT_CHANGE_PASSWORD;
        $user->save();
        Auth::login($user);

        return true;
    }

    public function isEmailExist($email) {
        return User::where('email' ,$email)->first();
    }
}
