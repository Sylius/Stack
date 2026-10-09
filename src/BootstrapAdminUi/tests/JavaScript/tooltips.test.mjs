import assert from 'node:assert/strict';
import { test } from 'node:test';
import { initTooltips, initTooltipsOnLiveRender } from '../../assets/scripts/tooltips.js';

function fakeTooltip() {
    const instances = new Map();

    class Tooltip {
        constructor(element) {
            this.element = element;
            this.disposed = false;
            instances.set(element, this);
        }

        dispose() {
            this.disposed = true;
            instances.delete(this.element);
        }

        static getInstance(element) {
            return instances.get(element) ?? null;
        }
    }

    return Tooltip;
}

function root(...triggers) {
    return {
        querySelectorAll: (selector) => {
            assert.equal(selector, '[data-bs-toggle="tooltip"]');

            return triggers;
        },
    };
}

test('binds a tooltip to every trigger', () => {
    const Tooltip = fakeTooltip();
    const first = {};
    const second = {};

    initTooltips(root(first, second), Tooltip);

    assert.ok(Tooltip.getInstance(first));
    assert.ok(Tooltip.getInstance(second));
});

test('replaces a tooltip already bound to a trigger', () => {
    const Tooltip = fakeTooltip();
    const trigger = {};
    initTooltips(root(trigger), Tooltip);
    const stale = Tooltip.getInstance(trigger);

    initTooltips(root(trigger), Tooltip);

    assert.equal(stale.disposed, true);
    assert.notEqual(Tooltip.getInstance(trigger), stale);
});

test('rebinds tooltips inside a live component after each render', () => {
    const Tooltip = fakeTooltip();
    const listeners = {};
    const target = { addEventListener: (name, callback) => { listeners[name] = callback; } };
    const hooks = {};
    const component = {
        element: root({}),
        on: (name, callback) => { hooks[name] = callback; },
    };
    const freshTrigger = {};

    initTooltipsOnLiveRender(target, Tooltip);
    listeners['live:connect']({ detail: { component } });
    component.element = root(freshTrigger);
    hooks['render:finished'](component);

    assert.ok(Tooltip.getInstance(freshTrigger));
});
