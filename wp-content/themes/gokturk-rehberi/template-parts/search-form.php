<?php
defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/arama/' ) ); ?>" class="d-flex gap-2">
    <label class="visually-hidden" for="gr-search">Arama</label>
    <input id="gr-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
           class="form-control" placeholder="Göktürk'te ne arıyorsunuz?">
    <button class="btn btn-success" type="submit">Ara</button>
</form>
