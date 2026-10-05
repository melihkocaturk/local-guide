<?php
defined( 'ABSPATH' ) || exit;

$business_id = get_the_ID();
$terms       = get_the_terms( $business_id, 'business_category' );
$status      = get_post_meta( $business_id, '_lg_business_status', true );
$address     = get_post_meta( $business_id, '_lg_address', true );
$neighborhood = get_post_meta( $business_id, '_lg_neighborhood', true );
$phone       = get_post_meta( $business_id, '_lg_phone', true );

$status_labels = array(
    'active'             => 'Açık',
    'temporarily_closed' => 'Geçici olarak kapalı',
    'moved'              => 'Taşındı',
    'permanently_closed' => 'Kalıcı olarak kapalı',
);
$status_label = isset( $status_labels[ $status ] ) ? $status_labels[ $status ] : '';
?>
<article <?php post_class( 'card border-0 shadow-sm overflow-hidden' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="d-block">
            <?php the_post_thumbnail( 'medium_large', array( 'class' => 'card-img-top' ) ); ?>
        </a>
    <?php endif; ?>

    <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
            <?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
                <p class="small text-uppercase text-muted fw-semibold mb-0">
                    <?php echo esc_html( $terms[0]->name ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $status_label && 'active' !== $status ) : ?>
                <span class="badge bg-light text-dark border"><?php echo esc_html( $status_label ); ?></span>
            <?php endif; ?>
        </div>

        <h2 class="h5 card-title mb-2">
            <a class="text-decoration-none text-dark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <?php if ( has_excerpt() ) : ?>
            <p class="card-text text-muted mb-3"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
        <?php endif; ?>

        <?php if ( $address || $neighborhood ) : ?>
            <p class="small text-muted mb-2">
                <?php echo esc_html( implode( ', ', array_filter( array( $neighborhood, $address ) ) ) ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $phone ) : ?>
            <p class="small mb-0 mt-auto">
                <a class="text-decoration-none" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
                    <?php echo esc_html( $phone ); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</article>
