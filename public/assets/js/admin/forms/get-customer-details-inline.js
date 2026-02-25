document.addEventListener('DOMContentLoaded', function () {
    const customerWrapper = document.querySelector('.form-group[bp-field-name="customer_id"]');
    const nameWrapper     = document.querySelector('.form-group[bp-field-name="customer_name"]');
    const emailWrapper    = document.querySelector('.form-group[bp-field-name="customer_email"]');
    const phoneWrapper    = document.querySelector('.form-group[bp-field-name="customer_phone"]');

    const customerSelect = customerWrapper.querySelector('select');
    const nameInput      = nameWrapper.querySelector('input');
    const emailInput     = emailWrapper.querySelector('input');
    const phoneInput     = phoneWrapper.querySelector('input');

    function fillFields() {
        const renderedSpan = customerWrapper.querySelector('.select2-selection__rendered');
        if (!renderedSpan) return;

        const text = renderedSpan.title || renderedSpan.textContent;
        if (text && text.includes('|')) {
            const parts = text.split('|').map(s => s.trim());
            nameInput.value  = parts[0] || '';
            phoneInput.value = parts[1] || '';
            emailInput.value = parts[2] || '';
        } else {
            nameInput.value  = '';
            phoneInput.value = '';
            emailInput.value = '';
        }
    }

    // Trigger on selection
    $(customerSelect).on('select2:select select2:unselect', fillFields);

    // Clear fields if selection is cleared
    $(customerSelect).on('select2:clear', function () {
        nameInput.value  = '';
        phoneInput.value = '';
        emailInput.value = '';
    });

    // Clear select2 if user types manually
    [nameInput, emailInput, phoneInput].forEach(input => {
        input.addEventListener('input', function () {
            if (customerSelect.value) {
                $(customerSelect).val(null).trigger('change.select2');
            }
        });
    });

    // Observe DOM changes for the Select2 rendered span
    const observer = new MutationObserver(fillFields);
    observer.observe(customerWrapper, { childList: true, subtree: true });

    // Initial fill if a customer is pre-selected
    fillFields();
});
