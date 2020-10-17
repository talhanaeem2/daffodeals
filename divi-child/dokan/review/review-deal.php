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

<div class="dokan-dashboard-wrap dashboard-review-deal">

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

        $post_id = base64_decode($_GET['id']);
        $startDate = get_post_meta($post_id, '_startDate', true); 
        $ended = '';
        if (!empty($startDate)) {
          $ended = date('M jS', strtotime("+3 day", strtotime($startDate)));
        }
    ?>

    <div class="dokan-dashboard-content dokan-reviews-content">

        <article class="dokan-reviews-area">
          <div class="header-deal-title"> 
            <button onclick="window.history.back();" class="btn btn-default btn-icon"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
            <div class="info">
              <span class="deal-title">
                <?php echo get_the_title($post_id); ?>
              </span>
              <span class="endedDate">Ended <?php echo $ended; ?></span>
            </div>
          </div>

          <div class="deal-summary-header">
              <div class="filter-group">
                  <select name="drop-down" id="review-deal-filter" class="select review-deal-filter">
                      <option value="Any"><?php _e('All Stars', 'woocommerce'); ?></option>
                      <option value="5">5 Stars</option>
                      <option value="4">4 Stars</option>
                      <option value="3">3 Stars</option>
                      <option value="2">2 Stars</option>
                      <option value="1">1 Stars</option>
                      <option disabled="disabled">------------------</option>
                      <option value="Positive">Positive</option>
                      <option value="Negative">Negative</option>
                  </select> 
              </div>  
              <div class="dropdown">
                <button class="btn btn-dropdown btn-default dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu align-right" role="menu">
                  <li><a href="<?php echo add_query_arg('rebook', base64_encode($post_id), esc_url( dokan_get_navigation_url('new-product'))); ?>"><?php esc_attr_e( 'REBOOK', 'dokan-lite' ); ?></a></li>
                  <li><a href="<?php echo kate_edit_product_url( $post_id ); ?>"><?php esc_attr_e( 'VIEW DETAILS', 'dokan-lite' ); ?></a></li>
                </ul>
              </div>  
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

                <div class="col-md-4">

                  <h2 class="headline"><?php _e( 'DEAL RATING', 'dokan' ); ?></h2>
                  <?php 
                  $product = wc_get_product($post_id);
                  $rating_count = $product->get_rating_count();
                  $review_count = $product->get_review_count();
                  $average      = $product->get_average_rating();

                  $ratings = get_post_group_comment($post_id); ?>

                  <div class="review-rating">
                    <div class="text-rating-row">
                      <span class="rating large ng-binding"><?php echo number_format($average,1); ?><span> / 5</span></span>
                    </div>
                    <div class="star-rating-row"> <?php echo wc_get_rating_html( $average, $rating_count );  ?>
                    (<span class="count"><a href="<?php echo add_query_arg('id',base64_encode($post_id) , dokan_get_navigation_url( 'review-deal' )); ?>"> <?php printf( _n( '%s review', '%s reviews', $review_count, 'woocommerce' ), esc_html( $review_count ) ); ?></a></span>)</div>

                  </div>
                  <input type="hidden" id="review_post_id" value="<?php echo $post_id; ?>"> 
                  <ul class="rating-filter-list">
                    <?php 
                    if($ratings){
                      foreach ($ratings as $key => $rate) {
                        $width  = round(($rate/$review_count ) * 100);

                        ?>
                        <li>
                          <span class="rating_bar"> 
                            <a href="javascript:void(0)" class="review-deal-rating" data-val="<?php echo $key; ?>" id="star_<?php echo $key; ?>" data-star-reviews="<?php echo $rate; ?>">
                              <?php echo $key; ?> Star 
                            </a>
                          </span>
                          <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width:<?php echo $width; ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                          <span class="count_bar"><?php echo number_format_short( $rate ); ?></span>
                        </li>
                        <?php 
                      }
                    } 
                    ?>  
                  </ul>               
                </div>

                <div id="review_deal_content" class="review_content_main">
                    <?php                     
                    $rate = 'Any';
                    review_deal($post_id,$rate);
                    ?>
                </div>  
                <div id="loader-bottom" class="review-loader hide">
                  <svg width="79px" height="79px" viewBox="-1 0 79 78" xmlns="http://www.w3.org/2000/svg"  style="line-height: 32px; width: 32px; height: 32px;"><g class="" id="dots" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g class=""><g class=""><path class="" d="M16.27,9.4394 C12.503,9.4394 9.439,12.5034 9.439,16.2704 C9.439,20.0374 12.503,23.1014 16.27,23.1014 C20.037,23.1014 23.101,20.0374 23.101,16.2704 C23.101,12.5034 20.037,9.4394 16.27,9.4394" id="dot8" fill="#111111"></path><path class="" d="M6.8306,32 C3.0636,32 -0.0004,35.064 -0.0004,38.831 C-0.0004,42.598 3.0636,45.662 6.8306,45.662 C10.5976,45.662 13.6616,42.598 13.6616,38.831 C13.6616,35.064 10.5976,32 6.8306,32" id="dot7" fill="#111111"></path><path class="" d="M16.27,54.6172 C12.503,54.6172 9.439,57.6802 9.439,61.4472 C9.439,65.2142 12.503,68.2782 16.27,68.2782 C20.037,68.2782 23.101,65.2142 23.101,61.4472 C23.101,57.6802 20.037,54.6172 16.27,54.6172" id="dot6" fill="#111111"></path><path class="" d="M38.8306,64.0557 C35.0636,64.0557 31.9996,67.1197 31.9996,70.8867 C31.9996,74.6537 35.0636,77.7177 38.8306,77.7177 C42.5976,77.7177 45.6616,74.6537 45.6616,70.8867 C45.6616,67.1197 42.5976,64.0557 38.8306,64.0557" id="dot5" fill="#111111"></path><path class="" d="M61.3911,54.6172 C57.6241,54.6172 54.5611,57.6802 54.5611,61.4472 C54.5611,65.2142 57.6241,68.2782 61.3911,68.2782 C65.1581,68.2782 68.2221,65.2142 68.2221,61.4472 C68.2221,57.6802 65.1581,54.6172 61.3911,54.6172" id="dot4" fill="#111111"></path><path class="" d="M70.8306,32 C67.0636,32 63.9996,35.064 63.9996,38.831 C63.9996,42.598 67.0636,45.662 70.8306,45.662 C74.5976,45.662 77.6616,42.598 77.6616,38.831 C77.6616,35.064 74.5976,32 70.8306,32" id="dot3" fill="#111111"></path><path class="" d="M61.3911,9.4394 C57.6241,9.4394 54.5611,12.5034 54.5611,16.2704 C54.5611,20.0374 57.6241,23.1014 61.3911,23.1014 C65.1581,23.1014 68.2221,20.0374 68.2221,16.2704 C68.2221,12.5034 65.1581,9.4394 61.3911,9.4394" id="dot2" fill="#111111"></path><path class="" d="M38.8306,0 C35.0636,0 31.9996,3.064 31.9996,6.831 C31.9996,10.598 35.0636,13.662 38.8306,13.662 C42.5976,13.662 45.6616,10.598 45.6616,6.831 C45.6616,3.064 42.5976,0 38.8306,0" id="dot1" fill="#111111"></path></g></g></g></svg>
                </div>              
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

