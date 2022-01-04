<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $resetPasswordLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($resetPasswordLink)
    {
        $this->resetPasswordLink = $resetPasswordLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('auth.reset_password_link')
                    ->subject(trans('messages.reset_password_notification'))
                    ->with(['resetPasswordLink' => $this->resetPasswordLink]);
    }
}
