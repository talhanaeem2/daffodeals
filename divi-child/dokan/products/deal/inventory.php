<?php
global $wpdb;
if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
}
$product_attributes = $wpdb->prefix . "product_attributes"; 
$tbl_product_variations = $wpdb->prefix . "product_variations"; 

$totalQtyAvailable = get_post_meta($post_id , '_totalQtyAvailable', true);

$combine_attributs = $wpdb->get_results("SELECT * FROM {$product_attributes} WHERE product_id=$post_id AND personalization = 'no' AND combine_status = 'yes'");

$combine_title = array();
$combine_id = 0;
$qty_limit = '';
$combine_bulk_options = array();
//var_dump('test');
if(!empty($combine_attributs)){
	foreach ($combine_attributs as $key => $attribut) { 
		$combine_title[$attribut->ID] =  $attribut->title; 
		$combine_id =  $attribut->ID; 
		$qty_limit = $attribut->quantity_status;
		$combine_bulk_options[$attribut->title] = explode(',', $attribut->variations);
	} 
}
//print_r($combine_attributs);
$attributs = $wpdb->get_results("SELECT * FROM {$product_attributes} WHERE product_id=$post_id AND personalization = 'no' AND combine_status = 'no' ");
//print_r($attributs);

$post_status   = $post->post_status;
if($post->post_status == 'publish' && current_user_can('seller')){ 
    $field_disabled = 'disabled="disabled"';
} else{
	$field_disabled = '';
}
?>

<?php if(empty($attributs) && empty($combine_attributs)){ ?>
	<div class="deal-note-options">
		<p><strong><?php esc_html_e( "You don't have any options yet. Please add option first.", 'dokan-lite' ); ?>.</strong></p>
	</div>
<?php } ?>

