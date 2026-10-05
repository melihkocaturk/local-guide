<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="gr-hero py-5">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9">
                <p class="text-uppercase small fw-semibold mb-3">Göktürk Rehberi</p>
                <h1 class="display-4 fw-bold mb-4">Göktürk'ü keşfedin.</h1>
                <p class="lead text-muted mb-4">
                    İşletmeler, rehberler ve Göktürk'te yaşam hakkında yerel bilgiler.
                </p>

                <form class="row g-2 justify-content-center" role="search"
                      method="get" action="<?php echo esc_url( home_url( '/arama/' ) ); ?>">
                    <div class="col-md-8">
                        <label class="visually-hidden" for="gr-home-search">Arama</label>
                        <input id="gr-home-search" type="search" name="s"
                               class="form-control form-control-lg"
                               placeholder="Göktürk'te ne arıyorsunuz?">
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-success btn-lg w-100" type="submit">Ara</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$business_query = post_type_exists( 'business' ) ? new WP_Query(
    array(
        'post_type'           => 'business',
        'posts_per_page'      => 6,
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    )
) : null;

$guide_category = get_category_by_slug( 'rehber' );

$guide_ids = $guide_category
    ? array_merge(
        array( (int) $guide_category->term_id ),
        get_term_children( (int) $guide_category->term_id, 'category' )
    )
    : array();

$guide_ids = array_map( 'intval', $guide_ids );

$guide_query = ! empty( $guide_ids ) ? new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 3,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'category__in'        => $guide_ids,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'no_found_rows'       => true,
    )
) : null;

$life_category = get_category_by_slug( 'gokturkte-yasam' );

$life_ids      = $life_category ? array_merge(
    array( (int) $life_category->term_id ),
    get_term_children( (int) $life_category->term_id, 'category' )
) : array();

$life_ids      = array_map( 'intval', $life_ids );

$life_query    = ! empty( $life_ids ) ? new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 3,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'category__in'        => $life_ids,
        'no_found_rows'       => true,
    )
) : null;
?>

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div>
                <p class="text-uppercase small fw-semibold mb-2">İşletmeler</p>
                <h2 class="h3 mb-0">İşletmeleri keşfedin</h2>
            </div>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'business' ) ?: home_url( '/isletmeler/' ) ); ?>" class="fw-semibold">Tüm işletmeler</a>
        </div>

        <div class="row g-4">
            <?php if ( $business_query && $business_query->have_posts() ) : ?>
                <?php while ( $business_query->have_posts() ) : $business_query->the_post(); ?>
                    <div class="col-md-6 col-lg-4">
                        <?php get_template_part( 'template-parts/business-card' ); ?>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12">
                    <div class="alert alert-light border rounded-4 mb-0">Henüz işletme eklenmedi.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div>
                <p class="text-uppercase small fw-semibold mb-2">Rehber</p>
                <h2 class="h3 mb-0">Göktürk'te ne yapabilirsiniz?</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/rehber/' ) ); ?>" class="fw-semibold">Tüm rehberler</a>
        </div>

        <div class="row g-4">
            <?php if ( $guide_query && $guide_query->have_posts() ) : ?>
                <?php while ( $guide_query->have_posts() ) : $guide_query->the_post(); ?>
                    <div class="col-md-6 col-lg-4">
                        <?php get_template_part( 'template-parts/guide-card' ); ?>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12">
                    <div class="alert alert-light border rounded-4 mb-0">Henüz rehber içeriği eklenmedi.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div>
                <p class="text-uppercase small fw-semibold mb-2">Göktürk'te Yaşam</p>
                <h2 class="h3 mb-0">Göktürk hakkında daha fazlasını öğrenin</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/yasam/' ) ); ?>" class="fw-semibold">Tüm yaşam içerikleri</a>
        </div>

        <div class="row g-4">
            <?php if ( $life_query && $life_query->have_posts() ) : ?>
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
    </div>
</section>

<?php
wp_reset_postdata();
get_footer();
