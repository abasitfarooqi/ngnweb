<?php

namespace App\Services\Club;

use App\Http\Controllers\SMSController;
use App\Mail\NewSubscriberNotification;
use App\Models\ClubMember;
use App\Models\Customer;
use App\Support\UkMobilePhone;
use Illuminate\Support\Facades\Mail;

/**
 * Shared Site + Mobile club join rules:
 * split email/phone uniqueness, OR customer link, always mint passkey + send creds.
 */
class ClubMemberRegistrationService
{
    /**
     * @param  array{
     *     full_name:string,
     *     email:string,
     *     phone:string,
     *     make?:string|null,
     *     model?:string|null,
     *     year?:string|null|int,
     *     vrm?:string|null,
     *     tc_agreed?:bool
     * }  $input
     * @return array{
     *     ok:bool,
     *     errors?:array<string,string>,
     *     member?:ClubMember,
     *     was_existing?:bool,
     *     linked_customer?:bool
     * }
     */
    public function register(array $input): array
    {
        $email = $this->normaliseEmail((string) ($input['email'] ?? ''));
        $phone = UkMobilePhone::normalize((string) ($input['phone'] ?? ''));

        if ($phone === '' || ! UkMobilePhone::isValidMobile($phone)) {
            return [
                'ok' => false,
                'errors' => [
                    'phone' => 'Please enter a valid UK mobile number starting with 07 (not 02). Symbols and +44 are not allowed.',
                ],
            ];
        }

        $byEmail = ClubMember::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->first();
        $byPhone = ClubMember::query()
            ->where('phone', $phone)
            ->first();

        $errors = [];

        if ($byEmail && $byPhone && (int) $byEmail->id !== (int) $byPhone->id) {
            $errors['email'] = 'This email is already in use.';
            $errors['phone'] = 'You already have an account with this number. Please log in.';
        } elseif ($byEmail) {
            $errors[$this->normalisePhone((string) $byEmail->phone) === $phone ? 'phone' : 'email']
                = $this->normalisePhone((string) $byEmail->phone) === $phone
                ? 'You already have an account with this number. Please log in.'
                : 'This email is already in use.';
        } elseif ($byPhone) {
            $errors['phone'] = 'You already have an account with this number. Please log in.';
        }

        if ($errors !== []) {
            return ['ok' => false, 'errors' => $errors];
        }

        $customerByEmail = Customer::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->first();
        $customerByPhone = Customer::query()
            ->where('phone', $phone)
            ->first();

        if ($customerByEmail && $customerByPhone && (int) $customerByEmail->id !== (int) $customerByPhone->id) {
            return [
                'ok' => false,
                'errors' => [
                    'email' => 'Email and phone belong to different customer records. Please contact support.',
                ],
            ];
        }

        $customer = $customerByEmail ?: $customerByPhone;

        $passkey = (string) random_int(100000, 999999);

        $clubMember = ClubMember::query()->create([
            'full_name' => trim((string) ($input['full_name'] ?? '')),
            'email' => $email,
            'phone' => $phone,
            'make' => $this->nullableString($input['make'] ?? null),
            'model' => $this->nullableString($input['model'] ?? null),
            'year' => $this->nullableString($input['year'] ?? null),
            'vrm' => isset($input['vrm']) && trim((string) $input['vrm']) !== ''
                ? strtoupper(trim((string) $input['vrm']))
                : null,
            'tc_agreed' => true,
            'is_active' => true,
            'passkey' => $passkey,
            'customer_id' => $customer?->id,
        ]);

        $linkedCustomer = false;
        if ($customer) {
            // Portal auth and club stay separate: link only, never force is_register.
            $customer->is_club = true;
            $customer->save();
            $linkedCustomer = true;
        }

        $this->sendCredentials($clubMember, $passkey);

        return [
            'ok' => true,
            'member' => $clubMember->fresh(),
            'was_existing' => false,
            'linked_customer' => $linkedCustomer,
        ];
    }

    public function normaliseEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public function normalisePhone(string $phone): string
    {
        return UkMobilePhone::normalize($phone);
    }

    /**
     * Resolve a club member for a logged-in portal customer (id, else email OR phone).
     */
    public function resolveForCustomer(Customer $customer, ?string $authEmail = null): ?ClubMember
    {
        $linked = $customer->clubMember;
        if ($linked) {
            return $linked;
        }

        $email = $this->normaliseEmail((string) ($authEmail ?: $customer->email ?: ''));
        $phone = $this->normalisePhone((string) ($customer->phone ?: ''));

        if ($email !== '') {
            $byEmail = ClubMember::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                ->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        if ($phone !== '') {
            return ClubMember::query()->where('phone', $phone)->first();
        }

        return null;
    }

    private function sendCredentials(ClubMember $clubMember, string $passkey): void
    {
        $phone = (string) $clubMember->phone;
        try {
            app(SMSController::class)->sendSms(
                $phone,
                "Your NGN Club Login Details:\nPhone: {$phone}\nPassword: {$passkey}"
            );
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to($clubMember->email)->send(new NewSubscriberNotification($clubMember, $passkey));
        } catch (\Throwable $e) {
            // Keep signup successful even if email fails.
            report($e);
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
