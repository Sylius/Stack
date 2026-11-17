import { Controller } from '@hotwired/stimulus';

/*
 * Accessible tabs (replaces the Bootstrap tabs, whose JS is not loaded).
 * - Tab: <button data-admin--tabs-target="tab" aria-controls="<panel id>">, panel: <div id="..." data-admin--tabs-target="panel">.
 * - Keyboard: arrows (up/down and left/right), Home, End; a single focusable tab (roving tabindex).
 * - The selected tab is kept in the URL hash (#tab-<id>) to reopen the same tab.
 * - Tabs whose panel holds a validation error are flagged (data-invalid), and submitting a form with a hidden
 *   invalid field opens its tab instead of failing without showing anything.
 */
export default class extends Controller {
    static targets = ['tab', 'panel'];

    connect() {
        this.onInvalid = this.onInvalid.bind(this);
        this.element.addEventListener('invalid', this.onInvalid, true);

        this.markInvalidTabs();

        const fromHash = this.tabTargets.find((tab) => `#tab-${tab.getAttribute('aria-controls')}` === window.location.hash);
        const firstInvalid = this.tabTargets.find((tab) => tab.dataset.invalid === 'true');
        const initial = firstInvalid ?? fromHash ?? this.tabTargets.find((tab) => tab.dataset.active === 'true') ?? this.tabTargets[0];

        if (initial) {
            this.show(initial, false);
        }
    }

    disconnect() {
        this.element.removeEventListener('invalid', this.onInvalid, true);
    }

    select(event) {
        event.preventDefault();
        this.show(event.currentTarget);
    }

    keydown(event) {
        const tabs = this.tabTargets;
        const index = tabs.indexOf(event.currentTarget);
        let next = null;

        switch (event.key) {
            case 'ArrowDown':
            case 'ArrowRight':
                next = tabs[(index + 1) % tabs.length];
                break;
            case 'ArrowUp':
            case 'ArrowLeft':
                next = tabs[(index - 1 + tabs.length) % tabs.length];
                break;
            case 'Home':
                next = tabs[0];
                break;
            case 'End':
                next = tabs[tabs.length - 1];
                break;
            default:
                return;
        }

        event.preventDefault();
        this.show(next);
        next.focus();
    }

    show(tab, updateHash = true) {
        const panelId = tab.getAttribute('aria-controls');

        this.tabTargets.forEach((item) => {
            const selected = item === tab;
            item.setAttribute('aria-selected', selected ? 'true' : 'false');
            item.setAttribute('tabindex', selected ? '0' : '-1');
            item.dataset.state = selected ? 'active' : 'inactive';
        });

        this.panelTargets.forEach((panel) => {
            panel.hidden = panel.id !== panelId;
        });

        if (updateHash) {
            history.replaceState(null, '', `#tab-${panelId}`);
        }
    }

    onInvalid(event) {
        const panel = event.target.closest('[data-admin--tabs-target="panel"]');
        if (!panel || !panel.hidden) {
            return;
        }

        const tab = this.tabTargets.find((item) => item.getAttribute('aria-controls') === panel.id);
        if (tab) {
            this.show(tab);
            tab.dataset.invalid = 'true';
        }
    }

    markInvalidTabs() {
        this.panelTargets.forEach((panel) => {
            if (!panel.querySelector('[aria-invalid="true"], [data-slot="field-error"]')) {
                return;
            }

            const tab = this.tabTargets.find((item) => item.getAttribute('aria-controls') === panel.id);
            if (tab) {
                tab.dataset.invalid = 'true';
            }
        });
    }
}
