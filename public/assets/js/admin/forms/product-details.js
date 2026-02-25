$(document).ready(function() {
    // Function to update the stock fields based on selected product
    function updateStockFields(productId) {
        console.log("Selected Product ID:", productId); // Log the selected product ID

        if (productId) {
            $.ajax({
                url: '/ngn-admin/ngn-stock-handler/fetch-product-data', // Use relative path
                type: 'GET',
                data: { id: productId },
                success: function(data) {
                    console.log("Fetched Product Data:", data); // Log the fetched data

                    // Log the stock values for Catford, Tooting, and Sutton
                    console.log("Catford Stock:", data.catford_stock);
                    console.log("Tooting Stock:", data.tooting_stock);
                    console.log("Sutton Stock:", data.sutton_stock);

                    // Set the SKU field
                    $('input[name="sku"]').val(data.sku); // Assuming SKU field has name 'sku'

                    // Set Catford, Tooting, and Sutton stock fields using standard selectors
                    $('input[name="catford_stock"]').val(data.catford_stock);
                    $('input[name="tooting_stock"]').val(data.tooting_stock);
                    $('input[name="sutton_stock"]').val(data.sutton_stock); // Set Sutton stock field
                },
                error: function(xhr) {
                    console.log("Error fetching product data:", xhr); // Log any errors
                }
            });
        } else {
            console.log("No Product ID selected."); // Log if no product ID is selected
        }
    }

    // Event listener for product selection
    $('select[name="product_id"]').change(function() {
        var productId = $(this).val();
        updateStockFields(productId); // Call the function to update stock fields
    });
});
