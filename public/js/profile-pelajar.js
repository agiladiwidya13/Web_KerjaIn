// js/profile-pelajar.js

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/session', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (!data.loggedIn || data.user.role !== 'pelajar') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = data.user.nama;
            loadProfil();
        })
        .catch(() => loadProfil());
});

function loadProfil() {
    fetch('/api/pelajar/profile', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                tampilNotif('Gagal memuat data: ' + data.message, 'error');
                return;
            }
            isiTampilan(data.data);
        })
        .catch(() => tampilNotif('Gagal terhubung ke server.', 'error'));
}

function isiTampilan(d) {
    const initials = (d.nama_lengkap || 'U').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    document.getElementById('avatar-initials').textContent = initials;

    const imgEl = document.getElementById('avatar-img');
    const initialsEl = document.getElementById('avatar-initials');
    if (d.foto_profil) {
        imgEl.src = '/' + d.foto_profil;
        imgEl.style.display = 'block';
        initialsEl.style.display = 'none';
    } else {
        imgEl.style.display = 'none';
        initialsEl.style.display = 'flex';
    }

    document.getElementById('sidebar-nama').textContent  = d.nama_lengkap || '—';
    document.getElementById('sidebar-email').textContent = d.email || '—';
    document.getElementById('sidebar-info').innerHTML = `
        <div class="sidebar-info-item"><span class="material-icons icon-inline">account_balance</span> ${d.universitas || 'Belum diisi'}</div>
        <div class="sidebar-info-item"><span class="material-icons icon-inline">menu_book</span> ${d.jurusan || 'Belum diisi'}</div>
        <div class="sidebar-info-item"><span class="material-icons icon-inline">calendar_today</span> Angkatan ${d.angkatan || '—'}</div>
    `;

    document.getElementById('v-nama').textContent        = d.nama_lengkap  || '—';
    document.getElementById('v-email').textContent       = d.email         || '—';
    document.getElementById('v-universitas').textContent = d.universitas   || 'Belum diisi';
    document.getElementById('v-jurusan').textContent     = d.jurusan       || 'Belum diisi';
    document.getElementById('v-angkatan').textContent    = d.angkatan      || 'Belum diisi';
    document.getElementById('v-bio').textContent         = d.bio           || 'Belum diisi';

    document.getElementById('e-nama').value        = d.nama_lengkap  || '';
    document.getElementById('e-email').value       = d.email         || '';
    document.getElementById('e-universitas').value = d.universitas   || '';
    document.getElementById('e-jurusan').value     = d.jurusan       || '';
    document.getElementById('e-angkatan').value    = d.angkatan      || '';
    document.getElementById('e-bio').value         = d.bio           || '';

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
    const universitas = document.getElementById('e-universitas').value.trim();
    const jurusan     = document.getElementById('e-jurusan').value.trim();
    const angkatan    = document.getElementById('e-angkatan').value.trim();

    if (!universitas || !jurusan || !angkatan) {
        tampilNotif('Universitas, jurusan, dan angkatan wajib diisi!', 'error');
        return;
    }
    if (parseInt(angkatan) < 2000 || parseInt(angkatan) > 2099) {
        tampilNotif('Tahun angkatan tidak valid (2000–2099).', 'error');
        return;
    }

    document.getElementById('btn-save').disabled = true;
    document.getElementById('btn-save').textContent = 'Menyimpan...';

    const formData = new FormData();
    formData.append('nama_lengkap',  document.getElementById('e-nama').value.trim());
    formData.append('universitas',   universitas);
    formData.append('jurusan',       jurusan);
    formData.append('angkatan',      angkatan);
    formData.append('bio',           document.getElementById('e-bio').value.trim());

    fetch('/api/pelajar/update', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tampilNotif(data.message, 'success');
                loadProfil();
                setTimeout(() => cancelEdit(), 1200);
            } else {
                tampilNotif(data.message, 'error');
            }
        })
        .catch(() => tampilNotif('Gagal terhubung ke server.', 'error'))
        .finally(() => {
            document.getElementById('btn-save').disabled = false;
            document.getElementById('btn-save').textContent = 'Simpan Perubahan';
        });
}

