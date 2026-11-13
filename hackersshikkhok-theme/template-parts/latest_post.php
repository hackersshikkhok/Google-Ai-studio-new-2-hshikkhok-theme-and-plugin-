<?php
/**
 * Latest Post Component - Emerald Green Theme with Scanline Effect & Real Metrics
 */
declare(strict_types=1);
$post_id     = get_the_ID();
$author_id   = get_the_author_meta('ID');
$likes       = (int) get_post_meta($post_id, 'hs_likes_count', true);
$views       = (int) get_post_meta($post_id, 'hs_views_count', true);
$com_count   = (int) get_comments_number($post_id);
?>
<article id="post-<?php the_ID(); ?>" class="hs-latest-card" style="background:#050811; border:1px solid #10b981; border-radius:12px; padding:16px; position:relative; overflow:hidden;">
    <div class="hs-scanline" style="position:absolute; top:0; left:0; width:100%; height:2px; background:rgba(16,185,129,0.5); animation:scan 4s linear infinite;"></div>
    
    <div style="margin-bottom:12px; border-radius:8px; overflow:hidden;">
        <a href="<?php the_permalink(); ?>">
            <?php if(has_post_thumbnail()) the_post_thumbnail('medium', ['style'=>'width:100%; height:120px; object-fit:cover;']); ?>
        </a>
    </div>
    <h2 style="color:#fff; font-size:1.1rem; margin:0 0 10px; font-weight:700;"><a href="<?php the_permalink(); ?>" style="text-decoration:none; color:#fff;"><?php the_title(); ?></a></h2>
    
    <!-- Professional Footer -->
    <div style="margin-top:10px; font-size:11px; color:#cbd5e1; border-top:1px solid rgba(16,185,129,0.2); padding-top:10px;">
        <div style="display:flex; align-items:center; gap:6px; margin-bottom:8px;">
            <img src="<?php echo esc_url(get_avatar_url($author_id, ['size'=>32])); ?>" style="width:20px; height:20px; border-radius:50%;">
            <span style="font-weight:700; color:#fff;"><?php echo esc_html(get_the_author()); ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
            <div style="display:flex; gap:8px;">
                <button type="button" class="hs-like-btn" data-post-id="<?php echo esc_attr((string)$post_id); ?>" style="background:none; border:none; color:#10b981; cursor:pointer; padding:0;">
                    ❤️ <span class="hs-likes-count"><?php echo esc_html(\HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_likes($post_id)); ?></span>
                </button>
                <a href="<?php the_permalink(); ?>#comments" style="text-decoration:none; color:#10b981;">💬 <?php echo esc_html($com_count); ?></a>
                <span>👁️ <?php echo esc_html(\HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_views($post_id)); ?></span>
            </div>
            <span>⏰ <?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'); ?></span>
        </div>
    </div>
</article>
<style>
@keyframes scan { from { top: -2px; } to { top: 100%; } }
</style>