<div class="options_details iv_options_details">
	<div class="option_left_part">
		<div class="option_left_boder">
			<ul class="iv_option_left_list inventory-option-list">				
				<?php if(!empty($attributs) || !empty($combine_attributs)){ ?>
					
					<?php if (!empty($combine_title)) { ?>
						<li class="<?php echo 'activenow';  ?>" data-iv_leftid="<?php echo $combine_id; ?>"> 	<a href="javascript:void(0)"><?php echo implode(' / ', $combine_title); ?></a></li>
					<?php } ?>				

					<?php foreach ($attributs as $key => $attribut) { ?>
						<li class="<?php if($key == 0 && empty($combine_title)){ echo 'activenow'; } ?>" data-iv_leftid="<?php echo $attribut->ID; ?>"> <a href="javascript:void(0)"><?php echo $attribut->title; ?></a></li>
					<?php } ?>
				<?php }else{ ?>
					<li class="activenow hide" data-iv_leftid="1"><a href="javascript:void(0)">[New Option]</a></li>
				<?php }  ?>							
			</ul>
		</div>
	</div>

	<div class="option_right_part">
		<ul class="iv_option_right_list inventory-option-list">
			<?php if(!empty($attributs) || !empty($combine_attributs)){ ?>

				<!-- ----- COMBINE ATTRIBUTES ----------->
			
				<?php  //var_dump($combine_title);
				if(!empty($combine_id)) { ?>	
					<li id="option-html" data-iv_rightid="<?php echo $combine_id; ?>" class="deal-options">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<?php foreach ($combine_title as $aid => $title) { ?>
											<th class="invat_thstart inv-option-title">
												<div class="inv-variation-titles">
													<span><?php echo $title; ?></span>	
													 <input type="hidden" class="combinations hide" <?php echo $field_disabled; ?> name="combinations[]" value="<?php echo $title; ?>">
													<a href="javascript:void(0)" data-li_id="<?php echo $aid; ?>" class="btn btn-option-exclude"><i class="fa fa-chain-broken" aria-hidden="true"></i> Exclude</a>
												</div>
												<input type="hidden" class="inv-combine-input hide" <?php echo $field_disabled; ?> name="options[combine][<?php echo sanitize_title($title); ?>]" value="yes">
											</th>
										<?php } ?>
										<th>
											<?php esc_html_e( "CUSTOM SKU (OPTIONAL)", 'dokan-lite' ); ?>
										</th>
										<th class="invat_thend">
											<span><?php esc_html_e( "QTY", 'dokan-lite' ); ?></span>
<!-- 											<a href="javascript:void(0)" class="btn btn-inve-unlimited hide"><?php esc_html_e($qty_limit, 'dokan-lite' ); ?></a> -->
											<input type="hidden" <?php //echo $field_disabled; ?> class="inv-limited-input hide" name="options[limited][<?php echo sanitize_title(implode('-', $combine_title)); ?>]" value="<?php echo $qty_limit; ?>">
										</th>
									</tr>								
								</thead>						
								<tbody>	
									<?php 
									$attr_id = $combine_id;								
									$product_variations = $wpdb->get_results("SELECT * FROM {$tbl_product_variations} WHERE attr_id=$attr_id");
										 
									//var_dump($attr_id);
									$qty = 0;
									if(!empty($product_variations)){ ?>
										<?php foreach ($product_variations as $key => $variation) { ?>
											<tr>
												<?php 
												$tds = '';
												$vtitel_arr = array();
												$vtitles = explode(',', $variation->title); 
												foreach ($vtitles as $key => $vtitel) {
													$tds .= '<td></td>';
													$vtitel_arr[] = $vtitel;
												?>
													<td><?php echo $vtitel; ?></td>
												<?php } ?>		

												<input type="hidden" <?php echo $field_disabled; ?> class="variation-title hide" value="<?php echo implode(',', $vtitel_arr); ?>" name="options[variation_title][<?php echo sanitize_title(implode('-', $combine_title)); ?>][]">

												<td>
													<input type="text" <?php echo $field_disabled; ?> value="<?php echo $variation->sku;?>" class="option-sku" name="options[sku][<?php echo sanitize_title(implode('-', $combine_title)); ?>][]">
													
													
												</td>
												<td>
													<input class="option-qty" <?php echo $field_disabled; ?> value="<?php echo $variation->qty;?>" type="number" name="options[qty][<?php echo sanitize_title(implode('-', $combine_title)); ?>][]" min="0">

													<?php 
													$qty += $variation->qty;
													?>
												</td>
											</tr>
										<?php } ?>
									<?php } else{
										//echo 'Product variation not found';
									} ?>
								</tbody>
								<tfoot>
									<tr>
										<?php echo $tds; ?><td class="invent_total">Total:</td>
										<td>
											<span class="total-quantity"><?php echo $qty; ?><span> / <?php echo $totalQtyAvailable; ?></span></span>
<!-- 											<span class="inventory-unlimited btn-inve-unlimited hide">∞<span></span></span> -->
										</td>
									</tr>
								</tfoot>	
							</table>	
						</div>				
					</li>
				<?php } ?>

				<?php foreach ($attributs as $key => $attribut) { $qty_limit = $attribut->quantity_status; ?>					
					<li id="option-html" data-iv_rightid="<?php echo $attribut->ID; ?>" class="deal-options <?php if(!empty($combine_id)){ echo 'hide'; }elseif($key != 0){ echo 'hide'; } ?>">
						<div class="table-responsive">
							<table class="table ">
								<thead>
									<tr>
										<th class="invat_thstart inv-option-title">
											<div class="inv-variation-titles">
												<span><?php echo $attribut->title; ?></span>
											</div>
											<input type="hidden" <?php echo $field_disabled; ?> class="inv-combine-input hide" name="options[combine][<?php echo sanitize_title($attribut->title); ?>]" value="no">
										<input type="hidden" class="inv-combine-input hide" name="combination[]" value="no">
										</th>
										<th>
											<?php esc_html_e( "CUSTOM SKU (OPTIONAL)", 'dokan-lite' ); ?>
										</th>
										<th class="invat_thend">
											<span><?php esc_html_e( "QTY", 'dokan-lite' ); ?></span>
<!-- 											<a href="javascript:void(0)" class="btn btn-inve-unlimited hide"><?php esc_html_e( "unlimited", 'dokan-lite' ); ?></a> -->
											<input type="hidden" <?php //echo $field_disabled; ?> class="inv-limited-input hide" name="options[limited][<?php echo sanitize_title($attribut->title); ?>]" value="unlimited">
										</th>
									</tr>								
								</thead>						
								<tbody>	
									<?php 
									$attr_id = $attribut->ID;								
									$product_variations = $wpdb->get_results("SELECT * FROM {$tbl_product_variations} WHERE attr_id=$attr_id");
									//print_r($product_variations);
									$qty = 0;
									if(!empty($product_variations)){ ?>
										<?php foreach ($product_variations as $key => $variation) { ?>
											<tr>
												<td><?php echo $variation->title; ?><input type="hidden" class="variation-title hide" value="<?php echo $variation->title; ?>" name="options[variation_title][<?php echo sanitize_title($attribut->title); ?>][]"></td>
												<td>
													<input type="text" <?php echo $field_disabled; ?> class="option-sku" value="<?php echo $variation->sku;?>" name="options[sku][<?php echo sanitize_title($attribut->title); ?>][]">
												</td>
												<td>
													<input class="option-qty" <?php echo $field_disabled; ?> type="number" value="<?php echo $variation->qty;?>" name="options[qty][<?php echo sanitize_title($attribut->title); ?>][]" min="0" value="0">
												</td>
											</tr>
										<?php $qty += $variation->qty;
											} ?>
									<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td class="invent_total">Total:</td>
										<td>
											<span class="total-quantity">
												<?php echo $qty; ?>
												<span> / <?php echo $totalQtyAvailable; ?></span>
											</span>
											<span class="inventory-unlimited hide">∞<span></span></span>
										</td>
									</tr>
								</tfoot>	
							</table>	
						</div>				
					</li>
				<?php } ?>

			<?php } else{ ?>
				<li class="deal-inventory" data-iv_rightid="1">
					<div class="table-responsive">
						<table class="table" id="tbl-inv-list">
							<thead>
								<tr>
									<th class="invat_thstart inv-option-title">
										<div class="inv-variation-titles">
											<span></span>
											<a href="javascript:void(0)" class="btn btn-option-combine"><i class="fa fa-link" aria-hidden="true"></i> <?php esc_html_e( "Combine", 'dokan-lite' ); ?></a>
										</div>
										<input type="hidden" class="inv-combine-input hide" name="combination[]" value="no">
									</th>
									<th>
										<?php esc_html_e( "CUSTOM SKU (OPTIONAL)", 'dokan-lite' ); ?>
									</th>
									<th class="invat_thend">
										<span><?php esc_html_e( "QTY", 'dokan-lite' ); ?></span>
										<input type="hidden" class="inv-limited-input hide" name="limited[]" value="unlimited">
									</th>
								</tr>								
							</thead>						
							<tbody>							
							</tbody>
							<tfoot>
								<tr>
									<?php $totalQtyAvailable = get_post_meta($post_id , '_totalQtyAvailable', true); ?>
									<td></td><td class="invent_total">Total:</td><td><span class="total-quantity">0<span> / <?php echo $totalQtyAvailable; ?></span></span><span class="inventory-unlimited hide">∞</span></td>
								</tr>
							</tfoot>	
						</table>
					</div>
				</li>
			<?php } ?>	
		</ul>
		
	</div>
</div>
