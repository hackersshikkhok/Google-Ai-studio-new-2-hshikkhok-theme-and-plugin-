<?php
/**
 * Native High-Speed Technical SEO Head Emitter.
 * Emits clean canonical URLs, OpenGraph/Twitter tags, Google verification, and Schema.org JSON-LD.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_head', static function (): void {
    $brand = (string) hs_get_setting( 'brand_name', 'HackersShikkhok' );

    // Google Search Console verification tag
    $gsc_token = trim( (string) hs_get_setting( 'gsc_verification_token', '' ) );
    if ( '' !== $gsc_token ) {
        echo '<meta name="google-site-verification" content="' . esc_attr( $gsc_token ) . '" />' . "\n";
    }

    $title = wp_get_document_title();
    $desc  = (string) hs_get_setting( 'brand_tagline', 'Cyber Defense Academy & 2,024+ Developer Tools' );
    $url   = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
    $type  = 'website';

    if ( is_singular() ) {
        $post_id = (int) get_queried_object_id();
        $excerpt = wp_strip_all_tags( (string) get_the_excerpt( $post_id ) );
        if ( '' !== $excerpt ) {
            $desc = wp_trim_words( $excerpt, 28, '...' );
        }
        $url  = (string) get_permalink( $post_id );
        $type = 'article';
    }

    if ( hs_get_setting( 'enable_og_meta', true ) ) {
        echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( $brand ) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
        echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    }

    if ( hs_get_setting( 'enable_schema_jsonld', true ) && is_front_page() ) {
        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => $brand,
            'url'             => home_url( '/' ),
            'potentialAction' => array(
                '@type'       => 'SearchAction',
                'target'      => home_url( '/?s={search_term_string}' ),
                'query-input' => 'required name=search_term_string',
            ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
}, 2 );
