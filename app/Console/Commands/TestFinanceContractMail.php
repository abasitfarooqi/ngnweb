<?php

namespace App\Console\Commands;

use App\Mail\FinanceContractReview;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestFinanceContractMail extends Command
{
    protected $signature = 'mail:test-finance-contract {--to= : Recipient e-mail address}';

    protected $description = 'Send a sample FinanceContractReview mailable (template + transport).';

    public function handle(): int
    {
        $to = $this->option('to') ?: (string) config('mail.from.address');
        $mailData = [
            'title' => 'Contract Review (artisan test)',
            'customer_name' => 'Artisan Test Customer',
            'body' => 'Test body from mail:test-finance-contract.',
            'url' => rtrim((string) config('app.url'), '/').'/',
        ];

        $financeMailer = config('mail.finance_contract_mailer');
        try {
            if (is_string($financeMailer) && $financeMailer !== '' && array_key_exists($financeMailer, config('mail.mailers'))) {
                Mail::mailer($financeMailer)->to($to)->send(new FinanceContractReview($mailData));
                $used = $financeMailer;
            } else {
                Mail::to($to)->send(new FinanceContractReview($mailData));
                $used = (string) config('mail.default');
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Sent to {$to} using mailer \"{$used}\".");

        return self::SUCCESS;
    }
}
