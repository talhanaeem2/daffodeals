<?php
/**
 *  Dokan Dashboard Orders Template
 *
 *  Load order related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
global $wpdb;
$seller_id    = dokan_get_current_user_id();
$pid  = isset( $_GET['pid'] ) ? sanitize_text_field( base64_decode($_GET['pid']) ) : null;
$ships_date = get_post_meta($pid , '_ships_date', true);
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

    <div class="dokan-dashboard-wrap" id="dashboard_main1">

        <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_before' );
            do_action( 'dokan_order_content_before' );

        ?>
        <section class="order-download">
          <div class="container">
            <div class="row">
              <div class="col-md-12">
                <div class="deal-title">
                  <h2> <?php echo get_the_title($pid); ?></h2>
                </div>
             <div class="ship-date">
                  <p>Ships By: <span><?php echo date('m/d/Y',strtotime($ships_date)); ?></span></p>
                </div>
              <div class="order-list">
                <h3>Order fulfillment Downloads</h3>
                    <form action="" method="POST"> 
                        <input type="hidden" name="pid" value="<?php echo $_GET['pid']; ?>">  
                        <input type="hidden" name="deal_id" value="<?php echo $pid; ?>">  
                        <?php wp_nonce_field( 'order_download_action', 'order_download_nonce' ); ?>
                        <select name="order_download_list"  class="form-control deal-order-download">
                            <option value="">Please download </option>
                            <option value="download_summary">Pick list</option>
                            <option value="order_fulfillment">Order list</option>
                            <option value="order_shipping">Shiping list</option>
                        </select>
                    </form>
              </div>
              </div>
           
            </div>
          </div>
       </section>
    </div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); 


?>