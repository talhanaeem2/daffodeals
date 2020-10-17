<?php
global $wpdb;
if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
}
$product_attributes = $wpdb->prefix . "product_attributes"; 
$tbl_product_variations = $wpdb->prefix . "product_variations"; 

$attributs = $wpdb->get_results("SELECT * FROM {$product_attributes} WHERE product_id=$post_id");
//print_r($attributs);
$post_status   = $post->post_status;
if(($post->post_status == 'publish' || $post->post_status == 'finalized') && current_user_can('seller')){ 
    $field_disabled = 'disabled="disabled"';
} else{
	$field_disabled = '';
}

$totalQtyAvailable = get_post_meta($post_id, '_totalQtyAvailable', true); 
?>

<?php if(empty($attributs)){ ?>
	<div class="deal-note-options">
		<div><i class="fa fa-file-o" aria-hidden="true"></i></div>
		<p><strong><?php esc_html_e( "You don't have any options & inventory yet", 'dokan-lite' ); ?>.</strong></p>
		<p><?php esc_html_e( "Click the button below to add your first option & inventory", 'dokan-lite' ); ?>.</p>
		<div class="add-option"><button type="button" title="<?php esc_html_e( "You don't have any options yet", 'dokan-lite' ); ?>" id="deal-add-option-btn" class="btn btn-add-option"><i class="fa fa-plus" aria-hidden="true"></i><?php esc_html_e( "ADD AN OPTION & INVENTORY", 'dokan-lite' ); ?></button>
		</div>
	</div>
<?php } ?>

