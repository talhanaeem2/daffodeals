<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed'); 

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $post_title     = $post->post_title;
    $post_content   = $post->post_content;
    $post_excerpt   = $post->post_excerpt;
    $post_status    = $post->post_status;
    $product        = wc_get_product( $post_id );
    $from_shortcode = true;
}

$posted_img_url   = $hide_instruction = '';
$hide_img_wrap    = 'dokan-hide';

$title = $post->post_title;   
$post_content = $post->post_content;  
$term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') ); 
$term = !empty($term)?$term[0]:0;
$post_metas = get_post_meta($post_id);    
$product_options = isset($post_metas['_product_options'])?$post_metas['_product_options'][0]:'';
$isHandMade = isset($post_metas['_isHandMade'])?$post_metas['_isHandMade'][0]:'';
$isPersonalized = isset($post_metas['_isPersonalized'])?$post_metas['_isPersonalized'][0]:'';
$startDate = isset($post_metas['_startDate'])?$post_metas['_startDate'][0]:'';
$ships_date = isset($post_metas['_ships_date'])?$post_metas['_ships_date'][0]:'';
$endDate = isset($post_metas['_endDate'])?$post_metas['_endDate'][0]:'';
$sale_price = isset($post_metas['_sale_price'])?$post_metas['_sale_price'][0]:'';
$regular_price = isset($post_metas['_regular_price'])?$post_metas['_regular_price'][0]:'';
$shipping_price = isset($post_metas['_shipping_price'])?$post_metas['_shipping_price'][0]:'';
$shippingPriceAdditionalItems = isset($post_metas['_shippingPriceAdditionalItems'])?$post_metas['_shippingPriceAdditionalItems'][0]:'';

$shipping_weight_lbs = isset($post_metas['_shipping_weight_lbs'])?$post_metas['_shipping_weight_lbs'][0]:'';  
$shipping_weight_oz = isset($post_metas['_shipping_weight_oz'])?$post_metas['_shipping_weight_oz'][0]:'';  
$totalQtyAvailable = isset($post_metas['_totalQtyAvailable'])?$post_metas['_totalQtyAvailable'][0]:''; 
$additional_information = isset($post_metas['_additional_information'])?$post_metas['_additional_information'][0]:''; 
$personalized_handmade = isset($post_metas['_personalized_handmade'])?$post_metas['_personalized_handmade'][0]:''; 

if ( has_post_thumbnail( $post_id ) ) {
    $feat_image_id     = get_post_thumbnail_id( $post_id );
    $posted_img     = $feat_image_id; 
   // $posted_img_url =  wp_get_attachment_url($feat_image_id); 
}
$product_images = isset($post_metas['_product_image_gallery'])?$post_metas['_product_image_gallery'][0]:'';

if ( !empty( $posted_img ) ) {
    $posted_img     = empty( $posted_img ) ? 0 : $posted_img;
    $posted_img_url = wp_get_attachment_url( $posted_img );
    $hide_instruction = 'dokan-hide';
    $hide_img_wrap = '';
}

if( ($post->post_status == 'publish' || $post->post_status == 'finalized') && current_user_can('seller')){ 
    $disabled = 'disabled="disabled"';
} else{
	$disabled = '';
}
?>
<div class="personal-info">

<ul>
<li>
  <label>Deal Title:</label> 
  <div class="input-text">
    <input class="form-control editreqfields" autocomplete="off"  name="post_title" id="post-title" type="text" <?php echo $disabled; ?>  value="<?php echo $title; ?>">
  </div>
</li>
<li>
  <div class="row">
    <div class="col-lg-6 col-md-12">
      <label>Product Category:</label> 
      <div class="input-text">
        <div class="data-align">
    <?php
    $selected_cat  = $term;
    if (!empty($disabled)) {
       $cat = get_term_by('id', $selected_cat, 'product_cat');
       echo $cat->name;
    }else{
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
    }
    
    ?>     
    </div>                      
  </div>
  </div>

  <div class="col-lg-6 col-md-12">
    <label>Personalized/Handmade:</label>
    <div class="input-text">
      <select name="personalized_handmade" <?php echo $disabled; ?> id="personalized_handmade" class="form-control">
        <option <?php echo ($personalized_handmade == 'yes')?'selected':''; ?> value="yes">Yes</option>
        <option <?php echo ($personalized_handmade == 'no')?'selected':''; ?> value="no">No</option>
      </select>
    </div>
  </div>
  </div>
</li>
<li><label>Product Description:</label> 
  <div class="input-text">
    <textarea name="post_content"  id="post_content" rows="5" class="form-control editreqfields" <?php echo $disabled; ?> ><?php echo $post_content; ?></textarea>
  </div>
