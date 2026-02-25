<?php

namespace App\Console\Commands;

use App\Services\WhatsAppNotificationService;
use Illuminate\Console\Command;

class SendPcnRemindersWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-pcn-reminders-whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for outstanding PCNs every 14 working days';

    protected $whatsappService;

    public function __construct(WhatsAppNotificationService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->whatsappService->sendPcnReminders();
        $this->info('Logged intended WhatsApp reminder messages successfully.');
    }
}
