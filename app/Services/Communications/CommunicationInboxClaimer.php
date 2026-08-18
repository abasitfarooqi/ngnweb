<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\CommunicationRecipient;
use App\Models\CustomerAuth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CommunicationInboxClaimer
{
    public function claimFor(CustomerAuth $customer): int
    {
        if (! app(CommunicationSchema::class)->ready()) {
            return 0;
        }

        $email = strtolower(trim((string) $customer->email));
        if ($email === '' || ! str_contains($email, '@')) {
            return 0;
        }

        $claimed = 0;

        try {
            $rows = Communication::query()
                ->with('deliveries')
                ->whereRaw('LOWER(recipient_email) = ?', [$email])
                ->where(function ($query) use ($customer): void {
                    $query->whereNull('customer_auth_id')
                        ->orWhere('customer_auth_id', $customer->id);
                })
                ->whereDoesntHave('recipients', fn ($query) => $query->where('customer_auth_id', $customer->id))
                ->orderBy('id')
                ->get();

            foreach ($rows as $communication) {
                if (! $this->inboxWasIntended($communication)) {
                    continue;
                }

                $this->attachRecipient($communication, $customer);
                $claimed++;
            }
        } catch (Throwable $exception) {
            Log::warning('Transactional communication inbox claim failed.', [
                'customer_auth_id' => $customer->id,
                'message' => $exception->getMessage(),
            ]);
        }

        return $claimed;
    }

    private function inboxWasIntended(Communication $communication): bool
    {
        if ((bool) data_get($communication->policy_snapshot, 'internal_inbox_enabled', false)) {
            return true;
        }

        return $communication->deliveries
            ->contains(fn (CommunicationDelivery $delivery): bool => $delivery->channel === 'internal_inbox');
    }

    private function attachRecipient(Communication $communication, CustomerAuth $customer): void
    {
        $now = now();

        if ($communication->customer_auth_id === null || $communication->customer_id === null) {
            $communication->forceFill([
                'customer_auth_id' => $communication->customer_auth_id ?: $customer->id,
                'customer_id' => $communication->customer_id ?: $customer->customer_id,
            ])->save();
        }

        CommunicationRecipient::query()->firstOrCreate(
            [
                'communication_id' => $communication->id,
                'customer_auth_id' => $customer->id,
            ],
            [],
        );

        $inbox = $communication->deliveries
            ->first(fn (CommunicationDelivery $delivery): bool => $delivery->channel === 'internal_inbox');

        if ($inbox && in_array($inbox->status, ['deferred', 'failed', 'pending'], true)) {
            $inbox->forceFill([
                'status' => 'delivered',
                'sent_at' => $inbox->sent_at ?: $now,
                'delivered_at' => $now,
                'failed_at' => null,
                'failure_reason' => null,
            ])->save();
        }
    }
}
