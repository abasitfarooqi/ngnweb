function enableEdit(button) {
    const row = button.closest('div');
    const input = row.querySelector('.inline-stock-input');
    const saveBtn = row.querySelector('.inline-save-btn');

    console.log("Enable Edit: ", input);  // Log the input element being edited
    console.log("Input Value Before Edit: ", input.value);  // Log current value of the input field

    input.readOnly = false;
    input.focus();
    button.classList.add('d-none');
    saveBtn.classList.remove('d-none');
}

function saveStock(button) {
    const row = button.closest('div');
    const input = row.querySelector('.inline-stock-input');
    const editBtn = row.querySelector('.inline-edit-btn');
    const field = input.getAttribute('data-field');
    const id = input.getAttribute('data-id');
    const newValue = input.value;

    console.log("Save Stock: ", { field, id, newValue });  // Log the details of the stock being saved
    console.log("Input Element: ", input);  // Log the input element
    console.log("Input Value: ", input.value);  // Log the current value of the input field

    // Send AJAX request to update stock
    fetch(`/admin/ngn-stock-handler/${id}/update-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // include CSRF token
        },
        body: JSON.stringify({ field, value: newValue })
    })
    .then(response => {
        console.log("Response Status: ", response.status);  // Log the response status
        return response.json();
    })
    .then(data => {
        console.log("Response Data: ", data);  // Log the data returned from the server

        if (data.success) {
            input.readOnly = true;
            button.classList.add('d-none');
            editBtn.classList.remove('d-none');
            console.log("Stock updated successfully.");  // Log success message
        } else {
            console.log("Failed to update stock: ", data);  // Log failure message
            alert('Failed to update stock');
        }
    })
    .catch(error => {
        console.error("Error during fetch request: ", error);  // Log any errors during the request
    });
}
