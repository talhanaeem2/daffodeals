<?php 
	global $product;
	$pid = get_the_ID();
?>
<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
<div class="product-element-top">
	<a href="<?php echo esc_url( get_permalink() ); ?>">
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked basel_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
	</a>
	<?php basel_hover_image(); ?>	
</div>
<?php 
	basel_swatches_list();
?>

<div class="product-info">
<div class="product-list-info">
	<?php
		/**
		 * woocommerce_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );
	?>
	<?php basel_product_brands_links(); ?>
	<?php
		/**
		 * woocommerce_after_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>
</div>

<div class="woocommerce-product-list-like">
	<?php 
	if (is_user_logged_in()) {
		# code...
		basel_wishlist_btn(); 
	}else{
		?>
		<a href="#" rel="nofollow" data-kcmodal="kcmodal" data-target="#login-popup" class="login_to_wishlist ">
        </a>        
		<?php 
	} 
	?>
	<span class="likecount">
		<?php $count = kc_count_product_likes($pid);
		echo !empty($count)?$count:'';
		?>
	</span>
</div>
</div>

<?php if ( basel_loop_prop( 'progress_bar' ) ): ?>
	<?php basel_stock_progress_bar(); ?>
<?php endif ?>

<?php if ( basel_loop_prop( 'timer' ) ): ?>
	<?php basel_product_sale_countdown(); ?>
<?php endif ?>
