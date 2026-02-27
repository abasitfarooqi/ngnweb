@extends(backpack_view('blank'))

@section('content')
<style>
  .card-header { font-weight: 600; }
  .table thead th { position: sticky; top: 0; background: #fff; z-index: 2; }
  .table td, .table th { vertical-align: middle !important; }
  .mini-input { min-width: 120px; }
  .wide-input { min-width: 180px; }
  .vin-input { min-width: 220px; }
  .actions-cell { min-width: 340px; }
  .badge-soft { background: rgba(13,110,253,.08); color: #0d6efd; border: 1px solid rgba(13,110,253,.2); }
  .muted-small { font-size: 12px; color: #6c757d; }
  .row-locked input { background: #f8f9fa; }
  .filter-hint { font-size: 12px; color: #6c757d; }
  .count-pill { font-size: 12px; padding: 2px 8px; border-radius: 999px; background: #f1f3f5; }
</style>

<div class="container-fluid">

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-warning mb-3">{{ session('error') }}</div>
  @endif

  @if ($errors->any())
      <div class="alert alert-danger mb-4 mt-3">
        <strong>Please fix the highlighted fields.</strong>
      </div>
    @endif

  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h2 class="mb-1">E-bike Manager</h2>
      <div class="text-muted">Create e-bikes with registration + current pricing (weekly + deposit) in one page.</div>
    </div>
    <div class="text-end">
      <span class="count-pill">Total: <span id="jsTotalCount">{{ count($ebikes) }}</span></span>
      <span class="count-pill ms-2">Showing: <span id="jsVisibleCount">{{ count($ebikes) }}</span></span>
    </div>
  </div>

  {{-- ADD NEW E-BIKE --}}
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span>Add new E-bike</span>
      <span class="badge badge-soft">Creates Bike + Registration + Current Pricing</span>
    </div>

    <div class="card-body">
  <form method="POST" action="{{ route('page.ebike_manager.store') }}">
    @csrf

    <div class="row">
      <div class="col-md-3 mb-2">
        <label class="form-label">Make</label>
        <input
          name="make"
          class="form-control @error('make') is-invalid @enderror"
          required
          value="{{ old('make') }}"
        >
        @error('make') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3 mb-2">
        <label class="form-label">Model</label>
        <input
          name="model"
          class="form-control @error('model') is-invalid @enderror"
          required
          value="{{ old('model') }}"
        >
        @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2 mb-2">
        <label class="form-label">Year</label>
        <input
          name="year"
          class="form-control @error('year') is-invalid @enderror"
          type="number"
          inputmode="numeric"
          
          value="{{ old('year') }}"
        >
        @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2 mb-2">
        <label class="form-label">Engine</label>
        <input
          name="engine"
          class="form-control @error('engine') is-invalid @enderror"
          value="{{ old('engine', 'Electric') }}"
        >
        @error('engine') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2 mb-2">
        <label class="form-label">Colour</label>
        <input
          name="color"
          class="form-control @error('color') is-invalid @enderror"
          required
          value="{{ old('color') }}"
        >
        @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 mb-2">
        <label class="form-label">Registration Number</label>
        <input
          name="registration_number"
          class="form-control @error('registration_number') is-invalid @enderror"
          required
          value="{{ old('registration_number') }}"
        >
        @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3 mb-2">
        <label class="form-label">VIN Number</label>
        <input
          name="vin_number"
          class="form-control @error('vin_number') is-invalid @enderror"
          required
          value="{{ old('vin_number') }}"
        >
        @error('vin_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2 mb-2">
        <label class="form-label">Weekly Price</label>
        <input
          name="weekly_price"
          class="form-control @error('weekly_price') is-invalid @enderror"
          type="number"
          step="0.01"
          required
          value="{{ old('weekly_price', 60) }}"
        >
        @error('weekly_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2 mb-2">
        <label class="form-label">Minimum Deposit</label>
        <input
          name="minimum_deposit"
          class="form-control @error('minimum_deposit') is-invalid @enderror"
          type="number"
          step="0.01"
          required
          value="{{ old('minimum_deposit', 200) }}"
        >
        @error('minimum_deposit') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2 mb-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Create E-bike</button>
      </div>

      <div class="d-none">
        <input
          name="vehicle_profile_id"
          type="hidden"
          value="{{ old('vehicle_profile_id', 1) }}"
          required
        >
      </div>
    </div>

    <div class="muted-small mt-2">
      Tip: E-bike will appear in “New Booking” only if it has Registration + Current Pricing.
    </div>

  
  </form>
</div>

  </div>



  {{-- LIST --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>E-bikes List (Real-time Filters)</span>
      <span class="muted-small">Default read-only. Click Edit to unlock a row.</span>
    </div>

    <div class="card-body pb-0">
      <div class="row g-2">
        <div class="col-md-4">
          <input id="fSearch" class="form-control" placeholder="Search make / model / VIN / reg">
        </div>
        <div class="col-md-3">
          <input id="fMake" class="form-control" placeholder="Make">
        </div>
        <div class="col-md-3">
          <input id="fReg" class="form-control" placeholder="Registration">
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button id="fReset" type="button" class="btn btn-outline-danger w-100">Reset</button>
        </div>
      </div>
    </div>

    <div class="card-body table-responsive">
      <table class="table table-striped align-middle" id="ebikeTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Registration</th>
            <th>VIN</th>
            <th>Make</th>
            <th>Model</th>
            <th>Year</th>
            <th>Weekly</th>
            <th>Deposit</th>
            <th class="actions-cell">Actions</th>
          </tr>
        </thead>

        <tbody>
        @foreach($ebikes as $b)
          @php
            $reg = optional($b->registrations->first())->registration_number;
            $pricing = optional($b->rentingPricings->first());
          @endphp

          <tr data-row="{{ $b->id }}"
              class="row-locked"
              data-id="{{ $b->id }}"
              data-reg="{{ strtolower($reg ?? '') }}"
              data-vin="{{ strtolower($b->vin_number ?? '') }}"
              data-make="{{ strtolower($b->make ?? '') }}"
              data-model="{{ strtolower($b->model ?? '') }}"
          >
            {{-- UPDATE FORM (only this form, no nesting) --}}
            <td>{{ $b->id }}</td>

            <td class="wide-input">
              <form id="updateForm{{ $b->id }}" method="POST" action="{{ route('page.ebike_manager.update', ['id' => $b->id]) }}">
                @csrf
                <input name="registration_number" class="form-control js-lock"
                       value="{{ $reg }}" required disabled>
            </td>

            <td class="vin-input">
                <input name="vin_number" class="form-control js-lock"
                       value="{{ $b->vin_number }}" required disabled>
            </td>

            <td class="wide-input">
                <input name="make" class="form-control js-lock"
                       value="{{ $b->make }}" required disabled>
            </td>

            <td class="wide-input">
                <input name="model" class="form-control js-lock"
                       value="{{ $b->model }}" required disabled>
            </td>

            <td class="mini-input">
                <input name="year" class="form-control js-lock"
                       type="number" value="{{ $b->year }}" disabled>
            </td>

            <input type="hidden" name="engine" value="{{ $b->engine ?? 'Electric' }}">
            <input type="hidden" name="color" value="{{ $b->color }}">
            <input type="hidden" name="vehicle_profile_id" value="{{ $b->vehicle_profile_id }}">
            <td class="mini-input">
                <input name="weekly_price" class="form-control js-lock"
                       type="number" step="0.01"
                       value="{{ $pricing?->weekly_price ?? 60 }}" required disabled>
            </td>

            <td class="mini-input">
                <input name="minimum_deposit" class="form-control js-lock"
                       type="number" step="0.01"
                       value="{{ $pricing?->minimum_deposit ?? 200 }}" required disabled>
              </form>
            </td>

            <td class="actions-cell">
              <button type="button" class="btn btn-sm btn-outline-primary js-edit">Edit</button>

              <button type="submit" form="updateForm{{ $b->id }}" class="btn btn-sm btn-outline-success js-save d-none">
                Save
              </button>

              <button type="button" class="btn btn-sm btn-outline-secondary js-cancel d-none">
                Cancel
              </button>

              {{-- DELETE FORM (separate, not nested) --}}
              <form method="POST"
                    action="{{ route('page.ebike_manager.delete', ['id' => $b->id]) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete this e-bike?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>

      <div id="jsNoResults" class="alert alert-info d-none mt-3">
        No e-bikes match your filters.
      </div>
    </div>
  </div>

</div>
@endsection

@push('after_scripts')
<script>
(function(){
  // ===== Unlock/Lock row editing =====
  document.addEventListener('click', function (e) {
    const row = e.target.closest('tr[data-row]');
    if (!row) return;

    const inputs = row.querySelectorAll('.js-lock');
    const editBtn = row.querySelector('.js-edit');
    const saveBtn = row.querySelector('.js-save');
    const cancelBtn = row.querySelector('.js-cancel');

    if (e.target.classList.contains('js-edit')) {
      inputs.forEach(i => i.disabled = false);
      row.classList.remove('row-locked');
      editBtn.classList.add('d-none');
      saveBtn.classList.remove('d-none');
      cancelBtn.classList.remove('d-none');

      // store original values for cancel
      inputs.forEach(i => i.dataset.original = i.value);
    }

    if (e.target.classList.contains('js-cancel')) {
      inputs.forEach(i => {
        if (i.dataset.original !== undefined) i.value = i.dataset.original;
        i.disabled = true;
      });
      row.classList.add('row-locked');
      editBtn.classList.remove('d-none');
      saveBtn.classList.add('d-none');
      cancelBtn.classList.add('d-none');
    }
  });

  // ===== Real-time filtering (client-side) =====
  const fSearch = document.getElementById('fSearch');
  const fMake = document.getElementById('fMake');
  const fReg = document.getElementById('fReg');
  const fReset = document.getElementById('fReset');

  const table = document.getElementById('ebikeTable');
  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const totalCount = document.getElementById('jsTotalCount');
  const visibleCount = document.getElementById('jsVisibleCount');
  const noResults = document.getElementById('jsNoResults');

  function normalise(s){ return (s || '').toString().trim().toLowerCase(); }

  function applyFilters(){
    const s = normalise(fSearch.value);
    const m = normalise(fMake.value);
    const r = normalise(fReg.value);

    let visible = 0;

    rows.forEach(row => {
      const make = row.dataset.make || '';
      const model = row.dataset.model || '';
      const vin = row.dataset.vin || '';
      const reg = row.dataset.reg || '';

      const matchesMake = !m || make.includes(m);
      const matchesReg = !r || reg.includes(r);

      const haystack = (make + ' ' + model + ' ' + vin + ' ' + reg);
      const matchesSearch = !s || haystack.includes(s);

      const show = matchesMake && matchesReg && matchesSearch;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    visibleCount.textContent = visible;
    noResults.classList.toggle('d-none', visible !== 0);
  }

  // Debounce for smoother typing
  function debounce(fn, wait){
    let t;
    return function(){
      clearTimeout(t);
      t = setTimeout(fn, wait);
    };
  }

  const live = debounce(applyFilters, 120);

  [fSearch, fMake, fReg].forEach(el => el.addEventListener('input', live));

  fReset.addEventListener('click', function(){
    fSearch.value = '';
    fMake.value = '';
    fReg.value = '';
    applyFilters();
  });

  // init counts
  totalCount.textContent = rows.length;
  applyFilters();
})();
</script>
@endpush
