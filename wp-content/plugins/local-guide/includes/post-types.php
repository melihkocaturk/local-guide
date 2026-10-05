<?php
defined( 'ABSPATH' ) || exit;

function lg_register_business_post_type() {
    register_post_type(
        'business',
        array(
            'labels' => array(
                'name'          => 'İşletmeler',
                'singular_name' => 'İşletme',
                'add_new_item'  => 'Yeni İşletme Ekle',
                'edit_item'     => 'İşletmeyi Düzenle',
            ),
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'has_archive'        => 'isletmeler',
            'rewrite'            => array( 'slug' => 'isletme' ),
            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
            'menu_position'     => 20,
            'menu_icon'         => 'dashicons-store',
        )
    );
}

add_action( 'init', 'lg_register_business_post_type' );

function lg_register_error_report_post_type() {
    register_post_type(
        'error_report',
        array(
            'labels' => array(
                'name'          => 'Bilgi Hataları',
                'singular_name' => 'Bilgi Hatası',
                'edit_item'     => 'Bilgi Hatasını İncele',
            ),
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'show_in_rest'    => false,
            'supports'        => array( 'title', 'editor' ),
            'menu_icon'       => 'dashicons-warning',
        )
    );
}

add_action( 'init', 'lg_register_error_report_post_type' );
