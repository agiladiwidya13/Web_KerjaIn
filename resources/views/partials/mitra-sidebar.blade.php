<aside class="dash-sidebar">
    <div class="sidebar-header">
        <h3>Menu Mitra</h3>
    </div>
    <a class="nav-item {{ request()->is('pages/mitra/dashboard*') ? 'active' : '' }}" href="/pages/mitra/dashboard">
        <span class="nav-icon"><span class="material-icons">bar_chart</span></span> Dashboard
    </a>
    <a class="nav-item {{ request()->is('pages/mitra/dashboard') && request()->get('section') === 'programs' ? 'active' : '' }}" href="/pages/mitra/dashboard?section=programs">
        <span class="nav-icon"><span class="material-icons">menu_book</span></span> Kelola Program
    </a>
    <a class="nav-item {{ request()->is('pages/mitra/dashboard') && request()->get('section') === 'mentors' ? 'active' : '' }}" href="/pages/mitra/dashboard?section=mentors">
        <span class="nav-icon"><span class="material-icons">person</span></span> Mentor Saya
    </a>
    <a class="nav-item {{ request()->is('pages/mitra/mentor-applications') ? 'active' : '' }}" href="/pages/mitra/mentor-applications">
        <span class="nav-icon"><span class="material-icons">assignment_ind</span></span> Lamaran Mentor
    </a>
    <a class="nav-item {{ request()->is('pages/mitra/candidates') ? 'active' : '' }}" href="/pages/mitra/candidates">
        <span class="nav-icon"><span class="material-icons">search</span></span> Cari Kandidat
    </a>
    <a class="nav-item {{ request()->is('pages/mitra/profile') ? 'active' : '' }}" href="/pages/mitra/profile">
        <span class="nav-icon"><span class="material-icons">account_circle</span></span> Profil Perusahaan
    </a>
</aside>
