<?php
    global $post;
?>
<article class="dokan-product-listing-area innerarea">

    <div class="dokan-dashboard-product-listing-wrapper">

        <?php 
        $pagenum       = isset( $_REQUEST['pagenum'] ) ? absint( $_REQUEST['pagenum'] ) : 1;
        $post_statuses = array( 'publish', 'draft', 'pending', 'future' );
        $get_data      = wp_unslash( $_REQUEST );

        $args = array(
            'posts_per_page' => 15,
            'paged'          => $pagenum,
            'author'         => get_current_user_id(),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => apply_filters( 'dokan_product_listing_exclude_type', array() ),
                    'operator' => 'NOT IN',
                ),
            ),
        );

        if (current_user_can( 'administrator' ) ) {
            unset($args['author']);
        }

        if ( isset( $get_data['post_status']) && in_array( $get_data['post_status'], $post_statuses ) ) {
            $args['post_status'] = $get_data['post_status'];
        }

        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'       => '_kate_post_status',
                'value'     => 'ended',
                'compare'   => '=',
            )
        );

        if( isset( $get_data['product_cat'] ) && $get_data['product_cat'] != -1 ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => (int)  $_GET['product_cat'],
                'include_children' => false,
            );
        }

        if ( isset( $get_data['product_search_name']) && !empty( $get_data['product_search_name'] ) ) {
            $args['s'] = $get_data['product_search_name'];
        }

        $original_post = $post;
        $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );
        $pagenum      = isset( $_REQUEST['pagenum'] ) ? absint( $_REQUEST['pagenum'] ) : 1;
        $base_url = dokan_get_navigation_url('pending-product');

       /* echo '<pre>';
        print_r($product_query);
         echo '</pre>';*/

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
        <div class="total-record">
            <?php echo $product_query->found_posts; ?> <?php esc_html_e( 'Deals', 'dokan-lite' ); ?>
        </div>

        <form id="product-filter" method="POST" class="dokan-form-inline">
           

            <table class="dokan-table dokan-table-striped product-listing-table dokan-inline-editable-table" id="dokan-product-list-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'REBOOK', 'dokan-lite' ); ?></th>
                        <th><?php esc_html_e( 'Deal', 'dokan-lite' ); ?></th>
                        <th class="tac"><?php esc_html_e( 'Sold', 'dokan-lite' ); ?></th>
                       <!--  <th class="tac"><?php //esc_html_e( 'Demand', 'dokan-lite' ); ?></th> -->
                        <th class="tac"><?php esc_html_e( 'Ended', 'dokan-lite' ); ?></th>
                        <th class="tac"><?php esc_html_e( 'Status', 'dokan-lite' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $seller_deal_status = seller_deal_status();
                    if ( $product_query->have_posts() ) {
                        while ($product_query->have_posts()) {
                            $product_query->the_post();

                            $row_actions = dokan_product_get_row_action( $post );
                            $tr_class = ( $post->post_status == 'pending' ) ? 'danger' : '';
                            $view_class = ($post->post_status == 'pending' ) ? 'dokan-hide' : '';
                            $product = wc_get_product( $post->ID );

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

                            $product_id      = $post->ID;
                            ?>
                            <tr class="<?php echo esc_attr( $tr_class ); ?>">
                                <td>
                                    <a id="rebook-btn" class="button outline small" href="<?php echo add_query_arg('rebook', base64_encode($post->ID), esc_url( dokan_get_navigation_url('new-product'))); ?>">
                                        <?php esc_attr_e( 'REBOOK', 'dokan-lite' ); ?>
                                    </a>
                                </td>
                                <td class="deal-info" data-title="<?php esc_attr_e( 'Deal', 'dokan-lite' ); ?>">
                                    <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
                                        <a href="<?php echo esc_url( dokan_edit_product_url( $post->ID ) ); ?>"><?php echo wp_kses( $product->get_image(), $img_kses ); ?></a>
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
                                                    <a target="_blank" href="<?php echo kate_edit_product_url( $product_id ); ?>"><?php esc_attr_e( 'DETAILS', 'dokan-lite' ); ?></a>
                                                </span>
                                                
                                                |

                                                <span class="view">
                                                    <a target="_blank" href="<?php echo get_permalink( $product_id ); ?>"><?php esc_attr_e( 'SEE ON KATE & CREW', 'dokan-lite' ); ?></a>
                                                </span>

                                                
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    
                                </td>
                               
                                <td class="tac" data-title="<?php esc_attr_e( 'SOLD', 'dokan-lite' ); ?>">
                                    <?php 
                                    $qty = get_post_meta($post->ID, '_totalQtyAvailable', true); 
                                    $sold = kc_get_deal_orders($post->ID);
                                    $percentage = !empty($qty)?($sold/$qty)*100:0;
                                    ?>
                                    <div class="deal-progressbar">
                                        <div style="width:<?php echo $percentage; ?>%"></div>
                                        <p> <?php echo $sold; ?> / <?php echo !empty($qty)?$qty:0; ?></p>
                                    </div>
                                </td>
                                <!-- <td class="tac" data-title="<?php //esc_attr_e( 'DEMAND', 'dokan-lite' ); ?>">0</td> -->

                                <td class="tac" data-title="<?php esc_attr_e( 'ENDED', 'dokan-lite' ); ?>">
                                    <?php 
                                    $endDate =  get_post_meta($post->ID, '_endDate', true); 
                                    if (!empty($endDate)) {
                                        echo date('m/d/y', strtotime($endDate));
                                    }
                                    ?>
                                </td>

                                <td class="tac" data-title="<?php esc_attr_e( 'STATUS', 'dokan-lite' ); ?>">
                                    <?php 
                                    $post_status =  get_post_meta($post->ID, '_kate_post_status', true); 
                                    if (!empty($post_status)) {
                                        echo $seller_deal_status[$post_status];
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