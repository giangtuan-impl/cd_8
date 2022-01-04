<?php

namespace App\Http\Controllers;

use App\Models\BuildNumber;
use App\Services\ApplicationService;
use App\Services\BuildNumberService;
use App\Services\FileService;
use Exception;

use Jenssegers\Agent\Agent;

class BuildNumberController extends Controller
{
    const ENV_ANDROID = 1;
    const ENV_IOS = 0;

    protected $buildService;
    protected $fileService;
    protected $applicationService;

    public function __construct(
        BuildNumberService $buildService,
        FileService $fileService,
        ApplicationService $applicationService
    ) {
        $this->buildService = $buildService;
        $this->fileService = $fileService;
        $this->applicationService = $applicationService;
    }

    public function download($id)
    {
        $build = $this->buildService->findOrFail($id);
        $invitedUserIds = $build->app->members->pluck('id');    // array contain all id of invited user
        $ownerId = $build->app->user->id;
        $memberIds = $invitedUserIds->merge($ownerId);          // array contain all person can download build

        //        if (! in_array(Auth::user()->id, $memberIds->toArray())) {
        //            return abort(403);
        //        }

        try {
            if ($build->env == self::ENV_ANDROID) {  # if file download was by Android (.apk)
                $headers = [
                    'Content-Type' => 'application/vnd.android.package-archive'
                ];
                $downloadLink = env('JENKINS_BUILD')
                    . $build->app->android_name
                    . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
                    . $build->build_number
                    . '/'
                    . $build->link;

                return response()->download($downloadLink, $build->link, $headers);
            } else {   # if file download was by iOS (.ipa)
                $downloadLink = env('JENKINS_BUILD')
                    . $build->app->ios_name
                    . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
                    . $build->build_number
                    . config('constants.IOS_PARAMS.IPA_FOLDER')
                    . $build->link;

                return response()->download($downloadLink);
            }
        } catch (Exception $e) {
            abort(404, trans('messages.not_found', ['attribute' => 'File']));
        }
    }

    public function plistDownload($id)
    {
        $build = $this->buildService->findOrFail($id);
        $plistFilePath = env('JENKINS_BUILD')
            . $build->app->ios_name
            . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
            . $build->build_number
            . config('constants.IOS_PARAMS.IPA_FOLDER')
            . $build->app->app_name
            . config('constants.IOS_PARAMS.PLIST_EXTENSION');

        if (!file_exists($plistFilePath)) {
            abort(404, trans('messages.not_found', ['attribute' => 'File']));
        }
        return response()->download($plistFilePath);
    }

    public function downloadOneStack($name, $buildNumberVersion = null)
    {
        if (!$name) {
            abort(404, trans('messages.not_found', ['attribute' => 'File']));
        }

        $application = $this->applicationService->findByDownloadName($name);

        if (!$application) {
            abort(404, trans('messages.not_found', ['attribute' => 'File']));
        }

        $agent = new Agent();

        if ($buildNumberVersion != null && $agent->isMobile())  // if has param build number version (only mobile)
        {
            $buildNumber = $this->buildNumberService->findOrFail($buildNumberVersion);

            if ($buildNumber->env == BuildNumber::ENV_ANDROID) {
                $androidBuild = $buildNumber;
                $this->applicationService->asyncUpdateAndroidInfo($androidBuild);
                $iosBuild = null;
            } else {
                $iosBuild = $buildNumber;
                $androidBuild = null;
            }

            $members = $application->members;

            // get all records except current record
            $previousAndroidBuilds = $application->buildNumbers()->filterAndroidBuildNumbers()
                ->where('id', '!=', $buildNumberVersion)
                ->get();

            $previousIosBuilds = $application->buildNumbers()->filterIOSBuildNumbers()
                ->where('id', '!=', $buildNumberVersion)
                ->get();
        } else {
            $androidBuild = $application->buildNumbers()->latestAndroidBuild();
            $this->applicationService->asyncUpdateAndroidInfo($androidBuild);
            $iosBuild = $application->buildNumbers()->latestIOSBuild();
            $members = $application->members;

            // get all records except current record
            $previousAndroidBuilds = $application->buildNumbers()->filterAndroidBuildNumbers()
                ->where('id', '!=', $androidBuild->id ?? null)
                ->get();

            $previousIosBuilds = $application->buildNumbers()->filterIOSBuildNumbers()
                ->where('id', '!=', $iosBuild->id ?? null)
                ->get();
        }

        return view('basic_auth_download.download', compact(
            'application',
            'members',
            'androidBuild',
            'iosBuild',
            'previousAndroidBuilds',
            'previousIosBuilds'
        ));
    }
}
