<?php
/**
 * Master Single Tool Dedicated Template - HackersShikkhok
 * CPT: tools (or /tools/{slug})
 * Client-Side Runner, Bookmarking, Persistent Star Reviews, Issue Reporting,
 * Server-Side Daily XP Rewards & Dynamic Related Tools.
 */
declare(strict_types=1);

get_header();

global $post;
$post_id      = get_the_ID();
$tool_slug    = get_post_field( 'post_name', $post_id ) ?: 'tool';
$tool_name    = get_the_title();
$tool_desc    = get_the_excerpt() ?: 'High-performance, privacy-first client-side developer and cybersecurity tool built by HackersShikkhok.';
$is_logged_in = is_user_logged_in();
$current_uid  = get_current_user_id();
$rest_nonce   = wp_create_nonce( 'wp_rest' );
$rest_base    = esc_url_raw( rest_url( 'hackersshikkhok/v1' ) );
$login_url    = wp_login_url( get_permalink( $post_id ) ?: home_url( '/tools/' ) );

$all_tools = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' )
    ? array_values( \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools() )
    : array();

$matched_tool = null;
foreach ( $all_tools as $t ) {
    $t_id = (string) ( $t['id'] ?? ( $t['tool_id'] ?? '' ) );
    if ( $t_id === $tool_slug || sanitize_title( $t['name'] ?? '' ) === $tool_slug ) {
        $matched_tool = $t;
        break;
    }
}

$category = $matched_tool['category'] ?? ( get_post_meta( $post_id, '_hs_tool_category', true ) ?: 'Developer Utilities' );
$center   = $matched_tool['center'] ?? ( get_post_meta( $post_id, '_hs_tool_center', true ) ?: 'Developer Lab' );
$pricing  = $matched_tool['pricing'] ?? ( get_post_meta( $post_id, '_hs_tool_pricing', true ) ?: 'free' );
$ext_url  = $matched_tool['tool_url'] ?? ( get_post_meta( $post_id, '_hs_tool_url', true ) ?: '' );

$user_bookmarks = $is_logged_in ? get_user_meta( $current_uid, '_hs_user_bookmarks', true ) : array();
$is_bookmarked  = is_array( $user_bookmarks ) && ( isset( $user_bookmarks[ 'tool_' . $tool_slug ] ) || isset( $user_bookmarks[ $tool_slug ] ) );

$rev_summary = class_exists( '\HackersShikkhok\Theme\Modules\Tools\ToolFactory' )
    ? \HackersShikkhok\Theme\Modules\Tools\ToolFactory::get_tool_reviews( $tool_slug )
    : array( 'avg_rating' => 0.0, 'review_count' => 0, 'reviews' => array() );

$related_tools = array();
foreach ( $all_tools as $rt ) {
    $rt_id = (string) ( $rt['id'] ?? ( $rt['tool_id'] ?? '' ) );
    if ( $rt_id === $tool_slug ) {
        continue;
    }
    if ( strcasecmp( (string) ( $rt['category'] ?? '' ), (string) $category ) === 0 || ( $rt['center'] ?? '' ) === $center ) {
        $related_tools[] = $rt;
        if ( count( $related_tools ) >= 4 ) {
            break;
        }
    }
}
if ( count( $related_tools ) < 3 ) {
    foreach ( $all_tools as $rt ) {
        $rt_id = (string) ( $rt['id'] ?? ( $rt['tool_id'] ?? '' ) );
        if ( $rt_id !== $tool_slug && ! in_array( $rt, $related_tools, true ) ) {
            $related_tools[] = $rt;
            if ( count( $related_tools ) >= 4 ) {
                break;
            }
        }
    }
}

