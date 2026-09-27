<!-- Customer Support Chatbot Widget -->
<div id="gani-chat-root">
    <!-- 1. Floating Chat Trigger Button -->
    <button id="gani-chat-toggle-btn" class="gani-chat-toggle" aria-label="Open Customer Support Chat">
        <span id="gani-chat-unread-badge" class="gani-chat-unread-badge d-none">0</span>
        <i id="gani-chat-toggle-icon" class="fas fa-comments"></i>
    </button>

    <!-- 2. Main Chat Drawer / Window -->
    <div id="gani-chat-window" class="gani-chat-window gani-chat-hidden">
        <!-- Chat Header -->
        <div class="gani-chat-header">
            <div class="gani-chat-header-info">
                <div class="gani-chat-avatar">
                    <i class="fas fa-headset"></i>
                    <span id="gani-ws-status-dot" class="gani-ws-dot online" title="WebSocket Status: Connected"></span>
                </div>
                <div class="gani-chat-title-group">
                    <h5 class="gani-chat-title">
                        @if(auth('customer')->check())
                            Hi, {{ auth('customer')->user()->name }}! 👋
                        @else
                            Hi! How can we help you? 👋
                        @endif
                    </h5>
                    <div class="gani-chat-status-bar">
                        <span id="gani-chat-mode-badge" class="gani-mode-badge ai">
                            <i class="fas fa-robot"></i> AI Assistant
                        </span>
                        <span id="gani-ws-status-text" class="gani-ws-text">Connected</span>
                    </div>
                </div>
            </div>
            <button id="gani-chat-close-btn" class="gani-chat-close-btn" aria-label="Close Chat">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- System Announcement / Notice Banner -->
        <div id="gani-chat-notice" class="gani-chat-notice d-none">
            <i class="fas fa-info-circle"></i> <span id="gani-chat-notice-text">Notice</span>
        </div>

        <!-- Chat Messages Container -->
        <div id="gani-chat-messages" class="gani-chat-messages">
            <!-- Initial Welcome Message -->
            <div class="gani-msg-row assistant">
                <div class="gani-msg-avatar"><i class="fas fa-robot"></i></div>
                <div class="gani-msg-bubble">
                    @if(auth('customer')->check())
                        Hello {{ auth('customer')->user()->name }}! I am your AI customer support assistant. You can ask me about our products, store policies, or check your orders and shipping status!
                    @else
                        Welcome to {{ $generalsetting->name ?? 'Gani Enterprise' }}! How can we assist you today? You can ask about our product catalog, delivery areas, or store policies!
                    @endif
                    <div class="gani-msg-time">{{ date('h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- AI Typing Indicator -->
        <div id="gani-chat-typing" class="gani-chat-typing d-none">
            <div class="gani-msg-avatar"><i class="fas fa-robot"></i></div>
            <div class="gani-typing-bubble">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>

        <!-- Inline Error Alert Banner -->
        <div id="gani-chat-error" class="gani-chat-error d-none">
            <span id="gani-chat-error-text">Network error occurred.</span>
            <button id="gani-chat-retry-btn" class="btn btn-sm btn-link p-0 text-white font-weight-bold ml-2">Retry</button>
        </div>

        <!-- Chat Input Footer -->
        <div class="gani-chat-footer">
            <!-- Guest Order Quick Prompt (if guest) -->
            @if(!auth('customer')->check())
            <div id="gani-guest-order-prompt" class="gani-guest-prompt d-none">
                <span>Looking for order tracking?</span>
                <a href="{{ route('customer.login') }}" class="gani-guest-login-link">Log In</a>
            </div>
            @endif

            <form id="gani-chat-form" onsubmit="return false;">
                <div class="gani-input-wrap">
                    <textarea 
                        id="gani-chat-input" 
                        class="gani-chat-input" 
                        placeholder="Type your message here..." 
                        rows="1" 
                        maxlength="2000"
                    ></textarea>
                    <button type="submit" id="gani-chat-send-btn" class="gani-chat-send-btn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="gani-input-meta">
                    <span class="gani-char-counter"><span id="gani-char-count">0</span> / 2000</span>
                    <span class="gani-hint-text">Press Enter to send</span>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chat Widget Styling -->
<style>
#gani-chat-root {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    z-index: 99999;
}

/* Floating Trigger Button */
.gani-chat-toggle {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #C9A84C 0%, #9E7D2B 100%);
    color: #ffffff;
    border: none;
    outline: none;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(201, 168, 76, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    z-index: 99999;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
}

.gani-chat-toggle:hover {
    transform: scale(1.08);
    box-shadow: 0 12px 30px rgba(201, 168, 76, 0.6);
}

.gani-chat-unread-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #e74c3c;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    border: 2px solid #ffffff;
}

/* Main Chat Drawer / Window */
.gani-chat-window {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 380px;
    height: 560px;
    max-height: 82vh;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 99998;
    transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
    opacity: 1;
    transform: translateY(0);
    visibility: visible;
}

.gani-chat-window.gani-chat-hidden {
    opacity: 0;
    transform: translateY(20px);
    visibility: hidden;
    pointer-events: none;
}

/* Header */
.gani-chat-header {
    background: #1a1a1a;
    color: #ffffff;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #C9A84C;
}

.gani-chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.gani-chat-avatar {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(201, 168, 76, 0.2);
    color: #C9A84C;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.gani-ws-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #1a1a1a;
    background: #2ecc71;
}

.gani-ws-dot.offline { background: #e74c3c; }
.gani-ws-dot.connecting { background: #f39c12; }

.gani-chat-title-group {
    display: flex;
    flex-direction: column;
}

.gani-chat-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1.2;
}

.gani-chat-status-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
}

.gani-mode-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}

.gani-mode-badge.ai { background: rgba(52, 152, 219, 0.2); color: #3498db; }
.gani-mode-badge.human { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

.gani-ws-text {
    font-size: 10px;
    color: #888888;
}

.gani-chat-close-btn {
    background: transparent;
    border: none;
    color: #aaaaaa;
    font-size: 18px;
    cursor: pointer;
    transition: color 0.2s ease;
}

.gani-chat-close-btn:hover { color: #ffffff; }

/* Notice Banner */
.gani-chat-notice {
    background: #fff8e7;
    color: #8a6d3b;
    border-bottom: 1px solid #faebcc;
    padding: 8px 14px;
    font-size: 12px;
}

/* Messages List */
.gani-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: #fcfcfc;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.gani-msg-row {
    display: flex;
    gap: 8px;
    max-width: 85%;
}

.gani-msg-row.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.gani-msg-row.assistant, .gani-msg-row.admin {
    align-self: flex-start;
}

.gani-msg-row.system {
    align-self: center;
    max-width: 95%;
}

.gani-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e9ecef;
    color: #495057;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}

.gani-msg-row.admin .gani-msg-avatar {
    background: #1a1a1a;
    color: #C9A84C;
}

.gani-msg-bubble {
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.45;
    word-break: break-word;
    position: relative;
}

.gani-msg-row.user .gani-msg-bubble {
    background: #C9A84C;
    color: #ffffff;
    border-bottom-right-radius: 2px;
}

.gani-msg-row.assistant .gani-msg-bubble {
    background: #ffffff;
    color: #212529;
    border: 1px solid #e9ecef;
    border-bottom-left-radius: 2px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
}

.gani-msg-row.admin .gani-msg-bubble {
    background: #1a1a1a;
    color: #ffffff;
    border-bottom-left-radius: 2px;
}

.gani-msg-row.system .gani-msg-bubble {
    background: #eef2f5;
    color: #6c757d;
    font-size: 11px;
    text-align: center;
    border-radius: 20px;
    padding: 6px 14px;
}

.gani-msg-time {
    font-size: 9px;
    opacity: 0.7;
    margin-top: 4px;
    text-align: right;
}

/* Typing Indicator */
.gani-chat-typing {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 16px 12px 16px;
}

.gani-typing-bubble {
    background: #ffffff;
    border: 1px solid #e9ecef;
    padding: 8px 14px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.gani-typing-bubble .dot {
    width: 6px;
    height: 6px;
    background: #888888;
    border-radius: 50%;
    animation: ganiBounce 1.4s infinite ease-in-out both;
}

.gani-typing-bubble .dot:nth-child(1) { animation-delay: -0.32s; }
.gani-typing-bubble .dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes ganiBounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

/* Error Banner */
.gani-chat-error {
    background: #e74c3c;
    color: #ffffff;
    padding: 8px 14px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Guest Prompt */
.gani-guest-prompt {
    background: #fff8e7;
    border-bottom: 1px solid #f3e5ab;
    padding: 6px 14px;
    font-size: 11px;
    color: #555555;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.gani-guest-login-link {
    color: #C9A84C;
    font-weight: 700;
    text-decoration: underline;
}

/* Input Footer */
.gani-chat-footer {
    border-top: 1px solid #e9ecef;
    background: #ffffff;
    padding: 10px 14px;
}

.gani-input-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 20px;
    padding: 4px 6px 4px 12px;
}

.gani-input-wrap:focus-within {
    border-color: #C9A84C;
    background: #ffffff;
}

.gani-chat-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 13px;
    resize: none;
    max-height: 80px;
}

.gani-chat-send-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #C9A84C;
    color: #ffffff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    transition: opacity 0.2s ease;
}

.gani-chat-send-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.gani-input-meta {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #888888;
    margin-top: 6px;
    padding: 0 4px;
}

/* Responsive Mobile Layout */
@media (max-width: 576px) {
    .gani-chat-window {
        bottom: 0;
        right: 0;
        width: 100vw;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
    }
}
</style>

<!-- Customer Support Chatbot Client Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const API_BASE = '/api/v1/chat';

    const toggleBtn   = document.getElementById('gani-chat-toggle-btn');
    const toggleIcon  = document.getElementById('gani-chat-toggle-icon');
    const windowEl    = document.getElementById('gani-chat-window');
    const closeBtn    = document.getElementById('gani-chat-close-btn');
    const messagesEl  = document.getElementById('gani-chat-messages');
    const inputEl     = document.getElementById('gani-chat-input');
    const sendBtn     = document.getElementById('gani-chat-send-btn');
    const formEl      = document.getElementById('gani-chat-form');
    const typingEl    = document.getElementById('gani-chat-typing');
    const errorEl     = document.getElementById('gani-chat-error');
    const errorTextEl = document.getElementById('gani-chat-error-text');
    const retryBtn    = document.getElementById('gani-chat-retry-btn');
    const unreadBadge = document.getElementById('gani-chat-unread-badge');
    const charCountEl = document.getElementById('gani-char-count');
    const modeBadge   = document.getElementById('gani-chat-mode-badge');
    const wsDot       = document.getElementById('gani-ws-status-dot');
    const wsText      = document.getElementById('gani-ws-status-text');

    let currentConversation = null;
    let isOpen = false;
    let unreadCount = 0;
    let pollInterval = null;

    // 1. Storage & Guest Token Setup (Secure 64-character CSPRNG token)
    function generateSecure64Token() {
        if (window.crypto && window.crypto.getRandomValues) {
            const array = new Uint8Array(32);
            window.crypto.getRandomValues(array);
            return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
        }
        // Fallback for older browsers
        let result = '';
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        for (let i = 0; i < 64; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    function getGuestToken() {
        let token = localStorage.getItem('gani_guest_chat_token');
        if (!token || token.length !== 64 || !/^[a-zA-Z0-9]{64}$/.test(token)) {
            token = generateSecure64Token();
            localStorage.setItem('gani_guest_chat_token', token);
        }
        return token;
    }

    function getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };
        const guestToken = getGuestToken();
        if (guestToken) {
            headers['X-Guest-Token'] = guestToken;
        }
        return headers;
    }

    // 2. Toggle Open / Close Drawer
    toggleBtn.addEventListener('click', function () {
        isOpen = !isOpen;
        if (isOpen) {
            windowEl.classList.remove('gani-chat-hidden');
            toggleIcon.className = 'fas fa-times';
            unreadCount = 0;
            updateUnreadBadge();
            scrollToBottom();
            inputEl.focus();
            if (!currentConversation) {
                initConversation();
            }
        } else {
            windowEl.classList.add('gani-chat-hidden');
            toggleIcon.className = 'fas fa-comments';
        }
    });

    closeBtn.addEventListener('click', function () {
        isOpen = false;
        windowEl.classList.add('gani-chat-hidden');
        toggleIcon.className = 'fas fa-comments';
    });

    // 3. Input validation and Enter key handler
    inputEl.addEventListener('input', function () {
        const len = inputEl.value.length;
        charCountEl.textContent = len;
        sendBtn.disabled = len === 0 || len > 2000;
        
        // Auto grow height
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(inputEl.scrollHeight, 80) + 'px';
    });

    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) {
                sendMessage();
            }
        }
    });

    formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!sendBtn.disabled) {
            sendMessage();
        }
    });

    retryBtn.addEventListener('click', function () {
        hideError();
        if (currentConversation) {
            loadMessages();
        } else {
            initConversation();
        }
    });

    // 4. Initialize or Fetch Conversation
    function initConversation() {
        hideError();
        fetch(API_BASE + '/conversations/current', { headers: getHeaders() })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data.conversation) {
                    currentConversation = res.data.conversation;
                    renderConversationState();
                    loadMessages();
                } else {
                    createConversation();
                }
            })
            .catch(() => createConversation());
    }

    function createConversation() {
        fetch(API_BASE + '/conversations', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ subject: 'Customer Support Chat' })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data.conversation) {
                currentConversation = res.data.conversation;
                renderConversationState();
            } else {
                showError('Could not start conversation session.');
            }
        })
        .catch(() => showError('Network connection error.'));
    }

    function renderConversationState() {
        if (!currentConversation) return;

        if (currentConversation.mode === 'human') {
            modeBadge.className = 'gani-mode-badge human';
            modeBadge.innerHTML = '<i class="fas fa-user-shield"></i> Human Support';
        } else {
            modeBadge.className = 'gani-mode-badge ai';
            modeBadge.innerHTML = '<i class="fas fa-robot"></i> AI Assistant';
        }
    }

    // 5. Load Messages
    function loadMessages() {
        if (!currentConversation) return;

        fetch(`${API_BASE}/conversations/${currentConversation.uuid}/messages`, { headers: getHeaders() })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data.data) {
                    renderMessages(res.data.data);
                }
            })
            .catch(() => showError('Failed to load message history.'));
    }

    function renderMessages(messages) {
        messagesEl.innerHTML = '';

        messages.forEach(msg => {
            appendMessageToDOM(msg);
        });

        scrollToBottom();
    }

    function appendMessageToDOM(msg) {
        const row = document.createElement('div');
        const role = msg.sender_type || 'system';

        if (role === 'guest' || role === 'customer') {
            row.className = 'gani-msg-row user';
            row.innerHTML = `
                <div class="gani-msg-bubble">
                    ${escapeHtml(msg.message)}
                    <div class="gani-msg-time">${formatTime(msg.created_at)}</div>
                </div>
            `;
        } else if (role === 'ai') {
            row.className = 'gani-msg-row assistant';
            row.innerHTML = `
                <div class="gani-msg-avatar"><i class="fas fa-robot"></i></div>
                <div class="gani-msg-bubble">
                    ${escapeHtml(msg.message)}
                    <div class="gani-msg-time">${formatTime(msg.created_at)}</div>
                </div>
            `;
        } else if (role === 'admin') {
            row.className = 'gani-msg-row admin';
            row.innerHTML = `
                <div class="gani-msg-avatar"><i class="fas fa-user-shield"></i></div>
                <div class="gani-msg-bubble">
                    <strong>Support Agent</strong><br>
                    ${escapeHtml(msg.message)}
                    <div class="gani-msg-time">${formatTime(msg.created_at)}</div>
                </div>
            `;
        } else {
            row.className = 'gani-msg-row system';
            row.innerHTML = `
                <div class="gani-msg-bubble">
                    ${escapeHtml(msg.message)}
                </div>
            `;
        }

        messagesEl.appendChild(row);
    }

    // 6. Send Message
    function sendMessage() {
        const text = inputEl.value.trim();
        if (!text || !currentConversation) return;

        // Append user message immediately
        const tempMsg = {
            sender_type: 'customer',
            message: text,
            created_at: new Date().toISOString()
        };
        appendMessageToDOM(tempMsg);
        scrollToBottom();

        inputEl.value = '';
        inputEl.style.height = 'auto';
        sendBtn.disabled = true;
        charCountEl.textContent = '0';

        showTyping();
        hideError();

        fetch(`${API_BASE}/conversations/${currentConversation.uuid}/messages`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(res => {
            hideTyping();
            if (res.success && res.data.ai_message) {
                appendMessageToDOM(res.data.ai_message);
                scrollToBottom();
            }
            if (res.data && res.data.user_message) {
                // If mode was updated to human
                initConversation();
            }
        })
        .catch(() => {
            hideTyping();
            showError('Message failed to send. Click retry.');
        });
    }

    // Helpers
    function showTyping() { typingEl.classList.remove('d-none'); scrollToBottom(); }
    function hideTyping() { typingEl.classList.add('d-none'); }
    function showError(msg) { errorTextEl.textContent = msg; errorEl.classList.remove('d-none'); }
    function hideError() { errorEl.classList.add('d-none'); }
    function scrollToBottom() { messagesEl.scrollTop = messagesEl.scrollHeight; }

    function updateUnreadBadge() {
        if (unreadCount > 0 && !isOpen) {
            unreadBadge.textContent = unreadCount;
            unreadBadge.classList.remove('d-none');
        } else {
            unreadBadge.classList.add('d-none');
        }
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function formatTime(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Polling fallback (runs every 6 seconds to fetch updates smoothly)
    setInterval(function() {
        if (currentConversation && isOpen) {
            loadMessages();
        }
    }, 6000);
});
</script>
