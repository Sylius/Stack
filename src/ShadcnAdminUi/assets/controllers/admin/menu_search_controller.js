import { Controller } from '@hotwired/stimulus';

/**
 * Filters the sidebar menu while typing, and expands the sidebar when a
 * collapsible item is clicked while it is collapsed to icons.
 * Section groups and sub-section headers are hidden when nothing under them matches.
 */
export default class extends Controller {
    static targets = ['input', 'item', 'group'];

    filter() {
        const query = this.inputTarget.value.trim().toLowerCase();

        this.itemTargets.forEach((item) => {
            const children = Array.from(item.querySelectorAll('[data-menu-search-child]'));

            if (children.length === 0) {
                item.hidden = query !== '' && !this.matches(item, query);
                return;
            }

            const parentMatches = this.matches(item.querySelector('[data-menu-search-label]'), query);
            let visibleChildren = 0;

            children.forEach((child) => {
                const visible = query === '' || parentMatches || this.matches(child, query);
                child.hidden = !visible;
                visibleChildren += visible ? 1 : 0;
            });

            item.hidden = query !== '' && visibleChildren === 0;
            this.toggleHeaders(item);

            if (query !== '' && visibleChildren > 0) {
                this.setSectionOpen(item, true);
            }
        });

        this.groupTargets.forEach((group) => {
            const items = this.itemTargets.filter((item) => group.contains(item));
            group.hidden = items.length > 0 && items.every((item) => item.hidden);
        });
    }

    // A header stays visible when at least one child until the next header is visible.
    toggleHeaders(item) {
        let header = null;
        let headerHasChild = false;
        const closeHeader = () => {
            if (header) {
                header.hidden = !headerHasChild;
            }
        };

        item.querySelectorAll('[data-menu-search-header], [data-menu-search-child]').forEach((element) => {
            if (element.hasAttribute('data-menu-search-header')) {
                closeHeader();
                header = element;
                headerHasChild = false;
            } else if (!element.hidden) {
                headerHasChild = true;
            }
        });
        closeHeader();
    }

    clear(event) {
        if (event.key && event.key !== 'Escape') {
            return;
        }
        this.inputTarget.value = '';
        this.filter();
    }

    expandSidebar() {
        if (document.querySelector('[data-slot="sidebar-provider"][data-state="collapsed"]')) {
            window.dispatchEvent(new CustomEvent('admin-sidebar:open'));
        }
    }

    matches(element, query) {
        return query === '' || (element?.textContent ?? '').toLowerCase().includes(query);
    }

    setSectionOpen(item, open) {
        const collapsible = item.querySelector('[data-controller~="shadcn--collapsible"]');
        if (collapsible) {
            // Stimulus observes the value attribute and calls openValueChanged().
            collapsible.setAttribute('data-shadcn--collapsible-open-value', String(open));
        }
    }
}
