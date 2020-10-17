<?php
global $woocommerce,$wpdb;
/**
 *  Dokan Dashboard Orders Template
 *
 *  Load order related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
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

        $startDate = get_post_meta($pid , '_startDate', true);
        $ships_date = get_post_meta($pid , '_ships_date', true);
        $seller_id    = dokan_get_current_user_id();
        $order_search  = isset( $_GET['order_search'] ) ? sanitize_text_field( $_GET['order_search'] ) : null;
        $user_orders  = dokan_search_orders($order_search);
        ?>
        <section class="order-download order-full order-search">
          <div class="container">
            <div class="row"> 
              <div class="col-md-12">
                <div class="row-order-serch">
                   <ul>
                     <li>
                        <form action="" id="frm_order_search" method="GET">  
                            <input type="text" class="order_search" value="<?php echo !empty($order_search)?$order_search:''; ?>" name="order_search" class="form-control" placeholder="Order Search: Customer email, Order #">
                            <span><i class="fa fa-search" aria-hidden="true"></i></span>
                        </form>
                     </li>
                   </ul>
                </div>
              </div>
              <div class="col-md-12">
                  <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Customer Name</th>
                      <th>Total Items Ordered</th>
                      <th>Tracking Number</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($user_orders)){

                    foreach ($user_orders as $order) {
                        $the_order = new WC_Order( $order->order_id );
                        //print_r();
                        $order_tracking = $wpdb->prefix."order_tracking";
                        $tracking = $wpdb->get_row( $wpdb->prepare( "SELECT *
                        FROM {$order_tracking}
                        WHERE
                        order_id = %d 
                        ", $order->order_id
                        ) );
                    ?>
                    <tr>
                      <td><a class="order-details" data-orderid="<?php echo $order->order_id; ?>" title="View Order Details" data-orderpid="<?php echo $pid; ?>" href="javascript:void(0)"><?php echo $the_order->get_order_number(); ?> <i class="fa fa-info-circle" aria-hidden="true" ></i> <i class="fa fa-refresh fa-spin orderdload" style="display: none;"></i></a></td>
                      <td><?php

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
                            <a href="mailto:<?php echo $user_info->user_email; ?>"><?php echo esc_html( $user ); ?></a>   
                        </td>
                        <td><?php echo $the_order->get_item_count( $item_type ); ?></td>
                        <td>
                            <a href="javascript:void(0)" data-orderid="<?php echo $order->order_id; ?>" data-tracking="<?php echo $tracking->trackingnumber; ?>" data-shipping="<?php echo $tracking->shippingdate; ?>" data-carrier="<?php echo $tracking->carrier; ?>" class="tracking-btn">
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
                        </td>
                    </tr>
                     <?php } ?>
                    <?php }else{ ?> 
                        <tr>
                            <td colspan="4">No order found.</td>
                        </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
              

              </div>
           
            </div>
          </div>
       </section>
</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>
<script>
    (function($){
        $(document).ready(function(){
            $('#shippingdate').datepicker({
                dateFormat: 'yy-m-d'
            });
        });
    })(jQuery);
</script>


<div class="modal fade bd-example-modal-lg" id="OrderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modalbox">
                <div id="OrderDetailsModalContent" class="OrderModal model_contant"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="OrderTrackingModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
       <div id="OrderTrackingContent" class="OrderModal model_contant">
        <div class="modal-header">
            <h3><?php _e( 'ENTER ORDER TRACKING INFO', 'dokan' ); ?></h3>
            <button type="button" class="btnclose btn-modal-close" data-dismiss="modal">&#10005;</button>
        </div>
        <div class="dashmodel_body">
            <div class="content">
                <p class="suggestion">
                    <?php _e( 'Please enter the Tracking #, Shipment Date, and Carrier for the order you are marking as shipped.', 'dokan' ); ?>
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
                <button type="button" class="btn btn-cancel" data-dismiss="modal"><?php _e( 'Cancel', 'dokan' ); ?></button>
            </div>
        </div>
    </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" id="UploadTrackingModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="OrderTrackingContent" class="OrderModal model_contant">
            <div class="modal-header">
                <h3><?php _e( 'UPLOAD TRACKING CSV FILE', 'dokan' ); ?></h3>
                <button type="button" class="btnclose btn-modal-close" data-dismiss="modal">&#10005;</button>
            </div>
            <div class="dashmodel_body">
                <div class="content">
                    <p id="file-heading" class="suggestion">
                        <?php _e( 'Select a', 'dokan' ); ?> <strong class="pink">.csv</strong> <?php _e( 'file containing
                        order # from our system, order id, tracking numbers, and shipment dates', 'dokan' ); ?>.
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
                        <p class="suggestion">
                            <?php _e( 'Please match each field from your uploaded spreadsheet that corresponds to the label on the left', 'dokan' ); ?>.
                        </p>
                        <div class="alert alert-success hide" id="uploadSuccessCount">
                           
                        </div>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td> <?php _e( 'Order', 'dokan' ); ?> #</td>
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
                    <button type="button" id="closefieldmapping" class="btn btn-cancel hide" data-dismiss="modal"><?php _e( 'Close', 'dokan' ); ?></button>
                    <button type="button" id="cancelfieldmapping" class="btn btn-cancel" data-dismiss="modal"><?php _e( 'Cancel', 'dokan' ); ?></button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
