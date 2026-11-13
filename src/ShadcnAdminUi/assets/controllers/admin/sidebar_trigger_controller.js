import { Controller } from '@hotwired/stimulus';

/**
 * The navbar and the sidebar are sibling hookables, so the navbar button cannot reach the
 * shadcn sidebar controller through a regular Stimulus action. It talks to it through window
 * events instead (see shared/components/sidebar_provider.html.twig).
 */
export default class extends Controller {
    toggle() {
        window.dispatchEvent(new CustomEvent('admin-sidebar:toggle'));
    }

    open() {
        window.dispatchEvent(new CustomEvent('admin-sidebar:open'));
    }
}
