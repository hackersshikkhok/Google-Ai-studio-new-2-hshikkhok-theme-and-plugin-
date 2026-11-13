<?php
/**
 * Popular Post Component - Amber Gold Theme with Hover Focus & Real Metrics
 */
declare(strict_types=1);
$post_id     = get_the_ID();
$author_id   = get_the_author_meta('ID');
$likes       = (int) get_post_meta($post_id, 'hs_likes_count', true);
$views       = (int) get_post_meta($post_id, 'hs_views_count', true);
$com_count   = (int) get_comments_number($post_id);
?>
<article id="post-<?php the_ID(); ?>" class="hs-popular-card" style="background:#0a0d17; border:2px solid #eab308; border-radius:16px; padding:20px; box-shadow:0 0 20px rgba(234,179,8,0.2); transition:0.3s; transform:scale(1.0);">
    <div style="margin-bottom:12px; border-radius:10px; overflow:hidden;">
        <a href="<?php the_permalink(); ?>">
            <?php if(has_post_thumbnail()) the_post_thumbnail('medium', ['style'=>'width:100%; height:140px; object-fit:cover;']); ?>
        </a>
    </div>
    <h2 style="color:#fff; font-size:1.2rem; margin:0 0 10px; font-weight:800;"><a href="<?php the_permalink(); ?>" style="text-decoration:none; color:#fff;"><?php the_title(); ?></a></h2>
    
    <!-- Professional Footer -->
    <div style="margin-top:15px; font-size:11px; color:#94a3b8; border-top:1px solid rgba(234,179,8,0.2); padding-top:10px;">
        <div style="display:flex; align-items:center; gap:6px; margin-bottom:8px;">
            <img src="<?php echo esc_url(get_avatar_url($author_id, ['size'=>32])); ?>" style="width:20px; height:20px; border-radius:50%;">
            <span style="font-weight:700; color:#fff;"><?php echo esc_html(get_the_author()); ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
            <div style="display:flex; gap:8px;">
                <button type="button" class="hs-like-btn" data-post-id="<?php echo esc_attr((string)$post_id); ?>" style="background:none; border:none; color:#eab308; cursor:pointer; padding:0;">
                    ❤️ <span class="hs-likes-count"><?php echo esc_html(\HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_likes($post_id)); ?></span>
                </button>
                <a href="<?php the_permalink(); ?>#comments" style="text-decoration:none; color:#eab308;">💬 <?php echo esc_html($com_count); ?></a>
                <span>👁️ <?php echo esc_html(\HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_views($post_id)); ?></span>
            </div>
            <span>⏰ <?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'); ?></span>
        </div>
    </div>
</article>
<style>
.hs-popular-card:hover { transform: translateY(-10px); box-shadow: 0 0 40px rgba(234,179,8,0.4); }
</style>
