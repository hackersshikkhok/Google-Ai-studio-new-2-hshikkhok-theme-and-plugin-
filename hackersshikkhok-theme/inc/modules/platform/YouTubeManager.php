<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class YouTubeManager {
    public const CHANNEL_HANDLE = '@HackersShikkhok';

    public static function register(): void {
        add_shortcode( 'hs_youtube_companion', array( self::class, 'render_companion_shortcode' ) );
        add_action( 'add_meta_boxes', array( self::class, 'register_meta_box' ) );
        add_action( 'save_post', array( self::class, 'save_companion_video_meta' ) );
    }

    public static function register_meta_box(): void {
        add_meta_box(
            'hs_youtube_companion_box',
            __( 'YouTube Companion Video', 'hackersshikkhok-core' ),
            array( self::class, 'render_meta_box' ),
            array( 'tutorials', 'post', 'tools', 'cyber_guide' ),
            'side',
            'default'
        );
    }

    public static function render_meta_box( \WP_Post $post ): void {
        wp_nonce_field( 'hs_save_youtube_meta', 'hs_youtube_meta_nonce' );
        $video_id = get_post_meta( $post->ID, '_hs_youtube_video_id', true );
        ?>
        <p>
            <label for="hs_youtube_video_id"><strong><?php esc_html_e( 'YouTube Video ID or URL:', 'hackersshikkhok-core' ); ?></strong></label>
            <input type="text" id="hs_youtube_video_id" name="hs_youtube_video_id" value="<?php echo esc_attr( (string) $video_id ); ?>" class="widefat" placeholder="e.g. dQw4w9WgXcQ" />
        </p>
        <p class="description"><?php esc_html_e( 'Official Channel: @HackersShikkhok. Enter 11-char ID or full URL.', 'hackersshikkhok-core' ); ?></p>
        <?php
    }

    public static function save_companion_video_meta( int $post_id ): void {
        if ( ! isset( $_POST['hs_youtube_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hs_youtube_meta_nonce'] ), 'hs_save_youtube_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        if ( isset( $_POST['hs_youtube_video_id'] ) ) {
            $raw = sanitize_text_field( wp_unslash( $_POST['hs_youtube_video_id'] ) );
            if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $raw, $match ) ) {
                $raw = $match[1];
            }
            update_post_meta( $post_id, '_hs_youtube_video_id', $raw );
        }
    }

    public static function render_companion_shortcode( array $atts = array() ): string {
        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return '';
        }
        $meta = self::get_companion_video_meta( $post_id );
        if ( empty( $meta['video_id'] ) ) {
            return '';
        }
        $video_id = esc_attr( $meta['video_id'] );
        return '<div class="hs-yt-embed-box">'
             . '<iframe class="hs-yt-iframe" src="https://www.youtube-nocookie.com/embed/' . $video_id . '" title="YouTube companion video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>'
             . '</div>';
    }

    public static function get_companion_video_meta( int $post_id ): array {
        return array(
            'video_id' => sanitize_text_field( (string) get_post_meta( $post_id, '_hs_youtube_video_id', true ) ),
            'channel'  => self::CHANNEL_HANDLE,
        );
    }

    public static function render_channel_hub(): string {
        return '<div class="hs-yt-channel-hub">'
             . '<div class="hs-yt-ambient-orb" aria-hidden="true"></div>'
             . '<div class="hs-yt-hub-header">'
             . '<div class="hs-yt-brand-wrap">'
             . '<div class="hs-yt-avatar-ring"><div class="hs-yt-avatar-inner">📺</div></div>'
             . '<div>'
             . '<span class="hs-yt-broadcast-pill">Official Broadcast</span>'
             . '<h2 class="hs-yt-hub-title">HackersShikkhok YouTube Academy</h2>'
             . '</div>'
             . '</div>'
             . '<a href="https://youtube.com/@HackersShikkhok" target="_blank" rel="noopener noreferrer" class="hs-yt-sub-cta">'
             . '<svg class="hs-yt-svg-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'
             . '<span>Subscribe @HackersShikkhok</span>'
             . '</a>'
             . '</div>'
             . '<div class="hs-yt-hub-grid">'
             . '<div class="hs-yt-video-card">'
             . '<div class="hs-yt-frame-wrap"><iframe class="hs-yt-iframe" src="https://www.youtube-nocookie.com/embed/live_stream?channel=UC_HackersShikkhok" title="Live Cybersecurity Labs" frameborder="0" allowfullscreen loading="lazy"></iframe></div>'
             . '<h4 class="hs-yt-video-title">🔴 Live Ethical Hacking &amp; CTF Streams</h4>'
             . '<p class="hs-yt-video-desc">Watch real-time penetration testing and bug bounty walkthroughs in Bangla.</p>'
             . '</div>'
             . '<div class="hs-yt-video-card">'
             . '<div class="hs-yt-frame-wrap"><iframe class="hs-yt-iframe" src="https://www.youtube-nocookie.com/embed/videoseries?list=PL_HackersShikkhok_Core" title="Core Security Course" frameborder="0" allowfullscreen loading="lazy"></iframe></div>'
             . '<h4 class="hs-yt-video-title">🛡️ Complete Cyber Security Bootcamp</h4>'
             . '<p class="hs-yt-video-desc">Zero to hero offensive security, Linux hardening, and web app pentesting.</p>'
             . '</div>'
             . '<div class="hs-yt-video-card">'
             . '<div class="hs-yt-frame-wrap"><iframe class="hs-yt-iframe" src="https://www.youtube-nocookie.com/embed/videoseries?list=PL_HackersShikkhok_Tools" title="Tool Development" frameborder="0" allowfullscreen loading="lazy"></iframe></div>'
             . '<h4 class="hs-yt-video-title">⚡ Custom Tool Development &amp; Exploit Dev</h4>'
             . '<p class="hs-yt-video-desc">Building custom Python and Bash automation scripts for red teaming.</p>'
             . '</div>'
             . '</div>'
             . '</div>';
    }
}
