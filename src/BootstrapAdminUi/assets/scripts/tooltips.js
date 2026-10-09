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

// A live component re-render morphs its markup, so tooltips inside it end up unbound or showing a stale title
export function initTooltipsOnLiveRender(target, Tooltip) {
    target.addEventListener('live:connect', (event) => {
        event.detail.component.on('render:finished', (component) => initTooltips(component.element, Tooltip));
    });
}