<div id="review-response-modal" class="modal-container">
  <div class="modalbox">
    <div class="model_contant">
      <button type="button" class="btnclose btn-modal-close">&#10005;</button>
      <div class="dashmodel_body">
        <h2><?php _e( 'Are You Sure', 'dokan' ); ?>?</h2>
        <div class="question single">
          <p>
            <?php _e( 'You should address a low rating by first', 'dokan' ); ?> <strong><?php _e( 'emailing the customer', 'dokan' ); ?></strong> <?php _e( 'to resolve their concern at', 'dokan' ); ?> <a href=""  class="comment-author-email"></a>.
          </p>
          <p>
            <?php _e( 'At last resort, you may respond publicly to the review', 'dokan' ); ?>.
            <?php _e( 'Once you do so, the', 'dokan' ); ?> <strong><?php _e( 'customer will no longer be able to edit their review', 'dokan' ); ?>.</strong>
            <?php _e( 'You may submit a single reply, which will be', 'dokan' ); ?> <strong><?php _e( 'displayed publicly', 'dokan' ); ?></strong> <?php _e( 'and', 'dokan' ); ?>
            <strong><?php _e( 'cannot be edited', 'dokan' ); ?>.</strong>
          </p>
          <p><?php _e( 'Are you sure you would like to leave a public response', 'dokan' ); ?>?</p>
        </div>
        <button type="button" id="btn-review-yes-reply" data-id="" class="btngotit"><?php _e( 'YES, REPLY', 'dokan' ); ?></button> 
        <button type="button" class="btn btn-cancel"><?php _e( 'CANCEL', 'dokan' ); ?></button>
      </div>
    </div>
  </div>
</div>


