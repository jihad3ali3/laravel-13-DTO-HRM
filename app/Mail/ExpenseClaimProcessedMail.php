<?php

namespace App\Mail;

use App\Models\ExpenseClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseClaimProcessedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ExpenseClaim $claim) {}

    public function envelope(): Envelope
    {
        $status = ucfirst($this->claim->status);

        return new Envelope(
            subject: "Expense Claim ({$this->claim->claim_code}) Status: {$status}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expense-status',
        );
    }
}
