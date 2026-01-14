document.addEventListener('click', (event) => {
    if (event.target.matches('[data-confirm]')) {
        const message = event.target.getAttribute('data-confirm');
        if (!confirm(message)) {
            event.preventDefault();
        }
    }
});
