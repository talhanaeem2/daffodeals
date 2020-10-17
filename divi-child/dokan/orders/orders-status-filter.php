<?php
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Dashboard order status filter template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
global $wp_query;

if ( isset( $_GET['order_id'] ) ) {
    ?>
        <a href="<?php echo esc_url( dokan_get_navigation_url( 'orders' ) ) ; ?>" class="dokan-btn"><?php esc_html_e( '&larr; Orders', 'dokan-lite' ); ?></a>
    <?php
} else {

	$orders_url = dokan_get_navigation_url( 'orders' );
    $pid  = isset( $_GET['pid'] ) ? sanitize_key( base64_decode($_GET['pid']) ) : null;
    /*dokan_order_listing_status_filter();*/
    ?>
    <div class="order-sidebar subsidebar">
        <h3><?php esc_html_e( 'Orders', 'dokan-lite' ); ?></h3>        
        <ul class="list-inline order-statuses-filter">
            <li <?php echo isset( $wp_query->query_vars['orders'] ) ? ' class="active"' : ''; ?>>
                <a href="<?php echo add_query_arg('pid', base64_encode($pid), esc_url( dokan_get_navigation_url('orders'))); ?>"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php esc_html_e( 'Orders', 'dokan-lite' ); ?></a>
            </li>
            <li <?php echo isset( $wp_query->query_vars['orders-summary'] ) ? ' class="active"' : ''; ?>>
                <a href="<?php echo add_query_arg('pid', base64_encode($pid), esc_url( dokan_get_navigation_url('orders-summary'))); ?>"><i class="fa fa-file-o" aria-hidden="true"></i> <?php esc_html_e( 'Summary', 'dokan-lite' ); ?></a>
            </li>
            <li <?php echo isset( $wp_query->query_vars['orders-download'] ) ? ' class="active"' : ''; ?>>
                <a href="<?php echo add_query_arg('pid', base64_encode($pid), esc_url( dokan_get_navigation_url('orders-download'))); ?>"><i class="fa fa-download" aria-hidden="true"></i> <?php esc_html_e( 'Downloads', 'dokan-lite' ); ?></a>
            </li>
        </ul>
    </div>    	
    <?php 
}
