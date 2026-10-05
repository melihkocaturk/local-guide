<?php
defined( 'ABSPATH' ) || exit;

function lg_add_business_meta_boxes() {
    add_meta_box(
        'lg_business_details',
        'İşletme Bilgileri',
        'lg_render_business_details_meta_box',
        'business',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_business', 'lg_add_business_meta_boxes' );

function lg_render_business_details_meta_box( $post ) {
    wp_nonce_field( 'lg_save_business_details', 'lg_business_details_nonce' );

    $phone        = get_post_meta( $post->ID, '_lg_phone', true );
    $email        = get_post_meta( $post->ID, '_lg_email', true );
    $website      = get_post_meta( $post->ID, '_lg_website', true );
    $instagram    = get_post_meta( $post->ID, '_lg_instagram', true );
    $facebook     = get_post_meta( $post->ID, '_lg_facebook', true );
    $address      = get_post_meta( $post->ID, '_lg_address', true );
    $neighborhood = get_post_meta( $post->ID, '_lg_neighborhood', true );
    $postal_code  = get_post_meta( $post->ID, '_lg_postal_code', true );
    $map_url      = get_post_meta( $post->ID, '_lg_map_url', true );
    $hours        = lg_get_business_hours( $post->ID );
    $features     = lg_get_business_features( $post->ID );
    $status       = get_post_meta( $post->ID, '_lg_business_status', true );

    if ( ! $status ) {
        $status = 'active';
    }

    $days = array(
        'monday'    => 'Pazartesi',
        'tuesday'   => 'Salı',
        'wednesday' => 'Çarşamba',
        'thursday'  => 'Perşembe',
        'friday'    => 'Cuma',
        'saturday'  => 'Cumartesi',
        'sunday'    => 'Pazar',
    );

    $feature_labels = lg_get_feature_labels();
    $statuses = array(
        'active'             => 'Aktif',
        'temporarily_closed' => 'Geçici Olarak Kapalı',
        'moved'              => 'Taşındı',
        'permanently_closed' => 'Kalıcı Olarak Kapalı',
    );
    ?>
    <div class="lg-business-admin">
        <h3>İletişim</h3>
        <div class="lg-admin-grid">
            <p>
                <label for="lg_phone"><strong>Telefon</strong></label>
                <input class="widefat" type="text" id="lg_phone" name="lg_phone" value="<?php echo esc_attr( $phone ); ?>">
            </p>
            <p>
                <label for="lg_email"><strong>E-posta</strong></label>
                <input class="widefat" type="email" id="lg_email" name="lg_email" value="<?php echo esc_attr( $email ); ?>">
            </p>
            <p>
                <label for="lg_website"><strong>Web Sitesi</strong></label>
                <input class="widefat" type="url" id="lg_website" name="lg_website" value="<?php echo esc_attr( $website ); ?>">
            </p>
            <p>
                <label for="lg_instagram"><strong>Instagram</strong></label>
                <input class="widefat" type="url" id="lg_instagram" name="lg_instagram" value="<?php echo esc_attr( $instagram ); ?>">
            </p>
            <p>
                <label for="lg_facebook"><strong>Facebook</strong></label>
                <input class="widefat" type="url" id="lg_facebook" name="lg_facebook" value="<?php echo esc_attr( $facebook ); ?>">
            </p>
        </div>

        <h3>Adres</h3>
        <div class="lg-admin-grid">
            <p>
                <label for="lg_address"><strong>Adres</strong></label>
                <textarea class="widefat" id="lg_address" name="lg_address" rows="3"><?php echo esc_textarea( $address ); ?></textarea>
            </p>
            <p>
                <label for="lg_neighborhood"><strong>Mahalle / Bölge</strong></label>
                <input class="widefat" type="text" id="lg_neighborhood" name="lg_neighborhood" value="<?php echo esc_attr( $neighborhood ); ?>">
            </p>
            <p>
                <label for="lg_postal_code"><strong>Posta Kodu</strong></label>
                <input class="widefat" type="text" id="lg_postal_code" name="lg_postal_code" value="<?php echo esc_attr( $postal_code ); ?>">
            </p>
            <p>
                <label for="lg_map_url"><strong>Harita Bağlantısı</strong></label>
                <input class="widefat" type="url" id="lg_map_url" name="lg_map_url" value="<?php echo esc_attr( $map_url ); ?>">
            </p>
        </div>

        <h3>Çalışma Saatleri</h3>
        <table class="widefat striped lg-hours-table">
            <thead>
                <tr>
                    <th>Gün</th>
                    <th>Açılış</th>
                    <th>Kapanış</th>
                    <th>Kapalı</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $days as $day_key => $day_label ) :
                    $day = isset( $hours[ $day_key ] ) ? $hours[ $day_key ] : array();
                    $open = isset( $day['open'] ) ? $day['open'] : '';
                    $close = isset( $day['close'] ) ? $day['close'] : '';
                    $closed = ! empty( $day['closed'] );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $day_label ); ?></strong></td>
                        <td><input type="time" name="lg_hours[<?php echo esc_attr( $day_key ); ?>][open]" value="<?php echo esc_attr( $open ); ?>"></td>
                        <td><input type="time" name="lg_hours[<?php echo esc_attr( $day_key ); ?>][close]" value="<?php echo esc_attr( $close ); ?>"></td>
                        <td><label><input type="checkbox" name="lg_hours[<?php echo esc_attr( $day_key ); ?>][closed]" value="1" <?php checked( $closed ); ?>> Kapalı</label></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Özellikler</h3>
        <div class="lg-feature-grid">
            <?php foreach ( $feature_labels as $feature_key => $feature_label ) : ?>
                <label>
                    <input type="checkbox" name="lg_features[]" value="<?php echo esc_attr( $feature_key ); ?>" <?php checked( in_array( $feature_key, $features, true ) ); ?>>
                    <?php echo esc_html( $feature_label ); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <h3>İşletme Durumu</h3>
        <p>
            <select name="lg_business_status" class="widefat">
                <?php foreach ( $statuses as $status_key => $status_label ) : ?>
                    <option value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $status, $status_key ); ?>><?php echo esc_html( $status_label ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    </div>
    <?php
}

