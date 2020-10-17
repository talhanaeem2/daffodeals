<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Dokan Dashboard Seller Profile Store Form Template
 *
 * @since 2.4
 */

$gravatar_id    = ! empty( $profile_info['gravatar_id'] ) ? $profile_info['gravatar_id'] : 0;
$gravatar_id    = ! empty( $profile_info['gravatar'] ) ? $profile_info['gravatar'] : $gravatar_id;
$banner_id      = ! empty( $profile_info['banner_id'] ) ? $profile_info['banner_id'] : 0;
$banner_id      = ! empty( $profile_info['banner'] ) ? $profile_info['banner'] : $banner_id;
$location      = isset( $profile_info['location'] ) ? $profile_info['location'] : '';
$dokan_owner   = isset( $profile_info['dokan_owner'] ) ? $profile_info['dokan_owner'] : '';
$about_owner   = isset( $profile_info['about_owner'] ) ? $profile_info['about_owner'] : '';
$owner_website = isset( $profile_info['owner_website'] ) ? $profile_info['owner_website'] : '';
$websiteIsPrivate = isset( $profile_info['websiteIsPrivate'] ) ? $profile_info['websiteIsPrivate'] : '';
$store_user               = dokan()->vendor->get( $current_user );

$seller_owner_image = get_user_meta($current_user, '_seller_owner_image',true);
//print_r($seller_owner_image);
?>
<?php do_action( 'dokan_seller_profile_before_form', $current_user, $profile_info ); ?>
    
    <div class="page-header">
        <h1><?php esc_html_e( 'Seller Profile', 'dokan-lite' ); ?></h1>
        <p><?php esc_html_e( 'Your profile helps customers get to know you and your brand. Don’t be afraid to show some personality!', 'dokan-lite' ); ?></p>
    </div>

    <form method="post" id="seller-profile-form"  action="" class="dokan-form-horizontal">

        <?php wp_nonce_field( 'seller_profile_nonce' ); ?>

            <div class="dokan-form-group">
                <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Company Name', 'dokan-lite' ); ?></label>
                <div class="dokan-w5 dokan-text-left">
                    <span><?php echo esc_html( $store_user->get_shop_name() ); ?></span>
                </div>
            </div>

            <div class="dokan-form-group">
                <label class="dokan-w3 dokan-control-label" for="dokan_gravatar"><?php esc_html_e( 'Seller Profile Image', 'dokan-lite' ); ?></label>
                <div class="dokan-w5 dokan-gravatar">
                    <div class="dokan-left gravatar-wrap<?php echo $gravatar_id ? '' : ' dokan-hide'; ?>">
                        <?php $gravatar_url = $gravatar_id ? wp_get_attachment_url( $gravatar_id ) : ''; ?>
                        <input type="hidden" class="dokan-file-field" value="<?php echo esc_attr( $gravatar_id ); ?>" name="dokan_gravatar">
                        <img class="dokan-gravatar-img" src="<?php echo esc_url( $gravatar_url ); ?>">
                        <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                    </div>
                    <div class="gravatar-button-area<?php echo esc_attr( $gravatar_id ) ? ' dokan-hide' : ''; ?>">
                        <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default"><i class="fa fa-cloud-upload"></i> <?php esc_html_e( 'Upload Photo', 'dokan-lite' ); ?></a>
                    </div>
                    <p>Suggested minimum image size is 200 x 200 pixels. This image will appear both on your seller profile page and all of your deal product listings. We strongly recommend you make this image a professional one of you, your product, a modeling of your product or your logo.</p>
                </div>
                
            </div>

            <div class="dokan-form-group">
                <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Location', 'dokan-lite' ); ?></label>
                <div class="dokan-w5 dokan-text-left">
                    <input id="dokan_store_location" required value="<?php echo esc_attr( $location ); ?>" name="store_location" placeholder="<?php esc_attr_e( 'Location', 'dokan-lite' ); ?>" class="dokan-form-control" type="text">
                </div>
            </div>

            <div class="dokan-form-group">               
                <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Owner Image', 'dokan-lite' ); ?></label>
                <div class="dokan-w5 dokan-text-left">
                    <div class="dokan-banner"> 
                        
                        <div class="image-wrap<?php echo (isset($seller_owner_image['image_url']) && $seller_owner_image['image_url']) ? '' : ' dokan-hide'; ?>">
                            <?php $banner_url = $banner_id ? wp_get_attachment_url( $banner_id ) : ''; ?>
                            <input type="hidden" class="dokan-file-field" value="<?php echo $banner_id; ?>" name="dokan_banner">
                            <img class="dokan-banner-img" src="<?php echo ($seller_owner_image['image_url'])?$seller_owner_image['image_url'].'?'.time():''; ?>">

                            <a class="close dokan-remove-banner-image">&times;</a>
                        </div>
                        <div class="button-area<?php echo (isset($seller_owner_image['image_url']) && $seller_owner_image['image_url']) ? ' dokan-hide' : ''; ?>">        
                            

                            <label class="dokan-btn dokan-btn-info dokan-theme owner_img_crop">
                                <i class="fa fa-cloud-upload"></i>
                                <?php esc_html_e( 'Upload Owner Image', 'dokan-lite' ); ?>
                                <input type="file" class="owner_img_field" id="input" name="image" accept="image/*">
                            </label>

                            
                        </div>
                        
                    </div> 
                   
                    <p class="help-block">
                        <?php
                            /**
                             * Filter `dokan_banner_upload_help`
                             *
                             * @since 2.4.10
                             */
                            $general_settings = get_option( 'dokan_general', [] );
                            $banner_width     = ! empty( $general_settings['store_banner_width'] ) ? $general_settings['store_banner_width'] : 200;
                            $banner_height    = ! empty( $general_settings['store_banner_height'] ) ? $general_settings['store_banner_height'] : 200;

                            $help_text = sprintf(
                                __('Suggested minimum image size is %s x %s. This is a picture of you allowing customers to connect a face to your business. This image will we be located in the About You section on your seller profile page.', 'dokan-lite' ),
                                $banner_width, $banner_height
                            );

                            echo esc_html( apply_filters( 'dokan_banner_upload_help', $help_text ) );
                            ?>
                        </p>
                    </div>

                    
                </div> <!-- .dokan-banner -->
                


            <?php do_action( 'dokan_seller_profile_after_banner', $current_user, $profile_info ); ?>

        

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_owner"><?php esc_html_e( 'Owner(s)', 'dokan-lite' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="dokan_owner" required value="<?php echo esc_attr( $dokan_owner ); ?>" name="dokan_owner" placeholder="<?php esc_attr_e( 'Owner(s)', 'dokan-lite' ); ?>" class="dokan-form-control" type="text">
            </div>
        </div>

         <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_about"><?php esc_html_e( 'About You', 'dokan-lite' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <textarea id="dokan_about" required name="about_owner" placeholder="<?php esc_attr_e( 'About You', 'dokan-lite' ); ?>" class="dokan-form-control"><?php echo esc_attr( $about_owner ); ?></textarea>               
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="owner_website"><?php esc_html_e( 'Website', 'dokan-lite' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="owner_website" required value="<?php echo esc_attr( $owner_website ); ?>" name="owner_website" placeholder="<?php esc_attr_e( 'Website', 'dokan-lite' ); ?>" class="dokan-form-control" type="text">
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label"></label>
            <div class="dokan-w5 dokan-text-left">
                <div class="seller_checkbox">
                    <input type="checkbox" <?php if(!empty($websiteIsPrivate)){ echo 'checked';  } ?> color="primary" id="websiteIsPrivate" name="websiteIsPrivate" value="false">
                    <label for="websiteIsPrivate">Keep my website private. Consumers often use this link to find more about your business, products and reviews.</label>
                </div>
            </div>
        </div>

        <?php do_action( 'dokan_seller_profile_form_bottom', $current_user, $profile_info ); ?>

        
        <div class="seller_footer">
            <div class="seller_footer_details">
                <div class="seller_left">
                    <a target="_blank" href="<?php echo esc_url( dokan_get_store_url( dokan_get_current_user_id() ) ); ?>" class="btn btn-default"><?php esc_html_e( 'View On Daffodeals', 'dokan-lite' ); ?></a>
                </div>
                <div class="seller_right">
                    <button type="submit" class="btn btn-default ajax_prev"><?php esc_html_e( 'Save Changes', 'dokan-lite' ); ?></button>
                </div>
            </div>
        </div>
    </form>

<?php do_action( 'dokan_seller_profile_after_form', $current_user, $profile_info ); ?>

<div id="crop_image_modal" class="crop_modal">
  <span class="crop_close">&times;</span>
    <div class="image-crop-area">
         <!-- <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.zoom(0.1)">
              <span class="fa fa-search-plus"></span>
            </span>
          </button>
          <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.zoom(-0.1)">
              <span class="fa fa-search-minus"></span>
            </span>
          </button>
           <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45" title="Rotate Left">
            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.rotate(-45)" aria-describedby="tooltip956418">
              <span class="fa fa-undo-alt"></span>
            </span>
          </button>
          <button type="button" class="btn btn-primary" data-method="rotate" data-option="45" title="Rotate Right">
            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.rotate(45)">
              <span class="fa fa-redo-alt"></span>
            </span>
          </button> -->
        <button type="button" class="btn btn-primary" id="crop">Crop</button>
        <div class="img-container">
            <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
        </div>
    </div>
</div>

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function () {      
      var image = document.getElementById('image');
      var input = document.getElementById('input');
     
      var cropper;

      //$('[data-toggle="tooltip"]').tooltip();

      input.addEventListener('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
          input.value = '';
          image.src = url;
            var modal = document.getElementById("crop_image_modal");
            var modalImg = jQuery("#crop_image");   
            modal.style.display = "block";
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
          file = files[0];

          if (URL) {
            done(URL.createObjectURL(file));
          } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function (e) {
              done(reader.result);
            };
            reader.readAsDataURL(file);
          }
        }

         cropper = new Cropper(image, {
          aspectRatio: 1,
          viewMode: 3,
        });

      });

    jQuery(document).on('click','.crop_close', function(){
        var modal = document.getElementById("crop_image_modal");
        modal.style.display = "none";
        cropper.destroy();
        cropper = null;
    });

      document.getElementById('crop').addEventListener('click', function () {
        var initialAvatarURL;
        var canvas;
        jQuery(this).append('<i class="fa fa-refresh fa-spin"></i>'); 
        if (cropper) {
          canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 600,
          });          
          canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('action', 'kc_owner_image');
            formData.append('avatar', blob, 'avatar.jpg');
            $.ajax('<?php echo admin_url('admin-ajax.php'); ?>', {
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              success: function () {
               
              },
              error: function () {
               
              },
              complete: function () {
               window.location.href = '';
              },
            });
          });
        }
      });
    });

</script>