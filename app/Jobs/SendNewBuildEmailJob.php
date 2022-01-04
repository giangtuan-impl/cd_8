<?php

namespace App\Jobs;

use App\Mail\NewBuildEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendNewBuildEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $build;
    protected $user;

    public function __construct($build, $user)
    {
        $this->build = $build;
        $this->user = $user;
    }

    public function handle()
    {
        $email = new NewBuildEmail($this->build);
        
        Mail::to($this->user->email)->locale($this->user->language)->send($email);
    }
}
