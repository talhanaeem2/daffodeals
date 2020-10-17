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
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
global $wpdb, $product, $post;
//WC 3.4
$post_class = 'post_class';
if ( function_exists( 'WC' ) && WC()->version >= '3.4' ) {
	$post_class = 'wc_product_class';
}

$pid = get_the_ID();
$product_terms = wp_get_object_terms( $pid,  'product_cat' );
//print_r($product_terms);
$product_cat = !empty($product_terms)?$product_terms[0]->slug:'';
$sectionhide = ($product_cat == 'gift-card')?'style="display: none"':'style="display: block"';
?>

<div  class="single-breadcrumbs-wrapper">
	<div class="container">
		<?php basel_current_breadcrumbs( 'shop' ); ?>
		<?php if ( ! basel_get_opt( 'hide_products_nav' ) ): ?>
			<?php basel_products_nav(); ?>
		<?php endif ?>
	</div>
</div>

<div class="container">
	<?php
		/**
		 * Hook: woocommerce_before_single_product.
		 *
		 * @hooked wc_print_notices - 10
		 */
		 do_action( 'woocommerce_before_single_product' );

		 if ( post_password_required() ) {
		 	echo get_the_password_form();
		 	return;
		 }

		$product_images_class  	= basel_product_images_class();
		$product_summary_class 	= basel_product_summary_class();
		$single_product_class  	= basel_single_product_class();
		$content_class 			= basel_get_content_class();
		$product_design 		= basel_product_design();

		$container_summary = 'container';

		if( $product_design == 'sticky' ) {
			$container_summary = 'container-fluid';
		}

	?>
</div>

<?php if($post->post_status == 'upcoming'){ 
$up_deal = get_deal_upcoming_time_difference($pid);
$dayleft = $up_deal['dayleft'];
$enddate = $up_deal['enddate'];
?>
	<!-- <div class="seller-preview-bar">This is a Seller Preview. It will be available in 16:58:10<span></span> </div> -->
	<div class="seller_prview">
		<div class="prvirew_details">
			<p>This is a Seller Preview. It will be available in &nbsp;<ul id="upclockdiv" data-enddeal="<?php echo $dayleft; ?>" data-time="<?php echo $enddate; ?>"><li><span class="updays"><?php echo sprintf( _n( 'Today', '%s days', $dayleft, 'woocommerce' ), $dayleft ); ?></span></li><li><span class="uphours">23</span></li><li><span class="upminutes">55</span></li><li><span class="upseconds">25</span></li></ul></p>
		</div>
	</div>
<?php } ?>

