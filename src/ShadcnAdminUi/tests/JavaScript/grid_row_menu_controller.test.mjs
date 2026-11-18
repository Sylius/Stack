import assert from 'node:assert/strict';
import { test } from 'node:test';
import GridRowMenuController from '../../assets/controllers/admin/grid_row_menu_controller.js';

// Small DOM doubles; no browser or additional dependency is required.
function action({ inDialog = false } = {}) {
    const attributes = new Map();

    return {
        setAttribute: (name, value) => attributes.set(name, value),
        getAttribute: name => attributes.get(name),
        closest: selector => (selector === 'dialog' && inDialog ? {} : null),
    };
}

function connect(actions) {
    const element = {
        querySelectorAll: selector => {
            assert.equal(selector, '[title]');
            return actions;
        },
    };
    const controller = new GridRowMenuController({ scope: { element } });
    controller.connect();
}

test('turns each grid action into a dropdown menu item', () => {
    const show = action();
    const remove = action();
    connect([show, remove]);

    for (const item of [show, remove]) {
        assert.equal(item.getAttribute('role'), 'menuitem');
        assert.equal(item.getAttribute('tabindex'), '-1');
        assert.equal(item.getAttribute('data-shadcn--dropdown-menu-target'), 'item');
    }
});

test('leaves the elements of a confirmation dialog alone', () => {
    const inDialog = action({ inDialog: true });
    connect([inDialog]);

    assert.equal(inDialog.getAttribute('role'), undefined);
    assert.equal(inDialog.getAttribute('data-shadcn--dropdown-menu-target'), undefined);
});
