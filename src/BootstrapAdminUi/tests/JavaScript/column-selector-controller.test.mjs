import assert from 'node:assert/strict';
import { test } from 'node:test';
import ColumnSelectorController from '../../assets/controllers/column-selector-controller.js';

// Test the real controller with small DOM doubles; no browser or additional dependency is required.
function classList(...classes) {
    const tokens = new Set(classes);

    return {
        contains: token => tokens.has(token),
        toggle: (token, force) => force ? tokens.add(token) : tokens.delete(token),
    };
}

function column(name, { visible = true, defaultVisible = true } = {}) {
    const attributes = new Map([['aria-checked', String(visible)]]);
    const visibleIcon = { classList: classList(...(visible ? [] : ['d-none'])) };
    const hiddenIcon = { classList: classList(...(visible ? ['d-none'] : [])) };

    return {
        dataset: { column: name, defaultVisible: String(defaultVisible) },
        visibleIcon,
        hiddenIcon,
        setAttribute: (attribute, value) => attributes.set(attribute, value),
        removeAttribute: attribute => attributes.delete(attribute),
        getAttribute: attribute => attributes.get(attribute) ?? null,
        querySelector: selector => selector === '[data-visible-icon]' ? visibleIcon : hiddenIcon,
        classList: classList(),
    };
}

function setup(t, columns, href = 'https://example.com/admin/books?page=2') {
    const assign = t.mock.fn();
    const windowDescriptor = Object.getOwnPropertyDescriptor(globalThis, 'window');
    Object.defineProperty(globalThis, 'window', {
        configurable: true,
        value: { location: { href, assign } },
    });
    t.after(() => {
        if (windowDescriptor) {
            Object.defineProperty(globalThis, 'window', windowDescriptor);
        } else {
            delete globalThis.window;
        }
    });

    const controller = Object.assign(new ColumnSelectorController({}), {
        columnTargets: columns,
        parameterValue: 'visibility',
    });
    controller.connect();

    return { controller, assign };
}

function click(target) {
    const event = { currentTarget: target, preventDefault: () => { event.defaultPrevented = true; } };

    return event;
}

function hide(clickEvent = undefined) {
    return { clickEvent };
}

test('toggles a column without navigating', t => {
    const name = column('name');
    const author = column('author');
    const { controller, assign } = setup(t, [name, author]);
    const event = click(author);

    controller.toggle(event);

    assert.equal(event.defaultPrevented, true);
    assert.equal(author.getAttribute('aria-checked'), 'false');
    assert.equal(author.visibleIcon.classList.contains('d-none'), true);
    assert.equal(author.hiddenIcon.classList.contains('d-none'), false);
    assert.equal(assign.mock.callCount(), 0);
});

test('prevents hiding the last visible column', t => {
    const name = column('name');
    const author = column('author', { visible: false });
    const { controller } = setup(t, [name, author]);

    assert.equal(name.classList.contains('disabled'), true);
    assert.equal(name.getAttribute('aria-disabled'), 'true');
    assert.equal(name.getAttribute('tabindex'), '-1');

    const lastVisibleColumnClick = click(name);
    controller.toggle(lastVisibleColumnClick);
    assert.equal(lastVisibleColumnClick.defaultPrevented, true);
    assert.equal(name.getAttribute('aria-checked'), 'true');

    controller.toggle(click(author));
    assert.equal(name.classList.contains('disabled'), false);
    assert.equal(name.getAttribute('aria-disabled'), null);
    assert.equal(name.getAttribute('tabindex'), null);
});

test('does not reload the page when nothing changed', t => {
    const name = column('name');
    const author = column('author');
    const { controller, assign } = setup(t, [name, author]);
    const clickEvent = click(null);

    controller.toggle(click(author));
    controller.toggle(click(author));
    controller.apply(hide(clickEvent));

    assert.equal(assign.mock.callCount(), 0);
    assert.equal(clickEvent.defaultPrevented, undefined);
});

test('does not reload the page when only the order of the parameters differs', t => {
    const name = column('name');
    const author = column('author', { visible: false });
    const { controller, assign } = setup(
        t,
        [name, author],
        'https://example.com/admin/books?visibility[author]=hidden&sorting[name]=asc&page=1',
    );

    controller.apply(hide(click(null)));

    assert.equal(assign.mock.callCount(), 0);
});

test('reloads the page with the columns differing from their default visibility only', t => {
    const name = column('name');
    const author = column('author');
    const isbn = column('isbn', { visible: false, defaultVisible: false });
    const { controller, assign } = setup(
        t,
        [name, author, isbn],
        'https://example.com/admin/books?page=2&visibility=foo&visibility[name]=visible&visibility[foo]=hidden',
    );

    controller.toggle(click(author));
    controller.toggle(click(isbn));
    controller.apply(hide());

    const url = new URL(assign.mock.calls[0].arguments[0]);
    assert.equal(url.searchParams.get('page'), '2');
    assert.deepEqual(
        [...url.searchParams].filter(([key]) => key.startsWith('visibility')),
        [['visibility[author]', 'hidden'], ['visibility[isbn]', 'visible']],
    );
});

test('cancels the navigation of the click closing the dropdown when applying changes', t => {
    const name = column('name');
    const author = column('author');
    const { controller, assign } = setup(t, [name, author]);
    const clickEvent = click(null);

    controller.toggle(click(author));
    controller.apply(hide(clickEvent));

    assert.equal(clickEvent.defaultPrevented, true);
    assert.equal(assign.mock.callCount(), 1);
});
