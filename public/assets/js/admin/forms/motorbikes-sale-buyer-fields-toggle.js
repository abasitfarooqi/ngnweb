// Toggle buyer fields visibility when "Is Sold" is checked (Motorbikes Sale CRUD)
// Backpack uses a hidden input name="is_sold" plus a visible checkbox; we use the checkbox for state and events.
// Validation is done by Backpack/Laravel Request (MotorbikesSaleRequest) when Is Sold is checked.
document.addEventListener('DOMContentLoaded', function () {
  function getIsSoldCheckbox() {
    var hidden = document.querySelector('input[name="is_sold"][type="hidden"]');
    if (!hidden) return null;
    var wrapper = hidden.closest('.form-check');
    if (!wrapper) return null;
    return wrapper.querySelector('input[type="checkbox"]');
  }

  function toggleBuyerFields() {
    var checkbox = getIsSoldCheckbox();
    var isChecked = checkbox ? checkbox.checked : false;
    document.querySelectorAll('.buyer-fields-wrapper').forEach(function (el) {
      el.style.display = isChecked ? 'block' : 'none';
    });
  }

  toggleBuyerFields();

  var checkbox = getIsSoldCheckbox();
  if (checkbox) {
    checkbox.addEventListener('change', toggleBuyerFields);
  }
});
