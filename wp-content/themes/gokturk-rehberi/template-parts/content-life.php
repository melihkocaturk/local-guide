<?php
defined( 'ABSPATH' ) || exit;

$life_parent = get_category_by_slug( 'gokturkte-yasam' );
$categories  = get_the_category();
$category    = null;

foreach ( $categories as $item ) {
    if ( $life_parent && (int) $item->term_id !== (int) $life_parent->term_id && cat_is_ancestor_of( $life_parent, $item ) ) {
        $category = $item;
        break;
    }
}
?>
<article <?php post_class(); ?>>
    <?php get_template_part( 'template-parts/breadcrumb' ); ?>
    <header class="mb-4">
        <p class="text-uppercase small fw-semibold mb-2">Göktürk'te Yaşam<?php echo $category ? ' · ' . esc_html( $category->name ) : ''; ?></p>
        <h1 class="display-6 fw-bold mb-3"><?php the_title(); ?></h1>
        <div class="text-muted"><?php echo esc_html( get_the_date() ); ?></div>
    </header>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="mb-5"><?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded-4' ) ); ?></div>
    <?php endif; ?>
    <div class="guide-content"><?php the_content(); ?></div>
</article>
