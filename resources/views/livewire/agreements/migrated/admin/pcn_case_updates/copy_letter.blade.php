@php
    $letter = <<<'EOT'
Dear Enforcement Team,

Please find attached the required documents for the transfer of liability for the above Penalty Charge Notice, in accordance with:
• Traffic Management Act 2004 (Sections 82–92)
• Road Traffic Act 1988 (Section 66(2) & Schedule 2)
• Road Traffic Offenders Act 1988
• Road Traffic Regulation Act 1984
• Where applicable: London Local Authorities and TfL Acts

At the material time, the vehicle was in the possession and control of the customer identified in the enclosed agreement.

The attached documents meet all statutory requirements for transfer of liability.
Attached documents:
Agreement
Statutory Extract
Authorisation Certificate

Please confirm that liability has been transferred to the customer.
EOT;
@endphp

<div class="mb-3" id="pcn-copy-liability-letter">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <strong class="text-uppercase small mb-0">Copy liability letter</strong>
        <button type="button" class="btn btn-sm btn-success px-3 text-nowrap" id="copy-letter-btn">Copy letter</button>
    </div>
    <textarea id="pcn-letter-template" class="form-control" rows="16" readonly>{{ $letter }}</textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('copy-letter-btn');
    var field = document.getElementById('pcn-letter-template');
    if (!btn || !field) return;

    btn.addEventListener('click', function () {
        var text = field.value;
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var original = btn.textContent;
            btn.textContent = 'Copied';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            setTimeout(function () {
                btn.textContent = original;
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
            }, 2000);
        }).catch(function (err) {
            console.error('Copy failed:', err);
        });
    });
});
</script>
