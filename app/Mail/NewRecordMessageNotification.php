<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class NewRecordMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $formattedDate;

    public function __construct($user, $selectedDate)
    {
        $this->user = $user;
        // 日付を「月日」の形式にフォーマット
        $this->formattedDate = Carbon::parse($selectedDate)->format('n月j日');
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
                    ->subject('連絡帳が届きました')
                    ->view('emails.new-record-message')
                    ->with([
                        'user' => $this->user,
                        'selectedDate' => $this->formattedDate // フォーマット済みの日付を渡す
                    ]);
    }
}