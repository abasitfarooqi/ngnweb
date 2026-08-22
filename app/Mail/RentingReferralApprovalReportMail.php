<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\RentingReferral;
use App\Models\User;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentingReferralApprovalReportMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(
        public RentingReferral $referral,
        public ?int $approvedByUserId = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Rental referral approval report');
    }

    public function content(): Content
    {
        $approver = $this->approvedByUserId
            ? User::query()->find($this->approvedByUserId)
            : $this->referral->reviewedBy;

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-referral-approval-report',
                [
                    'referral' => $this->referral,
                    'approver' => $approver,
                    'logs' => $this->referral->logs()->with('changedBy')->orderBy('id')->get(),
                    'checks' => app(\App\Services\Renting\RentingReferralService::class)->investigationChecks($this->referral),
                    'credit' => $this->referral->credit(),
                ],
                'Rental referral approval report',
            ),
        );
    }
}
