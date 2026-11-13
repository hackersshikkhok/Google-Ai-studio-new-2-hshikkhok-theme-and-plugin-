<?php
/**
 * Cybersecurity Awareness & Defense Lab (Blue-Team Defensive Education)
 * Interactive educational simulator for recognizing, analyzing, and mitigating email threats.
 * Locked for Premium Users (100 BDT / 1 Month unlock).
 * Strictly complies with Responsible Security Education & Google Cloud Usage Policies.
 *
 * @package HackersShikkhok\Core\Security
 */

namespace HackersShikkhok\Theme\Modules\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SecurityAwarenessDefenseLab {

    public static function register(): void {
        add_action( 'init', [ __CLASS__, 'register_rewrites' ] );
        add_filter( 'query_vars', [ __CLASS__, 'register_query_vars' ] );
        add_action( 'template_redirect', [ __CLASS__, 'handle_lab_template' ] );
        add_action( 'wp_head', [ __CLASS__, 'inject_noindex_header' ] );
        add_wp_ajax( 'hs_unlock_security_lab', [ __CLASS__, 'ajax_unlock_lab' ] );
    }

    public static function register_rewrites(): void {
        add_rewrite_rule( '^security-awareness-lab/?$', 'index.php?hs_security_lab=1', 'top' );
    }

    public static function register_query_vars( $vars ) {
        $vars[] = 'hs_security_lab';
        return $vars;
    }

    public static function inject_noindex_header(): void {
        $has_lab_query = ( isset( $GLOBALS['wp_query'] ) && ( $GLOBALS['wp_query'] instanceof \WP_Query ) && function_exists( 'get_query_var' ) && get_query_var( 'hs_security_lab' ) );
        if ( $has_lab_query || ( function_exists( 'is_page' ) && is_page( 'security-awareness-lab' ) ) ) {
            echo '<meta name="robots" content="noindex, nofollow, noarchive, nosnippet" />' . "\n";
        }
    }

    public static function handle_lab_template(): void {
        $has_lab_query = ( isset( $GLOBALS['wp_query'] ) && ( $GLOBALS['wp_query'] instanceof \WP_Query ) && function_exists( 'get_query_var' ) && get_query_var( 'hs_security_lab' ) ) || isset( $_GET['hs_security_lab'] );
        if ( $has_lab_query ) {
            status_header( 200 );
            self::render_lab_screen();
            exit;
        }
    }

    public static function is_user_unlocked( int $user_id ): bool {
        if ( ! $user_id ) {
            return false;
        }
        if ( user_can( $user_id, 'administrator' ) ) {
            return true;
        }
        $expiry = get_user_meta( $user_id, 'hs_security_lab_expiry', true );
        if ( $expiry && intval( $expiry ) > time() ) {
            return true;
        }
        return false;
    }

    public static function ajax_unlock_lab(): void {
        check_ajax_referer( 'hs_security_lab_nonce', 'nonce' );
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( [ 'message' => 'Please login to access this lab.' ] );
        }

        $wallet = \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $user_id );
        $price = 100.00;
        if ( floatval( $wallet['balance_bdt'] ) < $price ) {
            wp_send_json_error( [ 'message' => 'Insufficient balance. Please top up 100 BDT to your wallet.' ] );
        }

        \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::debit( $user_id, $price, 0, 'Security Awareness Lab Unlock (30 Days)' );
        $new_expiry = time() + ( 30 * 86400 );
        update_user_meta( $user_id, 'hs_security_lab_expiry', $new_expiry );

        wp_send_json_success( [
            'message' => 'Lab successfully unlocked! Access granted for 30 days.',
            'expiry'  => date_i18n( 'd M Y, h:i A', $new_expiry ),
        ] );
    }

    public static function render_lab_screen(): void {
        $user_id = get_current_user_id();
        $is_unlocked = self::is_user_unlocked( $user_id );
        ?>
        <!DOCTYPE html>
        <html lang="bn">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cybersecurity Awareness & Defense Lab — HackersShikkhok</title>
            <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>">
            <style>
                body { background: #070b14; color: #f8fafc; font-family: monospace; padding: 20px; }
                .lab-box { max-width: 900px; margin: 40px auto; background: #0f172a; border: 1px solid #00f5d4; border-radius: 12px; padding: 30px; box-shadow: 0 0 30px rgba(0,245,212,0.15); }
                .locked-badge { background: rgba(239,68,68,0.1); border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 8px; font-weight: bold; }
                .unlocked-badge { background: rgba(16,185,129,0.1); border: 1px solid #10b981; color: #10b981; padding: 8px 16px; border-radius: 8px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="lab-box">
                <h1 style="color:#00f5d4; margin-top:0;">🛡️ Cyber Defense Defense &amp; Security Awareness Lab</h1>
                <p style="color:#94a3b8;">
                       :   , SPF/DKIM/DMARC        ।
                </p>

                <?php if ( ! $is_unlocked ) : ?>
                    <div style="text-align:center; padding:40px 20px; background:#050811; border-radius:10px; margin-top:20px;">
                        <span style="font-size:40px;">🔒</span>
                        <h2 style="color:#f8fafc; margin:15px 0 10px 0;">Defense Lab Locked</h2>
                        <p style="color:#94a3b8; max-width:500px; margin:0 auto 20px auto;">
                                          ।
                        </p>
                        <button onclick="unlockLab()" style="background:#00f5d4; color:#050811; font-weight:bold; border:none; padding:12px 28px; border-radius:8px; cursor:pointer; font-size:14px;">
                            🔓 Unlock 1-Month Access for 100 BDT
                        </button>
                    </div>
                <?php else : ?>
                    <div style="background:#050811; padding:25px; border-radius:10px; margin-top:20px;">
                        <span class="unlocked-badge">✓ Premium Defense Lab Active</span>
                        <h3 style="color:#f8fafc; margin-top:20px;"> :     DMARC  </h3>
                        <p style="color:#94a3b8;">
                            Practice email protocol security analysis and detection techniques.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <script>
            function unlockLab() {
                fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'hs_unlock_security_lab',
                        nonce: '<?php echo esc_js( wp_create_nonce( 'hs_security_lab_nonce' ) ); ?>'
                    })
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.data ? data.data.message : 'An error occurred');
                    if (data.success) location.reload();
                });
            }
            </script>
        </body>
        </html>
        <?php
    }
}