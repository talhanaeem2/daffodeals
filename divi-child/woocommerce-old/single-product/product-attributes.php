<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-attributes.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! $product_attributes ) {
	return;
}
//print_r($product_attributes);
unset($product_attributes['weight']);
$pid = get_the_ID();
$shipping_price = get_post_meta($pid, '_shipping_price', true);
$shipping_additional_price = get_post_meta($pid, '_shippingPriceAdditionalItems', true);
$ships_date = get_post_meta($pid, '_ships_date', true);
$startDate = get_post_meta($pid, '_startDate', true);
$enddate = date('Y-m-d', strtotime($startDate.' +3 day'));
if (!empty($ships_date) && !empty($enddate)) {
   $datetime1 = new DateTime($enddate);
   $datetime2 = new DateTime($ships_date);
   $difference = $datetime1->diff($datetime2);
    $dayleft = $difference->d;
}else{
    $dayleft = 0;
}
?>
<table class="woocommerce-product-attributes shop_attributes">
	<?php foreach ( $product_attributes as $product_attribute_key => $product_attribute ) : ?>
		<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr( $product_attribute_key ); ?>">
			<th class="woocommerce-product-attributes-item__label"><?php echo wp_kses_post( $product_attribute['label'] ); ?></th>
			<td class="woocommerce-product-attributes-item__value"><?php echo wp_kses_post( $product_attribute['value'] ); ?></td>
		</tr>
	<?php endforeach; ?>
	
	<tr class="woocommerce-product-attributes-item">
		<th class="woocommerce-product-attributes-item__label"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="ji-box"><path d="M509.5 184.6L458.9 32.8C452.4 13.2 434.1 0 413.4 0H272v192h238.7c-.4-2.5-.4-5-1.2-7.4zM240 0H98.6c-20.7 0-39 13.2-45.5 32.8L2.5 184.6c-.8 2.4-.8 4.9-1.2 7.4H240V0zM0 224v240c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V224H0z"></path></svg></th>
		<td class="woocommerce-product-attributes-item__value">
			<?php 
			if (!empty($shipping_price)) {				
				echo wc_price($shipping_price); ?> shipping 
				<?php if (!empty($shipping_additional_price)) {
					?>
					<br> <?php echo wc_price($shipping_additional_price); ?> for each additional item
					<?php
				} 
			}else{
				?>
				<span class="freeshipping">Free Shipping</span>
				<?php 
			}
			?>
		</td>
	</tr>
	<?php if(!empty($dayleft)){ ?>
	<tr class="woocommerce-product-attributes-item">
		<th class="woocommerce-product-attributes-item__label"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" id="ji-truck"><path d="M624 352h-16V243.9c0-12.7-5.1-24.9-14.1-33.9L494 110.1c-9-9-21.2-14.1-33.9-14.1H416V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h16c0 53 43 96 96 96s96-43 96-96h128c0 53 43 96 96 96s96-43 96-96h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM160 464c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm80-208H416V144h44.1l99.9 99.9V256z"></path></svg></th>
		<td class="woocommerce-product-attributes-item__value">
			Seller usually ships within <?php echo ($dayleft>1)?$dayleft.' days':$dayleft.' day'; ?>.
		</td>
	</tr>
	<?php } ?>
</table>
