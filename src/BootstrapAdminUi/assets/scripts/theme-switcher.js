const toggleButton = document.querySelector('[data-theme-toggle]');

if (toggleButton) {
    toggleButton.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('sylius-theme', next);
        document.documentElement.setAttribute('data-bs-theme', next);
    });
}
