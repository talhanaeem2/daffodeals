<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Dokan Dashboard Seller Profile Store Form Template
 *
 * @since 2.4
 */

$storename      = isset( $profile_info['store_name'] ) ? $profile_info['store_name'] : '';
$phone          = isset( $profile_info['phone'] ) ? $profile_info['phone']:'';

$address         = isset( $profile_info['address'] ) ? $profile_info['address'] : '';
$address_street1 = isset( $profile_info['address']['street_1'] ) ? $profile_info['address']['street_1'] : '';
$address_street2 = isset( $profile_info['address']['street_2'] ) ? $profile_info['address']['street_2'] : '';
$address_city    = isset( $profile_info['address']['city'] ) ? $profile_info['address']['city'] : '';
$address_zip     = isset( $profile_info['address']['zip'] ) ? $profile_info['address']['zip'] : '';
$address_country = isset( $profile_info['address']['country'] ) ? $profile_info['address']['country'] : '';
$address_state   = isset( $profile_info['address']['state'] ) ? $profile_info['address']['state'] : '';

$store_user  = dokan()->vendor->get( $current_user );
$user_registered = $store_user->data->user_registered;
?>
<?php do_action( 'dokan_business_info_before_form', $current_user, $profile_info ); ?>
    
<div class="page-header">
    <h1><?php esc_html_e( 'Business Information', 'dokan-lite' ); ?></h1>
    <p><?php esc_html_e( 'Manage and update your business info! Don’t worry—we’ll keep your info private between us. Only your return address will be visible to customers.', 'dokan-lite' ); ?></p>
</div>

<form method="post" id="seller-business-info-form"  action="" class="dokan-form-horizontal">

    <?php wp_nonce_field( 'business_info_nonce' ); ?>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Company Name', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <span><?php echo $storename; ?></span>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Seller Since', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <span><?php echo date('F jS Y',strtotime($user_registered)); ?></span>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Company Phone', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input id="dokan_setting_phone" required value="<?php echo $phone; ?>" name="phone" class="dokan-form-control" type="number">
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Company Email', 'dokan-lite' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <span><?php echo $store_user->data->user_email; ?></span>
        </div>
    </div>

    <div class="page-sub-heading">
        <h3><?php esc_html_e( 'Payment Information', 'dokan-lite' ); ?></h3>
        <p><?php esc_html_e( 'These are the withdraw methods available for you. Please update your payment information below to submit withdraw requests and get your store payments seamlessly.', 'dokan-lite' ); ?></p>
    </div>

    <?php foreach ( $methods as $method_key ) {
        $method = dokan_withdraw_get_method( $method_key );
        ?>
        <div class="dokan-form-group mr_none">
            <label class="dokan-w3 dokan-control-label" for="dokan_setting"><?php echo esc_html( $method['title'] ) ?></label>
            <div class="dokan-w6">
                <?php if ( is_callable( $method['callback'] ) ) {
                    call_user_func( $method['callback'], $profile_info );
                } ?>
            </div> <!-- .dokan-w6 -->
        </div>
    <?php } ?>

    <div class="page-sub-heading">
        <h3><?php esc_html_e( 'Business Address', 'dokan-lite' ); ?></h3>
        <p><?php esc_html_e( 'Please verify that your address is up to date.', 'dokan-lite' ); ?></p>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php esc_html_e( 'Company Name', 'dokan-lite' ); ?></label>

        <div class="dokan-w5 dokan-text-left">
            <input id="dokan_store_name" required value="<?php echo esc_attr( $storename ); ?>" name="dokan_store_name" placeholder="<?php esc_attr_e( 'Company Name', 'dokan-lite' ); ?>" class="dokan-form-control" type="text">
        </div>
    </div>

    <!--address-->
    <?php
    $verified = false;

    if ( isset( $profile_info['dokan_verification']['info']['store_address']['v_status'] ) ) {
        if ( $profile_info['dokan_verification']['info']['store_address']['v_status'] == 'approved' ){
            $verified = true;
        }
    }
    dokan_seller_address_fields( $verified, true );
    ?>
    <!--address-->
    <div class="seller_footer">
        <div class="seller_footer_details">                
            <div class="seller_right">
                <button type="submit" class="btn btn-default ajax_prev"><?php esc_html_e( 'Save Changes', 'dokan-lite' ); ?></button>
            </div>
        </div>
    </div>

</form>

<?php do_action( 'dokan_business_info_after_form', $current_user, $profile_info ); ?>


