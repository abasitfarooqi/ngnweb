<script>
(function () {
    function signingPage() {
        return document.body && document.body.classList.contains('agreement-signing-page');
    }

    function effectiveTheme() {
        var stored = localStorage.getItem('ngn-theme');
        if (stored === 'dark' || stored === 'light') {
            return stored;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        if (!signingPage()) {
            return;
        }

        if (theme === 'dark' || theme === 'light') {
            document.body.setAttribute('data-theme', theme);
        } else {
            document.body.removeAttribute('data-theme');
        }
    }

    window.ngnSigningSetTheme = function (mode) {
        if (mode !== 'dark' && mode !== 'light') {
            return;
        }

        localStorage.setItem('ngn-theme', mode);
        applyTheme(mode);
    };

    window.ngnSigningToggleTheme = function () {
        var next = effectiveTheme() === 'dark' ? 'light' : 'dark';
        window.ngnSigningSetTheme(next);
    };

    document.addEventListener('DOMContentLoaded', function () {
        var stored = localStorage.getItem('ngn-theme');
        if (stored === 'dark' || stored === 'light') {
            applyTheme(stored);
        }

        var toggle = document.getElementById('agreement-theme-toggle');
        if (toggle) {
            toggle.addEventListener('click', window.ngnSigningToggleTheme);
        }
    });
})();
</script>
