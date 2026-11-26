/**
 * Controlador del Menú Lateral Responsivo (Hamburger Menu)
 * Gestiona la apertura y cierre del Sidebar en pantallas móviles y tablets.
 */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const closeBtn = document.getElementById('sidebarCloseBtn');

    function toggleSidebar() {
        if (!sidebar) return;
        sidebar.classList.toggle('show');
        if (overlay) {
            overlay.classList.toggle('show');
        }
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('show');
        if (overlay) {
            overlay.classList.remove('show');
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Cerrar con tecla Escape en móviles
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
});
