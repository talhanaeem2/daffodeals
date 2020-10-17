<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$GLOBALS['comment'] = $single_comment;
$comment_date       = get_comment_date( '', $single_comment->comment_ID );
$comment_author_img = get_avatar( $single_comment->comment_author_email, 180 );
$permalink          = get_comment_link( $single_comment );
//print_r($single_comment);
$post_ID = $single_comment->comment_post_ID;
?>
<li <?php comment_class(); ?> itemtype="http://schema.org/Review" itemscope="" itemprop="reviews">
    <div class="review_comment_container">
        <div class="comment-text">
            
            <?php
            if ( get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) :
                $rating = intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) );
                ?>
                <div class="dokan-rating">
                    <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating rating-<?php echo $rating; ?>" title="<?php echo sprintf( __( 'Rated %d out of 5', 'dokan' ), $rating ) ?>">
                        <span style="width:<?php echo ( intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'dokan' ); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <div class="comment-top">
                <span class="comment-date"><time datetime="<?php echo date( 'c', strtotime( $comment_date ) ); ?>" itemprop="datePublished"><?php echo get_timeago(strtotime($comment_date), 'dashboard'); ?></time></span>
            </div>
        </div>
        <div class="description" itemprop="description">
                <p><?php echo $single_comment->comment_content; ?></p>
            </div>
        <div class="product" itemprop="product">
            <a href="<?php echo get_permalink($post_ID); ?>">
                <span class="dokan-review-author-img"><?php echo get_the_post_thumbnail($post_ID,'thumbnail'); ?></span>
                <span><?php echo get_the_title($post_ID); ?></span>
            </a>
        </div>
    </div>
</li>