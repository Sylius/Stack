import { Controller } from '@hotwired/stimulus';

const STORAGE_KEY = 'sylius-theme';
const MODES = ['light', 'dark', 'auto'];

export default class extends Controller {
    static targets = ['option'];

    connect() {
        this.onStorage = (event) => {
            if (event.key === STORAGE_KEY || event.key === null) {
                this.render();
            }
        };
        window.addEventListener('storage', this.onStorage);
        this.render();
    }

    disconnect() {
        window.removeEventListener('storage', this.onStorage);
    }

    select(event) {
        const mode = event.params.mode;

        if (!MODES.includes(mode)) {
            return;
        }

        this.apply(mode);
    }

    toggle() {
        const current = document.documentElement.getAttribute('data-bs-theme') || 'light';

        this.apply(current === 'dark' ? 'light' : 'dark');
    }

    apply(mode) {
        if (mode === 'auto') {
            localStorage.removeItem(STORAGE_KEY);
        } else {
            localStorage.setItem(STORAGE_KEY, mode);
        }

        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = mode === 'auto' ? (prefersDark ? 'dark' : 'light') : mode;

        document.documentElement.setAttribute('data-theme-mode', mode);
        document.documentElement.setAttribute('data-bs-theme', theme);

        this.render();
    }

    render() {
        const mode = document.documentElement.getAttribute('data-theme-mode') || 'auto';

        this.optionTargets.forEach((option) => {
            const active = option.dataset.themeSwitcherModeParam === mode;
            option.classList.toggle('active', active);
            option.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }
}
