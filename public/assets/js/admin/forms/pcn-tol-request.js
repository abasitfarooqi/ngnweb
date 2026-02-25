function renderPdfButtons() {
    document.querySelectorAll('[data-repeatable-identifier="updates"]').forEach(function (row) {
        var hiddenInput = row.querySelector('input[name^="updates"][name$="[id]"]');
        var placeholder = row.querySelector('.pdf-button-placeholder');

        if (hiddenInput && placeholder) {
            var updateId = hiddenInput.value;
            if (updateId) {
                placeholder.innerHTML =
                    '<a href="/ngn-admin/pcn-tol-request/create?update_id=' +
                    updateId +
                    '" target="_blank" class="btn btn-sm btn-primary">' +
                    '<i class="la la-file-pdf"></i> PDF</a>';
            } else {
                placeholder.innerHTML = ''; // empty if no ID yet
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Initial render
    renderPdfButtons();

    // Re-render when a new repeatable row is added
    document.addEventListener('click', function (e) {
        if (e.target.closest('.add-repeatable-element-button')) {
            // Delay so Backpack inserts the new row before rendering
            setTimeout(renderPdfButtons, 100);
        }
    });
});
