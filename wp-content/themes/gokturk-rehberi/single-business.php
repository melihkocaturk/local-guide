<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container py-5">
    <?php while ( have_posts() ) : the_post(); ?>
        <?php
        $business_id = get_the_ID();
        $phone       = get_post_meta( $business_id, '_lg_phone', true );
        $email       = get_post_meta( $business_id, '_lg_email', true );
        $website     = get_post_meta( $business_id, '_lg_website', true );
        $instagram   = get_post_meta( $business_id, '_lg_instagram', true );
        $facebook    = get_post_meta( $business_id, '_lg_facebook', true );
        $address     = get_post_meta( $business_id, '_lg_address', true );
        $neighborhood = get_post_meta( $business_id, '_lg_neighborhood', true );
        $postal_code = get_post_meta( $business_id, '_lg_postal_code', true );
        $map_url     = get_post_meta( $business_id, '_lg_map_url', true );
        $hours       = function_exists( 'lg_get_business_hours' ) ? lg_get_business_hours( $business_id ) : array();
        $features    = function_exists( 'lg_get_business_features' ) ? lg_get_business_features( $business_id ) : array();
        $feature_labels = function_exists( 'lg_get_feature_labels' ) ? lg_get_feature_labels() : array();
        $status      = get_post_meta( $business_id, '_lg_business_status', true );
        $status_labels = array(
            'temporarily_closed' => 'Geçici Olarak Kapalı',
            'moved'              => 'Taşındı',
            'permanently_closed' => 'Kalıcı Olarak Kapalı',
        );
        $days = array(
            'monday'    => 'Pazartesi',
            'tuesday'   => 'Salı',
            'wednesday' => 'Çarşamba',
            'thursday'  => 'Perşembe',
            'friday'    => 'Cuma',
            'saturday'  => 'Cumartesi',
            'sunday'    => 'Pazar',
        );
        ?>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana Sayfa</a></li>
                <li class="breadcrumb-item"><a href="<?php echo esc_url( get_post_type_archive_link( 'business' ) ); ?>">İşletmeler</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
            </ol>
        </nav>

        <article <?php post_class(); ?>>
            <div class="row g-5 align-items-start">
                <div class="col-lg-8">
                    <p class="text-uppercase small fw-semibold text-success mb-2">İşletme</p>
                    <h1 class="display-5 fw-bold mb-3"><?php the_title(); ?></h1>

                    <?php if ( $status && 'active' !== $status && isset( $status_labels[ $status ] ) ) : ?>
                        <div class="alert alert-warning rounded-4 border-0 mb-4">
                            <?php echo esc_html( $status_labels[ $status ] ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="mb-5">
                            <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded-4 w-100' ) ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="business-content">
                        <?php the_content(); ?>
                    </div>

                    <?php if ( ! empty( $features ) ) : ?>
                        <section class="mt-5">
                            <h2 class="h4 fw-bold mb-3">Özellikler</h2>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ( $features as $feature ) : ?>
                                    <?php if ( isset( $feature_labels[ $feature ] ) ) : ?>
                                        <span class="badge rounded-pill text-bg-light border px-3 py-2 fw-normal">
                                            <?php echo esc_html( $feature_labels[ $feature ] ); ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if ( ! empty( $hours ) ) : ?>
                        <section class="mt-5">
                            <h2 class="h4 fw-bold mb-3">Çalışma Saatleri</h2>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <tbody>
                                        <?php foreach ( $days as $day_key => $day_label ) : ?>
                                            <?php
                                            $day = isset( $hours[ $day_key ] ) && is_array( $hours[ $day_key ] ) ? $hours[ $day_key ] : array();
                                            $closed = ! empty( $day['closed'] );
                                            $open = isset( $day['open'] ) ? $day['open'] : '';
                                            $close = isset( $day['close'] ) ? $day['close'] : '';
                                            ?>
                                            <tr>
                                                <th scope="row" class="fw-medium"><?php echo esc_html( $day_label ); ?></th>
                                                <td class="text-muted">
                                                    <?php
                                                    if ( $closed ) {
                                                        echo 'Kapalı';
                                                    } elseif ( $open && $close ) {
                                                        echo esc_html( $open . ' – ' . $close );
                                                    } else {
                                                        echo 'Belirtilmemiş';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php get_template_part( 'template-parts/report-error-box' ); ?>
                </div>

                <aside class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-lg-top" style="top: 2rem;">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-4">İşletme Bilgileri</h2>

                            <?php if ( $phone ) : ?>
                                <div class="mb-4">
                                    <div class="small text-muted mb-1">Telefon</div>
                                    <a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="fw-semibold text-decoration-none"><?php echo esc_html( $phone ); ?></a>
                                </div>
                            <?php endif; ?>

                            <?php if ( $email ) : ?>
                                <div class="mb-4">
                                    <div class="small text-muted mb-1">E-posta</div>
                                    <a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="fw-semibold text-decoration-none"><?php echo esc_html( $email ); ?></a>
                                </div>
                            <?php endif; ?>

                            <?php if ( $address || $neighborhood || $postal_code ) : ?>
                                <div class="mb-4">
                                    <div class="small text-muted mb-1">Adres</div>
                                    <?php if ( $address ) : ?><div><?php echo esc_html( $address ); ?></div><?php endif; ?>
                                    <?php if ( $neighborhood ) : ?><div><?php echo esc_html( $neighborhood ); ?></div><?php endif; ?>
                                    <?php if ( $postal_code ) : ?><div><?php echo esc_html( $postal_code ); ?></div><?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $map_url ) : ?>
                                <a class="btn btn-success w-100 mb-3" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer">Haritada Gör</a>
                            <?php endif; ?>

                            <?php if ( $website ) : ?>
                                <a class="btn btn-outline-success w-100 mb-2" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer">Web Sitesi</a>
                            <?php endif; ?>

                            <?php if ( $instagram || $facebook ) : ?>
                                <div class="d-flex gap-3 mt-3 small">
                                    <?php if ( $instagram ) : ?><a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a><?php endif; ?>
                                    <?php if ( $facebook ) : ?><a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a><?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
