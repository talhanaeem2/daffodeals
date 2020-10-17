<?php
/**
 * Dokan Dashboard Template
 *
 * Dokan Dashboard Order Main Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<?php
$user_string = '';
$user_id     = '';

if ( ! empty( $_GET['customer_id'] ) ) { // WPCS: input var ok.
    $user_id = absint( $_GET['customer_id'] ); // WPCS: input var ok, sanitization ok.
    $user    = get_user_by( 'id', $user_id );

    $user_string = sprintf(
        /* translators: 1: user display name 2: user ID 3: user email */
        esc_html__( '%1$s (#%2$s)', 'dokan-lite' ),
        $user->display_name,
        absint( $user->ID )
    );
}

$filter_date  = isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : '';
$order_search  = isset( $_GET['order_search'] ) ? sanitize_key( $_GET['order_search'] ) : '';
$order_status = isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all';
$pid  = isset( $_GET['pid'] ) ? sanitize_key( base64_decode($_GET['pid']) ) : null;
$startDate = get_post_meta($pid , '_startDate', true);
$ships_date = get_post_meta($pid , '_ships_date', true);
?>
<div class="deal-title">
    <h3><?php echo get_the_title($pid); ?></h3>
    <span><?php esc_attr_e( 'Deal Ran', 'dokan-lite' ); ?> <?php echo date('M d',strtotime($startDate)); ?> - <?php echo date('M d',strtotime("+3 day", strtotime($startDate))); ?>&nbsp; | &nbsp;<?php esc_attr_e( 'Ships by', 'dokan-lite' ); ?> - <?php echo date('M d',strtotime($ships_date)); ?>&nbsp;  |  &nbsp;<a href="<?php echo dd_edit_product_url($pid ); ?>"><?php esc_attr_e( 'DETAILS', 'dokan-lite' ); ?></a> | <a target="_blank" href="<?php echo get_permalink($pid); ?>"><?php esc_attr_e( 'SEE ON KATE&CREW', 'dokan-lite' ); ?></a></span>
</div>
<div class="dokan-order-filter-serach">
    <button class="upload-tracking dokan-left"><i class="fa fa-upload" aria-hidden="true"></i> <?php esc_attr_e( 'UPLOAD TRACKING', 'dokan-lite' ); ?></button>
    <form action="" method="GET" class="dokan-right">
        <div class="dokan-form-group">
             <input type="hidden" name="pid" value="<?php echo base64_encode($pid); ?>">
            <input type="text" class="search" name="order_search" id="" placeholder="<?php //esc_attr_e( 'Filter by Date', 'dokan-lite' ); ?>" value="<?php echo esc_attr( $order_search ); ?>">
            <input type="submit" name="dokan_order_filter" class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Search', 'dokan-lite' ); ?>">
            <input type="hidden" name="order_status" value="<?php echo  esc_attr( $order_status ); ?>">
           
        </div>
    </form>

    <form action="" method="POST" class="dokan-right hide">
        <div class="dokan-form-group">
            <?php
                wp_nonce_field( 'dokan_vendor_order_export_action', 'dokan_vendor_order_export_nonce' );
            ?>
            <input type="submit" name="dokan_order_export_all"  class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Export All', 'dokan-lite' ); ?>">
            <input type="submit" name="dokan_order_export_filtered"  class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Export Filtered', 'dokan-lite' ); ?>">
            <input type="hidden" name="order_date" value="<?php echo esc_attr( $filter_date ); ?>">
            <input type="hidden" name="order_status" value="<?php echo esc_attr( $order_status ); ?>">
        </div>
    </form>

    <div class="dokan-clearfix"></div>
</div>
