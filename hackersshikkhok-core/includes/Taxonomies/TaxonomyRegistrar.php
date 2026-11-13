<?php
declare(strict_types=1);

namespace HackersShikkhok\Core\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Consolidated Custom Taxonomy Registrar for HackersShikkhok Ecosystem.
 */
final class TaxonomyRegistrar {
    public static function register(): void {
        add_action( 'init', array( self::class, 'register_now' ), 5 );
    }

    public static function register_now(): void {
        $taxonomies = array(
            'hs_language'        => array( 'label' => 'Programming Language', 'hierarchical' => false, 'types' => array( 'tutorials', 'code', 'projects' ), 'slug' => 'language' ),
            'hs_difficulty'      => array( 'label' => 'Difficulty', 'hierarchical' => true, 'types' => array( 'tutorials', 'code', 'hs_course' ), 'slug' => 'difficulty' ),
            'hs_platform'        => array( 'label' => 'Platform', 'hierarchical' => true, 'types' => array( 'tools', 'code', 'projects' ), 'slug' => 'platform' ),
            'hs_topic'           => array( 'label' => 'Topic', 'hierarchical' => true, 'types' => array( 'tutorials', 'cyber', 'hs_question' ), 'slug' => 'topic' ),
            'hs_tool_type'       => array( 'label' => 'Tool Type', 'hierarchical' => true, 'types' => array( 'tools' ), 'slug' => 'tool-type' ),
            'hs_security_domain' => array( 'label' => 'Security Domain', 'hierarchical' => true, 'types' => array( 'cyber', 'troubleshooting', 'hs_course' ), 'slug' => 'security-domain' ),
            'hs_series'          => array( 'label' => 'Learning Series', 'hierarchical' => true, 'types' => array( 'tutorials', 'hs_course' ), 'slug' => 'series' ),
            'hs_license'         => array( 'label' => 'Code License', 'hierarchical' => false, 'types' => array( 'code' ), 'slug' => 'license' ),
            'hs_faculty'         => array( 'label' => 'Faculty', 'hierarchical' => true, 'types' => array( 'hs_course' ), 'slug' => 'faculty' ),
            'device_brand'       => array( 'label' => 'Device Brands', 'hierarchical' => true, 'types' => array( 'device_review', 'device_price' ), 'slug' => 'device-brand' ),
            'device_category'    => array( 'label' => 'Device Categories', 'hierarchical' => true, 'types' => array( 'device_review', 'device_price' ), 'slug' => 'device-category' ),
            'news_category'      => array( 'label' => 'News Categories', 'hierarchical' => true, 'types' => array( 'tech_news' ), 'slug' => 'news-category' ),
            'platform_category'  => array( 'label' => 'Platform Categories', 'hierarchical' => true, 'types' => array( 'hs_platform_center', 'hs_generated_app' ), 'slug' => 'platform-category' ),
        );

        foreach ( $taxonomies as $slug => $cfg ) {
            if ( taxonomy_exists( $slug ) ) {
                continue;
            }
            register_taxonomy( $slug, $cfg['types'], array(
                'label'        => $cfg['label'],
                'labels'       => array(
                    'name'          => $cfg['label'],
                    'singular_name' => $cfg['label'],
                    'search_items'  => 'Search ' . $cfg['label'],
                    'all_items'     => 'All ' . $cfg['label'],
                    'edit_item'     => 'Edit ' . $cfg['label'],
                    'update_item'   => 'Update ' . $cfg['label'],
                    'add_new_item'  => 'Add New ' . $cfg['label'],
                    'new_item_name' => 'New ' . $cfg['label'] . ' Name',
                    'menu_name'     => $cfg['label'],
                ),
                'public'       => true,
                'show_in_rest' => true,
                'hierarchical' => $cfg['hierarchical'],
                'rewrite'      => array( 'slug' => $cfg['slug'] ?? $slug, 'with_front' => false ),
            ) );
        }
    }
}
