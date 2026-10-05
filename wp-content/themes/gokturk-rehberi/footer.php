</main>

<footer class="site-footer border-top mt-5 py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-7">
                <h2 class="h5">Göktürk Rehberi</h2>
                <p class="text-muted mb-3">
                    Göktürk'ü keşfetmek için yerel rehber.
                </p>
                <p class="mb-0">
                    &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?>
                    <a href="https://www.melihkocaturk.com/" target="_blank">
                        Melih Kocatürk
                    </a>.
                    Tüm hakları saklıdır.
                </p>
            </div>
            <div class="col-md-5">
                <div class="footer-nav text-md-end">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer',
                            'container'      => false,
                            'fallback_cb'    => 'gr_footer_menu_fallback',
                            'menu_class'     => 'list-inline mb-0',
                        )
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
