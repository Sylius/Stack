import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    toggle() {
        const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('sylius-theme', next);
        document.documentElement.setAttribute('data-bs-theme', next);
    }
}
