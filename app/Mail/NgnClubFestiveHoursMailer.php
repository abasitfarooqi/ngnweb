<?php

namespace App\Mail;

use App\Models\ClubMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NgnClubFestiveHoursMailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The NGN Club member instance.
     */
    public ClubMember $member;

    /**
     * Create a new message instance.
     */
    public function __construct(ClubMember $member)
    {
        $this->member = $member;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('olders.emails.club.festive-hours')
            ->subject('Festive opening hours – NGN')
            ->with([
                'member' => $this->member,
            ]);
    }
}
