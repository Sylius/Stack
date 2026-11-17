import { Controller } from '@hotwired/stimulus';

/** Keeps a single item open in an accordion of shadcn Collapsible items: opening one closes the others. */
export default class extends Controller {
    closeOthers(event) {
        const item = event.currentTarget.closest('[data-controller~="shadcn--collapsible"]');

        if (item?.parentElement !== this.element || item.getAttribute('data-shadcn--collapsible-open-value') !== 'true') {
            return;
        }

        for (const other of this.element.querySelectorAll(':scope > [data-controller~="shadcn--collapsible"]')) {
            if (other !== item) {
                other.setAttribute('data-shadcn--collapsible-open-value', 'false');
            }
        }
    }
}
