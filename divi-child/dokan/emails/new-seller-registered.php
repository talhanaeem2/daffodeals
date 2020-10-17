<?php
/**
 * New Seller Email.
 *
 * An email sent to the admin when a new vendor is registered.
 *
 * @class       Dokan_Email_New_Seller
 * @version     2.6.8
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); 

$email_content =  $email->get_option( 'email_content' );
$email_content = str_replace('{seller_name}',$data['seller_name'],$email_content);
$email_content = str_replace('{store_name}',$data['store_name'],$email_content);
$email_content = str_replace('{site_name}',$data['site_name'],$email_content);
$email_content = str_replace('{seller_edit}',$data['seller_edit'],$email_content);
echo $user_message  = wpautop($email_content);

do_action( 'woocommerce_email_footer', $email );
