<?php
/**
 * Master Archive & Category Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Responsive 2-Column Grid with Cyber Sidebar
 */
declare(strict_types=1);

get_header();
?>
<main id="primary" class="site-main hs-archive-page-wrap">

    <!-- Archive Hero Header -->
    <header class="hs-archive-hero">
        <div class="hs-archive-hero-inner">
            <span class="hs-archive-badge">📁 CATEGORY ARCHIVE</span>
            <h1 class="hs-archive-title"><?php the_archive_title(); ?></h1>
            <div class="hs-archive-desc">
                <?php the_archive_description(); ?>
            </div>
        </div>
    </header>

    <!-- 2-Column Grid Body -->
    <div class="hs-layout-container hs-2col-layout">
        
        <!-- Left Posts Column -->
        <div class="hs-main-content-col">
            <?php if ( have_posts() ) : ?>
                <div class="hs-post-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/post-card' );
                    endwhile;
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
                <!-- Clean Cyber Fallback when no posts are published yet in this category -->
                <div class="hs-no-posts-card hs-cyber-card">
                    <span style="font-size:36px;">🔍</span>
                    <h3 style="color:#00f5d4;margin:12px 0;">No articles published in this archive yet.</h3>
                    <p style="color:#94a3b8;font-size:14px;max-width:540px;margin:0 auto 20px;">
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
