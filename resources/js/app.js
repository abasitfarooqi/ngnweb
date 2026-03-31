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
Alpine.start();
