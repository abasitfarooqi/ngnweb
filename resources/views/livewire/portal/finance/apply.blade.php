{{--
    Portal finance apply: mount() redirects to finance browse + enquiry panel.
    Legacy wizard markup is preserved in apply-legacy-inactive.blade.php (never included — see @includeWhen below).
--}}
@includeWhen(false, 'livewire.portal.finance.apply-legacy-inactive')
