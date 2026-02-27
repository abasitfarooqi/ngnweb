import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import persist from '@alpinejs/persist';

Alpine.plugin(focus);
Alpine.plugin(intersect);
Alpine.plugin(persist);

window.Alpine = Alpine;
Alpine.start();
