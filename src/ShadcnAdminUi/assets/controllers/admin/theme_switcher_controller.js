import { Controller } from '@hotwired/stimulus';

/**
 * Toggles the "dark" class on <html> and remembers the choice.
 * The initial theme is applied by an inline script (shared/layout/theme.html.twig)
 * before the stylesheet loads, to avoid a flash of the wrong theme.
 */
export default class extends Controller {
    static values = {
        storageKey: { type: String, default: 'sylius-theme' },
    };

    toggle() {
        const root = document.documentElement;
        const next = root.classList.contains('dark') ? 'light' : 'dark';

        root.classList.toggle('dark', next === 'dark');
        root.dataset.theme = next;

        try {
            localStorage.setItem(this.storageKeyValue, next);
        } catch {
            // Storage may be unavailable (private mode): the toggle still works for this page.
        }
    }
}
