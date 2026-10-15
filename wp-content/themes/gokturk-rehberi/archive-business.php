<?php
defined( 'ABSPATH' ) || exit;

$selected_category = isset( $_GET['business_category'] )
    ? sanitize_title( wp_unslash( $_GET['business_category'] ) )
    : '';

$categories = get_terms(
    array(
        'taxonomy'   => 'business_category',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    )
);

get_header();
?>

<div class="container py-5">
    <header class="mb-4">
        <p class="text-uppercase small fw-semibold">İşletmeler</p>
        <h1 class="display-6 fw-bold">Aradığınız yeri bulun.</h1>
        <p class="text-muted">Göktürk'teki işletmeleri keşfedin.</p>
    </header>

    <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
        <form class="row g-2 mb-5" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'business' ) ); ?>">
            <div class="col-sm-8 col-md-5">
                <label class="visually-hidden" for="business-category-filter">Kategori</label>
                <select class="form-select" id="business-category-filter" name="business_category">
                    <option value="">Tüm kategoriler</option>
                    <?php
                    $categories_by_parent = array();

                    foreach ( $categories as $category ) {
                        $categories_by_parent[ $category->parent ][] = $category;
                    }

                    $render_categories = function ( $parent_id = 0, $depth = 0 ) use ( &$render_categories, $categories_by_parent, $selected_category ) {
                        if ( empty( $categories_by_parent[ $parent_id ] ) ) {
                            return;
                        }

                        foreach ( $categories_by_parent[ $parent_id ] as $category ) {
                            $prefix = $depth > 0
                                ? str_repeat( '— ', $depth )
                                : '';

                            printf(
                                '<option value="%s" %s>%s%s</option>',
                                esc_attr( $category->slug ),
                                selected( $selected_category, $category->slug, false ),
                                esc_html( $prefix ),
                                esc_html( $category->name )
                            );

                            $render_categories( $category->term_id, $depth + 1 );
                        }
                    };

                    $render_categories();
                    ?>
                </select>
            </div>
            <div class="col-sm-auto">
                <button class="btn btn-dark" type="submit">Filtrele</button>
            </div>
            <?php if ( $selected_category ) : ?>
                <div class="col-sm-auto d-flex align-items-center">
                    <a class="small text-decoration-none" href="<?php echo esc_url( get_post_type_archive_link( 'business' ) ); ?>">Filtreyi temizle</a>
                </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>

    <div class="row g-4">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="col-md-6 col-lg-4">
                    <?php get_template_part( 'template-parts/business-card' ); ?>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-light border">İşletme bulunamadı.</div>
            </div>
        <?php endif; ?>
    </div>

    <?php get_template_part( 'template-parts/pagination' ); ?>
</div>

<?php get_footer(); ?>
