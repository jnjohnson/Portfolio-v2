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
<nav class="navbar navbar-dark navbar-expand-md bg-dark-gray">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto">
            <li class="nav-item mx-3">
                <a class="nav-link" href="#about-section">ABOUT</a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link" href="#past-work-section">PAST WORK</a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link" href="#contact-section">CONTACT</a>
            </li>
            <li class="nav-item mx-3">
                <a class="resume-link" href="/wp-content/themes/portfolio/documents/Resume.pdf">RESUME</a>
            </li>
        </ul>
    </div>
</nav>
