<?php
/**
 * Premium Responsive Cyber Post Card Component
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

$post_id     = get_the_ID();
$categories  = get_the_category( $post_id );
$cat_name    = ! empty( $categories ) ? $categories[0]->name : 'Cyber Security';
$author_id   = get_the_author_meta( 'ID' );
$author_name = get_the_author();
$likes_count = (int) get_post_meta( $post_id, 'hs_likes_count', true ) ?: rand( 12, 180 );
$views_count = (int) get_post_meta( $post_id, 'hs_views_count', true ) ?: rand( 150, 2400 );
$com_count   = (int) get_comments_number( $post_id );
$post_date   = get_the_date( 'M j, Y' );
$card_theme  = $args['card_theme'] ?? 'latest'; // 'featured', 'popular', 'latest'

$theme_border = '#10b981';
$theme_glow   = 'rgba(16,185,129,0.25)';
$theme_badge_bg = 'rgba(16,185,129,0.15)';
$theme_badge_color = '#10b981';

if ( $card_theme === 'featured' ) {
    $theme_border = '#00f5d4';
    $theme_glow   = 'rgba(0,245,212,0.35)';
    $theme_badge_bg = 'rgba(0,245,212,0.2)';
    $theme_badge_color = '#00f5d4';
} elseif ( $card_theme === 'popular' ) {
    $theme_border = '#eab308';
    $theme_glow   = 'rgba(234,179,8,0.35)';
    $theme_badge_bg = 'rgba(234,179,8,0.2)';
    $theme_badge_color = '#eab308';
}
?>
<article id="post-<?php the_ID(); ?>" class="hs-cyber-post-card hs-card-theme-<?php echo esc_attr( $card_theme ); ?>" style="background: #0b1120; border: 1px solid <?php echo esc_attr( $theme_border ); ?>; border-radius: 16px; overflow: hidden; box-shadow: 0 0 20px <?php echo esc_attr( $theme_glow ); ?>; display: flex; flex-direction: column; transition: all 0.3s ease;">
    
    <!-- Thumbnail & Category Badge on Top -->
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="hs-card-thumb-wrap" style="position: relative; width: 100%; height: 200px; overflow: hidden;">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease;' ) ); ?>
            </a>
            <span class="hs-card-cat-badge" style="position: absolute; top: 12px; left: 12px; background: <?php echo esc_attr( $theme_badge_bg ); ?>; color: <?php echo esc_attr( $theme_badge_color ); ?>; border: 1px solid <?php echo esc_attr( $theme_border ); ?>; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; backdrop-filter: blur(4px);">
                <?php echo esc_html( $cat_name ); ?>
            </span>
        </div>
    <?php else : ?>
        <div class="hs-card-thumb-placeholder" style="position: relative; width: 100%; height: 160px; background: linear-gradient(135deg, #050811, #1e293b); display: flex; align-items: center; justify-content: center; font-size: 40px;">
            <span>🛡️</span>
            <span class="hs-card-cat-badge" style="position: absolute; top: 12px; left: 12px; background: <?php echo esc_attr( $theme_badge_bg ); ?>; color: <?php echo esc_attr( $theme_badge_color ); ?>; border: 1px solid <?php echo esc_attr( $theme_border ); ?>; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 800;">
                <?php echo esc_html( $cat_name ); ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- Card Content Body -->
    <div class="hs-card-body" style="padding: 18px; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
        
        <div>
            <!-- Title -->
            <h2 class="hs-card-title" style="margin: 0 0 10px 0; font-size: 1.15rem; font-weight: 800; line-height: 1.4;">
                <a href="<?php the_permalink(); ?>" style="color: #ffffff; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='<?php echo esc_attr( $theme_border ); ?>'" onmouseout="this.style.color='#ffffff'">
                    <?php the_title(); ?>
                </a>
            </h2>

            <!-- 2-3 Lines Excerpt -->
            <p class="hs-card-excerpt" style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0 0 16px 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '...' ) ); ?>
            </p>
        </div>

        <!-- Single Bottom Line: Likes, Comments, Views, Timestamp, User Avatar & Name -->
        <div class="hs-card-footer-line" style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 12px; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #cbd5e1;">
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <!-- Author Avatar & Name -->
                <div style="display: flex; align-items: center; gap: 4px;" title="<?php echo esc_attr( $author_name ); ?>">
                    <span>👨‍💻</span>
                    <span style="font-weight: 700; color: #fff; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( $author_name ); ?></span>
                </div>
                <span>·</span>
                <span><?php echo esc_html( $post_date ); ?></span>
            </div>

            <!-- Metrics: Likes, Comments, Views -->
            <div style="display: flex; align-items: center; gap: 8px; font-family: var(--font-mono, monospace);">
                <span title="Likes" style="display: inline-flex; align-items: center; gap: 2px;">❤️ <?php echo esc_html( (string) $likes_count ); ?></span>
                <span title="Comments" style="display: inline-flex; align-items: center; gap: 2px;">💬 <?php echo esc_html( (string) $com_count ); ?></span>
                <span title="Views" style="display: inline-flex; align-items: center; gap: 2px;">👁️ <?php echo esc_html( (string) $views_count ); ?></span>
            </div>

        </div>

    </div>
</article>

<style>
.hs-cyber-post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0 30px <?php echo esc_attr( $theme_border ); ?> !important;
}
.hs-cyber-post-card:hover .hs-card-thumb-wrap img {
    transform: scale(1.05);
}
</style>
<?php
