document.addEventListener('alpine:init', () => {

    // --- UI State ---
    Alpine.store('ui', {
        sidebarOpen: JSON.parse(localStorage.getItem('cord-sidebar') ?? 'true'),
        activeModal: null,
        theme: localStorage.getItem('cord-theme') || 'system',

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('cord-sidebar', JSON.stringify(this.sidebarOpen));
        },

        openModal(name) {
            this.activeModal = name;
        },

        closeModal() {
            this.activeModal = null;
        },

        setTheme(theme) {
            this.theme = theme;
            localStorage.setItem('cord-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
        },
    });

    // --- Notifications (toasts) ---
    Alpine.store('notifications', {
        items: [],

        notify(message, type = 'success', duration = 4000) {
            const id = Date.now();
            this.items.push({ id, message, type });

            if (duration > 0) {
                setTimeout(() => this.dismiss(id), duration);
            }
        },

        dismiss(id) {
            this.items = this.items.filter(n => n.id !== id);
        },
    });

    // --- Form State (visual) ---
    Alpine.store('form', {
        activeTab: null,
        expandedSections: {},
        dirty: false,

        setTab(tab) {
            this.activeTab = tab;
        },

        toggleSection(key) {
            this.expandedSections[key] = !this.expandedSections[key];
        },

        isSectionExpanded(key) {
            return this.expandedSections[key] ?? true;
        },

        markDirty() {
            this.dirty = true;
        },

        markClean() {
            this.dirty = false;
        },
    });

    // --- Confirmable (reusable Alpine.data) ---
    Alpine.data('confirmable', (message = 'Tem certeza?') => ({
        open: false,
        message,

        confirm(callback) {
            this.open = true;
            this._callback = callback;
        },

        proceed() {
            if (this._callback) {
                this._callback();
            }
            this.open = false;
        },

        cancel() {
            this.open = false;
        },
    }));
});
