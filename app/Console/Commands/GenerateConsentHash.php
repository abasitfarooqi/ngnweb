<?php

namespace App\Console\Commands;

use App\Helpers\JudopayConsentHelper;
use Illuminate\Console\Command;

class GenerateConsentHash extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'judopay:generate-consent-hash {blade_file?}';

    /**
     * The console command description.
     */
    protected $description = 'Generate SHA-256 hash for consent form blade file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bladeFile = $this->argument('blade_file');
        
        // If no blade file provided, prompt for it
        if (!$bladeFile) {
            $bladeFile = $this->ask('Enter blade file name (e.g., judopay-authorisation-concent-form-v2)');
        }
        
        if (!$bladeFile) {
            $this->error('Blade file name is required');
            return 1;
        }
        
        // Remove .blade.php extension if provided
        $bladeFile = str_replace('.blade.php', '', $bladeFile);
        
        $this->info("Processing blade file: {$bladeFile}");
        
        // Check if file exists
        $filePath = resource_path("views/{$bladeFile}.blade.php");
        
        if (!file_exists($filePath)) {
            $this->error("Blade file not found: {$filePath}");
            return 1;
        }
        
        $this->info("✓ Blade file found: {$filePath}");
        
        // Extract consent text
        $consentText = JudopayConsentHelper::extractConsentTextFromBlade($bladeFile);
        
        if (empty($consentText)) {
            $this->error('Failed to extract consent text from blade file');
            return 1;
        }
        
        $this->info("✓ Extracted consent text (" . strlen($consentText) . " characters)");
        
        // Generate hash
        $hash = JudopayConsentHelper::generateConsentHash($consentText);
        
        if (!$hash) {
            $this->error('Failed to generate SHA-256 hash');
            return 1;
        }
        
        $this->info("✓ Generated SHA-256 hash:");
        $this->line('');
        $this->line("  {$hash}");
        $this->line('');
        
        // Show config snippet
        $this->info('📋 Copy this into config/judopay.php under consent.versions:');
        $this->line('');
        
        $versionId = $this->ask('Enter version ID (e.g., v2.0-judopay-cit)', 'v2.0-judopay-cit');
        $effectiveDate = $this->ask('Enter effective date (e.g., 2026-01-01)', '2026-01-01');
        $description = $this->ask('Enter description', 'Updated consent form');
        
        $configSnippet = "'{$versionId}' => [
    'blade_file' => '{$bladeFile}',
    'effective_date' => '{$effectiveDate}',
    'hash' => '{$hash}',
    'description' => '{$description}',
],";
        
        $this->line($configSnippet);
        $this->line('');
        
        // Ask if they want to see the extracted text
        if ($this->confirm('Show extracted consent text?')) {
            $this->line('');
            $this->line('Extracted consent text:');
            $this->line('─' . str_repeat('─', 50));
            $this->line($consentText);
            $this->line('─' . str_repeat('─', 50));
        }
        
        $this->info('Hash generation completed successfully!');
        
        return 0;
    }
}