<div id="product-<?php the_ID(); ?>" <?php $post_class( $single_product_class ); ?>>



	<div class="<?php echo esc_attr( $container_summary ); ?>">

		<div class="row">
			<div class="product-image-summary <?php echo esc_attr( $content_class ); ?>">
				<div class="row">
					<div class="<?php echo esc_attr( $product_images_class ); ?> product-images">
						<?php
							/**
							 * woocommerce_before_single_product_summary hook
							 *
							 * @hooked woocommerce_show_product_sale_flash - 10
							 * @hooked woocommerce_show_product_images - 20
							 */
							do_action( 'woocommerce_before_single_product_summary' );
							
							$sale_price = get_post_meta($pid, '_sale_price', true);
							$regular_price = get_post_meta($pid, '_regular_price', true);
							$price_save  = !empty($sale_price)?round((($regular_price-$sale_price) / $regular_price) * 100):0;
							
							$date_data = get_deal_time_difference($pid); 
							$dayleft = $date_data['dayleft'];

							$enddate = $date_data['enddate'];
							//print_r($date_data);
						?>
						
						<div class="product_time" <?php echo $sectionhide; ?>>
							<div class="product_range">
								<div class="save_time">
									<strong><?php echo $price_save; ?>%</strong>
									<p><?php _e('You Save', 'woocommerce'); ?></p>
								</div>
								<div id="deal-timer" data-enddeal="<?php echo $dayleft; ?>" data-time="<?php echo $enddate; ?>" class="save_time">
									<ul id="clockdiv">
										<li><span class="hours">23</span></li>
										<li><span class="minutes">55</span></li>
										<li><span class="seconds">25</span></li>
									</ul>
									<p><?php echo !empty($dayleft)? sprintf( _n( 'Today', '%s days', $dayleft, 'woocommerce' ), $dayleft ):__('TIME LEFT', 'woocommerce'); ?></p>
								</div>
								<div class="save_time">
									<?php if(empty($dayleft)){ ?>
										<span class="deal-sold-expired"><?php _e('Expired', 'woocommerce'); ?></span>
									<?php } /*else{ ?>
										<strong><?php echo kc_get_deal_orders($pid); ?></strong>
										<p><?php _e('AMT SOLD', 'woocommerce'); ?></p>
									<?php }*/ ?>
								</div>
							</div>
						</div>
					</div>
					<div class="<?php echo esc_attr( $product_summary_class ); ?> summary entry-summary">
						<div class="summary-inner <?php if( $product_design == 'compact' ) echo 'basel-scroll'; ?>">
							<div class="basel-scroll-content">
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
									 */
									do_action( 'woocommerce_single_product_summary' );
								
									
								?>


    
								<?php if ( $product_design != 'alt' && $product_design != 'sticky' && basel_get_opt( 'product_share' ) ): ?>
									<div class="product-share">
										<?php echo basel_shortcode_social( array( 'type' => basel_get_opt( 'product_share_type' ), 'size' => 'large', 'align' => 'left' ) ); ?>
									</div>
								<?php endif ?>

								<!-- ADD PRODUCT OPTIONS -->
								<?php 								
								$product_details = get_post_meta($pid, '_product_details', true) ;
								
								if (!empty($product_details[0])) {		

									?>
									<h2><?php _e('PRODUCT DETAILS', 'woocommerce'); ?></h2>
									<ul class="top_product_details">
										<?php foreach ($product_details as $key => $details) {
											if (!empty($details)) {
											?>
											<li><?php echo $details; ?></li>
											<?php 
											}
										} 
										?>
									</ul>
							
								<?php } ?>
								<a class="details_read_more" href="#product_details" <?php echo $sectionhide; ?>><?php _e('Read More'); ?></a>
							</div>
						</div>
					</div>
				</div><!-- .summary -->
			</div>

			<?php 
				/**
				 * woocommerce_sidebar hook
				 *
				 * @hooked woocommerce_get_sidebar - 10
				 */
				do_action( 'woocommerce_sidebar' );
			?>

		</div>
	</div>

	<?php if ( ( $product_design == 'alt' || $product_design == 'sticky' ) && basel_get_opt( 'product_share' ) ): ?>
		<div class="product-share">
			<?php echo basel_shortcode_social( array( 'type' => basel_get_opt( 'product_share_type' ), 'style' => 'colored' ) ); ?>
		</div>
	<?php endif ?>

	<div class="container">
		<?php
			/**
			 * basel_after_product_content hook
			 *
			 * @hooked basel_product_extra_content - 20
			 */
			do_action( 'basel_after_product_content' );
		?>
	</div>

	<?php if( $product_design != 'compact' ): ?>
		
		<div class="product-tabs-wrapper">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<?php
							/**
							 * woocommerce_after_single_product_summary hook
							 *
							 * @hooked woocommerce_output_product_data_tabs - 10
							 * @hooked woocommerce_upsell_display - 15
							 * @hooked woocommerce_output_related_products - 20
							 */
							do_action( 'woocommerce_after_single_product_summary' );
						?>
					</div>
				</div>	
			</div>
		</div>

	<?php endif ?>

</div><!-- #product-<?php the_ID(); ?> -->

<!-- RELATED PRODUCT -->
<?php
do_action( 'kc_related_product_content' );
?>

