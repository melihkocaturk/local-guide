<?php
defined( 'ABSPATH' ) || exit;

the_posts_pagination(
    array(
        'mid_size'  => 1,
        'prev_text' => '&laquo; Önceki',
        'next_text' => 'Sonraki &raquo;',
        'type'      => 'list',
    )
);