$schema_app = array(
    '@context'            => 'https://schema.org',
    '@type'               => 'SoftwareApplication',
    'name'                => $tool_name . ' — HackersShikkhok',
    'operatingSystem'     => 'All Modern Web Browsers',
    'applicationCategory' => 'DeveloperApplication',
    'offers'              => array( '@type' => 'Offer', 'price' => '0.00', 'priceCurrency' => 'USD' ),
    'description'         => $tool_desc,
    'publisher'           => array( '@type' => 'Organization', 'name' => 'HackersShikkhok', 'url' => home_url( '/' ) ),
);
if ( $rev_summary['review_count'] > 0 ) {
    $schema_app['aggregateRating'] = array(
        '@type'       => 'AggregateRating',
        'ratingValue' => (string) $rev_summary['avg_rating'],
        'reviewCount' => (string) $rev_summary['review_count'],
    );
}
?>

<script type="application/ld+json"><?php echo wp_json_encode( $schema_app, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?></script>

<main id="primary" class="site-main hs-single-tool-page" style="background:#080D17; color:#E8EEF7; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1200px; margin:0 auto;">

        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#929FB2; margin-bottom:20px; font-family:var(--font-mono); flex-wrap:wrap;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#20DCC5; text-decoration:none;">Home</a>
            <span>/</span>
            <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#20DCC5; text-decoration:none;">2,024+ Tools Directory</a>
            <span>/</span>
            <span style="color:#E8EEF7; font-weight:700;"><?php echo esc_html( $tool_name ); ?></span>
        </nav>

        <!-- Header Card -->
        <header style="background:linear-gradient(135deg, #101827, #172435); border:1px solid rgba(32,220,197,0.3); border-radius:20px; padding:28px; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <span style="background:rgba(32,220,197,0.12); color:#20DCC5; border:1px solid rgba(32,220,197,0.35); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px; text-transform:uppercase;"><?php echo esc_html( $category ); ?></span>
                    <span style="background:rgba(139,124,255,0.15); color:#8B7CFF; border:1px solid rgba(139,124,255,0.35); font-size:11px; font-weight:700; padding:3px 12px; border-radius:999px;"><?php echo esc_html( strtoupper( (string) $pricing ) ); ?></span>
                    <span style="background:rgba(234,191,87,0.14); color:#EABF57; font-size:11px; font-weight:700; padding:3px 12px; border-radius:999px;">
                        ★ <?php echo $rev_summary['review_count'] > 0 ? esc_html( $rev_summary['avg_rating'] . ' (' . $rev_summary['review_count'] . ')' ) : 'Unrated'; ?>
                    </span>
                </div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="hs-single-fav-btn" style="min-height:40px; padding:6px 14px; border-radius:8px; cursor:pointer; background:#080D17; border:1px solid rgba(255,255,255,0.14); color:<?php echo $is_bookmarked ? '#EABF57' : '#E8EEF7'; ?>; font-weight:700; font-size:12px;">
                        <?php echo $is_bookmarked ? '★ Bookmarked' : '🔖 Bookmark Tool'; ?>
                    </button>
                    <button type="button" id="hs-open-report-btn" style="min-height:40px; padding:6px 14px; border-radius:8px; cursor:pointer; background:rgba(244,63,94,0.12); border:1px solid rgba(244,63,94,0.3); color:#fb7185; font-weight:700; font-size:12px;">
                        🚩 Report Issue
                    </button>
                    <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#929FB2; font-size:13px; font-weight:600; text-decoration:none;">← Catalog</a>
                </div>
            </div>

            <h1 style="font-size:clamp(1.7rem, 3.2vw, 2.4rem); font-weight:900; color:#E8EEF7; margin:0 0 10px; line-height:1.2;">
                <?php echo esc_html( $tool_name ); ?>
            </h1>
            <p style="color:#929FB2; font-size:15px; margin:0; line-height:1.6; max-width:920px;">
                <?php echo esc_html( $tool_desc ); ?>
            </p>
            <?php if ( ! empty( $ext_url ) ) : ?>
                <div style="margin-top:12px;">
                    <a href="<?php echo esc_url( $ext_url ); ?>" target="_blank" rel="noopener noreferrer" style="color:#20DCC5; font-size:13px; font-weight:700; text-decoration:none;">🔗 Official Reference / Resource ↗</a>
                </div>
            <?php endif; ?>
        </header>

        <!-- Interactive Sandbox -->
        <section class="hs-tool-runner-card" style="background:#101827; border:1px solid rgba(32,220,197,0.35); border-radius:20px; padding:26px; margin-bottom:28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                <h2 style="font-size:16px; font-weight:800; color:#20DCC5; text-transform:uppercase; letter-spacing:0.5px; margin:0;">
                    ⚡ Live In-Browser Sandbox
                </h2>
                <div style="display:flex; gap:8px;">
                    <button type="button" id="hs-load-sample-btn" style="padding:8px 14px; background:#172435; border:1px solid rgba(255,255,255,0.14); color:#E8EEF7; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">📄 Load Sample</button>
                    <button type="button" id="hs-clear-input-btn" style="padding:8px 14px; background:rgba(244,63,94,0.12); border:1px solid rgba(244,63,94,0.3); color:#fb7185; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">✕ Clear</button>
                </div>
            </div>

            <textarea id="hs-tool-input-field" rows="6" placeholder="Enter input payload, JSON, encoded string, or text..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(32,220,197,0.25); border-radius:12px; padding:16px; color:#E8EEF7; font-family:var(--font-mono); font-size:13px; line-height:1.6; resize:vertical; outline:none; margin-bottom:16px;"></textarea>

            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:18px;">
                <button type="button" id="hs-tool-run-btn" style="min-height:46px; padding:12px 28px; background:#20DCC5; color:#080D17; border:none; border-radius:12px; font-size:14px; font-weight:800; cursor:pointer;">
                    🚀 Execute &amp; Process Instantly
                </button>
                <span id="hs-tool-status-badge" style="font-size:12px; font-weight:700; color:#20DCC5; background:rgba(32,220,197,0.12); padding:5px 14px; border-radius:999px; border:1px solid rgba(32,220,197,0.3);">
                    ● 100% Client-Side Engine
                </span>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:10px;">
                <label for="hs-tool-output-field" style="color:#8B7CFF; font-size:13px; font-weight:700; text-transform:uppercase;">Output &amp; Result:</label>
                <div style="display:flex; gap:8px;">
                    <button type="button" id="hs-tool-copy-btn" style="padding:7px 14px; background:rgba(32,220,197,0.12); border:1px solid rgba(32,220,197,0.3); color:#20DCC5; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">📋 Copy</button>
                    <button type="button" id="hs-tool-download-btn" style="padding:7px 14px; background:rgba(139,124,255,0.12); border:1px solid rgba(139,124,255,0.3); color:#8B7CFF; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">💾 Download</button>
                </div>
            </div>
            <textarea id="hs-tool-output-field" rows="6" readonly placeholder="Output appears immediately here..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(139,124,255,0.28); border-radius:12px; padding:16px; color:#20DCC5; font-family:var(--font-mono); font-size:13px; line-height:1.6; resize:vertical; outline:none; margin-bottom:18px;"></textarea>

            <!-- Server-Side Verified Daily Reward -->
            <div style="background:#172435; border:1px solid rgba(32,220,197,0.25); border-radius:14px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <div style="color:#20DCC5; font-weight:800; font-size:13px;">🎁 DAILY OPERATIVE TOOL REWARD</div>
                    <div style="color:#929FB2; font-size:12px;">Earn +10 Scholar XP &amp; BDT 1.00 once per day when practicing with this tool.</div>
                </div>
                <?php if ( $is_logged_in ) : ?>
                    <button type="button" id="hs-claim-tool-reward-btn" style="min-height:44px; padding:10px 20px; background:#20DCC5; color:#080D17; border:none; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer;">
                        ⚡ Claim +10 XP Reward
                    </button>
                <?php else : ?>
                    <a href="<?php echo esc_url( $login_url ); ?>" style="padding:10px 18px; background:#101827; border:1px solid #20DCC5; color:#20DCC5; border-radius:10px; font-size:13px; font-weight:700; text-decoration:none;">
                        Sign In to Claim XP →
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Persistent Star Rating & Community Reviews -->
        <section style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:26px; margin-bottom:28px;">
            <h2 style="color:#E8EEF7; font-size:1.3rem; font-weight:800; margin:0 0 6px;">⭐ Ratings &amp; Community Reviews</h2>
            <p id="hs-single-rev-summary" style="color:#929FB2; font-size:13px; margin:0 0 18px;">
                <?php echo $rev_summary['review_count'] > 0 ? esc_html( $rev_summary['avg_rating'] . ' / 5.0 average from ' . $rev_summary['review_count'] . ' review(s)' ) : 'No reviews yet. Be the first to rate this utility (+15 XP)!'; ?>
            </p>

            <?php if ( $is_logged_in ) : ?>
                <form id="hs-single-review-form" style="background:#172435; border-radius:14px; padding:18px; margin-bottom:18px;">
                    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:10px;">
                        <label for="hs-single-rating" style="font-size:13px; font-weight:700; color:#E8EEF7;">Score:</label>
                        <select id="hs-single-rating" style="background:#080D17; color:#EABF57; border:1px solid rgba(234,191,87,0.35); border-radius:8px; padding:8px 12px; font-weight:700;">
                            <option value="5">★★★★★ (5 - Excellent)</option>
                            <option value="4">★★★★☆ (4 - Very Good)</option>
                            <option value="3">★★★☆☆ (3 - Good)</option>
                            <option value="2">★★☆☆☆ (2 - Fair)</option>
                            <option value="1">★☆☆☆☆ (1 - Needs Work)</option>
                        </select>
                    </div>
                    <textarea id="hs-single-rev-text" rows="3" placeholder="Share feedback or tips for other engineers..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:12px; color:#E8EEF7; font-size:13px; margin-bottom:10px;"></textarea>
                    <button type="submit" style="min-height:42px; padding:10px 20px; background:#20DCC5; color:#080D17; font-weight:800; border:none; border-radius:8px; cursor:pointer; font-size:13px;">Publish Review (+15 XP)</button>
                </form>
            <?php else : ?>
                <p style="font-size:13px; color:#929FB2; margin-bottom:16px;"><a href="<?php echo esc_url( $login_url ); ?>" style="color:#20DCC5; font-weight:700;">Sign in</a> to rate and review this tool.</p>
            <?php endif; ?>

            <div id="hs-single-reviews-list" style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach ( $rev_summary['reviews'] as $rev ) : ?>
                    <div style="background:#080D17; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px;">
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px;">
                            <strong style="color:#E8EEF7;"><?php echo esc_html( (string) ( $rev['author_name'] ?? 'Member' ) ); ?></strong>
                            <span style="color:#EABF57;"><?php echo str_repeat( '★', (int) ( $rev['rating'] ?? 5 ) ); ?></span>
                        </div>
                        <p style="margin:0; color:#929FB2; font-size:13px; line-height:1.5;"><?php echo esc_html( (string) ( $rev['review'] ?? '' ) ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Dynamic Related Tools -->
        <section style="background:#101827; border:1px solid rgba(32,220,197,0.22); border-radius:20px; padding:26px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
                <div>
                    <h3 style="color:#E8EEF7; font-size:1.25rem; font-weight:800; margin:0 0 4px;">🔗 Related Tools in <?php echo esc_html( $category ); ?></h3>
                    <p style="color:#929FB2; font-size:13px; margin:0;">Explore companion client-side tools or level up inside our Cyber Academy.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="padding:10px 18px; background:#20DCC5; color:#080D17; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none;">🎓 Enter Academy</a>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:16px;">
                <?php foreach ( $related_tools as $rt ) :
                    $rt_id = (string) ( $rt['id'] ?? ( $rt['tool_id'] ?? '' ) );
                ?>
                    <a href="<?php echo esc_url( home_url( '/tools/?tool=' . urlencode( $rt_id ) ) ); ?>" style="background:#172435; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                        <div style="color:#20DCC5; font-weight:700; font-size:14px; margin-bottom:4px;"><?php echo esc_html( (string) ( $rt['name'] ?? '' ) ); ?></div>
                        <div style="color:#929FB2; font-size:12px;"><?php echo esc_html( (string) ( $rt['short_desc'] ?? '' ) ); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<!-- Report Modal -->
<div id="hs-single-report-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(8,13,23,0.85); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#101827; border:1px solid rgba(244,63,94,0.45); border-radius:18px; max-width:460px; width:100%; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 style="margin:0; color:#E8EEF7; font-size:1.15rem;">🚩 Report Issue</h3>
            <button type="button" id="hs-close-report-btn" style="background:transparent;border:none;color:#929FB2;font-size:18px;cursor:pointer;">✕</button>
        </div>
        <form id="hs-single-report-form" style="display:flex; flex-direction:column; gap:12px;">
            <select id="hs-single-rep-reason" style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:8px;">
                <option value="Broken Output / Bug">Broken Output / Bug</option>
                <option value="Wrong Metadata / URL">Wrong Metadata / URL</option>
                <option value="Security Concern">Security / Safety Concern</option>
            </select>
            <textarea id="hs-single-rep-details" rows="3" required placeholder="Describe what happened..." style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:8px;"></textarea>
            <button type="submit" style="min-height:42px; background:#f43f5e; color:#fff; font-weight:800; border:none; border-radius:8px; cursor:pointer;">Submit Report</button>
        </form>
    </div>
</div>

<script>
(function() {
    'use strict';
    const restBase   = '<?php echo esc_js( $rest_base ); ?>';
    const nonce      = '<?php echo esc_js( $rest_nonce ); ?>';
    const toolSlug   = <?php echo wp_json_encode( $tool_slug ); ?>;
    const toolName   = <?php echo wp_json_encode( $tool_name ); ?>;
    const postId     = <?php echo (int) $post_id; ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const loginUrl   = '<?php echo esc_js( $login_url ); ?>';

    function toast(msg, isErr) {
        let el = document.getElementById('hs-single-tool-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'hs-single-tool-toast';
            el.style.cssText = 'position:fixed;bottom:80px;right:20px;z-index:10000;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,0.5);';
            document.body.appendChild(el);
        }
        el.style.background = isErr ? '#4c0519' : '#064e3b';
        el.style.border = isErr ? '1px solid #f43f5e' : '1px solid #20DCC5';
        el.style.color = '#E8EEF7';
        el.textContent = msg;
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 3500);
    }

    const inputEl  = document.getElementById('hs-tool-input-field');
    const outputEl = document.getElementById('hs-tool-output-field');
    const runBtn   = document.getElementById('hs-tool-run-btn');
    const sampleBtn = document.getElementById('hs-load-sample-btn');
    const clearBtn  = document.getElementById('hs-clear-input-btn');
    const copyBtn   = document.getElementById('hs-tool-copy-btn');
    const dlBtn     = document.getElementById('hs-tool-download-btn');
    const claimBtn  = document.getElementById('hs-claim-tool-reward-btn');
    const favBtn    = document.getElementById('hs-single-fav-btn');

    if (sampleBtn && inputEl) {
        sampleBtn.addEventListener('click', function() {
            inputEl.value = '{"platform": "HackersShikkhok", "status": "secure", "tools": 2024}';
        });
    }
    if (clearBtn && inputEl && outputEl) {
        clearBtn.addEventListener('click', function() { inputEl.value = ''; outputEl.value = ''; });
    }
    if (runBtn && inputEl && outputEl) {
        runBtn.addEventListener('click', function() {
            const val = inputEl.value.trim();
            if (!val) {
                outputEl.value = '[!] Please enter input data or click Load Sample first.';
                return;
            }
            try {
                if (toolSlug.includes('json') || val.startsWith('{') || val.startsWith('[')) {
                    outputEl.value = JSON.stringify(JSON.parse(val), null, 2);
                } else if (toolSlug.includes('base64')) {
                    outputEl.value = btoa(unescape(encodeURIComponent(val)));
                } else {
                    outputEl.value = '=== PROCESSED CLIENT-SIDE ===\nLength: ' + val.length + ' chars\nWords: ' + val.split(/\s+/).filter(Boolean).length + '\nResult:\n' + val;
                }
            } catch (err) {
                outputEl.value = '[Parse Error]: ' + err.message;
            }
        });
    }
    if (copyBtn && outputEl) {
        copyBtn.addEventListener('click', function() {
            if (!outputEl.value) return;
            navigator.clipboard.writeText(outputEl.value).then(() => toast('✓ Copied output!'));
        });
    }
    if (dlBtn && outputEl) {
        dlBtn.addEventListener('click', function() {
            if (!outputEl.value) return;
            const blob = new Blob([outputEl.value], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = toolSlug + '-result.txt';
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    if (claimBtn) {
        claimBtn.addEventListener('click', function() {
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            claimBtn.disabled = true;
            claimBtn.textContent = 'Verifying...';
            fetch(restBase + '/tools/usage-reward', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ tool_slug: toolSlug })
            })
            .then(r => r.json())
            .then(d => {
                toast(d.message || 'Done!', !d.success);
                claimBtn.textContent = d.awarded ? '✓ Claimed +10 XP & BDT 1' : '✓ Claimed Today';
                claimBtn.style.background = '#10b981';
            })
            .catch(() => {
                claimBtn.disabled = false;
                claimBtn.textContent = '⚡ Claim +10 XP Reward';
                toast('Request failed.', true);
            });
        });
    }

    if (favBtn) {
        favBtn.addEventListener('click', function() {
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            fetch(restBase + '/library/bookmarks/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ item_id: 'tool_' + toolSlug, item_type: 'tool', title: toolName, url: window.location.href })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const saved = !!d.is_bookmarked;
                    favBtn.textContent = saved ? '★ Bookmarked' : '🔖 Bookmark Tool';
                    favBtn.style.color = saved ? '#EABF57' : '#E8EEF7';
                    toast(saved ? '✓ Bookmarked to Dashboard!' : 'Bookmark removed.');
                }
            });
        });
    }

    const revForm = document.getElementById('hs-single-review-form');
    if (revForm) {
        revForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const rating = parseInt(document.getElementById('hs-single-rating').value, 10) || 5;
            const review = document.getElementById('hs-single-rev-text').value.trim();
            if (!review) return;
            fetch(restBase + '/tools/review', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ tool_slug: toolSlug, rating, review })
            })
            .then(r => r.json())
            .then(d => {
                toast(d.message || 'Review updated', !d.success);
                if (d.success) {
                    document.getElementById('hs-single-rev-summary').textContent = `${d.avg_rating} / 5.0 average from ${d.review_count} review(s)`;
                    const list = document.getElementById('hs-single-reviews-list');
                    if (list && Array.isArray(d.reviews)) {
                        list.innerHTML = d.reviews.map(r => `<div style="background:#080D17;border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:14px;"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;"><strong style="color:#E8EEF7;">${String(r.author_name||'').replace(/</g,'&lt;')}</strong><span style="color:#EABF57;">${'★'.repeat(r.rating||5)}</span></div><p style="margin:0;color:#929FB2;font-size:13px;">${String(r.review||'').replace(/</g,'&lt;')}</p></div>`).join('');
                    }
                    document.getElementById('hs-single-rev-text').value = '';
                }
            });
        });
    }

    const repModal = document.getElementById('hs-single-report-modal');
    const openRep = document.getElementById('hs-open-report-btn');
    const closeRep = document.getElementById('hs-close-report-btn');
    const repForm = document.getElementById('hs-single-report-form');
    if (openRep && repModal) openRep.addEventListener('click', () => {
        if (!isLoggedIn) { window.location.href = loginUrl; return; }
        repModal.style.display = 'flex';
    });
    if (closeRep && repModal) closeRep.addEventListener('click', () => { repModal.style.display = 'none'; });
    if (repForm) {
        repForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(restBase + '/tools/report', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    tool_slug: toolSlug,
                    tool_name: toolName,
                    post_id: postId,
                    reason: document.getElementById('hs-single-rep-reason').value,
                    details: document.getElementById('hs-single-rep-details').value.trim()
                })
            })
            .then(r => r.json())
            .then(d => {
                toast(d.message || 'Reported.', !d.success);
                if (d.success) repModal.style.display = 'none';
            });
        });
    }
})();
</script>

<?php get_footer(); ?>
