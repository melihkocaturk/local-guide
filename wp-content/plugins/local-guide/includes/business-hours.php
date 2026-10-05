<?php
defined( 'ABSPATH' ) || exit;

function lg_get_business_hours( $business_id ) {
    $hours = get_post_meta( $business_id, '_lg_business_hours', true );
    return is_array( $hours ) ? $hours : array();
}
