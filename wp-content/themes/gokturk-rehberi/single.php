<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>

    <div class="container py-5">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
            $categories       = get_the_category();
            $guide_parent     = get_category_by_slug( 'rehber' );
            $life_parent      = get_category_by_slug( 'gokturkte-yasam' );
            $is_guide         = false;
            $is_life          = false;

            foreach ( $categories as $category ) {
                if ( $guide_parent && ( (int) $category->term_id === (int) $guide_parent->term_id || cat_is_ancestor_of( $guide_parent, $category ) ) ) {
                    $is_guide = true;
                }

                if ( $life_parent && ( (int) $category->term_id === (int) $life_parent->term_id || cat_is_ancestor_of( $life_parent, $category ) ) ) {
                    $is_life = true;
                }
            }

            if ( $is_guide ) {
                get_template_part( 'template-parts/content', 'guide' );
            } elseif ( $is_life ) {
                get_template_part( 'template-parts/content', 'life' );
            } else {
                ?>
                <article <?php post_class(); ?>>
                    <h1 class="display-6 fw-bold mb-4"><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </article>
                <?php
            }
            ?>
        <?php endwhile; ?>
    </div>

<?php get_footer(); ?>
