<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IntrusionAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Ang variable na naglalaman ng detalye ng intrusion.
     *
     * @var string
     */
    public $alertDetails;

    /**
     * Create a new message instance.
     *
     * @param string $alertDetails
     * @return void
     */
    public function __construct($alertDetails)
    {
        $this->alertDetails = $alertDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Itinuro na natin sa tamang path: resources/views/admin/email/intrusion_alert.blade.php
        return $this->subject('⚠ CRITICAL: SecureLab Intrusion Alert')
                    ->view('admin.email.intrusion_alert');
    }
}