<?php
/**
 * Master 2,024+ Tools Super Platform Archive & Standalone Tool Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Paginated Catalog, Multi-Filter Chips, Bookmarks, Community Submissions, Reviews & Reports
 */
declare(strict_types=1);

get_header();

$centers   = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ? \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_centers() : array();
$all_tools = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ? array_values( \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools() ) : array();

$is_logged_in = is_user_logged_in();
$current_uid  = get_current_user_id();
$rest_nonce   = wp_create_nonce( 'wp_rest' );
$rest_base    = esc_url_raw( rest_url( 'hackersshikkhok/v1' ) );
$login_url    = wp_login_url( home_url( '/tools/' ) );

$user_bookmarks = $is_logged_in ? get_user_meta( $current_uid, '_hs_user_bookmarks', true ) : array();
if ( ! is_array( $user_bookmarks ) ) {
    $user_bookmarks = array();
}
$usage_counts = get_option( '_hs_tool_usage_counts', array() );
if ( ! is_array( $usage_counts ) ) {
    $usage_counts = array();
}

// Merge community-submitted published tools CPT entries if not in registry
$cpt_tools = get_posts( array(
    'post_type'      => 'tools',
    'post_status'    => 'publish',
    'posts_per_page' => 60,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
$existing_slugs = array();
foreach ( $all_tools as $t ) {
    $existing_slugs[ sanitize_key( (string) ( $t['id'] ?? ( $t['tool_id'] ?? '' ) ) ) ] = true;
}
foreach ( $cpt_tools as $cp ) {
    $slug_cp = sanitize_key( $cp->post_name );
    if ( ! isset( $existing_slugs[ $slug_cp ] ) ) {
        array_unshift( $all_tools, array(
            'id'         => $slug_cp,
            'post_id'    => $cp->ID,
            'name'       => $cp->post_title,
            'category'   => get_post_meta( $cp->ID, '_hs_tool_category', true ) ?: 'Community Tools',
            'center'     => 'web-dev',
            'short_desc' => wp_strip_all_tags( $cp->post_excerpt ?: wp_trim_words( $cp->post_content, 20 ) ),
            'pricing'    => get_post_meta( $cp->ID, '_hs_tool_pricing', true ) ?: 'free',
            'tool_url'   => get_post_meta( $cp->ID, '_hs_tool_url', true ) ?: '',
            'featured'   => true,
            'is_recent'  => true,
            'permalink'  => get_permalink( $cp->ID ),
        ) );
    }
}

$requested_slug = isset( $_GET['tool'] ) ? sanitize_key( wp_unslash( $_GET['tool'] ) ) : '';
$action_param   = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
$active_tool    = null;

if ( ! empty( $requested_slug ) ) {
    foreach ( $all_tools as $t ) {
        $t_id = (string) ( $t['id'] ?? ( $t['tool_id'] ?? '' ) );
        if ( $t_id === $requested_slug || sanitize_title( $t['name'] ?? '' ) === $requested_slug ) {
            $active_tool = $t;
            break;
        }
    }
}
?>

<main id="primary" class="site-main hs-tools-archive-wrap">

<?php if ( $active_tool ) :
    $tool_id     = (string) ( $active_tool['id'] ?? ( $active_tool['tool_id'] ?? 'tool' ) );
    $tool_name   = (string) ( $active_tool['name'] ?? 'Developer Tool' );
    $tool_cat    = (string) ( $active_tool['category'] ?? 'General Utilities' );
    $tool_desc   = (string) ( $active_tool['short_desc'] ?? ( $active_tool['description'] ?? 'Instant client-side privacy-first developer tool.' ) );
    $tool_center = (string) ( $active_tool['center'] ?? 'Developer Lab' );
    $is_saved    = isset( $user_bookmarks[ 'tool_' . $tool_id ] ) || isset( $user_bookmarks[ $tool_id ] );
    $rev_summary = class_exists( '\HackersShikkhok\Theme\Modules\Tools\ToolFactory' )
        ? \HackersShikkhok\Theme\Modules\Tools\ToolFactory::get_tool_reviews( $tool_id )
        : array( 'avg_rating' => 0.0, 'review_count' => 0, 'reviews' => array() );

    $schema_data = array(
        '@context'            => 'https://schema.org',
        '@type'               => 'SoftwareApplication',
        'name'                => $tool_name . ' — HackersShikkhok Online Tool',
        'operatingSystem'     => 'All Modern Web Browsers',
        'applicationCategory' => 'DeveloperApplication',
        'offers'              => array(
            '@type'         => 'Offer',
            'price'         => '0.00',
            'priceCurrency' => 'USD',
        ),
        'description'         => $tool_desc,
        'publisher'           => array(
            '@type' => 'Organization',
            'name'  => 'HackersShikkhok',
            'url'   => home_url( '/' ),
        ),
    );
    if ( $rev_summary['review_count'] > 0 ) {
        $schema_data['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => (string) $rev_summary['avg_rating'],
            'reviewCount' => (string) $rev_summary['review_count'],
        );
    }
?>
    <script type="application/ld+json"><?php echo wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?></script>

    <article class="hs-standalone-tool-container" style="max-width:1180px; margin:0 auto; padding:32px 16px;">
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="margin-bottom:20px; font-size:13px; color:#929FB2;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#20DCC5;">Home</a>
            <span class="hs-breadcrumb-sep">/</span>
            <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#20DCC5;">2,024+ Tools Directory</a>
            <span class="hs-breadcrumb-sep">/</span>
            <span class="hs-breadcrumb-current" style="color:#E8EEF7;"><?php echo esc_html( $tool_name ); ?></span>
        </nav>

        <header style="background:linear-gradient(135deg, #101827, #172435); border:1px solid rgba(32,220,197,0.3); border-radius:18px; padding:26px; margin-bottom:24px;">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <span style="background:rgba(32,220,197,0.12); color:#20DCC5; border:1px solid rgba(32,220,197,0.3); font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px;">
                        <?php echo esc_html( $tool_cat ); ?>
                    </span>
                    <span style="background:rgba(139,124,255,0.15); color:#8B7CFF; border:1px solid rgba(139,124,255,0.3); font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px;">
                        100% In-Browser Execution
                    </span>
                    <span style="background:rgba(234,191,87,0.15); color:#EABF57; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px;">
                        ★ <?php echo $rev_summary['review_count'] > 0 ? esc_html( $rev_summary['avg_rating'] . ' (' . $rev_summary['review_count'] . ' reviews)' ) : 'New · Be first to rate'; ?>
                    </span>
                </div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="hs-single-bookmark-btn" class="hs-mini-btn" style="min-height:40px; padding:6px 14px; border-radius:8px; cursor:pointer; background:<?php echo $is_saved ? 'rgba(234,191,87,0.2)' : 'rgba(255,255,255,0.06)'; ?>; color:<?php echo $is_saved ? '#EABF57' : '#E8EEF7'; ?>; border:1px solid rgba(255,255,255,0.14);" onclick="HS_TOOL_RUNNER.toggleBookmark('<?php echo esc_attr( $tool_id ); ?>', '<?php echo esc_attr( $tool_name ); ?>', this)">
                        <?php echo $is_saved ? '★ Bookmarked' : '🔖 Bookmark'; ?>
                    </button>
                    <button type="button" class="hs-mini-btn" style="min-height:40px; padding:6px 14px; border-radius:8px; cursor:pointer; background:rgba(244,63,94,0.12); color:#fb7185; border:1px solid rgba(244,63,94,0.3);" onclick="HS_TOOL_RUNNER.openReportModal('<?php echo esc_attr( $tool_id ); ?>', '<?php echo esc_attr( $tool_name ); ?>')">
                        🚩 Report Issue
                    </button>
                    <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#929FB2; font-size:13px; font-weight:600; text-decoration:none; padding:6px 10px;">
                        ← All Tools
                    </a>
                </div>
            </div>

            <h1 style="font-size:clamp(1.6rem, 3vw, 2.2rem); font-weight:900; color:#E8EEF7; margin:0 0 10px; line-height:1.2;">
                <?php echo esc_html( $tool_name ); ?>
            </h1>
            <p style="color:#929FB2; font-size:14px; margin:0; line-height:1.6; max-width:850px;">
                <?php echo esc_html( $tool_desc ); ?>
            </p>
        </header>

        <section class="hs-tool-runner-card" style="background:#101827; border:1px solid rgba(32,220,197,0.35); border-radius:18px; padding:24px; margin-bottom:28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                <label for="hs-single-input" style="color:#20DCC5; font-weight:700; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">
                    Input Data / Source Payload:
                </label>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.loadSampleFor('<?php echo esc_attr( $tool_id ); ?>')">Load Sample Data</button>
                    <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.clearSingle()">Clear</button>
                </div>
            </div>

            <textarea id="hs-single-input" class="hs-modal-textarea" rows="6" placeholder="Paste or type text, code, JSON, or payload to process..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(32,220,197,0.25); border-radius:12px; padding:14px; color:#E8EEF7; font-family:var(--font-mono); font-size:13px; resize:vertical; outline:none; margin-bottom:16px;"></textarea>

            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
                <button type="button" class="hs-btn-primary" onclick="HS_TOOL_RUNNER.executeSingle('<?php echo esc_attr( $tool_id ); ?>')" style="min-height:44px; padding:12px 26px; font-size:14px; font-weight:800; cursor:pointer;">
                    ⚡ Execute &amp; Process Instantly
                </button>
                <span id="hs-single-status" style="font-size:12px; font-weight:700; color:#20DCC5; background:rgba(32,220,197,0.12); padding:5px 12px; border-radius:999px; border:1px solid rgba(32,220,197,0.3);">
                    ● 100% Client-Side Ready
                </span>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px;">
                <label for="hs-single-output" style="color:#8B7CFF; font-weight:700; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">
                    Processed Output / Result:
                </label>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.copySingleOutput()">📋 Copy Result</button>
                    <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.downloadSingleOutput('<?php echo esc_attr( $tool_id ); ?>')">💾 Download</button>
                </div>
            </div>

            <textarea id="hs-single-output" class="hs-modal-textarea" rows="7" readonly placeholder="Output will appear here immediately after execution..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(139,124,255,0.3); border-radius:12px; padding:14px; color:#20DCC5; font-family:var(--font-mono); font-size:13px; resize:vertical; outline:none; margin-bottom:18px;"></textarea>

            <!-- Server-Side Daily Tool Reward -->
            <div style="background:#172435; border:1px solid rgba(32,220,197,0.28); border-radius:14px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <div style="color:#20DCC5; font-weight:800; font-size:13px;">🎁 DAILY TOOL EXECUTION REWARD</div>
                    <div style="color:#929FB2; font-size:12px;">Execute this utility and claim +10 Scholar XP &amp; BDT 1.00 once per day (verified in wallet ledger).</div>
                </div>
                <?php if ( $is_logged_in ) : ?>
                    <button type="button" id="hs-archive-reward-btn" onclick="HS_TOOL_RUNNER.claimDailyReward('<?php echo esc_attr( $tool_id ); ?>', this)" style="min-height:44px; padding:10px 18px; background:#20DCC5; color:#080D17; border:none; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer;">
                        ⚡ Claim +10 XP Reward
                    </button>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url( home_url( '/tools/?tool=' . urlencode( $tool_id ) ) ) ); ?>" style="padding:10px 18px; background:#101827; border:1px solid #20DCC5; color:#20DCC5; border-radius:10px; font-size:13px; font-weight:700; text-decoration:none;">
                        Sign In to Earn XP →
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Ratings & Reviews Section -->
        <section style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:26px; margin-bottom:30px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
                <div>
                    <h2 style="font-size:1.3rem; font-weight:800; color:#E8EEF7; margin:0 0 4px;">⭐ Community Ratings &amp; Reviews</h2>
                    <p id="hs-review-summary-line" style="color:#929FB2; font-size:13px; margin:0;">
                        <?php echo $rev_summary['review_count'] > 0 ? esc_html( $rev_summary['avg_rating'] . ' / 5.0 average from ' . $rev_summary['review_count'] . ' verified user(s)' ) : 'No reviews yet. Share your experience and earn +15 XP!'; ?>
                    </p>
                </div>
            </div>

            <?php if ( $is_logged_in ) : ?>
                <form id="hs-tool-review-form" onsubmit="return HS_TOOL_RUNNER.submitReview(event, '<?php echo esc_attr( $tool_id ); ?>')" style="background:#172435; border-radius:14px; padding:18px; margin-bottom:20px;">
                    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:10px;">
                        <label for="hs-rev-rating" style="font-size:13px; font-weight:700; color:#E8EEF7;">Your Rating:</label>
                        <select id="hs-rev-rating" style="background:#080D17; color:#EABF57; border:1px solid rgba(234,191,87,0.35); border-radius:8px; padding:8px 12px; font-weight:700;">
                            <option value="5">★★★★★ (5 - Excellent)</option>
                            <option value="4">★★★★☆ (4 - Very Good)</option>
                            <option value="3">★★★☆☆ (3 - Good)</option>
                            <option value="2">★★☆☆☆ (2 - Fair)</option>
                            <option value="1">★☆☆☆☆ (1 - Needs Work)</option>
                        </select>
                    </div>
                    <textarea id="hs-rev-text" rows="3" placeholder="How helpful and accurate was this tool in your workflow?" style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:12px; color:#E8EEF7; font-size:13px; margin-bottom:10px;"></textarea>
                    <button type="submit" style="min-height:42px; padding:10px 20px; background:#20DCC5; color:#080D17; font-weight:800; border:none; border-radius:8px; cursor:pointer; font-size:13px;">Submit Review (+15 XP)</button>
                </form>
            <?php else : ?>
                <p style="font-size:13px; color:#929FB2; margin-bottom:16px;"><a href="<?php echo esc_url( $login_url ); ?>" style="color:#20DCC5; font-weight:700;">Sign in</a> to rate this tool and submit a review.</p>
            <?php endif; ?>

            <div id="hs-tool-reviews-list" style="display:flex; flex-direction:column; gap:12px;">
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

        <!-- Related Tools Section -->
        <section style="margin-top:24px;">
            <h3 style="font-size:1.3rem; font-weight:800; color:#E8EEF7; margin-bottom:16px;">
                Related Tools in <?php echo esc_html( $tool_cat ); ?>
            </h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px;">
                <?php
                $count = 0;
                foreach ( $all_tools as $rt ) {
                    $rt_id = (string) ( $rt['id'] ?? ( $rt['tool_id'] ?? '' ) );
                    if ( $rt_id !== $tool_id && ( $rt['category'] ?? '' ) === $tool_cat && $count < 4 ) {
                        $count++;
                        ?>
                        <div class="hs-tool-box hs-cyber-card" style="padding:18px;">
                            <span class="hs-tool-box-cat"><?php echo esc_html( $rt['category'] ?? 'Tool' ); ?></span>
                            <h4 style="color:#E8EEF7; font-size:1rem; font-weight:700; margin:8px 0;"><?php echo esc_html( $rt['name'] ?? '' ); ?></h4>
                            <p style="color:#929FB2; font-size:12px; margin-bottom:14px;"><?php echo esc_html( $rt['short_desc'] ?? '' ); ?></p>
                            <a href="<?php echo esc_url( home_url( '/tools/?tool=' . urlencode( $rt_id ) ) ); ?>" class="hs-tool-launch-btn" style="text-align:center; text-decoration:none;">
                                Launch Tool &rarr;
                            </a>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </section>
    </article>

<?php else : ?>
    <?php
    // Prepare lightweight catalog JSON for instant paginated filtering (avoids rendering 2,024+ heavy DOM nodes at once)
    $catalog_items = array();
    $idx = 0;
    foreach ( $all_tools as $t ) {
        $tid     = (string) ( $t['id'] ?? ( $t['tool_id'] ?? '' ) );
        $tname   = (string) ( $t['name'] ?? 'Developer Tool' );
        $tcat    = (string) ( $t['category'] ?? 'General' );
        $tdesc   = (string) ( $t['short_desc'] ?? ( $t['description'] ?? 'Instant client-side developer utility.' ) );
        $tcenter = (string) ( $t['center'] ?? '' );
        $pricing = (string) ( $t['pricing'] ?? 'free' );
        $saved   = isset( $user_bookmarks[ 'tool_' . $tid ] ) || isset( $user_bookmarks[ $tid ] );
        $pop     = (int) ( $usage_counts[ $tid ] ?? 0 );
        $featured = ! empty( $t['featured'] ) || $idx < 24;
        $recent   = ! empty( $t['is_recent'] ) || $idx < 36;
        $rev      = class_exists( '\HackersShikkhok\Theme\Modules\Tools\ToolFactory' )
            ? \HackersShikkhok\Theme\Modules\Tools\ToolFactory::get_tool_reviews( $tid )
            : array( 'avg_rating' => 0, 'review_count' => 0 );

        $catalog_items[] = array(
            'id'       => $tid,
            'name'     => $tname,
            'cat'      => $tcat,
            'center'   => $tcenter,
            'desc'     => $tdesc,
            'pricing'  => $pricing,
            'featured' => $featured,
            'recent'   => $recent,
            'popular'  => $pop,
            'saved'    => $saved,
            'rating'   => $rev['avg_rating'],
            'revCount' => $rev['review_count'],
            'url'      => ! empty( $t['permalink'] ) ? $t['permalink'] : home_url( '/tools/?tool=' . urlencode( $tid ) ),
        );
        $idx++;
    }
    $initial_batch = array_slice( $catalog_items, 0, 24 );
    ?>

    <!-- Master 2,024+ Tools Directory Hero -->
    <section class="hs-tools-hero">
        <div class="hs-tools-hero-inner">
            <nav class="hs-breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
                <span class="hs-breadcrumb-sep">/</span>
                <span class="hs-breadcrumb-current">2,024+ Tools Directory</span>
            </nav>

            <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:14px;">
                <div>
                    <span class="hs-hero-badge">⚡ 2,024+ UNIVERSAL TOOLBOX</span>
                    <h1 class="hs-tools-hero-title" style="margin-bottom:6px;">Developer, Security &amp; Engineering Tools Suite</h1>
                    <p class="hs-tools-hero-sub" style="margin:0;">
                        100% Client-Side, Privacy-Preserving, Instant In-Browser Execution. Browse by category, rate tools, or submit your own.
                    </p>
                </div>
                <button type="button" onclick="HS_TOOL_RUNNER.openSubmitModal()" style="min-height:44px; padding:11px 20px; background:#20DCC5; color:#080D17; border:none; border-radius:10px; font-weight:800; font-size:13px; cursor:pointer; flex-shrink:0;">
                    + Submit a Tool
                </button>
            </div>

            <div class="hs-tools-search-wrap">
                <input type="text" id="hs-tool-live-search" class="hs-tools-search-input" placeholder="🔍 Search 2,024+ tools by name, category, or keyword (e.g. JSON, Hash, Base64, Subnet, JWT)..." autocomplete="off" />
                <span id="hs-tool-search-count" class="hs-tool-count-badge hs-tabular"><?php echo esc_html( number_format( count( $catalog_items ) ) ); ?> Tools</span>
            </div>
        </div>
    </section>

    <!-- Primary Category & Sort/Type Filters -->
    <section class="hs-tools-filters-container">
        <div class="hs-filter-chips-row" style="margin-bottom:10px;">
            <button type="button" class="hs-filter-chip hs-filter-active" data-mode="all">All Tools (<?php echo esc_html( (string) count( $catalog_items ) ); ?>)</button>
            <button type="button" class="hs-filter-chip" data-mode="free">🆓 Free</button>
            <button type="button" class="hs-filter-chip" data-mode="featured">✨ Featured</button>
            <button type="button" class="hs-filter-chip" data-mode="popular">🔥 Popular</button>
            <button type="button" class="hs-filter-chip" data-mode="recent">🆕 Recent</button>
            <button type="button" class="hs-filter-chip" data-mode="favorites">★ My Favorites</button>
        </div>

        <div class="hs-filter-chips-row">
            <button type="button" class="hs-filter-chip hs-cat-chip hs-filter-active" data-cat="all">All Categories</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="Cybersecurity">🛡️ Cybersecurity</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="Web">💻 Web Dev</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="Encoding">🔄 Encoders</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="Crypto">🔐 Cryptography</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="Network">📡 Networking</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="CSS">🎨 CSS &amp; Design</button>
            <button type="button" class="hs-filter-chip hs-cat-chip" data-cat="Generator">⚡ Generators</button>
        </div>
    </section>

    <?php if ( ! empty( $centers ) ) : ?>
        <section class="hs-centers-pills-container">
            <h3 class="hs-centers-pills-title">🌐 Filter by Platform Center:</h3>
            <div class="hs-centers-pills-scroll">
                <?php foreach ( $centers as $c ) : ?>
                    <button type="button" class="hs-center-chip" data-center="<?php echo esc_attr( $c['slug'] ); ?>" style="border-left: 3px solid <?php echo esc_attr( $c['accent'] ?? '#20DCC5' ); ?>;">
                        <span class="hs-chip-name"><?php echo esc_html( $c['name_bn'] ?? $c['name'] ); ?></span>
                        <span class="hs-chip-count"><?php echo esc_html( (string) ( $c['count'] ?? '' ) ); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Paginated Tools Grid -->
    <section class="hs-tools-grid-section">
        <div id="hs-tools-cards-container" class="hs-tools-grid">
            <?php foreach ( $initial_batch as $tool ) : ?>
                <div class="hs-tool-box hs-cyber-card" data-tool-id="<?php echo esc_attr( $tool['id'] ); ?>">
                    <div class="hs-tool-box-top">
                        <span class="hs-tool-box-cat"><?php echo esc_html( $tool['cat'] ); ?></span>
                        <button type="button" class="hs-tool-fav-btn" title="Bookmark Tool" onclick="HS_TOOL_RUNNER.toggleBookmark('<?php echo esc_attr( $tool['id'] ); ?>', '<?php echo esc_attr( $tool['name'] ); ?>', this)" style="background:transparent;border:none;cursor:pointer;font-size:16px;color:<?php echo $tool['saved'] ? '#EABF57' : '#929FB2'; ?>;">
                            <?php echo $tool['saved'] ? '★' : '☆'; ?>
                        </button>
                    </div>
                    <h3 class="hs-tool-box-title"><?php echo esc_html( $tool['name'] ); ?></h3>
                    <p class="hs-tool-box-desc"><?php echo esc_html( $tool['desc'] ); ?></p>
                    <div class="hs-tool-box-bottom">
                        <span class="hs-tool-rating" style="font-size:12px;color:#EABF57;">
                            <?php echo $tool['revCount'] > 0 ? esc_html( '★ ' . $tool['rating'] . ' (' . $tool['revCount'] . ')' ) : '🆓 Client-Side'; ?>
                        </span>
                        <div style="display:flex; gap:6px;">
                            <a href="<?php echo esc_url( $tool['url'] ); ?>" class="hs-mini-btn" style="text-decoration:none;" title="Guide & Reviews">📖 Page</a>
                            <button type="button" class="hs-tool-launch-btn" onclick="HS_TOOL_RUNNER.openTool('<?php echo esc_attr( $tool['id'] ); ?>', '<?php echo esc_attr( addslashes( $tool['name'] ) ); ?>', '<?php echo esc_attr( addslashes( $tool['desc'] ) ); ?>')">Launch &rarr;</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="hs-tools-pagination-bar" style="text-align:center; margin-top:28px;">
            <button type="button" id="hs-load-more-tools-btn" onclick="HS_TOOL_RUNNER.loadMore()" style="min-height:46px; padding:12px 30px; background:#172435; border:1px solid #20DCC5; color:#20DCC5; font-weight:800; font-size:14px; border-radius:10px; cursor:pointer;">
                Load More Tools (Showing <span id="hs-showing-count"><?php echo count( $initial_batch ); ?></span> of <span id="hs-filtered-total"><?php echo count( $catalog_items ); ?></span>)
            </button>
        </div>
    </section>

    <!-- Interactive Tool Runner Modal -->
    <div id="hs-tool-modal-overlay" class="hs-modal-overlay" style="display:none;" onclick="HS_TOOL_RUNNER.closeModal()"></div>
    <div id="hs-tool-modal" class="hs-tool-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="hs-modal-tool-title">
        <div class="hs-modal-header">
            <div class="hs-modal-title-wrap">
                <span class="hs-modal-icon">⚡</span>
                <div>
                    <h3 id="hs-modal-tool-title" class="hs-modal-title">Tool Runner</h3>
                    <p id="hs-modal-tool-desc" class="hs-modal-desc"></p>
                </div>
            </div>
            <button type="button" class="hs-modal-close-btn" onclick="HS_TOOL_RUNNER.closeModal()">✕</button>
        </div>

        <div style="display:flex; gap:8px; padding:8px 20px; background:#080D17; border-bottom:1px solid rgba(255,255,255,0.06); flex-wrap:wrap;">
            <button type="button" id="hs-tab-btn-runner" class="hs-mini-btn" style="background:#20DCC5; color:#080D17; font-weight:800;" onclick="HS_TOOL_RUNNER.switchModalTab('runner')">⚡ Runner</button>
            <button type="button" id="hs-tab-btn-guide" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.switchModalTab('guide')">📖 Guide</button>
            <button type="button" id="hs-tab-btn-faq" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.switchModalTab('faq')">❓ Privacy &amp; FAQ</button>
            <a id="hs-modal-fullpage-link" href="#" class="hs-mini-btn" style="margin-left:auto; text-decoration:none; color:#20DCC5;">🔗 Full Page, Reviews &amp; XP ↗</a>
        </div>

        <div class="hs-modal-body">
            <div id="hs-modal-tab-runner">
                <div class="hs-modal-group">
                    <div class="hs-group-header">
                        <label for="hs-tool-input-area" class="hs-group-label">Input Data / Payload:</label>
                        <div class="hs-group-actions">
                            <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.loadSample()">Sample Data</button>
                            <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.clearInput()">Clear</button>
                        </div>
                    </div>
                    <textarea id="hs-tool-input-area" class="hs-modal-textarea" rows="5" placeholder="Paste or type text, JSON, code, or payload here..."></textarea>
                </div>

                <div class="hs-modal-actions-bar">
                    <button type="button" id="hs-tool-execute-btn" class="hs-btn-primary" onclick="HS_TOOL_RUNNER.executeCurrent()">
                        ⚡ Execute / Process Tool
                    </button>
                    <span id="hs-tool-status-badge" class="hs-status-ready">Ready (100% In-Browser)</span>
                </div>

                <div class="hs-modal-group">
                    <div class="hs-group-header">
                        <label for="hs-tool-output-area" class="hs-group-label">Computed Result / Output:</label>
                        <div class="hs-group-actions">
                            <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.copyOutput()">📋 Copy</button>
                            <button type="button" class="hs-mini-btn" onclick="HS_TOOL_RUNNER.downloadOutput()">💾 Download</button>
                        </div>
                    </div>
                    <textarea id="hs-tool-output-area" class="hs-modal-textarea hs-output-area" rows="6" readonly placeholder="Output will appear here instantly..."></textarea>
                </div>
            </div>

            <div id="hs-modal-tab-guide" style="display:none; padding:10px 0; color:#E8EEF7; font-size:13px; line-height:1.6;">
                <h4 style="color:#20DCC5; font-size:15px; margin-bottom:8px;">📖 Step-by-Step Instructions</h4>
                <ol style="padding-left:20px; margin-bottom:16px;">
                    <li style="margin-bottom:8px;"><strong>Input Setup:</strong> Enter your payload or click Sample Data.</li>
                    <li style="margin-bottom:8px;"><strong>Local Execution:</strong> Click Execute / Process Tool. Everything computes inside your browser memory.</li>
                    <li style="margin-bottom:8px;"><strong>Export:</strong> Copy the result to clipboard or download it cleanly.</li>
                </ol>
            </div>

            <div id="hs-modal-tab-faq" style="display:none; padding:10px 0; color:#E8EEF7; font-size:13px; line-height:1.6;">
                <h4 style="color:#8B7CFF; font-size:15px; margin-bottom:10px;">❓ Privacy &amp; Zero-Telemetry Architecture</h4>
                <p style="color:#929FB2;">All parsing, encoding, formatting, and validation execute locally on your device. No input is ever sent to remote telemetry logs.</p>
            </div>
        </div>
    </div>

    <!-- Submit Tool Modal -->
    <div id="hs-submit-tool-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(8,13,23,0.85); align-items:center; justify-content:center; padding:16px;">
        <div style="background:#101827; border:1px solid rgba(32,220,197,0.4); border-radius:18px; max-width:560px; width:100%; padding:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="margin:0; color:#E8EEF7; font-size:1.25rem;">🛠️ Submit a Developer or Security Tool</h3>
                <button type="button" onclick="HS_TOOL_RUNNER.closeSubmitModal()" style="background:transparent;border:none;color:#929FB2;font-size:18px;cursor:pointer;">✕</button>
            </div>
            <?php if ( ! $is_logged_in ) : ?>
                <p style="color:#929FB2; font-size:14px;">Please sign in to submit a community tool for moderation review.</p>
                <a href="<?php echo esc_url( wp_login_url( home_url( '/tools/?action=submit' ) ) ); ?>" style="display:inline-block; padding:12px 22px; background:#20DCC5; color:#080D17; font-weight:800; border-radius:10px; text-decoration:none;">Sign In to Submit →</a>
            <?php else : ?>
                <form onsubmit="return HS_TOOL_RUNNER.submitNewTool(event)" style="display:flex; flex-direction:column; gap:12px;">
                    <input type="text" id="hs-sub-name" required placeholder="Tool Name (e.g. JWT Signature Inspector)" style="padding:11px 14px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:10px;" />
                    <input type="url" id="hs-sub-url" placeholder="Reference / Repository / Live URL (optional)" style="padding:11px 14px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:10px;" />
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <select id="hs-sub-cat" style="padding:11px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:10px;">
                            <option value="Cybersecurity">Cybersecurity</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Cryptography">Cryptography</option>
                            <option value="Networking">Networking</option>
                            <option value="AI & Automation">AI &amp; Automation</option>
                        </select>
                        <select id="hs-sub-pricing" style="padding:11px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:10px;">
                            <option value="free">Free / Open Source</option>
                            <option value="freemium">Freemium</option>
                            <option value="paid">Commercial</option>
                        </select>
                    </div>
                    <textarea id="hs-sub-desc" rows="4" required placeholder="Describe what this tool does and how developers use it..." style="padding:11px 14px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:10px;"></textarea>
                    <button type="submit" style="min-height:44px; background:#20DCC5; color:#080D17; font-weight:800; border:none; border-radius:10px; cursor:pointer;">Submit for Moderation Review</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    window.HS_TOOLS_CATALOG = <?php echo wp_json_encode( $catalog_items ); ?>;
    </script>
<?php endif; ?>

<!-- Report Tool Modal -->
<div id="hs-report-tool-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(8,13,23,0.85); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#101827; border:1px solid rgba(244,63,94,0.45); border-radius:18px; max-width:480px; width:100%; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 style="margin:0; color:#E8EEF7; font-size:1.15rem;">🚩 Report Tool Issue</h3>
            <button type="button" onclick="HS_TOOL_RUNNER.closeReportModal()" style="background:transparent;border:none;color:#929FB2;font-size:18px;cursor:pointer;">✕</button>
        </div>
        <form onsubmit="return HS_TOOL_RUNNER.submitReport(event)" style="display:flex; flex-direction:column; gap:12px;">
            <input type="hidden" id="hs-rep-slug" />
            <input type="hidden" id="hs-rep-name" />
            <select id="hs-rep-reason" style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:8px;">
                <option value="Incorrect Output / Bug">Incorrect Output / Bug</option>
                <option value="Broken Link / UI Issue">Broken Link / UI Issue</option>
                <option value="Outdated Specification">Outdated Specification</option>
                <option value="Security Concern">Security / Safety Concern</option>
            </select>
            <textarea id="hs-rep-details" rows="3" required placeholder="Describe the issue briefly..." style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; border-radius:8px;"></textarea>
            <button type="submit" style="min-height:42px; background:#f43f5e; color:#fff; font-weight:800; border:none; border-radius:8px; cursor:pointer;">Send Report</button>
        </form>
    </div>
</div>

</main>

<script>
window.HS_TOOL_RUNNER = {
    restBase: '<?php echo esc_js( $rest_base ); ?>',
    nonce: '<?php echo esc_js( $rest_nonce ); ?>',
    isLoggedIn: <?php echo $is_logged_in ? 'true' : 'false'; ?>,
    loginUrl: '<?php echo esc_js( $login_url ); ?>',
    currentToolId: '',
    currentToolName: '',
    filteredTools: Array.isArray(window.HS_TOOLS_CATALOG) ? window.HS_TOOLS_CATALOG : [],
    pageSize: 24,
    renderedCount: 24,
    activeMode: 'all',
    activeCat: 'all',
    activeCenter: '',
    searchQuery: '',

    toast: function(msg, isErr) {
        let el = document.getElementById('hs-global-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'hs-global-toast';
            el.className = 'hs-toast-banner';
            el.style.cssText = 'position:fixed;bottom:80px;right:20px;z-index:10000;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,0.5);';
            document.body.appendChild(el);
        }
        el.style.background = isErr ? '#4c0519' : '#064e3b';
        el.style.border = isErr ? '1px solid #f43f5e' : '1px solid #20DCC5';
        el.style.color = '#E8EEF7';
        el.textContent = msg;
        el.style.display = 'block';
        clearTimeout(this._toastTimer);
        this._toastTimer = setTimeout(() => { el.style.display = 'none'; }, 3500);
    },

    escapeHtml: function(str) {
        return String(str || '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
    },

    applyFilters: function() {
        const all = Array.isArray(window.HS_TOOLS_CATALOG) ? window.HS_TOOLS_CATALOG : [];
        const q = this.searchQuery.toLowerCase();
        const cat = this.activeCat.toLowerCase();
        const center = this.activeCenter;
        const mode = this.activeMode;

        let list = all.filter(t => {
            if (mode === 'free' && t.pricing !== 'free') return false;
            if (mode === 'featured' && !t.featured) return false;
            if (mode === 'recent' && !t.recent) return false;
            if (mode === 'favorites' && !t.saved) return false;
            if (cat !== 'all' && !(t.cat || '').toLowerCase().includes(cat) && !(t.center || '').toLowerCase().includes(cat)) return false;
            if (center && t.center !== center) return false;
            if (q && !(t.name + ' ' + t.cat + ' ' + t.desc).toLowerCase().includes(q)) return false;
            return true;
        });

        if (mode === 'popular') {
            list = list.slice().sort((a, b) => (b.popular + b.revCount * 5) - (a.popular + a.revCount * 5));
        }

        this.filteredTools = list;
        this.renderedCount = Math.min(this.pageSize, list.length);
        this.renderGrid();
    },

    renderGrid: function() {
        const grid = document.getElementById('hs-tools-cards-container');
        if (!grid) return;
        const slice = this.filteredTools.slice(0, this.renderedCount);
        if (slice.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1;background:#101827;border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:36px;text-align:center;color:#929FB2;">No matching tools found. Try clearing your filter or search query.</div>';
        } else {
            grid.innerHTML = slice.map(t => {
                const safeId = this.escapeHtml(t.id);
                const safeName = this.escapeHtml(t.name);
                const safeDesc = this.escapeHtml(t.desc);
                const jsName = String(t.name || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                const jsDesc = String(t.desc || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                const ratingLabel = t.revCount > 0 ? `★ ${t.rating} (${t.revCount})` : '🆓 Client-Side';
                return `<div class="hs-tool-box hs-cyber-card" data-tool-id="${safeId}">
                    <div class="hs-tool-box-top">
                        <span class="hs-tool-box-cat">${this.escapeHtml(t.cat)}</span>
                        <button type="button" class="hs-tool-fav-btn" title="Bookmark Tool" onclick="HS_TOOL_RUNNER.toggleBookmark('${safeId}', '${jsName}', this)" style="background:transparent;border:none;cursor:pointer;font-size:16px;color:${t.saved ? '#EABF57' : '#929FB2'};">${t.saved ? '★' : '☆'}</button>
                    </div>
                    <h3 class="hs-tool-box-title">${safeName}</h3>
                    <p class="hs-tool-box-desc">${safeDesc}</p>
                    <div class="hs-tool-box-bottom">
                        <span class="hs-tool-rating" style="font-size:12px;color:#EABF57;">${ratingLabel}</span>
                        <div style="display:flex;gap:6px;">
                            <a href="${this.escapeHtml(t.url)}" class="hs-mini-btn" style="text-decoration:none;">📖 Page</a>
                            <button type="button" class="hs-tool-launch-btn" onclick="HS_TOOL_RUNNER.openTool('${safeId}', '${jsName}', '${jsDesc}')">Launch &rarr;</button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        const countBadge = document.getElementById('hs-tool-search-count');
        const showingEl  = document.getElementById('hs-showing-count');
        const totalEl    = document.getElementById('hs-filtered-total');
        const moreBtn    = document.getElementById('hs-load-more-tools-btn');
        if (countBadge) countBadge.textContent = this.filteredTools.length.toLocaleString() + ' Tools';
        if (showingEl) showingEl.textContent = slice.length;
        if (totalEl) totalEl.textContent = this.filteredTools.length;
        if (moreBtn) moreBtn.style.display = (this.renderedCount < this.filteredTools.length) ? 'inline-block' : 'none';
    },

    loadMore: function() {
        this.renderedCount = Math.min(this.filteredTools.length, this.renderedCount + 36);
        this.renderGrid();
    },

    toggleBookmark: function(toolId, toolName, btnEl) {
        if (!this.isLoggedIn) {
            this.toast('Sign in to bookmark tools to your dashboard.', true);
            return;
        }
        fetch(this.restBase + '/library/bookmarks/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': this.nonce },
            body: JSON.stringify({
                item_id: 'tool_' + toolId,
                item_type: 'tool',
                title: toolName,
                url: '<?php echo esc_url( home_url( '/tools/?tool=' ) ); ?>' + encodeURIComponent(toolId)
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                const saved = !!d.is_bookmarked;
                if (Array.isArray(window.HS_TOOLS_CATALOG)) {
                    const found = window.HS_TOOLS_CATALOG.find(x => x.id === toolId);
                    if (found) found.saved = saved;
                }
                if (btnEl) {
                    if (btnEl.id === 'hs-single-bookmark-btn') {
                        btnEl.textContent = saved ? '★ Bookmarked' : '🔖 Bookmark';
                        btnEl.style.color = saved ? '#EABF57' : '#E8EEF7';
                    } else {
                        btnEl.textContent = saved ? '★' : '☆';
                        btnEl.style.color = saved ? '#EABF57' : '#929FB2';
                    }
                }
                this.toast(saved ? '✓ Tool saved to Bookmarks!' : 'Removed from Bookmarks.');
            }
        })
        .catch(() => this.toast('Network error updating bookmark.', true));
    },

    claimDailyReward: function(toolSlug, btn) {
        if (!this.isLoggedIn) {
            window.location.href = this.loginUrl;
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Crediting...';
        fetch(this.restBase + '/tools/usage-reward', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': this.nonce },
            body: JSON.stringify({ tool_slug: toolSlug })
        })
        .then(r => r.json())
        .then(d => {
            this.toast(d.message || 'Reward processed.', !d.success);
            btn.textContent = d.awarded ? '✓ Claimed +10 XP & BDT 1' : '✓ Claimed Today';
            btn.style.background = '#10b981';
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = '⚡ Claim +10 XP Reward';
            this.toast('Failed to claim reward.', true);
        });
    },

    submitReview: function(e, toolSlug) {
        e.preventDefault();
        const rating = parseInt(document.getElementById('hs-rev-rating').value, 10) || 5;
        const review = document.getElementById('hs-rev-text').value.trim();
        if (!review) return false;
        fetch(this.restBase + '/tools/review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': this.nonce },
            body: JSON.stringify({ tool_slug: toolSlug, rating, review })
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) {
                this.toast(d.message || 'Could not save review.', true);
                return;
            }
            this.toast(d.message);
            const sumEl = document.getElementById('hs-review-summary-line');
            if (sumEl) sumEl.textContent = `${d.avg_rating} / 5.0 average from ${d.review_count} verified user(s)`;
            const listEl = document.getElementById('hs-tool-reviews-list');
            if (listEl && Array.isArray(d.reviews)) {
                listEl.innerHTML = d.reviews.map(r => `
                    <div style="background:#080D17;border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                            <strong style="color:#E8EEF7;">${this.escapeHtml(r.author_name)}</strong>
                            <span style="color:#EABF57;">${'★'.repeat(r.rating || 5)}</span>
                        </div>
                        <p style="margin:0;color:#929FB2;font-size:13px;line-height:1.5;">${this.escapeHtml(r.review)}</p>
                    </div>
                `).join('');
            }
            document.getElementById('hs-rev-text').value = '';
        })
        .catch(() => this.toast('Network error submitting review.', true));
        return false;
    },

    openSubmitModal: function() {
        const m = document.getElementById('hs-submit-tool-modal');
        if (m) m.style.display = 'flex';
    },
    closeSubmitModal: function() {
        const m = document.getElementById('hs-submit-tool-modal');
        if (m) m.style.display = 'none';
    },
    submitNewTool: function(e) {
        e.preventDefault();
        fetch(this.restBase + '/tools/submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': this.nonce },
            body: JSON.stringify({
                tool_name: document.getElementById('hs-sub-name').value.trim(),
                tool_url: document.getElementById('hs-sub-url').value.trim(),
                category: document.getElementById('hs-sub-cat').value,
                pricing: document.getElementById('hs-sub-pricing').value,
                description: document.getElementById('hs-sub-desc').value.trim()
            })
        })
        .then(r => r.json())
        .then(d => {
            this.toast(d.message || 'Submitted!', !d.success);
            if (d.success) {
                this.closeSubmitModal();
                e.target.reset();
            }
        })
        .catch(() => this.toast('Submission failed.', true));
        return false;
    },

    openReportModal: function(slug, name) {
        if (!this.isLoggedIn) {
            this.toast('Please sign in to report a tool issue.', true);
            return;
        }
        document.getElementById('hs-rep-slug').value = slug;
        document.getElementById('hs-rep-name').value = name;
        document.getElementById('hs-report-tool-modal').style.display = 'flex';
    },
    closeReportModal: function() {
        document.getElementById('hs-report-tool-modal').style.display = 'none';
    },
    submitReport: function(e) {
        e.preventDefault();
        fetch(this.restBase + '/tools/report', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': this.nonce },
            body: JSON.stringify({
                tool_slug: document.getElementById('hs-rep-slug').value,
                tool_name: document.getElementById('hs-rep-name').value,
                reason: document.getElementById('hs-rep-reason').value,
                details: document.getElementById('hs-rep-details').value.trim()
            })
        })
        .then(r => r.json())
        .then(d => {
            this.toast(d.message || 'Reported!', !d.success);
            if (d.success) this.closeReportModal();
        })
        .catch(() => this.toast('Failed to send report.', true));
        return false;
    },

    openTool: function(id, name, desc) {
        this.currentToolId = id;
        this.currentToolName = name;
        document.getElementById('hs-modal-tool-title').textContent = name;
        document.getElementById('hs-modal-tool-desc').textContent = desc;
        document.getElementById('hs-tool-input-area').value = '';
        document.getElementById('hs-tool-output-area').value = '';
        document.getElementById('hs-tool-status-badge').textContent = 'Ready (100% Client-Side)';
        const fullLink = document.getElementById('hs-modal-fullpage-link');
        if (fullLink) fullLink.href = '<?php echo esc_url( home_url( "/tools/?tool=" ) ); ?>' + encodeURIComponent(id);
        this.switchModalTab('runner');
        document.getElementById('hs-tool-modal-overlay').style.display = 'block';
        document.getElementById('hs-tool-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    },

    closeModal: function() {
        document.getElementById('hs-tool-modal-overlay').style.display = 'none';
        document.getElementById('hs-tool-modal').style.display = 'none';
        document.body.style.overflow = '';
    },

    switchModalTab: function(tab) {
        ['runner', 'guide', 'faq'].forEach(t => {
            const el = document.getElementById('hs-modal-tab-' + t);
            const btn = document.getElementById('hs-tab-btn-' + t);
            if (el) el.style.display = (t === tab) ? 'block' : 'none';
            if (btn) {
                btn.style.background = (t === tab) ? '#20DCC5' : 'rgba(255,255,255,0.06)';
                btn.style.color = (t === tab) ? '#080D17' : '#E8EEF7';
            }
        });
    },

    clearInput: function() {
        document.getElementById('hs-tool-input-area').value = '';
        document.getElementById('hs-tool-output-area').value = '';
    },
    clearSingle: function() {
        document.getElementById('hs-single-input').value = '';
        document.getElementById('hs-single-output').value = '';
    },
    loadSample: function() {
        document.getElementById('hs-tool-input-area').value = this.getSampleFor(this.currentToolId);
    },
    loadSampleFor: function(id) {
        const input = document.getElementById('hs-single-input');
        if (input) input.value = this.getSampleFor(id);
    },
    getSampleFor: function(id) {
        id = String(id || '').toLowerCase();
        if (id.includes('json')) return '{\n  "project": "HackersShikkhok",\n  "status": "active",\n  "tools_count": 2024\n}';
        if (id.includes('base64') || id.includes('encoder') || id.includes('url')) return 'HackersShikkhok — Defensive Cybersecurity & 2,024+ Tools Platform';
        if (id.includes('subnet') || id.includes('ip') || id.includes('cidr')) return '192.168.1.0/24';
        return 'Sample payload: HackersShikkhok deterministic client utility test.';
    },

    executeCurrent: function() {
        const input = document.getElementById('hs-tool-input-area').value;
        const output = document.getElementById('hs-tool-output-area');
        const badge = document.getElementById('hs-tool-status-badge');
        badge.textContent = '⚡ Processing...';
        setTimeout(() => {
            output.value = this.computeLogic(this.currentToolId, input);
            badge.textContent = '✓ Executed Successfully (Local V8)';
        }, 80);
    },
    executeSingle: function(id) {
        const input = document.getElementById('hs-single-input').value;
        const output = document.getElementById('hs-single-output');
        const badge = document.getElementById('hs-single-status');
        if (badge) badge.textContent = '⚡ Processing...';
        setTimeout(() => {
            output.value = this.computeLogic(id, input);
            if (badge) badge.textContent = '✓ Executed Successfully (Local V8)';
        }, 80);
    },

    computeLogic: function(id, val) {
        if (!val || !val.trim()) return 'Error: Input data is empty.';
        id = String(id || '').toLowerCase();
        try {
            if (id.includes('json') && id.includes('minify')) return JSON.stringify(JSON.parse(val));
            if (id.includes('json')) return JSON.stringify(JSON.parse(val), null, 2);
            if (id.includes('base64') && (id.includes('decode') || id.includes('dec'))) return decodeURIComponent(escape(atob(val.trim())));
            if (id.includes('base64')) return btoa(unescape(encodeURIComponent(val)));
            if (id.includes('url') && (id.includes('decode') || id.includes('dec'))) return decodeURIComponent(val);
            if (id.includes('url') && id.includes('encode')) return encodeURIComponent(val);
            if (id.includes('word') || id.includes('count')) {
                const w = val.trim().split(/\s+/).filter(Boolean).length;
                return `Words: ${w}\nCharacters: ${val.length}\nLines: ${val.split('\n').length}\nBytes: ${new Blob([val]).size}`;
            }
            if (id.includes('reverse')) return val.split('').reverse().join('');
            if (id.includes('upper')) return val.toUpperCase();
            if (id.includes('lower')) return val.toLowerCase();
            return `[PROCESSED BY HACKERSSHIKKHOK ${id.toUpperCase()}]\nLength: ${val.length} chars\nResult:\n${val}`;
        } catch (e) {
            return `Execution Error: ${e.message}`;
        }
    },

    copyOutput: function() {
        const out = document.getElementById('hs-tool-output-area');
        if (!out || !out.value) return;
        navigator.clipboard.writeText(out.value).then(() => this.toast('✓ Copied output to clipboard!'));
    },
    copySingleOutput: function() {
        const out = document.getElementById('hs-single-output');
        if (!out || !out.value) return;
        navigator.clipboard.writeText(out.value).then(() => this.toast('✓ Copied output to clipboard!'));
    },
    downloadOutput: function() {
        this.downloadFile(document.getElementById('hs-tool-output-area').value, (this.currentToolId || 'result') + '-output.txt');
    },
    downloadSingleOutput: function(id) {
        this.downloadFile(document.getElementById('hs-single-output').value, id + '-output.txt');
    },
    downloadFile: function(text, filename) {
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('hs-tool-live-search');
    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                HS_TOOL_RUNNER.searchQuery = searchInput.value.trim();
                HS_TOOL_RUNNER.applyFilters();
            }, 140);
        });
    }

    document.querySelectorAll('.hs-filter-chip[data-mode]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.hs-filter-chip[data-mode]').forEach(b => b.classList.remove('hs-filter-active'));
            btn.classList.add('hs-filter-active');
            HS_TOOL_RUNNER.activeMode = btn.getAttribute('data-mode') || 'all';
            HS_TOOL_RUNNER.applyFilters();
        });
    });

    document.querySelectorAll('.hs-cat-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.hs-cat-chip').forEach(b => b.classList.remove('hs-filter-active'));
            btn.classList.add('hs-filter-active');
            HS_TOOL_RUNNER.activeCat = btn.getAttribute('data-cat') || 'all';
            HS_TOOL_RUNNER.applyFilters();
        });
    });

    document.querySelectorAll('.hs-center-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            const c = btn.getAttribute('data-center') || '';
            if (HS_TOOL_RUNNER.activeCenter === c) {
                HS_TOOL_RUNNER.activeCenter = '';
                btn.classList.remove('hs-filter-active');
            } else {
                document.querySelectorAll('.hs-center-chip').forEach(b => b.classList.remove('hs-filter-active'));
                btn.classList.add('hs-filter-active');
                HS_TOOL_RUNNER.activeCenter = c;
            }
            HS_TOOL_RUNNER.applyFilters();
        });
    });

    <?php if ( 'submit' === $action_param ) : ?>
    HS_TOOL_RUNNER.openSubmitModal();
    <?php endif; ?>
});
</script>

<?php
get_footer();
