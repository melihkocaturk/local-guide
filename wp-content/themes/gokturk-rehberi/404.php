<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container py-5 text-center">
    <p class="display-1 fw-bold mb-0">404</p>
    <h1 class="h2 mb-3">Sayfa bulunamadı</h1>
    <p class="text-muted mb-4">Aradığınız sayfa mevcut olmayabilir.</p>
    <a class="btn btn-success" href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana sayfaya dön</a>
</div>

<?php get_footer(); ?>
