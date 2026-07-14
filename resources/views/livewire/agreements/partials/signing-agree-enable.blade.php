{{-- Enable #signButton when #agreementCheckbox is checked. Include after both elements exist. --}}
<script>
(function () {
    function bindAgreementSignToggle() {
        var agreementCheckbox = document.getElementById('agreementCheckbox');
        var signButton = document.getElementById('signButton');
        if (!agreementCheckbox || !signButton) {
            return;
        }

        var sync = function () {
            var ok = !!agreementCheckbox.checked;
            signButton.disabled = !ok;
            if (ok) {
                signButton.removeAttribute('disabled');
                signButton.classList.remove('disabled');
                signButton.setAttribute('aria-disabled', 'false');
            } else {
                signButton.setAttribute('disabled', 'disabled');
                signButton.classList.add('disabled');
                signButton.setAttribute('aria-disabled', 'true');
            }
        };

        agreementCheckbox.addEventListener('change', sync);
        agreementCheckbox.addEventListener('input', sync);
        agreementCheckbox.addEventListener('click', function () {
            // Keep UI in sync if custom checkbox styles delay the change event.
            setTimeout(sync, 0);
        });

        var label = agreementCheckbox.closest('label') || document.querySelector('label[for="agreementCheckbox"]');
        if (label) {
            label.addEventListener('click', function () {
                setTimeout(sync, 0);
            });
        }

        sync();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindAgreementSignToggle);
    } else {
        bindAgreementSignToggle();
    }
})();
</script>
