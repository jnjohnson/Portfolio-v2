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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet">
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
