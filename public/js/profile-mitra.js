// js/profile-mitra.js

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/session', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (!data.loggedIn || data.user.role !== 'mitra') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = data.user.nama;
            loadProfil();
        })
        .catch(() => loadProfil());
});

function loadProfil() {
    fetch('/api/mitra/profile', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') { tampilNotif('Gagal memuat data.', 'error'); return; }
            isiTampilan(data.data);
        })
        .catch(() => tampilNotif('Gagal terhubung ke server.', 'error'));
}

function isiTampilan(d) {
    const initials = (d.nama_lengkap || 'P').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
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

    // Company logo display
    const vLogo = document.getElementById('v-logo');
    const vLogoEmpty = document.getElementById('v-logo-empty');
    if (d.logo_perusahaan) {
        vLogo.src = '/' + d.logo_perusahaan;
        vLogo.style.display = 'block';
        vLogoEmpty.style.display = 'none';
    } else {
        vLogo.style.display = 'none';
        vLogoEmpty.style.display = 'block';
    }

    // Company logo preview (edit mode)
    const eLogoPrev = document.getElementById('e-logo-preview');
    const eLogoEmpty = document.getElementById('e-logo-empty');
    if (d.logo_perusahaan) {
        eLogoPrev.src = '/' + d.logo_perusahaan;
        eLogoPrev.style.display = 'block';
        eLogoEmpty.style.display = 'none';
    } else {
        eLogoPrev.style.display = 'none';
        eLogoEmpty.style.display = 'block';
    }

    document.getElementById('sidebar-nama').textContent  = d.nama_usaha || d.nama_lengkap || '—';
    document.getElementById('sidebar-email').textContent = d.email || '—';
    document.getElementById('sidebar-info').innerHTML = `
        <div class="sidebar-info-item"><span class="material-icons icon-inline">factory</span> ${d.bidang_usaha || 'Belum diisi'}</div>
        <div class="sidebar-info-item"><span class="material-icons icon-inline">location_on</span> ${d.kota || 'Belum diisi'}</div>
        <div class="sidebar-info-item"><span class="material-icons icon-inline">phone</span> ${d.kontak_bisnis || 'Belum diisi'}</div>
    `;

    document.getElementById('v-nama').textContent   = d.nama_lengkap  || '—';
    document.getElementById('v-email').textContent  = d.email         || '—';
    document.getElementById('v-usaha').textContent  = d.nama_usaha    || 'Belum diisi';
    document.getElementById('v-bidang').textContent = d.bidang_usaha  || 'Belum diisi';
    document.getElementById('v-kota').textContent   = d.kota          || 'Belum diisi';
    document.getElementById('v-kontak').textContent = d.kontak_bisnis || 'Belum diisi';

    document.getElementById('e-nama').value    = d.nama_lengkap  || '';
    document.getElementById('e-email').value   = d.email         || '';
    document.getElementById('e-usaha').value   = d.nama_usaha    || '';
    document.getElementById('e-bidang').value  = d.bidang_usaha  || '';
    document.getElementById('e-kota').value    = d.kota          || '';
    document.getElementById('e-kontak').value  = d.kontak_bisnis || '';

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
    const nama_usaha = document.getElementById('e-usaha').value.trim();
    if (!nama_usaha) {
        tampilNotif('Nama usaha wajib diisi!', 'error');
        return;
    }

    document.getElementById('btn-save').disabled = true;
    document.getElementById('btn-save').textContent = 'Menyimpan...';

    const formData = new FormData();
    formData.append('nama_lengkap',  document.getElementById('e-nama').value.trim());
    formData.append('nama_usaha',    nama_usaha);
    formData.append('bidang_usaha',  document.getElementById('e-bidang').value.trim());
    formData.append('kota',          document.getElementById('e-kota').value.trim());
    formData.append('kontak_bisnis', document.getElementById('e-kontak').value.trim());

    fetch('/api/mitra/update', { method: 'POST', body: formData, credentials: 'same-origin' })
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
    document.getElementById('delete-form').style.display     = show ? 'block' : 'none';
    document.getElementById('btn-show-delete').style.display = show ? 'none'  : 'inline-flex';
    if (!show) document.getElementById('del-pass').value = '';
}

function hapusAkun() {
    const pass = document.getElementById('del-pass').value.trim();
    if (!pass) { tampilNotif('Masukkan password untuk konfirmasi.', 'error'); return; }
    if (!confirm('Yakin ingin menghapus akun mitra? Data tidak bisa dipulihkan!')) return;

    document.getElementById('btn-del').disabled = true;
    document.getElementById('btn-del').textContent = 'Menghapus...';

    const formData = new FormData();
    formData.append('password_confirm', pass);

    fetch('/api/mitra/delete', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tampilNotif(data.message, 'success');
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

function uploadLogoPerusahaan(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        tampilNotif('Ukuran file maksimal 2 MB', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('logo', file);

    tampilNotif('Sedang mengupload logo...', 'success');

    fetch('/api/mitra/upload-logo', {
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
            tampilNotif('Logo perusahaan berhasil diubah!', 'success');
            loadProfil();
        } else {
            tampilNotif(data.message, 'error');
        }
    })
    .catch(err => {
        const msg = err.errors && err.errors.logo ? err.errors.logo[0] : (err.message || 'Gagal mengupload logo.');
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
