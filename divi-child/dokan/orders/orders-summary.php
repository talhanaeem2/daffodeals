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
$order_search  = isset( $_GET['order_search'] ) ? sanitize_text_field($_GET['order_search'])  : null;
$paged        = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit        = 10;
$offset       = ( $paged - 1 ) * $limit;

$pid  = isset( $_GET['pid'] ) ? sanitize_text_field( base64_decode($_GET['pid']) ) : null;
$startDate = get_post_meta($pid , '_startDate', true);
$ships_date = get_post_meta($pid , '_ships_date', true);

$user_orders_summary  = dokan_get_deal_orders_summary( $seller_id, $order_search, $pid );
$product_title = get_the_title($pid);
//print_r($user_orders_summary);
$tbl_attributes = $wpdb->prefix."product_attributes"; 
$attributes = $wpdb->get_results( $wpdb->prepare( "SELECT title
    FROM $tbl_attributes
    WHERE
    product_id = %d ", $pid
) );

?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

    <div class="dokan-dashboard-wrap">

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

        <div class="dokan-dashboard-content dokan-orders-content">

            <?php

                /**
                 *  dokan_order_content_inside_before hook
                 *
                 *  @hooked show_seller_enable_message
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_order_content_inside_before' );

               
            ?>


            <article class="dokan-orders-area">

                <?php
                    dokan_get_template_part( 'orders/orders-status-filter' );
                    /**
                     *  dokan_order_inside_content Hook
                     *
                     *  @hooked dokan_order_listing_status_filter
                     *  @hooked dokan_order_main_content
                     *
                     *  @since 2.4
                     */
                    //do_action( 'dokan_order_inside_content' );
               
                ?>
                <div class="order-summary">
                    <div class="deal-title">
                        <h3><?php echo get_the_title($pid); ?></h3>
                        <span><?php esc_attr_e( 'Deal Ran', 'dokan-lite' ); ?> <?php echo date('M d',strtotime($startDate)); ?> - <?php echo date('M d',strtotime("+3 day", strtotime($startDate))); ?>&nbsp; | &nbsp;<?php esc_attr_e( 'Ships by', 'dokan-lite' ); ?> - <?php echo date('M d',strtotime($ships_date)); ?>&nbsp;  |  &nbsp;<a href="<?php echo dd_edit_product_url($pid ); ?>"><?php esc_attr_e( 'DETAILS', 'dokan-lite' ); ?></a> | <a target="_blank" href="<?php echo get_permalink($pid); ?>"><?php esc_attr_e( 'SEE ON KATE&CREW', 'dokan-lite' ); ?></a></span>
                    </div>
                    <div class="dokan-order-filter-serach">
                        <button id="PrintOrderSummary" class="upload-tracking dokan-left"><i class="fa fa-print" aria-hidden="true"></i> <?php esc_attr_e( 'PRINT', 'dokan-lite' ); ?></button>
                        <form action="" method="GET" class="dokan-right">
                            <div class="dokan-form-group">
                                <input type="hidden" name="pid" value="<?php echo base64_encode($pid); ?>">
                                <input type="text" class="search" name="order_search" id="" placeholder="<?php //esc_attr_e( 'Filter by Date', 'dokan-lite' ); ?>" value="<?php echo esc_attr( $order_search ); ?>">
                                <input type="submit" name="dokan_order_filter" class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Search', 'dokan-lite' ); ?>">
                                <input type="hidden" name="order_status" value="<?php echo  esc_attr( $order_status ); ?>">

                            </div>
                        </form>
                    </div>
                    <?php if ( $user_orders_summary ) { ?>
                        <div id="summary-list" class="summary-list">
                            <table class="dokan-table dokan-table-striped">
                                <thead>
                                    <tr>
                                        <?php 
                                        if (!empty($attributes)) {
                                               foreach ($attributes as $key => $attr) {
                                                ?>
                                                <th><?php echo $attr->title; ?></th>
                                                <?php 
                                            }
                                        }else{
                                            ?>
                                           <th><?php _e('Product', 'dokan-lite'); ?></th>
                                            <?php 
                                        } 
                                        ?>
                                        <th><?php _e('QUANTITY', 'dokan-lite'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_qty = 0;
                                    $colspans = 0;
                                    foreach ($user_orders_summary as $order) {
                                        $qty = wc_get_order_item_meta( $order->order_item_id, '_qty', true );
                                        $product_attributes = wc_get_order_item_meta( $order->order_item_id, 'product_attributes', true );
                                        $colspans = !empty($product_attributes)?count($product_attributes):0;
                                        $total_qty += $qty;
                                        ?>
                                            <tr>
                                                <?php 
                                                if (!empty($product_attributes)) {
                                                    foreach ($product_attributes as $val) {
                                                        ?>
                                                        <td class="dokan-order-id" data-title="Order">
                                                            <?php echo ($val)?ucfirst($val):'N/A'; ?>
                                                        </td>
                                                        <?php 
                                                    }
                                                }else{
                                                ?>
                                                    <td class="dokan-order-id" data-title="Order">
                                                    <?php echo $product_title; ?>
                                                    </td>
                                                <?php  
                                                }
                                                ?>
                                                <td><?php echo $qty; ?></td>
                                            </tr>
                                        <?php 
                                    }
                                    ?>
                                    <td style="text-align: left" colspan="<?php echo $colspans; ?>"><strong><?php _e('TOTAL', 'dokan-lite'); ?></strong></td>
                                    <td><strong><?php echo $total_qty; ?></strong></td>
                                </tbody>
                            </table>
                        </div>
                    <?php }else{ ?>
                        <div class="dokan-error">
                            <?php esc_html_e( 'No orders summary found', 'dokan-lite' ); ?>
                        </div>
                    <?php } ?>
                </div>
            </article>


            <?php

                /**
                 *  dokan_order_content_inside_after hook
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_order_content_inside_after' );
            ?>

        </div> <!-- #primary .content-area -->

        <?php

            /**
             *  dokan_dashboard_content_after hook
             *  dokan_order_content_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_after' );
            do_action( 'dokan_order_content_after' );

        ?>

    </div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); 


?>