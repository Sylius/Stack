import { Controller } from '@hotwired/stimulus';

/** Removes the element (flash messages, alerts) when the user closes it. */
export default class extends Controller {
    close() {
        this.element.remove();
    }
}
