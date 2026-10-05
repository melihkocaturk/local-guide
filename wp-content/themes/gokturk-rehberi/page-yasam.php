<?php
/**
 * Göktürk'te Yaşam landing page.
 */
defined( 'ABSPATH' ) || exit;
get_header();

$life_category = get_category_by_slug( 'gokturkte-yasam' );
$page          = max( 1, (int) get_query_var( 'paged' ) );
$life_category_ids = array();

if ( $life_category ) {
    $life_category_ids = array_merge(
        array( (int) $life_category->term_id ),
        get_term_children( (int) $life_category->term_id, 'category' )
    );
    $life_category_ids = array_map( 'intval', $life_category_ids );
}

$life_query = new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 12,
        'paged'               => $page,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'category__in'        => $life_category_ids,
    )
);
?>

    <section class="py-5">
        <div class="container">
            <?php get_template_part( 'template-parts/breadcrumb' ); ?>

            <header class="mb-5">
                <p class="text-uppercase small fw-semibold mb-2">Göktürk'te Yaşam</p>
                <h1 class="display-6 fw-bold mb-3">Göktürk'te Yaşam</h1>
                <p class="lead text-muted mb-0">Göktürk hakkında bilmeniz gereken yerel bilgiler.</p>
            </header>

            <?php
            $life_categories = $life_category
                ? get_categories(
                    array(
                        'taxonomy'   => 'category',
                        'parent'     => (int) $life_category->term_id,
                        'hide_empty' => true,
                    )
                )
                : array();
            ?>

            <?php if ( ! empty( $life_categories ) ) : ?>
                <nav class="d-flex flex-wrap gap-2 mb-5" aria-label="Göktürk'te Yaşam kategorileri">
                    <?php foreach ( $life_categories as $category ) : ?>
                        <a class="btn btn-sm btn-outline-secondary rounded-pill" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                            <?php echo esc_html( $category->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <div class="row g-4">
                <?php if ( $life_query->have_posts() ) : ?>
                    <?php while ( $life_query->have_posts() ) : $life_query->the_post(); ?>
                        <div class="col-md-6 col-lg-4">
                            <?php get_template_part( 'template-parts/life-card' ); ?>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="col-12">
                        <div class="alert alert-light border rounded-4 mb-0">Henüz yaşam içeriği eklenmedi.</div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $life_query->max_num_pages > 1 ) : ?>
                <nav class="mt-5" aria-label="Göktürk'te Yaşam sayfaları">
                    <?php
                    echo wp_kses_post(
                        paginate_links(
                            array(
                                'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                                'format'    => '?paged=%#%',
                                'current'   => $page,
                                'total'     => $life_query->max_num_pages,
                                'mid_size'  => 1,
                                'prev_text' => '&laquo; Önceki',
                                'next_text' => 'Sonraki &raquo;',
                                'type'      => 'list',
                            )
                        )
                    );
                    ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>

<?php
wp_reset_postdata();
get_footer();
