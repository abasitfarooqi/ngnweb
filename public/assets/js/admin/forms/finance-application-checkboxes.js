document.addEventListener('DOMContentLoaded', function () {
    const isNewWrapper = document.querySelector('.form-group[bp-field-name="is_new"]');
    const isUsedWrapper = document.querySelector('.form-group[bp-field-name="is_used"]');
    const isUsedExtendedCustomWrapper = document.querySelector('.form-group[bp-field-name="is_used_extended_custom"]');
    const isNewLatestWrapper = document.querySelector('.form-group[bp-field-name="is_new_latest"]');
    const isUsedLatestWrapper = document.querySelector('.form-group[bp-field-name="is_used_latest"]');
    const insurancePCNWrapper = document.querySelector('.form-group[bp-field-name="insurance_pcn"]');

    const checkboxes = [
        isNewWrapper ? isNewWrapper.querySelector('input.form-check-input[type="checkbox"]') : null,
        isUsedWrapper ? isUsedWrapper.querySelector('input.form-check-input[type="checkbox"]') : null,
        isUsedExtendedCustomWrapper ? isUsedExtendedCustomWrapper.querySelector('input.form-check-input[type="checkbox"]') : null,
        isNewLatestWrapper ? isNewLatestWrapper.querySelector('input.form-check-input[type="checkbox"]') : null,
        isUsedLatestWrapper ? isUsedLatestWrapper.querySelector('input.form-check-input[type="checkbox"]') : null,
    ].filter(Boolean);

    const hiddenInputs = [
        isNewWrapper ? isNewWrapper.querySelector('input[type="hidden"][name="is_new"]') : null,
        isUsedWrapper ? isUsedWrapper.querySelector('input[type="hidden"][name="is_used"]') : null,
        isUsedExtendedCustomWrapper ? isUsedExtendedCustomWrapper.querySelector('input[type="hidden"][name="is_used_extended_custom"]') : null,
        isNewLatestWrapper ? isNewLatestWrapper.querySelector('input[type="hidden"][name="is_new_latest"]') : null,
        isUsedLatestWrapper ? isUsedLatestWrapper.querySelector('input[type="hidden"][name="is_used_latest"]') : null,
    ].filter(Boolean);

    const insurancePCNCheckbox = insurancePCNWrapper ? insurancePCNWrapper.querySelector('input.form-check-input[type="checkbox"]') : null;

    // Create or find the status label container below checkboxes
    let statusContainer = document.querySelector('#contractTypeStatus');
    if (!statusContainer) {
        statusContainer = document.createElement('div');
        statusContainer.id = 'contractTypeStatus';
        statusContainer.style.marginTop = '0px';
        statusContainer.style.marginBottom = '10px';
        statusContainer.style.fontWeight = '600';
        const lastWrapper = isUsedLatestWrapper || isNewLatestWrapper || isUsedExtendedCustomWrapper || isUsedWrapper || isNewWrapper;
        if (lastWrapper && lastWrapper.parentNode) {
            lastWrapper.parentNode.insertBefore(statusContainer, lastWrapper.nextSibling);
        }
    }

    // Enhanced labels with insurance condition
    const labelsMap = {
        'is_new': 'New Motorcycle – 5 Year Standard Finance',
        'is_new + ins': 'New Motorcycle – 5 Months Contracts (Insurance/PCN)',
        'is_used': 'Used Vehicle – 5 Year Standard Finance with Limited Warranty',
        'is_used + ins': 'Used Vehicle – 5 Months Contracts (Insurance/PCN) with Limited Warranty',
        'is_used_extended_custom': 'Used Vehicle – 18 Month Contract with Conditional Warranty',
        'is_used_extended_custom + ins': 'Used Vehicle – 5 Months Contracts (Insurance/PCN) with Conditional Warranty',
        'is_new_latest': 'New Latest Contract – 12 Month Standard Finance',
        'is_new_latest + ins': 'New Latest Contract – 12 Months Contract (Insurance/PCN)',
        'is_used_latest': 'Used Latest Contract – 12 Month Standard Finance',
        'is_used_latest + ins': 'Used Latest Contract – 12 Months Contract (Insurance/PCN)',
    };

    // Helper to get currently selected contract type key (with insurance info)
    function getSelectedKey() {
        const selected = checkboxes.find(chk => chk.checked);
        if (!selected) return null;

        const wrapper = selected.closest('.form-group[bp-field-name]');
        const fieldName = wrapper ? wrapper.getAttribute('bp-field-name') : null;
        if (!fieldName) return null;

        const insChecked = insurancePCNCheckbox && insurancePCNCheckbox.checked;

        if (insChecked) {
            return fieldName + ' + ins';
        }
        return fieldName;
    }

    // Capture what was originally checked before script resets
    const originallyChecked = checkboxes.filter(chk => chk.checked);
    let originallySelectedLabel = 'None';
    if (originallyChecked.length === 1) {
        const fieldName = originallyChecked[0].closest('.form-group[bp-field-name]').getAttribute('bp-field-name');
        const insChecked = insurancePCNCheckbox && insurancePCNCheckbox.checked;
        originallySelectedLabel = labelsMap[insChecked ? fieldName + ' + ins' : fieldName] || 'Unknown';
    } else if (originallyChecked.length > 1) {
        originallySelectedLabel = originallyChecked.map(chk => {
            const fieldName = chk.closest('.form-group[bp-field-name]').getAttribute('bp-field-name');
            const insChecked = insurancePCNCheckbox && insurancePCNCheckbox.checked;
            return labelsMap[insChecked ? fieldName + ' + ins' : fieldName] || 'Unknown';
        }).join(', ');
    }

    // Show the original selection BEFORE resetting checkboxes
    statusContainer.textContent = 'Originally Selected Contract Type(s): ' + originallySelectedLabel;

    function updateStatusLabel() {
        const key = getSelectedKey();
        if (key) {
            statusContainer.textContent = 'Selected Contract Type: ' + (labelsMap[key] || 'Unknown');
        } else {
            statusContainer.textContent = 'No Contract Type Selected';
        }
    }

    function syncHiddenInputs() {
        checkboxes.forEach((chk, idx) => {
            if (hiddenInputs[idx]) {
                hiddenInputs[idx].value = chk.checked ? '1' : '0';
            }
        });
        updateStatusLabel();
    }

    function enforceSingleChecked(checkedCheckbox) {
        checkboxes.forEach(chk => {
            if (chk !== checkedCheckbox) {
                chk.checked = false;
            }
        });
        syncHiddenInputs();
    }

    // When contract type changes
    checkboxes.forEach(chk => {
        chk.addEventListener('change', function () {
            if (this.checked) {
                enforceSingleChecked(this);
            } else {
                syncHiddenInputs();
            }
        });
    });

    // When insurance_pcn changes — update label accordingly
    if (insurancePCNCheckbox) {
        insurancePCNCheckbox.addEventListener('change', updateStatusLabel);
    }

    // Initialise
    if (originallyChecked.length === 1) {
        enforceSingleChecked(originallyChecked[0]);
    } else {
        checkboxes.forEach(chk => (chk.checked = false));
        syncHiddenInputs();
    }

    // Subscription contract logic - Following the same pattern as checkboxes above
    const subscriptionWrapper = document.querySelector('.form-group[bp-field-name="is_subscription"]');
    const subscriptionCheckbox = subscriptionWrapper ? subscriptionWrapper.querySelector('input.form-check-input[type="checkbox"]') : null;
    const subscriptionOptionWrapper = document.querySelector('#subscription-option-wrapper');
    const subscriptionOptionRadios = subscriptionOptionWrapper ? subscriptionOptionWrapper.querySelectorAll('input[type="radio"][name="subscription_option"]') : null;


    function toggleSubscriptionOptions() {
        const isChecked = subscriptionCheckbox && subscriptionCheckbox.checked;
        
        if (subscriptionOptionWrapper) {
            subscriptionOptionWrapper.style.display = isChecked ? 'block' : 'none';
            if (!isChecked && subscriptionOptionRadios) {
                subscriptionOptionRadios.forEach(radio => {
                    radio.checked = false;
                });
            }
        }
    }

    // Subscription option radio buttons work independently - no automatic updates to weekly instalment

    // Handle subscription checkbox change
    if (subscriptionCheckbox) {
        toggleSubscriptionOptions();
        subscriptionCheckbox.addEventListener('change', function() {
            toggleSubscriptionOptions();
        });
    }

    // Handle initial state if subscription is already checked
    if (subscriptionCheckbox && subscriptionCheckbox.checked) {
        toggleSubscriptionOptions();
    }
});
