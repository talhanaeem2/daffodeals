<?php

global $post, $wpdb;

$from_shortcode = false;

if ( !isset( $post->ID ) && ! isset( $_GET['product_id'] ) ) {
    wp_die( esc_html__( 'Access Denied, No product found', 'dokan-lite' ) );
}

if ( isset( $post->ID ) && $post->ID && 'product' == $post->post_type ) {
    $post_id      = $post->ID;
    $post_title   = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status  = $post->post_status;
    $product      = wc_get_product( $post_id );
}

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

if ( ! dokan_is_product_author( $post_id ) && !current_user_can( 'administrator' )) {
    wp_die( esc_html__( 'Access Denied', 'dokan-lite' ) );
    exit();
}

if ( ! $from_shortcode ) {
    get_header();
}

if ( ! empty( $_GET['errors'] ) ) {
    Dokan_Template_Products::$errors = $_GET['errors'];
}
if($post->post_status == 'pulish' || $post->post_status == 'finalized'){ 
    $field_disabled = 'disabled="disabled"';
} else{
	$field_disabled = '';
}

?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

    <div class="dokan-dashboard-wrap" id="dashboard_main1">

        <?php

            /**
             *  dokan_dashboard_content_before hook
             *  dokan_before_product_content_area hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_before' );
            do_action( 'dokan_before_product_content_area' );
            
        ?>

        <section class="deal-section">
          <div class="container">
            <?php  //SUB MENU CALL HERE.
            dokan_get_template_part( 'products/prduct-sub-menu' ); ?>
            <div class="deal-detail">
              <form id="frm-edit-deal" action="" class="dokan-product-edits-form" method="post">
                  <input type="hidden" name="dokan_product_id" id="dokan-edit-product-id" value="<?php echo esc_attr( $post_id ); ?>"/>
                  <div class="menu-profile">
                    <ul>
                     <li><a class="ptabs active" targetid="#deal-details">Product Info</a></li>
						<li><a class="ptabs" targetid="#vendor-details">Vendor Info</a></li>
                     <?php if ($post->post_status == 'editing' || $post->post_status == 'finalized' || $post->post_status == 'publish' || $post->post_status == 'declined' || $post->post_status == 'ended' || $post->post_status == 'shipping'){ ?>
                        <li><a class="ptabs" targetid="#deal-options">Options</a></li>
                        <li><a class="ptabs" targetid="#deal-inventory">Inventory</a></li>
                     <?php } ?>
                    </ul>
                    <div class="edit-product-heading">
                        <span class="deal-title"><?php echo $post_title; ?></span>
                         <?php if ($post->post_status == 'editing'){ 
                          $finalize_deal_date = get_post_meta( $post_id, '_finalize_deal_date', true );
                          ?>                        
                          <span class="deal-title">Finalize By: <?php echo date('F d', strtotime($finalize_deal_date)); ?></span>
                        <?php } ?>
                    </div>
                  </div>
                 
                  <?php if (isset($_SESSION['deal_error'])) { ?>
                    <div class="dokan-alert dokan-alert-danger">
                        <a class="dokan-close" data-dismiss="alert">&times;</a>

                        <?php foreach ($_SESSION['deal_error'] as $error) { ?>

                            <strong><?php esc_html_e( 'Error!', 'dokan-lite' ); ?></strong> <?php echo esc_html( $error ); ?>.<br>

                        <?php } ?>
                    </div>
                <?php unset($_SESSION['deal_error']); } ?>

                <?php if ( isset( $_SESSION['deal_success'] ) ): ?>
                    <div class="dokan-alert dokan-alert-success">
                        <a class="dokan-close" data-dismiss="alert">&times;</a>
                        <strong><?php esc_html_e( 'Success!', 'dokan-lite' ); ?></strong>
                        <?php printf( __( 'Information saved successfully.') ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php unset($_SESSION['deal_success']);  endif; ?>

                  <div class="deal-tab-section" id="deal-details">
					   <?php 
					  	$vendor_id = get_post_field( 'post_author', get_the_id() );
						$vendor = new WP_User($vendor_id);
						$store_info = dokan_get_store_info( $vendor->ID );
					  	echo '<h3 style="text-align: center; color: rgb(100,100,100); margin-bottom: 30px;"> Store Name: ' . $store_info['store_name'] . '</h3>';
					  
					  ?>
                    <?php dokan_get_template_part( 'products/deal/details' ); ?>
                  </div> 
				  <div class="deal-tab-section" id="vendor-details" style="display: none;">
					  <?php 
					  	$vendor_id = get_post_field( 'post_author', get_the_id() );
						$vendor = new WP_User($vendor_id);
						$store_info = dokan_get_store_info( $vendor->ID );
					  	echo '<h3 style="text-align: center; color: rgb(100,100,100); margin-bottom: 30px;"> Store Name: ' . $store_info['store_name'] . '</h3>';
					  
					  ?>
				  </div>
                  <?php if ($post->post_status == 'editing' || $post->post_status == 'finalized' || $post->post_status == 'publish' || $post->post_status == 'declined' || $post->post_status == 'ended' || $post->post_status == 'shipping'){ ?>
                    <div class="deal-tab-section" id="deal-options" style="display: none;">
                      <div id="options">
                        <?php dokan_get_template_part( 'products/deal/options' ); ?>
                      </div>
                    </div>
				  	<div class="deal-tab-section" id="deal-inventory" style="display: none;">                      
                      <div id="inventory">
                        <?php dokan_get_template_part( 'products/deal/inventory' ); ?>
                      </div>
                    </div>
                    <?php 
                    if (current_user_can('seller')){
                      echo '<input type="hidden" name="post_status" value="'.$post->post_status.'">';
                    }
                    ?>
                  <?php } ?>
                  <?php wp_nonce_field( 'dokan_edit_deal', 'dokan_edit_deal_nonce' ); ?>
                 <input type="hidden" name="deal_step" value="">
                 <?php if (current_user_can('administrator')){ ?>
                    <button type="button" name="add_product" class="submit-btn btn-finalized"  value="create_and_add_new"><?php esc_attr_e( 'Finalize', 'dokan-lite' );  ?></button>
                 <?php  }else{ ?> 
                    <button type="submit" name="add_product" class="submit-btn"  value="create_and_add_new"><?php esc_attr_e( 'SAVE', 'dokan-lite' );  ?></button>
                 <?php  }  ?>
                 
              </form>
            </div>
               
          </div>
       </section>  
    </div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>


<script type="text/javascript">
jQuery(document).ready(function(){
  jQuery('.fa-crop').click(function(){
    var image = jQuery(this).attr('datasrc');
    var attach_id = jQuery(this).attr('imgid');
    var datafea = jQuery(this).attr('datafea');
    //console.log(image);
    jQuery('#imgsersss').attr('src', image);
    var image2 = document.querySelector('#imgsersss');
    var cropper;
    var $modal = $('#avatar-modal');
    $modal.on('shown.bs.modal', function () {
      cropper = new Cropper(image2, {
       aspectRatio:1/1,
      });
    }).on('hidden.bs.modal', function () {
      cropper.destroy();
      cropper = null;
    });     
    jQuery('.setDragMode').click(function(){
      var rotateval = jQuery(this).data('option');
      image2.cropper.setDragMode(rotateval);
    });
    jQuery('.rotate').click(function(){
      var rotateval = jQuery(this).data('option');
      image2.cropper.rotate(rotateval);
    });
    jQuery('.zoom').click(function(){
      var zooms = jQuery(this).data('option');
      image2.cropper.zoom(zooms);
    });
    jQuery('#avatar-save').click(function(){
        var initialAvatarURL;
        var canvas;
        if (cropper) {
          jQuery('.cropspin').show();
          canvas = cropper.getCroppedCanvas({
           
          });         
          canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('action', 'dd_crop_deal_image');
            formData.append('attach_id', attach_id);
            formData.append('isfeat', datafea);
            formData.append('dd_image_crop', '<?php echo wp_create_nonce('imgnonce'); ?>');
            formData.append('dealimg', blob, 'deal.png');
            $.ajax('<?php echo admin_url('admin-ajax.php'); ?>', {
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              success: function (data) {
                jQuery('.crop_msg').html(data);
              },
              error: function () {
                //avatar.src = initialAvatarURL;
                //$alert.show().addClass('alert-warning').text('Upload error');
              },
              complete: function () {
                //$progress.hide();
                setTimeout(function(){
                 window.location.href='';
                }, 2000);
               
              },
            });
          });
        }
      });
      $modal.modal('show');
  });
});
 </script>

<!-- Modal -->
<div class="modal fade" id="avatar-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Edit Image</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
             <img id="imgsersss" src="">
          </div>
        </div>
        <div class="modal-footer">
          <div class="row avatar-btns">
            <p class="crop_msg" style="font-weight: bold; text-align: center; width: 100%;"></p>
            <div class="col-md-9">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary setDragMode" data-method="setDragMode" data-option="move" title="Move">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;move&quot;)">
                      <span class="fa fa-arrows"></span>
                    </span>
                  </button>
                  <button type="button" class="btn btn-primary setDragMode" data-method="setDragMode" data-option="crop" title="Crop">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;crop&quot;)">
                      <span class="fa fa-crop"></span>
                    </span>
                  </button>
                </div>               
                <div class="btn-group">
                  <button type="button" class="btn btn-primary rotate" data-method="rotate" data-option="-45" title="Rotate Left">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-45)">
                      <span class="fa fa-rotate-left"></span>
                    </span>
                  </button>
                  <button type="button" class="btn btn-primary rotate" data-method="rotate" data-option="45" title="Rotate Right">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(45)">
                      <span class="fa fa-rotate-right"></span>
                    </span>
                  </button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-primary zoom" data-method="zoom" data-option="0.1" title="Zoom In">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
                      <span class="fa fa-search-plus"></span>
                    </span>
                  </button>
                  <button type="button" class="btn btn-primary zoom" data-method="zoom" data-option="-0.1" title="Zoom Out">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
                      <span class="fa fa-search-minus"></span>
                    </span>
                  </button>
                </div>
              </div>
              <div class="col-md-3">
                  <button type="button" id="avatar-save" class="btn btn-primary btn-block avatar-save">Crop <i class="fa fa-refresh fa-spin cropspin" style="display: none;"></i></button>
              </div>

            </div>
        </div>
      </div>
    </div>
  </div>
</div>

