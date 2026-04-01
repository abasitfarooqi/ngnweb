{{-- Run before @fluxAppearance: keep ngn-theme and flux.appearance aligned so theme does not reset on navigation --}}
<script>
(function () {
    var ngn = localStorage.getItem('ngn-theme');
    var fa = localStorage.getItem('flux.appearance');
    if (ngn === 'dark' || ngn === 'light') {
        localStorage.setItem('flux.appearance', ngn);
    } else if (fa === 'dark' || fa === 'light') {
        localStorage.setItem('ngn-theme', fa);
    }
    var effective = localStorage.getItem('ngn-theme');
    if (effective === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (effective === 'light') {
        document.documentElement.classList.remove('dark');
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
})();
</script>
