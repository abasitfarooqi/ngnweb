// public/assets/js/admin/forms/repeatable-services-serializer.js
(function () {
  'use strict';

  /**
   * Find the hidden repeatable input (name="updates") and merge
   * checklist selections (per-row) into the JSON value before submit.
   *
   * Works with Backpack's repeatable + checklist inside each row.
   */

  function getRepeatableInput() {
    return document.querySelector('input[name="updates"]');
  }

  function parseUpdates(value) {
    if (!value) return [];
    try {
      return JSON.parse(value);
    } catch (e) {
      // If it's already an array (rare) or invalid JSON, return empty array
      return Array.isArray(value) ? value : [];
    }
  }

  function serializeUpdates(updates) {
    try {
      return JSON.stringify(updates);
    } catch (e) {
      return '[]';
    }
  }

  function collectServicesForIndex(index) {
    // Primary selector used by Backpack repeatable checklist inside a repeatable row
    // (your existing script used data-repeatable-input-name="services")
    const selector1 = `input[type="checkbox"][data-repeatable-input-name="services"]`;
    // Fallback selector for inputs whose name is updates[<index>][services][] (classic)
    const selector2 = `input[type="checkbox"][name^="updates[${index}][services]"]`;

    const checked = new Set();

    // First try the data-repeatable-input-name style within the wrapper with bp-field-name
    document.querySelectorAll(selector1).forEach(function (el) {
      // Find ancestor that indicates this checkbox belongs to index
      const bp = el.closest('[bp-field-name]');
      if (!bp) return;
      const bpf = bp.getAttribute('bp-field-name') || '';
      const m = bpf.match(/^updates\.(\d+)\.services$/);
      if (!m) return;
      if (parseInt(m[1], 10) === index && el.checked) {
        const v = el.value;
        if (v !== undefined && v !== '') checked.add(Number(v));
      }
    });

    // Next, fallback to inputs with name updates[index][services][]
    document.querySelectorAll(selector2).forEach(function (el) {
      if (el.checked) {
        const v = el.value;
        if (v !== undefined && v !== '') checked.add(Number(v));
      }
    });

    return Array.from(checked);
  }

  function syncChecklistIntoRepeatableJson() {
    const input = getRepeatableInput();
    if (!input) return;

    const updates = parseUpdates(input.value);

    // collect all checklist wrappers, extract their bp-field-name to find index
    const checklists = document.querySelectorAll('[data-init-function="bpFieldInitChecklist"][bp-field-type="checklist"]');

    // If checklists exist, use bp-field-name to locate the index.
    // If not, fallback to iterating updates indexes and reading by name selector.
    if (checklists && checklists.length) {
      checklists.forEach(function (wrapper) {
        const bpf = wrapper.getAttribute('bp-field-name') || ''; // e.g. "updates.0.services"
        const m = bpf.match(/^updates\.(\d+)\.services$/);
        if (!m) return;
        const idx = parseInt(m[1], 10);
        // Ensure updates array has placeholder at idx
        while (updates.length <= idx) updates.push({});
        updates[idx].services = collectServicesForIndex(idx);
      });
    } else {
      // Fallback: iterate indexes from 0..updates.length-1
      for (let i = 0; i < updates.length; i++) {
        updates[i].services = collectServicesForIndex(i);
      }
    }

    input.value = serializeUpdates(updates);
  }

  // Run on form submit (capture phase to run before other handlers).
  document.addEventListener('submit', function (ev) {
    // Only act on Backpack CRUD form
    const form = ev.target;
    if (!form || !form.matches || !form.matches('form')) return;
    // quick guard: only run if the form has an updates input
    if (!getRepeatableInput()) return;
    syncChecklistIntoRepeatableJson();
    // proceed (synchronous)
  }, true);

  // Also keep JSON updated if user toggles checkboxes (helps client-side previews)
  document.addEventListener('change', function (ev) {
    const target = ev.target;
    if (!target) return;
    if (target.matches('input[type="checkbox"][data-repeatable-input-name="services"], input[type="checkbox"][name^="updates["]')) {
      // Slight debounce not necessary, but keep it simple
      syncChecklistIntoRepeatableJson();
    }
  });

  // On page load ensure hidden JSON contains current selections (useful for edit pages)
  window.addEventListener('load', function () {
    if (getRepeatableInput()) {
      syncChecklistIntoRepeatableJson();
    }
  });
})();
