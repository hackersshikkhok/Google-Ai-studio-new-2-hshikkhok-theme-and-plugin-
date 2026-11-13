<?php
/**
 * HackersShikkhok — Multilingual Translation Ledger & Global Routing Registry
 *
 * Provides a robust, zero-third-party dependency translation relational ledger
 * connecting English, Bengali, Arabic, and Spanish posts, terms, and custom entities.
 *
 * @package HackersShikkhok\Core\Localization
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\SEO;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class TranslationLedger {

    public const SUPPORTED_LANGUAGES = array(
        'bn' => array(
            'code'      => 'bn',
            'locale'    => 'bn_BD',
            'name'      => 'বাংলা',
            'name_en'   => 'Bengali',
            'direction' => 'ltr',
            'flag'      => '🇧🇩',
            'is_default'=> true,
            'slug_prefix'=> '',
        ),
        'en' => array(
            'code'      => 'en',
            'locale'    => 'en_US',
            'name'      => 'English',
            'name_en'   => 'English',
            'direction' => 'ltr',
            'flag'      => '🌐',
            'is_default'=> false,
            'slug_prefix'=> 'en',
        ),
        'ar' => array(
            'code'      => 'ar',
            'locale'    => 'ar_SA',
            'name'      => 'العربية',
            'name_en'   => 'Arabic',
            'direction' => 'rtl',
            'flag'      => '🇸🇦',
            'is_default'=> false,
            'slug_prefix'=> 'ar',
        ),
        'es' => array(
            'code'      => 'es',
            'locale'    => 'es_ES',
            'name'      => 'Español',
            'name_en'   => 'Spanish',
            'direction' => 'ltr',
            'flag'      => '🇪🇸',
            'is_default'=> false,
            'slug_prefix'=> 'es',
        ),
    );

    public static function register(): void {
        add_action( 'init', array( self::class, 'register_rewrite_rules' ), 1 );
        add_filter( 'query_vars', array( self::class, 'register_query_vars' ) );
        add_action( 'template_redirect', array( self::class, 'handle_locale_context' ), 1 );
        add_filter( 'locale', array( self::class, 'filter_runtime_locale' ) );
        add_filter( 'the_title', array( self::class, 'filter_localized_title' ), 10, 2 );
        add_filter( 'the_content', array( self::class, 'filter_localized_content' ), 10, 1 );
    }

    public static function filter_localized_title( string $title, int $post_id = 0 ): string {
        $curr_lang = self::get_current_lang();
        if ( 'bn' === $curr_lang || ! $post_id ) {
            return $title;
        }

        $meta_key = '_hs_title_' . $curr_lang;
        $localized = get_post_meta( $post_id, $meta_key, true );
        if ( ! empty( $localized ) && is_string( $localized ) ) {
            return $localized;
        }

        return $title;
    }

    public static function filter_localized_content( string $content ): string {
        $curr_lang = self::get_current_lang();
        if ( 'bn' === $curr_lang ) {
            return $content;
        }

        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return $content;
        }

        $meta_key = '_hs_content_' . $curr_lang;
        $localized = get_post_meta( $post_id, $meta_key, true );
        if ( ! empty( $localized ) && is_string( $localized ) ) {
            return $localized;
        }

        return $content;
    }

    public static function register_rewrite_rules(): void {
        // Safe, non-destructive prefix rewrite rules for /en/, /ar/, /es/
        $languages = array( 'en', 'ar', 'es' );
        foreach ( $languages as $lang ) {
            add_rewrite_rule(
                "^{$lang}/?$",
                'index.php?hs_lang=' . $lang,
                'top'
            );
            add_rewrite_rule(
                "^{$lang}/([^/]+)/?$",
                'index.php?hs_lang=' . $lang . '&pagename=$matches[1]',
                'top'
            );
            add_rewrite_rule(
                "^{$lang}/tools/([^/]+)/?$",
                'index.php?hs_lang=' . $lang . '&tools=$matches[1]',
                'top'
            );
            add_rewrite_rule(
                "^{$lang}/tutorials/([^/]+)/?$",
                'index.php?hs_lang=' . $lang . '&tutorials=$matches[1]',
                'top'
            );
            add_rewrite_rule(
                "^{$lang}/cve/([^/]+)/?$",
                'index.php?hs_lang=' . $lang . '&hs_cve_id=$matches[1]',
                'top'
            );
        }
    }

    public static function register_query_vars( array $vars ): array {
        $vars[] = 'hs_lang';
        $vars[] = 'hs_cve_id';
        return $vars;
    }

    public static function get_current_lang(): string {
        $lang = '';

        // 1. Direct GET parameter takes precedence and is always accessible early
        if ( isset( $_GET['hs_lang'] ) && is_string( $_GET['hs_lang'] ) ) {
            $lang = sanitize_key( $_GET['hs_lang'] );
        } elseif ( isset( $_GET['lang'] ) && is_string( $_GET['lang'] ) ) {
            $lang = sanitize_key( $_GET['lang'] );
        }

        // 2. Query variable only if \WP_Query is fully initialized (prevents "Call to a member function get() on null")
        if ( empty( $lang ) && isset( $GLOBALS['wp_query'] ) && ( $GLOBALS['wp_query'] instanceof \WP_Query ) && function_exists( 'get_query_var' ) ) {
            $query_var = get_query_var( 'hs_lang' );
            if ( is_string( $query_var ) && ! empty( $query_var ) ) {
                $lang = sanitize_key( $query_var );
            }
        }

        // 3. Fallback to REQUEST_URI prefix check (e.g. /en/, /ar/, /es/)
        if ( empty( $lang ) && ! empty( $_SERVER['REQUEST_URI'] ) && is_string( $_SERVER['REQUEST_URI'] ) ) {
            $uri = (string) $_SERVER['REQUEST_URI'];
            foreach ( array_keys( self::SUPPORTED_LANGUAGES ) as $code ) {
                if ( preg_match( '#^/' . preg_quote( $code, '#' ) . '(/|$)#i', $uri ) ) {
                    $lang = $code;
                    break;
                }
            }
        }

        // 4. Safe cookie preference fallback
        if ( empty( $lang ) && ! empty( $_COOKIE['hs_preferred_lang'] ) && is_string( $_COOKIE['hs_preferred_lang'] ) ) {
            $cookie_lang = sanitize_key( $_COOKIE['hs_preferred_lang'] );
            if ( isset( self::SUPPORTED_LANGUAGES[ $cookie_lang ] ) ) {
                $lang = $cookie_lang;
            }
        }

        if ( ! empty( $lang ) && isset( self::SUPPORTED_LANGUAGES[ $lang ] ) ) {
            return $lang;
        }

        return 'bn'; // Native default
    }

    public static function handle_locale_context(): void {
        // Enforce safe cookie setting if user explicitly chose language
        if ( isset( $_GET['hs_lang_choice'] ) ) {
            $chosen = sanitize_key( (string) $_GET['hs_lang_choice'] );
            if ( isset( self::SUPPORTED_LANGUAGES[ $chosen ] ) ) {
                setcookie( 'hs_preferred_lang', $chosen, time() + ( 86400 * 365 ), '/' );
            }
        }
    }

    public static function filter_runtime_locale( string $locale ): string {
        try {
            $curr = self::get_current_lang();
            if ( isset( self::SUPPORTED_LANGUAGES[ $curr ]['locale'] ) ) {
                return self::SUPPORTED_LANGUAGES[ $curr ]['locale'];
            }
        } catch ( \Throwable $e ) {
            return $locale;
        }
        return $locale;
    }

    /**
     * Link two posts as translation pairs in hs_translation_ledger
     */
    public static function link_translations( int $source_id, int $target_id, string $source_lang, string $target_lang ): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'hs_translation_ledger';
        
        $group_hash = md5( 'group_' . min( $source_id, $target_id ) . '_' . time() );

        // Check if source already has a cluster
        $existing_hash = $wpdb->get_var( $wpdb->prepare(
            "SELECT translation_group FROM {$table} WHERE entity_id = %d AND entity_type = 'post'",
            $source_id
        ) );

        if ( $existing_hash ) {
            $group_hash = $existing_hash;
        }

        // Insert or update source record
        $wpdb->replace( $table, array(
            'translation_group' => $group_hash,
            'entity_type'       => 'post',
            'entity_id'         => $source_id,
            'language_code'     => $source_lang,
            'source_hash'       => md5( (string) get_post_field( 'post_content', $source_id ) ),
            'sync_status'       => 'synced',
            'updated_at'        => current_time( 'mysql' ),
        ), array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' ) );

        // Insert or update target record
        $wpdb->replace( $table, array(
            'translation_group' => $group_hash,
            'entity_type'       => 'post',
            'entity_id'         => $target_id,
            'language_code'     => $target_lang,
            'source_hash'       => md5( (string) get_post_field( 'post_content', $target_id ) ),
            'sync_status'       => 'synced',
            'updated_at'        => current_time( 'mysql' ),
        ), array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' ) );

        return true;
    }

    /**
     * Retrieve all verified translation alternates for a given post
     *
     * @param int $post_id
     * @return array<string, string> Array mapping language_code => permalink
     */
    public static function get_translation_alternates( int $post_id ): array {
        global $wpdb;
        $table = $wpdb->prefix . 'hs_translation_ledger';

        $group = $wpdb->get_var( $wpdb->prepare(
            "SELECT translation_group FROM {$table} WHERE entity_id = %d AND entity_type = 'post' LIMIT 1",
            $post_id
        ) );

        if ( ! $group ) {
            return array();
        }

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT language_code, entity_id FROM {$table} WHERE translation_group = %s AND sync_status != 'archived'",
            $group
        ), ARRAY_A );

        $alternates = array();
        foreach ( $rows as $row ) {
            $permalink = get_permalink( (int) $row['entity_id'] );
            if ( $permalink && 'publish' === get_post_status( (int) $row['entity_id'] ) ) {
                $alternates[ $row['language_code'] ] = $permalink;
            }
        }

        return $alternates;
    }
}