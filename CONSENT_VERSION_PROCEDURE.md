# 📋 PROCEDURE: How to Add a New Consent Form Version

## Complete Step-by-Step Guide

### Step 1: Create New Blade File
```bash
# Copy existing version
cp resources/views/judopay-authorisation-concent-form-v1.blade.php \
   resources/views/judopay-authorisation-concent-form-v2.blade.php
```

**Then edit `judopay-authorisation-concent-form-v2.blade.php`**:
- Modify consent text in sections 1-8 (lines 97-122) as needed
- Keep all other structure identical
- **Do NOT touch v1 file**

### Step 2: Generate SHA-256 Hash
```bash
php artisan judopay:generate-consent-hash judopay-authorisation-concent-form-v2
```

**Copy the generated hash from terminal output**

### Step 3: Update Config File
Open `config/judopay.php` and add new version under the `consent.versions` section:

```php
'consent' => [
    'current_version' => 'v1.0-judopay-cit', // Change this when releasing new version
    
    'versions' => [
        'v1.0-judopay-cit' => [/* existing */],
        
        'v2.0-judopay-cit' => [
            'blade_file' => 'judopay-authorisation-concent-form-v2',
            'effective_date' => '2026-01-01',  // When v2 becomes effective
            'hash' => 'paste_generated_hash_here',  // From Step 2
            'description' => 'Updated for FCA 2026 requirements',
        ],
    ],
],
```

### Step 4: Test New Version (Optional)
Before making live, test by temporarily forcing v2:

```php
// In RecurringController.php showAuthorizationForm() method
$version = 'v2.0-judopay-cit'; // Force v2 for testing
```

Test the flow, then remove this line.

### Step 5: Make New Version Live
Update config:

```php
'current_version' => 'v2.0-judopay-cit',  // Changed from v1.0
```

**Done!** New customers now see v2, existing customers keep v1 (unless you change grandfathering logic).

---

## Version Behaviour

### Existing Customers (already signed v1)
- Continue seeing v1 form if they re-authorise (unless forced upgrade)
- All their payments reference v1 hash
- v1 remains legally valid indefinitely

### New Customers (after v2 release)
- Automatically shown v2 form
- Their payments reference v2 hash

### Optional: Force Upgrade
To require existing customers to sign new terms:
```php
// In showAuthorizationForm()
$version = JudopayConsentHelper::getCurrentVersion(); // Always use latest
```

---

## Example Terminal Output

When running the hash generation command:

```
Processing blade file: judopay-authorisation-concent-form-v2
✓ Blade file found: resources/views/judopay-authorisation-concent-form-v2.blade.php
✓ Extracted consent text (1,234 characters)
✓ Generated SHA-256 hash:

  a3f5b8c9d2e1f0a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3

📋 Copy this into config/judopay.php under consent.versions:

'v2.0-judopay-cit' => [
    'blade_file' => 'judopay-authorisation-concent-form-v2',
    'effective_date' => '2026-01-01',
    'hash' => 'a3f5b8c9d2e1f0a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3',
    'description' => 'Your description here',
],
```

---

## Safety Notes

- ✅ **Never modify existing blade files** - always create new versions
- ✅ **Test before going live** - use Step 4 to verify new version works
- ✅ **Keep old versions** - they remain legally valid for existing customers
- ✅ **Config-driven** - all version management in one file
- ✅ **Hash verification** - system can prove exact terms shown to customer

---

## Troubleshooting

### Hash generation fails
- Check blade file exists and is readable
- Ensure sections 1-8 contain consent text
- Check file permissions

### Version not found
- Verify version ID in config matches exactly
- Check config file syntax (no trailing commas)
- Clear config cache: `php artisan config:clear`

### Blade file not found
- Check file path in config
- Ensure `.blade.php` extension is correct
- Verify file exists in `resources/views/`

---

## Legal Compliance

This system provides:
- **Non-repudiation**: Customer cannot claim terms were different
- **Audit trail**: Every payment knows its exact consent version
- **Version control**: Track changes to consent terms over time
- **GDPR compliance**: Demonstrate transparency in data processing
