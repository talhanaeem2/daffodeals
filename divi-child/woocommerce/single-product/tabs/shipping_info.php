<?php
$product_id = get_the_ID();
$ships_date = get_post_meta( $product_id, '_ships_date', true);
$shipping_price = get_post_meta( $product_id, '_shipping_price', true);
$shippingPriceAdditionalItems = get_post_meta( $product_id, '_shippingPriceAdditionalItems', true);
//var_dump($product_id);
?>
<ul class="list-unstyled">
    <li><span><strong>Ship by Date:</strong>  </span> <span class="details"> <?php echo date('M jS', strtotime($ships_date)); ?></span></li>
    <li><span><strong>Shipping Cost:</strong>  </span> 
    	<?php if(!empty($shipping_price)){ ?> 
    		<span class="details"> <?php echo !empty($shipping_price)?wc_price($shipping_price).' (1st item)':''; ?></span>
    	<?php }elseif(!empty($shippingPriceAdditionalItems)){ ?>  
    		<span class="details"> <?php echo !empty($shippingPriceAdditionalItems)?wc_price($shippingPriceAdditionalItems).' (Additional items)':''; ?></span>
    	<?php } ?>    	 
    </li>     
    <li>
    	<?php if(!empty($shipping_price) && !empty($shippingPriceAdditionalItems)){ ?>
    	<span><strong>&nbsp;</strong>  </span>  <span class="details"> <?php echo !empty($shippingPriceAdditionalItems)?wc_price($shippingPriceAdditionalItems).' (Additional items)':''; ?></span>
    	<?php } ?>  
    </li>
    
</ul>