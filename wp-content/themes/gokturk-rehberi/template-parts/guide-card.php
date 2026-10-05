<?php
defined( 'ABSPATH' ) || exit;

$categories = get_the_category();
$category   = ! empty( $categories ) ? $categories[0] : null;
?>
<article <?php post_class( 'card border-0 shadow-sm overflow-hidden' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
            <?php the_post_thumbnail( 'medium_large', array( 'class' => 'card-img-top', 'loading' => 'lazy' ) ); ?>
        </a>
    <?php endif; ?>
    <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="small text-uppercase fw-semibold">Rehber</span>
            <?php if ( $category && 'rehber' !== $category->slug ) : ?>
                <span class="small text-muted">• <?php echo esc_html( $category->name ); ?></span>
            <?php endif; ?>
        </div>
        <h2 class="h5 card-title mb-3">
            <a class="text-decoration-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <p class="card-text text-muted mb-0"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
    </div>
</article>
