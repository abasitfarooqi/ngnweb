<?php

// app/Console/Commands/SendBatchEmails.php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendBatchEmails extends Command
{
    // Command signature and description
    protected $signature = 'send:batch-emails';

    protected $description = 'Send batch emails to users who have not received them yet';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Dispatch the job to send batch emails
        dispatch(new SendBatchUserCredentialsJob);

        $this->info('Batch emails are being sent.');
    }
}
