import './bootstrap';

function dismissToast(toast) {
    if (! toast || toast.dataset.toastClosing === 'true') {
        return;
    }

    toast.dataset.toastClosing = 'true';
    toast.classList.add('opacity-0', 'translate-y-2', 'scale-95');
    toast.classList.remove('opacity-100');

    window.setTimeout(() => {
        toast.remove();
    }, 220);
}

document.addEventListener('click', (event) => {
    const toastClose = event.target.closest('[data-toast-close]');

    if (toastClose) {
        dismissToast(toastClose.closest('[data-toast]'));
        return;
    }

    const profileToggle = event.target.closest('[data-profile-menu-toggle]');

    if (profileToggle) {
        const menuWrap = profileToggle.closest('[data-profile-menu-wrap]');
        const menu = menuWrap?.querySelector('[data-profile-menu]');

        if (menu) {
            menu.classList.toggle('hidden');
        }

        return;
    }

    const openProfileMenu = document.querySelector('[data-profile-menu-wrap] [data-profile-menu]:not(.hidden)');

    if (openProfileMenu && ! event.target.closest('[data-profile-menu-wrap]')) {
        openProfileMenu.classList.add('hidden');
    }

    const filter = event.target.closest('[data-section-filter]');

    if (filter) {
        const scope = filter.closest('[data-filter-scope]');
        const target = filter.dataset.sectionFilter;

        if (! scope || ! target) {
            return;
        }

        scope.querySelectorAll('[data-section-filter]').forEach((button) => {
            const active = button === filter;
            button.classList.toggle('border-blue-500/50', active);
            button.classList.toggle('bg-blue-600', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('border-white/10', !active);
            button.classList.toggle('bg-white/5', !active);
            button.classList.toggle('text-slate-300', !active);
        });

        scope.querySelectorAll('[data-filter-section]').forEach((section) => {
            section.classList.toggle('hidden', target !== 'all' && section.dataset.filterSection !== target);
        });
    }

    const toggle = event.target.closest('[data-password-toggle]');

    if (! toggle) {
        return;
    }

    const input = document.querySelector(toggle.dataset.passwordToggle);

    if (! input) {
        return;
    }

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    toggle.innerHTML = isPassword
        ? '<i class="fas fa-eye-slash"></i>'
        : '<i class="fas fa-eye"></i>';
});

function normalizeDuplicateValue(value) {
    return (value || '').trim().toLowerCase();
}

function validateUniqueForm(form) {
    const fields = form.querySelectorAll('[data-unique-field]');
    let hasDuplicate = false;

    fields.forEach((field) => {
        const values = JSON.parse(field.dataset.uniqueValues || '[]').map(normalizeDuplicateValue);
        const currentValue = normalizeDuplicateValue(field.value);
        const originalValue = normalizeDuplicateValue(field.dataset.originalValue || '');
        const duplicate = currentValue && currentValue !== originalValue && values.includes(currentValue);
        const warning = form.querySelector(`[data-unique-warning="${field.dataset.uniqueField}"]`);

        field.classList.toggle('border-amber-400', duplicate);
        field.classList.toggle('focus:border-amber-400', duplicate);

        if (warning) {
            warning.classList.toggle('hidden', ! duplicate);
        }

        hasDuplicate = hasDuplicate || duplicate;
    });

    form.querySelectorAll('[data-unique-submit]').forEach((button) => {
        button.disabled = hasDuplicate;
        button.classList.toggle('cursor-not-allowed', hasDuplicate);
        button.classList.toggle('opacity-50', hasDuplicate);
    });
}

document.addEventListener('input', (event) => {
    const field = event.target.closest('[data-unique-field]');

    if (field) {
        validateUniqueForm(field.closest('form'));
    }
});

window.addEventListener('load', () => {
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        window.setTimeout(() => dismissToast(toast), 5000);
    });

    document.querySelectorAll('form[data-unique-form]').forEach(validateUniqueForm);

    document.querySelectorAll('[data-filter-scope]').forEach((scope) => {
        const params = new URLSearchParams(window.location.search);
        const target = params.get('filter') || window.location.hash.replace('#', '');
        const button = target ? scope.querySelector(`[data-section-filter="${target}"]`) : null;

        if (button) {
            button.click();
        }
    });
});
