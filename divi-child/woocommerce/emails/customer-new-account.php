<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer username */ ?>
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ 
$user = get_user_by('login',$user_login);
$user_roles = $user->roles;
$blogname = get_option('blogname');

if ( in_array( 'seller', $user_roles, true )) {
	
	$email_content =  $email->get_option( 'seller_email_content' ); 
	$email_content = str_replace('{vendor_name}',$user->first_name,$email_content);
	$email_content = str_replace('{site_name}',$blogname,$email_content);
	echo $user_message  = wpautop($email_content);

	/**
	* Show user-defined additional content - this is set in each email's settings.
	*/
	if ( $additional_content ) {
		echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	}
}else{

	$email_content =  $email->get_option( 'customer_email_content' ); 
	$email_content = str_replace('{customer_name}',$user_login,$email_content);
	$email_content = str_replace('{site_name}',$blogname,$email_content);
	$email_content = str_replace('{my_account_url}',esc_url( wc_get_page_permalink( 'myaccount' ) ),$email_content);
	echo $user_message  = wpautop($email_content);
?>
	
	<?php if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>
		<?php /* translators: %s: Auto generated password */ ?>
		<p><?php printf( esc_html__( 'Your password has been automatically generated: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
	<?php endif; 

	/**
	* Show user-defined additional content - this is set in each email's settings.
	*/
	if ( $additional_content ) {
		echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	}

	do_action( 'woocommerce_email_footer', $email );
}

