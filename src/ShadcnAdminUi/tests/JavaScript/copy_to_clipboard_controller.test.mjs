import assert from 'node:assert/strict';
import { test } from 'node:test';
import CopyToClipboardController from '../../assets/controllers/admin/copy_to_clipboard_controller.js';

// Test the real controller with small DOM doubles; no browser or additional dependency is required.
function element(...classes) {
    const tokens = new Set(classes);
    const attributes = new Map();

    return {
        disabled: false,
        textContent: '',
        setAttribute: (name, value) => attributes.set(name, value),
        removeAttribute: name => attributes.delete(name),
        getAttribute: name => attributes.get(name),
        classList: {
            add: token => tokens.add(token),
            remove: token => tokens.delete(token),
            contains: token => tokens.has(token),
            toggle: (token, force) => force ? tokens.add(token) : tokens.delete(token),
        },
    };
}

function setup(t, writeText = t.mock.fn(async () => {})) {
    t.mock.timers.enable({ apis: ['setTimeout'] });
    const navigatorDescriptor = Object.getOwnPropertyDescriptor(globalThis, 'navigator');
    Object.defineProperty(globalThis, 'navigator', {
        configurable: true,
        value: { clipboard: { writeText } },
    });
    t.after(() => {
        if (navigatorDescriptor) {
            Object.defineProperty(globalThis, 'navigator', navigatorDescriptor);
        } else {
            delete globalThis.navigator;
        }
    });

    const controller = Object.assign(new CopyToClipboardController({}), {
        buttonTarget: element(),
        hasButtonTarget: true,
        copyIconTarget: element(),
        successIconTarget: element('hidden'),
        errorIconTarget: element('hidden'),
        statusTarget: element('sr-only'),
        hasValueValue: true,
        valueValue: '000123',
        copiedLabelValue: 'Copied',
        errorLabelValue: 'Unable to copy',
    });
    controller.connect();
    t.after(() => controller.disconnect());

    return { controller, writeText };
}

test('copies the explicit value and resets successful feedback after two seconds', async t => {
    const { controller, writeText } = setup(t);
    controller.valueValue = 'Référence 日本語\n000123';
    await controller.copy();

    assert.deepEqual(writeText.mock.calls[0].arguments, [controller.valueValue]);
    assert.equal(controller.copyIconTarget.classList.contains('hidden'), true);
    assert.equal(controller.successIconTarget.classList.contains('hidden'), false);
    assert.equal(controller.statusTarget.textContent, 'Copied');
    assert.equal(controller.statusTarget.classList.contains('sr-only'), true);
    assert.equal(controller.buttonTarget.getAttribute('aria-busy'), undefined);
    t.mock.timers.tick(2000);
    assert.equal(controller.copyIconTarget.classList.contains('hidden'), false);
    assert.equal(controller.successIconTarget.classList.contains('hidden'), true);
    assert.equal(controller.statusTarget.textContent, '');
});

test('makes failures visible and allows retrying', async t => {
    const writeText = t.mock.fn(async () => { throw new Error('Permission denied'); });
    const { controller } = setup(t, writeText);
    await controller.copy();

    assert.equal(controller.errorIconTarget.classList.contains('hidden'), false);
    assert.equal(controller.statusTarget.classList.contains('sr-only'), false);
    assert.equal(controller.statusTarget.textContent, 'Unable to copy');
    assert.equal(controller.pendingCopy, null);

    writeText.mock.mockImplementation(async () => {});
    await controller.copy();
    assert.equal(controller.errorIconTarget.classList.contains('hidden'), true);
    assert.equal(controller.successIconTarget.classList.contains('hidden'), false);
    assert.equal(controller.statusTarget.classList.contains('sr-only'), true);
});

test('handles an unavailable Clipboard API', async t => {
    const { controller } = setup(t);
    delete navigator.clipboard;
    await controller.copy();

    assert.equal(controller.statusTarget.textContent, 'Unable to copy');
    assert.equal(controller.statusTarget.classList.contains('sr-only'), false);
});

