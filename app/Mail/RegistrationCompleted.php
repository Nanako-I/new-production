<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
                    ->subject('登録完了のお知らせ')
                    ->view('emails.registration_completed')
                    ->with(['user' => $this->user]);
    }
} 