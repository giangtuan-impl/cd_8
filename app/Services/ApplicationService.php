<?php

namespace App\Services;

use Exception;
use ZipArchive;
use Redoc\IpaParser;
use SimpleXMLElement;
use App\Jobs\ApktoolJob;
use App\Models\Application;
use Illuminate\Http\Request;
// use Redoc\IpaParser;
use App\Helpers\iOSPNGNormalizer;
use CFPropertyList\CFPropertyList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class ApplicationService
{
    protected $application;
    protected $fileService;

    public function __construct(
        Application $application,
        FileService $fileService
    ) {
        $this->application = $application;
        $this->fileService = $fileService;
    }

    public function createApp($data)
    {
        $data['user_id'] = auth()->user()->id;

        if (!$this->checkFolderExist(env('JENKINS_BUILD') . $data['ios_name'])) {
            $this->fileService->makeDirectory(env('JENKINS_BUILD') . $data['ios_name']);
        } else {
            $this->validateFolderNotExists($data['ios_name'], config('constants.IOS_BUILD_NAME'));
        }

        if (!$this->checkFolderExist(env('JENKINS_BUILD') . $data['android_name'])) {
            $this->fileService->makeDirectory(env('JENKINS_BUILD') . $data['android_name']);
        } else {
            $this->validateFolderExists($data['android_name'], config('constants.ANDROID_BUILD_NAME'));
        }

        return $this->application->create($data);
    }

    public function updateApp($data, $id)
    {
        // kiem tra thu da ton tai hay chua, neu chua
        $data['user_id'] = auth()->user()->id;
        $app = Application::findOrFail($id);

        if ($this->checkFolderExist(env('JENKINS_BUILD') . $data['ios_name'])) {
            $this->validateFolderExists(env('JENKINS_BUILD') . $data['ios_name'], config('constants.IOS_BUILD_NAME'));
        } else {
            if (!$this->checkFolderExist(env('JENKINS_BUILD') . $data['ios_name'])) {
                $this->fileService->makeDirectory(env('JENKINS_BUILD') . $data['ios_name']);
            }
            $this->fileService->renameDirectory(env('JENKINS_BUILD') . $app['ios_name'], env('JENKINS_BUILD') . $data['ios_name']);
        }

        if ($this->checkFolderExist(env('JENKINS_BUILD') . $data['android_name'])) {
            $this->validateFolderExists(env('JENKINS_BUILD') . $data['android_name'], config('constants.ANDROID_BUILD_NAME'));
        } else {
            if (!$this->checkFolderExist(env('JENKINS_BUILD') . $data['android_name'])) {
                $this->fileService->makeDirectory(env('JENKINS_BUILD') . $data['android_name']);
            }
            $this->fileService->renameDirectory(env('JENKINS_BUILD') . $app['android_name'], env('JENKINS_BUILD') . $data['android_name']);
        }

        return $this->application->findOrFail($id)->update($data);
    }

    public function deleteDirectory($path, $buildType)
    {
        if (!$this->fileService->deleteDirectory($path)) {
            throw ValidationException::withMessages([$buildType => __('Folder does not exists')]);
        }
    }

    public function validateFolderExists($folderName, $buildType)
    {
        if (!$this->checkFolderExist($folderName)) {
            throw ValidationException::withMessages([$buildType => __($folderName . ' folder does not exists')]);
        }
    }

    public function validateFolderNotExists($folderName, $buildType)
    {
        if ($this->checkFolderExist($folderName)) {
            throw ValidationException::withMessages([$buildType => __('folder has been exists')]);
        }
    }

    public function checkFolderExist($folderName)
    {
        $path = env("JENKINS_BUILD") . $folderName;

        return file_exists($path);
    }

    public function findByName($name)
    {
        return $this->application->whereAppName($name)->first();
    }

    public function findByDownloadName($name)
    {
        return $this->application->whereDownloadName($name)->first();
    }

    public function findById($id)
    {
        return $this->application->findOrFail($id);
    }

    public function getAppIOSInfo($fileName, $folderAppName, $buildNumber)
    {
        $listUUId = [];
        $bundleId = "";
        $bundleName = "";
        $link = "";
        $versionNumber = "";
        $versionCodeNumber = "";
        $appIcon = "";

        $ipaFolderPath = env("JENKINS_BUILD") . $folderAppName . config('constants.JENKINS_BUILD_FOLDER_PREFIX') . $buildNumber . config('constants.IOS_PARAMS.IPA_FOLDER');
        $ipaFilePath = $ipaFolderPath . $fileName;

        $extractedFolderPath = $ipaFolderPath . config('constants.IOS_PARAMS.EXTRACT_FOLDER_NAME');
        if (glob($extractedFolderPath . '*')) {
            $this->fileService->deleteDirectory(glob($extractedFolderPath . '*')[0]);   # delete all old folders in extracted folder
        }

        try {
            $zip = new ZipArchive;
            if ($zip->open($ipaFilePath) === TRUE) {
                $zip->extractTo($ipaFolderPath);
                $zip->close();
                $link = $fileName;
                $appExtensionFolderPath = glob($ipaFolderPath . config('constants.IOS_PARAMS.EXTRACT_FOLDER_NAME') . '*' . config('constants.IOS_PARAMS.APP_EXTENSION'));   // path : Payload/folder.app/
                $files = File::files($appExtensionFolderPath[0]);    // get all files in folder.app

                foreach ($files as $file) {
                    if ($file->getFilename() == config('constants.IOS_PARAMS.MOBILE_PROVISION_FILE_NAME')) {
                        $listUUId = $this->readUUIDFromFile($file);
                    }
                    if ($file->getFileName() == config('constants.IOS_PARAMS.INFO_LIST_FILE_NAME')) {
                        $bundleId = $this->readBundleInfoFromFile($file, config('constants.IOS_PARAMS.CF_BUNDLE_IDENTIFIER_TAG'));
                        $versionNumber = $this->readBundleInfoFromFile($file, config('constants.IOS_PARAMS.CF_BUNDLE_SHORT_VERSION_STRING_TAG'));
                        $versionCodeNumber = $this->readBundleInfoFromFile($file, config('constants.IOS_PARAMS.CF_BUNDLE_VERSION_TAG'));
                        $bundleName = $this->readBundleNameFromFile($file, config('constants.IOS_PARAMS.CF_BUNDLE_NAME_TAG'));
                    }
                    if ($file->getFilename() == config('constants.IOS_PARAMS.APP_ICON_FILE_NAME')) {
                        $appIcon = $this->getIOSAppIcon($file);
                        iOSPNGNormalizer::fix(config('constants.DEFAULT_ICON_FOLDER') . $appIcon);  // uncrush png image in file .ipa
                    }
                }
            }
        } catch (DirectoryNotFoundException $e) {
            Log::error($e->getMessage());
        }
        return [
            'uuid_list' => $listUUId,
            'bundle_id' => $bundleId,
            'bundle_name' => $bundleName,
            'version_number' => $versionNumber,
            'version_code_number' => $versionCodeNumber,
            'link' => $link,
            'app_icon' => $appIcon
        ];
    }

    public function readUUIDFromFile($file)
    {
        try {
            $xmlString = file_get_contents($file->getPathName());
            $provisionTag = config('constants.IOS_PARAMS.PROVISIONED_DEVICES_TAG');
            $bundleTagPos = strpos($xmlString, $provisionTag) + strlen($provisionTag);

            $endTagArray = config('constants.IOS_PARAMS.END_ARRAY_TAG');
            $endTagArrayPos = strpos($xmlString, $endTagArray, $bundleTagPos) + strlen($endTagArray);

            $xml = substr($xmlString, $bundleTagPos, $endTagArrayPos - $bundleTagPos);
            $json = json_encode(simplexml_load_string($xml));
            $array = json_decode($json, TRUE);
            return array_values($array)[0];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return [];
        }
    }

    public function readBundleInfoFromFile($file, $bundleTag)
    {
        try {
            $xmlString = File::get($file->getPathName());
            $bundleTagPos = strpos($xmlString, $bundleTag);

            if (!$bundleTagPos) {
                return "";
            }

            $startStringTag = config('constants.IOS_PARAMS.START_STRING_TAG');
            $startStringTagPos = strpos($xmlString, $startStringTag, $bundleTagPos) + strlen($startStringTag);

            $endStringTag = config('constants.IOS_PARAMS.END_STRING_TAG');
            $endStringTagPos = strpos($xmlString, $endStringTag, $bundleTagPos);

            if (!$startStringTagPos || !$endStringTagPos || ($startStringTagPos >= $endStringTagPos)) {
                return "";
            }
            $bundleID = substr($xmlString, $startStringTagPos, $endStringTagPos - $startStringTagPos);

            return $bundleID;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return "";
        }
    }

    public function readBundleNameFromFile($file, $bundleTag)
    {
        try {
            $xmlString = File::get($file->getPathName());
            $bundleTagPos = strpos($xmlString, $bundleTag);

            if (!$bundleTagPos) {
                return "";
            }

            $startStringTag = config('constants.IOS_PARAMS.START_STRING_TAG');
            $startStringTagPos = strpos($xmlString, $startStringTag, $bundleTagPos) + strlen($startStringTag);

            $endStringTag = config('constants.IOS_PARAMS.END_STRING_TAG');
            $endStringTagPos = strpos($xmlString, $endStringTag, $bundleTagPos);

            if (!$startStringTagPos || !$endStringTagPos || ($startStringTagPos >= $endStringTagPos)) {
                return "";
            }
            $bundleName = substr($xmlString, $startStringTagPos, $endStringTagPos - $startStringTagPos);

            return $bundleName;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return "";
        }
    }

    public function getIOSAppIcon($iconFile)
    {
        try {
            if (!is_dir(config('constants.DEFAULT_ICON_FOLDER'))) {
                $this->fileService->makeDirectory(config('constants.DEFAULT_ICON_FOLDER'));
            }

            $maxFileSize = 0;
            if ($iconFile->getSize() > $maxFileSize) {
                return $this->fileService->upload(config('constants.DEFAULT_ICON_FOLDER'), $iconFile);
            }
        } catch (FileNotFoundException $e) {
            Log::error($e->getMessage());
        }
    }

    public function getAppAndroidInfo($folderAppName, $buildNumber)
    {
        $apkFolderPath = env("JENKINS_BUILD") . $folderAppName . config('constants.JENKINS_BUILD_FOLDER_PREFIX') . $buildNumber;

        try {
            $files = File::files($apkFolderPath);
            foreach ($files as $file) {
                if ($file->getExtension() === config('constants.ANDROID_PARAMS.APK_EXTENSION')) {
                    // implement extract file apk when found
                    $apktoolJob = new ApktoolJob($apkFolderPath, $file->getFilename(), $file->getFilenameWithoutExtension());
                    dispatch($apktoolJob);

                    $extractFolderPath = $file->getPath() . '/' . $file->getFilenameWithoutExtension();
                    $data = $this->readAndroidManifestXml($extractFolderPath);
                    if ($data) {
                        $data['link'] = $file->getFilename();
                        return $data;
                    }
                }
            }
        } catch (DirectoryNotFoundException $e) {
            Log::error($e->getMessage());
        }

        return [
            'bundle_id' => '',
            'app_icon' => '',
            'version' => '',
            'version_code' => '',
            'link' => ''
        ];
    }

    public function readAndroidManifestXml($path)
    {
        if (!file_exists($path)) {
            return [];
        }

        $file = $this->fileService->findFileInDirectory($path, config('constants.ANDROID_PARAMS.MANIFEST_FILE_NAME'));
        if (!$file) {
            return [];
        }

        $content = file_get_contents($file);
        $xml = new SimpleXMLElement($content);
        $applicationAttributes = $xml->application[0]->attributes('android', TRUE);
        $icon = isset($applicationAttributes['icon']) ? $this->getAndroidAppIcon($path . "/" . config('constants.ANDROID_PARAMS.RESOURCE_FOLDER_NAME'), (string)$applicationAttributes['icon']) : '';
        $data = [
            'app_icon' => $icon,
            'bundle_id' => isset($xml->attributes()->package) ? (string)$xml->attributes()->package : '',
            'version_number' => isset($xml->attributes()->platformBuildVersionName) ? (string)$xml->attributes()->platformBuildVersionName : '',
            'version_code_number' => isset($xml->attributes()->platformBuildVersionCode) ? (string)$xml->attributes()->platformBuildVersionCode : '',
        ];

        return $data;
    }

    public function getAndroidAppIcon($directoryPath, $iconPath)
    {
        // tim thu muc lay icon => link da luu tren project
        if (!$iconPath) {
            return '';
        }

        $iconData = explode('/', $iconPath);

        if (!isset($iconData[0]) || !isset($iconData[1])) {
            return '';
        }

        $folder = str_replace("@", "", $iconData[0]);
        $icon = $iconData[1] . ".";

        try {
            $directories = File::directories($directoryPath);

            $iconFile = null;
            $maxFileSize = 0;
            foreach ($directories as $dir) {
                if (str_contains($dir, $folder)) {
                    $files = File::files($dir);
                    foreach ($files as $file) {
                        if (str_contains($file->getFilename(), $icon) && $file->getSize() > $maxFileSize) {
                            $iconFile = $file;
                            $maxFileSize = $file->getSize();
                        }
                    }
                }
            }

            return $this->fileService->upload(config('constants.DEFAULT_ICON_FOLDER'), $iconFile);
        } catch (DirectoryNotFoundException $e) {
            Log::error($e->getMessage());
        }

        return '';
    }

    public function asyncUpdateAndroidInfo($androidBuild)
    {
        $androidProperties = [
            'bundle_id',
            'version_number',
            'version_code_number',
        ];

        // update after apktool queue processed
        try {
            $apkFolderPath = env("JENKINS_BUILD")
                . $androidBuild->app->android_name
                . config('constants.JENKINS_BUILD_FOLDER_PREFIX')
                . $androidBuild->build_number;

            $files = File::files($apkFolderPath);
            // find apk folder
            foreach ($files as $file) {
                if ($file->getExtension() === config('constants.ANDROID_PARAMS.APK_EXTENSION')) {
                    $apkFolder = $file->getFilenameWithoutExtension();
                }
            }

            foreach ($androidProperties as $item) {
                if (!$androidBuild[$item]) {
                    $data = $this->readAndroidManifestXml($apkFolderPath . '/' . $apkFolder);
                    $data['link'] = $file->getFilename();
                    $androidBuild->update($data);
                }
            }
        } catch (Exception $e) {
            return response()->json(trans('messages.updating_information'));
        }
    }
}
