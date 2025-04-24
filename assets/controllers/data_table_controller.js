import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
  static values = {
    page: Number,
    pushOnBrowserHistory: Boolean,
  }

  async initialize() {
    this.component = await getComponent(this.element);

    const dataTables = document.querySelectorAll('[data-sylius-data-table]');
    const basePath = window.location.pathname;

    dataTables.forEach((dataTable) => {
      this.addLinkEventListener(dataTable);
    });

    this.component.on('render:finished', () => {
      const currentPage = this.pageValue || 1;
      dataTables.forEach((dataTable) => {
        this.updateLinks(dataTable, basePath, currentPage);
      });
    });
  }


  addLinkEventListener(dataTable) {
    dataTable.addEventListener('click', (event) => {
      const link = event.target.closest('a[data-action]');
      if (link) {
        event.preventDefault();
      }

      const href = link.getAttribute('href');
      if (!href) return;

      if (this.pushOnBrowserHistoryValue) {
        // Push the new URL into the browser history
        window.history.pushState({}, '', href);
      }
    });
  }

  updateLinks(dataTable, basePath, currentPage) {
    const links = dataTable.querySelectorAll('a[data-action]');

    links.forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;

      // Parse the href into a full URL object
      const url = new URL(href, window.location.origin);

      // Extract only the query string (e.g., ?page=3&limit=25)
      let query = url.searchParams;

      if (link.rel === 'prev' || link.rel === 'next') {
        query = this.updatePageQuery(query, link.rel, currentPage);
      }

      // Build a new href using the given base path and the original query string
      const newHref = basePath + '?' + query.toString();

      // Replace the original href with the cleaned-up one
      link.setAttribute('href', newHref);
    });
  }

  updatePageQuery(query, rel, currentPage) {

    let newPage = rel === 'prev' ? currentPage - 1 : currentPage + 1;

    // Ensure the page doesn't go below 1
    newPage = Math.max(newPage, 1);

    // Update the page query parameter
    query.set('page', newPage);

    return query;
  }
}