function toggleDeleteForm(show) {
    document.getElementById('delete-form').style.display      = show ? 'block' : 'none';
    document.getElementById('btn-show-delete').style.display  = show ? 'none'  : 'inline-flex';
    if (!show) document.getElementById('del-pass').value = '';
}

function hapusAkun() {
    const pass = document.getElementById('del-pass').value.trim();
    if (!pass) {
        tampilNotif('Masukkan password untuk konfirmasi penghapusan.', 'error');
        return;
    }
    if (!confirm('Yakin ingin menghapus akun? Semua data kamu akan hilang permanen!')) return;

    document.getElementById('btn-del').disabled = true;
    document.getElementById('btn-del').textContent = 'Menghapus...';

    const formData = new FormData();
    formData.append('password_confirm', pass);

    fetch('/api/pelajar/delete', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tampilNotif(data.message + ' Mengalihkan...', 'success');
                setTimeout(() => window.location.href = '/', 1500);
            } else {
                tampilNotif(data.message, 'error');
                document.getElementById('btn-del').disabled = false;
                document.getElementById('btn-del').textContent = 'Hapus Akun Saya';
            }
        })
        .catch(() => {
            tampilNotif('Gagal terhubung ke server.', 'error');
            document.getElementById('btn-del').disabled = false;
            document.getElementById('btn-del').textContent = 'Hapus Akun Saya';
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
    fetch('/api/logout', { method: 'POST', credentials: 'same-origin' })
        .then(() => { window.location.href = '/'; })
        .catch(() => { window.location.href = '/'; });
}

function uploadFotoProfil(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        tampilNotif('Ukuran file maksimal 2 MB', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('foto', file);

    tampilNotif('Sedang mengupload foto...', 'success');

    fetch('/api/upload-foto', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => { throw err; });
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success') {
            tampilNotif('Foto profil berhasil diubah!', 'success');
            loadProfil();
            if (typeof loadSession === 'function') {
                loadSession();
            }
        } else {
            tampilNotif(data.message, 'error');
        }
    })
    .catch(err => {
        const msg = err.errors && err.errors.foto ? err.errors.foto[0] : (err.message || 'Gagal mengupload foto.');
        tampilNotif(msg, 'error');
    });
}

function gantiPassword() {
    const current = document.getElementById('p-current').value;
    const newPass = document.getElementById('p-new').value;
    const confirmPass = document.getElementById('p-confirm').value;

    if (!current || !newPass || !confirmPass) {
        tampilNotif('Semua field password wajib diisi!', 'error');
        return;
    }

    if (newPass.length < 8) {
        tampilNotif('Password baru minimal 8 karakter!', 'error');
        return;
    }

    if (newPass !== confirmPass) {
        tampilNotif('Konfirmasi password baru tidak cocok!', 'error');
        return;
    }

    const btn = document.getElementById('btn-change-pass');
    btn.disabled = true;
    btn.textContent = 'Mengubah password...';

    const formData = new FormData();
    formData.append('current_password', current);
    formData.append('new_password', newPass);
    formData.append('new_password_confirmation', confirmPass);

    fetch('/api/change-password', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => { throw err; });
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success') {
            tampilNotif('Password berhasil diubah!', 'success');
            document.getElementById('p-current').value = '';
            document.getElementById('p-new').value = '';
            document.getElementById('p-confirm').value = '';
        } else {
            tampilNotif(data.message, 'error');
        }
    })
    .catch(err => {
        const msg = err.errors && err.errors.new_password ? err.errors.new_password[0] : (err.message || 'Gagal mengubah password.');
        tampilNotif(msg, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Ganti Password';
    });
}