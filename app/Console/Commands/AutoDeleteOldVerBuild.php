<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\BuildNumber;
use App\Services\BuildNumberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoDeleteOldVerBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:autoDeleteOldVerBuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command auto delete old version of all app, keep 5 version latest';
    protected $appModel;
    protected $buildNumberService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Application $application,
        BuildNumberService $buildNumberService
    ) {
        parent::__construct();

        $this->appModel = $application;
        $this->buildNumberService = $buildNumberService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $apps = $this->appModel->all();

            foreach ($apps as $app) {
                $this->buildNumberService->deleteOldVersions($app->id, BuildNumber::ENV_IOS);
                $this->buildNumberService->deleteOldVersions($app->id, BuildNumber::ENV_ANDROID);
            }

            Log::notice("[CronJob][AutoDeleteOldVerBuild] number build is deleted:  " . $apps->count());
        } catch (\Exception $exception) {
            Log::error("[CronJob][AutoDeleteOldVerBuild] " . $exception->getMessage());
        }

        return 0;
    }
}
