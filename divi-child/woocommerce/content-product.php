<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	echo '<div class="product_img_container">';
	do_action( 'woocommerce_before_shop_loop_item' ); 
	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	
	?>
	<div class="product_overlay"> 
		<div class="product_overlay_details"> 
			<ul class="product_overlay_list">   
				<li><?php if (is_user_logged_in()) {
					echo do_shortcode('[yith_wcwl_add_to_wishlist]');
				}else{ echo '<a href="'.site_url('my-account').'"><i class="yith-wcwl-icon fa fa-heart"></i></a>'; }  ?></li>      
				<li><a href="<?php echo get_permalink(); ?>"><img class="img-fluid" src="<?php echo site_url(); ?>/wp-content/themes/divi-child/fpcustomization/images/cart_upload.png" alt="Image"></a></li>  
			</ul>  
		</div>
	</div> 
	<?php

	echo '</div>'; 
	echo '<div class="product_info_wrapper">';
	echo '<div class="product_price_title_container">';
		do_action( 'woocommerce_shop_loop_item_title' ); 
		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item' );
	echo '</div>'; 

	$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
	?>
	<div class="right_product_data">    
		<?php if (is_user_logged_in()) {
		echo do_shortcode('[yith_wcwl_add_to_wishlist]');
		}else{ echo '<a href="'.site_url('my-account').'"><i class="yith-wcwl-icon fa fa-heart"></i></a>'; }  ?>

		<div class="sharebtns">
			<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/share-icon.png"> </span>
			<ul class="sharepopup">
				<li><a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo get_permalink(); ?>"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
				<li><a target="_blank" href="https://twitter.com/share?url=<?php echo get_permalink(); ?>&text=<?php echo get_the_title(); ?>"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>
				<li><a target="_blank" href="https://pinterest.com/pin/create/bookmarklet/?media=<?php echo $featured_img_url; ?>&url=<?php echo get_permalink(); ?>&description=<?php echo get_the_title(); ?>"><i class="fa fa-pinterest-square" aria-hidden="true"></i></a></li>
				<li><a href="mailto:?subject=<?php echo get_the_title(); ?>&body=<?php echo get_permalink(); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></li>


			</ul>
		</div>
	</div>
	</div> 
</li>
