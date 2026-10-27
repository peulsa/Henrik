document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
        const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
        root.dataset.theme = next;
        localStorage.setItem('henrik-theme', next);
    });

    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        document.querySelector('#sidebar')?.classList.toggle('open');
    });

    const movementType = document.querySelector('[data-movement-type]');
    if (movementType) {
        const updateFields = () => {
            const type = movementType.value;
            const needsFrom = ['deliver', 'transfer', 'install', 'adjust_out'].includes(type);
            const needsTo = ['receive', 'transfer', 'remove', 'adjust_in'].includes(type);
            document.querySelector('[data-from-field]')?.classList.toggle('field-muted', !needsFrom);
            document.querySelector('[data-to-field]')?.classList.toggle('field-muted', !needsTo);
        };
        movementType.addEventListener('change', updateFields);
        updateFields();
    }
});