<!-- PRODUCT DETAILS-->
<div class="single_product_details" id="product_details" <?php echo $sectionhide; ?>>
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<div class="product-description">
					<h2><?php _e('PRODUCT DESCRIPTION', 'woocommerce'); ?></h2>
					<div class="description">
						<?php 					
						$product_description = get_post_meta($pid, '_product_description', true);
						if (!empty($product_description)) 
						{
							 echo wpautop($product_description);									
						} 
						?>
					</div>
				</div>
				<div class="product-details">
					<div class="details">	
						<?php 
						$product_details = get_post_meta($pid, '_product_details', true);
						if (!empty($product_details[0])) {									
							?>
							<h2><?php _e('PRODUCT DETAILS', 'woocommerce'); ?></h2>
							<ul>
								<?php foreach ($product_details as $key => $details) {
									if (!empty($details)) {
									?>
										<li><?php echo $details; ?></li>
									<?php 
									}
								} 
								?>
							</ul>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="product-seller">
					<h2><?php _e('SELLER', 'woocommerce'); ?></h2>
					<div class="seller">
						<?php  
						$author_id  = get_post_field( 'post_author', $pid);
						if (!empty($author_id)) {
							$store_info = dokan_get_store_info( $author_id );
							$store_settings = get_seller_profile_info( $author_id );
							$gravatar_id = ! empty( $store_settings['gravatar_id'] ) ? $store_settings['gravatar_id'] : 0;
							$banner_url = $gravatar_id ? wp_get_attachment_url( $gravatar_id ) : '';
							?>
							<a href="<?php echo esc_url(dokan_get_store_url($author_id)); ?>">
								<?php if(!empty($banner_url)){ ?>
									<div class="single-seller-left">
										 <img class="seller-profile-image" src="<?php echo esc_url( $banner_url ); ?>">
									</div>
								<?php } ?>
								<div class="single-seller-right">
									<p><?php if(!empty($store_info['store_name'])){ echo esc_html( $store_info['store_name'] ); } ?></p>
									<p>
										<?php echo seller_get_readable_rating($author_id); ?>
									</p>
								</div>
							</a>
							<?php 
						}
						?>
						
					</div>
				</div>
				<div class="product-shipping">
					<?php 
					$shipping_price = get_post_meta($pid, '_shipping_price', true);
					$shipping_additional_price = get_post_meta($pid, '_shippingPriceAdditionalItems', true);
					$ships_date = get_post_meta($pid, '_ships_date', true);
					if (!empty($shipping_price)) {									
						?>
						<h2><?php _e('SHIPPING', 'woocommerce'); ?></h2>
						<div class="shipping">
							<?php echo wc_price($shipping_price); ?> <?php _e('for the first item', 'woocommerce'); ?> 
							<?php if(!empty($shipping_additional_price)){ _e(' and ', 'woocommerce'); echo wc_price($shipping_additional_price);  _e(' for each additional item', 'woocommerce'); } ?>. US only. <?php echo ($ships_date)?'Ships no later than '.date('D d M', strtotime($ships_date)).'.':''; ?>
						</div>
					<?php } ?>
				</div>
				<div class="product-fine-print">
					<?php 
					$fine_print = get_post_meta($pid, '_fine_print', true);
					if (!empty($fine_print)) {									
						?>
						<h2><?php _e('FINE PRINT', 'woocommerce'); ?></h2>
						<div class="fine-print">
							<?php echo $fine_print; ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>	
	</div>
</div>

<!-- UPSELL PRODUCTS -->
<?php
do_action( 'kc_upsell_product_content' );
?>


<!-- RATING AND REVIEW SECTION -->
<div class="rating_review_details" <?php echo $sectionhide; ?>>
<div class="container">
	<div class="row">
		<div class="col-md-4">
			<h2><?php _e('RATINGS & REVIEWS', 'woocommerce'); ?></h2>
			<div class="deal-ratings">
				<div class="rating_image">
					<?php 
					if ( has_post_thumbnail() ) {
						the_post_thumbnail('shop_thumbnail');
					}
					?>
					<div class="rating_star">
						<?php 
							echo get_the_title($pid);

							$rating_count = $product->get_rating_count();
							$review_count = $product->get_review_count();
							$average      = $product->get_average_rating();
							?>
							<div class="woocommerce-product-rating">
								<?php echo wc_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
							</div>
							<?php if ( comments_open() ) : ?>
								<?php echo number_format($average,1); ?>
								(<?php printf( _n( '%s review', '%s reviews', $review_count, 'woocommerce' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)
								<?php // phpcs:enable ?>
							<?php endif
						?>
					</div>
				</div>
				<div class="left-rating-filter">
					<?php $rationgs = get_post_group_comment($pid); ?>
					<input type="hidden" id="deal_comment_post_id" value="<?php echo $pid; ?>">
					<ul class="rating-filter-list">
						<?php 
						if($rationgs){
							foreach ($rationgs as $key => $rate) {
								$width = !empty($review_count)?round(($rate/$review_count ) * 100):0;
								?>
								<li class="rating-filter-li" data-val="<?php echo $key; ?>">
									<span class="rating_bar"><?php echo $key; ?> Star</span>
									<div class="progress">
										<div class="progress-bar" style="width:<?php echo $width; ?>%" ></div>
									</div>
									<span class="count_bar"><?php echo !empty($rate)?$rate:''; ?></span>
								</li>
								<?php 
							}
						} 
					
						?>	
					</ul>				
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="filter-right">
				<label for="rating-filter"><?php _e('Filter by', 'woocommerce'); ?>:</label>
				<select name="drop-down" id="rating-filter" placeholder="Filter by:" class="select rating-filter">
					<option value="Any"><?php _e('All Reviews', 'woocommerce'); ?></option>
					<option value="5">5 Star</option>
					<option value="4">4 Star</option>
					<option value="3">3 Star</option>
					<option value="2">2 Star</option>
					<option value="1">1 Star</option>
				</select>
			</div>
			<div id="comment-list" class="comment-list single-product-content">
				<?php
				do_action( 'kc_reviews_content' );
				?>

				<div class="review-loader hide">
					<div class="boxloader"><div class="loadernew"></div></div>
				</div>

				<?php 
				$seller_reviews = seller_count_review_ratings($author_id);
				if (!empty($seller_reviews)) {
					$store_url = dokan_get_store_url($author_id);
					echo '<div class="seller-all-reviews"><a class="" href="'.$store_url.'">';
					$reviewcount = (int)$seller_reviews->count;
					
					echo sprintf( __( 'See all '._n( ' %d review ', ' %d reviews ','dokan-lite', $reviewcount).' for this Seller', 'dokan-lite' ), $reviewcount);
					echo '</a></div>';
				}
				?>
			</div>			
		</div>
	</div>
</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>