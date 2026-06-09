<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 120px);
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid var(--dash-border);
        }
        .chat-sidebar {
            width: 300px;
            border-right: 1px solid var(--dash-border);
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .contact-item {
            padding: 16px;
            border-bottom: 1px solid var(--dash-border);
            cursor: pointer;
            transition: 0.2s;
        }
        .contact-item:hover {
            background: #eff6ff;
        }
        .contact-item.active {
            background: #eff6ff;
            border-left: 4px solid var(--primary);
        }
        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--dash-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8fafc;
        }
        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .message-mine {
            background: var(--primary);
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        .message-theirs {
            background: #fff;
            color: var(--text);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid var(--dash-border);
        }
        .message-time {
            font-size: 0.75rem;
            margin-top: 4px;
            opacity: 0.8;
            text-align: right;
        }
        .chat-input-area {
            padding: 16px 24px;
            border-top: 1px solid var(--dash-border);
            display: flex;
            gap: 12px;
            background: #fff;
        }
        .chat-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--dash-border);
            border-radius: 24px;
            outline: none;
            font-family: inherit;
        }
        .chat-input:focus {
            border-color: var(--primary);
        }
        .btn-send {
            background: var(--primary);
            color: #fff;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .btn-send:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<nav>
    <div class="logo" onclick="window.location.href='/'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links"></div>
    <div class="nav-auth">
        <a href="javascript:history.back()" class="btn-dash btn-dash-outline" style="margin-right:8px;">Kembali</a>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<div style="padding: 24px; max-width: 1200px; margin: 0 auto;">
    <div class="chat-container">
        <!-- Sidebar Contacts -->
        <div class="chat-sidebar">
            <div style="padding: 16px; border-bottom: 1px solid var(--dash-border); font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <span class="material-icons" style="font-size: 1.2rem; color: var(--dash-accent);">mail</span> Kotak Masuk
            </div>
            <div id="contacts-list" style="overflow-y: auto; flex: 1;">
                <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                    Memuat pesan...
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main" id="chat-area" style="display: none;">
            <div class="chat-header">
                <div>
                    <h3 style="margin: 0;" id="chat-title">Nama Kontak</h3>
                    <div style="font-size: 0.85rem; color: var(--text-muted);" id="chat-subtitle">Role</div>
                </div>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be injected here -->
            </div>
            
            <div class="chat-input-area">
                <input type="text" id="message-input" class="chat-input" placeholder="Ketik pesan Anda..." onkeypress="if(event.key === 'Enter') sendMessage()">
                <button class="btn-send" onclick="sendMessage()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
        
        <!-- Empty State -->
        <div class="chat-main" id="chat-empty" style="align-items: center; justify-content: center; background: #f8fafc;">
            <div style="text-align: center; color: var(--text-muted);">
                <div style="margin-bottom: 16px;"><span class="material-icons" style="font-size: 4rem; color: var(--text-muted);">forum</span></div>
                <h3>Pilih percakapan</h3>
                <p>Pilih kontak di sebelah kiri untuk mulai mengobrol.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let currentContactId = null;

    document.addEventListener('DOMContentLoaded', loadContacts);

    function handleLogout() {
        fetch('/api/logout', { method: 'POST' })
            .then(() => window.location.href = '/')
            .catch(() => window.location.href = '/');
    }

    function loadContacts() {
        fetch('/api/messages/contacts')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('contacts-list');
                if (data.status === 'success' && data.data.length > 0) {
                    container.innerHTML = data.data.map(c => `
                        <div class="contact-item" id="contact-${c.id}" onclick="openChat('${c.id}')">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <strong style="color: var(--text);">${c.nama}</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">${c.last_time}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 0.85rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                    ${c.last_message || 'Mulai percakapan...'}
                                </div>
                                ${c.unread > 0 ? `<div style="background: var(--danger); color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; font-weight: 700;">${c.unread}</div>` : ''}
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = `<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">Belum ada percakapan.</div>`;
                }
            });
    }

    function openChat(contactId) {
        currentContactId = contactId;
        document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
        const contactEl = document.getElementById(`contact-${contactId}`);
        if(contactEl) contactEl.classList.add('active');

        document.getElementById('chat-empty').style.display = 'none';
        document.getElementById('chat-area').style.display = 'flex';
        
        const messagesContainer = document.getElementById('chat-messages');
        messagesContainer.innerHTML = `<div style="text-align: center; color: var(--text-muted); padding: 20px;">Memuat riwayat chat...</div>`;

        fetch(`/api/messages/${contactId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('chat-title').textContent = data.contact.nama;
                    document.getElementById('chat-subtitle').textContent = data.contact.role.toUpperCase();
                    
                    if(data.data.length === 0) {
                        messagesContainer.innerHTML = `<div style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada riwayat pesan. Mulai sapa sekarang!</div>`;
                    } else {
                        messagesContainer.innerHTML = data.data.map(m => `
                            <div class="message-bubble ${m.is_mine ? 'message-mine' : 'message-theirs'}">
                                <div>${m.isi}</div>
                                <div class="message-time">${m.time}</div>
                            </div>
                        `).join('');
                        scrollToBottom();
                    }
                    // Refresh contacts to clear unread badge
                    loadContacts();
                }
            });
    }

    function sendMessage() {
        if (!currentContactId) return;
        
        const input = document.getElementById('message-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        
        // Optimistic UI update
        const messagesContainer = document.getElementById('chat-messages');
        if(messagesContainer.innerHTML.includes('Belum ada riwayat')) {
            messagesContainer.innerHTML = '';
        }
        
        const tempId = Date.now();
        messagesContainer.innerHTML += `
            <div class="message-bubble message-mine" style="opacity: 0.7;" id="msg-${tempId}">
                <div>${text}</div>
                <div class="message-time">Mengirim...</div>
            </div>
        `;
        scrollToBottom();

        const formData = new FormData();
        formData.append('isi', text);

        fetch(`/api/messages/${currentContactId}`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const msgEl = document.getElementById(`msg-${tempId}`);
                    if(msgEl) {
                        msgEl.style.opacity = '1';
                        msgEl.querySelector('.message-time').textContent = data.data.time;
                    }
                    loadContacts();
                }
            });
    }

    function scrollToBottom() {
        const container = document.getElementById('chat-messages');
        container.scrollTop = container.scrollHeight;
    }
</script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
