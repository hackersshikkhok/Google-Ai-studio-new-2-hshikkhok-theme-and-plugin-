<?php
/**
 * HackersShikkhok - Cyber AI Chatbot Drawer / Modal (English UI)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$rest_url   = esc_url_raw( rest_url( 'hackersshikkhok/v1/ai/tutor/ask' ) );
$rest_nonce = wp_create_nonce( 'wp_rest' );
?>
<div id="hs-ai-chatbot-backdrop" class="hs-ai-chatbot-backdrop" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="hs-ai-chatbot-title">
    <div class="hs-ai-chatbot-panel">
        <div class="hs-ai-chatbot-header">
            <div class="hs-ai-chatbot-brand" id="hs-ai-chatbot-title">
                <span class="hs-ai-bot-orb" aria-hidden="true">🤖</span>
                <div>
                    <strong>HackersShikkhok AI</strong>
                    <small>Cyber &amp; Coding Assistant</small>
                </div>
            </div>
            <button type="button" class="hs-ai-chatbot-close" onclick="closeHsAiChatbot()" aria-label="Close AI Chatbot">✕</button>
        </div>

        <div class="hs-ai-chatbot-chips">
            <button type="button" class="hs-ai-chip" onclick="sendHsAiQuickPrompt('Give me a beginner cybersecurity learning roadmap')">🛡️ Cyber Roadmap</button>
            <button type="button" class="hs-ai-chip" onclick="sendHsAiQuickPrompt('Explain OWASP Top 10 clearly')">🔐 OWASP Top 10</button>
            <button type="button" class="hs-ai-chip" onclick="sendHsAiQuickPrompt('How do I use the 2,024+ client-side tools?')">⚡ 2,024+ Tools</button>
        </div>

        <div class="hs-ai-chatbot-messages" id="hs-ai-chatbot-messages">
            <div class="hs-ai-msg hs-ai-msg--bot">
                <div class="hs-ai-msg-bubble">
                    Hello! I am <strong>HackersShikkhok AI</strong>. Ask me any question about cybersecurity, full-stack engineering, or our 2,024+ tools.
                </div>
            </div>
        </div>

        <form class="hs-ai-chatbot-form" onsubmit="submitHsAiChat(event)">
            <input
                type="text"
                id="hs-ai-chat-input"
                class="hs-ai-chat-input"
                placeholder="Ask any cyber or coding question..."
                autocomplete="off"
                required
            />
            <button type="submit" id="hs-ai-chat-send" class="hs-ai-chat-send" aria-label="Send message">
                ➤
            </button>
        </form>
    </div>
</div>

<script>
(function() {
    var restEndpoint = '<?php echo esc_js( $rest_url ); ?>';
    var restNonce = '<?php echo esc_js( $rest_nonce ); ?>';
    var isSending = false;

    window.openHsAiChatbot = function() {
        var el = document.getElementById('hs-ai-chatbot-backdrop');
        if (!el) return;
        el.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(function() {
            var inp = document.getElementById('hs-ai-chat-input');
            if (inp) inp.focus();
        }, 120);
    };

    window.closeHsAiChatbot = function() {
        var el = document.getElementById('hs-ai-chatbot-backdrop');
        if (!el) return;
        el.style.display = 'none';
        document.body.style.overflow = '';
    };

    window.sendHsAiQuickPrompt = function(text) {
        var inp = document.getElementById('hs-ai-chat-input');
        if (inp) {
            inp.value = text;
            submitHsAiChat();
        }
    };

    function appendHsAiMessage(sender, text) {
        var list = document.getElementById('hs-ai-chatbot-messages');
        if (!list) return null;

        var wrap = document.createElement('div');
        wrap.className = 'hs-ai-msg ' + (sender === 'user' ? 'hs-ai-msg--user' : 'hs-ai-msg--bot');

        var bubble = document.createElement('div');
        bubble.className = 'hs-ai-msg-bubble';
        bubble.textContent = text;

        wrap.appendChild(bubble);
        list.appendChild(wrap);
        list.scrollTop = list.scrollHeight;
        return bubble;
    }

    window.submitHsAiChat = function(e) {
        if (e) e.preventDefault();
        if (isSending) return;

        var inp = document.getElementById('hs-ai-chat-input');
        var sendBtn = document.getElementById('hs-ai-chat-send');
        if (!inp) return;

        var query = inp.value.trim();
        if (!query) return;

        appendHsAiMessage('user', query);
        inp.value = '';
        isSending = true;
        if (sendBtn) sendBtn.disabled = true;

        var loadingBubble = appendHsAiMessage('bot', 'Thinking...');

        fetch(restEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': restNonce
            },
            body: JSON.stringify({
                query: query,
                lesson_context: document.title || 'HackersShikkhok Platform',
                mode: 'explain'
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var reply = (res && res.response)
                ? res.response
                : 'Sorry, no response received right now. Please try asking in our Community Q&A Forum.';
            if (loadingBubble) loadingBubble.textContent = reply;
        })
        .catch(function() {
            if (loadingBubble) {
                loadingBubble.textContent = 'Network error occurred. Please try again.';
            }
        })
        .finally(function() {
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
            var list = document.getElementById('hs-ai-chatbot-messages');
            if (list) list.scrollTop = list.scrollHeight;
        });
    };

    document.addEventListener('click', function(e) {
        var backdrop = document.getElementById('hs-ai-chatbot-backdrop');
        if (backdrop && e.target === backdrop) {
            window.closeHsAiChatbot();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeHsAiChatbot();
        }
    });
})();
</script>
