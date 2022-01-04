<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LangController extends Controller
{
    private $languageList = User::LANGUAGES;
    
    public function changeLanguage(Request $request, $lang)
    {
        if (in_array($lang, $this->languageList)) {
            $request->session()->put(['lang' => $lang]);
            
            if (Auth::check()) {
                $this->updateUserLanguage($lang);
            }
            return redirect()->back();
        }
    }

    public function updateUserLanguage($lang) {
        $user = Auth::user();
        $user->language = $lang;
        $user->save();
    }
}
