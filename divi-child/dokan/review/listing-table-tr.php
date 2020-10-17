<?php
/**
 * Dokan Listing Table tr template
 *
 * @since 2.4
 *
 * @package dokan
 */
$post_ID = $comment->comment_post_ID;
//print_r($comment);
$review_response = get_comment_meta( $comment->comment_ID, '_review_response', true);
//print_r($review_response);
?>
<tr>
    <td>
        <a href="<?php //echo add_query_arg('id',base64_encode($post_ID) , dokan_get_navigation_url( 'review-deal' )); ?>#">
            <?php echo get_the_post_thumbnail($post_ID,'thumbnail'); ?>
            <?php echo get_the_title($post_ID); ?>
        </a>
    </td>
    <td><?php echo $comment->comment_content; ?></td>
    <td>
        <?php if ( get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>
            <?php $rating =  intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ); ?>
            <div class="dokan-rating">
                <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating rating-<?php echo $rating; ?>" title="<?php echo sprintf( __( 'Rated %d out of 5', 'dokan' ), $rating ) ?>">
                    <span style="width:<?php echo ( intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'dokan' ); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </td>
    <td><?php echo get_timeago(strtotime($comment_date), 'reviews' );  ?></td>
</tr>
