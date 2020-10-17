<?php
global $wpdb;
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Main Dashboard template for Fron-end
 *
 *  @since 2.4
 *
 *  @package dokan
 */
$user_id = get_current_user_id();
?>
<?php do_action( 'dokan_dashboard_wrap_start' ); ?>
<style type="text/css">
.wc_sparkline {
    width: 4em;
    height: 2em;
    display: block;
    float: right;
    position: absolute;
    right: 65px;
    top: 50%;
    margin-right: 12px;
    margin-top: -1.25em;
}
</style>

<script src='https://daffodeals.com/wp-content/plugins/woocommerce/assets/js/admin/reports.min.js?ver=3.9.1'></script>
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
        ?>

        <div class="dokan-dashboard-content">

            <?php

                /**
                 *  dokan_dashboard_content_before hook
                 *
                 *  @hooked show_seller_dashboard_notice
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_dashboard_content_inside_before' );
            ?>

            <article class="dashboard-content-area">

                <?php

                    /**
                     *  dokan_dashboard_before_widgets hook
                     *
                     *  @hooked dokan_show_profile_progressbar
                     *
                     *  @since 2.4
                     */
                    //do_action( 'dokan_dashboard_before_widgets' );

                ?>

                <section class="sale-section">
                    <div class="container">
                       <div class="row">
                       <div class="col-12">
                       <div class="sale-report" id="woocommerce_dashboard_status">
                          <h1>Sales and Revenue Report</h1>
                          <?php 

                          global $wpdb;

                          $query            = array();
                          $query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as amount
                          FROM {$wpdb->posts} as posts";
                          $query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
                          $query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
                          $query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";

                          $query['join']   .= "INNER JOIN {$wpdb->prefix}dokan_orders AS do ON do.order_id = posts.ID ";

                          $query['where']   = "WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) ";
                          $query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", array( 'completed', 'processing', 'on-hold' ) ) . "' ) ";
                          $query['where']  .= "AND order_item_meta.meta_key = '_line_subtotal' ";
                          $query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
                          $query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "' ";
                          $query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
                          $query['where']  .= "AND do.seller_id = $user_id ";
                          $query['limits']  = 'LIMIT 1';
                          $top_sell = $wpdb->get_row( implode( ' ', $query ) );


                          $query            = array();
                          $query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
                          FROM {$wpdb->posts} as posts";
                          $query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
                          $query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
                          $query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";

                          $query['join']   .= "INNER JOIN {$wpdb->prefix}dokan_orders AS do ON do.order_id = posts.ID ";

                          $query['where']   = "WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) ";
                          $query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", array( 'completed', 'processing', 'on-hold' ) ) . "' ) ";
                          $query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
                          $query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
                          $query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "' ";
                          $query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
                          $query['where']  .= "AND do.seller_id = $user_id ";

                          $query['groupby'] = 'GROUP BY product_id';
                          $query['orderby'] = 'ORDER BY qty DESC';
                          $query['limits']  = 'LIMIT 1';
                          $top_seller = $wpdb->get_row( implode( ' ', $query ) );

                          $awaiting_orders = $wpdb->get_var("SELECT COUNT(posts.ID) as orders FROM {$wpdb->posts} as posts INNER JOIN {$wpdb->prefix}dokan_orders AS do ON do.order_id = posts.ID WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) AND posts.post_status IN ( 'wc-" . implode( "','wc-", array( 'completed', 'processing', 'on-hold' ) ) . "' ) AND do.seller_id = $user_id ");

                          $shipped = $wpdb->get_var("SELECT COUNT(posts.ID) as orders FROM {$wpdb->posts} as posts INNER JOIN {$wpdb->prefix}dokan_orders AS do ON do.order_id = posts.ID INNER JOIN {$wpdb->prefix}order_tracking AS ot ON ot.order_id = posts.ID  WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) AND posts.post_status IN ( 'wc-" . implode( "','wc-", array( 'completed', 'processing', 'on-hold' ) ) . "' ) AND do.seller_id = $user_id AND ot.trackingnumber != '' ");
                          
                          //print_r($top_sell);
                          $netsell = !empty($top_sell->amount)?wc_price($top_sell->amount):wc_price(0);

                          $awaiting_orders = $awaiting_orders-$shipped;

                         // echo WC_ABSPATH;
                        include_once WC_ABSPATH . 'includes/admin/reports/class-wc-admin-report.php';

                       $reports = new WC_Admin_Report();
                       // echo WC_ABSPATH;

                          ?>
                          <ul class="wc_status_list">
                             <li class="sales-this-month">
                                <div class="left-sale">
                                   <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/net-sale.png" alt="">
                                   <h3><?php echo $netsell; ?></h3>
                                   <p>Net Sales this Month</p>
                                </div>  
                                <div class="graph-right">
                                  <?php echo $reports->sales_sparkline( '', max( 7, date( 'd', current_time( 'timestamp' ) ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
                                  
                                </div>                
                             </li>
                              
                             <?php if(!empty($top_seller->qty)){ ?>
                             <li>
                                <div class="left-sale">
                                   <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/wall-iocn.png" alt="">
                                   <h3><?php echo get_the_title( $top_seller->product_id ); ?></h3>
                                   <p>Top Seller this Month (Sold <?php echo $top_seller->qty; ?>)</p>
                                </div> 

                               <div class="graph-right">
                                   <?php echo $reports->sales_sparkline( $top_seller->product_id, max( 7, date( 'd', current_time( 'timestamp' ) ) ), 'count' ); ?>
                                </div>       
                             </li>
                            <?php } ?>

                             <li class="half-item">
                                <div class="left-sale">
                                   <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/dots-icon.png" alt="">
                                   <h3><?php echo ($awaiting_orders>1)?$awaiting_orders.' Orders':$awaiting_orders.' Order'; ?></h3>
                                   <p>Awaiting Shipment</p>
                                </div>                  
                             </li>
                             <li class="half-item">
                                <div class="left-sale">
                                   <img src="<?php echo get_stylesheet_directory_uri(); ?>/fpcustomization/images/line-icon.png" alt="">
                                   <h3><?php echo ($shipped>1)?$shipped.' Orders':$shipped.' Order'; ?></h3>
                                   <p>Shipped</p>
                                </div>                  
                             </li>
                          </ul>
                       </div>
                       </div>
                       </div>
                    </div>
                    
                 </section>

            </article><!-- .dashboard-content-area -->

             <?php

                /**
                 *  dokan_dashboard_content_inside_after hook
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_dashboard_content_inside_after' );
            ?>


        </div><!-- .dokan-dashboard-content -->

        <?php

            /**
             *  dokan_dashboard_content_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_after' );
        ?>
       
    </div><!-- .dokan-dashboard-wrap -->
<?php do_action( 'dokan_dashboard_wrap_end' ); ?>
<?php //do_action( 'add_change_password_popup' ); ?>

