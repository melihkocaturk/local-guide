<?php
defined( 'ABSPATH' ) || exit;
get_header();

$query_string = get_search_query();
$results_count = (int) $GLOBALS['wp_query']->found_posts;
?>

<div class="container py-5">
    <header class="mb-5">
        <p class="text-uppercase small fw-semibold text-muted mb-2">Arama</p>
        <h1 class="display-6 fw-bold mb-3">
            “<?php echo esc_html( $query_string ); ?>”
        </h1>
        <p class="text-muted mb-0">
            <?php echo esc_html( number_format_i18n( $results_count ) ); ?> sonuç bulundu.
        </p>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="row g-4">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="col-md-6 col-lg-4">
                    <?php
                    if ( 'business' === get_post_type() ) {
                        get_template_part( 'template-parts/business-card' );
                    } else {
                        $categories = get_the_category();
                        $life_parent = get_category_by_slug( 'gokturkte-yasam' );
                        $is_life = false;

                        if ( $life_parent && ! empty( $categories ) ) {
                            foreach ( $categories as $category ) {
                                if ( (int) $category->term_id === (int) $life_parent->term_id || cat_is_ancestor_of( $life_parent, $category ) ) {
                                    $is_life = true;
                                    break;
                                }
                            }
                        }

                        if ( $is_life ) {
                            get_template_part( 'template-parts/life-card' );
                        } else {
                            get_template_part( 'template-parts/guide-card' );
                        }
                    }
                    ?>
                </div>
            <?php endwhile; ?>
        </div>

        <nav class="mt-5" aria-label="Arama sonuçları sayfalama">
            <?php
            echo wp_kses_post(
                paginate_links(
                    array(
                        'type'      => 'list',
                        'prev_text' => '‹',
                        'next_text' => '›',
                    )
                )
            );
            ?>
        </nav>
    <?php else : ?>
        <div class="py-5 text-center border rounded-4 bg-light">
            <div class="col-md-6 mx-auto">
                <h2 class="h4 mb-2">Sonuç bulunamadı.</h2>
                <p class="text-muted mb-4">Farklı bir kelime veya işletme adı deneyebilirsiniz.</p>
                <?php get_template_part( 'template-parts/search-form' ); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
