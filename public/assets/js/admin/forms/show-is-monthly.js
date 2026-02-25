// public/assets/js/admin/forms/show-is-monthly.js
document.addEventListener('DOMContentLoaded', function () {
  const contractWrapper = document.querySelector('.form-group[bp-field-name="contract_type"]');
  const isMonthlyWrap   = document.querySelector('.form-group[bp-field-name="is_monthly"]');
  const monthlyCheckbox = isMonthlyWrap.querySelector('input[type="checkbox"]');
  const hiddenField     = isMonthlyWrap.querySelector('input[type="hidden"][name="is_monthly"]');

  function toggleMonthlyField() {
    const checked = contractWrapper.querySelector('.form-check-input:checked');
    if (checked && (checked.value === 'is_new' || checked.value === 'is_used')) {
      isMonthlyWrap.style.display = '';
    } else {
      isMonthlyWrap.style.display = 'none';
      if (monthlyCheckbox.checked) {
        monthlyCheckbox.checked = false;
      }
      if (hiddenField) {
        hiddenField.value = 0;
      }
    }
  }
  contractWrapper.addEventListener('change', toggleMonthlyField);
  toggleMonthlyField();
});
