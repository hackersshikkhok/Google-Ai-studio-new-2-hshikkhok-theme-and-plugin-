<?php
/**
 * Featured Post Component - Cyan Cyberpunk Theme
 */
declare(strict_types=1);
$post_id     = get_the_ID();
$author_id   = get_the_author_meta('ID');
$author_name = get_the_author();
$likes       = (int) get_post_meta($post_id, 'hs_likes_count', true);
$views       = (int) get_post_meta($post_id, 'hs_views_count', true);
$com_count   = (int) get_comments_number($post_id);
?>
<article id="post-<?php the_ID(); ?>" class="hs-featured-card" style="background:#050811; border:1px solid #00f5d4; border-radius:20px; padding:20px; box-shadow:0 0 25px rgba(0,245,212,0.15); transition:0.3s;">
    <div class="hs-card-thumb-wrap" style="position:relative; border-radius:12px; overflow:hidden; margin-bottom:16px;">
        <a href="<?php the_permalink(); ?>">
            <?php if(has_post_thumbnail()) the_post_thumbnail('medium_large', ['style'=>'width:100%; height:180px; object-fit:cover;']); ?>
        </a>
        <span style="position:absolute; top:10px; left:10px; background:#00f5d4; color:#050811; padding:4px 12px; border-radius:6px; font-weight:900; font-size:11px;"><?php echo esc_html(get_the_category()[0]->name ?? 'Notice'); ?></span>
    </div>
    <h2 style="color:#fff; font-size:1.3rem; margin:0 0 10px; font-weight:800;"><a href="<?php the_permalink(); ?>" style="text-decoration:none; color:#fff;"><?php the_title(); ?></a></h2>
    <p style="color:#94a3b8; font-size:13px; line-height:1.6; margin-bottom:16px;"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
    
    <!-- Professional Footer -->
    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(0,245,212,0.2); padding-top:12px; font-size:11px; color:#cbd5e1; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:6px;">
            <img src="<?php echo esc_url(get_avatar_url($author_id, ['size'=>32])); ?>" style="width:24px; height:24px; border-radius:50%;">
            <span style="font-weight:700; color:#fff;"><?php echo esc_html($author_name); ?></span>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <button type="button" class="hs-like-btn" data-post-id="<?php echo esc_attr((string)$post_id); ?>" style="background:none; border:none; color:#00f5d4; cursor:pointer; padding:0;">
                ❤️ <span class="hs-likes-count"><?php echo esc_html(\HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_likes($post_id)); ?></span>
            </button>
            <a href="<?php the_permalink(); ?>#comments" style="text-decoration:none; color:#00f5d4;">💬 <?php echo esc_html($com_count); ?></a>
            <span>👁️ <?php echo esc_html(\HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_views($post_id)); ?></span>
            <span>⏰ <?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'); ?></span>
        </div>
    </div>
</article>
