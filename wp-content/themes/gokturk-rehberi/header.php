<?php
defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                Göktürk Rehberi
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#primary-navigation" aria-controls="primary-navigation"
                    aria-expanded="false" aria-label="<?php esc_attr_e( 'Menüyü aç', 'gokturk-rehberi' ); ?>">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="primary-navigation">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'fallback_cb'    => 'gr_primary_menu_fallback',
                        'menu_class'     => 'navbar-nav ms-auto',
                    )
                );
                ?>
            </div>
        </div>
    </nav>
</header>

<main id="primary" class="site-main">
