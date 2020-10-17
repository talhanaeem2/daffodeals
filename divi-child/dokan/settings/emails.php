<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Dokan Dashboard Seller Profile Store Form Template
 *
 * @since 2.4
 */

//print_r($seller_emails);
$store_user = dokan()->vendor->get( $current_user );
?>
<?php do_action( 'dokan_seller_email_before_form', $current_user, $profile_info ); ?>
    
<div class="page-header">
    <h1><?php esc_html_e( 'Email Communication', 'dokan-lite' ); ?></h1>
    <p><?php esc_html_e( 'Ready to delegate information to your team? Keep up your time-management skills by selecting the most relevant emails for each of your team member’s needs.', 'dokan-lite' ); ?></p>
</div>

<form method="post" id="seller-emails-form"  action="" class="dokan-form-horizontal">

    <?php wp_nonce_field( 'seller_emails_nonce' ); ?>
    <div class="page-sub-heading">
        <h3><?php esc_html_e( 'Email Subscriptions', 'dokan-lite' ); ?></h3>
        <p><?php esc_html_e( 'This allows you to specify where emails are sent.
.', 'dokan-lite' ); ?></p>
    </div>
    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Deals', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input type="text" name="seller_emails[deal]" value="<?php echo isset($seller_emails['deal'])?$seller_emails['deal']:''; ?>">
            <span><?php esc_html_e( 'Deal updates, deal comments, beautify reminders', 'dokan-lite' ); ?>.</span>
        </div>
    </div>

    <?php /* <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Customer Support', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input type="text" name="seller_emails['customer_support']">
            <span><?php esc_html_e( 'Refunds, exchanges, cancelled orders, order updates, and order address changes', 'dokan-lite' ); ?>.</span>
        </div>
    </div>
    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Shipping', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input type="text" name="seller_emails['customer_support']">
            <span><?php esc_html_e( 'Shipping reminders', 'dokan-lite' ); ?>.</span>
        </div>
    </div>
    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Shipping', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input type="text" name="seller_emails['customer_support']">
            <span><?php esc_html_e( 'Shipping reminders', 'dokan-lite' ); ?>.</span>
        </div>
    </div> */ ?>

    <div class="seller_footer">
        <div class="seller_footer_details">           
            <div class="seller_right">
                <button type="submit" class="btn btn-default ajax_prev"><?php esc_html_e( 'Save Changes', 'dokan-lite' ); ?></button>
            </div>
        </div>
    </div>
</form>

<?php do_action( 'dokan_seller_email_after_form', $current_user, $profile_info ); ?>


