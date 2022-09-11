
	<?php
		wp_footer();
	?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<?php
		if (function_exists('extra_footer_script')) {
			extra_footer_script();
		}
	?>
</body>
</html>