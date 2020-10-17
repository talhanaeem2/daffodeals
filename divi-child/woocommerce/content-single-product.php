<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

$pid = get_the_ID();
$date_data = get_deal_time_difference($pid); 

$additional_information = get_post_meta( $pid, '_additional_information', true);

if (!empty($date_data)) {
	$dayleft = $date_data['dayleft'];
	$enddate = $date_data['enddate'];
?>

	<div id="deal-expire-timer" data-enddeal="<?php echo $dayleft; ?>" data-time="<?php echo $enddate; ?>" class="deal-expire-timer-bar">
		<span><?php _e('Deal Ends', 'woocommerce'); ?>:</span>
		<ul id="clockdiv">
			<li><span class="hours">23</span></li>
			<li><span class="minutes">55</span></li>
			<li><span class="seconds">25</span></li>
		</ul>
		<span>(<?php echo !empty($dayleft)? sprintf( _n( 'Today', '%s days', $dayleft, 'woocommerce' ), $dayleft ):__('TIME LEFT', 'woocommerce'); ?>)</span>
	</div>
<?php } ?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>


	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );

		/*if (!empty($additional_information)) {
			?>
			<div class="additional_information">
				<span><strong>Additional Information</strong></span>
				<p><?php echo $additional_information; ?></p>
			</div>
			<?php 
		}*/
		$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
		?>
		<ul class="sharebtndetails">
			<li><a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo get_permalink(); ?>"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
			<li><a target="_blank" href="https://twitter.com/share?url=<?php echo get_permalink(); ?>&text=<?php echo get_the_title(); ?>"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>
			<li><a target="_blank" href="https://pinterest.com/pin/create/bookmarklet/?media=<?php echo $featured_img_url; ?>&url=<?php echo get_permalink(); ?>&description=<?php echo get_the_title(); ?>"><i class="fa fa-pinterest-square" aria-hidden="true"></i></a></li>
			<li><a href="mailto:?subject=<?php echo get_the_title(); ?>&body=<?php echo get_permalink(); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></li>
		</ul>
	</div>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
