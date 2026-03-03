import './bootstrap';

import pace from 'pace-js';
import 'pace-js/themes/blue/pace-theme-center-simple.css';

pace.start({
    document: true,
    ajax: true,
    restartOnRequestAfter: false,
});

document.addEventListener('livewire:navigating', () => {
    pace.restart();
});

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