</li>
<li class="date">
  <div class="row">
      <div class="col-lg-6 col-md-12">
        <label>Deal Start Date:</label> 
          <div class="input-text">
            <input type="text" autocomplete="off" class="form-control  editreqfields"  <?php echo $disabled; ?> id="preferred-start-date" name="startDate" value="<?php echo date('m/d/Y', strtotime($startDate)); ?>">
            <div class="date-icon">
              <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/date-icon.png" alt="icon">
            </div>
          </div>
      </div>
      <div class="col-lg-6 col-md-12">
        <label>Ship By Date:</label> 
          <div class="input-text">
            <?php 
            if ($personalized_handmade == 'yes') {
              $shipping_days = date('m/d/Y',strtotime($endDate. ' + 14 days'));
            }else{
              $shipping_days = date('m/d/Y',strtotime($endDate. ' + 7 days'));
            }
            ?>
            <input type="text" autocomplete="off" enddate="<?php echo date('m/d/Y',strtotime($endDate)); ?>" shipbydate="<?php echo $shipping_days; ?>"  class="form-control  editreqfields" <?php echo $disabled; ?> id="preferred-ship-date" name="ship_by" value="<?php echo date('m/d/Y', strtotime($ships_date)); ?>">
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
            <input type="number" class="form-control dokan-product-sales-price <?php if($post_status == 'editing'){echo 'editreqfields';} ?>" id="sale_price" name="_sale_price" placeholder="" <?php echo $disabled; ?> value="<?php echo $sale_price; ?>" step="any">
          </div>
      </div>
      <div class="col-lg-6 col-md-12">
        <label>MSRP <span>(Retail Price):</span></label> 
          <div class="input-text">
            <input type="number" class="form-control dokan-product-regular-price <?php if($post_status == 'editing'){echo 'editreqfields';} ?>" id="retail_price" name="_regular_price" placeholder="" <?php echo $disabled; ?> value="<?php echo $regular_price; ?>" step="any">
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
		                <input type="number" class="form-control dokan-product-shipping-price" id="shipping_price" name="shipping_price" placeholder="" <?php echo $disabled; ?> id="shipping-price" value="<?php echo $shipping_price; ?>" step="any">
		              <p>1st Item </p>
		         </div>
               <div class="col-lg-4 col-md-12">             
	              <input type="number" class="form-control dokan-product-shipping-price" <?php echo $disabled; ?> name="shippingPriceAdditionalItems" placeholder=""  value="<?php echo $shippingPriceAdditionalItems;  ?>" step="any">
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
    <input type="number" class="form-control dokan-product-shipping-price editreqfields" name="totalQtyAvailable" placeholder="" id="total_qty" value="<?php echo $totalQtyAvailable; ?>" <?php echo $disabled; ?> min="1" step="any">
  </div>
</li>
<li class="upload-img">
  <label>Images:</label> 
  <div class="input-text">
     
        <div class="featured-image">
            <div class="dokan-feat-image-upload">
                <div class="instruction-inside <?php echo esc_attr( $hide_instruction ); ?>">
                    <input type="hidden" name="feat_image_id" class="dokan-feat-image-id editreqfields" value="<?php echo esc_attr( $posted_img ); ?>">
                    <i class="fa fa-cloud-upload"></i>
                    <a href="#" class="dokan-feat-image-btn dokan-btn"><?php esc_html_e( 'Upload Photo', 'dokan-lite' ); ?></a>
                </div>
                <div class="image-wrap <?php echo esc_attr( $hide_img_wrap ); ?>">
                    <i class="fa fa-crop" aria-hidden="true" datasrc="<?php echo esc_url( $posted_img_url ); ?>" datafea="1" imgid="<?php echo esc_attr( $posted_img ); ?>"></i>

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
                                          $attachment_image2 = wp_get_attachment_image_src( $image_id, 'full' );
                                          ?>
                                          <li class="image" data-attachment_id="<?php echo esc_attr( $image_id ); ?>">
                                              <img src="<?php echo esc_url( $attachment_image[0] ); ?>" alt="">
                                              <i class="fa fa-crop" aria-hidden="true" datasrc="<?php echo esc_url( $attachment_image2[0] ); ?>" datafea="0" imgid="<?php echo esc_attr( $image_id ); ?>"></i>
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
<li class="additional-info">
  <label> Need to tell us any additional information?</label> 
  <div class="input-text">
    <textarea  class="form-control" <?php echo $disabled; ?> name="additional_information"><?php echo $additional_information; ?></textarea>
  </div>
</li>
<?php if (current_user_can('administrator')) { 
     $rebook = get_post_meta( $post_id, '_rebook', true);
   if(!empty($rebook)){ ?>
      <input type="hidden" name="rebook" value="1">
    <?php } ?>
<li>
  <label> <?php esc_html_e( 'Status', 'dokan-lite' ); ?></label> 
  <div class="input-text">

    <select name="post_status" class="dokan-form-control editreqfields" id="seller_post_status">
        <?php 
        $seller_status = seller_deal_status();        
        $save_status = $post->post_status;

        foreach ($seller_status as $key => $status) {
            $selected = '';
            if (!empty($save_status) && $save_status == $key) {
                $selected = 'selected';
            }
        ?>
            <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $status; ?></option>
            <?php 
        } ?>
    </select>
    <br>
    <textarea id="vendor_status_message" placeholder="Enter message here." name="vendor_status_message" class="form-control" style="display: none;"></textarea>
  </div>
</li>
<?php   } ?>

</ul>
</div>
<div>

</div>
 