<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

$home_url = home_url();
$active_class = ' class="active"';
global $wp, $wp_query;

$user_id  = dokan_get_current_user_id();
$store_user = dokan()->vendor->get( $user_id );
$profile_info = dokan_get_store_info($user_id);
$user = wp_get_current_user();
$name = $user->first_name;
$name .= !empty($user->last_name)?'&nbsp;'.$user->last_name:'';
//$seller_owner_image = get_user_meta($user_id, '_seller_owner_image',true);
?>
<header>
  <div class="">
     <div class="row">
        <div class="col-md-12">
           <div class="top_header">
              <div class="dashnav">
                 <a href="<?php echo site_url(); ?>" class="navbar-brand"><img class="img-fluid" src="<?php echo site_url(); ?>/wp-content/uploads/2020/05/dashlogo.png" alt="Logo" /></a>
              </div>
              <div class="seller-account">
                 <a href="javascript:void(0);" role="button" id="menuopen">
                  <?php if(isset($profile_info['vendor_logo']['url']) && !empty($profile_info['vendor_logo']['url'])){ ?>
                     <img src="<?php echo $profile_info['vendor_logo']['url']; ?>" alt="<?php echo esc_html( $store_user->get_shop_name() ); ?>">
                  <?php }else{ ?>
                    <img src="<?php echo site_url(); ?>/wp-content/themes/divi-child/fpcustomization/images/user_img.png" alt="<?php echo esc_html( $store_user->get_shop_name() ); ?>">
                  <?php } ?>
                   <?php echo esc_html( $store_user->get_shop_name() ); ?>
                   <i class="fa fa-chevron-down updown-down" aria-hidden="true"></i>
                 </a>
                
                 <ul style="display:none;">
                   <li> <a class="seller-profile-links" href="<?php echo site_url('my-account'); ?>"><?php esc_html_e( 'Personal Account', 'dokan-lite' ); ?></a></li>
                   <li><a class="seller-profile-links" href="<?php echo wp_logout_url( home_url() ) ?>"><?php esc_html_e( 'Log Out', 'dokan-lite' ); ?></a></li>
                 </ul>
              </div>                
           </div>
        </div>
     </div>
  </div>


<div class="profile_tabing">
      <div class="">
         <div class="row">
            <div class="col-md-12">
            <nav class="navbar navbar-expand-lg">
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">Menu</button> 
                    <div class="collapse navbar-collapse" id="collapsibleNavbar">
                      <ul class="navbar-nav">
                        <li class="nav-item">
                          <a class="nav-link <?php echo (!isset($wp->query_vars['settings']) && !isset($wp->query_vars['new-product']) && !isset($wp->query_vars['pending-product']) && !isset($wp->query_vars['tools']) && !isset($wp->query_vars['scheduled']) && !isset($wp->query_vars['payments']) && !isset($wp->query_vars['deals-ended']) && !isset($wp->query_vars['customer-reviews']) && !isset($_GET['product_id']) && !isset($wp->query_vars['orders']) && !isset($wp->query_vars['fulfillment']))? ' active' : ''; ?>" href="<?php echo esc_url( dokan_get_navigation_url() ); ?>">Overview</a> 
                        </li>
                        <li class="nav-item">
                          <a class="nav-link <?php echo (isset($wp->query_vars['new-product']) || isset($wp->query_vars['pending-product']) || isset($wp->query_vars['scheduled']) || isset($wp->query_vars['deals-ended']) || isset($_GET['product_id']) || isset($wp->query_vars['orders']) || isset($wp->query_vars['fulfillment']))? ' active' : ''; ?>" href="<?php echo esc_url( dokan_get_navigation_url( 'new-product' ) ); ?>">My Deals</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link <?php echo isset($wp->query_vars['payments'])? ' active' : ''; ?>" href="<?php echo esc_url( dokan_get_navigation_url( 'payments' ) ); ?>">Payments</a>
                        </li>    
                         <li class="nav-item">
                          <a class="nav-link <?php echo isset($wp->query_vars['customer-reviews'])? ' active' : ''; ?>" href="<?php echo esc_url( dokan_get_navigation_url( 'customer-reviews' ) ); ?>">Customer Reviews</a>
                        </li> 
                         <li class="nav-item">
                          <a class="nav-link <?php echo ( $wp->query_vars['settings'] == 'profile' || $wp->query_vars['settings'] == 'contract' )? ' active' : ''; ?>" href="<?php echo esc_url( dokan_get_navigation_url( 'settings/profile' ) ); ?>">My Profile</a>
                        </li> 
                         <li class="nav-item">
                          <a class="nav-link <?php echo isset($wp->query_vars['tools'])? ' active' : ''; ?>" href="<?php echo esc_url( dokan_get_navigation_url( 'tools' ) ); ?>">Tools</a>
                        </li> 
                      </ul>
                    </div>  
                  </nav>
              
            </div>
         </div>
      </div>
   </div>
   </header>
   <script>
    jQuery("body").click(function(){      
        jQuery(".seller-account ul").hide('300');
      });
     jQuery("#menuopen").click(function(event){
             event.stopPropagation();
        jQuery(".seller-account ul").slideToggle('300');
      });
   </script>