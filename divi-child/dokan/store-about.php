<?php
/**
 * The Template for displaying all reviews.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

$vendor      = dokan()->vendor->get( get_query_var( 'author' ) );
$vendor_info = $vendor->get_shop_info();
$store_user               = dokan()->vendor->get( get_query_var( 'author' ) );
//print_r($store_user);
$store_info               = get_seller_profile_info($store_user->get_id());;
$social_info              = $store_user->get_social_profiles();
$store_tabs               = dokan_get_store_tabs( $store_user->get_id() );
$social_fields            = dokan_get_social_profile_fields();
$seller_owner_image = get_user_meta($store_user->get_id(), '_seller_owner_image',true);
$name = '';
$name = get_user_meta($store_user->get_id(), 'first_name', true);
$last_name =  get_user_meta($store_user->get_id(), 'last_name', true);
$name .= !empty($last_name)?' '.$last_name:'';
$user_registered = $store_user->data->user_registered;

get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<div id="primary" class="content-area dokan-single-store dokan-w12">
    <div id="dokan-content" class="site-content store-review-wrap woocommerce" role="main">

        <?php dokan_get_template_part( 'store-header' ); ?>

        <div class="profile-info-summery-wrapper dokan-clearfix">
            <div class="profile-info-summery">
                <div class="col-md-4">
                    <?php if(isset($seller_owner_image['image_url']) && $seller_owner_image['image_url']){  ?>
                        <div class="profile-img <?php echo esc_attr( $profile_img_class ); ?>">
                            <img src="<?php echo esc_url( $seller_owner_image['image_url'] ) ?>"
                                alt="<?php echo esc_attr( $store_user->get_shop_name() ) ?>"
                                size="150">
                        </div> 
                    <?php } ?>
                    <div class="details">
                        <?php if($store_info['dokan_owner']){  ?>
                            <p><?php echo $store_info['dokan_owner']; ?></p> 
                        <?php } ?>
                        <p>Seller Since <?php echo date('F Y',strtotime($user_registered)); ?></p>
                    </div>                   
                </div>

                <div class="col-md-8">
                    <?php if($store_info['about_owner']){  ?>
                        <h3>ABOUT</h3>
                        <p><?php echo wpautop($store_info['about_owner']); ?></p>
                    <?php } ?>

                    <?php 
                    if($store_info['owner_website'] && $store_info['websiteIsPrivate'] != 'false'){  ?>
                        <h3>Website</h3>
                        <p><a target="_blank" href="<?php echo add_http_to_url($store_info['owner_website']); ?>"><?php echo $store_info['owner_website']; ?></a> </p>
                     <?php } ?>
                </div> <!-- .profile-info -->
            </div><!-- .profile-info-summery -->
        </div><!-- .profile-info-summery-wrapper -->

    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->

<div class="dokan-clearfix"></div>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>
