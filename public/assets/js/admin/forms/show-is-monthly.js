// public/assets/js/admin/forms/show-is-monthly.js
// Form uses checkbox contract flags (is_new / is_used), not a contract_type radio.
document.addEventListener('DOMContentLoaded', function () {
  const isMonthlyWrap = document.querySelector('.form-group[bp-field-name="is_monthly"]');
  if (!isMonthlyWrap) {
    return;
  }

  const monthlyCheckbox = isMonthlyWrap.querySelector('input[type="checkbox"]');
  const hiddenField = isMonthlyWrap.querySelector('input[type="hidden"][name="is_monthly"]');
  const contractWrapper = document.querySelector('.form-group[bp-field-name="contract_type"]');
  const legacyMonthlyTypes = ['is_new', 'is_used'].map(function (name) {
    return document.querySelector('.form-group[bp-field-name="' + name + '"]');
  }).filter(Boolean);

  function monthlyShouldShow() {
    if (contractWrapper) {
      const checked = contractWrapper.querySelector('.form-check-input:checked');
      return Boolean(checked && (checked.value === 'is_new' || checked.value === 'is_used'));
    }
    return legacyMonthlyTypes.some(function (wrap) {
      const cb = wrap.querySelector('input.form-check-input[type="checkbox"]');
      return cb && cb.checked;
    });
  }

  function toggleMonthlyField() {
    const show = monthlyShouldShow();
    isMonthlyWrap.classList.toggle('d-none', !show);
    if (!show) {
      if (monthlyCheckbox && monthlyCheckbox.checked) {
        monthlyCheckbox.checked = false;
      }
      if (hiddenField) {
        hiddenField.value = 0;
      }
    }
  }

  if (contractWrapper) {
    contractWrapper.addEventListener('change', toggleMonthlyField);
  } else {
    legacyMonthlyTypes.forEach(function (wrap) {
      wrap.addEventListener('change', toggleMonthlyField);
    });
    var form = document.getElementById('crudForm');
    if (form) {
      form.addEventListener('change', function (e) {
        var t = e.target;
        if (t && t.matches && t.matches('input.form-check-input[type="checkbox"]')) {
          toggleMonthlyField();
        }
      });
    }
  }

  toggleMonthlyField();
});
