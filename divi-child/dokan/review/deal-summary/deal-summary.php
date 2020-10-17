<?php
/**
 * Dokan Dahsbarod Review Main Template
 *
 * @since 2.4
 *
 * @package dokan
 */
global $post, $wpdb;
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_review_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        //do_action( 'dokan_dashboard_review_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-reviews-content">

        <article class="dokan-reviews-area">

            <?php 
            $page_number       = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
            $get_data      = wp_unslash( $_GET );

            $user_id = get_current_user_id();

            $rate = '';
            if( isset( $get_data['rate'] ) && !empty($get_data['rate'])) {
                $rate = $get_data['rate'];
            }

            $rating_filter = ''; 
            
            if(!empty($rate) && $rate == 'Positive') {
                $rating_filter = " HAVING AVG(cm.meta_value) >= 3 "; 
            }elseif(!empty($rate) && $rate == 'Negative') {
               $rating_filter = " HAVING AVG(cm.meta_value) <= 3 ";  
            }elseif(!empty($rate) && $rate != 'Any') {
                $rate_less = $rate+1;
                $rating_filter = " HAVING AVG(cm.meta_value) >= $rate AND AVG(cm.meta_value) < $rate_less"; 
            }           

            $limit       = 20;
            $status      = '1';
            $pagenum     = max( 1, $page_number );
            $offset      = ( $pagenum - 1 ) * $limit;

            $deal_query = $wpdb->get_results(
              "SELECT p.*  
              FROM 
              $wpdb->comments as c, 
              $wpdb->commentmeta as cm, 
              $wpdb->posts as p
              WHERE p.post_author='$user_id' AND
              p.post_status='publish' AND
              c.comment_post_ID=p.ID AND
              cm.comment_id = c.comment_ID AND
              c.comment_approved='$status' AND
              cm.meta_key = 'rating' AND
              cm.meta_value != '' AND
              p.post_type='product'  
              GROUP BY p.ID
              $rating_filter
              ORDER BY c.comment_ID DESC
              LIMIT $offset,$limit"
            );  

            $total_results = $wpdb->get_var(
              "SELECT COUNT(*)  
              FROM 
              (SELECT COUNT( DISTINCT p.ID)  
              FROM 
              $wpdb->comments as c, 
              $wpdb->commentmeta as cm, 
              $wpdb->posts as p
              WHERE p.post_author='$user_id' AND
              p.post_status='publish' AND
              c.comment_post_ID=p.ID AND
              cm.comment_id = c.comment_ID AND
              c.comment_approved='$status' AND
              cm.meta_key = 'rating' AND
              cm.meta_value != '' AND
              p.post_type='product'
              GROUP BY p.ID
              $rating_filter
              ORDER BY c.comment_ID DESC) as total"
            );

            $total_pages = ceil($total_results/$limit);
            //print_r($deal_query);

            $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
            $base_url = dokan_get_navigation_url('deal-summary');        
            ?>

            <div class="deal-summary-header">
                <div class="dokan-dashboard-header">
                    <select name="drop-down" onchange="window.location.href='<?php echo $base_url; ?>?rate='+this.value" class="select store-rating-filter">
                        <option value="Any"><?php _e('All Stars', 'woocommerce'); ?></option>
                        <option <?php if($rate == '5'){ echo 'selected'; } ?> value="5">5 Stars</option>
                        <option <?php if($rate == '4'){ echo 'selected'; } ?> value="4">4 Stars</option>
                        <option <?php if($rate == '3'){ echo 'selected'; } ?>  value="3">3 Stars</option>
                        <option <?php if($rate == '2'){ echo 'selected'; } ?> value="2">2 Stars</option>
                        <option <?php if($rate == '1'){ echo 'selected'; } ?> value="1">1 Stars</option>
                        <option disabled="disabled">------------------</option>
                        <option <?php if($rate == 'Positive'){ echo 'selected'; } ?> value="Positive">Positive</option>
                        <option <?php if($rate == 'Negative'){ echo 'selected'; } ?> value="Negative">Negative</option>
                    </select> 
                </div><!-- .dokan-dashboard-header -->

                <?php 
                if ( $total_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $total_pages,
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
            </div>

            <div id="loader-top" class="review-loader hide">
                <div class="boxloader"><div class="loadernew"></div></div>
            </div>
            <div class="dokan-comments-wrap">
                <?php
                /**
                * dokan_review_content_status_filter hook
                *
                * @hooked dokan_review_status_filter
                */
                do_action( 'dokan_review_content_status_filter', $post_type, $counts );
                ?>
                <div class="deal-summary">
                    <table class="dokan-table dokan-table-striped product-listing-table dokan-inline-editable-table" id="dokan-product-list-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Deal', 'dokan-lite' ); ?></th>
                                <th class="tac"><?php esc_html_e( 'RATING', 'dokan-lite' ); ?></th>
                                <th class="tac"><?php esc_html_e( 'END DATE', 'dokan-lite' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            

                            if ( $deal_query ) {

                                foreach ($deal_query as $key => $post) {

                                    setup_postdata( $post ); 
                                    //$product_query->the_post();

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
                                    ?>
                                    <tr class="<?php echo esc_attr( $tr_class ); ?>">
                                        <td class="deal-info" data-title="<?php esc_attr_e( 'Deal', 'dokan-lite' ); ?>">
                                            <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
                                                <a href="<?php echo add_query_arg('id',base64_encode($post->ID) , dokan_get_navigation_url( 'review-deal' )); ?>"><?php echo wp_kses( $product->get_image(), $img_kses ); ?></a>
                                            <?php else: ?>
                                                <?php echo wp_kses( $product->get_image(), $img_kses ); ?>
                                            <?php endif ?>
                                        
                                            <div class="deal-title">
                                                <?php if ( current_user_can( 'dokan_edit_product' ) ): ?>
                                                <p><a href="<?php echo add_query_arg('id',base64_encode($post->ID) , dokan_get_navigation_url( 'review-deal' )); ?>"><?php echo esc_html( $product->get_title() ); ?></a></p>
                                                <?php else: ?>
                                                    <p><a href=""><?php echo esc_html( $product->get_title() ); ?></a></p>
                                                <?php endif ?>

                                                <?php if ( !empty( $row_actions ) ): ?>
                                                    <div class="row-actions">
                                                        <span class="edit">
                                                            <a href="<?php echo kate_edit_product_url( $post->ID ); ?>"><?php esc_attr_e( 'View Details', 'dokan-lite' ); ?></a>
                                                        </span>          
                                                        |
                                                         <span class="REBOOK">
                                                            <a href="<?php echo add_query_arg('rebook', base64_encode($post->ID), esc_url( dokan_get_navigation_url('new-product'))); ?>"><?php esc_attr_e( 'REBOOK', 'dokan-lite' ); ?></a>
                                                        </span>
                                                    </div>
                                                <?php endif ?>
                                            </div>       
                                        </td>
                                       
                                        <td class="tac" data-title="<?php esc_attr_e( 'RATING', 'dokan-lite' ); ?>">
                                            <div class="rating-info">
                                            <?php 
                                            $rating_count = $product->get_rating_count();
                                            $review_count = $product->get_review_count();
                                            $average      = $product->get_average_rating();
                                            echo wc_get_rating_html( $average, $rating_count ); 

                                            $review_text = sprintf( _n( '%s review', '%s reviews', $review_count, 'dokan-lite' ), $review_count );

                                             $rationgs = get_post_group_comment($post->ID);

                                             ?>
                                            <div class="review-text">(<?php echo $review_text; ?>)</div>
                                            <div class="review-chart">
                                                <ul class="rating-filter-list">
                                                    <?php 
                                                    if($rationgs){
                                                        foreach ($rationgs as $key => $rate) {
                                                            $width  = round(($rate/$review_count ) * 100);

                                                            ?>
                                                            <li>
                                                                <span class="rating_bar"><?php echo $key; ?> Star</span>
                                                                <div class="progress">
                                                                    <div class="progress-bar" role="progressbar" style="width:<?php echo $width; ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <span class="count_bar"><?php echo $rate; ?></span>
                                                            </li>
                                                            <?php 
                                                        }
                                                    } 
                                                    ?>  
                                                </ul>
                                                <div class="read-all-reviews">  <a href="<?php echo add_query_arg('id',base64_encode($post->ID) , dokan_get_navigation_url( 'review-deal' )); ?>"><?php esc_attr_e( 'Read All', 'dokan-lite' ); ?> <?php echo $review_text; ?></a>
                                                </div>
                                             </div>
                                             </div>
                                        </td>                              
                                        <td class="tac" data-title="<?php esc_attr_e( 'ENDED', 'dokan-lite' ); ?>">
                                            <?php 
                                            $startDate = get_post_meta($post->ID, '_startDate', true); 
                                            if (!empty($startDate)) {
                                                echo date('M d', strtotime("+3 day", strtotime($startDate)));
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
                </div>
                <?php 
                if ( $total_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $total_pages,
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
                <?php wp_reset_postdata(); ?>
            </div>

        </article>

    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_review_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_review_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>