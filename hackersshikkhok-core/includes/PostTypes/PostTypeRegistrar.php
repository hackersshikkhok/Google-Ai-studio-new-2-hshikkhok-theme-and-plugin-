<?php
declare(strict_types=1);

namespace HackersShikkhok\Core\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Consolidated Custom Post Type Registrar for HackersShikkhok Ecosystem.
 * All 18 platform CPTs are registered here for zero fragmentation.
 */
final class PostTypeRegistrar {
    public static function register(): void {
        add_action( 'init', array( self::class, 'register_now' ), 5 );
    }

    public static function register_now(): void {
        $cpts = array(
            'tutorials'          => array(
                'label'   => 'Tutorials',
                'rewrite' => array( 'slug' => 'tutorials' ),
                'icon'    => 'dashicons-welcome-learn-more',
            ),
            'code'               => array(
                'label'   => 'Code Library',
                'rewrite' => array( 'slug' => 'code' ),
                'icon'    => 'dashicons-editor-code',
            ),
            'tools'              => array(
                'label'   => 'Developer Tools',
                'rewrite' => array( 'slug' => 'tools' ),
                'icon'    => 'dashicons-hammer',
            ),
            'projects'           => array(
                'label'   => 'Projects',
                'rewrite' => array( 'slug' => 'projects' ),
                'icon'    => 'dashicons-portfolio',
            ),
            'cyber'              => array(
                'label'   => 'Cyber Guides',
                'rewrite' => array( 'slug' => 'cyber' ),
                'icon'    => 'dashicons-shield-alt',
            ),
            'troubleshooting'    => array(
                'label'   => 'Troubleshooting',
                'rewrite' => array( 'slug' => 'troubleshooting' ),
                'icon'    => 'dashicons-sos',
            ),
            'hs_question'        => array(
                'label'   => 'Forum Q&A',
                'rewrite' => array( 'slug' => 'forum-questions' ),
                'icon'    => 'dashicons-format-chat',
            ),
            'hs_video'           => array(
                'label'   => 'YouTube Resources',
                'rewrite' => array( 'slug' => 'videos' ),
                'icon'    => 'dashicons-video-alt3',
            ),
            'hs_course'          => array(
                'label'   => 'Academy Courses',
                'rewrite' => array( 'slug' => 'courses' ),
                'icon'    => 'dashicons-welcome-learn-more',
            ),
            'hs_resource'        => array(
                'label'   => 'Resources',
                'rewrite' => array( 'slug' => 'resources' ),
                'icon'    => 'dashicons-media-document',
            ),
            'device_review'      => array(
                'label'   => 'Device Reviews',
                'rewrite' => array( 'slug' => 'device-reviews' ),
                'icon'    => 'dashicons-smartphone',
            ),
            'device_price'       => array(
                'label'   => 'Device Prices & Specs',
                'rewrite' => array( 'slug' => 'device-prices' ),
                'icon'    => 'dashicons-tag',
            ),
            'tech_news'          => array(
                'label'   => 'Tech News',
                'rewrite' => array( 'slug' => 'tech-news' ),
                'icon'    => 'dashicons-megaphone',
            ),
            'hs_cyber_lab'       => array(
                'label'   => 'Cyber Labs',
                'rewrite' => array( 'slug' => 'cyber-labs' ),
                'icon'    => 'dashicons-shield',
            ),
            'hs_elite_challenge' => array(
                'label'       => 'Elite Challenges',
                'has_archive' => 'challenges',
                'rewrite'     => array( 'slug' => 'challenges' ),
                'icon'        => 'dashicons-awards',
            ),
            'hs_tech_question'   => array(
                'label'       => 'Q&A Hub',
                'has_archive' => 'questions',
                'rewrite'     => array( 'slug' => 'questions' ),
                'icon'        => 'dashicons-editor-help',
            ),
            'hs_platform_center' => array(
                'label'   => 'Platform Centers',
                'rewrite' => array( 'slug' => 'platform-center-item' ),
                'icon'    => 'dashicons-superhero',
            ),
            'hs_generated_app'   => array(
                'label'   => 'Generated Apps & Tools',
                'rewrite' => array( 'slug' => 'app-project' ),
                'icon'    => 'dashicons-smartphone',
            ),
        );

        foreach ( $cpts as $slug => $cfg ) {
            if ( post_type_exists( $slug ) ) {
                continue;
            }
            register_post_type( $slug, array(
                'label'        => $cfg['label'],
                'labels'       => array(
                    'name'          => $cfg['label'],
                    'singular_name' => $cfg['label'],
                    'add_new'       => 'Add New ' . $cfg['label'],
                    'add_new_item'  => 'Add New ' . $cfg['label'],
                    'edit_item'     => 'Edit ' . $cfg['label'],
                ),
                'public'       => true,
                'show_in_rest' => true,
                'has_archive'  => $cfg['has_archive'] ?? true,
                'rewrite'      => $cfg['rewrite'],
                'menu_icon'    => $cfg['icon'] ?? 'dashicons-admin-post',
                'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields', 'comments' ),
            ) );
        }
    }
}
