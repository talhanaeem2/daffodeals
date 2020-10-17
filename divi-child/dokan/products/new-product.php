<?php

$get_data  = wp_unslash( $_GET );
$post_data = wp_unslash( $_POST );

/**
*  dokan_new_product_wrap_before hook
*
*  @since 2.4
*/
do_action( 'dokan_new_product_wrap_before' );

//REBOOK DEAL DATA
$title = esc_attr(dokan_posted_input( 'post_title' ));
$term = esc_attr(dokan_posted_input( 'product_cat' ));
$product_options = esc_attr( dokan_posted_textarea( 'product_options' ) );
$isHandMade = esc_attr( dokan_posted_input( 'isHandMade' ) );
$isPersonalized = esc_attr( dokan_posted_input( 'isPersonalized' ) );
$startDate = esc_attr( dokan_posted_input( 'startDate' ) );
$ship_by = esc_attr( dokan_posted_input( 'ship_by' ) );
$sale_price = esc_attr( dokan_posted_input( '_sale_price' ) );
$regular_price = esc_attr( dokan_posted_input( '_regular_price' ) );
$shipping_price = esc_attr( dokan_posted_input( '_shipping_price' ) );
$shippingPriceAdditionalItems = esc_attr(dokan_posted_input('shippingPriceAdditionalItems'));
$personalized_handmade = esc_attr(dokan_posted_input('personalized_handmade'));
$requireReturnWhenWrongItemSent = esc_attr(dokan_posted_input('requireReturnWhenWrongItemSent'));
$requireReturnWhenStyleNotAsExpected = esc_attr(dokan_posted_input('requireReturnWhenStyleNotAsExpected'));
$requireReturnWhenBadItem = esc_attr(dokan_posted_input('requireReturnWhenBadItem'));
$requireReturnWhenItemNotAsDescribed = esc_attr(dokan_posted_input('requireReturnWhenItemNotAsDescribed'));
$shipping_weight_lbs = esc_attr(dokan_posted_input('shipping_weight_lbs'));
$shipping_weight_oz = esc_attr(dokan_posted_input('shipping_weight_oz'));
$totalQtyAvailable = esc_attr(dokan_posted_input('totalQtyAvailable'));
$post_content = esc_attr(dokan_posted_textarea('post_content'));
$additional_information = esc_attr(dokan_posted_textarea('additional_information'));
$posted_img       = esc_attr(dokan_posted_input( 'feat_image_id' ));
$posted_img_url   = $hide_instruction = '';
$hide_img_wrap    = 'dokan-hide';
$product_images =  isset($post_data['product_image_gallery'])?$post_data['product_image_gallery']:'';

