<?php

namespace App\Services;

use App\Jobs\SendNewBuildEmailJob;

class NotifyService
{
    public function sendNewBuildEmail($build) {
        $invitedUsers = $build->app->members;

        foreach ($invitedUsers as $user) {
            $email = new SendNewBuildEmailJob($build, $user);
            dispatch($email);
        }
    }
}