<?php
defined( 'ABSPATH' ) || exit;

function lg_get_business_features( $business_id ) {
    $features = get_post_meta( $business_id, '_lg_features', true );
    return is_array( $features ) ? $features : array();
}

function lg_get_feature_labels() {
    return array(
        'parking'        => 'Otopark',
        'wifi'           => 'Wi-Fi',
        'outdoor'        => 'Açık Alan',
        'pet_friendly'   => 'Evcil Hayvan Dostu',
        'child_friendly' => 'Çocuk Dostu',
        'accessible'     => 'Engelli Erişimi',
    );
}
