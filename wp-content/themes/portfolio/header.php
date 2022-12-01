<?php
	ftheme::load_partials();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta content="width=device-width" name="viewport">
	<title><?php
        $title = '';
        if (!is_archive()) {
            $title = get_the_title();
        } else {
            $title = ucfirst($wp_query->query['post_type']);
        }
        echo $title . ' - Jimmy Johnson\'s Portfolio';
	?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c1ed880e92.js" crossorigin="anonymous"></script>
	<?php
		wp_head();
	?>
</head>
<body <?php body_class(); ?>>
<nav>
    <div tabindex="0" class="mobile-nav-button">
        <span></span>
    </div>
<?php
    wp_nav_menu(array(
        'menu'  => 'header'
    ));
?>
</nav>