function lg_save_business_details( $post_id ) {
    if ( ! isset( $_POST['lg_business_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lg_business_details_nonce'] ) ), 'lg_save_business_details' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( wp_is_post_revision( $post_id ) || 'business' !== get_post_type( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_fields = array(
        'lg_phone'        => '_lg_phone',
        'lg_address'      => '_lg_address',
        'lg_neighborhood' => '_lg_neighborhood',
        'lg_postal_code'  => '_lg_postal_code',
    );

    foreach ( $text_fields as $request_key => $meta_key ) {
        $value = isset( $_POST[ $request_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $request_key ] ) ) : '';
        lg_update_or_delete_meta( $post_id, $meta_key, $value );
    }

    $email = isset( $_POST['lg_email'] ) ? sanitize_email( wp_unslash( $_POST['lg_email'] ) ) : '';
    lg_update_or_delete_meta( $post_id, '_lg_email', $email );

    $url_fields = array(
        'lg_website'   => '_lg_website',
        'lg_instagram' => '_lg_instagram',
        'lg_facebook'  => '_lg_facebook',
        'lg_map_url'   => '_lg_map_url',
    );

    foreach ( $url_fields as $request_key => $meta_key ) {
        $value = isset( $_POST[ $request_key ] ) ? esc_url_raw( wp_unslash( $_POST[ $request_key ] ) ) : '';
        lg_update_or_delete_meta( $post_id, $meta_key, $value );
    }

    $hours = isset( $_POST['lg_hours'] ) && is_array( $_POST['lg_hours'] ) ? wp_unslash( $_POST['lg_hours'] ) : array();
    update_post_meta( $post_id, '_lg_business_hours', lg_sanitize_hours( $hours ) );

    $features = isset( $_POST['lg_features'] ) && is_array( $_POST['lg_features'] ) ? wp_unslash( $_POST['lg_features'] ) : array();
    update_post_meta( $post_id, '_lg_features', lg_sanitize_features( $features ) );

    $status = isset( $_POST['lg_business_status'] ) ? wp_unslash( $_POST['lg_business_status'] ) : 'active';
    update_post_meta( $post_id, '_lg_business_status', lg_sanitize_business_status( $status ) );
}
add_action( 'save_post_business', 'lg_save_business_details' );

function lg_update_or_delete_meta( $post_id, $meta_key, $value ) {
    if ( '' === $value ) {
        delete_post_meta( $post_id, $meta_key );
        return;
    }

    update_post_meta( $post_id, $meta_key, $value );
}

function lg_enqueue_business_admin_assets( $hook ) {
    $screen = get_current_screen();

    if ( ! $screen || 'business' !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    wp_enqueue_style( 'lg-business-admin', LG_PLUGIN_URL . 'assets/css/admin.css', array(), LG_VERSION );
}
add_action( 'admin_enqueue_scripts', 'lg_enqueue_business_admin_assets' );
