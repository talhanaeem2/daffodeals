<?php
global $woocommerce,$wpdb;

$seller_id    = dokan_get_current_user_id();
$order_search  = isset( $_GET['order_search'] ) ? sanitize_text_field( $_GET['order_search'] ) : null;
$pid  = isset( $_GET['pid'] ) ? sanitize_key( base64_decode($_GET['pid']) ) : null;
/*$order_status = isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all';
$paged        = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit        = 10;
$offset       = ( $paged - 1 ) * $limit;
$order_date   = isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : NULL;
$user_orders  = dokan_get_deal_orders( $seller_id, $order_status, $order_date, $limit, $offset, $order_search, $pid );*/
$user_orders  = dokan_get_deal_orders( $seller_id, $order_search, $pid );

$order_statuses = array(
    '-1'            => __( 'Bulk Actions', 'dokan-lite' ),
    'wc-on-hold'    => __( 'Change status to on-hold', 'dokan-lite' ),
    'wc-processing' => __( 'Change status to processing', 'dokan-lite' ),
    'wc-completed'  => __( 'Change status to completed', 'dokan-lite' )
);
$order_statuses = apply_filters( 'dokan_bulk_order_statuses', $order_statuses );

if ( $user_orders ) {
    ?>
    <form id="order-filter" method="POST" class="dokan-form-inline">
        <?php if( dokan_get_option( 'order_status_change', 'dokan_selling', 'on' ) == 'on' ) : ?>
            <div class="dokan-form-group">
                <label for="bulk-order-action-selector" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'dokan-lite' ); ?></label>

                <select name="status" id="bulk-order-action-selector" class="dokan-form-control chosen">
                    <?php foreach ( $order_statuses as $key => $value ) : ?>
                        <option class="bulk-order-status" value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $value ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="dokan-form-group">
                <?php wp_nonce_field( 'bulk_order_status_change', 'security' ); ?>
                <input type="submit" name="bulk_order_status_change" id="bulk-order-action" class="dokan-btn dokan-btn-theme" value="<?php esc_attr_e( 'Apply', 'dokan-lite' ); ?>">
            </div>
        <?php endif; ?>
        <table class="dokan-table dokan-table-striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Order', 'dokan-lite' ); ?>#</th>
                    <th><?php esc_html_e( 'Customer', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'PURCHASED', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'TRACKING', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'SHIPPED', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'dokan-lite' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($user_orders as $order) {
                    $the_order = new WC_Order( $order->order_id );
                    $order_tracking = $wpdb->prefix."order_tracking";
                    $tracking = $wpdb->get_row( $wpdb->prepare( "SELECT *
                      FROM {$order_tracking}
                      WHERE
                      order_id = %d 
                      ", $order->order_id
                  ) );
                    ?>
                    <tr>
                        <td class="dokan-order-id" data-title="<?php esc_attr_e( 'Order', 'dokan-lite' ); ?>" >
                            <?php if ( current_user_can( 'dokan_view_order' ) ): ?>
                                <?php echo '<a class="order-details" data-orderid="'.$order->order_id.'" data-orderpid="'.$pid.'" href="javascript:void(0)"><strong><i class="fa fa-external-link" aria-hidden="true"></i> ' . sprintf( __( '%s', 'dokan-lite' ), esc_attr( $the_order->get_order_number() ) ) . '</strong></a>'; ?>
                            <?php else: ?>
                                <?php echo '<strong>' . sprintf( __( '%s', 'dokan-lite' ), esc_attr( $the_order->get_order_number() ) ) . '</strong>'; ?>
                            <?php endif ?>
                        </td>
                        <td class="dokan-order-customer" data-title="<?php esc_attr_e( 'Customer', 'dokan-lite' ); ?>" >
                            <?php

                            // reset user info
                            $user_info = '';

                            if ( $the_order->get_user_id() ) {
                                $user_info = get_userdata( $the_order->get_user_id() );
                            }

                            if ( !empty( $user_info ) ) {

                                $user = '';

                                if ( $user_info->first_name || $user_info->last_name ) {
                                    $user .= esc_html( $user_info->first_name . ' ' . $user_info->last_name );
                                } else {
                                    $user .= esc_html( $user_info->display_name );
                                }

                            } else {
                                $user = __( 'Guest', 'dokan-lite' );
                            }
                            ?>
                            <a href="mailto:<?php echo $user_info->user_email; ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo esc_html( $user ); ?></a>
                        </td>
                        <td class="dokan-order-date" data-title="<?php esc_attr_e( 'Date', 'dokan-lite' ); ?>" >
                            <?php
                            if ( '0000-00-00 00:00:00' == dokan_get_date_created( $the_order ) ) {
                                $t_time = $h_time = __( 'Unpublished', 'dokan-lite' );
                            } else {
                                $t_time    = get_the_time( 'Y/m/d g:i:s A', dokan_get_prop( $the_order, 'id' ) );
                                $gmt_time  = strtotime( dokan_get_date_created( $the_order ) . ' UTC' );
                                $time_diff = current_time( 'timestamp', 1 ) - $gmt_time;

                                if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
                                    $h_time = sprintf( __( '%s ago', 'dokan-lite' ), human_time_diff( $gmt_time, current_time( 'timestamp', 1 ) ) );
                                } else {
                                    $h_time = get_the_time( 'Y/m/d', dokan_get_prop( $the_order, 'id' ) );
                                }
                            }
                            
                            echo esc_html( apply_filters( 'post_date_column_time', date('M d', strtotime(dokan_date_time_format( $h_time, true )))  , dokan_get_prop( $the_order, 'id' ) ) ) ;
                            ?>
                        </td>

                        <td class="dokan-order-tracking" data-title="<?php esc_attr_e( 'Order Tracking', 'dokan-lite' ); ?>" >
                            <a href="javascript:void(0)" data-orderid="<?php echo $order->order_id; ?>" data-tracking="<?php echo $tracking->trackingnumber; ?>" data-shipping="<?php echo $tracking->shippingdate; ?>" data-carrier="<?php echo $tracking->carrier; ?>" class="order-tracking">
                                <?php 
                                if (!empty($tracking)) {
                                   echo $tracking->carrier;
                                }else{
                                    esc_attr_e( 'Add Tracking','dokan-lite'); 
                                }
                                ?>
                            </a>  
                            <?php 
                            if (!empty($tracking)) {
                            echo ' - '.date('M d',strtotime($tracking->trackingdate));
                            }
                            ?>
                            <?php //echo $the_order->get_formatted_order_total(); ?>
                        </td>
                        <td class="dokan-order-shipped" data-title="<?php esc_attr_e( 'Order Shipped', 'dokan-lite' ); ?>" >
                            <?php 
                            if (!empty($tracking)) {
                            echo date('M d',strtotime($tracking->shippingdate));
                            }
                            ?>
                        </td>
                        <td class="dokan-order-status" data-title="<?php esc_attr_e( 'Status', 'dokan-lite' ); ?>" >
                            <?php echo '<span class="dokan-label dokan-label-' . dokan_get_order_status_class( dokan_get_prop( $the_order, 'status' ) ) . '">' . dokan_get_order_status_translated( dokan_get_prop( $the_order, 'status' ) ) . '</span>'; ?>
                        </td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>
    </form>

    <?php
    /*$order_count = dokan_get_seller_orders_number( $seller_id, $order_status );

    // if date is selected then calculate number_of_pages accordingly otherwise calculate number_of_pages =  ( total_orders / limit );
    if ( ! is_null( $order_date ) ) {
        if ( count( $user_orders ) >= $limit ) {
            $num_of_pages = ceil ( ( ( $order_count + count( $user_orders ) ) - count( $user_orders ) ) / $limit );
        } else {
            $num_of_pages = ceil( count( $user_orders ) / $limit );
        }
    } else {
        $num_of_pages = ceil( $order_count / $limit );
    }


    $base_url  = dokan_get_navigation_url( 'orders' );

    if ( $num_of_pages > 1 ) {
        echo '<div class="pagination-wrap">';
        $page_links = paginate_links( array(
            'current'   => $paged,
            'total'     => $num_of_pages,
            'base'      => $base_url. '%_%',
            'format'    => '?pagenum=%#%',
            'add_args'  => false,
            'type'      => 'array',
        ) );

        echo "<ul class='pagination'>\n\t<li>";
        echo join("</li>\n\t<li>", $page_links);
        echo "</li>\n</ul>\n";
        echo '</div>';
    }*/
    ?>

<?php } else { ?>

    <div class="dokan-error">
        <?php esc_html_e( 'No orders found', 'dokan-lite' ); ?>
    </div>

<?php } ?>

