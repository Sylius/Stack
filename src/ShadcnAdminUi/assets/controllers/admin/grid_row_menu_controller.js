import { Controller } from '@hotwired/stimulus';

/**
 * Row "…" menu of the grid: the item actions are rendered by the grid action templates (icon
 * buttons and links carrying a title), not by DropdownMenu:Item. Turn each of them into a menu
 * item so the shadcn--dropdown-menu controller moves the focus with the arrow keys.
 * Elements inside a <dialog> (the delete confirmation) are not menu items.
 */
export default class extends Controller {
    connect() {
        for (const action of this.element.querySelectorAll('[title]')) {
            if (action.closest('dialog')) {
                continue;
            }

            action.setAttribute('role', 'menuitem');
            action.setAttribute('tabindex', '-1');
            action.setAttribute('data-shadcn--dropdown-menu-target', 'item');
        }
    }
}
