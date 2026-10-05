<?php
defined( 'ABSPATH' ) || exit;

$reported = isset( $_GET['reported'] ) && '1' === $_GET['reported'];

$business_id = isset( $_GET['isletme'] )
    ? absint( $_GET['isletme'] )
    : 0;

$business = $business_id
    ? get_post( $business_id )
    : null;

if ( ! $business || 'business' !== get_post_type( $business_id ) ) {
    $business_id = 0;
    $business    = null;
}

get_header();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <p class="text-uppercase small fw-semibold"></p>
            <h1 class="display-6 fw-bold">Bir bilgi hatası mı gördünüz?</h1>
            <p class="text-muted mb-4">
                İşletme veya içerik bilgilerindeki hataları bize bildirebilirsiniz.
            </p>

            <?php if ( $reported ) : ?>
                <div class="alert alert-success rounded-4 mb-4" role="alert">
                    <strong>Bildiriminiz alındı.</strong>
                    Teşekkür ederiz. Bildirdiğiniz bilgi ekibimiz tarafından incelenecektir.
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="p-4 border rounded-4">
                <input type="hidden" name="action" value="lg_submit_error_report">
                <?php wp_nonce_field( 'lg_submit_error_report', 'lg_error_report_nonce' ); ?>

                <div class="mb-3">
                    <label class="form-label" for="lg-business">İşletme</label>

                    <?php if ( $business ) : ?>

                        <input
                            type="hidden"
                            name="business_id"
                            value="<?php echo esc_attr( $business_id ); ?>"
                        >

                        <input
                            class="form-control"
                            id="lg-business"
                            type="text"
                            value="<?php echo esc_attr( get_the_title( $business_id ) ); ?>"
                            readonly
                        >

                    <?php else : ?>

                        <input
                            class="form-control"
                            id="lg-business"
                            name="business_id"
                            type="number"
                            min="1"
                            required
                        >

                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="lg-type">Hata türü</label>
                    <select class="form-select" id="lg-type" name="error_type" required>
                        <option value="">Seçiniz</option>
                        <option value="business_closed">İşletme kapanmış</option>
                        <option value="business_moved">İşletme taşınmış</option>
                        <option value="wrong_address">Adres yanlış</option>
                        <option value="wrong_phone">Telefon yanlış</option>
                        <option value="wrong_website">Web sitesi yanlış</option>
                        <option value="wrong_social">Sosyal medya bilgisi yanlış</option>
                        <option value="wrong_hours">Çalışma saatleri yanlış</option>
                        <option value="wrong_content">İçerik bilgisi yanlış</option>
                        <option value="other">Diğer</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="lg-description">Açıklama</label>
                    <textarea class="form-control" id="lg-description" name="description" rows="5" required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="lg-email">E-posta (opsiyonel)</label>
                    <input class="form-control" id="lg-email" name="email" type="email">
                </div>

                <button class="btn btn-success" type="submit">Bildirimi gönder</button>
            </form>
        </div>
    </div>
</div>

<?php get_footer(); ?>
