$(document).ready(function() {
    // Listen for the form submission
    $('form').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Perform AJAX request to recalculate
        $.ajax({
            url: this.action,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                // Update the fields with the new values
                $('#total_cost').val(data.total_cost);
                $('#distance').val(data.distance);
                console.log('Fields updated:', data.total_cost, data.distance); // Debugging log
            },
            error: function(error) {
                console.error('AJAX error:', error);
            }
        });
    });
});