@php
    $title = $title ?? 'MOTORCYCLE SALE AGREEMENT';
@endphp
<div class="agreement-brand-header-wrap">
    <div class="agreement-theme-toolbar">
        <button type="button" class="agreement-theme-toggle" id="agreementThemeToggle" aria-label="Switch light or dark mode">
            Light / Dark
        </button>
    </div>
    <div class="agreement-brand-header" role="group" aria-label="Company header">
        <div class="agreement-brand-header__logo">
            <x-agreements.theme-logo class="agreement-brand-header__logo-img w-full" />
        </div>
        <div class="agreement-brand-header__address">
            9-13 Catford Hill, <br>
            London, SE6 4NU<br>
            0203 409 5478 / 0208 314 1498<br>
            customerservice@neguinhomotors.co.uk<br>
            ngnmotors.co.uk
        </div>
        <div class="agreement-brand-header__title">
            {{ $title }}
        </div>
    </div>
</div>
<script>
(function () {
    var root = document.documentElement;
    var key = 'ngn-agreement-theme';
    var siteKey = 'ngn-theme';
    var btn = document.getElementById('agreementThemeToggle');

    function applyTheme(theme) {
        var next = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-agreement-theme', next);
        if (document.body) {
            document.body.setAttribute('data-theme', next);
            document.body.setAttribute('data-agreement-theme', next);
        }
        try {
            localStorage.setItem(key, next);
            localStorage.setItem(siteKey, next);
        } catch (e) {}
        if (btn) {
            btn.textContent = next === 'dark' ? 'Switch to light' : 'Switch to dark';
            btn.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
        }
    }

    var saved = null;
    try { saved = localStorage.getItem(key) || localStorage.getItem(siteKey); } catch (e) {}
    if (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        saved = 'dark';
    }
    applyTheme(saved || 'light');

    if (btn) {
        btn.addEventListener('click', function () {
            var current = root.getAttribute('data-agreement-theme') === 'dark' ? 'dark' : 'light';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }
})();
</script>
