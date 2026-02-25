function updateLogBookSentValue() {
    const logBookSentHiddenInput = $('input[name="log_book_sent"]'); // The hidden input field
    const logbookTransferDateField = $('[bp-field-name="logbook_transfer_date"]'); // The field to show/hide

    // Log the current value of the hidden input
    console.log('Hidden input value:', logBookSentHiddenInput.val());

    // Check the hidden input value and toggle the d-none class based on the value
    if (logBookSentHiddenInput.val() === '1') {
        logbookTransferDateField.removeClass('d-none'); // Show the field if value is 1
        console.log('logbook_transfer_date field is now visible');
    } else {
        logbookTransferDateField.addClass('d-none'); // Hide the field if value is 0
        console.log('logbook_transfer_date field is now hidden');
    }
}

$(document).ready(function() {
    // Initial call to handle the visibility of the field based on the hidden input value
    updateLogBookSentValue();

    // Listen for changes to the hidden input or checkbox state if needed and re-check the value
    $(document).on('change', 'input[name="log_book_sent"]', updateLogBookSentValue);
});
