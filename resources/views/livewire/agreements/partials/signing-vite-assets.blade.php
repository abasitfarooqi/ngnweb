@include('components.ngn-signing-assets')
<script>
(function () {
    var key = 'ngn-agreement-theme';
    var siteKey = 'ngn-theme';
    var saved = null;
    try { saved = localStorage.getItem(key) || localStorage.getItem(siteKey); } catch (e) {}
    if (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        saved = 'dark';
    }
    var theme = saved === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-agreement-theme', theme);
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.body || !document.body.classList.contains('agreement-signing-page')) {
            return;
        }
        var current = document.documentElement.getAttribute('data-agreement-theme') === 'dark' ? 'dark' : 'light';
        document.body.setAttribute('data-theme', current);
        document.body.setAttribute('data-agreement-theme', current);
    });
})();
</script>
