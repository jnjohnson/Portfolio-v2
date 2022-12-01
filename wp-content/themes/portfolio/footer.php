    <footer>
        <div class="wrap">
        <?php
            wp_nav_menu(array(
                'menu'  => 'header'
            ));
        ?>
            <div class="social">
                <a class="github" target="_blank" href="{$about['github']}">Github <i class="fa-brands fa-github"></i></a>
                <a class="linkedin" target="_blank" href="{$about['linkedin']}">LinkedIn <i class="fa-brands fa-linkedin"></i></a>
            </div>
        </div>
    </footer>
	<?php
		wp_footer();
	?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>