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
})();
</script>
