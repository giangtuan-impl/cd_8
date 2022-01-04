<?php

namespace App\Console\Commands;

use App\Models\BuildNumber;
use Illuminate\Console\Command;

class ModifyBuildLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modify:build-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modify to new link';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
            old link: /var/www/CDInternal/storage/app/iOS_HealthCare/build-jenkins-2/HealthCare_development.ipa
            new link: HealthCare_development.ipa
        */
        $this->info('Modifying build link...');

        $builds = BuildNumber::all();
        foreach ($builds as $build) {
            if ($build->link) {
                $build->link = array_reverse(explode('/', $build->link))[0];
                $build->save();
            }
        }

        $this->info('Done !');
    }
}
