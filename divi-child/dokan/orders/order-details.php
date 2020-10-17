<?php
global $woocommerce, $wpdb;

//$order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;
/*if ( !dokan_is_seller_has_order( dokan_get_current_user_id(), $order_id ) ) {
    echo '<div class="dokan-alert dokan-alert-danger">' . esc_html__( 'This is not yours, I swear!', 'dokan-lite' ) . '</div>';
    return;
}*/

$statuses = wc_get_order_statuses();
$order    = new WC_Order( $order_id );
$hide_customer_info = dokan_get_option( 'hide_customer_info', 'dokan_selling', 'off' );

$customer_user = absint( get_post_meta( dokan_get_prop( $order, 'id' ), '_customer_user', true ) );
if ( $customer_user && $customer_user != 0 ) {
    $customer_userdata = get_userdata( $customer_user );
    $display_name =  $customer_userdata->first_name.' '.$customer_userdata->last_name;
} else {
    $display_name = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_first_name', true ). ' '. get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_last_name', true );
}

$items = $wpdb->prefix.'woocommerce_order_items';
$join        = " INNER JOIN {$items} item ON p.ID = item.order_id";
$where       = sprintf( " item.order_id = %d AND item.order_item_type='line_item' AND", $order_id );  
$seller_id    = dokan_get_current_user_id();

$items = $wpdb->get_results( $wpdb->prepare( "SELECT item.order_item_id
            FROM {$wpdb->prefix}dokan_orders AS do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                {$where}
                p.post_status != 'trash'            
            ORDER BY p.post_date DESC
            ",
        ) );

$tbl_attributes = $wpdb->prefix."product_attributes"; 
$attributes = $wpdb->get_results( $wpdb->prepare( "SELECT title
    FROM $tbl_attributes
    WHERE
    product_id = %d ", $pid
) );

?>
<div class="modal-header">
    <h3><?php _e( 'ORDER', 'dokan' ); ?>&nbsp; #<?php echo $order_id; ?> <?php ///echo $display_name; ?></h3>
    <button type="button" class="btnclose btn-modal-close" data-dismiss="modal">&#10005;</button>
</div>

<div class="dashmodel_body">
    <?php if ( $items ) { ?>
        <div id="summary-list" class="summary-list">
            <table class="dokan-table dokan-table-striped">
                <tbody>
                    <?php
                    $total_qty = 0;
                    $colspans = 0;
                    $subtotal = 0;
                    //print_r($items);
                    foreach ($items as $item) {
                        $qty = wc_get_order_item_meta( $item->order_item_id, '_qty', true );
                        $line_total = wc_get_order_item_meta( $item->order_item_id, '_line_total', true );
                        $subtotal += $line_total; 
                        $product_attributes = wc_get_order_item_meta( $item->order_item_id, 'product_attributes', true );
                        $colspans = count($product_attributes);
                        ?>
                        <tr>
                            <td class="dokan-order-id" data-title="Order">
                            <?php 
                             //print_r($product_attributes);
                            if (!empty($product_attributes)) {
                                foreach ($product_attributes as $key => $val) {
                                    ?>   
                                     <p><strong><?php echo ucfirst($key); ?>: </strong> <?php echo ($val)?ucfirst($val):'N/A'; ?> </p>                                    
                                    <?php 
                                }
                            }
                            ?>  
                            </td>
                            <td><?php echo $qty; ?></td>
                            
                            <td><?php echo wc_price($line_total); ?></td>
                        </tr>
                        <?php 
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                             <table class="order-total">
                            <tbody>
                                <tr><td><strong>Customer Email: </strong><br> <?php echo $customer_userdata->user_email; ?></td></tr>
                                <tr>
                                    <td>
                                        <strong>Shipping Address: </strong><br>
                                    <?php
                                    if ( $order->get_formatted_shipping_address() ) {
                                    echo wp_kses_post( $order->get_formatted_shipping_address() );
                                    } else {
                                    if ( $order->get_formatted_billing_address() ) {
                                    echo wp_kses_post( $order->get_formatted_billing_address() );
                                    } else {
                                    _e( 'No shipping address set.', 'dokan-lite' );
                                    }                        
                                    }
                                    ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </td>   
                        <td></td>   
                        <td>                         
                            <table class="wc-order-totals">
                                    <tr>
                                        <td><?php _e( 'Subtotal', 'dokan' ); ?>:</td>
                                        <td class="total">
                                            <?php echo wc_price($subtotal); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php _e( 'Discount', 'dokan' ); ?>:</td>
                                        <td class="total">
                                            <?php echo wc_price( $order->get_total_discount(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?>
                                        </td>
                                    </tr>

                                    <?php do_action( 'woocommerce_admin_order_totals_after_discount', dokan_get_prop( $order, 'id' ) ); ?>

                                    <tr>
                                        <td><?php _e( 'Shipping', 'dokan' ); ?>:</td>
                                        <td class="total"><?php 
                                        $total_shipping = get_post_meta( $order_id, '_shipping_price',true);
                                        echo wc_price($total_shipping); ?>
                                    </td>
                                </tr>

                                <?php do_action( 'woocommerce_admin_order_totals_after_shipping', dokan_get_prop( $order, 'id' ) ); ?>

                                <?php if ( wc_tax_enabled() ) : ?>
                                    <?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
                                        <tr>
                                            <td><?php echo $tax->label; ?>:</td>
                                            <td class="total"><?php echo $tax->formatted_amount; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php do_action( 'woocommerce_admin_order_totals_after_tax', dokan_get_prop( $order, 'id' ) ); ?>

                                <tr>
                                    <td><?php _e( 'Order Total', 'dokan' ); ?>:</td>
                                    <td class="total">
                                        <div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
                                    </td>
                                </tr>

                                <?php do_action( 'woocommerce_admin_order_totals_after_total', dokan_get_prop( $order, 'id' ) ); ?>

                                <tr>
                                    <td class="refunded-total"><?php _e( 'Refunded', 'dokan' ); ?>:</td>
                                    <td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
                                </tr>

                                <?php do_action( 'woocommerce_admin_order_totals_after_refunded', dokan_get_prop( $order, 'id' ) ); ?>

                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
           
        </div>
    <?php } ?>
    
    <div class="customer-email">
        
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-cancel" data-dismiss="modal"><?php _e( 'CLOSE', 'dokan' ); ?></button>
    </div>
</div>