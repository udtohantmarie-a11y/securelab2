<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode, $userName)
    {
        $this->otpCode = $otpCode;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Dito natin binago: Itinuro na natin sa admin/email/ folder mo
        return $this->subject('SecureLab - Login Verification Code')
                    ->view('admin.email.otp_template');
    }
}