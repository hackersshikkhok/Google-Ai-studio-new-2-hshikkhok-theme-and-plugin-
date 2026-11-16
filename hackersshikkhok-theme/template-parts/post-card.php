<?php
/**
 * Premium Responsive Cyber Post Card Component
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

$post_id     = (int) get_the_ID();
$categories  = get_the_category( $post_id );
$cat_name    = ! empty( $categories ) ? $categories[0]->name : 'Cyber Security';
$author_id   = (int) get_the_author_meta( 'ID' );
$author_name = get_the_author();
$likes_count = (int) get_post_meta( $post_id, 'hs_likes_count', true );
$views_count = (int) get_post_meta( $post_id, 'hs_views_count', true );
$com_count   = (int) get_comments_number( $post_id );
$card_theme  = $args['card_theme'] ?? 'latest';
$mod_class   = 'hs-feed-card--' . ( in_array( $card_theme, array( 'featured', 'popular', 'latest' ), true ) ? $card_theme : 'latest' );
$badge_mod   = $card_theme === 'featured' ? 'cyan' : ( $card_theme === 'popular' ? 'amber' : 'emerald' );
?>
<article id="post-<?php echo $post_id; ?>" class="hs-feed-card <?php echo esc_attr( $mod_class ); ?>">
    <div class="hs-feed-thumb">
        <a href="<?php the_permalink(); ?>" class="hs-feed-thumb-link">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large', array( 'class' => 'hs-feed-img', 'loading' => 'lazy' ) ); ?>
            <?php else : ?>
                <div class="hs-feed-placeholder hs-feed-placeholder--<?php echo esc_attr( $badge_mod ); ?>" aria-hidden="true">
                    <span>🛡️</span>
                </div>
            <?php endif; ?>
        </a>
        <span class="hs-feed-badge hs-feed-badge--<?php echo esc_attr( $badge_mod ); ?>"><?php echo esc_html( $cat_name ); ?></span>
    </div>

    <div class="hs-feed-body">
        <h3 class="hs-feed-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <p class="hs-feed-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20, '...' ) ); ?></p>

        <div class="hs-feed-footer">
            <div class="hs-feed-author">
                <img src="<?php echo esc_url( get_avatar_url( $author_id, array( 'size' => 32 ) ) ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" loading="lazy" />
                <span><?php echo esc_html( $author_name ); ?></span>
            </div>
            <div class="hs-feed-metrics">
                <button type="button" class="hs-like-btn" data-post-id="<?php echo $post_id; ?>" aria-label="Like post">
                    ❤️ <span class="hs-likes-count"><?php echo esc_html( (string) $likes_count ); ?></span>
                </button>
                <a href="<?php the_permalink(); ?>#comments" aria-label="Comments">💬 <?php echo esc_html( (string) $com_count ); ?></a>
                <span>👁️ <?php echo esc_html( (string) $views_count ); ?></span>
            </div>
        </div>
    </div>
</article>
