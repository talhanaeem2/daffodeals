<?php
    global $post,$wpdb;
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-circle-progress/1.1.3/circle-progress.min.js"></script>
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
        do_action( 'dokan_dashboard_content_inside_before' );
        ?>
         <section class="deal-section">
              <div class="container">
                <?php  //SUB MENU CALL HERE.
                    dokan_get_template_part( 'products/prduct-sub-menu' ); ?>
                
                 <div class="center-filter">
                   <ul>
                     <li>
                       <div class="filter-deal">
                          <label>Filter Deal Status: </label>
                           <select name="filter" onchange="window.location.href=''+'?filter='+this.value">
                            <option value="">All</option>
                            <option <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'declined')?'selected':''; ?> value="declined">Declined</option>
                            <option <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'shipping')?'selected':''; ?> value="shipping">Shipping</option>
                            <option <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'ended')?'selected':''; ?> value="ended">Complete</option>
                          </select>
                        </div> 
                
                     </li>
                     <li>
                       <div class="order_search_section">
                          <form action="<?php echo esc_url( dokan_get_navigation_url( 'order-search' ) ); ?>" method="GET" id="frm_order_search">Order Search  <input type="text" id="order_search" placeholder="Customer email, Order #" name="order_search"></form>
                        </div>
                     </li>
                   </ul>
                 </div>

                <div class="product-list-deal">
                    <?php dokan_product_dashboard_errors(); ?>
					<table id="myTable"> 
						<thead>
							<th style="text-align:center">Featured Image</th>
							<th style="text-align:center">Deal Details</th>
							<th style="text-align:center">Inventory</th>
							<th></th>
						</thead>
						<tbody>
                    <?php //dokan_product_listing_filter(); 
                    $pagenum       = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;                    
                    $get_data      = wp_unslash( $_GET );

                    $order = 'ASC';
                    
                    $args = array(
                        'posts_per_page' => 15,                        
                        'meta_key' => '_startDate',
                        'paged'          => $pagenum,
                        'post_status'    => array( 'ended', 'declined', 'shipping'),
                        'author'         => get_current_user_id(),
                        'order'         => $order,
                        'orderby'       => 'meta_value post_status',
                    );

                    if (current_user_can( 'administrator' ) ) {
                        unset($args['author']);
                    } 

                    if (isset($_GET['filter']) && !empty($_GET['filter'])) {
                        unset($args['post_status']);
                        $args['post_status'] = array($_GET['filter']);
                    }

                    if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {                        
                        $args['meta_query'][] = array(
                            'key'       => '_startDate',
                            'value'     => $_GET['filter_date'],
                            'compare'   => '=',
                        );
                    }

                    if ( isset( $get_data['product_search_name']) && !empty( $get_data['product_search_name'] ) ) {
                        $args['s'] = $get_data['product_search_name'];
                    }

                    $original_post = $post;
                    $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );

                    $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                    $base_url = dokan_get_navigation_url('deals-ended');

                    $seller_deal_status = seller_deal_status();

                    if ( $product_query->have_posts() ) {

                        while ($product_query->have_posts()) {

                            $product_query->the_post();

                            $row_actions = dokan_product_get_row_action( $post );
                            $tr_class = ( $post->post_status == 'pending' ) ? 'danger' : '';
                            $view_class = ($post->post_status == 'pending' ) ? 'dokan-hide' : '';
                            $product = wc_get_product( $post->ID );
                            $author_id = $post->post_author;
                            $store_user = dokan()->vendor->get($author_id);

                            $row_args = array(
                                'post' => $post,
                                'product' => $product,
                                'tr_class' => $tr_class,
                                'row_actions' => $row_actions,
                            );

                            $img_kses = apply_filters( 'dokan_product_image_attributes', array(
                                'img' => array(
                                    'alt'    => array(),
                                    'class'  => array(),
                                    'height' => array(),
                                    'src'    => array(),
                                    'width'  => array(),
                                ),
                            ) );

                            $row_actions_kses = apply_filters( 'dokan_row_actions_kses', array(
                                'span' => array(
                                    'class' => array(),
                                ),
                                'a' => array(
                                    'href'    => array(),
                                    'onclick' => array(),
                                ),
                            ) );

                            $price_kses = apply_filters( 'dokan_price_kses', array(
                                'span' => array(
                                    'class' => array()
                                ),
                            ) );

                            $sold = dd_get_deal_orders($post->ID);
                            $shipped = dd_get_deal_shipped_orders($post->ID);
                            $totalQtyAvailable = get_post_meta($post->ID, '_totalQtyAvailable', true);
                            ?>
                     <tr>
                       <td>
                        <div class="product-imge">
                           <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
                                <?php echo wp_kses( $product->get_image(), $img_kses ); ?>
                            <?php else: ?>
                                <?php echo wp_kses( $product->get_image(), $img_kses ); ?>
                            <?php endif ?>
                        </div>
                       </td>
                       <td>                 
                          <h3 class="title">
                            <?php   $vendor_id = get_post_field( 'post_author', get_the_id() );
									$vendor = new WP_User($vendor_id);
									$store_info = dokan_get_store_info( $vendor->ID );
								if ( current_user_can( 'dokan_edit_product' ) ): ?>
                            <p class="f-w"><?php echo esc_html( $product->get_title() ); ?></p>
                            <?php else: ?>
                            <p><a href=""><?php echo esc_html( $product->get_title() ); ?></a></p>
                            <?php endif ?>
							  <p class="f-s">Store Name: <?php echo $store_info['store_name']; ?></p>
                            <?php 
                            $startDate = get_post_meta($post->ID, '_startDate', true);
							//$endDate = get_post_meta($post->ID, '_endDate', true);
							$ships_date =  get_post_meta($post->ID, '_ships_date', true);
							$testDate = new DateTime($ships_date);
							$testDate->modify('-1 day');
							$endDate = $testDate->format("m/d/y");
                            if (!empty($startDate)) {
                              $startDate = date('m/d/y', strtotime($startDate));
                            } 
                            if (!empty($endDate)) {
                              $endDate = date('m/d/y', strtotime($endDate));
                            } 
                            if (!empty($ships_date)) {
                              $ships_date = date('M d', strtotime($ships_date));
								//$ships_date1 = date('m/d/y', strtotime($ships_date));
                            }
                            ?>
                            <br><?php echo $startDate; ?> - <?php echo $endDate; ?>
                            <br>   Ships by <?php echo $ships_date; ?></h3>
                       </td>
                       <td><div class="sold-ty circle-progress" id="circle-<?php echo $post->ID; ?>"> <strong><?php echo !empty($sold)?$sold.'/':'0/'; echo $totalQtyAvailable; ?></strong> <span class="whatsold" dealid="<?php echo $post->ID; ?>">What's sold?</span></div>
                        <script >  
                        $('#circle-<?php echo $post->ID; ?>').circleProgress({
                        startAngle: -1.560,
                        size: 100,
                        value : <?php echo $sold/$totalQtyAvailable; ?>,
                        fill: {
                        color: '#ffc101'
                        }
                        });
                        </script>
                         <div class="sold-item">Units Sold</div>
                       </td>
                       <td class="action-btn">
                         <a href="<?php echo dokan_edit_product_url( $post->ID ); ?>">Details</a>
                          <?php 
                          if (!empty($sold)) {
                          ?>
                          <a href="<?php echo add_query_arg('pid', base64_encode($post->ID), esc_url( dokan_get_navigation_url('orders'))); ?>">Orders</a>
                          <?php /* <a target="_blank" href="<?php echo add_query_arg('pid', base64_encode($post->ID), esc_url( dokan_get_navigation_url('fulfillment'))); ?>">Fulfillment</a> */ ?>
                          <?php } 


                          if ($post->post_status == 'ended' || $post->post_status == 'shipping') {
                          ?>
                            <a href="<?php echo add_query_arg('rebook', base64_encode($post->ID), esc_url( dokan_get_navigation_url('new-product'))); ?>">Rebook</a>
                          <?php } 


                          if ($post->post_status == 'declined') {
                            echo '<a class="gray-btn">'.ucfirst($post->post_status).'</a>';
                          }

                          if (!empty($sold)) {
                            if (!empty($shipped) && $sold == $shipped) {
                              echo '<a class="green-bg">Shipped</a>';
                            }else{
                              echo '<a class="red-bg">Not Shipped</a>';
                            }
                          }
                          ?>  
                       </td>
                     </tr>
                     <?php
                        }

                    } else {
                    ?>
                        <tr>
                            <td colspan="7"><?php esc_html_e( 'No deal found', 'dokan-lite' ); ?></td>
                        </tr>
                    <?php } ?>
					</tbody>
                   </table>
                   <?php 
                    if ( $product_query->max_num_pages > 1 ) {
                        echo '<div class="pagination-wrap">';
                        $page_links = paginate_links( array(
                            'current'   => $pagenum,
                            'total'     => $product_query->max_num_pages,
                            'base'      => $base_url. '%_%',
                            'format'    => '?pagenum=%#%',
                            'add_args'  => false,
                            'type'      => 'array',
                            'prev_text' => __( '&laquo; Previous', 'dokan-lite' ),
                            'next_text' => __( 'Next &raquo;', 'dokan-lite' )
                        ) );

                        echo '<ul class="pagination"><li>';
                        echo join("</li>\n\t<li>", $page_links ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
                        echo "</li>\n</ul>\n";
                        echo '</div>';
                    }
                    ?>
                     <?php
                    wp_reset_postdata();
                    ?>
                </div>       
              </div>
           </section>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>

<!-- Modal -->
<div class="modal fade" id="whatsoldmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>