<?php
    global $post;
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
        ?>

        <div class="dokan-dashboard-content dokan-product-listing">

            <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_before' );
            do_action( 'dokan_before_listing_product' );

            //SUB MENU CALL HERE.
            dokan_get_template_part( 'products/prduct-sub-menu' );
            ?>
			

            <article class="dokan-product-listing-area innerarea">


                <?php dokan_product_dashboard_errors(); ?>

                <div class="dokan-w12 dokan-deal-top-section">
                    <?php //dokan_product_listing_filter(); 
                    $pagenum       = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                    $post_statuses = array( 'publish', 'draft', 'pending', 'future' );
                    $get_data      = wp_unslash( $_GET );

                    $args = array(
                        'posts_per_page' => 15,
                        'paged'          => $pagenum,
                        'author'         => get_current_user_id(),
                        'post_status'    => array( 'ended', 'declined'),
                    );                    
                    
                    if (current_user_can( 'administrator' ) ) {
                        unset($args['author']);
                    }

                    if ( isset( $get_data['product_search_name']) && !empty( $get_data['product_search_name'] ) ) {
                        $args['s'] = $get_data['product_search_name'];
                    }
                    $seller_deal_status = seller_deal_status();
                    $original_post = $post;
                    $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );
                    ?>
                    <div class="deal-top-left">
                        <div class="deal-dropdown">
                            <button id="proposalSubStatusFilter" data-toggle="dropdown" class="dropdown-toggle deal-status">
                                <span><?php if(isset($_GET['filter']) && !empty($_GET['filter'])){ echo $seller_deal_status[$_GET['filter']];  }else{  esc_html_e( 'All', 'dokan-lite' );  } ?></span> &nbsp;<i class="fa fa-chevron-down" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="<?php if(!isset($_GET['filter'])){ echo 'current-filter'; } ?>">
                                    <a href="<?php echo dokan_get_navigation_url( 'products'); ?>"><?php esc_html_e( 'All', 'dokan-lite' ); ?></a>
                                </li>
                                <li class="<?php if(isset($_GET['filter']) && $_GET['filter'] == 'ended'){ echo 'current-filter'; } ?>">
                                    <a href="<?php echo add_query_arg('filter', 'ended', dokan_get_navigation_url( 'products')); ?>"><?php esc_html_e( 'ENDED', 'dokan-lite' ); ?></a>
                                </li>
                                <?php /* <li class="<?php if(isset($_GET['filter']) && $_GET['filter'] == 'cencelled'){ echo 'current-filter'; } ?>">
                                    <a href="<?php echo add_query_arg('filter', 'cencelled', dokan_get_navigation_url( 'products')); ?>"><?php esc_html_e( 'CANCELLED', 'dokan-lite' ); ?></a>
                                </li>*/ ?>
                                <li class="<?php if(isset($_GET['filter']) && $_GET['filter'] == 'declined'){ echo 'current-filter'; } ?>">
                                    <a href="<?php echo add_query_arg('filter', 'declined', dokan_get_navigation_url( 'products')); ?>"><?php esc_html_e( 'DECLINED', 'dokan-lite' ); ?></a>
                                </li> 
                            </ul>
                        </div>

                        <form action="" method="post">
                            <button class="deal-download"><i class="fa fa-download" aria-hidden="true"></i></button>
                            <input type="hidden" name="deal_download" value="history">
                            <?php wp_nonce_field( 'deal_download_action', 'deal_history_nonce' ); ?>
                        </form>
                    </div>

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

                    <form method="get" class="dokan-form-inline dokan-w6 dokan-product-search-form">

                    <button type="submit" name="product_listing_search" value="ok" class="dokan-btn dokan-btn-theme"><i class="fa fa-search" aria-hidden="true"></i></button>

                    <?php wp_nonce_field( 'dokan_product_search', 'dokan_product_search_nonce' ); ?>

                    <div class="dokan-form-group">
                    <input type="text" class="dokan-form-control" name="product_search_name" placeholder="<?php esc_html_e( 'Search', 'dokan-lite' ) ?>" value="<?php echo isset( $get_data['product_search_name'] ) ? esc_attr( $get_data['product_search_name'] ) : '' ?>">
                    </div>

                    <?php
                    if ( isset( $get_data['product_cat'] ) ) { ?>
                    <input type="hidden" name="product_cat" value="<?php echo esc_attr( $get_data['product_cat'] ); ?>">
                    <?php }

                    if ( isset( $get_data['date'] ) ) { ?>
                    <input type="hidden" name="date" value="<?php echo esc_attr( $get_data['date'] ); ?>">
                    <?php }
                    ?>
                    </form>
                </div>

                <div class="dokan-dashboard-product-listing-wrapper">

                    <form id="product-filter" method="POST" class="dokan-form-inline">
                       
                        <table class="dokan-table dokan-table-striped product-listing-table dokan-inline-editable-table" id="dokan-product-list-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Deal', 'dokan-lite' ); ?></th>
                                    <?php if (current_user_can('administrator')){ ?>
                                        <th class="tac"><?php esc_html_e( 'Vendor', 'dokan-lite' ); ?></th>
                                    <?php } ?>
                                    <th class="tac"><?php esc_html_e( 'SOLD', 'dokan-lite' ); ?></th>
                                    <th class="tac"><?php esc_html_e( 'ENDED', 'dokan-lite' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
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
                                        ?>
                                        <tr class="<?php echo esc_attr( $tr_class ); ?>">
                                            <td class="deal-info" data-title="<?php esc_attr_e( 'Deal', 'dokan-lite' ); ?>">
                                                <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
                                                    <a href="<?php echo esc_url( kate_edit_product_url( $post->ID ) ); ?>"><?php echo wp_kses( $product->get_image(), $img_kses ); ?></a>
                                                <?php else: ?>
                                                    <?php echo wp_kses( $product->get_image(), $img_kses ); ?>
                                                <?php endif ?>
                                            
                                                <div class="deal-title">
                                                    <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
                                                    <p><a href="<?php echo esc_url( kate_edit_product_url( $post->ID ) ); ?>"><?php echo esc_html( $product->get_title() ); ?></a></p>
                                                    <?php else: ?>
                                                        <p><a href=""><?php echo esc_html( $product->get_title() ); ?></a></p>
                                                    <?php endif ?>

                                                    <?php if ( !empty( $row_actions ) ): ?>
                                                       <div class="row-actions">
                                                            <span class="edit">
                                                                <a href="<?php echo kate_edit_product_url( $post->ID ); ?>"><?php esc_attr_e( 'Details', 'dokan-lite' ); ?></a>
                                                            </span>
                                                            <?php if ($post->post_status != 'declined') { ?>
                                                            |
                                                            <span class="SEE-ON-KATE-CREW">
                                                                <a href="<?php echo get_permalink( $post->ID ); ?>" target="_blank">
                                                                    <?php esc_attr_e( 'SEE ON KATE & CREW', 'dokan-lite' ); ?>
                                                                </a>
                                                            </span>
                                                            |
                                                            <span class="ORDERS">
                                                                <a href="<?php echo add_query_arg('pid', base64_encode($post->ID), esc_url( dokan_get_navigation_url('orders'))); ?>"><?php esc_attr_e( 'ORDERS', 'dokan-lite' ); ?></a>
                                                            </span>
                                                            |
                                                             <span class="REBOOK">
                                                                <a href="<?php echo add_query_arg('rebook', base64_encode($post->ID), esc_url( dokan_get_navigation_url('new-product'))); ?>"><?php esc_attr_e( 'REBOOK', 'dokan-lite' ); ?></a>
                                                            </span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php endif ?>
                                                </div>
                                                
                                            </td>

                                            <?php if (current_user_can('administrator')){ ?>
                                                <td class="tac"><?php echo ucfirst($store_user->get_shop_name()); ?></td>
                                            <?php } ?>
                                           
                                            <td class="tac" data-title="<?php esc_attr_e( 'SOLD', 'dokan-lite' ); ?>">
                                                <?php 
                                                $qty = get_post_meta($post->ID, '_totalQtyAvailable', true); 
                                                $sold = kc_get_deal_orders($post->ID);
                                                $percentage = !empty($qty)?($sold/$qty)*100:0;
                                                if ($post->post_status != 'declined') {
                                                ?>
                                                <div class="deal-progressbar">
                                                    <div style="width:<?php echo $percentage; ?>%"></div>
                                                    <p> <?php echo ($sold)?$sold:'0'; ?> / <?php echo !empty($qty)?$qty:0; ?></p>
                                                </div>
                                                <?php } ?>
                                            </td>
                                           
                                            <td class="tac" data-title="<?php esc_attr_e( 'Ended Date', 'dokan-lite' ); ?>">
                                                 <?php 
                                                 if ($post->post_status == 'declined' || $post->post_status == 'canceled')
                                                 {
                                                    echo ucfirst($post->post_status);
                                                 }else{
                                                    $endDate =  get_post_meta($post->ID, '_endDate', true); 
                                                    if (!empty($endDate)) {
                                                        echo date('m/d/y', strtotime($endDate));
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
                    </form>
                </div>
                    <?php
                    wp_reset_postdata();                    
                    ?>
                </article>

                <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
            do_action( 'dokan_after_listing_product' );
            ?>

        </div><!-- #primary .content-area -->

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
