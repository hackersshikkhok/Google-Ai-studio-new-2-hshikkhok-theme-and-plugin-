<?php
/**
 * Professional Cyberpunk Search Box & AI Voice Speaker Template Part
 * HackersShikkhok Theme
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user_id = get_current_user_id();
$saved_voice = get_user_meta( $current_user_id, 'hs_ai_voice', true ) ?: 'Ananya';
?>
<div class="hs-search-box-wrapper" style="margin: 16px 0; position: relative;">
    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="hs-search-form" style="display: flex; align-items: center; background: #0b1120; border: 1px solid rgba(0,245,212,0.35); border-radius: 14px; padding: 6px 10px; box-shadow: 0 0 20px rgba(0,245,212,0.15); transition: all 0.3s ease;">
        
        <div style="display: flex; align-items: center; padding-left: 10px; color: #00f5d4; font-size: 18px;">
            🔍
        </div>

        <input type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="সার্চ করুন (Search tutorials, tools, CTF labs)..." class="hs-search-input" style="flex: 1; background: transparent; border: none; color: #ffffff; padding: 12px 14px; font-size: 14px; outline: none; font-family: inherit;" />

        <!-- AI Voice Speaker Reader Button -->
        <div class="hs-speaker-container" style="position: relative; display: inline-flex; align-items: center; margin-right: 6px;">
            <button type="button" id="hs-speaker-btn" class="hs-speaker-btn" title="AI Voice Reader (Listen to Page/Search)" data-voice="<?php echo esc_attr( $saved_voice ); ?>" style="background: linear-gradient(135deg, rgba(0,245,212,0.2), rgba(123,44,191,0.25)); border: 1px solid rgba(0,245,212,0.5); color: #00f5d4; padding: 8px 14px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 12px; transition: all 0.2s;">
                <span class="hs-speaker-icon" style="font-size: 16px;">🔊</span>
                <span class="hs-desktop-only">স্পিকার (<span id="hs-active-voice-label"><?php echo esc_html( $saved_voice ); ?></span>)</span>
                <!-- Soundwave Animation Bars (hidden by default, active when reading) -->
                <span class="hs-soundwave" id="hs-soundwave" style="display: none; align-items: center; gap: 2px; height: 14px;">
                    <span class="hs-bar-w"></span>
                    <span class="hs-bar-w"></span>
                    <span class="hs-bar-w"></span>
                    <span class="hs-bar-w"></span>
                </span>
            </button>
        </div>

        <button type="submit" class="hs-search-submit-btn" style="background: linear-gradient(135deg, #00f5d4, #00bbf9); color: #050811; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 800; font-size: 13px; cursor: pointer; box-shadow: 0 0 15px rgba(0,245,212,0.3); transition: all 0.2s;">
            খুঁজুন ➔
        </button>
    </form>
</div>

<style>
.hs-search-form:focus-within {
    border-color: #00f5d4;
    box-shadow: 0 0 30px rgba(0,245,212,0.35);
}
.hs-speaker-btn:hover {
    background: linear-gradient(135deg, rgba(0,245,212,0.35), rgba(123,44,191,0.4));
    box-shadow: 0 0 15px rgba(0,245,212,0.4);
}
/* Soundwave Animation */
.hs-soundwave .hs-bar-w {
    display: inline-block;
    width: 3px;
    height: 10px;
    background: #00f5d4;
    border-radius: 2px;
    animation: hsWaveAnim 0.6s infinite ease-in-out alternate;
}
.hs-soundwave .hs-bar-w:nth-child(2) { animation-delay: 0.15s; }
.hs-soundwave .hs-bar-w:nth-child(3) { animation-delay: 0.3s; }
.hs-soundwave .hs-bar-w:nth-child(4) { animation-delay: 0.45s; }

@keyframes hsWaveAnim {
    0% { height: 4px; background: #00f5d4; }
    100% { height: 16px; background: #ff007f; box-shadow: 0 0 8px #ff007f; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const speakerBtn = document.getElementById('hs-speaker-btn');
    const soundwave = document.getElementById('hs-soundwave');
    const searchInput = document.querySelector('.hs-search-input');
    
    if (speakerBtn && 'speechSynthesis' in window) {
        let isSpeaking = false;
        let speechUtterance = null;

        speakerBtn.addEventListener('click', function() {
            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                if (soundwave) soundwave.style.display = 'none';
                speakerBtn.style.borderColor = 'rgba(0,245,212,0.5)';
                return;
            }

            // Gather text from search input or page title / first paragraph
            let textToRead = searchInput && searchInput.value.trim() !== '' 
                ? "খুঁজছি: " + searchInput.value 
                : (document.querySelector('h1') ? document.querySelector('h1').innerText : "স্বাগতম হ্যাকার শিক্ষক ডট কম এ।");

            speechUtterance = new SpeechSynthesisUtterance(textToRead);
            speechUtterance.rate = 1.0;
            speechUtterance.pitch = 1.0;

            // Pick voice based on persona
            const voiceName = speakerBtn.getAttribute('data-voice') || 'Ananya';
            const voices = window.speechSynthesis.getVoices();
            if (voices.length > 0) {
                // Try matching Bengali or English voices
                let selectedVoice = voices.find(v => v.lang.includes('bn') || v.lang.includes('en'));
                if (selectedVoice) speechUtterance.voice = selectedVoice;
            }

            speechUtterance.onstart = function() {
                isSpeaking = true;
                if (soundwave) soundwave.style.display = 'inline-flex';
                speakerBtn.style.borderColor = '#ff007f';
            };

            speechUtterance.onend = function() {
                isSpeaking = false;
                if (soundwave) soundwave.style.display = 'none';
                speakerBtn.style.borderColor = 'rgba(0,245,212,0.5)';
            };

            window.speechSynthesis.speak(speechUtterance);
        });
    }
});
</script>
<?php