<script>
    (function($){
        $(document).ready(function(){
            $('#shippingdate').datepicker({
                dateFormat: 'yy-m-d'
            });
        });
    })(jQuery);
</script>

<div id="OrderDetailsModal" class="modal-container">
  <div class="modalbox">
    <div id="OrderDetailsModalContent" class="OrderModal model_contant">
      
    </div>
  </div>
</div>

<div id="OrderTrackingModal" class="modal-container">
  <div class="modalbox">
    <div id="OrderTrackingContent" class="OrderModal model_contant">
        <div class="modal-header">
            <h3><?php _e( 'ENTER ORDER # TRACKING INFO', 'dokan' ); ?></h3>
            <button type="button" class="btnclose btn-modal-close">&#10005;</button>
        </div>
        <div class="dashmodel_body">
            <div class="content">
                <p class="question">
                    <?php _e( 'Please enter the Tracking #, Shipment Date, and Carrier for the order you are marking as shipped.', 'dokan' ); ?>
                </p>
                <p class="question hide">
                    <?php _e( 'Please enter the Delivery Email Address and Delivery Date for the order you are marking as shipped.', 'dokan' ); ?>
                    
                </p>
                <form action="" method="post" id="frm-order-tracking">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><?php _e( 'Tracking', 'dokan' ); ?> #:</td>
                                <td>
                                    <input id="trackingNumberText" type="text" name="trackingNumber" class="form-control fieldrequired">
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Shipment Date', 'dokan' ); ?>:</td>
                                <td>
                                    <input id="shippingdate" type="text" name="ShippingDate" class="form-control fieldrequired">
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Carrier', 'dokan' ); ?>:</td>
                                <td>
                                    <select id="carrierSelect" name="carrier" class="form-control fieldrequired">
                                        <option value=" ">Select</option>
                                        <option value="FedEx">FedEx</option>
                                        <option value="FedExSmartPost">FedEx SmartPost</option>
                                        <option value="UPS">UPS</option>
                                        <option value="UPSMailInnovations">UPS Mail Innovations</option>
                                        <option value="UPSSurePost">UPS SurePost</option>
                                        <option value="USPS">USPS</option>
                                        <option value="DHLECommerce">DHL</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div id="tracking-msg"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-loader pull-left removeTracking hide">
                    <span><span><?php _e( 'Remove Tracking Number', 'dokan' ); ?></span>
                </button>
                <input type="hidden" id="track_order_id" name="track_order_id">
                <button type="button" id="saveTracking" class="btn btn-primary btn-loader"><span><?php _e( 'Save', 'dokan' ); ?></span></button>
                <button type="button" class="btn btn-cancel"><?php _e( 'Cancel', 'dokan' ); ?></button>
            </div>
        </div>
    </div>
  </div>
