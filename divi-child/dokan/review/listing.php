<?php
/**
 * Dokan Review Listing Template
 *
 * @since 2.4
 *
 * @package dokan
 */

?>
<form id="dokan_comments-form" action="" method="post">

<?php 

do_action( 'dokan_review_listing_table_body', $post_type );


if ( current_user_can( 'dokan_manage_reviews' ) && dokan_get_option( 'seller_review_manage', 'dokan_general', 'on' ) == 'on' ) : ?>
    <select name="comment_status" class="hide">
        <?php
            if ( $comment_status == 'hold' ) {
                ?>
                <option value="none"><?php _e( '-None-', 'dokan' ); ?></option>
                <option value="approve"><?php _e( 'Mark Approve', 'dokan' ); ?></option>
                <option value="spam"><?php _e( 'Mark Spam', 'dokan' ); ?></option>
                <option value="trash"><?php _e( 'Mark Trash', 'dokan' ); ?></option>
            <?php } else if ( $comment_status == 'spam' ) { ?>
                <option value="none"><?php _e( '-None-', 'dokan' ); ?></option>
                <option value="approve"><?php _e( 'Mark Not Spam', 'dokan' ); ?></option>
                <option value="delete"><?php _e( 'Delete permanently', 'dokan' ); ?></option>
            <?php } else if ( $comment_status == 'trash' ) { ?>
                <option value="none"><?php _e( '-None-', 'dokan' ); ?></option>
                <option value="approve"><?php _e( 'Restore', 'dokan' ); ?></option>
                <option value="delete"><?php _e( 'Delete permanently', 'dokan' ); ?></option>
            <?php } else { ?>
                <option value="none"><?php _e( '-None-', 'dokan' ); ?></option>
                <option value="hold"><?php _e( 'Mark Pending', 'dokan' ); ?></option>
                <option value="spam"><?php _e( 'Mark Spam', 'dokan' ); ?></option>
                <option value="trash"><?php _e( 'Mark Trash', 'dokan' ); ?></option>
                <?php
            }
        ?>
    </select>

    <?php wp_nonce_field( 'dokan_comment_nonce_action', 'dokan_comment_nonce' ); ?>

    <input type="submit" value="<?php _e( 'Submit', 'dokan' ); ?>" class="dokan-btn  dokan-danger dokan-btn-theme dokan-btn-sm hide" name="comt_stat_sub">
</form>
<?php endif; ?>