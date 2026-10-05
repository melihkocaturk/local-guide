<?php
/**
 * Editorial category archive.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$category = get_queried_object();

$guide = get_category_by_slug( 'rehber' );
$life  = get_category_by_slug( 'gokturkte-yasam' );

$is_guide_category = $category instanceof WP_Term
	&& 'category' === $category->taxonomy
	&& $guide
	&& (
		(int) $category->term_id === (int) $guide->term_id
		|| cat_is_ancestor_of( $guide, $category )
	);

$is_life_category = $category instanceof WP_Term
	&& 'category' === $category->taxonomy
	&& $life
	&& (
		(int) $category->term_id === (int) $life->term_id
		|| cat_is_ancestor_of( $life, $category )
	);

$section_url   = '';
$section_label = '';
$card_template = '';

if ( $is_guide_category ) {
	$section_url   = home_url( '/rehber/' );
	$section_label = 'Rehber';
	$card_template = 'guide-card';
} elseif ( $is_life_category ) {
	$section_url   = home_url( '/yasam/' );
	$section_label = "Göktürk'te Yaşam";
	$card_template = 'life-card';
}
?>

<section class="py-5">
	<div class="container">

		<?php if ( $section_url && $section_label && $card_template ) : ?>

			<nav
				aria-label="<?php esc_attr_e( 'Breadcrumb', 'gokturk-rehberi' ); ?>"
				class="small text-muted mb-4"
			>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					Ana Sayfa
				</a>

				<span class="mx-2">/</span>

				<a href="<?php echo esc_url( $section_url ); ?>">
					<?php echo esc_html( $section_label ); ?>
				</a>

				<?php if ( (int) $category->term_id !== (int) get_category_by_slug( $is_guide_category ? 'rehber' : 'gokturkte-yasam' )->term_id ) : ?>
					<span class="mx-2">/</span>
					<span><?php echo esc_html( $category->name ); ?></span>
				<?php endif; ?>
			</nav>

			<header class="mb-5">
				<p class="text-uppercase small fw-semibold mb-2">
					<?php echo esc_html( $section_label ); ?>
				</p>

				<h1 class="display-6 fw-bold mb-3">
					<?php echo esc_html( $category->name ); ?>
				</h1>

				<?php if ( ! empty( $category->description ) ) : ?>
					<div class="lead text-muted">
						<?php echo wp_kses_post( $category->description ); ?>
					</div>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>

				<div class="row g-4">
					<?php while ( have_posts() ) : the_post(); ?>

						<div class="col-md-6 col-lg-4">
							<?php get_template_part( 'template-parts/' . $card_template ); ?>
						</div>

					<?php endwhile; ?>
				</div>

				<?php get_template_part( 'template-parts/pagination' ); ?>

			<?php else : ?>

				<div class="alert alert-light border rounded-4 mb-0">
					Bu kategoride henüz içerik bulunmuyor.
				</div>

			<?php endif; ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/breadcrumb' ); ?>

			<header class="mb-5">
				<h1 class="display-6 fw-bold mb-3">
					<?php single_cat_title(); ?>
				</h1>

				<?php if ( category_description() ) : ?>
					<div class="text-muted">
						<?php echo wp_kses_post( category_description() ); ?>
					</div>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>

				<div class="row g-4">
					<?php while ( have_posts() ) : the_post(); ?>

						<div class="col-md-6 col-lg-4">
							<?php get_template_part( 'template-parts/guide-card' ); ?>
						</div>

					<?php endwhile; ?>
				</div>

				<?php get_template_part( 'template-parts/pagination' ); ?>

			<?php else : ?>

				<div class="alert alert-light border rounded-4 mb-0">
					Bu kategoride henüz içerik bulunmuyor.
				</div>

			<?php endif; ?>

		<?php endif; ?>

	</div>
</section>

<?php get_footer(); ?>