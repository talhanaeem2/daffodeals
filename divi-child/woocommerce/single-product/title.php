<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

the_title( '<h1 class="product_title entry-title">', '</h1>' );
?>

<div class="single-product-wishlist">
	<?php 
	if (is_user_logged_in()) {
		echo do_shortcode('[yith_wcwl_add_to_wishlist]');
	}else{ 
		echo '<a href="'.site_url('my-account').'"><i class="yith-wcwl-icon fa fa-heart"></i></a>'; 
	}  
	?>
	<input type="hidden" id="deal_comment_post_id" value="<?php echo get_the_ID(); ?>">
</div>
