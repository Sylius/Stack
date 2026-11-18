import { Controller } from '@hotwired/stimulus';

/**
 * Row selection for the grid data table:
 * - "all" checkbox in the table header (with the indeterminate state),
 * - bulk action triggers, disabled while nothing is selected,
 * - bulk forms: the selected ids are appended as "ids[]" before submitting.
 */
export default class extends Controller {
    static targets = ['all', 'item', 'action', 'count'];

    connect() {
        this.refresh();
    }

    toggleAll() {
        const checked = this.allTarget.checked;
        this.itemTargets.forEach((item) => {
            item.checked = checked;
        });
        this.refresh();
    }

    refresh() {
        const selected = this.selectedIds();
        const total = this.itemTargets.length;

        if (this.hasAllTarget) {
            this.allTarget.checked = total > 0 && selected.length === total;
            this.allTarget.indeterminate = selected.length > 0 && selected.length < total;
        }

        this.actionTargets.forEach((action) => {
            action.disabled = selected.length === 0;
        });

        this.countTargets.forEach((count) => {
            count.textContent = String(selected.length);
            count.hidden = selected.length === 0;
        });

        this.itemTargets.forEach((item) => {
            item.closest('tr')?.setAttribute('data-state', item.checked ? 'selected' : '');
        });
    }

    submit(event) {
        const form = event.target;
        form.querySelectorAll('input[name="ids[]"]').forEach((input) => input.remove());

        this.selectedIds().forEach((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
    }

    selectedIds() {
        return this.itemTargets.filter((item) => item.checked).map((item) => item.value);
    }
}