test('ignores repeated clicks while a copy is pending without removing keyboard focus', async t => {
    const deferred = Promise.withResolvers();
    const writeText = t.mock.fn(() => deferred.promise);
    const { controller } = setup(t, writeText);
    const pending = controller.copy();
    await controller.copy();

    assert.equal(writeText.mock.callCount(), 1);
    assert.equal(controller.buttonTarget.getAttribute('aria-busy'), 'true');
    assert.equal(controller.buttonTarget.disabled, false);
    deferred.resolve();
    await pending;
    assert.equal(controller.statusTarget.textContent, 'Copied');
    assert.equal(controller.buttonTarget.getAttribute('aria-busy'), undefined);
});

for (const outcome of ['resolve', 'reject']) {
    test(`ignores a late ${outcome} after disconnect and reconnect`, async t => {
        const stale = Promise.withResolvers();
        const current = Promise.withResolvers();
        const writeText = t.mock.fn(() => stale.promise);
        const { controller } = setup(t, writeText);
        const staleCopy = controller.copy();
        controller.disconnect();
        controller.connect();
        writeText.mock.mockImplementation(() => current.promise);
        const currentCopy = controller.copy();
        stale[outcome]();
        await staleCopy;

        assert.equal(controller.statusTarget.textContent, '');
        assert.equal(controller.buttonTarget.getAttribute('aria-busy'), 'true');
        t.mock.timers.tick(2000);
        assert.equal(controller.statusTarget.textContent, '');
        current.resolve();
        await currentCopy;
        assert.equal(controller.statusTarget.textContent, 'Copied');
    });
}

test('does not access removed targets or schedule feedback after disconnect', async t => {
    const deferred = Promise.withResolvers();
    const { controller } = setup(t, () => deferred.promise);
    const pending = controller.copy();
    controller.hasButtonTarget = false;
    controller.disconnect();
    delete controller.buttonTarget;
    delete controller.successIconTarget;
    delete controller.errorIconTarget;
    delete controller.statusTarget;
    deferred.resolve();
    await pending;
    t.mock.timers.tick(2000);
});

test('clears the feedback timer on disconnect and resets on reconnect', async t => {
    const { controller } = setup(t);
    await controller.copy();
    const reset = t.mock.method(controller, 'reset');
    controller.disconnect();
    t.mock.timers.tick(2000);
    assert.equal(reset.mock.callCount(), 0);
    controller.connect();
    assert.equal(controller.statusTarget.textContent, '');
    assert.equal(controller.successIconTarget.classList.contains('hidden'), true);
});

test('does not let an older timer clear newer feedback', async t => {
    const { controller } = setup(t);
    await controller.copy();
    t.mock.timers.tick(1500);
    await controller.copy();
    t.mock.timers.tick(500);
    assert.equal(controller.statusTarget.textContent, 'Copied');
    t.mock.timers.tick(1500);
    assert.equal(controller.statusTarget.textContent, '');
});

for (const state of [{ valueValue: '' }, { hasValueValue: false }, { disabled: true }]) {
    test(`does not copy an unavailable value or disabled button: ${JSON.stringify(state)}`, async t => {
        const { controller, writeText } = setup(t);
        if (state.disabled) {
            controller.buttonTarget.disabled = true;
        } else {
            Object.assign(controller, state);
        }
        await controller.copy();
        assert.equal(writeText.mock.callCount(), 0);
        assert.equal(controller.statusTarget.textContent, '');
    });
}

test('copies zero and keeps multiple instances independent', async t => {
    const { controller: first, writeText } = setup(t);
    const second = Object.assign(new CopyToClipboardController({}), {
        ...first,
        buttonTarget: element(),
        copyIconTarget: element(),
        successIconTarget: element('hidden'),
        errorIconTarget: element('hidden'),
        statusTarget: element('sr-only'),
        valueValue: '0',
    });
    second.connect();
    t.after(() => second.disconnect());
    await second.copy();

    assert.deepEqual(writeText.mock.calls[0].arguments, ['0']);
    assert.equal(second.statusTarget.textContent, 'Copied');
    assert.equal(first.statusTarget.textContent, '');
});
