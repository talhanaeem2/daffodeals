<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$GLOBALS['comment'] = $single_comment;
$comment_date       = get_comment_date( '', $single_comment->comment_ID );
$comment_author_img = get_avatar( $single_comment->comment_author_email, 180 );
$permalink          = get_comment_link( $single_comment );
$post_ID = $single_comment->comment_post_ID;
$review_response = get_comment_meta( $single_comment->comment_ID, '_review_response', true);
?>
<li <?php comment_class(); ?> itemtype="http://schema.org/Review" itemscope="" itemprop="reviews">
    <div class="review_comment_container">
        <div class="comment-text">
            <div class="comment-top">
                <span class="woocommerce-review__author"><strong><?php comment_author(); ?> </strong></span>
                <span class="comment-date"><time datetime="<?php echo date( 'c', strtotime( $comment_date ) ); ?>" itemprop="datePublished"><?php echo get_timeago(strtotime($comment_date), 'store'); ?></time></span>
            </div>
            <?php
            if ( get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) :
                $rating = intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) );
                ?>
                <div class="dokan-rating">
                    <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'dokan' ), $rating ) ?>">
                        <span style="width:<?php echo ( intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'dokan' ); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <div class="description" itemprop="description">
                <p><?php echo $single_comment->comment_content; ?></p>
            </div>
            <?php if(!empty($review_response)){ ?>
                <div class="seller-response-container">
                    <div class="seller-response-row">
                        <div class="seller-response-top">
                            <span class="seller-response-label">Show Seller Response:</span>
                            <i class="fa fa-chevron-down" color="mineShaft"></i>
                        </div>
                        <span class="seller-response-text hide"><?php echo isset($review_response['response'])?$review_response['response']:''; ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="product" itemprop="product">
            <a href="<?php echo get_permalink($post_ID); ?>">
                <span class="dokan-review-author-img"><?php echo get_the_post_thumbnail($post_ID,'thumbnail'); ?></span>
                <span><?php echo get_the_title($post_ID); ?></span>
            </a>
        </div>
    </div>
</li>