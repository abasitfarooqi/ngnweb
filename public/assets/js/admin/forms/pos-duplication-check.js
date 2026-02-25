$.ajax({
    url: '/ngn-admin/club-member-purchase/fetch/pos-invoice', // Ensure this matches the route
    type: 'GET', // Ensure it's a GET request
    success: function(data) {
        console.log(data); // Handle the response data as needed
    },
    error: function(xhr) {
        console.log('Error:', xhr.status, xhr.statusText);
    }
});

$(document).ready(function() {
    // Helper to get Backpack field input by name
    function getCrudFieldInput(name) {
        return $(`[name='${name}']`).not('[type=hidden]');
    }

    // Add AUTO checkbox for discount calculation if not present
    var discountFieldWrapper = getCrudFieldInput('discount').closest('[bp-field-wrapper]');
    if (discountFieldWrapper.find('.auto-discount-checkbox').length === 0) {
        discountFieldWrapper.append(`
            <div class="form-check mt-2">
                <input class="form-check-input auto-discount-checkbox" type="checkbox" id="auto-discount-calc" checked>
                <label class="form-check-label" for="auto-discount-calc">AUTO calculate discount</label>
            </div>
        `);
    }

    function calculateDiscount() {
        var percent = parseFloat(getCrudFieldInput('percent').val());
        var total = parseFloat(getCrudFieldInput('total').val());
        if (!isNaN(percent) && !isNaN(total)) {
            var discount = (percent / 100) * total;
            getCrudFieldInput('discount').val(discount.toFixed(2));
        }
    }

    // Listen for changes on percent and total fields
    getCrudFieldInput('percent').on('input', function() {
        if ($('#auto-discount-calc').is(':checked')) {
            calculateDiscount();
        }
    });
    getCrudFieldInput('total').on('input', function() {
        if ($('#auto-discount-calc').is(':checked')) {
            calculateDiscount();
        }
    });

    // Listen for changes on the AUTO checkbox
    $(document).on('change', '#auto-discount-calc', function() {
        if ($(this).is(':checked')) {
            calculateDiscount();
            getCrudFieldInput('discount').prop('readonly', true);
        } else {
            getCrudFieldInput('discount').prop('readonly', false);
        }
    });

    // Set initial readonly state
    if ($('#auto-discount-calc').is(':checked')) {
        getCrudFieldInput('discount').prop('readonly', true);
    }
});