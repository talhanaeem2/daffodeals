<?php 
global $wpdb;
if (!empty($orders)){
    $first_order = new WC_Order( $orders[0]->order_id );
  ?>
  <div class="order-search">
      <div class="customer-details">
          <h4><?php echo $first_order->get_billing_first_name().'&nbsp;'.$first_order->get_billing_last_name(); ?></h4>
          <p><?php echo $first_order->get_billing_email(); ?></p>
          <p><?php echo (count($orders)>1)?count($orders).' orders':count($orders).' order'; ?></p>
      </div>
      <?php 
      foreach ($orders as $order) {
          $the_order = new WC_Order( $order->order_id );
          $items = $the_order->get_items();
          ?>
          <div class="order">
            <div class="head">
                <span class="fldate">#<?php echo $order->order_id; ?> - <?php echo date('M d, Y',strtotime($the_order->get_date_created())); ?></span>
                <div class="head-right">
                  <span class="frtar">
                    <?php echo wc_price($the_order->get_total()); ?>
                  </span>
                  <i class="fa fa-chevron-right" aria-hidden="true"></i>
                </div>
            </div>
            <div class="order-details hide">
          <?php 
          //print_r($items);
          if (!empty($items)){
              //print_r($items);
              $i = 0;
              foreach ($items as $item_id => $item) {
                //print_r($item);
                $item_name  = $item->get_name();
                $product_id = $item->get_product_id();
                $line_total = $item->get_total();
                $quantity   = $item->get_quantity(); 
                $product_attributes = wc_get_order_item_meta( $item_id, 'product_attributes', true );

                $tbl_attributes = $wpdb->prefix."product_attributes"; 
                $attributes = $wpdb->get_results( $wpdb->prepare( "SELECT title
                FROM $tbl_attributes
                WHERE
                product_id = %d ", $product_id
                ) );

                $order_tracking = $wpdb->prefix."order_tracking";
                $ordership = $wpdb->get_row( $wpdb->prepare( "SELECT trackingnumber, carrier
                FROM {$order_tracking}
                WHERE
                order_id = %d 
                ", $order->order_id
                ) );
                ?>
                <div class="order-content">
                    <?php if($i == 0){ ?>
                    <div class="order-header">
                      <i class="fa fa-chevron-left" aria-hidden="true"></i>
                      <h3><?php echo date('M d, Y',strtotime($the_order->get_date_created())); ?></h3>
                    </div>
                    <?php } ?>
                    <div class="order-body">
                     
                      <div class="padded-content order-summary">
                        <?php echo get_the_post_thumbnail($product_id, 'thumbnail'); ?>
                        <div class="info">
                          <p>
                            <span class="txt-pink"><?php echo wc_price($line_total); ?></span>
                            -
                            <a href="<?php echo dokan_edit_product_url($product_id); ?>">
                              <?php echo $item_name; ?>
                            </a>
                          </p>
                        </div>
                      </div>
                      <?php if($i == 0){ ?>
                      <div class="shipping-info">
                          <?php if(!empty($ordership->trackingnumber)){ ?>
                            <h4><?php echo $ordership->trackingnumber; ?> (<?php echo $ordership->carrier; ?>)</h4>
                          <?php } ?>
                          <span>
                              <strong>SHIPPED</strong> - 
                              <?php 
                              $ships_date =  get_post_meta($product_id, '_ships_date', true); 
                              if (!empty($ships_date)) {
                                echo date('m/d/y', strtotime($ships_date));
                              }                              
                              ?>
                          </span>
                      </div>

                      <div class="customer-info-section">
                        <div class="customer-head">
                          <p>ORDER #</p>
                          <p>CUSTOMER</p>
                          <p>EMAIL</p>
                        </div>
                        <div class="customer-info">
                          <p><?php echo $the_order->order_id; ?></p>
                          <p><?php echo $the_order->get_billing_first_name().'&nbsp;'.$the_order->get_billing_last_name(); ?></p>
                          <p><?php echo $the_order->get_billing_email(); ?></p>
                        </div>
                      </div>
                      <?php } ?>

                      <?php if (!empty($attributes)) { ?>
                       
                        <div class="options">
                          <article class="padded-content">
                            <div class="details">         
                              <div class="txt-grayLight">
                                <p>QTY</p>
                                 <?php 
                                if (!empty($attributes)) {
                                       foreach ($attributes as $key => $attr) {
                                        ?>
                                        <p><?php echo $attr->title; ?></p>
                                        <?php 
                                    }
                                }
                                ?>
                              </div>
                              <div class="tar-txt-gray">
                                <p><?php echo $quantity; ?></p>
                                <?php 
                                if (!empty($product_attributes)) {
                                  foreach ($product_attributes as $val) {
                                    ?>
                                    <p><?php echo ($val)?ucfirst($val):'N/A'; ?></p>
                                    <?php 
                                  }
                                }
                                ?>
                              </div>
                            </div>
                          </article>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                <?php 
             $i++;  }
          }
          ?>
            <div class="order-footer">
            <div class="order-total">
              <?php
              if ( $totals = $the_order->get_order_item_totals() ) {
                foreach ( $totals as $total ) {
                  ?>
                  <p>
                    <label><?php echo wp_kses_data( $total['label'] ); ?></label>
                    <span><?php echo wp_kses_post( $total['value']); ?></span>
                  </p>
                  <?php
                }
              }
              ?>
            </div>           
            <div class="footer-customer-info">
               <p class="ng-binding"><?php echo $first_order->get_billing_first_name().'&nbsp;'.$first_order->get_billing_last_name(); ?></p>
               <p class="ng-binding"><?php echo $first_order->get_billing_address_1(); ?></p>
               <p class="ng-binding"><?php echo $first_order->get_billing_address_2(); ?></p>
               <p class="ng-binding"><?php echo $first_order->get_billing_city(); ?>, <?php echo $first_order->get_billing_country(); ?> <?php echo $first_order->get_billing_postcode(); ?></p>
            </div>
              <div class="padded-content">
                <button type="button" class="btn btn-default">Close
                </button>
              </div>
            </div>
          </div>
          </div>
        <?php 
      }
      ?>
  </div>
  <?php 
}else{
  ?>
  <div class="error">
    <h4>Error processing your request.</h4>
    <p> Please try again </p>
  </div>
  <?php
}
?>