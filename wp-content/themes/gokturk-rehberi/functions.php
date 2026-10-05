<?php
/**
 * Göktürk Rehberi Theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

function gr_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        )
    );

    register_nav_menus(
        array(
            'primary' => __( 'Ana Menü', 'gokturk-rehberi' ),
            'footer'  => __( 'Footer Menü', 'gokturk-rehberi' ),
        )
    );
}
add_action( 'after_setup_theme', 'gr_theme_setup' );

/**
 * Primary navigation fallback for a fresh WordPress installation.
 */
function gr_primary_menu_fallback() {
    $items = array(
        'Ana Sayfa'          => home_url( '/' ),
        'İşletmeler'         => get_post_type_archive_link( 'business' ) ?: home_url( '/isletmeler/' ),
        'Rehber'             => home_url( '/rehber/' ),
        "Göktürk'te Yaşam" => home_url( '/yasam/' ),
    );

    echo '<ul class="navbar-nav ms-auto">';
    foreach ( $items as $label => $url ) {
        echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Footer navigation fallback for a fresh WordPress installation.
 */
function gr_footer_menu_fallback() {
    $items = array(
        'Hakkımızda' => home_url( '/hakkimizda/' ),
        'İletişim'   => home_url( '/iletisim/' ),
    );

    echo '<ul class="list-inline mb-0">';
    foreach ( $items as $label => $url ) {
        echo '<li class="list-inline-item me-3"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

function gr_enqueue_assets() {
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();

    // Bootstrap is loaded from the official jsDelivr package URL in production.
    // Keeping the version explicit avoids relying on a mutable CDN alias.
    wp_enqueue_style(
        'gr-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
        array(),
        '5.3.8'
    );

    wp_enqueue_style(
        'gr-theme',
        $theme_uri . '/assets/css/theme.css',
        array( 'gr-bootstrap' ),
        file_exists( $theme_dir . '/assets/css/theme.css' )
            ? filemtime( $theme_dir . '/assets/css/theme.css' )
            : '1.0.0'
    );

    wp_enqueue_script(
        'gr-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js',
        array(),
        '5.3.8',
        true
    );

    wp_enqueue_script(
        'gr-main',
        $theme_uri . '/assets/js/main.js',
        array(),
        file_exists( $theme_dir . '/assets/js/main.js' )
            ? filemtime( $theme_dir . '/assets/js/main.js' )
            : '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'gr_enqueue_assets' );

function gr_register_search_rewrite() {
    add_rewrite_rule(
        '^arama/?$',
        'index.php?gr_search=1',
        'top'
    );
}

add_action( 'init', 'gr_register_search_rewrite', 20 );

function gr_register_search_query_var( $vars ) {
    $vars[] = 'gr_search';

    return $vars;
}

add_filter( 'query_vars', 'gr_register_search_query_var' );

/**
 * Business archive filtering and pagination defaults.
 */
function gr_business_archive_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_post_type_archive( 'business' ) || $query->is_tax( 'business_category' ) ) {
        $query->set( 'posts_per_page', 12 );
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'gr_business_archive_query' );

/**
 * Unified site search: businesses and editorial posts only.
 */
function gr_site_search_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $is_custom_search = (bool) get_query_var( 'gr_search' );

    if ( ! $query->is_search() && ! $is_custom_search ) {
        return;
    }

    $query->set(
        'post_type',
        array(
            'post',
            'business',
        )
    );
}
add_action( 'pre_get_posts', 'gr_site_search_query' );

/**
 * Lightweight SEO metadata for V1. Compatible with common SEO plugins by
 * avoiding output when another plugin already handles the document title/meta.
 */
function gr_seo_description() {
    if ( is_front_page() ) {
        return 'Göktürk Rehberi; Göktürk\'te işletmeleri, yerel rehberleri ve yaşam içeriklerini keşfetmenizi sağlar.';
    }

    if ( is_singular() ) {
        $description = get_the_excerpt();
        if ( ! $description ) {
            $description = wp_strip_all_tags( get_the_content() );
        }
        return wp_trim_words( $description, 30, '…' );
    }

    if ( is_post_type_archive( 'business' ) ) {
        return 'Göktürk\'teki işletmeleri kategori, adres ve işletme bilgileriyle keşfedin.';
    }

    if ( is_tax( 'business_category' ) ) {
        return single_term_title( '', false ) . ' kategorisindeki Göktürk işletmelerini keşfedin.';
    }

    if ( is_search() || get_query_var( 'gr_search' ) ) {
        return 'Göktürk Rehberi içinde işletme, rehber ve Göktürk\'te yaşam içeriklerini arayın.';
    }

    if ( is_404() ) {
        return 'Aradığınız sayfa bulunamadı. Göktürk Rehberi ana sayfasına dönebilirsiniz.';
    }

    return 'Göktürk Rehberi; Göktürk hakkında işletme, rehber ve yerel yaşam bilgileri sunar.';
}

function gr_output_seo_meta() {
    if ( is_admin() ) {
        return;
    }

    $description = gr_seo_description();
    $title       = wp_get_document_title();

    if ( is_search() || get_query_var( 'gr_search' ) ) {
        $canonical = home_url( '/arama/' );
    } elseif ( is_404() ) {
        $canonical = '';
    } elseif ( is_post_type_archive( 'business' ) ) {
        $canonical = get_post_type_archive_link( 'business' );
    } elseif ( is_tax( 'business_category' ) ) {
        $canonical = get_term_link( get_queried_object() );
    } elseif ( is_singular() ) {
        $canonical = get_permalink();
    } elseif ( is_front_page() ) {
        $canonical = home_url( '/' );
    } else {
        $canonical = home_url( '/' );
    }

    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

    if ( $canonical && ! is_wp_error( $canonical ) ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
    }

    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . '">' . "\n";

    if ( is_search() || get_query_var( 'gr_search' ) || is_404() ) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }
}
add_action( 'wp_head', 'gr_output_seo_meta', 5 );
