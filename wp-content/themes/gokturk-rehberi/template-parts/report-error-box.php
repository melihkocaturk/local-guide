<?php
defined( 'ABSPATH' ) || exit;

$business_id = get_the_ID();
$report_url  = add_query_arg(
    'isletme',
    $business_id,
    home_url( '/hata-bildir/' )
);
?>

<div class="p-4 bg-light rounded-4 mt-5">
    <h2 class="h5">Bilgilerde hata mı var?</h2>

    <p class="text-muted">
        İşletme bilgilerinde bir hata gördüyseniz bize bildirebilirsiniz.
    </p>

    <a class="btn btn-outline-success" href="<?php echo esc_url( $report_url ); ?>">
        Bilgi hatası bildir
    </a>
</div>