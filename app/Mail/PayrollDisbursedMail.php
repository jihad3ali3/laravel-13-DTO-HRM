<?php

namespace App\Mail;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayrollDisbursedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payroll $payroll) {}

    public function envelope(): Envelope
    {
        $monthName = date('F', mktime(0, 0, 0, $this->payroll->month, 1));

        return new Envelope(
            subject: "Salary Disbursed for {$monthName} {$this->payroll->year}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payroll-disbursed',
        );
    }
}
