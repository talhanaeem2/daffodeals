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
if ( in_array( 'seller', $user_roles, true )) {
    ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user->first_name ) ); ?></p>
    <p><?php _e('Thank you for applying to sell with Kate & Crew! We’re reviewing your application and will get back to you within the next three business days', 'woocommerce'); ?>.</p>
    <p><?php _e('If you have any further questions or concerns, please contact seller support at ', 'woocommerce'); ?> <a href="mailto:support@kateandcrew.com">support@kateandcrew.com</a>.</p>
<?php 
	/**
	* Show user-defined additional content - this is set in each email's settings.
	*/
	if ( $additional_content ) {
		echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	}
}else{
?>
	<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></p>
	<p><?php printf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'woocommerce' ), esc_html( $blogname ), '<strong>' . esc_html( $user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
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

