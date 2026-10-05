<?php
defined( 'ABSPATH' ) || exit;

$categories = get_the_category();
?>
<article <?php post_class( 'guide-single' ); ?>>
    <?php get_template_part( 'template-parts/breadcrumb' ); ?>

    <header class="mb-4">
        <p class="text-uppercase small fw-semibold mb-2">Rehber</p>
        <h1 class="display-5 fw-bold mb-3"><?php the_title(); ?></h1>
        <div class="small text-muted">
            <?php echo esc_html( get_the_date() ); ?>
            <?php if ( ! empty( $categories ) ) : ?>
                · <?php echo esc_html( $categories[0]->name ); ?>
            <?php endif; ?>
        </div>
    </header>

    <?php if ( has_post_thumbnail() ) : ?>
        <div class="mb-5">
            <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded-4' ) ); ?>
        </div>
    <?php endif; ?>

    <div class="content-area guide-content">
        <?php the_content(); ?>
    </div>
</article>
