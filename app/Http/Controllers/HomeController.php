<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $ownerApp = $user->application;
        $invitedApp = $user->applications;

        $applicationCollection = $invitedApp->merge($ownerApp);

        $agent = new Agent();
        if ($agent->isDesktop())    // access by desktop
        {
            $applications = $applicationCollection;
        } else                        // access by mobile
        {
            $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
            $iPad    = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
            $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");

            $arrId = $applicationCollection->pluck('id');

            if ($iPhone || $iPad) {
                $applications = Application::whereHas('buildNumbers', function ($q) use ($arrId) {
                    return $q->filterIOSBuildNumbers();
                })->whereIn('id', $arrId)->get();
            } else if ($Android) {
                $applications = Application::whereHas('buildNumbers', function ($q) use ($arrId) {
                    return $q->filterAndroidBuildNumbers();
                })->whereIn('id', $arrId)->get();
            }
        }
        return view('home', compact('applications'));
    }
}
