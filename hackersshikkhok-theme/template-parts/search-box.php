<?php
/**
 * Slim Cyberpunk Voice-Input Command Search Box Template Part
 * Supports both 'below_header' (default) and 'top_inline' (Facebook-style compact header mode).
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$search_mode = ( isset( $args['mode'] ) && 'top_inline' === $args['mode'] ) ? 'top_inline' : 'below_header';
$wrap_class  = 'top_inline' === $search_mode ? 'hs-search-box-wrapper hs-search-box-wrapper--inline' : 'hs-search-box-wrapper';
?>
<div class="<?php echo esc_attr( $wrap_class ); ?>">
    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="hs-search-form" id="hs-command-search-form">
        <span class="hs-search-icon" aria-hidden="true">🔍</span>

        <input
            type="search"
            name="s"
            id="hs-search-input-field"
            value="<?php echo esc_attr( get_search_query() ); ?>"
            placeholder="<?php echo esc_attr( 'top_inline' === $search_mode ? 'Search HackerShikkhok...' : 'Search courses, tools, commands, tutorials...' ); ?>"
            class="hs-search-input"
            aria-label="Search site"
        />

        <!-- Interactive Voice Search Input (Speech-to-Text Mic inside Search Box) -->
        <button
            type="button"
            id="hs-voice-search-btn"
            class="hs-speaker-btn hs-voice-mic-btn"
            title="Voice Search (Speak to search)"
            aria-label="Voice Search"
            aria-pressed="false"
        >
            <svg class="hs-voice-mic-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                <line x1="12" y1="19" x2="12" y2="23"></line>
                <line x1="8" y1="23" x2="16" y2="23"></line>
            </svg>
            <span class="hs-soundwave" id="hs-soundwave" aria-hidden="true">
                <span class="hs-bar-w"></span>
                <span class="hs-bar-w"></span>
                <span class="hs-bar-w"></span>
            </span>
        </button>

        <button type="submit" class="hs-search-submit-btn hs-tap-red">
            Search
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var micBtn = document.getElementById('hs-voice-search-btn');
    var soundwave = document.getElementById('hs-soundwave');
    var searchInput = document.getElementById('hs-search-input-field');
    var searchForm = document.getElementById('hs-command-search-form');
    var defaultPlaceholder = searchInput ? searchInput.getAttribute('placeholder') : 'Search courses, tools, commands, tutorials...';

    if (!micBtn || !searchInput || !searchForm) {
        return;
    }

    var SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRec) {
        micBtn.addEventListener('click', function() {
            searchInput.focus();
            searchInput.placeholder = 'Voice input not supported in this browser. Type here...';
            setTimeout(function() {
                searchInput.placeholder = defaultPlaceholder;
            }, 2600);
        });
        return;
    }

    var recognition = null;
    var isListening = false;

    function stopListeningState() {
        isListening = false;
        micBtn.classList.remove('is-speaking');
        micBtn.setAttribute('aria-pressed', 'false');
        if (soundwave) soundwave.classList.remove('is-playing');
        searchInput.placeholder = defaultPlaceholder;
    }

    micBtn.addEventListener('click', function() {
        if (isListening && recognition) {
            recognition.stop();
            stopListeningState();
            return;
        }

        try {
            recognition = new SpeechRec();
            recognition.lang = 'en-US';
            recognition.interimResults = true;
            recognition.maxAlternatives = 1;

            recognition.onstart = function() {
                isListening = true;
                micBtn.classList.add('is-speaking');
                micBtn.setAttribute('aria-pressed', 'true');
                if (soundwave) soundwave.classList.add('is-playing');
                searchInput.placeholder = 'Listening... Speak now';
                searchInput.focus();
            };

            recognition.onresult = function(event) {
                var transcript = '';
                for (var i = event.resultIndex; i < event.results.length; i++) {
                    transcript += event.results[i][0].transcript;
                }
                searchInput.value = transcript.trim();
                if (event.results[event.results.length - 1].isFinal && searchInput.value !== '') {
                    stopListeningState();
                    searchForm.submit();
                }
            };

            recognition.onerror = function() {
                stopListeningState();
            };

            recognition.onend = function() {
                stopListeningState();
            };

            recognition.start();
        } catch (err) {
            stopListeningState();
        }
    });
});
</script>
