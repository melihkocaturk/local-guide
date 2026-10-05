<?php
defined( 'ABSPATH' ) || exit;
?>
<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'gokturk-rehberi' ); ?>" class="small text-muted mb-4">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana Sayfa</a>
    <span class="mx-2">/</span>
    <span><?php the_title(); ?></span>
</nav>
