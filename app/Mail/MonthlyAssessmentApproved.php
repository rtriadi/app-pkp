<?php

namespace App\Mail;

use App\Models\MonthlyAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyAssessmentApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MonthlyAssessment $assessment;

    /**
     * Create a new message instance.
     */
    public function __construct(MonthlyAssessment $assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $monthName = date('F', mktime(0, 0, 0, $this->assessment->month, 1));
        return new Envelope(
            subject: "Penilaian Bulanan {$monthName} {$this->assessment->year} Telah Disetujui - APP PKP",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-assessment-approved',
            with: [
                'assessment' => $this->assessment,
                'employee' => $this->assessment->performanceAgreement->employee,
                'agreement' => $this->assessment->performanceAgreement,
                'monthName' => date('F', mktime(0, 0, 0, $this->assessment->month, 1)),
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