if (isset($_GET['rebook']) && !empty($_GET['rebook'])) 
{
    $post_id =  base64_decode($_GET['rebook']);
    $post = get_post($post_id);
    $title = $post->post_title;   
    $post_content = $post->post_content;  
    $term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') ); 
    $term = !empty($term)?$term[0]:0;
    $post_metas = get_post_meta($post_id);    
    $product_options = isset($post_metas['_product_options'])?$post_metas['_product_options'][0]:'';
    $isHandMade = isset($post_metas['_isHandMade'])?$post_metas['_isHandMade'][0]:'';
    $isPersonalized = isset($post_metas['_isPersonalized'])?$post_metas['_isPersonalized'][0]:'';
    $startDate = isset($post_metas['_startDate'])?$post_metas['_startDate'][0]:'';
    $ship_by = isset($post_metas['_ships_date'])?$post_metas['_ships_date'][0]:'';
    $sale_price = isset($post_metas['_sale_price'])?$post_metas['_sale_price'][0]:'';
    $regular_price = isset($post_metas['_regular_price'])?$post_metas['_regular_price'][0]:'';
    $shipping_price = isset($post_metas['_shipping_price'])?$post_metas['_shipping_price'][0]:'';
    $shippingPriceAdditionalItems = isset($post_metas['_shippingPriceAdditionalItems'])?$post_metas['_shippingPriceAdditionalItems'][0]:'';   
    $personalized_handmade = isset($post_metas['_personalized_handmade'])?$post_metas['_personalized_handmade'][0]:''; 
    $shipping_weight_lbs = isset($post_metas['_shipping_weight_lbs'])?$post_metas['_shipping_weight_lbs'][0]:'';  
    $shipping_weight_oz = isset($post_metas['_shipping_weight_oz'])?$post_metas['_shipping_weight_oz'][0]:'';  
    $totalQtyAvailable = isset($post_metas['_totalQtyAvailable'])?$post_metas['_totalQtyAvailable'][0]:''; 
    $additional_information = isset($post_metas['_additional_information'])?$post_metas['_additional_information'][0]:''; 
    if ( has_post_thumbnail( $post_id ) ) {
        $feat_image_id     = get_post_thumbnail_id( $post_id );
        $posted_img     = $feat_image_id;        
    }
    $product_images = isset($post_metas['_product_image_gallery'])?$post_metas['_product_image_gallery'][0]:'';

    if($post->post_status == 'publish' || $post->post_status == 'finalized' && current_user_can('seller')){ 
    	$field_disabled = 'disabled="disabled"';
	}
    
}
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

    <div class="dokan-dashboard-wrap" id="dashboard_main1">
        <?php

            /**
             *  dokan_dashboard_content_before hook
             *  dokan_before_new_product_content_area hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_before' );
            do_action( 'dokan_before_new_product_content_area' );
        ?>        

        <section class="deal-section">
          <div class="container">
            <?php  //SUB MENU CALL HERE.
            dokan_get_template_part( 'products/prduct-sub-menu' ); ?>
            <div class="deal-detail">
                <?php if ( Dokan_Template_Products::$errors ) { ?>
                    <div class="dokan-alert dokan-alert-danger">
                        <a class="dokan-close" data-dismiss="alert">&times;</a>

                        <?php foreach ( Dokan_Template_Products::$errors as $error) { ?>

                            <strong><?php esc_html_e( 'Error!', 'dokan-lite' ); ?></strong> <?php echo esc_html( $error ); ?>.<br>

                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ( isset( $get_data['created_product'] ) ): ?>
                    <div class="dokan-alert dokan-alert-success">
                        <a class="dokan-close" data-dismiss="alert">&times;</a>
                        <strong><?php esc_html_e( 'Success!', 'dokan-lite' ); ?></strong>
                        <?php printf( __( 'You have successfully created <a href="%s"><strong>%s</strong></a> product', 'dokan-lite' ), esc_url( dokan_edit_product_url( intval( $get_data['created_product'] ) ) ), get_the_title( intval( $get_data['created_product'] ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php endif ?>
                <?php

                $can_sell = apply_filters( 'dokan_can_post', true );

               // if ( $can_sell ) {
                    
                    if ( !empty( $posted_img ) ) {
                        $posted_img     = empty( $posted_img ) ? 0 : $posted_img;
                        $posted_img_url = wp_get_attachment_url( $posted_img );
                        $hide_instruction = 'dokan-hide';
                        $hide_img_wrap = '';
                    }
                    //if ( dokan_is_seller_enabled( get_current_user_id() ) ) {  ?>
              <form class="dokan-form-container" id="submit_from" method="post">
                 <div class="personal-info">
                    <h3>Product Information</h3>
					 <?php 
					  	$vendor_id = get_post_field( 'post_author', get_the_id() );
						$vendor = new WP_User($vendor_id);
						$store_info = dokan_get_store_info( get_current_user_id() );
					  ?>
                     <ul>
						 <li>
                          <label>Vendor Name:</label> 
                          <label class="vendor-namee"><?php echo $store_info['store_name']; ?></label>
                        </li>
                        <li>
                          <label>Deal Title:</label> 
                          <div class="input-text">
                            <input class="form-control req_field" autocomplete="off" name="post_title" id="post-title" type="text" maxlength="30" value="<?php echo $title; ?>">
                          </div>
                        </li>
                        <li>
                          <div class="row">
                            <div class="col-lg-6 col-md-12">
                              <label>Product Category:</label> 
                              <div class="input-text">
                                <?php
                                $selected_cat  = $term;
                                $category_args =  array(
                                    'show_option_none' => __( '- Select a category -', 'dokan-lite' ),
                                    'hierarchical'     => 1,
                                    'hide_empty'       => 0,
                                    'name'             => 'product_cat',
                                    'id'               => 'product_cat',
                                    'taxonomy'         => 'product_cat',
                                    'title_li'         => '',
                                    'class'            => 'product_cat form-control dokan-select2',
                                    'exclude'          => array('15','68','50','51','52','54','55','58','59','60','61','62','63'),
                                    'selected'         => $selected_cat,
                                );
                                //'exclude'          => array('15','68','50','51','52','54','55','58','59','60','61','62','63'),
                                wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
                                ?>                           
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                              <label>Personalized/Handmade:</label>
                              <div class="input-text">
                                <select name="personalized_handmade" id="personalized_handmade" class="form-control">
                                  <option <?php echo ($personalized_handmade == 'no')?'selected':''; ?> value="no">No</option>
                                  <option <?php echo ($personalized_handmade == 'yes')?'selected':''; ?> value="yes">Yes</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </li>
                        <li><label>Product Description:</label> 
                          <div class="input-text">
                            <textarea name="post_content" id="post_content" rows="5" class="form-control"><?php echo $post_content; ?></textarea>
                          </div>
                        </li>
                        <li class="date">
                          <div class="row">
                              <div class="col-lg-6 col-md-12">
                                <label>Deal Start Date:</label> 
                                  <div class="input-text">
                                    <input type="text" autocomplete="off" class="form-control  req_field" id="preferred-start-date" name="startDate" value="<?php echo $startDate; ?>">
                                    <div class="date-icon">
                                      <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/date-icon.png" alt="icon">
                                    </div>
                                  </div>
                              </div>
                              <div class="col-lg-6 col-md-12">
                                <label>Ship By Date:</label> 
                                  <div class="input-text">
									  <?php if($post_status = 'pending' || $post_status = 'finalized' || $post_status = 'publish' && current_user_can('administrator')){
											$disabled = 'disabled="disabled"';
										} ?>
                                    <input type="text" autocomplete="off" class="form-control " id="preferred-ship-date" name="ship_by" value="<?php echo $ship_by; ?>" <?php echo $disabled; ?>>
                                    <div class="date-icon">
                                      <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/date-icon.png" alt="icon">
                                    </div>
                                  </div>
                              </div>
                           </div>
                           <label class="hide-mob"></label>
                           <div class="input-text">
                             <p>* Deal must be finalized 5 days prior to the deal start date.</p>
                           </div>
                        </li>
                        <li class="date price">
                          <div class="row">
                              <div class="col-lg-6 col-md-12">
                                <label>Deal Price:</label> 
                                  <div class="input-text">
                                    <input type="number" class="form-control dokan-product-sales-price req_field" id="sale_price" name="_sale_price" placeholder="" value="<?php echo $sale_price;  ?>" min="0" step="any">
                                  </div>
                              </div>
                              <div class="col-lg-6 col-md-12">
                                <label>MSRP <span>(Retail Price):</span></label> 
                                  <div class="input-text">
                                    <input type="number" class="form-control dokan-product-regular-price req_field" id="retail_price" name="_regular_price" placeholder="" value="<?php echo $regular_price; ?>" min="0" step="any">
                                  </div>
                              </div>
                           </div>
                          </li>
                          <li class="date">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                  <label>Shipping:</label> 
                                  <div class="input-text">
                                    <div class="row">
                                      <div class="col-lg-4 col-md-12">
                                        <input type="number" class="form-control dokan-product-shipping-price" id="shipping_price" name="_shipping_price" placeholder="" min="0" id="shipping-price" value="<?php echo $shipping_price; ?>" step="any">
                                        <p>1st Item </p>
                                      </div>
                                       <div class="col-lg-4 col-md-12">
                                        <input type="number" class="form-control dokan-product-shipping-price" name="shippingPriceAdditionalItems" placeholder=""  value="<?php echo $shippingPriceAdditionalItems;  ?>" min="0" step="any">
                                         <p>Additional item</p>                                        
                                       </div>
                                    </div>
                                    
                                  </div>  
                                </div>
                             </div>                      
                        </li>
                        <li>
                          <label>Total Units Available: </label> 
                          <div class="input-text">
                            <input type="number" class="form-control dokan-product-shipping-price req_field" name="totalQtyAvailable" placeholder="" id="total_qty" value="<?php echo $totalQtyAvailable; ?>" min="1" step="any">
                          </div>
                        </li>
                        <li class="upload-img">
                          <label>Images:</label> 
                          <div class="input-text">
                             
                                <div class="featured-image">
                                    <div class="dokan-feat-image-upload">
                                        <div class="instruction-inside <?php echo esc_attr( $hide_instruction ); ?>">
                                            <input type="hidden" name="feat_image_id" class="dokan-feat-image-id req_field" value="<?php echo esc_attr( $posted_img ); ?>">
                                            <i class="fa fa-cloud-upload"></i>
                                            <a href="#" class="dokan-feat-image-btn dokan-btn"><?php esc_html_e( 'Upload Photo', 'dokan-lite' ); ?></a>
                                        </div>
                                        <div class="image-wrap <?php echo esc_attr( $hide_img_wrap ); ?>">
                                            <a class="close dokan-remove-feat-image">&times;</a>
                                            <img src="<?php echo esc_url( $posted_img_url ); ?>" alt="">
                                        </div>
                                    </div>
                                </div>

                              <div class="img-right">
                                 <div class="dokan-product-gallery">
                                      <div class="dokan-side-body" id="dokan-product-images">
                                          <div id="product_images_container">
                                              <ul class="product_images dokan-clearfix">
                                                  <?php
                                                      if ( isset( $product_images ) ) { 

                                                          $gallery        = explode( ',', $product_images );

                                                          if ( $gallery ) {
                                                              foreach ( $gallery as $image_id ) {
                                                                  if ( empty( $image_id ) ) {
                                                                      continue;
                                                                  }

                                                                  $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                                                                  ?>
                                                                  <li class="image" data-attachment_id="<?php echo esc_attr( $image_id ); ?>">
                                                                      <img src="<?php echo esc_url( $attachment_image[0] ); ?>" alt="">
                                                                      <a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'dokan-lite' ); ?>">&times;</a>
                                                                  </li>
                                                                  <?php
                                                              }
                                                          }
                                                      }
                                                      ?>
                                                  <li class="add-image add-product-images tips" data-title="<?php esc_attr_e( 'Add gallery image', 'dokan-lite' ); ?>">
                                                      <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                  </li>
                                              </ul>
                                              <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
                                          </div>
                                      </div>
                                  </div> <!-- .product-gallery -->
                              </div>
                          </div>
                        </li>
						 
						 <p style="text-align:center; color:red; font-size:13px;" id="images-length">Minimum 3 images required</p>
						 
                        <li class="additional-info">
                          <label> Need to tell us any additional information?</label> 
                          <div class="input-text">
                            <textarea  class="form-control" name="additional_information" value=""><?php echo $additional_information; ?></textarea>
                          </div>
                        </li>
                        <li>
                          <div class="text-center">
                            <?php 
                            if (isset($_GET['rebook']) && !empty($_GET['rebook'])) 
                            {
                              ?>
                              <input type="hidden" name="rebook_deal" value="<?php echo $post_id; ?>">
                              <?php 
                            }
                            ?>
                            <?php wp_nonce_field( 'dokan_add_new_product', 'dokan_add_new_product_nonce' ); ?>
                            <input type="hidden" name="seller_post_status" value="in-review">
                             <button type="submit" name="add_product" class="submit-btn"  value="create_and_add_new"><?php esc_attr_e( 'Submit For Review', 'dokan-lite' ); ?></button><!-- 
                           <input type="submit" name="add_product" value="Submit For Review" class="submit-btn"> -->
                         </div>
                        </li>
                      </ul>
                   </div>
                   <div>
                     
                   </div>
              </form>
            </div>
               
          </div>
       </section>    


    </div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>

<?php

    /**
     *  dokan_new_product_wrap_after hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_new_product_wrap_after' );
?>
<style>
.error { border :1px solid red; }
</style>


<script>

	jQuery( document ).ready(function($){
		var inc = 0;
		
		var myVar = setInterval(myTimer, 0);

		function myTimer() {
			var gallery_li = $( '#product_images_container ul li' ).each(function(i,v){
				inc = i;
		 	});
			
			if( inc < 2 ){
				$( 'button.submit-btn' ).attr( 'disabled','disabled' );
				$("#images-length").text('Minimum 3 Images required');
			}else{
// 				clearInterval( myVar );
				$( 'button.submit-btn' ).removeAttr( 'disabled' );
				$("#images-length").text('');
			}
		}
		
	});

</script>

