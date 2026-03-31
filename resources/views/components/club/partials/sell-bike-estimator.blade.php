@props(['formId' => 'clubdash_sell_form'])

@php $yMax = (int) date('Y'); @endphp

<div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Sell your motorbike — instant estimate</h3>
    <form id="{{ $formId }}" method="POST" action="{{ route('vehicle.estimate') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 text-center">Vehicle registration number</label>
            <div class="flex justify-center">
                <div class="club-uk-vrm-container">
                    <input type="text" name="vrm" id="{{ $formId }}_vrm" class="club-uk-vrm-input" required maxlength="12" autocomplete="off" />
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Model</flux:label>
                <flux:input name="model" id="{{ $formId }}_model" class="uppercase" required pattern="[A-Za-z0-9/ -]*" />
            </flux:field>
            <flux:field>
                <flux:label>Make</flux:label>
                <flux:input name="make" id="{{ $formId }}_make" class="uppercase" required pattern="[A-Za-z0-9/ -]*" />
            </flux:field>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Year</flux:label>
                <flux:select name="vehicle_year" id="{{ $formId }}_year" required>
                    <option value="">Select year</option>
                    @for ($year = $yMax; $year >= 2014; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Engine (CC)</flux:label>
                <flux:input type="number" name="engine_size" id="{{ $formId }}_engine" min="0" step="1" required />
            </flux:field>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Mileage</flux:label>
                <flux:input type="number" name="mileage" id="{{ $formId }}_mileage" min="0" step="1" required />
            </flux:field>
            <flux:field>
                <flux:label>New vehicle price (£)</flux:label>
                <flux:input type="number" name="base_price" id="{{ $formId }}_base_price" min="0" step="1" required />
            </flux:field>
        </div>
        <flux:field>
            <flux:label>Condition (1–10): <span id="{{ $formId }}_cond_label" class="text-brand-red font-bold">5</span></flux:label>
            <input type="range" name="condition" id="{{ $formId }}_condition" min="1" max="10" step="1" value="5"
                class="w-full accent-brand-red" />
        </flux:field>
        <div class="text-center">
            <flux:button type="submit" variant="filled" class="bg-brand-red text-white">Get estimate</flux:button>
        </div>
    </form>

    <div id="{{ $formId }}_result" class="mt-6 hidden border-t border-gray-200 dark:border-gray-700 pt-6">
        <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-2">Estimated value</p>
        <p class="text-center text-3xl font-black text-brand-red">£<span id="{{ $formId }}_value">0.00</span></p>
        <input type="hidden" id="{{ $formId }}_record_id" value="" />
        <p class="text-center text-sm text-gray-500 mt-4">How do you feel about this estimate?</p>
        <div class="flex justify-center gap-4 mt-2">
            <button type="button" class="{{ $formId }}_fb px-4 py-2 border border-green-600 text-green-700 hover:bg-green-50 dark:hover:bg-green-900/20" data-like="1" aria-label="Thumbs up">👍</button>
            <button type="button" class="{{ $formId }}_fb px-4 py-2 border border-red-600 text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20" data-like="0" aria-label="Thumbs down">👎</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
  const formId = @json($formId);
  const form = document.getElementById(formId);
  if (!form) return;
  const vrm = document.getElementById(formId + '_vrm');
  const make = document.getElementById(formId + '_make');
  const model = document.getElementById(formId + '_model');
  const year = document.getElementById(formId + '_year');
  const engine = document.getElementById(formId + '_engine');
  const cond = document.getElementById(formId + '_condition');
  const condLabel = document.getElementById(formId + '_cond_label');
  const resultBox = document.getElementById(formId + '_result');
  const valueEl = document.getElementById(formId + '_value');
  const recordId = document.getElementById(formId + '_record_id');
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (cond && condLabel) {
    cond.addEventListener('input', function () { condLabel.textContent = this.value; });
  }

  function disableFields(disabled) {
    [make, model, year, engine].forEach(function (el) { if (el) el.disabled = disabled; });
  }

  if (vrm) {
    vrm.addEventListener('blur', function () {
      const reg = (this.value || '').trim();
      if (!reg || !token) return;
      disableFields(true);
      fetch(@json(url('/vrm/check-vehicle')) + '?registration_number=' + encodeURIComponent(reg), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token }
      }).then(function (r) { return r.json(); }).then(function (data) {
        if (data.success && data.data) {
          var d = data.data;
          if (d.make && make) make.value = String(d.make).toUpperCase();
          if (d.model && model) model.value = String(d.model).toUpperCase();
          if (d.yearOfManufacture && year) year.value = d.yearOfManufacture;
          if (d.engineCapacity && engine) engine.value = d.engineCapacity;
        }
      }).catch(function () {}).finally(function () { disableFields(false); });
    });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    valueEl.textContent = '…';
    resultBox.classList.remove('hidden');
    var fd = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: fd
    }).then(function (r) { return r.json(); }).then(function (data) {
      if (data.success && data.calculated_value) {
        valueEl.textContent = parseFloat(data.calculated_value).toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        recordId.value = data.record_id || '';
      } else {
        valueEl.textContent = 'Error';
        window.dispatchEvent(new CustomEvent('toast-show', { detail: { slots: { text: data.message || 'Estimation failed' }, dataset: { variant: 'danger' } } }));
      }
    }).catch(function () {
      valueEl.textContent = 'Error';
      window.dispatchEvent(new CustomEvent('toast-show', { detail: { slots: { text: 'Request failed' }, dataset: { variant: 'danger' } } }));
    });
  });

  document.querySelectorAll('.' + formId + '_fb').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var rid = recordId.value;
      if (!rid || !token) return;
      fetch(@json(route('vehicle.estimate.feedback')), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ record_id: parseInt(rid, 10), like: this.getAttribute('data-like') === '1' })
      }).then(function (r) { return r.json(); }).then(function (d) {
        if (!d.success) {
          window.dispatchEvent(new CustomEvent('toast-show', { detail: { slots: { text: d.message || 'Could not save feedback' }, dataset: { variant: 'warning' } } }));
        }
      }).catch(function () {
        window.dispatchEvent(new CustomEvent('toast-show', { detail: { slots: { text: 'Could not save feedback' }, dataset: { variant: 'danger' } } }));
      });
    });
  });
})();
</script>
@endpush
