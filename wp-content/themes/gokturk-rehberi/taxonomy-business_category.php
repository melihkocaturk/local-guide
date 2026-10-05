<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container py-5">
    <header class="mb-5">
        <p class="text-uppercase small fw-semibold">İşletme kategorisi</p>
        <h1 class="display-6 fw-bold"><?php single_term_title(); ?></h1>
        <?php the_archive_description( '<div class="text-muted mt-3">', '</div>' ); ?>
    </header>

    <div class="row g-4">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="col-md-6 col-lg-4">
                    <?php get_template_part( 'template-parts/business-card' ); ?>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-light border">Bu kategoride işletme bulunamadı.</div>
            </div>
        <?php endif; ?>
    </div>

    <?php get_template_part( 'template-parts/pagination' ); ?>
</div>

<?php get_footer(); ?>
