<?php
/**
 * Plugin Name: Local Guide
 * Description: Göktürk Rehberi V1 işletme ve yerel rehber altyapısı.
 * Version: 1.0.0
 * Author: Göktürk Rehberi
 * Text Domain: local-guide
 */

defined( 'ABSPATH' ) || exit;

define( 'LG_VERSION', '1.0.0' );
define( 'LG_PLUGIN_FILE', __FILE__ );
define( 'LG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function lg_maybe_flush_rewrite_rules() {
    $rewrite_version = get_option( 'lg_rewrite_version' );
    if ( '1.1.0' !== $rewrite_version ) {
        update_option( 'lg_rewrite_version', '1.1.0' );
        flush_rewrite_rules();
    }
}
add_action( 'init', 'lg_maybe_flush_rewrite_rules', 99 );

require_once LG_PLUGIN_DIR . 'includes/post-types.php';
require_once LG_PLUGIN_DIR . 'includes/taxonomies.php';
require_once LG_PLUGIN_DIR . 'includes/meta-fields.php';
require_once LG_PLUGIN_DIR . 'includes/business-hours.php';
require_once LG_PLUGIN_DIR . 'includes/business-features.php';
require_once LG_PLUGIN_DIR . 'includes/business-admin.php';
require_once LG_PLUGIN_DIR . 'includes/error-reports.php';


function lg_get_or_create_category( $name, $parent = 0 ) {
    $term = term_exists( $name, 'category' );

    if ( ! $term ) {
        $term = wp_insert_term(
            $name,
            'category',
            array( 'parent' => (int) $parent )
        );
    }

    if ( is_wp_error( $term ) ) {
        return 0;
    }

    $term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;

    if ( (int) $parent !== (int) get_term_field( 'parent', $term_id, 'category' ) ) {
        wp_update_term(
            $term_id,
            'category',
            array( 'parent' => (int) $parent )
        );
    }

    return $term_id;
}

function lg_seed_guide_categories() {
    $parent_id = lg_get_or_create_category( 'Rehber' );

    foreach ( array( 'Yeme & İçme', 'Çocuk', 'Spor', 'Günlük İhtiyaçlar', 'Alışveriş' ) as $category_name ) {
        lg_get_or_create_category( $category_name, $parent_id );
    }
}

function lg_seed_life_categories() {
    $parent_id = lg_get_or_create_category( "Göktürk'te Yaşam" );

    foreach ( array( 'Mahalleler ve Bölgeler', 'Ulaşım', "Göktürk'te Yaşamak", 'Doğa ve Açık Hava', 'Aile ve Çocuk', "Göktürk'ün Geçmişi" ) as $category_name ) {
        lg_get_or_create_category( $category_name, $parent_id );
    }
}

function lg_editorial_section( $post_id ) {
    if ( 'post' !== get_post_type( $post_id ) ) {
        return '';
    }

    $categories = get_the_category( $post_id );
    $guide      = get_category_by_slug( 'rehber' );
    $life       = get_category_by_slug( 'gokturkte-yasam' );

    foreach ( $categories as $category ) {
        if ( $guide && ( (int) $category->term_id === (int) $guide->term_id || cat_is_ancestor_of( $guide, $category ) ) ) {
            return 'guide';
        }
    }

    foreach ( $categories as $category ) {
        if ( $life && ( (int) $category->term_id === (int) $life->term_id || cat_is_ancestor_of( $life, $category ) ) ) {
            return 'life';
        }
    }

    return '';
}

function lg_register_editorial_rewrites() {
    $editorial_sections = array(
        'rehber'          => 'rehber',
        'gokturkte-yasam' => 'yasam',
    );

    foreach ( $editorial_sections as $parent_slug => $url_base ) {
        $parent = get_category_by_slug( $parent_slug );

        if ( ! $parent ) {
            continue;
        }

        $categories = get_categories(
            array(
                'taxonomy'   => 'category',
                'child_of'   => (int) $parent->term_id,
                'hide_empty' => false,
            )
        );

        foreach ( $categories as $category ) {
            add_rewrite_rule(
                '^' . preg_quote( $url_base, '/' ) . '/' . preg_quote( $category->slug, '/' ) . '/?$',
                'index.php?cat=' . (int) $category->term_id . '&lg_editorial_category=' . $url_base,
                'top'
            );
        }
    }

    add_rewrite_rule(
        '^rehber/([^/]+)/?$',
        'index.php?name=$matches[1]&lg_section=guide',
        'top'
    );

    add_rewrite_rule(
        '^yasam/([^/]+)/?$',
        'index.php?name=$matches[1]&lg_section=life',
        'top'
    );
}
add_action( 'init', 'lg_register_editorial_rewrites', 20 );

