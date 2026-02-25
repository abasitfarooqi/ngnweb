<?php

namespace App\Helpers;

class JudopayLogSanitizer
{
    /**
     * Kill switch for redaction - set to false to disable all sanitisation
     * WARNING: Only disable for debugging! Never disable in production!
     * 
     * To disable: Change this line to: return false;
     */
    private static function isRedactionEnabled(): bool
    {
        return true; // ← Change this to 'false' to disable redaction
    }

    /**
     * Sanitise JudoPay response for safe logging
     * - Card tokens: Shows first 2 and last 2 chars (SO*****7w)
     * - Auth codes: Full redaction (***REDACTED***)
     * - PII: Partial masking for debugging
     */
    public static function sanitizeResponse(array $response): array
    {
        // Kill switch - return original data if redaction disabled
        if (!self::isRedactionEnabled()) {
            return $response;
        }

        $sanitised = $response;

        // 1. Sanitise card tokens (all variations)
        $sanitised = self::sanitizeCardTokens($sanitised);

        // 2. Redact auth/transaction IDs
        $sanitised = self::redactTransactionIds($sanitised);

        // 3. Mask PII
        $sanitised = self::maskPii($sanitised);

        // 4. Sanitise nested receipts
        if (isset($sanitised['receipt'])) {
            $sanitised['receipt'] = self::sanitizeResponse($sanitised['receipt']);
        }

        return $sanitised;
    }

    /**
     * Sanitise card tokens - keep first 2 and last 2 chars
     */
    private static function sanitizeCardTokens(array $data): array
    {
        // Handle both 'cardToken' and 'CardToken' (case variations)
        foreach (['cardToken', 'CardToken'] as $key) {
            if (isset($data[$key]) && ! empty($data[$key])) {
                $data[$key] = self::partialRedact($data[$key], 2, 2);
            }
        }

        // Handle nested cardDetails.cardToken
        if (isset($data['cardDetails']['cardToken']) && ! empty($data['cardDetails']['cardToken'])) {
            $data['cardDetails']['cardToken'] = self::partialRedact($data['cardDetails']['cardToken'], 2, 2);
        }

        return $data;
    }

    /**
     * Redact sensitive transaction IDs completely
     */
    private static function redactTransactionIds(array $data): array
    {
        $fieldsToRedact = [
            'authCode',
            'acquirerTransactionId',
            'paymentNetworkTransactionId',
        ];

        foreach ($fieldsToRedact as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }

    /**
     * Mask PII for GDPR compliance
     */
    private static function maskPii(array $data): array
    {
        // Mask email
        if (isset($data['emailAddress'])) {
            $data['emailAddress'] = self::maskEmail($data['emailAddress']);
        }

        // Mask phone
        if (isset($data['mobileNumber'])) {
            $data['mobileNumber'] = self::maskPhone($data['mobileNumber']);
        }

        // Mask cardholder name
        if (isset($data['cardHolderName'])) {
            $data['cardHolderName'] = self::maskName($data['cardHolderName']);
        }
        if (isset($data['cardDetails']['cardHolderName'])) {
            $data['cardDetails']['cardHolderName'] = self::maskName($data['cardDetails']['cardHolderName']);
        }

        // Mask billing address
        if (isset($data['billingAddress'])) {
            $data['billingAddress'] = self::maskAddress($data['billingAddress']);
        }

        // Mask card address (and its cardHolderName)
        if (isset($data['cardAddress'])) {
            if (isset($data['cardAddress']['cardHolderName'])) {
                $data['cardAddress']['cardHolderName'] = self::maskName($data['cardAddress']['cardHolderName']);
            }
            $data['cardAddress'] = self::maskAddress($data['cardAddress']);
        }

        return $data;
    }

    /**
     * Partial redaction - keep first N and last N chars
     */
    public static function partialRedact(string $value, int $keepStart = 2, int $keepEnd = 2): string
    {
        // Kill switch - return original value if redaction disabled
        if (!self::isRedactionEnabled()) {
            return $value;
        }

        $length = strlen($value);
        if ($length <= ($keepStart + $keepEnd)) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, $keepStart).str_repeat('*', $length - $keepStart - $keepEnd).substr($value, -$keepEnd);
    }

    /**
     * Mask email - keep first and last char of local part
     */
    private static function maskEmail(string $email): string
    {
        if (strpos($email, '@') === false) {
            return $email;
        }
        [$local, $domain] = explode('@', $email, 2);
        if (strlen($local) <= 2) {
            return $email;
        }

        return substr($local, 0, 1).str_repeat('*', strlen($local) - 2).substr($local, -1).'@'.$domain;
    }

    /**
     * Mask phone - keep first 3 and last 4 digits
     */
    private static function maskPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleaned) < 7) {
            return $phone;
        }

        return substr($cleaned, 0, 3).str_repeat('*', strlen($cleaned) - 7).substr($cleaned, -4);
    }

    /**
     * Mask name - keep first char of each word
     */
    private static function maskName(string $name): string
    {
        $words = explode(' ', $name);

        return implode(' ', array_map(function ($word) {
            return strlen($word) > 0 ? substr($word, 0, 1).'***' : $word;
        }, $words));
    }

    /**
     * Mask address object
     */
    private static function maskAddress(array $address): array
    {
        if (isset($address['address1'])) {
            $address['address1'] = substr($address['address1'], 0, 1).'***';
        }
        if (isset($address['address2'])) {
            $address['address2'] = '***';
        }
        if (isset($address['postCode']) || isset($address['postcode'])) {
            $pc = $address['postCode'] ?? $address['postcode'];
            if (strlen($pc) > 3) {
                $masked = substr($pc, 0, 2).str_repeat('*', strlen($pc) - 3).substr($pc, -1);
                isset($address['postCode']) ? $address['postCode'] = $masked : $address['postcode'] = $masked;
            }
        }
        if (isset($address['town'])) {
            $address['town'] = substr($address['town'], 0, 1).'***';
        }

        return $address;
    }

    /**
     * Sanitise HTTP headers
     */
    public static function sanitizeHeaders(array $headers): array
    {
        // Kill switch - return original headers if redaction disabled
        if (!self::isRedactionEnabled()) {
            return $headers;
        }

        $sanitised = $headers;

        // Sanitise Authorization header
        if (isset($sanitised['authorization'])) {
            $sanitised['authorization'] = ['Basic ***REDACTED***'];
        }
        if (isset($sanitised['Authorization'])) {
            $sanitised['Authorization'] = 'Basic ***REDACTED***';
        }

        // Sanitise PHP auth headers
        if (isset($sanitised['php-auth-user'])) {
            $sanitised['php-auth-user'] = ['***REDACTED***'];
        }
        if (isset($sanitised['php-auth-pw'])) {
            $sanitised['php-auth-pw'] = ['***REDACTED***'];
        }

        return $sanitised;
    }

    /**
     * Safe log JudoPay response without sensitive data
     */
    public static function logResponse(string $context, array $response): void
    {
        \Log::channel('judopay')->info($context, self::sanitizeResponse($response));
    }
}

