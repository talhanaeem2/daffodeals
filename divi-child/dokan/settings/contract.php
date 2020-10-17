<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Dokan Dashboard Seller Contract Store Form Template
 *
 * @since 2.4
 */

//print_r($seller_emails);
$store_user               = dokan()->vendor->get( $current_user );

?>
<?php do_action( 'dokan_seller_email_before_form', $current_user, $profile_info ); ?>
    
<section class="profile-section">
  <div class="container">
    <div class="menu-profile">
      <ul>
        <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'settings/profile' ) ); ?>" >Vendor Information</a></li>
             <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'settings/contract' ) ); ?>" class="active">Contract</a></li>
      </ul>
    </div>
    <div class="contract-dec">
      <form action="" id="contract-frm" method="post">
        <?php wp_nonce_field( 'seller_contract_nonce' ); ?>
        <?php
          $args = array(
          'post_type' => 'contract',
          'post_status' => 'publish',
          'posts_per_page' => 1,
          );
          $the_query = new WP_Query( $args );
          // The Loop
          if ( $the_query->have_posts() ) :
          while ( $the_query->have_posts() ) : $the_query->the_post();
          global $post;
          ?>
        <div class="contract-list">
          <h2>
            <?php the_title(); ?>
          </h2>
          <?php the_content(); ?>
        </div>
        <?php
        //the_content('Continue Reading');
        endwhile;
        endif;
        // Reset Post Data
        wp_reset_postdata();
        ?>
        <div class="contract-input">
            <input type="text" name="terms_conditions"  <?php if(!empty($term_conditions)){ echo 'disabled="disabled"'; } ?> class="terms_conditions" value="<?php echo $term_conditions; ?>" placeholder="Type Your Name Here">
            <div class="text-center">
              <button type="submit" name="accept" <?php if(!empty($term_conditions)){ echo 'disabled="disabled"'; } ?> class="accept"> <?php if(!empty($term_conditions)){ echo 'Accepted'; }else{ echo 'Accept'; } ?> <i class="fa fa-refresh fa-spin" style="display: none;"></i></button>
              <div class="dokan-ajax-response"></div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
<?php do_action( 'dokan_seller_email_after_form', $current_user, $profile_info ); ?>


