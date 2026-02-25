// === Prefill checklist services in repeatables (non-destructive; runs beside your logger) ===
(function () {
    const WRAPPER_SELECTOR = '[data-init-function="bpFieldInitChecklist"][bp-field-type="checklist"]';
  
    function getSelectedFromHidden(wrapper) {
      const hidden = wrapper.querySelector('input[type="hidden"][name*="[services]"]');
      if (!hidden) return null;
  
      let arr;
      try { arr = JSON.parse(hidden.value || '[]'); } catch { return null; }
      if (!Array.isArray(arr) || !arr.length) return null;
  
      return {
        ids: arr.map(x => Number(x.id)).filter(n => !Number.isNaN(n)),
        names: arr.map(x => (x.name || '').trim()).filter(Boolean),
        updateId:
          (arr[0] && arr[0].pivot && arr[0].pivot.update_id) ||
          wrapper.getAttribute('bp-field-name') ||
          'unknown'
      };
    }
  
    function findInputs(wrapper) {
      // Primary: Backpack checklist uses this attribute
      let inputs = wrapper.querySelectorAll('input[type="checkbox"][data-repeatable-input-name="services"]');
  
      // Fallback: by exact name derived from bp-field-name="updates.N.services"
      if (!inputs.length) {
        const bpf = wrapper.getAttribute('bp-field-name'); // e.g. "updates.0.services"
        const m = bpf && bpf.match(/^updates\.(\d+)\.services$/);
        if (m) {
          inputs = wrapper.querySelectorAll(`input[type="checkbox"][name="updates[${m[1]}][services][]"]`);
        }
      }
      return inputs;
    }
  
    function applyChecks(wrapper) {
      const data = getSelectedFromHidden(wrapper);
      if (!data) return;
  
      const { ids, names, updateId } = data;
      const inputs = findInputs(wrapper);
      if (!inputs.length) return; // not rendered yet; MutationObserver will retry later
  
      // clear first (handles re-renders)
      inputs.forEach(i => (i.checked = false));
  
      // 1) try by value
      let checked = 0;
      inputs.forEach(chk => {
        const v = Number(chk.value);
        if (ids.includes(v)) {
          chk.checked = true;
          chk.dispatchEvent(new Event('change', { bubbles: true }));
          checked++;
          console.log(`[prefill] (value) update ${updateId} -> service_id ${v} checked`);
        }
      });
  
      // 2) fallback by label text if values don’t match
      if (checked === 0 && names.length) {
        inputs.forEach(chk => {
          const lbl = chk.closest('label');
          const text = (lbl ? lbl.textContent : chk.parentElement?.textContent || '').trim();
          if (text && names.some(n => text.includes(n))) {
            chk.checked = true;
            chk.dispatchEvent(new Event('change', { bubbles: true }));
            checked++;
            console.log(`[prefill] (label) update ${updateId} -> "${text}" checked`);
          }
        });
      }
  
      if (checked === 0) {
        console.warn(`[prefill] No matches found to check for update ${updateId}. IDs=`, ids, ' Names=', names);
      }
    }
  
    function runAll() {
      document.querySelectorAll(WRAPPER_SELECTOR).forEach(applyChecks);
    }
  
    // Run after full load (Backpack often finishes init after DOMContentLoaded)
    window.addEventListener('load', () => setTimeout(runAll, 0));
    // Also run shortly after DOMContentLoaded just in case
    document.addEventListener('DOMContentLoaded', () => setTimeout(runAll, 50));
  
    // Re-apply whenever Backpack/Repeatable adds or re-renders rows
    const mo = new MutationObserver(muts => {
      for (const m of muts) {
        if (m.type !== 'childList') continue;
  
        // If a checklist wrapper itself appears
        m.addedNodes.forEach(node => {
          if (!(node instanceof Element)) return;
  
          if (node.matches && node.matches(WRAPPER_SELECTOR)) {
            applyChecks(node);
            return;
          }
          // Or if a wrapper exists somewhere inside the added subtree
          const inner = node.querySelector && node.querySelector(WRAPPER_SELECTOR);
          if (inner) applyChecks(inner);
  
          // Or if checkbox inputs for services get injected later inside an existing wrapper
          const anyServiceInput = node.querySelector && node.querySelector('input[type="checkbox"][data-repeatable-input-name="services"]');
          if (anyServiceInput) {
            const wrapper = anyServiceInput.closest(WRAPPER_SELECTOR);
            if (wrapper) applyChecks(wrapper);
          }
        });
      }
    });
    mo.observe(document.body, { childList: true, subtree: true });


    document.addEventListener('click', function(e) {
      if (!e.target.classList.contains('toggle-services-btn')) return;
  
      // get the closest parent wrapper of this button
      const wrapper = e.target.closest('[bp-field-wrapper]');
      if (!wrapper) return;
  
      // the checklist field is the next sibling with bp-field-type="checklist"
      let checklist = wrapper.nextElementSibling;
      while (checklist && checklist.getAttribute('bp-field-type') !== 'checklist') {
          checklist = checklist.nextElementSibling;
      }
      if (!checklist) return;
  
      // Ensure we always toggle properly, even if initially visible
      if (checklist.classList.contains('d-none') || getComputedStyle(checklist).display === 'none') {
          checklist.classList.remove('d-none');
          checklist.style.display = '';
      } else {
          checklist.classList.add('d-none');
          checklist.style.display = 'none';
      }
  });
  
  })();
