// dashboard-global.js
// Script untuk Dark Mode dan Real-time Notifications

// Intercept fetch to handle 401 (Unauthorized/Session Expired) globally
(function() {
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch(...args).then(response => {
            if (response.status === 401) {
                const currentPath = window.location.pathname;
                if (currentPath !== '/' && !currentPath.includes('/api/')) {
                    window.location.href = '/?expired=1';
                }
            }
            return response;
        });
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initNotifications();
    initSessionProfile();
    initSidebarToggle();
});

function initDarkMode() {
    const isDark = localStorage.getItem('theme') === 'dark';
    if (isDark) {
        document.body.classList.add('dark-mode');
    }

    const navAuth = document.querySelector('.nav-auth');
    if (navAuth) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'btn-dash btn-dash-outline';
        toggleBtn.style.marginRight = '8px';
        toggleBtn.style.padding = '6px 12px';
        toggleBtn.style.display = 'flex';
        toggleBtn.style.alignItems = 'center';
        toggleBtn.style.gap = '6px';
        toggleBtn.innerHTML = isDark ? '<span class="material-icons" style="font-size:20px;">light_mode</span>' : '<span class="material-icons" style="font-size:20px;">dark_mode</span>';
        toggleBtn.title = 'Toggle Dark Mode';
        
        toggleBtn.onclick = () => {
            const isCurrentlyDark = document.body.classList.contains('dark-mode');
            if (isCurrentlyDark) {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
                toggleBtn.innerHTML = '<span class="material-icons" style="font-size:20px;">dark_mode</span>';
            } else {
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
                toggleBtn.innerHTML = '<span class="material-icons" style="font-size:20px;">light_mode</span>';
            }
        };

        navAuth.prepend(toggleBtn);
    }
}

function initNotifications() {
    const navAuth = document.querySelector('.nav-auth');
    if (!navAuth) return;

    // Tambahkan komponen lonceng
    const notifContainer = document.createElement('div');
    notifContainer.className = 'nav-notifications';
    notifContainer.innerHTML = `
        <span class="material-icons" style="font-size: 1.2rem;">notifications</span>
        <span class="badge-count" id="notif-count" style="display: none;">0</span>
        <div class="notification-dropdown" id="notif-dropdown">
            <div style="padding: 12px 16px; border-bottom: 1px solid var(--dash-border); font-weight: 700;">
                Notifikasi
            </div>
            <div id="notif-list">
                <div style="padding: 20px; text-align: center; color: var(--text-muted);">Memuat...</div>
            </div>
        </div>
    `;

    notifContainer.onclick = (e) => {
        if (e.target.closest('.notif-item')) return; // let links work
        const dropdown = document.getElementById('notif-dropdown');
        dropdown.classList.toggle('show');
    };

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-notifications')) {
            const dropdown = document.getElementById('notif-dropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });

    navAuth.prepend(notifContainer);

    // Fetch initial
    fetchNotifications();

    // Polling setiap 30 detik untuk "Real-time" prototype
    setInterval(fetchNotifications, 30000);
}

function fetchNotifications() {
    fetch('/api/notifications')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const countBadge = document.getElementById('notif-count');
                if (data.unread_count > 0) {
                    countBadge.textContent = data.unread_count;
                    countBadge.style.display = 'block';
                } else {
                    countBadge.style.display = 'none';
                }

                const listContainer = document.getElementById('notif-list');
                if (data.data.length === 0) {
                    listContainer.innerHTML = `<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Belum ada notifikasi.</div>`;
                } else {
                    listContainer.innerHTML = data.data.map(n => `
                        <a href="${n.link}" class="notif-item ${n.is_read ? '' : 'unread'}" onclick="markAsRead('${n.id}')">
                            <span class="notif-title">${n.title}</span>
                            <span class="notif-msg">${n.message}</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted); margin-top: 4px;">${n.created_at}</span>
                        </a>
                    `).join('');
                }
            }
        })
        .catch(console.error);
}

window.markAsRead = function(id) {
    fetch(`/api/notifications/${id}/read`, { method: 'POST' });
};

function initSessionProfile() {
    fetch('/api/session', { credentials: 'same-origin' })
        .then(res => {
            if (!res.ok) throw new Error('Not logged in');
            return res.json();
        })
        .then(data => {
            if (data.status === 'success' && data.loggedIn) {
                const navNama = document.getElementById('nav-nama');
                if (navNama) {
                    navNama.textContent = data.user.nama;
                }
                
                const navAuth = document.querySelector('.nav-auth');
                if (navAuth) {
                    let navImg = document.getElementById('nav-avatar-img');
                    if (!navImg) {
                        navImg = document.createElement('img');
                        navImg.id = 'nav-avatar-img';
                        navImg.style.width = '32px';
                        navImg.style.height = '32px';
                        navImg.style.borderRadius = '50%';
                        navImg.style.objectFit = 'cover';
                        navImg.style.marginRight = '8px';
                        navImg.style.border = '2px solid var(--secondary)';
                        // Try insertion after the theme toggle button if present, otherwise prepended
                        const toggleBtn = navAuth.querySelector('button');
                        if (toggleBtn) {
                            toggleBtn.after(navImg);
                        } else {
                            navAuth.prepend(navImg);
                        }
                    }
                    if (data.user.foto_profil) {
                        navImg.src = data.user.foto_profil;
                        navImg.style.display = 'inline-block';
                    } else {
                        navImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.user.nama)}&background=4338ca&color=fff&bold=true`;
                        navImg.style.display = 'inline-block';
                    }
                }
            }
        })
        .catch(() => {});
}

window.loadSession = initSessionProfile;

function initSidebarToggle() {
    const sidebar = document.querySelector('.dash-sidebar');
    if (!sidebar) return;

    const header = sidebar.querySelector('.sidebar-header');
    if (!header) return;

    if (!header.querySelector('.btn-collapse-sidebar')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'btn-collapse-sidebar';
        toggleBtn.innerHTML = '<span class="material-icons">chevron_left</span>';
        toggleBtn.title = 'Kecilkan/Besarkan Sidebar';
        
        toggleBtn.onclick = () => {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? 'yes' : 'no');
        };
        
        header.appendChild(toggleBtn);
    }

    if (localStorage.getItem('sidebar_collapsed') === 'yes') {
        sidebar.classList.add('collapsed');
    }
}
