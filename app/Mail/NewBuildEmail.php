<?php

namespace App\Mail;

use App\Models\BuildNumber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewBuildEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $build;

    public function __construct($build)
    {
        $this->build = $build;
    }

    public function build()
    {
        $appName = $this->build->app->app_name;
        $buildNumber = $this->build->build_number;
        $env = $this->build->env == BuildNumber::ENV_IOS ? 'iOS ' : 'Android ';
        $subject = '[Alobridge CD] ' . $env . $appName . ' #' . $buildNumber . ' ' . trans('messages.has_been_delivered');

        return $this->view('mail_template.new_build_email')
            ->subject($subject)
            ->with([
                'appName' => $appName,
                'buildNumber' => $buildNumber,
                'link' => route('apps.show', ['app' => $this->build->app->id])
            ]);
    }
}
