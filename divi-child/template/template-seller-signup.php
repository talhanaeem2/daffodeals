<?php 

/* Template name: Seller Signup */

/*if (is_user_logged_in()) {
	wp_redirect(home_url());
	exit();
}*/

get_header(); 

wp_enqueue_script( 'dokan-vendor-registration' );

// Get content width and sidebar position
//$content_class = basel_get_content_class();

$product_cats = array('Accessories','Apparel','Beauty','Craft/DIY','Footwear','Furniture','Home Goods','Jewelry','Kids/Baby','Tech/Gadgets','Other');
$TimeInBusiness = array('Brand new/Haven\'t opened yet','Less than a year','1-3 years','3-5 years','Over 5 years');
$AnnualRevenueLastYear = array('$0-$10,000','$10,000-$50,000','$50,000-$100,000','$100,000-$500,000','$500,000-$1,000,000','$1,000,000+');
$IntendedDiscountRate = array('10-25%','25-50%','More than 50%');
$free_shipping = array('Yes','No');
$businessType = array('Individual/Sole Proprietor', 'Partnership', 'C Corporation', 'S Corporation', 'Limited Liability Company');
?>


<div class="site-content <?php //echo esc_attr( $content_class ); ?>" role="main">
	<section class="profile-section">
      <div class="container">
         <div class="profile-detail vendor">
             <h2>Apply to be a Vendor on Daffodeals!</h2>
			<?php 
			//WC 3.5.0
			$all_notices  = WC()->session->get( 'wc_notices', array() );
			//print_r($all_notices);
			if ( version_compare( WC()->version, '3.5.0', '<' ) ) {
				wc_print_notices();
			}
			$postdata = wc_clean( $_POST );
      //print_r($postdata);
      if (is_user_logged_in() && empty($postdata)) {
        $user_id = get_current_user_id();
        $f_name = get_user_meta( $user_id, 'first_name', true );
        $l_name = get_user_meta( $user_id, 'last_name', true );
        $user = get_user_by( 'id', $user_id);
        $postdata['fname'] = $f_name;
        $postdata['lname'] = $l_name;
        //print_r($user);
        $postdata['email'] = $user->user_email;
      }
      
			?>
            <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
            	<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
            	<?php do_action( 'woocommerce_register_form_start' ); ?>
               <div class="personal-info">
                 <ul>
                    <li>
                      <label>Company Name: </label> 
                      <div class="input-text">
                        <input type="text"  class="form-control" name="shopname" id="sellerk-url" value="<?php if ( ! empty( $postdata['shopname'] ) ) echo esc_attr($postdata['shopname']); ?>" autocomplete="off" required="required" />
                        <input type="text"  style="display: none;" class="input-text form-control hide" name="shopurl" id="seller-url" value="<?php if ( ! empty( $postdata['shopurl'] ) ) echo esc_attr($postdata['shopurl']); ?>" required="required">
						            <small class="hide" style="display: none;"><?php echo site_url(); ?>/store/<strong id="url-alart"></strong></small>
                      </div>
                    </li>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) && !is_user_logged_in()) : ?>
                    <li>
                       <label><?php esc_html_e( 'Password', 'woocommerce' ); ?>: </label> 
                        <div class="input-text">
                            <input type="password" class="form-control" name="password" id="reg_password" required="required" autocomplete="new-password" />
                        </div>
                    </li>
                    <?php endif; ?>

                    <li>
                      <label>Website URL:</label> 
                      <div class="input-text">
                        <input type="text" class="form-control" name="website_url" autocomplete="off" id="website" value="<?php if ( ! empty( $postdata['website_url'] ) ) echo esc_attr($postdata['website_url']); ?>" required="required" />
                      </div>
                    </li>
                  </ul>
               </div>
               <div class="address-info ">
                  <h3>Contact Info: <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/contact-icon.png" alt="contact"></h3>
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="fname" autocomplete="off" placeholder="First Name" id="first-name" value="<?php if ( ! empty( $postdata['fname'] ) ) echo esc_attr($postdata['fname']); ?>" required="required" />
                    </div>
                    <div class="col-md-6">
                    	<input type="text" class="form-control" name="lname" autocomplete="off" placeholder="Last Name" id="last-name" value="<?php if ( ! empty( $postdata['lname'] ) ) echo esc_attr($postdata['lname']); ?>" required="required" />
                    </div>
                    <div class="col-md-6">
                      <input type="email" class="form-control" required="required" autocomplete="off" placeholder="Email" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $postdata['email'] ) ) ? esc_attr( $postdata['email'] ) : ''; ?>" />
                    </div>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="phone" autocomplete="off" id="companyPhone" placeholder="Phone" value="<?php if ( ! empty( $postdata['phone'] ) ) echo esc_attr($postdata['phone']); ?>" required="required" />
                    </div>
                  </div>
               </div>
               <div class="address-info">
                 <h3>Company Address: <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/loction-icon.png" alt=""></h3>
                 <div class="row">
                   <!-- <div class="col-md-12">
                    <input type="text" class="form-control" placeholder="Name" name="">
                  </div> -->
                   <div class="col-md-6">
                      <input type="text" class="form-control" name="streetAddress" autocomplete="off" placeholder="Address" id="streetAddress" value="<?php if ( ! empty( $postdata['streetAddress'] ) ) echo esc_attr($postdata['streetAddress']); ?>" required="required" />
                    </div>
                   <div class="col-md-6">
                       <input type="text" class="form-control" name="streetAddress2" autocomplete="off" placeholder="Address line 2" id="streetAddress2" value="<?php if ( ! empty( $postdata['streetAddress2'] ) ) echo esc_attr($postdata['streetAddress2']); ?>" />
                   </div>
                    <div class="col-md-4">
                    	<input type="text" class="form-control" placeholder="City" autocomplete="off" name="locality" id="locality" value="<?php if ( ! empty( $postdata['locality'] ) ) echo esc_attr($postdata['locality']); ?>" required="required" />
                    </div>
                    <div class="col-md-4">
                      <input type="text" class="form-control" name="region" autocomplete="off" placeholder="State" id="region" value="<?php if ( ! empty( $postdata['region'] ) ) echo esc_attr($postdata['region']); ?>" required="required" />
                    </div>
                    <div class="col-md-4">
                    	<input type="text" class="input-text form-control" autocomplete="off" name="postalCode" placeholder="Zipcode" id="postalCode" value="<?php if ( ! empty( $postdata['postalCode'] ) ) echo esc_attr($postdata['postalCode']); ?>" required="required" />
                    </div>
                 </div>
              
               </div>
                <div class="personal-info">
                 <h3>Business Info:</h3>
                 <ul>
                    <li>
                      <label>Time in business</label> 
                      <div class="input-text">
                        <select id="TimeInBusiness" name="TimeInBusiness" required="required" placeholder="Please select one..." class="form-control">
							<option value="">Please select one...</option>
							<?php foreach ($TimeInBusiness as $key => $business) {
								$selected = '';
								if (isset($postdata['TimeInBusiness']) && $postdata['TimeInBusiness'] == $business) {
									$selected = 'selected';
								}
								?>
								<option <?php echo $selected; ?> value="<?php echo $business; ?>"><?php echo $business; ?></option>
								<?php 
							} ?>
						</select>
                      </div>
                    </li>
                    
                    <li>
                      <label>Business Type</label> 
                      <div class="input-text">
                        <select id="business-formation" name="businessType" required="required" placeholder="Please select one..." class="form-control">
							<option value="">Please select one...</option>
							<?php foreach ($businessType as $key => $type) { 
								$selected = '';
								if (isset($postdata['businessType']) && $postdata['businessType'] == $type) {
									$selected = 'selected';
								}
								?>
								<option <?php echo $selected; ?> value="<?php echo $type; ?>"><?php echo $type; ?></option>
							<?php } ?>
						</select>
                      </div>
                    </li>
                     <li>
                      <label>Annual revenue last year</label> 
                      <div class="input-text">
                        <select id="AnnualRevenueLastYear" name="AnnualRevenueLastYear" required="required" placeholder="Please select one..." class="form-control">
							<option value="">Please select one...</option>
							<?php foreach ($AnnualRevenueLastYear as $key => $revenue) {
								$selected = '';
								if (isset($postdata['AnnualRevenueLastYear']) && $postdata['AnnualRevenueLastYear'] == $revenue) {
									$selected = 'selected';
								}
								?>
								<option <?php echo $selected; ?> value="<?php echo $revenue; ?>"><?php echo $revenue; ?></option>
								<?php 
							} ?>
						</select>
                      </div>
                    </li>
                    <li>
                      <label>EIN <span>(Employer Identification Number)</span></label> 
                      <div class="input-text">
                        <input type="text" class="input-text form-control" autocomplete="off" name="employerIdentificationNumber" id="employerIdentificationNumber" value="<?php if ( ! empty( $postdata['employerIdentificationNumber'] ) ) echo esc_attr($postdata['employerIdentificationNumber']); ?>" required="required" />
                      </div>
                    </li>
                  </ul>
               </div>
               <div class="personal-info">
                 <h3>Product  Info:</h3>
                 <ul>
                     <li class="additional-info">
                      <label> What should we know about your product?</label> 
                      <div class="input-text">
                        <textarea required="required" placeholder="Tell us about your products..." name="ProductDescription" class="form-control"><?php if ( ! empty( $postdata['ProductDescription'] ) ) echo esc_attr($postdata['ProductDescription']); ?></textarea>
                      </div>
                    </li>
                  </ul>
               </div>
               <div class="personal-info">
                 <h3>Sample Product Images:</h3>
                 <ul>
                     <li class="additional-info">
                      <label> Please upload 2-5 images of your product.</label> 
                      <div class="simple-product-img ">                        
                        <ul id="seller-product-preview-imgs"><!-- <span id="product-no-image">No image chosen...</span> --></ul>
                        <div class="img-block">
                          <div class="browse-img">
                              <div class="title">Browse</div>
                              <input type="file" accept="image/*" title="Choose a file to upload" required="required" id="seller-product-preview" multiple="multiple" name="seller_signup_images" >
                            </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                  <div class="text-center">
                    <?php if (is_user_logged_in()) { ?>
                        <?php wp_nonce_field( 'account_migration_action', 'account_migration_nonce' ); ?>
                        <input type="submit" name="account_migration" value="<?php _e( 'Submit', 'dokan' ); ?>"  class="submit-btn" />
                    <?php }else{ ?> 
                        <?php wp_nonce_field( 'woocommerce-register' ); ?>
                        <input type="submit" name="register" value="Save"  class="submit-btn" />
                        <input type="hidden" name="role" value="seller">
                    <?php } ?>
                 </div>
               </div>
               <?php do_action( 'woocommerce_register_form_end' ); ?>                     
            </form>
         </div>
      </div>
   </section>
</div><!-- .site-content -->

<?php get_footer(); ?>