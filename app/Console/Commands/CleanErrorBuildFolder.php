<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Application;
use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CleanErrorBuildFolder extends Command
{
    protected $signature = 'clean:build';

    protected $description = 'Clean up all trashed build-jenkins folders & trashed app folder';

    protected $application;
    protected $fileService;

    public function __construct(
        Application $application, 
        FileService $fileService)
    {
        $this->application = $application;
        $this->fileService = $fileService;
        parent::__construct();
    }

    public function handle()
    {
        $trashedApp = $this->application->onlyTrashed()->get();

        // Delete trashed app folder
        foreach($trashedApp as $trashApp) {
            DB::beginTransaction();
            try {
                $this->fileService->deleteDirectory(env('JENKINS_BUILD') . $trashApp->android_name);
                $this->fileService->deleteDirectory(env('JENKINS_BUILD') . $trashApp->ios_name);
                DB::commit();
            } catch(Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
            }
        }

        // Delete trashed build-jenkins folder
        $apps = $this->application->all();
        foreach($apps as $app) {
            foreach($app->buildNumbers()->onlyTrashed()->get() as $buildNumber) {
                DB::beginTransaction();
                try {
                    $this->fileService->deleteDirectory(env('JENKINS_BUILD') 
                                                        . $app->android_name 
                                                        . config('constants.JENKINS_BUILD_FOLDER_PREFIX') 
                                                        . $buildNumber->build_number);

                    $this->fileService->deleteDirectory(env('JENKINS_BUILD') 
                                                        . $app->ios_name 
                                                        . config('constants.JENKINS_BUILD_FOLDER_PREFIX') 
                                                        . $buildNumber->build_number);
                    DB::commit();
                } catch(Exception $ex) {
                    DB::rollBack();
                    Log::error($e->getMessage());
                }
            }
        }

        $this->info("Everything was cleaned up!");
    }
}
