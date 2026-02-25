<?php

namespace App\Jobs;

use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Services\JudopayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JudopayRefundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $receiptId,
        public float $amount,
        public string $currency,
        public string $yourPaymentReference,
        public int $originalSessionId,
        public int $subscriptionId
    ) {}

    public function handle(): void
    {
        self::processRefund(
            $this->receiptId,
            $this->amount,
            $this->currency,
            $this->yourPaymentReference,
            $this->originalSessionId,
            $this->subscriptionId
        );
    }

    /**
     * Process refund synchronously (used for automatic refunds)
     */
    public static function processRefund(
        string $receiptId,
        float $amount,
        string $currency,
        string $yourPaymentReference,
        int $originalSessionId,
        int $subscriptionId
    ): void {
        try {
            Log::channel('judopay')->info('Processing automatic CIT refund', [
                'receipt_id' => $receiptId,
                'amount' => $amount,
                'currency' => $currency,
                'payment_reference' => $yourPaymentReference,
                'session_id' => $originalSessionId,
                'subscription_id' => $subscriptionId,
            ]);

            // Check for existing refund outcome to prevent duplicates (idempotency)
            $existingRefund = JudopayPaymentSessionOutcome::where('session_id', $originalSessionId)
                ->where('session_type', 'App\Models\JudopayCitPaymentSession')
                ->where('status', 'refunded')
                ->where('source', 'api')
                ->first();

            if ($existingRefund) {
                Log::channel('judopay')->warning('Refund already processed for this session - skipping', [
                    'session_id' => $originalSessionId,
                    'existing_outcome_id' => $existingRefund->id,
                    'receipt_id' => $receiptId,
                ]);

                return;
            }

            // Build refund payload
            $payload = [
                'receiptId' => $receiptId,
                'amount' => $amount,
                'currency' => $currency,
                'yourPaymentReference' => $yourPaymentReference,
            ];

            // Call JudoPay refund API
            $result = JudopayService::processRefund($payload);

            if ($result['success']) {
                Log::channel('judopay')->info('Automatic refund initiated successfully', [
                    'session_id' => $originalSessionId,
                    'receipt_id' => $receiptId,
                    'refund_receipt_id' => data_get($result['data'], 'receiptId'),
                    'note' => 'Webhook will update session status to refunded',
                ]);
            } else {
                Log::channel('judopay')->error('Automatic refund failed', [
                    'session_id' => $originalSessionId,
                    'receipt_id' => $receiptId,
                    'error' => $result['message'],
                ]);
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Automatic refund exception', [
                'session_id' => $originalSessionId,
                'receipt_id' => $receiptId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
