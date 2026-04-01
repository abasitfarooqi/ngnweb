import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import persist from '@alpinejs/persist';

Alpine.plugin(focus);
Alpine.plugin(intersect);
Alpine.plugin(persist);

Alpine.data('homeRentalCarousel', (slideCount) => ({
    slideCount,
    index: 0,
    get atStart() {
        return this.index <= 0;
    },
    get atEnd() {
        return this.index >= this.slideCount - 1;
    },
    onScroll() {
        const el = this.$refs.viewport;
        if (!el) return;
        const w = el.clientWidth || 1;
        this.index = Math.min(this.slideCount - 1, Math.max(0, Math.round(el.scrollLeft / w)));
    },
    step(delta) {
        this.goTo(Math.min(this.slideCount - 1, Math.max(0, this.index + delta)));
    },
    goTo(i) {
        const el = this.$refs.viewport;
        if (!el) return;
        const w = el.clientWidth;
        el.scrollTo({ left: i * w, behavior: 'smooth' });
        this.index = i;
    },
}));

window.Alpine = Alpine;

// ngnSetColourMode: see resources/views/components/partials/theme-api.blade.php (loaded after @fluxAppearance).

document.addEventListener('livewire:navigated', function () {
    var ngn = localStorage.getItem('ngn-theme');
    var fa = localStorage.getItem('flux.appearance');
    var mode = ngn === 'dark' || ngn === 'light' ? ngn : (fa === 'dark' || fa === 'light' ? fa : null);
    if (mode && window.Flux && typeof window.Flux.applyAppearance === 'function') {
        window.Flux.applyAppearance(mode);
    } else if (mode) {
        document.documentElement.classList.toggle('dark', mode === 'dark');
    } else if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
        window.Flux.applyAppearance('system');
    }
});

Alpine.start();
