<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function App\Helpers\upload;

class UserService
{
    protected $application;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(Request $request)
    {
        $password = Str::random(8);
        $user = User::create(array_merge($request->all(), [
            'password' => $password,
            'is_must_change_password' => User::MUST_CHANGE_PASSWORD
        ]));

        $this->sendEmail($user, $password);

        $user->save();
    }

    public function sendEmail($user, $password)
    {
        $currentLanguage = app()->getLocale();      // get current language
        app()->setLocale($user->language);          // set locale to user's language
        Mail::send('auth.send_password', ['user' => $user, 'password' => $password], function ($message) use ($user, $password) {
            $message->to($user->email);
            $message->subject('[Alobridge CD] ' . trans('messages.create_member_successfully'));
        });
        app()->setLocale($currentLanguage);         // reset to current language
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->only([
            'name',
            'language'
        ]);
        if ($request->hasFile('avatar')) {
            $data['avatar'] =  upload($request, 'avatar', 'image');
        }

        $user->update($data);
    }

    public function findByEmailWithTrashed($email)
    {
        return $this->user->withTrashed()->whereEmail($email)->first();
    }

    public function destroy($id)
    {
        return $this->user->withTrashed()->find($id)->forceDelete();
    }
}
