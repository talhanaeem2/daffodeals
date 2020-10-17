<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Dokan Settings Main Template
 *
 * @since 2.4
 *
 * @package dokan
 */

function generate_key( $sellerId ) {
	$to_hash = $sellerId . date( 'U' ) . mt_rand();
	return 'DOKANSS-' . hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
}

global $wpdb, $wp_query,$wp;
$userid = get_current_user_id();

$shipstation_tbl = $wpdb->prefix . "shipstation_auth_keys";
$auth_key = $wpdb->get_var("SELECT auth_key FROM {$shipstation_tbl} WHERE user_id=".$userid); 
if (empty($auth_key)){
	//var_dump( 'auth_key is empty' );
//     $auth_key = WC_ShipStation_Integration::generate_key();
    $auth_key = generate_key( $userid );
    $data = array('user_id' => $userid, 'auth_key' => $auth_key);
    $format = array('%d','%s');
    $db = $wpdb->insert($shipstation_tbl,$data,$format);
	update_user_meta( $userid, 'shipstation_auth_key', $auth_key );
// 	var_dump( $shipstation_tbl );
// 	var_dump($userid);
}
?>
<?php do_action( 'dokan_dashboard_wrap_start' ); ?>
<div class="dashboard-resources">
    <div class="dokan-dashboard-wrap" id="dashboard_main1">
        <?php

            /**
             *  dokan_dashboard_content_before hook
             *  dokan_dashboard_settings_store_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_before' );
            do_action( 'dokan_dashboard_settings_content_before' );
		//var_dump($auth_key);
        ?>
        <section class="profile-section">
      <div class="container">
        <div class="tools">
         <form>
           <ul>
             <li>
              <div class="row">
                 <div class="col-lg-4 col-md-12">
                   <label>Shipstation Authentication Key: </label>
                 </div>
                 <div class="col-lg-8 col-md-12">
                   <input class="form-control" type="text" name="woocommerce_shipstation_auth_key" id="woocommerce_shipstation_auth_key" disabled="disabled" value="<?php echo $auth_key ; ?>" placeholder="" readonly="readonly">
                 </div>
               </div>
             </li>
             <li>
              <div class="row">
                 <div class="col-lg-6 col-md-12">
                   <label>Store URL:</label>
                 </div>
                 <div class="col-lg-6 col-md-12">
                   <input type="text" name="" class="form-control" disabled="disabled" placeholder="" value="<?php echo dokan_get_store_url($userid); ?>">
                 </div>
               </div>
             </li>
           </ul>
         </form>
         <div class="faq-vendor">
           <h2>Vendor FAQs:</h2>
           <div class="accordion_container">
                <?php
                $args = array(
                'post_type' => 'faqs',
                'post_status' => 'publish',
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'posts_per_page' => -1,
                );
                $the_query = new WP_Query( $args );
                // The Loop
                if ( $the_query->have_posts() ) :
                    $i = 1;
                while ( $the_query->have_posts() ) : $the_query->the_post();
                global $post;
                ?>
                    <div class="accordion_head"><strong>Q.<?php echo $i; ?></strong> <?php the_title(); ?> <span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none;">
                    <?php the_content(); ?>
                    </div>
                <?php
                //the_content('Continue Reading');
                    $i++;
                endwhile;
                endif;
                // Reset Post Data
                wp_reset_postdata();
                ?>
            </div>
         </div>
        </div>
      </div>
   </section>
    </div><!-- .dokan-dashboard-wrap -->
</div>
<script>
$(document).ready(function(){
//toggle the component with class accordion_body
$(".accordion_head").click(function(){
if ($('.accordion_body').is(':visible')) {
  $(".accordion_body").slideUp(300);
  $(".plusminus").text('+');
}
    if( $(this).next(".accordion_body").is(':visible')){
        $(this).next(".accordion_body").slideUp(300);
        $(this).children(".plusminus").text('+');
    }else {
        $(this).next(".accordion_body").slideDown(300); 
        $(this).children(".plusminus").text('-');
    }
});
});

</script>

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>