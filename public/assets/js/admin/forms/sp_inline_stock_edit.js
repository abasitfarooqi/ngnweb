function spEnableStockEdit(button) {
    const row = button.closest('div');
    const input = row.querySelector('.sp-inline-stock-input');
    const saveBtn = row.querySelector('.sp-inline-save-btn');

    input.readOnly = false;
    input.focus();
    button.classList.add('d-none');
    saveBtn.classList.remove('d-none');
}

function spSaveStock(button) {
    const row = button.closest('div');
    const input = row.querySelector('.sp-inline-stock-input');
    const editBtn = row.querySelector('.sp-inline-edit-btn');
    const field = input.getAttribute('data-field');
    const id = input.getAttribute('data-id');
    const newValue = input.value;

    fetch('/admin/sp-stock-handler/' + id + '/update-stock', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ field, value: newValue }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                input.readOnly = true;
                button.classList.add('d-none');
                editBtn.classList.remove('d-none');
            } else {
                alert('Failed to update stock');
            }
        })
        .catch(() => {
            alert('Failed to update stock');
        });
}
