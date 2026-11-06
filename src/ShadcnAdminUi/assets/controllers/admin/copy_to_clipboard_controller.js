import { Controller } from '@hotwired/stimulus';

/** Port of the BootstrapAdminUi copy-to-clipboard controller, using Tailwind utilities. */
export default class extends Controller {
    static targets = ['button', 'copyIcon', 'successIcon', 'errorIcon', 'status'];

    static values = {
        value: String,
        copiedLabel: String,
        errorLabel: String,
    };

    connect() {
        this.pendingCopy = null;
        this.reset();
    }

    async copy() {
        if (this.pendingCopy || this.buttonTarget.disabled || !this.hasValueValue || this.valueValue === '') {
            return;
        }

        this.reset();
        const operation = {};
        this.pendingCopy = operation;
        this.buttonTarget.setAttribute('aria-busy', 'true');

        try {
            await navigator.clipboard.writeText(this.valueValue);

            if (this.pendingCopy === operation) {
                this.showFeedback(this.successIconTarget, this.copiedLabelValue);
            }
        } catch {
            if (this.pendingCopy === operation) {
                this.showFeedback(this.errorIconTarget, this.errorLabelValue, true);
            }
        } finally {
            if (this.pendingCopy === operation) {
                this.pendingCopy = null;
                this.buttonTarget.removeAttribute('aria-busy');
            }
        }
    }

    disconnect() {
        this.pendingCopy = null;
        clearTimeout(this.resetTimer);

        if (this.hasButtonTarget) {
            this.buttonTarget.removeAttribute('aria-busy');
        }
    }

    showFeedback(icon, message, isError = false) {
        this.reset();
        this.copyIconTarget.classList.add('hidden');
        icon.classList.remove('hidden');
        this.statusTarget.classList.toggle('sr-only', !isError);
        this.statusTarget.textContent = message;

        this.resetTimer = setTimeout(() => this.reset(), 2000);
    }

    reset() {
        clearTimeout(this.resetTimer);
        this.resetTimer = null;

        this.copyIconTarget.classList.remove('hidden');
        this.successIconTarget.classList.add('hidden');
        this.errorIconTarget.classList.add('hidden');
        this.statusTarget.classList.add('sr-only');
        this.statusTarget.textContent = '';
    }
}
