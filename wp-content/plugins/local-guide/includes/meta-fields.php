<?php
defined( 'ABSPATH' ) || exit;

function lg_register_meta_fields() {
    $fields = array(
        '_lg_phone'            => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
        '_lg_email'            => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
        '_lg_website'          => array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
        '_lg_address'          => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
        '_lg_neighborhood'     => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
        '_lg_postal_code'      => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
        '_lg_map_url'          => array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
        '_lg_instagram'        => array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
        '_lg_facebook'         => array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
        '_lg_business_hours'   => array( 'type' => 'array', 'sanitize_callback' => 'lg_sanitize_hours' ),
        '_lg_features'         => array( 'type' => 'array', 'sanitize_callback' => 'lg_sanitize_features' ),
        '_lg_business_status'  => array( 'type' => 'string', 'sanitize_callback' => 'lg_sanitize_business_status' ),
    );

    foreach ( $fields as $key => $args ) {
        register_post_meta(
            'business',
            $key,
            array(
                'type'              => $args['type'],
                'single'            => true,
                'show_in_rest'      => false,
                'sanitize_callback' => $args['sanitize_callback'],
            )
        );
    }
}
add_action( 'init', 'lg_register_meta_fields' );

function lg_sanitize_hours( $value ) {
    if ( ! is_array( $value ) ) {
        return array();
    }

    $days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
    $clean = array();

    foreach ( $days as $day ) {
        if ( empty( $value[ $day ] ) || ! is_array( $value[ $day ] ) ) {
            continue;
        }

        $clean[ $day ] = array(
            'closed' => ! empty( $value[ $day ]['closed'] ),
            'open'   => isset( $value[ $day ]['open'] ) ? sanitize_text_field( $value[ $day ]['open'] ) : '',
            'close'  => isset( $value[ $day ]['close'] ) ? sanitize_text_field( $value[ $day ]['close'] ) : '',
        );
    }

    return $clean;
}

function lg_sanitize_features( $value ) {
    $allowed = array( 'parking', 'wifi', 'outdoor', 'pet_friendly', 'child_friendly', 'accessible' );

    if ( ! is_array( $value ) ) {
        return array();
    }

    return array_values( array_intersect( $allowed, array_map( 'sanitize_key', $value ) ) );
}

function lg_sanitize_business_status( $value ) {
    $allowed = array( 'active', 'temporarily_closed', 'moved', 'permanently_closed' );
    $value   = sanitize_key( $value );

    return in_array( $value, $allowed, true ) ? $value : 'active';
}
