<?php
    $img_url = wp_get_attachment_image_src(get_field('recipe_image'), 'full')[0];
    header('Location: '.$img_url);