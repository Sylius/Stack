/* eslint-env browser */
import * as bootstrap from 'bootstrap';
import { initTooltips, initTooltipsOnLiveRender } from './tooltips';

// Fix dropdowns
(() => {
    document.querySelectorAll('.dropdown-static').forEach((dropdownToggleEl) => {
        const parent = dropdownToggleEl.closest('[data-bs-toggle="dropdown"]');
        if (parent) {
            let dropdown = new bootstrap.Dropdown(parent, {
                popperConfig(defaultBsPopperConfig) {
                    return { ...defaultBsPopperConfig, strategy: 'fixed' };
                },
            });
        }
    });
})();

// Initialize tooltips
initTooltips(document, bootstrap.Tooltip);
initTooltipsOnLiveRender(document, bootstrap.Tooltip);

window.bootstrap = bootstrap;
