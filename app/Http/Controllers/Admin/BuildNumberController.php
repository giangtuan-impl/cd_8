<?php

namespace App\Http\Controllers\Admin;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\BuildNumber;
use App\Services\ApplicationService;
use App\Services\BuildNumberService;
use App\Services\FileService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Helpers\CustomAlert;

class BuildNumberController extends Controller
{
    protected $applicationService;
    protected $buildNumberService;
    protected $fileService;
    protected $customAlert;

    public function __construct(
        ApplicationService $applicationService,
        BuildNumberService $buildNumberService,
        FileService $fileService,
        CustomAlert $customAlert
    ) {
        $this->middleware('auth');
        $this->middleware('check-device');
        $this->middleware('check-admin');
        $this->applicationService = $applicationService;
        $this->buildNumberService = $buildNumberService;
        $this->fileService = $fileService;
        $this->customAlert = $customAlert;
    }

    public function index()
    {
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($app)
    {
        $application = $this->applicationService->findById($app);
        $androidBuilds = $application->buildNumbers()->filterAndroidBuildNumbers()->get();
        foreach ($androidBuilds as $androidBuild) {
            $this->applicationService->asyncUpdateAndroidInfo($androidBuild);
        }
        $iosBuilds = $application->buildNumbers()->filterIOSBuildNumbers()->get();

        return view('app_page.build_manager', compact('iosBuilds', 'androidBuilds', 'application'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($buildNumber)
    {
        DB::beginTransaction();
        try {
            $build = $this->buildNumberService->findOrFail($buildNumber);
            $folderAppName = $build->env == BuildNumber::ENV_IOS ? $build->app->ios_name : $build->app->android_name;
            $folderBuildPath = env("JENKINS_BUILD")
                . $folderAppName
                . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
                . $build->build_number;

            $this->fileService->deleteDirectory($folderBuildPath);

            $build->delete();
            DB::commit();

            return redirect()->back()->with($this->customAlert::alert('success', trans('messages.delete_build_number_successfully')));
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->with($this->customAlert::alert('error', trans('messages.delete_build_number_failed')));
        }
    }

    public function deleteOldVersions($app)
    {
        try {
            $this->buildNumberService->deleteOldVersions($app, BuildNumber::ENV_IOS);
            $this->buildNumberService->deleteOldVersions($app, BuildNumber::ENV_ANDROID);
            return redirect()->back()->with($this->customAlert::alert('success', trans('messages.delete_old_versions_successfully')));
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return redirect()->back()->with($this->customAlert::alert('error', trans('messages.delete_old_versions_fail')));
    }
}
