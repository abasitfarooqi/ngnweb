$.ajax({
    url: '/ngn-admin/ngn-product-management/fetch/brand',
    type: 'GET', // Ensure it's a GET request
    success: function(data) {
        console.log(data);
    },
    error: function(xhr) {
        console.log('Error:', xhr.status, xhr.statusText);
    }
});
