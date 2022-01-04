<?php

namespace App\Services;

use App\Models\BuildNumber;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BuildNumberService
{
    protected $model;
    protected $appService;
    protected $fileService;
    protected $notifyService;

    public function __construct(
        BuildNumber $model,
        ApplicationService $appService,
        FileService $fileService,
        NotifyService $notifyService
    ) {
        $this->model = $model;
        $this->appService = $appService;
        $this->fileService = $fileService;
        $this->notifyService = $notifyService;
    }

    public function create($data)
    {
        if ($data['env'] == BuildNumber::ENV_IOS) {
            $buildInfo = $this->appService->getAppIOSInfo($data['file']->getClientOriginalName(), $data['build_name'], $data['build_number']);
            $data = array_merge($data, [
                'uuid_list' => json_encode($buildInfo['uuid_list']),
                'bundle_id' => $buildInfo['bundle_id'],
                'bundle_name' => $buildInfo['bundle_name'],
                'link' => $buildInfo['link'],
                'version_number' => $buildInfo['version_number'],
                'version_code_number' => $buildInfo['version_code_number'],
                'app_icon' => $buildInfo['app_icon'],
            ]);
        } else {
            $buildInfo = $this->appService->getAppAndroidInfo($data['build_name'], $data['build_number']);
            $data = array_merge($data, $buildInfo);
        }

        $build = $this->model->create($data);
        $this->storeUUIDAndPlistFile($data, $buildInfo, $build);
        $this->notifyService->sendNewBuildEmail($build);

        return $data;
    }

    public function storeUUIDAndPlistFile($data, $buildInfo, $build)
    {
        if ($data['env'] == BuildNumber::ENV_IOS) {
            //save UUIDs in file
            $iosPath = env('JENKINS_BUILD') . $data['build_name'] . config('constants.JENKINS_BUILD_FOLDER_PREFIX') . $data['build_number'] . config('constants.IOS_PARAMS.IPA_FOLDER');
            $this->fileService->saveFile($iosPath . 'uuids' . config('constants.IOS_PARAMS.JSON_EXTENSION'), $data['uuid_list']);

            //save .plist file
            $this->fileService->saveFile($iosPath . $data['app_name'] . config('constants.IOS_PARAMS.PLIST_EXTENSION'), view(config('constants.IOS_PARAMS.PLIST_FILE_TEMPLATE_PATH'))->with([
                'link' => route('build.download', ['id' => $build->id]),
                'bundleId' => $buildInfo['bundle_id'],
                'bundleVersion' => $buildInfo['version_number'],
                'bundleName' => $buildInfo['bundle_name']
            ])->render());
        }
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function storeUploadFile($data)
    {
        $iosPath = env('JENKINS_BUILD')
            . $data['build_name']
            . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
            . $data['build_number']
            . config('constants.IOS_PARAMS.IPA_FOLDER');

        $androidPath = env('JENKINS_BUILD')
            . $data['build_name']
            . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
            . $data['build_number'];

        if ($data['env'] == BuildNumber::ENV_IOS) {
            if (!file_exists($iosPath)) {
                $this->fileService->makeDirectory($iosPath);
            }

            if ($data['file']) {
                $originalFileName = $data['file']->getClientOriginalName();
                $data['file']->move($iosPath, $originalFileName);
            }
        } else {
            if (!file_exists($androidPath)) {
                $this->fileService->makeDirectory($androidPath);
            }

            if ($data['file']) {
                $name = $data['file']->getClientOriginalName();
                $data['file']->move($androidPath, $name);
            }
        }
    }

    public function deleteOldVersions($appId, $env)
    {
        $listOldVersions = $this->getListOldVersions($appId, $env);

        foreach ($listOldVersions as $build) {
            try {
                $this->deleteBuildNumber($build);
            } catch (QueryException $e) {
                Log::error($e->getMessage());
            }
        }
    }


    public function deleteBuildNumber(BuildNumber $build)
    {
        $folderAppName = $build->env == BuildNumber::ENV_IOS ? $build->app->ios_name : $build->app->android_name;
        $folderBuildPath = config("constants.JENKINS_BUILD")
            . $folderAppName
            . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
            . $build->build_number;

        $this->fileService->deleteDirectory($folderBuildPath);

        $build->delete();
    }

    public function getListOldVersions($appId, $env, $top = BuildNumber::MAX_NEW_VERSION_COUNT)
    {
        $listNewestVersionIds = $this->model->where([
            ['app_id', $appId],
            ['env', $env],
        ])
            ->take($top)
            ->latest('build_number')
            ->pluck('id')
            ->toArray();

        return $this->model->where([
            ['app_id', $appId],
            ['env', $env],
        ])->whereNotIn('id', $listNewestVersionIds)->with('app')->get();
    }
}
