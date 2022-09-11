<?php
	ftheme::load_partials();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta content="width=device-width" name="viewport">
	<title><?php
		echo get_bloginfo('name');
	?></title>
	<?php /*
	<link href="https://fonts.googleapis.com/css?family=Work+Sans:400,500,700&display=swap" rel="stylesheet">
	*/ ?>
	<?php
		wp_head();
	?>
</head>
<body <?php body_class(); ?>>
