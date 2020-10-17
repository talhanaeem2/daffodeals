<?php
/**
 * Dokan Review Listing Table body Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<div class="container">
<div class="row">
<div class="col-md-12">
  
  <?php 
  $reviews_data  = get_store_total_reviews_and_ratings(get_current_user_id());
  $average      = $reviews_data->average;
  $review_count = $reviews_data->reviews;
  $rating_count = $reviews_data->ratings;
  
  $rationgs = get_store_group_comment(get_current_user_id()); ?>

  <div class="review-rating">
       <div class="revirw-block">
        <h2>Overall Rating:</h2>
        <div class="starsicon">
          <?php echo wc_get_rating_html( $average, $rating_count );  ?>
        </div>
        <div>
          <ul style="margin-top: 15px;">
            <?php 
            if($rationgs){
              foreach ($rationgs as $key => $rate) {
                $width  = ($review_count)?round(($rate/$review_count ) * 100):0;

                ?>
                 <li style="display: flex;align-items: center;">
                    <div class="text-rating"><?php echo $key; ?> stars</div>
                    <div style="width: 200px;max-width: 200px;height: 7px; margin: 0 7px;"><span style="height:  10px;background: #e3b937;display:block;width: <?php echo $width; ?>%;"></span></div>
                    <div class="text-rating-v"><?php echo number_format_short( $rate ); ?></div>
                  </li>
               
                <?php 
              }
            } 
            ?> 
          </ul>
        </div>
      </div>
      <div class="filter-review">
        <h3>Filter By:</h3>
        <div class="dropdown">
          <select id="review-rating-filter" class="select store-rating-filter">
            <option value="0">Select</option>
            <option value="5">5 Star</option>
            <option value="4">4 Star</option>
            <option value="3">3 Star</option>
            <option value="2">2 Star</option>
            <option value="1">1 Star</option>
          </select>
        </div>
    </div>          
  </div>

<div class="rating-found">
  <?php
  if ( count( $comments ) == 0 ) {
      echo '<span colspan="5">' . __( 'No Reviews Found', 'dokan' ) . '</span>';
  }else{
    ?>
    <table>
      <thead>
        <th>Deal</th>
        <th>Review</th>
        <th>Rating</th>
        <th>Time</th>
      </thead>
      <tbody id="review_content_main" class="review_content_main">
          <?php 
          foreach ( $comments as $comment ) {		
              Dokan_Pro_Reviews::init()->render_row( $comment, $post_type );
          }
          ?>
      </tbody>
    </table>
    <?php 
  }
  ?>
</div>

</div>
</div>
</div>