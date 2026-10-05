<?php
defined( 'ABSPATH' ) || exit;

function lg_register_business_taxonomy() {
    register_taxonomy(
        'business_category',
        array( 'business' ),
        array(
            'labels' => array(
                'name'          => 'İşletme Kategorileri',
                'singular_name' => 'İşletme Kategorisi',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'hierarchical'      => true,
            'rewrite'           => array( 'slug' => 'isletme-kategorisi' ),
        )
    );
}

add_action( 'init', 'lg_register_business_taxonomy' );
