<?php 

/* Template name: Seller Dashboard */

if (!is_user_logged_in()) {
	wp_redirect(home_url());
	exit();
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php 
			
			if (have_posts() ) :
				while (have_posts() ) : the_post();
					the_content();
				endwhile;
			endif;

			// Reset Post Data
			wp_reset_postdata();
		?>	
		<?php wp_footer(); ?>
	</body>
</html>