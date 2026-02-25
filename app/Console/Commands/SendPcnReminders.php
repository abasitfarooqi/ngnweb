<?php

namespace App\Console\Commands;

use App\Services\SmsNotificationService;
use Illuminate\Console\Command;

class SendPcnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-pcn-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders for outstanding PCNs every 14 working days';

    protected $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->smsService->sendPcnReminders();
        $this->info('Logged intended PCN reminder SMS successfully.');
    }
}
