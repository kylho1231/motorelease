import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();

window.renderLucideIcons = () => {
    createIcons({ icons });
};

// GetLayers Interactive Spotlight Effect
function initGetLayersSpotlight() {
    document.querySelectorAll('[data-spotlight]').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    window.renderLucideIcons();
    initGetLayersSpotlight();
});
