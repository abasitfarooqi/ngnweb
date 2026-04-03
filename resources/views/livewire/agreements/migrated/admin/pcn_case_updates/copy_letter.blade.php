@php
    $pcnNumber = $entry->pcn_number ?? 'N/A';
    $regNo = optional($entry->motorbike)->reg_no ?? 'N/A';
    $hirer = optional($entry->customer)->full_name ?? 'N/A';
    $userName = trim((backpack_user()->first_name ?? '') . ' ' . (backpack_user()->last_name ?? '')) ?: (backpack_user()->name ?? 'NGN Staff');

    $customer = optional($entry)->customer;
    $dobStr = '';
    if ($customer && $customer->dob) {
        try {
            $dobStr = $customer->dob instanceof \DateTimeInterface
                ? $customer->dob->format('d/m/Y')
                : \Carbon\Carbon::parse($customer->dob)->format('d/m/Y');
        } catch (\Throwable $e) {
            $dobStr = '';
        }
    }
    $addressStr = trim(implode(', ', array_filter([
        optional($customer)->address,
        optional($customer)->postcode,
    ])));
    $licenceStr = optional($customer)->license_number ?? '';

    $contactNumber = '';
    if ($customer) {
        $contactNumber = trim((string) ($customer->phone ?? ''));
        if ($contactNumber === '') {
            $contactNumber = trim((string) ($customer->whatsapp ?? ''));
        }
        if ($contactNumber === '') {
            $contactNumber = 'N/A';
        }
    }
    $emailAddress = optional($customer)->email ?? '';

    $letter = <<<EOT
Dear Sir/Madam,

I am writing on behalf of Neguinho Motors Ltd to request the transfer of liability for the Penalty Charge Notice {$pcnNumber}, that was issued to vehicle registration {$regNo}.

Please be advised that this vehicle was hired to the following customer:

Name: "{$hirer}"
Address: {$addressStr}
Driving Licence No.: {$licenceStr}
Date of Birth: {$dobStr}
Contact Number: {$contactNumber}
Email Address: {$emailAddress}

We would be grateful if you could confirm by email once the liability has been successfully transferred to the hirer.

Thank you for your urgent attention to this matter.

Kind regards,
{$userName}
Office Manager
Neguinho Motors Ltd
Phone: +44 7929 554539
Email: Catford@neguinhomotors.co.uk
4A Penwortham Road, London, SW16 6RE
EOT;
@endphp

<div class="mb-2">
    <button type="button" class="btn btn-sm btn-primary" id="copy-letter-btn">Copy Letter</button>
</div>

<textarea id="pcn-letter-template" class="form-control d-none" rows="15" readonly>{{ $letter }}</textarea>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('copy-letter-btn')?.addEventListener('click', function () {
        const text = document.getElementById('pcn-letter-template')?.value;
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            alert('Letter copied to clipboard!');
        }).catch(err => console.error('Copy failed:', err));
    });
});
</script>
