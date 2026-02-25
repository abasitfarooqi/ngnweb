function toggleStoreTransferFields() {
    const transactionType = $('[name="transaction_type"]').val();
    const fromBranchField = $('[bp-field-name="from_branch_id"]');
    const toBranchField = $('[bp-field-name="to_branch_id"]');
    const mainBranchField = $('[bp-field-name="branch_id"]');
    const stockInField = $('[bp-field-name="in"]');
    const stockOutField = $('[bp-field-name="out"]');
    const transferQtyField = $('[bp-field-name="transfer_qty"]');

    // Show or hide fields based on transaction type
    if (transactionType === 'stock_transfer') {
        fromBranchField.removeClass('d-none');   // Show the 'From Branch' field
        toBranchField.removeClass('d-none');     // Show the 'To Branch' field
        transferQtyField.removeClass('d-none');  // Show Transfer Quantity field

        stockInField.addClass('d-none');         // Hide Stock IN field
        stockOutField.addClass('d-none');        // Hide Stock OUT field
        mainBranchField.addClass('d-none');      // Hide the main branch field

        // Clear the values of the hidden fields
        fromBranchField.val(''); // Clear 'From Branch' value
        toBranchField.val('');   // Clear 'To Branch' value
    } else {
        fromBranchField.addClass('d-none');      // Hide the 'From Branch' field
        toBranchField.addClass('d-none');        // Hide the 'To Branch' field
        transferQtyField.addClass('d-none');     // Hide Transfer Quantity field

        stockInField.removeClass('d-none');      // Show Stock IN field
        stockOutField.removeClass('d-none');     // Show Stock OUT field
        mainBranchField.removeClass('d-none');   // Show the main branch field
    }
}

$(document).ready(function() {
    toggleStoreTransferFields(); // Call function on page load to handle pre-selected values
    $(document).on('change', '[name="transaction_type"]', toggleStoreTransferFields); // Bind to change event
});
