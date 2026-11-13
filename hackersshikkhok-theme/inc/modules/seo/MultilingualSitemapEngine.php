<?php
/**
 * HackersShikkhok — Multilingual XML Sitemap Index & Child Engine
 *
 * Implements Google Search Console compliant sitemap structure with Google Image Sitemap protocol:
 * - /sitemap_index.xml (Master index)
 * - /sitemap-bn.xml (Bengali articles, courses & tools)
 * - /sitemap-en.xml (English articles, courses & tools)
 * - /sitemap-tools.xml (All 2,024+ free developer & security tools with dynamic vector SVG images)
 * - /sitemap-courses.xml (Cybersecurity Academy courses & CTF labs with SVG banners)
 * - /sitemap-cve.xml (Curated security advisories & CVE analyses)
 *
 * Excludes drafts, private posts, and non-canonical duplicates.
 *
 * @package HackersShikkhok\Core\SEO
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\SEO;

use HackersShikkhok\Theme\Modules\SEO\TranslationLedger;
use HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class MultilingualSitemapEngine {

    public static function register(): void {
        add_action( 'init', array( self::class, 'register_sitemap_rewrites' ) );
        add_filter( 'query_vars', array( self::class, 'register_query_vars' ) );
        add_action( 'template_redirect', array( self::class, 'dispatch_sitemap_request' ), 1 );
    }

    public static function register_sitemap_rewrites(): void {
        add_rewrite_rule( '^sitemap\.xml$', 'index.php?hs_sitemap=index', 'top' );
        add_rewrite_rule( '^sitemap_index\.xml$', 'index.php?hs_sitemap=index', 'top' );
        add_rewrite_rule( '^sitemap-([a-z0-9_-]+)\.xml$', 'index.php?hs_sitemap=$matches[1]', 'top' );
    }

    public static function register_query_vars( array $vars ): array {
        $vars[] = 'hs_sitemap';
        return $vars;
    }

    public static function dispatch_sitemap_request(): void {
        $type = get_query_var( 'hs_sitemap' );
        
        // Zero-Fail Direct Request-URI Inspection (Works even before rewrite flush)
        if ( empty( $type ) ) {
            $uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
            $path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
            if ( $path === 'sitemap.xml' || $path === 'sitemap_index.xml' ) {
                $type = 'index';
            } elseif ( preg_match( '/^sitemap-([a-z0-9_-]+)\.xml$/', $path, $matches ) ) {
                $type = $matches[1];
            }
        }

        if ( empty( $type ) ) {
            return;
        }

        // Clean any output buffer to ensure pure valid XML without whitespace
        if ( ob_get_level() > 0 ) {
            ob_clean();
        }

        header( 'Content-Type: application/xml; charset=utf-8' );
        header( 'X-Robots-Tag: noindex, follow' );

        if ( 'index' === $type ) {
            self::render_sitemap_index();
        } elseif ( 'bn' === $type || 'en' === $type ) {
            self::render_language_sitemap( $type );
        } elseif ( 'tools' === $type ) {
            self::render_tools_sitemap();
        } elseif ( 'courses' === $type ) {
            self::render_courses_sitemap();
        } elseif ( 'reviews' === $type ) {
            self::render_cpt_sitemap( 'device_review', 0.90 );
        } elseif ( 'prices' === $type ) {
            self::render_cpt_sitemap( 'device_price', 0.90 );
        } elseif ( 'news' === $type ) {
            self::render_cpt_sitemap( 'tech_news', 0.90 );
        } elseif ( 'posts' === $type ) {
            self::render_cpt_sitemap( 'post', 0.85 );
        } elseif ( 'pages' === $type ) {
            self::render_cpt_sitemap( 'page', 0.80 );
        } elseif ( 'tutorials' === $type ) {
            self::render_cpt_sitemap( 'tutorials', 0.85 );
        } elseif ( 'code' === $type ) {
            self::render_cpt_sitemap( 'code', 0.80 );
        } elseif ( 'cve' === $type ) {
            self::render_cve_sitemap();
        } else {
            self::render_generic_sitemap( $type );
        }
        exit;
    }

    public static function get_all_sitemap_urls(): array {
        return array(
            'Master Index'        => home_url( '/sitemap_index.xml' ),
            'Standard XML'        => home_url( '/sitemap.xml' ),
            'Articles & Posts'    => home_url( '/sitemap-posts.xml' ),
            'Core Pages & Legal'  => home_url( '/sitemap-pages.xml' ),
            'Device Reviews'      => home_url( '/sitemap-reviews.xml' ),
            'Device Prices'       => home_url( '/sitemap-prices.xml' ),
            'Tech News'           => home_url( '/sitemap-news.xml' ),
            '2,024+ Dev Tools'    => home_url( '/sitemap-tools.xml' ),
            'Academy Courses'     => home_url( '/sitemap-courses.xml' ),
            'Tutorials'           => home_url( '/sitemap-tutorials.xml' ),
            'Code Library'        => home_url( '/sitemap-code.xml' ),
            'Bengali Articles'    => home_url( '/sitemap-bn.xml' ),
            'English Articles'    => home_url( '/sitemap-en.xml' ),
        );
    }

    private static function render_sitemap_index(): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $sitemaps = array(
            'posts'   => current_time( 'c' ),
            'pages'   => current_time( 'c' ),
            'reviews' => current_time( 'c' ),
            'prices'  => current_time( 'c' ),
            'news'    => current_time( 'c' ),
            'tools'   => current_time( 'c' ),
            'courses' => current_time( 'c' ),
            'tutorials'=> current_time( 'c' ),
            'bn'      => current_time( 'c' ),
            'en'      => current_time( 'c' ),
        );

        foreach ( $sitemaps as $slug => $lastmod ) {
            echo "  <sitemap>\n";
            echo '    <loc>' . esc_url( home_url( "/sitemap-{$slug}.xml" ) ) . "</loc>\n";
            echo '    <lastmod>' . esc_html( $lastmod ) . "</lastmod>\n";
            echo "  </sitemap>\n";
        }

        echo '</sitemapindex>';
    }

    private static function render_cpt_sitemap( string $cpt, float $priority = 0.85 ): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $args = array(
            'post_type'      => $cpt,
            'post_status'    => 'publish',
            'posts_per_page' => 500,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                    'key'     => '_hs_seo_noindex',
                    'value'   => '1',
                    'compare' => '!=',
                ),
                array(
                    'key'     => '_hs_seo_noindex',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        );

        $query = new \WP_Query( $args );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id   = get_the_ID();
                $permalink = get_permalink();
                $lastmod   = get_the_modified_date( 'c' );
                $title     = get_the_title();
                $thumbnail = get_the_post_thumbnail_url( $post_id, 'large' );

                echo "  <url>\n";
                echo '    <loc>' . esc_url( $permalink ) . "</loc>\n";
                echo '    <lastmod>' . esc_html( $lastmod ) . "</lastmod>\n";
                echo "    <changefreq>daily</changefreq>\n";
                echo '    <priority>' . esc_html( number_format( $priority, 2 ) ) . "</priority>\n";

                if ( $thumbnail ) {
                    echo "    <image:image>\n";
                    echo '      <image:loc>' . esc_url( $thumbnail ) . "</image:loc>\n";
                    echo '      <image:title>' . esc_html( $title ) . "</image:title>\n";
                    echo "    </image:image>\n";
                }

                echo "  </url>\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';
    }

    private static function render_language_sitemap( string $lang ): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Query only published, publicly queryable posts
        $args = array(
            'post_type'      => array( 'post', 'tutorials', 'code', 'tools', 'hs_course', 'cyber', 'projects' ),
            'post_status'    => 'publish',
            'posts_per_page' => 500,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        );

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id   = get_the_ID();
                $permalink = get_permalink();
                $lastmod   = get_the_modified_date( 'c' );
                $title     = get_the_title();

                if ( 'en' === $lang ) {
                    $permalink = str_replace( home_url( '/' ), home_url( '/en/' ), $permalink );
                }

                echo "  <url>\n";
                echo '    <loc>' . esc_url( $permalink ) . "</loc>\n";
                echo '    <lastmod>' . esc_html( $lastmod ) . "</lastmod>\n";
                echo "    <changefreq>weekly</changefreq>\n";
                echo "    <priority>0.8</priority>\n";

                $featured_image = get_the_post_thumbnail_url( $post_id, 'full' );
                if ( ! $featured_image && get_post_type() === 'tools' ) {
                    $featured_image = home_url( '/wp-json/hackersshikkhok/v1/tools/banner/' . sanitize_key( get_post_field( 'post_name', $post_id ) ) );
                }
                if ( $featured_image ) {
                    echo "    <image:image>\n";
                    echo '      <image:loc>' . esc_url( $featured_image ) . "</image:loc>\n";
                    echo '      <image:title>' . esc_html( $title ) . "</image:title>\n";
                    echo "    </image:image>\n";
                }

                echo "  </url>\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';
    }

    private static function render_tools_sitemap(): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $emitted_slugs = array();

        // 1. Output published WordPress CPT tools
        $args = array(
            'post_type'      => 'tools',
            'post_status'    => 'publish',
            'posts_per_page' => 500,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        );

        $query = new \WP_Query( $args );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $slug = get_post_field( 'post_name', get_the_ID() );
                $emitted_slugs[ $slug ] = true;

                $svg_banner = home_url( '/wp-json/hackersshikkhok/v1/tools/banner/' . sanitize_key( $slug ) );
                $tool_title = get_the_title();

                echo "  <url>\n";
                echo '    <loc>' . esc_url( get_permalink() ) . "</loc>\n";
                echo '    <lastmod>' . esc_html( get_the_modified_date( 'c' ) ) . "</lastmod>\n";
                echo "    <changefreq>daily</changefreq>\n";
                echo "    <priority>0.9</priority>\n";
                echo "    <image:image>\n";
                echo '      <image:loc>' . esc_url( $svg_banner ) . "</image:loc>\n";
                echo '      <image:title>' . esc_html( $tool_title ) . " — HackersShikkhok Free Cyber Security Tool</image:title>\n";
                echo "      <image:caption>Interactive Vector Architecture Diagram &amp; 100% Client-Side Privacy</image:caption>\n";
                echo "    </image:image>\n";
                echo "  </url>\n";
            }
            wp_reset_postdata();
        }

        // 2. Output Registry-based Virtual Tools
        if ( class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ) {
            $all_tools = UniversalToolRegistry::get_all_tools();
            $now_iso = current_time( 'c' );
            $site_url = home_url( '/' );

            $count = 0;
            foreach ( $all_tools as $slug => $tool ) {
                if ( isset( $emitted_slugs[ $slug ] ) ) {
                    continue;
                }
                if ( $count > 1500 ) {
                    break;
                }
                $count++;

                $tool_url   = trailingslashit( $site_url . 'tools/' . sanitize_key( $slug ) );
                $tool_name  = $tool['name'] ?? ucfirst( str_replace( '-', ' ', $slug ) );
                $svg_banner = home_url( '/wp-json/hackersshikkhok/v1/tools/banner/' . sanitize_key( $slug ) );

                echo "  <url>\n";
                echo '    <loc>' . esc_url( $tool_url ) . "</loc>\n";
                echo '    <lastmod>' . esc_html( $now_iso ) . "</lastmod>\n";
                echo "    <changefreq>daily</changefreq>\n";
                echo "    <priority>0.9</priority>\n";
                echo "    <image:image>\n";
                echo '      <image:loc>' . esc_url( $svg_banner ) . "</image:loc>\n";
                echo '      <image:title>' . esc_html( $tool_name ) . " — Free Online Developer Tool</image:title>\n";
                echo "      <image:caption>Client-Side WebWorker Execution &amp; Instant Vector Architecture Diagram</image:caption>\n";
                echo "    </image:image>\n";
                echo "  </url>\n";
            }
        }

        echo '</urlset>';
    }

    private static function render_courses_sitemap(): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $args = array(
            'post_type'      => 'hs_course',
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        );

        $query = new \WP_Query( $args );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id   = get_the_ID();
                $permalink = get_permalink();
                $lastmod   = get_the_modified_date( 'c' );
                $title     = get_the_title();
                $thumbnail = get_the_post_thumbnail_url( $post_id, 'full' );

                echo "  <url>\n";
                echo '    <loc>' . esc_url( $permalink ) . "</loc>\n";
                echo '    <lastmod>' . esc_html( $lastmod ) . "</lastmod>\n";
                echo "    <changefreq>weekly</changefreq>\n";
                echo "    <priority>0.95</priority>\n";

                if ( $thumbnail ) {
                    echo "    <image:image>\n";
                    echo '      <image:loc>' . esc_url( $thumbnail ) . "</image:loc>\n";
                    echo '      <image:title>' . esc_html( $title ) . " — Free Cybersecurity Course</image:title>\n";
                    echo "    </image:image>\n";
                }

                echo "  </url>\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';
    }

    private static function render_cve_sitemap(): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_intelligence';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
            $records = $wpdb->get_results(
                "SELECT cve_id, updated_at FROM {$table} WHERE publish_status = 'published' ORDER BY updated_at DESC LIMIT 200",
                ARRAY_A
            );

            foreach ( $records as $row ) {
                echo "  <url>\n";
                echo '    <loc>' . esc_url( home_url( '/cve/' . sanitize_key( $row['cve_id'] ) . '/' ) ) . "</loc>\n";
                echo '    <lastmod>' . esc_html( gmdate( 'c', strtotime( $row['updated_at'] ) ) ) . "</lastmod>\n";
                echo "    <changefreq>weekly</changefreq>\n";
                echo "    <priority>0.7</priority>\n";
                echo "  </url>\n";
            }
        }

        echo '</urlset>';
    }

    private static function render_generic_sitemap( string $slug ): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        echo "  <url>\n";
        echo '    <loc>' . esc_url( home_url( '/' ) ) . "</loc>\n";
        echo '    <lastmod>' . esc_html( current_time( 'c' ) ) . "</lastmod>\n";
        echo "  </url>\n";
        echo '</urlset>';
    }
}