</div>

<div id="UploadTrackingModal" class="modal-container">
  <div class="modalbox">
    <div id="OrderTrackingContent" class="OrderModal model_contant">
        <div class="modal-header">
            <h3><?php _e( 'UPLOAD TRACKING FILE', 'dokan' ); ?></h3>
            <button type="button" class="btnclose btn-modal-close">&#10005;</button>
        </div>
        <div class="dashmodel_body">
            <div class="content">
                <p id="file-heading" class="directions">
                    <?php _e( 'Select a', 'dokan' ); ?> <strong class="pink">.csv</strong> <?php _e( 'file containing
                    order #\'s from our system, tracking numbers, and shipment dates', 'dokan' ); ?>.
                </p>
                <div class="fileupload fileupload-new form-finish">
                    <div class="alert alert-danger" id="showFileError"></div>
                    <div id="uploadTrackingInput" class="input-append input-group mara">
                        <button id="plupload-btn" class="btn btn-file btn-input btn-primary">
                            <i class="fa fa-upload"></i> &nbsp;<?php _e( 'Select File', 'dokan' ); ?>
                        </button>
                        <div class="fileupload-field">
                            <input id="input-file-upload" type="file" accept=".csv">
                        </div>
                    </div>
                </div>
                <div id="readyToSubmit" class="field-mappings hide">
                    <p class="directions">
                        <?php _e( 'Please match each field from your uploaded spreadsheet that corresponds to the label on the left', 'dokan' ); ?>.
                    </p>
                    <div class="alert alert-success hide" id="uploadSuccessCount">
                       
                    </div>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td> <?php _e( 'K&C Order', 'dokan' ); ?> #</td>
                                <td>
                                    <select id="fieldmap_orderid" class="form-control">
                                        <option value=" ">Select</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="ng-binding"><?php _e( 'Tracking', 'dokan' ); ?> #</td>
                                <td>
                                    <select id="fieldmap_trackingnumber" class="form-control">
                                        <option value=" ">Select</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Shipping Date', 'dokan' ); ?> #</td>
                                <td>
                                    <select id="fieldmap_shipping_date" class="form-control">
                                        <option value=" ">Select</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Carrier', 'dokan' ); ?></td>
                                <td>
                                    <select id="fieldmap_carrier" class="form-control">
                                        <option value="FedEx">FedEx</option>
                                        <option value="FedExSmartPost">FedEx SmartPost</option>
                                        <option value="UPS">UPS</option>
                                        <option value="UPSMailInnovations">UPS Mail Innovations</option>
                                        <option value="UPSSurePost">UPS SurePost</option>
                                        <option value="USPS">USPS</option>
                                        <option value="DHLECommerce">DHL</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="submitfieldmapping" class="btn btn-primary btn-loader hide"><span><?php _e( 'Submit Upload', 'dokan' ); ?></span></button>
                <button type="button" id="closefieldmapping" class="btn btn-cancel hide"><?php _e( 'Close', 'dokan' ); ?></button>
                <button type="button" id="cancelfieldmapping" class="btn btn-cancel"><?php _e( 'Cancel', 'dokan' ); ?></button>
            </div>
        </div>
    </div>
  </div>
</div>