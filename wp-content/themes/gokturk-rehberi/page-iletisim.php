<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container py-5">
    <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class(); ?>>
            <p class="text-uppercase small fw-semibold">İletişim</p>
            <h1 class="display-6 fw-bold mb-4"><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
