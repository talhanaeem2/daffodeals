<?php
/**
 * Dokan Settings Profile Template
 *
 * @since 2.2.2 Insert action before profile settings form
 *
 * @package dokan
 */


$user_info = get_userdata($current_user);

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

do_action( 'dokan_profile_settings_before_form', $current_user, $profile_info ); 

/*echo '<pre>';
print_r($profile_info);
print_r($store_user);
echo '</pre>';*/
?>

<section class="profile-section">
      <div class="container">
        <div class="menu-profile">
           <ul>
             <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'settings/profile' ) ); ?>" class="active">Vendor Information</a></li>
             <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'settings/contract' ) ); ?>">Contract</a></li>
           </ul>
        </div>
         <div class="profile-detail">
            <?php if(isset($_SESSION['dd_success_msg'])){ ?>
              <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Success!</strong> <?php echo $_SESSION['dd_success_msg']; ?>.
              </div>
            <?php unset($_SESSION['dd_success_msg']);
            }if(isset($_SESSION['dd_error_msg'])){ 
             ?>
              <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error!</strong> <?php echo $_SESSION['dd_error_msg']; ?>.
              </div>
            <?php unset($_SESSION['dd_error_msg']); } ?>

            <form method="post" id="vendor-info-form" enctype="multipart/form-data"  action="<?php echo admin_url('admin-ajax.php'); ?>">
              <input type="hidden" name="action" value="vendor_information">
              <?php wp_nonce_field( 'vendor_info_action', 'vendor_info_nonce' ); ?>
               <div class="personal-info">
                <h3>Contact Information:</h3>
                 <ul>
                    <li>
                      <label>Company Name: </label> 
                      <div class="input-text">
                        <input type="text" class="form-control" name="dokan_store_name" value="<?php echo esc_html( $store_user->get_shop_name() ); ?>">
                      </div>
                    </li>
                    <li><label>Vendor Logo: </label> 
                      <div class="input-text">
                        <div class="browse-wrap">
                          <div class="title">Browse</div>
                            <input type="file" name="vendor_logo" class="upload" title="Choose a file to upload">
                        </div>
                        <div class="text-company"> Please upload your company logo.</div>
                        <p class="upload-path">250 x250 px size</p>
                        <?php 
                        $vendor_logo = $profile_info['vendor_logo'];
                        if (!empty($vendor_logo)) {
                          ?>
                          <img width="250" height="250" src="<?php echo $vendor_logo['url'] ?>">
                          <?php 
                        }
                        ?>
                      </div>
                    </li>
                    <li><label>Company Phone:</label> 
                      <div class="input-text">
                        <input type="text" class="form-control" name="phone" value="<?php echo isset($profile_info['phone'])?$profile_info['phone']:''?>">
                      </div>
                    </li>
                    <li><label>Company Email:</label> 
                      <div class="input-text">
                        <input type="text" class="form-control" name="user_email" value="<?php echo $user_info->user_email; ?>">
                      </div>
                    </li>

                  </ul>
               </div>
               <div class="payment-info">
                  <h3>Payment Information:</h3>
                  <p>We will be paying our vendors through PayPal. Please provide your PayPal email address below.</p>
                  <input type="text" class="form-control" name="settings[paypal][email]" value="<?php echo isset($profile_info['payment']['paypal']['email'])?$profile_info['payment']['paypal']['email']:''; ?>">
               </div>
               <div class="address-info">
                 <h3>Company Address:</h3>
                 <div class="row">
                   <!-- <div class="col-md-12">
                    <input type="text" class="form-control" placeholder="Name" name="">
                  </div> -->
                   <div class="col-md-6">
                      <input type="text" class="form-control" placeholder="Address" name="dokan_address[street_1]" value="<?php echo isset($profile_info['address']['street_1'])?$profile_info['address']['street_1']:''?>">
                    </div>
                   <div class="col-md-6">
                      <input type="text" class="form-control" placeholder="Address line 2" name="dokan_address[street_2]" value="<?php echo isset($profile_info['address']['street_2'])?$profile_info['address']['street_2']:''?>">
                   </div>
                    <div class="col-md-4">
                      <input type="text" class="form-control" placeholder="City" name="dokan_address[city]" value="<?php echo isset($profile_info['address']['city'])?$profile_info['address']['city']:''?>">
                    </div>
                    <div class="col-md-4">
                      <input type="text" class="form-control" placeholder="State" name="dokan_address[state]" value="<?php echo isset($profile_info['address']['state'])?$profile_info['address']['state']:''?>">
                    </div>
                    <div class="col-md-4">
                      <input type="text" class="form-control" placeholder="Zipcode" name="dokan_address[zip]" value="<?php echo isset($profile_info['address']['zip'])?$profile_info['address']['zip']:''?>">
                       <!--  <i class='fa fa-refresh fa-spin'></i> -->

                    </div>
                 </div>
                 <div class="text-center">
                   <input type="submit" name="" value="Save"  class="submit-btn" />

                 </div>
               </div>
               
            </form>
         </div>
      </div>
   </section>

<?php
do_action( 'dokan_profile_settings_after_form', $current_user, $profile_info ); ?>
 <script>
 var span = document.getElementsByClassName('upload-path');
  // Button
  var uploader = document.getElementsByName('upload');
  // On change
  for( item in uploader ) {
    // Detect changes
    uploader[item].onchange = function() {
      // Echo filename in span
      span[0].innerHTML = this.files[0].name;
    }
  }
</script>


