<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

$pid = get_the_ID();

$date_data = get_deal_time_difference($pid); 
$dayleft = $date_data['dayleft'];

if ( $product->is_in_stock() && !empty($dayleft)) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<h1>
			Here working..
		</h1>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input( array(
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
		) );

		do_action( 'woocommerce_after_add_to_cart_quantity' );

		global $wpdb;
		$product_attributes = $wpdb->prefix . "product_attributes"; 
		$post_id = $product->get_id();
		$attributs = $wpdb->get_var("SELECT ID FROM {$product_attributes} WHERE product_id=$post_id");

		if (!empty($attributs)) {
			?>
			<button type="button" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
			<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt hide"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
			<?php 
		}else{
			?>
			<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
			<?php 
		}
		?>
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
<?php else: ?>
	<div class="clock"><i class="fa fa-clock-o" aria-hidden="true"></i></div>	
	
	<div class="btn-notify">
		<p><?php _e('You missed it! This deal expired', 'woocommerce');/*_e('You missed it! This deal expired, but we can notify you when it's back in stock', 'woocommerce');*/ ?>.</p>
		<button type="button" data-kcmodal="kcmodal" data-target="#login-popup" name="notify-me-button" class="button alt hide"><i class="fa fa-bell" aria-hidden="true"></i> <?php _e('Notify Me', 'woocommerce'); ?></button>
	</div>
<?php endif; 