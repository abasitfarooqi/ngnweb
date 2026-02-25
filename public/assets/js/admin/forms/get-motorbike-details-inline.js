document.addEventListener('DOMContentLoaded', function () {
    // === Motorbike fields ===
    const bikeWrapper   = document.querySelector('.form-group[bp-field-name="motorbike_id"]');
    const regWrapper    = document.querySelector('.form-group[bp-field-name="registration_number"]');
    const vinWrapper    = document.querySelector('.form-group[bp-field-name="vin"]');
    const makeWrapper   = document.querySelector('.form-group[bp-field-name="make"]');
    const modelWrapper  = document.querySelector('.form-group[bp-field-name="model"]');
    const yearWrapper   = document.querySelector('.form-group[bp-field-name="year"]');

    const bikeSelect   = bikeWrapper.querySelector('select');
    const regInput     = regWrapper.querySelector('input');
    const vinInput     = vinWrapper.querySelector('input');
    const makeInput    = makeWrapper.querySelector('input');
    const modelInput   = modelWrapper.querySelector('input');
    const yearInput    = yearWrapper.querySelector('input');

    function fillMotorbikeFields() {
        const renderedSpan = bikeWrapper.querySelector('.select2-selection__rendered');
        if (!renderedSpan) return;

        const text = renderedSpan.title || renderedSpan.textContent;
        if (text && text.includes('|')) {
            const parts = text.split('|').map(s => s.trim());
            regInput.value    = parts[0] || '';
            makeInput.value   = parts[1] ? parts[1].split(' ')[0] : '';
            modelInput.value  = parts[1] ? parts[1].split(' ').slice(1).join(' ') : '';
            yearInput.value   = parts[2] || '';
            vinInput.value    = parts[3] || '';
        } else {
            regInput.value   = '';
            vinInput.value   = '';
            makeInput.value  = '';
            modelInput.value = '';
            yearInput.value  = '';
        }
    }

    // Trigger on Select2 change / unselect / clear
    $(bikeSelect).on('select2:select select2:unselect', fillMotorbikeFields);
    $(bikeSelect).on('select2:clear', function () {
        regInput.value   = '';
        vinInput.value   = '';
        makeInput.value  = '';
        modelInput.value = '';
        yearInput.value  = '';
    });

    // Typing manually clears Select2
    [regInput, vinInput, makeInput, modelInput, yearInput].forEach(input => {
        input.addEventListener('input', function () {
            if (bikeSelect.value) $(bikeSelect).val(null).trigger('change.select2');
        });
    });

    // MutationObserver to handle rendered span immediately
    const bikeObserver = new MutationObserver(fillMotorbikeFields);
    bikeObserver.observe(bikeWrapper, { childList: true, subtree: true });

    // Initialise on page load
    fillMotorbikeFields();
});