<div class="options_details <?php if(empty($attributs)){ echo 'hide'; } ?>">
	<div class="option_left_part">
		<div class="option_left_boder">
			<ul class="add_option_list option-list">
				<?php if(!empty($attributs)){ ?>
					<?php foreach ($attributs as $key => $attribut) { ?>
						<li class="<?php if($key == 0){ echo 'activenow'; } ?>" data-o_leftid="<?php echo $attribut->ID; ?>"> <a href="javascript:void(0)"><?php echo $attribut->title; ?></a></li>
					<?php } ?>
				
				<?php }else{ ?>
					<li class="activenow" data-o_leftid="1"><i class="fa fa-arrows-v sortableli" aria-hidden="true"></i> <a href="javascript:void(0)">[New Option]</a></li>
				<?php } ?>			
			</ul>
			<?php if(empty($attributs) || !empty($attributs)){ ?>
				<div class="addmore_link">
					<a href="javascript:void(0)"><i class="fa fa-plus"></i><?php esc_html_e( "Add Option", 'dokan-lite' ); ?></a>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="option_right_part">
		<ul class="option-list">
			<?php if(!empty($attributs)){ ?>
					<?php foreach ($attributs as $key => $attribut) { ?>
						<li id="option-html" data-o_rightid="<?php echo $attribut->ID; ?>" class="deal-options <?php if($key != 0){ echo 'hide'; } ?>">
							<div class="dokan-form-group">
								<div class="label_ques">
									<label><?php esc_html_e( "Option Title", 'dokan-lite' ); ?></label>
									
								</div>
								<input type="text" class="form-control option-title" autocomplete="off" name="options[title][]" placeholder="Example: Size" <?php echo $field_disabled; ?> value="<?php echo $attribut->title; ?>">
								<span class="help-block"><?php esc_html_e( "Title to describe this option like Size, Color etc.", 'dokan-lite' ); ?>.</span>
							</div>

							<?php if($attribut->personalization == 'no'){ ?>  
								<div class="dokan-form-group variations-area">
									<div class="label_ques">
										<label><?php esc_html_e( "Option Values", 'dokan-lite' ); ?></label>
										
									</div>

									<div class="sort-option-values-main">
										<div class="sort-option-values">
											<?php 
											$variations = $attribut->variations;
											$variations_arr = explode(',', $variations);
											//$variations_skip_2 = array_slice($variations_arr,2);
											
											if(!empty($variations_arr)){ ?>
												<?php foreach ($variations_arr as $key => $variation) { ?>
													<div class="<?php if($post_status == 'beautify' || $post_status == 'finalized' || $post_status == 'editing' || $post_status == 'publish'){ echo 'values-list'; }?> option-values-list"><input type="hidden" class="variation-list-input" <?php //echo $field_disabled; ?> name="options[values][<?php echo sanitize_title($attribut->title); ?>][]" value="<?php echo $variation; ?>"><span><span><?php echo $variation; ?></span><?php if(current_user_can('administrator') || (current_user_can('seller') && $post_status == 'editing')){ ?><i class="fa fa-times del-option-val" aria-hidden="true"></i><?php } else{var_dump(current_user_can('seller'));} ?></span></div>
												<?php } ?>
											<?php } ?>	
										</div>
										<input type="text" <?php echo $field_disabled; ?> class="form-control input-options" placeholder="Example: Small 0-4">
									</div>

									<span class="help-block"><?php esc_html_e( "Enter option values (e.g. Small, Medium , Large) separated by a comma", 'dokan-lite' ); ?>.</span>  <!-- Double-click to edit -->
									<?php if(current_user_can('administrator') || current_user_can('seller')){ ?>
									<div class="clar_data">
										<a class="btn-remove-variation" href="javascript:void(0)"><i class="fa fa-trash-o"></i><?php esc_html_e( "Delete option values", 'dokan-lite' ); ?></a>
									</div>
									<?php } ?>
								</div>
							<?php } ?>

							<div class="dokan-form-group">
								<div class="label_ques">
									<div class="chek_box">
										<input type="hidden" <?php echo $field_disabled; ?> value="no" name="<?php if($attribut->personalization == 'no'){ echo 'options[personalization][]'; } ?>" class="form-control option-personalization">
										<input type="checkbox" <?php echo $field_disabled; ?> id="adition_info1" value="yes" name="options[personalization][]" <?php if($attribut->personalization == 'yes'){ echo 'checked'; } ?>  class="option-personalization">
										<label for="adition_info1"><?php esc_html_e( "This option is a Personalization", 'dokan-lite' ); ?>.</label>
									</div>
									
								</div>
								<?php
								if($attribut->personalization == 'yes'){
									if($attribut->char_allowed == '0'){
										$allowed_char = '30';
									} else{
										$allowed_char = $attribut->char_allowed;
									}
								}
								?>
								<input type="number" <?php echo $field_disabled; ?> class="form-control option-character" name="options[char_allowed][]" placeholder="" value="<?php echo $allowed_char; ?>">
								<span class="help-block"><?php esc_html_e( "Maximum characters allowed", 'dokan-lite' ); ?>.</span>
							</div>	
							
							<?php if(current_user_can('administrator') || current_user_can('seller')){ ?>
							<div class="dokan-form-group remove-option-main"><button type="button" class="btn btn-remove-option"><i class="fa fa-trash" aria-hidden="true"></i> Remove Option </button></div>
							<?php } ?>
						</li>
					<?php } ?>

				<?php }else{ ?>

					<li id="option-html" data-o_rightid="1" class="deal-options">
						<div class="dokan-form-group">
							<div class="label_ques">
								<label><?php esc_html_e( "Option Title", 'dokan-lite' ); ?></label>
								
							</div>
							<input <?php echo $field_disabled; ?> type="text" class="form-control option-title" autocomplete="off" name="options[title][]" placeholder="Example: Size">
							<span class="help-block"><?php esc_html_e( "Title to describe this option such as Size, Color or Style", 'dokan-lite' ); ?>.</span>
						</div>

						<div class="dokan-form-group variations-area">
							<div class="label_ques">
								<label><?php esc_html_e( "Option Values", 'dokan-lite' ); ?></label>	
							</div>

							<div class="sort-option-values-main">
								<div class="sort-option-values">
									
								</div>
								<input <?php echo $field_disabled; ?> type="text" class="form-control input-options" placeholder="Example: Small 0-4">
							</div>

							<span class="help-block"><?php esc_html_e( "Enter option values (e.g. Small, Medium , Large) separated by a comma", 'dokan-lite' ); ?>.</span>  <!-- Double-click to edit -->
							<div class="clar_data">
								<a class="btn-remove-variation" href="javascript:void(0)"><i class="fa fa-trash-o"></i><?php esc_html_e( "Delete option values", 'dokan-lite' ); ?></a>
							</div>
						</div>

						<div class="dokan-form-group">
							<div class="label_ques">
								<div class="chek_box">
									<input <?php echo $field_disabled; ?> type="hidden" value="no" name="options[personalization][]" class="form-control">
									<input type="checkbox" id="adition_info1" value="yes" name="options[personalization][]" class="option-personalization">
									<label for="adition_info1"><?php esc_html_e( "This option is a Personalization", 'dokan-lite' ); ?>.</label>
								</div>
								
							</div>
							<input type="number" class="form-control option-character" name="options[char_allowed][]" placeholder="">
							<span class="help-block"><?php esc_html_e( "Maximum characters allowed", 'dokan-lite' ); ?>.</span>
						</div>
						
						<div class="dokan-form-group remove-option-main"><button type="submit" class="btn btn-remove-option"><i class="fa fa-trash" aria-hidden="true"></i> Remove Option </button></div>
						
					</li>
			<?php } ?>
		</ul>
		
	</div>
</div>