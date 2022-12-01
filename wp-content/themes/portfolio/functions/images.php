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

function get_alt_text($image_id) {
	$alt = get_post_meta($image_id , '_wp_attachment_image_alt', true);
	$meta = wp_get_attachment_metadata($image_id);

	if(empty( $alt )){
		$alt = sprintf(
			'%s %s %s', 
			wp_get_attachment_caption($image_id), 
			$meta['file'], 
			get_image_description($image_id)
		);
	}

	return $alt;
}

function get_image($img_id, $size = 'full') {
	if (!empty($img_id)) {
		$alt = get_alt_text($img_id);
		$img_url = get_std_image($img_id, $size);
		
		return '<img src="'.$img_url.'" alt="'.$alt.'">';
	} else {
		return '';
	}
}

function get_image_description($image_id){
	$post = get_post($image_id);
	return $post->post_content;
}

new fld_images();