import { Controller } from '@hotwired/stimulus';

const MODES = ['light', 'dark', 'auto'];

/**
 * Switches between the light, dark and auto (follows the OS preference) modes and remembers the choice.
 * The initial theme is applied by an inline script (shared/layout/theme.html.twig)
 * before the stylesheet loads, to avoid a flash of the wrong theme. That script also
 * follows OS changes in auto mode and syncs open tabs.
 */
export default class extends Controller {
    static targets = ['option'];

    static values = {
        storageKey: { type: String, default: 'sylius-theme' },
    };

    connect() {
        this.onStorage = (event) => {
            if (event.key === this.storageKeyValue || event.key === null) {
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
        this.apply(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
    }

    apply(mode) {
        try {
            if (mode === 'auto') {
                localStorage.removeItem(this.storageKeyValue);
            } else {
                localStorage.setItem(this.storageKeyValue, mode);
            }
        } catch {
            // Storage may be unavailable (private mode): the switch still works for this page.
        }

        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = mode === 'auto' ? (prefersDark ? 'dark' : 'light') : mode;
        const root = document.documentElement;

        root.classList.toggle('dark', theme === 'dark');
        root.dataset.theme = theme;
        root.dataset.themeMode = mode;

        this.render();
    }

    render() {
        const mode = document.documentElement.dataset.themeMode || 'auto';

        this.optionTargets.forEach((option) => {
            const checked = option.getAttribute('data-admin--theme-switcher-mode-param') === mode;
            option.setAttribute('aria-checked', String(checked));

            const indicator = option.querySelector('[data-radio-indicator]');
            if (indicator) {
                indicator.hidden = !checked;
            }
        });
    }
}
