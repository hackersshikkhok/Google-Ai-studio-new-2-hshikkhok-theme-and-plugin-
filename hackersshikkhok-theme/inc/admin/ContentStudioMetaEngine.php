<?php
/**
 * Content Studio Meta Engine & Multi-Intent SEO Controller
 * 
 * Manages custom meta boxes, specification ledgers, verified price tracking,
 * and high-intent SEO templates for:
 * - Device Reviews (device_review)
 * - Device Prices & Variants (device_price)
 * - Tech News (tech_news)
 * 
 * @package HackersShikkhok\Core\Admin
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ContentStudioMetaEngine {

    public static function register(): void {
        add_action( 'add_meta_boxes', array( self::class, 'register_meta_boxes' ) );
        add_action( 'save_post', array( self::class, 'save_meta_boxes' ), 10, 2 );
        add_action( 'admin_menu', array( self::class, 'register_admin_menu' ) );
        add_action( 'admin_init', array( self::class, 'register_settings' ) );
    }

    public static function register_settings(): void {
        register_setting( 'hs_seo_templates_group', 'hs_seo_title_review' );
        register_setting( 'hs_seo_templates_group', 'hs_seo_title_price' );
        register_setting( 'hs_seo_templates_group', 'hs_seo_title_news' );
        register_setting( 'hs_seo_templates_group', 'hs_seo_title_article' );
        register_setting( 'hs_seo_templates_group', 'hs_seo_title_tutorial' );
    }

    public static function register_admin_menu(): void {
        add_menu_page(
            'Content & SEO Studio',
            'Content & SEO Hub',
            'edit_posts',
            'hs-content-seo-hub',
            array( self::class, 'render_admin_hub' ),
            'dashicons-chart-area',
            26
        );

        add_submenu_page(
            'hs-content-seo-hub',
            'SEO & Quality Audit',
            'SEO & Quality Audit',
            'edit_posts',
            'hs-content-seo-hub',
            array( self::class, 'render_admin_hub' )
        );

        add_submenu_page(
            'hs-content-seo-hub',
            'SEO Title Templates',
            'SEO Title Templates',
            'manage_options',
            'hs-seo-templates',
            array( self::class, 'render_seo_templates_page' )
        );
    }

    public static function register_meta_boxes(): void {
        // 1. Device Review Meta Box
        add_meta_box(
            'hs_device_review_meta',
            '📱 Device Review & Evaluation Matrix (Google Review & Product Schema Ready)',
            array( self::class, 'render_device_review_metabox' ),
            'device_review',
            'normal',
            'high'
        );

        // 2. Device Price Meta Box
        add_meta_box(
            'hs_device_price_meta',
            '💰 Device Verified Price in Bangladesh & Variants (Google Product Offer Ready)',
            array( self::class, 'render_device_price_metabox' ),
            'device_price',
            'normal',
            'high'
        );

        // 3. Tech News Meta Box
        add_meta_box(
            'hs_tech_news_meta',
            '📰 Tech News Intelligence & Source Attribution (Google NewsArticle Ready)',
            array( self::class, 'render_tech_news_metabox' ),
            'tech_news',
            'normal',
            'high'
        );

        // 4. Custom SEO Snippet Override for all editorial post types
        $seo_supported = array( 'post', 'tutorials', 'device_review', 'device_price', 'tech_news', 'tools', 'hs_course' );
        foreach ( $seo_supported as $pt ) {
            add_meta_box(
                'hs_custom_seo_override',
                '🔍 High-Intent Search SEO & Google Preview Override',
                array( self::class, 'render_seo_override_metabox' ),
                $pt,
                'normal',
                'default'
            );
        }
    }

    /**
     * Render Device Review Meta Box
     */
    public static function render_device_review_metabox( \WP_Post $post ): void {
        wp_nonce_field( 'hs_save_review_meta', 'hs_review_meta_nonce' );

        $device_name   = get_post_meta( $post->ID, '_hs_device_name', true );
        $verdict       = get_post_meta( $post->ID, '_hs_review_verdict', true );
        $display       = get_post_meta( $post->ID, '_hs_spec_display', true );
        $processor     = get_post_meta( $post->ID, '_hs_spec_processor', true );
        $ram_storage   = get_post_meta( $post->ID, '_hs_spec_ram_storage', true );
        $battery       = get_post_meta( $post->ID, '_hs_spec_battery', true );
        $camera        = get_post_meta( $post->ID, '_hs_spec_camera', true );
        $os            = get_post_meta( $post->ID, '_hs_spec_os', true );
        $score_display = get_post_meta( $post->ID, '_hs_score_display', true ) ?: '8.5';
        $score_camera  = get_post_meta( $post->ID, '_hs_score_camera', true ) ?: '8.8';
        $score_battery = get_post_meta( $post->ID, '_hs_score_battery', true ) ?: '9.0';
        $score_overall = get_post_meta( $post->ID, '_hs_score_overall', true ) ?: '8.8';
        $pros          = get_post_meta( $post->ID, '_hs_review_pros', true );
        $cons          = get_post_meta( $post->ID, '_hs_review_cons', true );
        $price_url     = get_post_meta( $post->ID, '_hs_related_price_url', true );
        $mfg_url       = get_post_meta( $post->ID, '_hs_manufacturer_url', true );
        ?>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label><strong>Target Device Model Name:</strong></label>
                <input type="text" name="_hs_device_name" value="<?php echo esc_attr( (string) $device_name ); ?>" placeholder="e.g. Samsung Galaxy A56 5G" style="width:100%; margin-top:4px;" />
            </div>
            <div>
                <label><strong>Related Verified Price Page Link / Slug:</strong></label>
                <input type="text" name="_hs_related_price_url" value="<?php echo esc_attr( (string) $price_url ); ?>" placeholder="e.g. /device-prices/samsung-galaxy-a56-price-in-bangladesh/" style="width:100%; margin-top:4px;" />
            </div>
        </div>

        <div style="margin-top:14px;">
            <label><strong>Editorial Verdict Summary (Highlighted on Top of Review):</strong></label>
            <textarea name="_hs_review_verdict" rows="2" style="width:100%; margin-top:4px;" placeholder="e.g. A stellar mid-ranger with flagship-grade Super AMOLED display, dependable 45W battery, and clean One UI software update pledge."><?php echo esc_textarea( (string) $verdict ); ?></textarea>
        </div>

        <div style="margin-top:16px; background:#f8fafc; border:1px solid #e2e8f0; padding:14px; border-radius:8px;">
            <strong style="color:#0f172a;">⚙️ Key Hardware Specifications (Displayed in Structured Specs Matrix & Schema):</strong>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:10px;">
                <div>
                    <label style="font-size:12px;">Display:</label>
                    <input type="text" name="_hs_spec_display" value="<?php echo esc_attr( (string) $display ); ?>" placeholder="e.g. 6.7 inch Super AMOLED+, 120Hz, 1000 nits" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">Processor / Chipset:</label>
                    <input type="text" name="_hs_spec_processor" value="<?php echo esc_attr( (string) $processor ); ?>" placeholder="e.g. Exynos 1580 (4nm) Octa-Core" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">RAM &amp; Storage:</label>
                    <input type="text" name="_hs_spec_ram_storage" value="<?php echo esc_attr( (string) $ram_storage ); ?>" placeholder="e.g. 8GB RAM, 128GB / 256GB UFS 3.1" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">Battery &amp; Charging:</label>
                    <input type="text" name="_hs_spec_battery" value="<?php echo esc_attr( (string) $battery ); ?>" placeholder="e.g. 5000 mAh Li-Po, 45W Fast Charging" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">Camera System:</label>
                    <input type="text" name="_hs_spec_camera" value="<?php echo esc_attr( (string) $camera ); ?>" placeholder="e.g. 50MP (OIS) + 12MP Ultra-wide + 5MP Macro | 32MP Front" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">OS &amp; Software:</label>
                    <input type="text" name="_hs_spec_os" value="<?php echo esc_attr( (string) $os ); ?>" placeholder="e.g. Android 15, One UI 7.0 (6 Years OS Upgrades)" style="width:100%;" />
                </div>
            </div>
        </div>

        <div style="margin-top:16px; background:#eff6ff; border:1px solid #bfdbfe; padding:14px; border-radius:8px;">
            <strong style="color:#1e3a8a;">⭐ Benchmarks &amp; Verified Editorial Scores (1 to 10 Scale):</strong>
            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px; margin-top:8px;">
                <div>
                    <label style="font-size:12px;">Display Score (1-10):</label>
                    <input type="number" step="0.1" min="1" max="10" name="_hs_score_display" value="<?php echo esc_attr( (string) $score_display ); ?>" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">Camera Score (1-10):</label>
                    <input type="number" step="0.1" min="1" max="10" name="_hs_score_camera" value="<?php echo esc_attr( (string) $score_camera ); ?>" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;">Battery Score (1-10):</label>
                    <input type="number" step="0.1" min="1" max="10" name="_hs_score_battery" value="<?php echo esc_attr( (string) $score_battery ); ?>" style="width:100%;" />
                </div>
                <div>
                    <label style="font-size:12px;"><strong>Overall Final Score:</strong></label>
                    <input type="number" step="0.1" min="1" max="10" name="_hs_score_overall" value="<?php echo esc_attr( (string) $score_overall ); ?>" style="width:100%; font-weight:bold; color:#1d4ed8;" />
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px;">
            <div>
                <label><strong style="color:#16a34a;">🟢 Pros (Good Points — 1 per line):</strong></label>
                <textarea name="_hs_review_pros" rows="4" style="width:100%; margin-top:4px;" placeholder="Brilliant 120Hz Super AMOLED screen&#10;Excellent 45W battery longevity&#10;Promised 6 years of security patches&#10;Clean stereo speakers with Dolby Atmos"><?php echo esc_textarea( (string) $pros ); ?></textarea>
            </div>
            <div>
                <label><strong style="color:#dc2626;">🔴 Cons (Drawbacks — 1 per line):</strong></label>
                <textarea name="_hs_review_cons" rows="4" style="width:100%; margin-top:4px;" placeholder="Charger not included in retail box&#10;No 3.5mm headphone jack&#10;Gaming gets slightly warm under sustained load"><?php echo esc_textarea( (string) $cons ); ?></textarea>
            </div>
        </div>
        <?php
    }

    /**
     * Render Device Price Meta Box
     */
    public static function render_device_price_metabox( \WP_Post $post ): void {
        wp_nonce_field( 'hs_save_price_meta', 'hs_price_meta_nonce' );

        $device_name   = get_post_meta( $post->ID, '_hs_price_device_name', true );
        $price_bdt     = get_post_meta( $post->ID, '_hs_price_bdt', true ) ?: '৳42,999';
        $price_type    = get_post_meta( $post->ID, '_hs_price_type', true ) ?: 'Official';
        $variants_json = get_post_meta( $post->ID, '_hs_price_variants_json', true );
        $availability  = get_post_meta( $post->ID, '_hs_price_availability', true ) ?: 'Available in Bangladesh';
        $verified_date = get_post_meta( $post->ID, '_hs_price_last_verified', true ) ?: current_time( 'Y-m-d' );
        $source        = get_post_meta( $post->ID, '_hs_price_source', true ) ?: 'Official Brand Authorized Showrooms';
        $quick_specs   = get_post_meta( $post->ID, '_hs_price_quick_specs', true );
        $review_url    = get_post_meta( $post->ID, '_hs_related_review_url', true );

        if ( empty( $variants_json ) ) {
            $variants_json = wp_json_encode( array(
                array( 'variant' => '8GB RAM / 128GB ROM', 'price' => '৳42,999', 'type' => 'Official' ),
                array( 'variant' => '8GB RAM / 256GB ROM', 'price' => '৳47,999', 'type' => 'Official' ),
            ), JSON_PRETTY_PRINT );
        }
        ?>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label><strong>Device Name:</strong></label>
                <input type="text" name="_hs_price_device_name" value="<?php echo esc_attr( (string) $device_name ); ?>" placeholder="e.g. Samsung Galaxy A56" style="width:100%; margin-top:4px;" />
            </div>
            <div>
                <label><strong>Base / Starting Verified Price (BDT):</strong></label>
                <input type="text" name="_hs_price_bdt" value="<?php echo esc_attr( (string) $price_bdt ); ?>" placeholder="e.g. ৳42,999" style="width:100%; margin-top:4px; font-weight:bold; color:#059669;" />
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-top:14px;">
            <div>
                <label><strong>Price Type:</strong></label>
                <select name="_hs_price_type" style="width:100%; margin-top:4px;">
                    <option value="Official" <?php selected( $price_type, 'Official' ); ?>>Official (যাচাইকৃত অফিসিয়াল দাম)</option>
                    <option value="Unofficial / Retail" <?php selected( $price_type, 'Unofficial / Retail' ); ?>>Unofficial / Retail Market</option>
                    <option value="Expected" <?php selected( $price_type, 'Expected' ); ?>>Expected / Upcoming</option>
                    <option value="Rumored" <?php selected( $price_type, 'Rumored' ); ?>>Rumored</option>
                </select>
            </div>
            <div>
                <label><strong>Availability Status:</strong></label>
                <select name="_hs_price_availability" style="width:100%; margin-top:4px;">
                    <option value="Available in Bangladesh" <?php selected( $availability, 'Available in Bangladesh' ); ?>>Available in Bangladesh</option>
                    <option value="In Stock" <?php selected( $availability, 'In Stock' ); ?>>In Stock</option>
                    <option value="Upcoming" <?php selected( $availability, 'Upcoming' ); ?>>Upcoming</option>
                    <option value="Out of Stock" <?php selected( $availability, 'Out of Stock' ); ?>>Out of Stock</option>
                    <option value="Discontinued" <?php selected( $availability, 'Discontinued' ); ?>>Discontinued</option>
                </select>
            </div>
            <div>
                <label><strong>Price Last Verified Date:</strong></label>
                <input type="date" name="_hs_price_last_verified" value="<?php echo esc_attr( (string) $verified_date ); ?>" style="width:100%; margin-top:4px;" />
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:14px;">
            <div>
                <label><strong>Verified Price Source Note:</strong></label>
                <input type="text" name="_hs_price_source" value="<?php echo esc_attr( (string) $source ); ?>" placeholder="e.g. Authorized Brand Outlets &amp; Daraz Official Mall" style="width:100%; margin-top:4px;" />
            </div>
            <div>
                <label><strong>Related Full Review Link / Slug:</strong></label>
                <input type="text" name="_hs_related_review_url" value="<?php echo esc_attr( (string) $review_url ); ?>" placeholder="e.g. /device-reviews/samsung-galaxy-a56-review/" style="width:100%; margin-top:4px;" />
            </div>
        </div>

        <div style="margin-top:14px;">
            <label><strong>Quick Specs Highlights (Single line):</strong></label>
            <input type="text" name="_hs_price_quick_specs" value="<?php echo esc_attr( (string) $quick_specs ); ?>" placeholder="e.g. 6.7' FHD+ 120Hz Super AMOLED | Exynos 1580 | 50MP OIS | 5000 mAh 45W" style="width:100%; margin-top:4px;" />
        </div>

        <div style="margin-top:14px;">
            <label><strong>RAM &amp; Storage Variants with Pricing (JSON or Structured List):</strong></label>
            <textarea name="_hs_price_variants_json" rows="4" style="width:100%; font-family:monospace; font-size:12px; margin-top:4px;"><?php echo esc_textarea( (string) $variants_json ); ?></textarea>
            <span style="font-size:11px; color:#64748b;">Example: [{"variant": "8GB / 128GB", "price": "৳42,999", "type": "Official"}, {"variant": "8GB / 256GB", "price": "৳47,999", "type": "Official"}]</span>
        </div>
        <?php
    }

    /**
     * Render Tech News Meta Box
     */
    public static function render_tech_news_metabox( \WP_Post $post ): void {
        wp_nonce_field( 'hs_save_news_meta', 'hs_news_meta_nonce' );

        $subheadline = get_post_meta( $post->ID, '_hs_news_subheadline', true );
        $source_name = get_post_meta( $post->ID, '_hs_news_source_name', true );
        $source_url  = get_post_meta( $post->ID, '_hs_news_source_url', true );
        $bd_date     = get_post_meta( $post->ID, '_hs_news_bd_launch_date', true );
        ?>
        <div>
            <label><strong>News Subheadline / Fast Facts Summary:</strong></label>
            <input type="text" name="_hs_news_subheadline" value="<?php echo esc_attr( (string) $subheadline ); ?>" placeholder="e.g. Samsung introduces the next-generation Galaxy A series featuring 4nm silicon and upgraded IP68 durability." style="width:100%; margin-top:4px;" />
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-top:14px;">
            <div>
                <label><strong>News Source / Attribution:</strong></label>
                <input type="text" name="_hs_news_source_name" value="<?php echo esc_attr( (string) $source_name ); ?>" placeholder="e.g. Samsung Global Newsroom" style="width:100%; margin-top:4px;" />
            </div>
            <div>
                <label><strong>Source Reference URL:</strong></label>
                <input type="url" name="_hs_news_source_url" value="<?php echo esc_attr( (string) $source_url ); ?>" placeholder="https://news.samsung.com/..." style="width:100%; margin-top:4px;" />
            </div>
            <div>
                <label><strong>Expected Bangladesh Availability / Launch:</strong></label>
                <input type="text" name="_hs_news_bd_launch_date" value="<?php echo esc_attr( (string) $bd_date ); ?>" placeholder="e.g. Late March 2026" style="width:100%; margin-top:4px;" />
            </div>
        </div>
        <?php
    }

    /**
     * Render Custom SEO Override Meta Box
     */
    public static function render_seo_override_metabox( \WP_Post $post ): void {
        wp_nonce_field( 'hs_save_seo_override', 'hs_seo_override_nonce' );

        $custom_title = get_post_meta( $post->ID, '_hs_seo_custom_title', true );
        $custom_desc  = get_post_meta( $post->ID, '_hs_seo_custom_desc', true );
        $canonical    = get_post_meta( $post->ID, '_hs_seo_canonical_url', true );
        $noindex      = get_post_meta( $post->ID, '_hs_seo_noindex', true );
        ?>
        <div style="display:grid; grid-template-columns:1fr; gap:12px;">
            <div>
                <label><strong>Custom Google SEO Title Link (Leave empty to use automatic high-intent template):</strong></label>
                <input type="text" name="_hs_seo_custom_title" value="<?php echo esc_attr( (string) $custom_title ); ?>" placeholder="e.g. Samsung Galaxy A56 Review: Pros, Cons & Verdict | Hackers শিক্ষক" style="width:100%; margin-top:4px;" />
            </div>
            <div>
                <label><strong>Custom Meta Description (Max 160 characters):</strong></label>
                <textarea name="_hs_seo_custom_desc" rows="2" maxlength="160" style="width:100%; margin-top:4px;" placeholder="e.g. In-depth review of Samsung Galaxy A56 with real display benchmarks, 45W battery test, camera samples, and verified price in Bangladesh."><?php echo esc_textarea( (string) $custom_desc ); ?></textarea>
            </div>
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                <div>
                    <label><strong>Canonical URL Override (Leave empty for default post URL):</strong></label>
                    <input type="url" name="_hs_seo_canonical_url" value="<?php echo esc_attr( (string) $canonical ); ?>" placeholder="<?php echo esc_attr( get_permalink( $post->ID ) ?: 'https://hackersshikkhok.com/...' ); ?>" style="width:100%; margin-top:4px;" />
                </div>
                <div style="display:flex; align-items:center; margin-top:20px;">
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="_hs_seo_noindex" value="1" <?php checked( $noindex, '1' ); ?> />
                        <span style="color:#dc2626; font-weight:600;">Exclude from Google Index (Noindex)</span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save Meta Box data with strict nonces and sanitization
     */
    public static function save_meta_boxes( int $post_id, \WP_Post $post ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 1. Device Review
        if ( isset( $_POST['hs_review_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hs_review_meta_nonce'] ) ), 'hs_save_review_meta' ) ) {
            $fields = array(
                '_hs_device_name', '_hs_review_verdict', '_hs_spec_display',
                '_hs_spec_processor', '_hs_spec_ram_storage', '_hs_spec_battery',
                '_hs_spec_camera', '_hs_spec_os', '_hs_score_display',
                '_hs_score_camera', '_hs_score_battery', '_hs_score_overall',
                '_hs_review_pros', '_hs_review_cons', '_hs_related_price_url', '_hs_manufacturer_url'
            );
            foreach ( $fields as $f ) {
                if ( isset( $_POST[ $f ] ) ) {
                    update_post_meta( $post_id, $f, sanitize_textarea_field( wp_unslash( $_POST[ $f ] ) ) );
                }
            }
        }

        // 2. Device Price
        if ( isset( $_POST['hs_price_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hs_price_meta_nonce'] ) ), 'hs_save_price_meta' ) ) {
            $fields = array(
                '_hs_price_device_name', '_hs_price_bdt', '_hs_price_type',
                '_hs_price_variants_json', '_hs_price_availability',
                '_hs_price_last_verified', '_hs_price_source',
                '_hs_price_quick_specs', '_hs_related_review_url'
            );
            foreach ( $fields as $f ) {
                if ( isset( $_POST[ $f ] ) ) {
                    update_post_meta( $post_id, $f, sanitize_textarea_field( wp_unslash( $_POST[ $f ] ) ) );
                }
            }
        }

        // 3. Tech News
        if ( isset( $_POST['hs_news_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hs_news_meta_nonce'] ) ), 'hs_save_news_meta' ) ) {
            $fields = array(
                '_hs_news_subheadline', '_hs_news_source_name',
                '_hs_news_source_url', '_hs_news_bd_launch_date'
            );
            foreach ( $fields as $f ) {
                if ( isset( $_POST[ $f ] ) ) {
                    update_post_meta( $post_id, $f, sanitize_text_field( wp_unslash( $_POST[ $f ] ) ) );
                }
            }
        }

        // 4. SEO Overrides
        if ( isset( $_POST['hs_seo_override_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hs_seo_override_nonce'] ) ), 'hs_save_seo_override' ) ) {
            update_post_meta( $post_id, '_hs_seo_custom_title', sanitize_text_field( wp_unslash( $_POST['_hs_seo_custom_title'] ?? '' ) ) );
            update_post_meta( $post_id, '_hs_seo_custom_desc', sanitize_textarea_field( wp_unslash( $_POST['_hs_seo_custom_desc'] ?? '' ) ) );
            update_post_meta( $post_id, '_hs_seo_canonical_url', esc_url_raw( wp_unslash( $_POST['_hs_seo_canonical_url'] ?? '' ) ) );
            update_post_meta( $post_id, '_hs_seo_noindex', isset( $_POST['_hs_seo_noindex'] ) ? '1' : '0' );
        }
    }

    /**
     * Render Master Admin Hub Dashboard
     */
    public static function render_admin_hub(): void {
        $reviews_count = wp_count_posts( 'device_review' )->publish ?? 0;
        $prices_count  = wp_count_posts( 'device_price' )->publish ?? 0;
        $news_count    = wp_count_posts( 'tech_news' )->publish ?? 0;
        $posts_count   = wp_count_posts( 'post' )->publish ?? 0;
        ?>
        <div class="wrap" style="max-width:1200px;">
            <h1 style="display:flex; align-items:center; gap:10px;">
                <span>🛡️</span> Hackers শিক্ষক — Content Studio &amp; Multi-Intent SEO Engine
            </h1>
            <p class="description" style="font-size:14px;">
                Manage distinct content architecture, specialized device reviews, verified price tracking, tech news, and Google Search Console rich-result readiness from one unified control center.
            </p>

            <!-- Metrics Cards -->
            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin:24px 0;">
                <div style="background:#0b1120; color:#fff; border:1px solid #00f5d4; border-radius:12px; padding:18px;">
                    <div style="font-size:12px; color:#00f5d4; font-weight:700;">DEVICE REVIEWS</div>
                    <div style="font-size:28px; font-weight:900; margin:6px 0;"><?php echo (int) $reviews_count; ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=device_review' ) ); ?>" style="color:#38bdf8; text-decoration:none; font-size:12px;">Manage Reviews ➔</a>
                </div>

                <div style="background:#0b1120; color:#fff; border:1px solid #10b981; border-radius:12px; padding:18px;">
                    <div style="font-size:12px; color:#10b981; font-weight:700;">VERIFIED PRICES</div>
                    <div style="font-size:28px; font-weight:900; margin:6px 0;"><?php echo (int) $prices_count; ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=device_price' ) ); ?>" style="color:#38bdf8; text-decoration:none; font-size:12px;">Manage Prices ➔</a>
                </div>

                <div style="background:#0b1120; color:#fff; border:1px solid #a78bfa; border-radius:12px; padding:18px;">
                    <div style="font-size:12px; color:#a78bfa; font-weight:700;">TECH NEWS UPDATES</div>
                    <div style="font-size:28px; font-weight:900; margin:6px 0;"><?php echo (int) $news_count; ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tech_news' ) ); ?>" style="color:#38bdf8; text-decoration:none; font-size:12px;">Manage Tech News ➔</a>
                </div>

                <div style="background:#0b1120; color:#fff; border:1px solid #f59e0b; border-radius:12px; padding:18px;">
                    <div style="font-size:12px; color:#f59e0b; font-weight:700;">ARTICLES &amp; TUTORIALS</div>
                    <div style="font-size:28px; font-weight:900; margin:6px 0;"><?php echo (int) $posts_count; ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>" style="color:#38bdf8; text-decoration:none; font-size:12px;">Manage Articles ➔</a>
                </div>
            </div>

            <!-- Architectural Breakdown & Google Search Appearance Blueprint -->
            <div style="background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:24px; margin-bottom:24px;">
                <h2 style="margin-top:0;">🌐 Google Search Intent &amp; Content Architecture Matrix</h2>
                <table class="widefat striped" style="border:none;">
                    <thead>
                        <tr>
                            <th>Content Type</th>
                            <th>Target URL Structure</th>
                            <th>Search Intent</th>
                            <th>SEO Title Template</th>
                            <th>Structured Data Schema</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Device Review</strong></td>
                            <td><code>/device-reviews/{slug}/</code></td>
                            <td><em>"Samsung Galaxy A56 review Bangladesh"</em></td>
                            <td><code>{Name} Review: Pros, Cons &amp; Verdict | Hackers শিক্ষক</code></td>
                            <td><span class="badge" style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:4px;">Product + Review (Stars &amp; Pros/Cons)</span></td>
                        </tr>
                        <tr>
                            <td><strong>Device Price</strong></td>
                            <td><code>/device-prices/{slug}/</code></td>
                            <td><em>"Samsung Galaxy A56 price in Bangladesh"</em></td>
                            <td><code>{Name} Price in Bangladesh: Variants &amp; Specs | Hackers শিক্ষক</code></td>
                            <td><span class="badge" style="background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:4px;">Product + Offer (BDT Price &amp; InStock)</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tech News</strong></td>
                            <td><code>/tech-news/{slug}/</code></td>
                            <td><em>"Samsung new phone launch Bangladesh"</em></td>
                            <td><code>{Headline}: Features &amp; Launch Details | Hackers শিক্ষক</code></td>
                            <td><span class="badge" style="background:#ede9fe; color:#5b21b6; padding:2px 8px; border-radius:4px;">NewsArticle (Publisher &amp; Timestamp)</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tutorials</strong></td>
                            <td><code>/tutorials/{slug}/</code></td>
                            <td><em>"How to learn Python programming"</em></td>
                            <td><code>{Title}: Beginner's Guide | Hackers শিক্ষক</code></td>
                            <td><span class="badge" style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:4px;">TechArticle / HowTo</span></td>
                        </tr>
                        <tr>
                            <td><strong>Academy Courses</strong></td>
                            <td><code>/courses/{slug}/</code></td>
                            <td><em>"Cybersecurity course with certificate"</em></td>
                            <td><code>{Course Name} | Hackers শিক্ষক Academy</code></td>
                            <td><span class="badge" style="background:#fee2e2; color:#991b1b; padding:2px 8px; border-radius:4px;">Course + Credential</span></td>
                        </tr>
                        <tr>
                            <td><strong>Developer Tools</strong></td>
                            <td><code>/tools/?tool={slug}</code></td>
                            <td><em>"Online sha256 hash generator"</em></td>
                            <td><code>{Tool Name} Online Tool | Hackers শিক্ষক</code></td>
                            <td><span class="badge" style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:4px;">SoftwareApplication + FAQPage</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Content Quality & Verification Alerts -->
            <div style="background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:24px;">
                <h3 style="margin-top:0;">🛡️ Content Quality, Price Verification &amp; AdSense Compliance Audit</h3>
                <p>Google requires verified information for product listings, original testing for reviews, and reliable price quotes without false placeholders.</p>
                <ul style="line-height:2; list-style:disc; padding-left:24px;">
                    <li><strong>Always verify price currency and date:</strong> Ensure every Price page has the <em>"Price Last Verified Date"</em> updated.</li>
                    <li><strong>Cross-link between Reviews &amp; Prices:</strong> Every Device Review automatically displays a button to its matching Price page, and every Price page links back to the in-depth Review.</li>
                    <li><strong>Zero-Fake Reviews:</strong> Never fabricate aggregate review counts or copy ratings from competitor sites.</li>
                    <li><strong>AdSense Safe Ads Placement:</strong> Distinct advertising containers with clear <em>"Advertisement"</em> labels prevent accidental clicks.</li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Render SEO Title Templates Page
     */
    public static function render_seo_templates_page(): void {
        ?>
        <div class="wrap" style="max-width:900px;">
            <h1>⚙️ SEO Title &amp; OpenGraph Templates</h1>
            <p class="description">Define the automated search title patterns for each content type. Leave empty to use system defaults.</p>

            <form method="post" action="options.php" style="background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:24px; margin-top:16px;">
                <?php settings_fields( 'hs_seo_templates_group' ); ?>
                <?php do_settings_sections( 'hs_seo_templates_group' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="hs_seo_title_review">Device Review Title Pattern</label></th>
                        <td>
                            <input type="text" id="hs_seo_title_review" name="hs_seo_title_review" value="<?php echo esc_attr( get_option( 'hs_seo_title_review', '{name} Review: Features, Pros, Cons & Verdict | Hackers শিক্ষক' ) ); ?>" class="regular-text" style="width:100%;" />
                            <p class="description">Variables: <code>{name}</code>, <code>{brand}</code>, <code>{site_name}</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_seo_title_price">Device Price Title Pattern</label></th>
                        <td>
                            <input type="text" id="hs_seo_title_price" name="hs_seo_title_price" value="<?php echo esc_attr( get_option( 'hs_seo_title_price', '{name} Price in Bangladesh: Variants & Specs | Hackers শিক্ষক' ) ); ?>" class="regular-text" style="width:100%;" />
                            <p class="description">Variables: <code>{name}</code>, <code>{brand}</code>, <code>{price}</code>, <code>{site_name}</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_seo_title_news">Tech News Title Pattern</label></th>
                        <td>
                            <input type="text" id="hs_seo_title_news" name="hs_seo_title_news" value="<?php echo esc_attr( get_option( 'hs_seo_title_news', '{title}: Features & Launch Details | Hackers শিক্ষক' ) ); ?>" class="regular-text" style="width:100%;" />
                            <p class="description">Variables: <code>{title}</code>, <code>{category}</code>, <code>{site_name}</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_seo_title_tutorial">Tutorial Title Pattern</label></th>
                        <td>
                            <input type="text" id="hs_seo_title_tutorial" name="hs_seo_title_tutorial" value="<?php echo esc_attr( get_option( 'hs_seo_title_tutorial', '{title}: Beginner\'s Guide | Hackers শিক্ষক' ) ); ?>" class="regular-text" style="width:100%;" />
                            <p class="description">Variables: <code>{title}</code>, <code>{site_name}</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_seo_title_article">General Article Title Pattern</label></th>
                        <td>
                            <input type="text" id="hs_seo_title_article" name="hs_seo_title_article" value="<?php echo esc_attr( get_option( 'hs_seo_title_article', '{title} | Hackers শিক্ষক' ) ); ?>" class="regular-text" style="width:100%;" />
                            <p class="description">Variables: <code>{title}</code>, <code>{site_name}</code></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button( 'Save SEO Templates' ); ?>
            </form>
        </div>
        <?php
    }
}
