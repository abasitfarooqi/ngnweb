<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class JudopayConsentHelper
{
    /**
     * Get the current consent version from config
     */
    public static function getCurrentVersion(): string
    {
        try {
            return config('judopay.consent.current_version', 'v1.0-judopay-cit');
        } catch (\Exception $e) {
            Log::error('Failed to get current consent version', ['error' => $e->getMessage()]);
            return 'v1.0-judopay-cit';
        }
    }

    /**
     * Get version configuration with fallback
     */
    public static function getVersionConfig(string $version): array
    {
        try {
            $versions = config('judopay.consent.versions', []);
            $config = $versions[$version] ?? null;
            
            if (!$config) {
                Log::warning('Consent version config not found, using default', ['version' => $version]);
                return [
                    'blade_file' => 'judopay-authorisation-concent-form-v1',
                    'effective_date' => '2025-10-07',
                    'hash' => null,
                    'description' => 'Default consent version',
                ];
            }
            
            return $config;
        } catch (\Exception $e) {
            Log::error('Failed to get version config', [
                'version' => $version,
                'error' => $e->getMessage()
            ]);
            
            return [
                'blade_file' => 'judopay-authorisation-concent-form-v1',
                'effective_date' => '2025-10-07',
                'hash' => null,
                'description' => 'Default consent version',
            ];
        }
    }

    /**
     * Get blade file name for version
     */
    public static function getBladeFile(string $version): string
    {
        try {
            $config = self::getVersionConfig($version);
            return $config['blade_file'] ?? 'judopay-authorisation-concent-form-v1';
        } catch (\Exception $e) {
            Log::error('Failed to get blade file for version', [
                'version' => $version,
                'error' => $e->getMessage()
            ]);
            return 'judopay-authorisation-concent-form-v1';
        }
    }

    /**
     * Extract consent text from blade file (sections 1-8)
     */
    public static function extractConsentTextFromBlade(string $bladeFile): string
    {
        try {
            $filePath = resource_path("views/{$bladeFile}.blade.php");
            
            if (!file_exists($filePath)) {
                Log::error('Blade file not found', ['file' => $filePath]);
                return '';
            }
            
            $content = file_get_contents($filePath);
            
            // Extract sections 1-8 (the consent text)
            // Look for the section titles and extract content between them
            $pattern = '/<h6 class="section-title">(1|2|3|4|5|6|7|8)\.\s*[^<]+<\/h6>\s*<p>([^<]+)<\/p>/';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            
            $consentText = '';
            foreach ($matches as $match) {
                $consentText .= $match[2] . "\n";
            }
            
            // Normalise whitespace
            $consentText = preg_replace('/\s+/', ' ', trim($consentText));
            
            Log::info('Extracted consent text', [
                'blade_file' => $bladeFile,
                'text_length' => strlen($consentText)
            ]);
            
            return $consentText;
        } catch (\Exception $e) {
            Log::error('Failed to extract consent text from blade', [
                'blade_file' => $bladeFile,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Generate SHA-256 hash of consent text
     */
    public static function generateConsentHash(string $text): ?string
    {
        try {
            if (empty($text)) {
                Log::warning('Empty consent text provided for hash generation');
                return null;
            }
            
            $hash = hash('sha256', $text);
            
            Log::info('Generated consent hash', [
                'text_length' => strlen($text),
                'hash' => $hash
            ]);
            
            return $hash;
        } catch (\Exception $e) {
            Log::error('Failed to generate consent hash', [
                'text_length' => strlen($text),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verify consent hash matches text
     */
    public static function verifyConsentHash(string $text, string $hash): bool
    {
        try {
            $generatedHash = self::generateConsentHash($text);
            
            if (!$generatedHash) {
                Log::error('Failed to generate hash for verification');
                return false;
            }
            
            $isValid = hash_equals($generatedHash, $hash);
            
            Log::info('Consent hash verification', [
                'is_valid' => $isValid,
                'provided_hash' => $hash,
                'generated_hash' => $generatedHash
            ]);
            
            return $isValid;
        } catch (\Exception $e) {
            Log::error('Failed to verify consent hash', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get consent text for display (for debugging/audit)
     */
    public static function getConsentTextForDisplay(string $version): string
    {
        try {
            $config = self::getVersionConfig($version);
            $bladeFile = $config['blade_file'] ?? 'judopay-authorisation-concent-form-v1';
            
            return self::extractConsentTextFromBlade($bladeFile);
        } catch (\Exception $e) {
            Log::error('Failed to get consent text for display', [
                'version' => $version,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }
}