function lg_editorial_query_vars( $vars ) {
    $vars[] = 'lg_section';
    return $vars;
}
add_filter( 'query_vars', 'lg_editorial_query_vars' );

function lg_editorial_single_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $section = $query->get( 'lg_section' );
    if ( ! in_array( $section, array( 'guide', 'life' ), true ) ) {
        return;
    }

    $query->set( 'post_type', 'post' );
}
add_action( 'pre_get_posts', 'lg_editorial_single_query' );

function lg_editorial_post_link( $url, $post ) {
    if ( 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
        return $url;
    }

    $section = lg_editorial_section( $post->ID );
    if ( 'guide' === $section ) {
        return home_url( '/rehber/' . $post->post_name . '/' );
    }

    if ( 'life' === $section ) {
        return home_url( '/yasam/' . $post->post_name . '/' );
    }

    return $url;
}
add_filter( 'post_link', 'lg_editorial_post_link', 10, 2 );

function lg_editorial_category_link( $termlink, $term, $taxonomy ) {
    if ( 'category' !== $taxonomy || ! $term instanceof WP_Term ) {
        return $termlink;
    }

    $guide = get_category_by_slug( 'rehber' );
    $life  = get_category_by_slug( 'gokturkte-yasam' );

    if ( ! $guide && ! $life ) {
        return $termlink;
    }

    if ( $guide && (int) $term->term_id === (int) $guide->term_id ) {
        return home_url( '/rehber/' );
    }

    if ( $guide && cat_is_ancestor_of( $guide, $term ) ) {
        return home_url( '/rehber/' . $term->slug . '/' );
    }

    if ( $life && (int) $term->term_id === (int) $life->term_id ) {
        return home_url( '/yasam/' );
    }

    if ( $life && cat_is_ancestor_of( $life, $term ) ) {
        return home_url( '/yasam/' . $term->slug . '/' );
    }

    return $termlink;
}
add_filter( 'term_link', 'lg_editorial_category_link', 10, 3 );

function lg_editorial_category_query_vars( $vars ) {
    $vars[] = 'lg_editorial_category';

    return $vars;
}
add_filter( 'query_vars', 'lg_editorial_category_query_vars' );

function lg_seed_business_categories() {
    $categories = array(
        'Yeme & İçme' => array( 'Restoran', 'Kafe', 'Kahvaltı', 'Fırın', 'Tatlı' ),
        'Sağlık'      => array(),
        'Güzellik'    => array(),
        'Spor'        => array(),
        'Çocuk'       => array(),
        'Eğitim'      => array(),
        'Alışveriş'   => array(),
        'Ev & Yaşam'  => array(),
        'Hizmetler'   => array(),
        'Otomotiv'    => array(),
    );

    foreach ( $categories as $parent_name => $children ) {
        $parent = term_exists( $parent_name, 'business_category' );

        if ( ! $parent ) {
            $parent = wp_insert_term( $parent_name, 'business_category' );
        }

        if ( is_wp_error( $parent ) ) {
            continue;
        }

        $parent_id = is_array( $parent ) ? (int) $parent['term_id'] : (int) $parent;

        foreach ( $children as $child_name ) {
            if ( ! term_exists( $child_name, 'business_category' ) ) {
                wp_insert_term(
                    $child_name,
                    'business_category',
                    array( 'parent' => $parent_id )
                );
            }
        }
    }
}

register_activation_hook(
    __FILE__,
    function () {
        lg_register_business_post_type();
        lg_register_business_taxonomy();
        lg_register_error_report_post_type();
        lg_seed_business_categories();
        lg_seed_guide_categories();
        lg_seed_life_categories();
        flush_rewrite_rules();
    }
);

register_deactivation_hook(
    __FILE__,
    function () {
        flush_rewrite_rules();
    }
);
