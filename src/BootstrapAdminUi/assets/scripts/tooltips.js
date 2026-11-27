/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

export function initTooltips(root, Tooltip) {
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltipTriggerEl) => {
        Tooltip.getInstance(tooltipTriggerEl)?.dispose();
        new Tooltip(tooltipTriggerEl);
    });
}

// live:connect fires again on every reconnect of the same component, so its hook is registered only once
export function initTooltipsOnLiveRender(target, Tooltip) {
    const boundComponents = new WeakSet();

    target.addEventListener('live:connect', (event) => {
        const { component } = event.detail;
        if (boundComponents.has(component)) {
            return;
        }

        boundComponents.add(component);
        component.on('render:finished', (renderedComponent) => initTooltips(renderedComponent.element, Tooltip));
    });
}
