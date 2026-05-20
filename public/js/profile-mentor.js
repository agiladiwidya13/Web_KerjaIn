// js/profile-mentor.js

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/session', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (!data.loggedIn || data.user.role !== 'mentor') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = data.user.nama;
            loadProfil();
        })
        .catch(() => loadProfil());
});

function loadProfil() {
    fetch('/api/mentor/profile', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') { tampilNotif('Gagal memuat data.', 'error'); return; }
            isiTampilan(data.data);
        })
        .catch(() => tampilNotif('Gagal terhubung ke server.', 'error'));
}

function isiTampilan(d) {
    const initials = (d.nama_lengkap || 'M').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    document.getElementById('avatar-initials').textContent = initials;

    document.getElementById('sidebar-nama').textContent  = d.nama_lengkap || '—';
    document.getElementById('sidebar-email').textContent = d.email || '—';
    document.getElementById('sidebar-info').innerHTML = `
        <div class="sidebar-info-item">💼 ${d.profesi || 'Belum diisi'}</div>
        <div class="sidebar-info-item">🏢 ${d.perusahaan || 'Belum diisi'}</div>
        <div class="sidebar-info-item">📅 ${d.tahun_pengalaman || 0} Tahun Pengalaman</div>
    `;

    document.getElementById('v-nama').textContent       = d.nama_lengkap      || '—';
    document.getElementById('v-email').textContent      = d.email             || '—';
    document.getElementById('v-profesi').textContent    = d.profesi           || 'Belum diisi';
    document.getElementById('v-perusahaan').textContent = d.perusahaan        || 'Belum diisi';
    document.getElementById('v-tahun').textContent      = (d.tahun_pengalaman || 0) + ' tahun';
    document.getElementById('v-bio').textContent        = d.bio_keahlian      || 'Belum diisi';

    document.getElementById('e-nama').value       = d.nama_lengkap      || '';
    document.getElementById('e-email').value      = d.email             || '';
    document.getElementById('e-profesi').value    = d.profesi           || '';
    document.getElementById('e-perusahaan').value = d.perusahaan        || '';
    document.getElementById('e-tahun').value      = d.tahun_pengalaman  || '';
    document.getElementById('e-bio').value        = d.bio_keahlian      || '';

    document.getElementById('nav-nama').textContent = d.nama_lengkap || '';
}

function toggleEdit() {
    document.getElementById('view-mode').style.display = 'none';
    document.getElementById('edit-mode').style.display = 'block';
    document.getElementById('btn-toggle-edit').style.display = 'none';
}

function cancelEdit() {
    document.getElementById('edit-mode').style.display = 'none';
    document.getElementById('view-mode').style.display = 'block';
    document.getElementById('btn-toggle-edit').style.display = 'inline-flex';
    document.getElementById('notif').style.display = 'none';
}

function simpanProfil() {
    const profesi    = document.getElementById('e-profesi').value.trim();
    const perusahaan = document.getElementById('e-perusahaan').value.trim();

    if (!profesi || !perusahaan) {
        tampilNotif('Profesi dan perusahaan wajib diisi!', 'error');
        return;
    }

    document.getElementById('btn-save').disabled = true;
    document.getElementById('btn-save').textContent = '⏳ Menyimpan...';

    const formData = new FormData();
    formData.append('nama_lengkap',     document.getElementById('e-nama').value.trim());
    formData.append('profesi',          profesi);
    formData.append('perusahaan',       perusahaan);
    formData.append('tahun_pengalaman', document.getElementById('e-tahun').value || 0);
    formData.append('bio_keahlian',     document.getElementById('e-bio').value.trim());

    fetch('/api/mentor/update', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tampilNotif('✅ ' + data.message, 'success');
                loadProfil();
                setTimeout(() => cancelEdit(), 1200);
            } else {
                tampilNotif('❌ ' + data.message, 'error');
            }
        })
        .catch(() => tampilNotif('❌ Gagal terhubung ke server.', 'error'))
        .finally(() => {
            document.getElementById('btn-save').disabled = false;
            document.getElementById('btn-save').textContent = '💾 Simpan Perubahan';
        });
}

function toggleDeleteForm(show) {
    document.getElementById('delete-form').style.display     = show ? 'block' : 'none';
    document.getElementById('btn-show-delete').style.display = show ? 'none'  : 'inline-flex';
    if (!show) document.getElementById('del-pass').value = '';
}

function hapusAkun() {
    const pass = document.getElementById('del-pass').value.trim();
    if (!pass) { tampilNotif('Masukkan password untuk konfirmasi.', 'error'); return; }
    if (!confirm('⚠️ Yakin ingin menghapus akun mentor? Data tidak bisa dipulihkan!')) return;

    document.getElementById('btn-del').disabled = true;
    document.getElementById('btn-del').textContent = '⏳ Menghapus...';

    const formData = new FormData();
    formData.append('password_confirm', pass);

    fetch('/api/mentor/delete', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tampilNotif('✅ ' + data.message, 'success');
                setTimeout(() => window.location.href = '/', 1500);
            } else {
                tampilNotif('❌ ' + data.message, 'error');
                document.getElementById('btn-del').disabled = false;
                document.getElementById('btn-del').textContent = '🗑️ Hapus Akun Saya';
            }
        })
        .catch(() => {
            tampilNotif('❌ Gagal terhubung ke server.', 'error');
            document.getElementById('btn-del').disabled = false;
            document.getElementById('btn-del').textContent = '🗑️ Hapus Akun Saya';
        });
}

function tampilNotif(pesan, tipe) {
    const el = document.getElementById('notif');
    el.textContent = pesan;
    el.className = 'notif notif-' + tipe;
    el.style.display = 'block';
    if (tipe === 'success') setTimeout(() => { el.style.display = 'none'; }, 4000);
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function handleLogout() {
    const confirmed = confirm('Apakah Anda yakin ingin keluar?');
    if (!confirmed) return;
    
    fetch('/api/logout', { method: 'POST', credentials: 'same-origin' })
        .then(() => { window.location.href = '/'; })
        .catch(() => { window.location.href = '/'; });
}
