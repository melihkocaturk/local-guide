<?php
defined( 'ABSPATH' ) || exit;

function lg_error_report_types() {
    return array(
        'business_closed' => 'İşletme kapanmış',
        'business_moved'  => 'İşletme taşınmış',
        'wrong_address'   => 'Adres yanlış',
        'wrong_phone'     => 'Telefon yanlış',
        'wrong_website'   => 'Web sitesi yanlış',
        'wrong_social'    => 'Sosyal medya bilgisi yanlış',
        'wrong_hours'     => 'Çalışma saatleri yanlış',
        'wrong_content'   => 'İçerik bilgisi yanlış',
        'other'           => 'Diğer',
    );
}

function lg_handle_error_report() {
    if (
        ! isset( $_POST['lg_error_report_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['lg_error_report_nonce'] ) ),
            'lg_submit_error_report'
        )
    ) {
        wp_die( esc_html__( 'Geçersiz istek.', 'local-guide' ), 403 );
    }

    $business_id = isset( $_POST['business_id'] ) ? absint( $_POST['business_id'] ) : 0;
    $error_type  = isset( $_POST['error_type'] ) ? sanitize_key( $_POST['error_type'] ) : '';
    $description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
    $email       = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

    if (
        ! $business_id ||
        'business' !== get_post_type( $business_id ) ||
        'publish' !== get_post_status( $business_id )
    ) {
        wp_die( esc_html__( 'Geçersiz işletme.', 'local-guide' ), 400 );
    }

    $types = lg_error_report_types();

    if ( ! isset( $types[ $error_type ] ) || empty( $description ) ) {
        wp_die( esc_html__( 'Eksik veya geçersiz bilgi.', 'local-guide' ), 400 );
    }

    $report_id = wp_insert_post(
        array(
            'post_type'    => 'error_report',
            'post_status'  => 'publish',
            'post_title'   => $types[ $error_type ] . ' - #' . $business_id,
            'post_content' => $description,
        ),
        true
    );

    if ( is_wp_error( $report_id ) ) {
        wp_die( esc_html__( 'Bildirim kaydedilemedi.', 'local-guide' ), 500 );
    }

    update_post_meta( $report_id, '_lg_report_business', $business_id );
    update_post_meta( $report_id, '_lg_report_type', $error_type );
    update_post_meta( $report_id, '_lg_report_description', $description );
    update_post_meta( $report_id, '_lg_report_email', $email );
    update_post_meta( $report_id, '_lg_report_status', 'pending' );

    wp_safe_redirect( add_query_arg( 'reported', '1', wp_get_referer() ?: home_url( '/' ) ) );
    exit;
}

add_action( 'admin_post_nopriv_lg_submit_error_report', 'lg_handle_error_report' );
add_action( 'admin_post_lg_submit_error_report', 'lg_handle_error_report' );


function lg_error_report_columns( $columns ) {
    return array(
        'cb'       => isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox" />',
        'title'    => 'Bildirim',
        'business' => 'İşletme',
        'type'     => 'Hata Türü',
        'status'   => 'Durum',
        'date'     => 'Tarih',
    );
}
add_filter( 'manage_error_report_posts_columns', 'lg_error_report_columns' );

function lg_error_report_column_content( $column, $post_id ) {
    if ( 'business' === $column ) {
        $business_id = absint( get_post_meta( $post_id, '_lg_report_business', true ) );
        echo $business_id ? esc_html( get_the_title( $business_id ) ) : '—';
        return;
    }

    if ( 'type' === $column ) {
        $type  = get_post_meta( $post_id, '_lg_report_type', true );
        $types = lg_error_report_types();
        echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : '—' );
        return;
    }

    if ( 'status' === $column ) {
        $status = get_post_meta( $post_id, '_lg_report_status', true );
        $labels = array(
            'pending'  => 'Bekliyor',
            'reviewed' => 'İncelendi',
            'resolved' => 'Çözüldü',
            'rejected' => 'Reddedildi',
        );
        echo esc_html( isset( $labels[ $status ] ) ? $labels[ $status ] : '—' );
    }
}
add_action( 'manage_error_report_posts_custom_column', 'lg_error_report_column_content', 10, 2 );


function lg_add_error_report_meta_box() {
    add_meta_box(
        'lg_error_report_details',
        'Bildirim Detayları',
        'lg_render_error_report_meta_box',
        'error_report',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_error_report', 'lg_add_error_report_meta_box' );

function lg_render_error_report_meta_box( $post ) {
    wp_nonce_field( 'lg_save_error_report', 'lg_error_report_admin_nonce' );

    $business_id = absint( get_post_meta( $post->ID, '_lg_report_business', true ) );
    $type        = get_post_meta( $post->ID, '_lg_report_type', true );
    $description = get_post_meta( $post->ID, '_lg_report_description', true );
    $email       = get_post_meta( $post->ID, '_lg_report_email', true );
    $status      = get_post_meta( $post->ID, '_lg_report_status', true );

    if ( ! $status ) {
        $status = 'pending';
    }

    $statuses = array(
        'pending'  => 'Bekliyor',
        'reviewed' => 'İncelendi',
        'resolved' => 'Çözüldü',
        'rejected' => 'Reddedildi',
    );
    $types = lg_error_report_types();
    ?>
    <div class="lg-error-report-admin">
        <p><strong>İşletme:</strong><br>
            <?php if ( $business_id && get_post( $business_id ) ) : ?>
                <a href="<?php echo esc_url( get_edit_post_link( $business_id ) ); ?>">
                    <?php echo esc_html( get_the_title( $business_id ) ); ?>
                </a>
            <?php else : ?>
                —
            <?php endif; ?>
        </p>
        <p><strong>Hata Türü:</strong><br>
            <?php echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : '—' ); ?>
        </p>
        <p><strong>Bildirim:</strong><br>
            <?php echo nl2br( esc_html( $description ) ); ?>
        </p>
        <?php if ( $email ) : ?>
            <p><strong>E-posta:</strong><br>
                <a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
            </p>
        <?php endif; ?>
        <p>
            <label for="lg_report_status"><strong>Durum</strong></label>
            <select class="widefat" id="lg_report_status" name="lg_report_status">
                <?php foreach ( $statuses as $status_key => $status_label ) : ?>
                    <option value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $status, $status_key ); ?>><?php echo esc_html( $status_label ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    </div>
    <?php
}

function lg_save_error_report_details( $post_id ) {
    if (
        ! isset( $_POST['lg_error_report_admin_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['lg_error_report_admin_nonce'] ) ),
            'lg_save_error_report'
        )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( wp_is_post_revision( $post_id ) || 'error_report' !== get_post_type( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $allowed_statuses = array( 'pending', 'reviewed', 'resolved', 'rejected' );
    $status = isset( $_POST['lg_report_status'] ) ? sanitize_key( wp_unslash( $_POST['lg_report_status'] ) ) : 'pending';

    if ( ! in_array( $status, $allowed_statuses, true ) ) {
        $status = 'pending';
    }

    update_post_meta( $post_id, '_lg_report_status', $status );
}
add_action( 'save_post_error_report', 'lg_save_error_report_details' );
