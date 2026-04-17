{{-- After @fluxAppearance: Flux must exist. Ensures toggling light mode cannot be overwritten silently. --}}
<script>
(function () {
    window.ngnSetColourMode = function (mode) {
        if (mode !== 'dark' && mode !== 'light') {
            return;
        }
        localStorage.setItem('ngn-theme', mode);
        if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
            try {
                window.Flux.applyAppearance(mode);
            } catch (e) {}
        }
        document.documentElement.classList.toggle('dark', mode === 'dark');
    };

    {{-- Flux initialises from flux.appearance || system; ngn-theme is the product source of truth when set. Re-apply once so html.dark and Flux never disagree after load. --}}
    var ngn = localStorage.getItem('ngn-theme');
    if (ngn === 'dark' || ngn === 'light') {
        if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
            try {
                window.Flux.applyAppearance(ngn);
            } catch (e) {}
        }
        document.documentElement.classList.toggle('dark', ngn === 'dark');
    }
})();
</script>
