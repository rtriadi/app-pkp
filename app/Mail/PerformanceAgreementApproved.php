<?php

namespace App\Mail;

use App\Models\PerformanceAgreement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PerformanceAgreementApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PerformanceAgreement $agreement;

    /**
     * Create a new message instance.
     */
    public function __construct(PerformanceAgreement $agreement)
    {
        $this->agreement = $agreement;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Perjanjian Kinerja Anda Telah Disetujui - APP PKP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.performance-agreement-approved',
            with: [
                'agreement' => $this->agreement,
                'employee' => $this->agreement->employee,
                'approver' => $this->agreement->approvedBy,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
