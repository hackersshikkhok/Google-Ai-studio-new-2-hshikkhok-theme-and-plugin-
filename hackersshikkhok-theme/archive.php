<?php
/**
 * Master Archive & Category Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Responsive 2-Column Grid with Subcategories, Sorting & Cyber Sidebar
 */
declare(strict_types=1);

get_header();

$sort = isset( $_GET['sort'] ) ? sanitize_key( (string) wp_unslash( $_GET['sort'] ) ) : 'latest';
if ( ! in_array( $sort, array( 'latest', 'popular', 'discussed' ), true ) ) {
    $sort = 'latest';
}

$queried_obj = get_queried_object();
$child_cats  = array();
if ( is_category() && $queried_obj instanceof WP_Term ) {
    $child_cats = get_categories( array( 'parent' => $queried_obj->term_id, 'hide_empty' => false ) );
}
$top_tags = get_tags( array( 'number' => 10, 'orderby' => 'count', 'order' => 'DESC' ) );

// Re-query cleanly if user selected sort=popular or sort=discussed
$archive_query = null;
if ( 'latest' !== $sort ) {
    global $wp_query;
    $q_args = array_merge(
        $wp_query->query_vars,
        array(
            'orderby' => ( 'popular' === $sort ) ? 'meta_value_num' : 'comment_count',
            'order'   => 'DESC',
        )
    );
    if ( 'popular' === $sort ) {
        $q_args['meta_key'] = 'hs_views_count';
    }
    $archive_query = new WP_Query( $q_args );
}
?>
<main id="primary" class="site-main hs-archive-page-wrap">

    <!-- Archive Hero Header -->
    <header class="hs-archive-hero">
        <div class="hs-archive-hero-inner">
            <span class="hs-archive-badge">📁 KNOWLEDGE ARCHIVE</span>
            <h1 class="hs-archive-title"><?php the_archive_title(); ?></h1>
            <div class="hs-archive-desc">
                <?php the_archive_description(); ?>
            </div>

            <?php if ( ! empty( $child_cats ) ) : ?>
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:16px;">
                    <?php foreach ( $child_cats as $cc ) : ?>
                        <a href="<?php echo esc_url( (string) get_category_link( $cc->term_id ) ); ?>" style="padding:5px 12px; background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:8px; color:#20DCC5; font-size:12px; font-weight:700; text-decoration:none;">
                            <?php echo esc_html( $cc->name ); ?> (<?php echo (int) $cc->count; ?>)
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $top_tags ) && ! is_wp_error( $top_tags ) ) : ?>
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:14px; align-items:center;">
                    <span style="font-size:11px; color:#929FB2; font-weight:700;">Popular Tags:</span>
                    <?php foreach ( $top_tags as $tg ) : ?>
                        <a href="<?php echo esc_url( (string) get_tag_link( $tg->term_id ) ); ?>" style="padding:3px 9px; background:#172435; border-radius:6px; color:#E8EEF7; font-size:11px; text-decoration:none;">
                            #<?php echo esc_html( $tg->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Sort Strip -->
    <div style="max-width:1320px; margin:20px auto 0; padding:0 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div class="hs-forum-tabs" style="margin-bottom:0;">
            <a href="<?php echo esc_url( remove_query_arg( 'sort' ) ); ?>" class="hs-forum-tab-btn <?php echo 'latest' === $sort ? 'is-active' : ''; ?>">⏱️ Latest</a>
            <a href="<?php echo esc_url( add_query_arg( 'sort', 'popular' ) ); ?>" class="hs-forum-tab-btn <?php echo 'popular' === $sort ? 'is-active' : ''; ?>">🔥 Most Viewed</a>
            <a href="<?php echo esc_url( add_query_arg( 'sort', 'discussed' ) ); ?>" class="hs-forum-tab-btn <?php echo 'discussed' === $sort ? 'is-active' : ''; ?>">💬 Most Discussed</a>
        </div>
        <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" style="padding:7px 14px; background:#20DCC5; color:#080D17; border-radius:8px; font-size:12px; font-weight:800; text-decoration:none;">+ Publish Article</a>
    </div>

    <!-- 2-Column Grid Body -->
    <div class="hs-layout-container hs-2col-layout">

        <!-- Left Posts Column -->
        <div class="hs-main-content-col">
            <?php
            $active_loop = $archive_query ?: null;
            $has_items   = $active_loop ? $active_loop->have_posts() : have_posts();
            if ( $has_items ) :
            ?>
                <div class="hs-post-grid">
                    <?php
                    if ( $active_loop ) {
                        while ( $active_loop->have_posts() ) {
                            $active_loop->the_post();
                            get_template_part( 'template-parts/post-card' );
                        }
                        wp_reset_postdata();
                    } else {
                        while ( have_posts() ) {
                            the_post();
                            get_template_part( 'template-parts/post-card' );
                        }
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <div class="hs-pagination-wrap">
                    <?php
                    the_posts_pagination( array(
                        'prev_text' => '&larr; Previous',
                        'next_text' => 'Next &rarr;',
                    ) );
                    ?>
                </div>

            <?php else : ?>
                <div class="hs-no-posts-card hs-cyber-card">
                    <span style="font-size:36px;">🔍</span>
                    <h3 style="color:#20DCC5;margin:12px 0;">No articles published in this archive yet.</h3>
                    <p style="color:#929FB2;font-size:14px;max-width:540px;margin:0 auto 20px;">
                        Explore our 8 Master Academic Faculties, 28 Platform Centers, or launch one of our 2,024+ client-side developer tools below.
                    </p>
                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                        <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-btn-primary">🎓 Explore Academy</a>
                        <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" class="hs-btn-secondary">⚡ Launch 2,024+ Tools</a>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hs-btn-secondary">🏠 Back to Home</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Sticky Sidebar -->
        <?php get_sidebar(); ?>

    </div>

</main>

<?php
get_footer();

