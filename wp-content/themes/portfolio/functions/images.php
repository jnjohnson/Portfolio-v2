<?php

class fld_images {
	function __construct() {
		//false = do not crop, instead set the maxium width/height to below and keep ratio as uploaded
		add_image_size( 'largest_image', 1800, 1200, false);
	}
}

function get_std_image($image_id, $size = 'largest_image') {
	if (empty($image_id)) {
		return "";
	}
	$src = wp_get_attachment_image_src($image_id, $size);
	if ($src !== false) {
		return $src[0];
	}

	return "";
}

new fld_images();