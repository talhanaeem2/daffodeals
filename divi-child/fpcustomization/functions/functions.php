<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

if (session_status() == PHP_SESSION_NONE) { session_start(); }

//FILES INCLUDE HERE
require_once(BASEL_THEMEROOT.'/fpcustomization/functions/product-functions.php');
require_once(BASEL_THEMEROOT.'/fpcustomization/functions/store-functions.php');
require_once(BASEL_THEMEROOT.'/fpcustomization/functions/dashboard-functions.php');
require_once(BASEL_THEMEROOT.'/fpcustomization/functions/admin-functions.php');

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue scripts
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'dd_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'dd_enqueue_scripts', 99 );

	function dd_enqueue_scripts() 
  {
		global $wp;
    $version = '';
    
		//ENQUEQE STYLE 
    wp_enqueue_style( 'custom-bootstrap', BASEL_THEME_DIR . '/fpcustomization/css/bootstrap.min.css?'.time(), array(), $version );
    wp_enqueue_style( 'custom-font-awesome', BASEL_THEME_DIR . '/fpcustomization/css/font-awesome.min.css?'.time(), array(), $version );
    wp_enqueue_style( 'custom-dashbaord', BASEL_THEME_DIR . '/fpcustomization/css/style.css?'.time(), array(), $version );
    wp_enqueue_style( 'custom-calendar', BASEL_THEME_DIR . '/fpcustomization/css/calendar.css?'.time(), array(), $version );


    //ENQUEQE SCRIPTS 
    if (isset($_GET['product_id']) && isset($_GET['action']) && $_GET['action'] == 'edit') {

        wp_enqueue_script('ckeditor', 'https://cdn.ckeditor.com/4.13.0/standard-all/ckeditor.js', array(), '', 'true' );
        wp_enqueue_script('image-edit', 'https://daffodeals.com/wp-admin/js/image-edit.min.js?'.time(), array(), '', 'true' );
        wp_enqueue_script('vendor-cropper', BASEL_THEME_DIR . '/fpcustomization/js/cropper.min.js', array(), '', '' );
        wp_enqueue_style( 'vendor-cropper', BASEL_THEME_DIR . '/fpcustomization/css/cropper.min.css', array(), $version );

        wp_enqueue_script('dd-deal', BASEL_THEME_DIR . '/fpcustomization/js/edit-deal.js?'.time(), array(), '', 'true' );
    }
    if (isset( $wp->query_vars['new-product'])) {
        wp_enqueue_script('dd-deal', BASEL_THEME_DIR . '/fpcustomization/js/dd-add-deal.js?'.time(), array(), '', 'true' );
    }

    //DASHBOARD JS
    if (is_page('dashboard')) {
        
        wp_enqueue_script('vendor-popper', BASEL_THEME_DIR . '/fpcustomization/js/popper.min.js?'.time(), array(), '', '' );
        wp_enqueue_script('vendor-bootstrap', BASEL_THEME_DIR . '/fpcustomization/js/bootstrap.min.js?'.time(), array(), '', '' );
       /* wp_enqueue_script('vendor-croppie', BASEL_THEME_DIR . '/fpcustomization/js/croppie.js?'.time(), array(), '', '' );
        wp_enqueue_style( 'vendor-croppie', BASEL_THEME_DIR . '/fpcustomization/css/croppie.css?'.time(), array(), $version );*/

        wp_enqueue_script('vendor-dashboard', BASEL_THEME_DIR . '/fpcustomization/js/dashboard.js?'.time(), array(), '', '' );
    }

    if (isset( $wp->query_vars['settings'])) {
      wp_enqueue_script('vendor-cropper', BASEL_THEME_DIR . '/fpcustomization/js/cropper.min.js', array(), '', '' );
      wp_enqueue_style( 'vendor-cropper', BASEL_THEME_DIR . '/fpcustomization/css/cropper.min.css', array(), $version );
    }
    

    //FRONTEND JS
    if (!is_page('dashboard')) {
        wp_enqueue_script('vendor-frontend', BASEL_THEME_DIR . '/fpcustomization/js/frontend.js?'.time(), array(), '', 'true');
        wp_localize_script( 'vendor-frontend', 'dd_ajax',
          array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'dd-nonce' )
          )
        );
    }

	}
}
//ADD SCRIPT IN HEADER
add_action('wp_head', 'dd_add_script');
function dd_add_script(){
  ?>
  <script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
  </script>
  <?php 
}

/*
* ADD BODY CLASS FOR DASHBOARD INNER PAGES.
*/ 
add_filter( 'body_class', 'dd_custom_class', 99 );
function dd_custom_class( $classes ) {
	global $wp;
    if (is_page('dashboard') &&  isset( $wp->query_vars['active-product'] )) {
        $classes[] = 'dashboard-active-product';
    }
    if (is_page('dashboard') && isset( $wp->query_vars['shipping-product'] )) {
        $classes[] = 'dashboard-shipping-product';
    }
    if (is_page('dashboard') && isset( $wp->query_vars['pending-product'] )) {
        $classes[] = 'dashboard-pending-product';
    }
    if (is_page('dashboard') &&  isset( $wp->query_vars['products'] )) {
    	if (isset($_GET['product_id'])) {
        	$classes[] = 'dashboard-edit-product';    		
    	}else{
        	$classes[] = 'dashboard-products';
    	}
    }
    if (is_page('dashboard') &&  isset( $wp->query_vars['new-product'] )) {
        $classes[] = 'dashboard-new-product';
    }

    return $classes;
}


/*
* CREATE NEW TABLES HERE
*/
add_action('wp', 'dd_create_new_tables');

function dd_create_new_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    //PRODUCT ATTRIBUTES
    $product_attributes = $wpdb->prefix . "product_attributes"; 

    $sql = "CREATE TABLE IF NOT EXISTS {$product_attributes} (
      ID bigint(20) NOT NULL AUTO_INCREMENT,
      product_id bigint(20) NOT NULL,
      title varchar(250) DEFAULT '' NOT NULL,
      personalization varchar(3) DEFAULT '' NOT NULL,
      char_allowed int(3) NOT NULL,
      buy_limit int(3) NOT NULL,
      variations varchar(250) DEFAULT '' NOT NULL,
      quantity_status varchar(15) DEFAULT '' NOT NULL,
      combine_status varchar(3) DEFAULT '' NOT NULL,
      PRIMARY KEY  (ID)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    //PRODUCT VARIATIONS
    $product_variations = $wpdb->prefix . "product_variations"; 

    $sql = "CREATE TABLE IF NOT EXISTS {$product_variations} (
      ID bigint(20) NOT NULL AUTO_INCREMENT,     
      title varchar(250) DEFAULT '' NOT NULL,
      squ varchar(250) DEFAULT '' NOT NULL,
      qty int(5) NOT NULL,
      sold_qty int(5) NOT NULL,
      attr_id bigint(20) NOT NULL,
      attribute_group varchar(250) DEFAULT '' NOT NULL,
      variation_group varchar(250) DEFAULT '' NOT NULL,
      variation_type varchar(30) DEFAULT '' NOT NULL,
      PRIMARY KEY  (ID)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );


    //DEAL COMMENTS
    $deal_comments = $wpdb->prefix . "deal_comments";     
    $sql = "CREATE TABLE IF NOT EXISTS {$deal_comments} (
      ID bigint(20) NOT NULL AUTO_INCREMENT,     
      post_id bigint(20) NOT NULL,     
      sender bigint(20) NOT NULL,     
      receiver text NOT NULL,     
      comment text NOT NULL,
      attachment text DEFAULT '' NOT NULL,
      comment_date datetime NOT NULL,      
      seen text NOT NULL,       
      PRIMARY KEY  (ID)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    //ORDER TRACKING
    $order_tracking = $wpdb->prefix . "order_tracking";     
    $sql = "CREATE TABLE IF NOT EXISTS {$order_tracking} (
      ID bigint(20) NOT NULL AUTO_INCREMENT,     
      order_id bigint(20) NOT NULL,     
      trackingnumber bigint(11) NOT NULL,     
      carrier varchar(100) DEFAULT '' NOT NULL,     
      shippingdate date NOT NULL,      
      trackingdate date NOT NULL,
      PRIMARY KEY  (ID)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    //ORDER SHIPSTATION AUTH KEYS
    $shipstation_auth_keys = $wpdb->prefix . "shipstation_auth_keys";     
    $sql = "CREATE TABLE IF NOT EXISTS {$shipstation_auth_keys} (
      ID bigint(20) NOT NULL AUTO_INCREMENT,     
      user_id bigint(20) NOT NULL,     
      auth_key text NOT NULL,
      PRIMARY KEY  (ID)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    //SELLER LEDGER Entry
    $ledger_entry = $wpdb->prefix . "ledger_entry";     
    $sql = "CREATE TABLE IF NOT EXISTS {$ledger_entry} (
      ID bigint(20) NOT NULL AUTO_INCREMENT,     
      seller_id bigint(20) NOT NULL,     
      admin_id bigint(20) NOT NULL,     
      deal_id bigint(20) NOT NULL,     
      deal_titel varchar(250) DEFAULT '' NOT NULL,     
      entry_type varchar(250) DEFAULT '' NOT NULL,     
      amount float NOT NULL,     
      shipping_amount float NOT NULL,     
      seller_commission float NOT NULL,     
      admin_commission float NOT NULL,     
      admin_percentage float NOT NULL,     
      commission_type varchar(50) DEFAULT '' NOT NULL,     
      description text NOT NULL,
      status varchar(50) DEFAULT '' NOT NULL,
      revenue_date datetime NOT NULL,
      created_date datetime NOT NULL,
      PRIMARY KEY  (ID)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
}

/**
 * Register a custom post type. 
 */
function dd_custom_post_type() {
    $labels = array(
        'name'                  => _x( 'Contracts', 'Post type general name', 'woocommerce' ),
        'singular_name'         => _x( 'Contract', 'Post type singular name', 'woocommerce' ),
        'menu_name'             => _x( 'Contracts', 'Admin Menu text', 'woocommerce' ),
        'name_admin_bar'        => _x( 'Contract', 'Add New on Toolbar', 'woocommerce' ),
        'add_new'               => __( 'Add New', 'woocommerce' ),
        'add_new_item'          => __( 'Add New Contract', 'woocommerce' ),
        'new_item'              => __( 'New Contract', 'woocommerce' ),
        'edit_item'             => __( 'Edit Contract', 'woocommerce' ),
        'view_item'             => __( 'View Contract', 'woocommerce' ),
        'all_items'             => __( 'All Contracts', 'woocommerce' ),
        'search_items'          => __( 'Search Contracts', 'woocommerce' ),
        'parent_item_colon'     => __( 'Parent Contracts:', 'woocommerce' ),
        'not_found'             => __( 'No contracts found.', 'woocommerce' ),
        'not_found_in_trash'    => __( 'No contracts found in Trash.', 'woocommerce' ),       
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'contract' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-welcome-write-blog',
        'supports'           => array( 'title', 'editor', 'page-attributes'),
    );
 
    register_post_type( 'contract', $args );

    $labels = array(
        'name'                  => _x( 'FAQs', 'Post type general name', 'woocommerce' ),
        'singular_name'         => _x( 'FAQ', 'Post type singular name', 'woocommerce' ),
        'menu_name'             => _x( 'FAQs', 'Admin Menu text', 'woocommerce' ),
        'name_admin_bar'        => _x( 'FAQ', 'Add New on Toolbar', 'woocommerce' ),
        'add_new'               => __( 'Add New', 'woocommerce' ),
        'add_new_item'          => __( 'Add New FAQ', 'woocommerce' ),
        'new_item'              => __( 'New FAQ', 'woocommerce' ),
        'edit_item'             => __( 'Edit FAQ', 'woocommerce' ),
        'view_item'             => __( 'View FAQ', 'woocommerce' ),
        'all_items'             => __( 'All FAQs', 'woocommerce' ),
        'search_items'          => __( 'Search FAQs', 'woocommerce' ),
        'parent_item_colon'     => __( 'Parent FAQs:', 'woocommerce' ),
        'not_found'             => __( 'No contracts found.', 'woocommerce' ),
        'not_found_in_trash'    => __( 'No contracts found in Trash.', 'woocommerce' ),       
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'faqs' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-welcome-write-blog',
        'supports'           => array( 'title', 'editor', 'page-attributes'),
    );
 
    register_post_type( 'faqs', $args );
}
 
add_action( 'init', 'dd_custom_post_type' );




//SET EMAIL CONTENT TYPE
add_filter( 'wp_mail_content_type','email_html_content_type' );
function email_html_content_type() {
    return 'text/html';
}

//SET EMAIL FROM NAME
add_filter( 'wp_mail_from_name','email_wp_mail_from_name' );
function email_wp_mail_from_name() {
    return '=?UTF-8?B?'.base64_encode(htmlspecialchars_decode(get_option('blogname'), ENT_NOQUOTES)).'?=';
}

//SET EMAIL FROM NAME
add_filter( 'wp_mail_from','email_wp_mail_from' );
function email_wp_mail_from() {
    return 'info@daffodeals.com';
}


//SEND EMAIL FUNCTION
function dd_send_email_handler($receiver='', $subject, $content, $email_heading, $admin=''){
  
  $mailer = WC()->mailer();
  $email = new WC_Email(); 

  if (!empty($receiver)) {
      $message       = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $content ) ) );     
      wp_mail($receiver, $subject, $message);
  }

  if (!empty($admin)) {
      $blogname = get_option('blogname');
      $admin_email = get_option('admin_email');      
      $message       = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $content ) ) );     
      wp_mail($admin_email, $subject, $message);
  }
}

//TIME AGO FUNCTION HERE.
function get_timeago( $ptime, $format = '' )
{
    $estimate_time = time() - $ptime;
    $seconds = $estimate_time;
    $minutes      = round($seconds / 60 );
    $hours           = round($seconds / 3600);
    $days          = round($seconds / 86400); 
    $weeks          = round($seconds / 604800);   
    $months          = round($seconds / 2629440); 
    $years          = round($seconds / 31553280);  

    if( $estimate_time < 1 )
    {
        return '1 second ago';
    }

    $condition = array( 
                12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $estimate_time / $secs;

        if( $d >= 1 )
        {
            $r = round( $d );
            if ($format == 'reviews') {
              if ($days <= 7) {
                return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
              }else{
                return date('M jS', $ptime);
              }              
            }elseif ($format == 'dashboard') {
              if ($days <= 7) {
                return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
              }else{
                return date('m/d/Y', $ptime);
              }              
            }elseif ($format == 'store') {
              if ($days <= 7) {
                return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
              }elseif ($months <=12) {
                return date('d M', $ptime);
              }else{
                return date('m/d/Y', $ptime);
              }
            }else{
              return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
            }
        }
    }
}




/** 
 * Use to convert large positive numbers in to short form like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
 */
function number_format_short( $n ) {
  if ($n >= 0 && $n < 1000) {
    // 1 - 999
    $n_format = floor($n);
    $suffix = '';
  } else if ($n >= 1000 && $n < 1000000) {
    // 1k-999k
    $n_format = floor($n / 1000);
    $suffix = 'K+';
  } else if ($n >= 1000000 && $n < 1000000000) {
    // 1m-999m
    $n_format = floor($n / 1000000);
    $suffix = 'M+';
  } else if ($n >= 1000000000 && $n < 1000000000000) {
    // 1b-999b
    $n_format = floor($n / 1000000000);
    $suffix = 'B+';
  } else if ($n >= 1000000000000) {
    // 1t+
    $n_format = floor($n / 1000000000000);
    $suffix = 'T+';
  }

  return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
}

//SELLER SIGNUP FORM VALIDATIONS
add_filter( 'woocommerce_registration_errors', 'dd_registration_errors', 1 , 30);
function dd_registration_errors($errors){
  $error = 0;
  if (isset($_POST['fname']) && empty($_POST['fname'])) {
    wc_add_notice( __( 'Please enter first name.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['lname']) && empty($_POST['lname'])) {
    wc_add_notice( __( 'Please enter last name.', 'woocommerce' ), 'error' );
    $error = 1;
  }  
  if (isset($_POST['shopname']) && empty($_POST['shopname'])) {
    wc_add_notice( __( 'Please enter company name.', 'woocommerce' ), 'error' );
    $error = 1;
  }

  $username = sanitize_text_field( $_POST['shopname'] );
  if (isset($_POST['shopname']) && username_exists( $username ) ) {
    wc_add_notice( __( 'Company name already used in the username. Please enter another.', 'woocommerce' ), 'error' );
    $error = 1;
  }

  if (isset($_POST['shopname']) && !empty($_POST['shopname'])) {
    $url_slug = isset( $_POST['shopname'] ) ? sanitize_text_field( $_POST['shopname'] ) : '';
        $check    = true;
    $user     = get_user_by( 'slug', $url_slug );
   /* if ( '' != $user ) {
      wc_add_notice( __( 'Company/Business name already used. Please enter another.', 'woocommerce' ), 'error' );
      $error = 1;
    }    */
  }
  if (isset($_POST['shopurl']) && empty($_POST['shopurl'])) {
    wc_add_notice( __( 'Please enter shop URL.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['businessType']) && empty($_POST['businessType'])) {
    wc_add_notice( __( 'Please enter business formation.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  

  if (isset($_POST['employerIdentificationNumber']) && empty($_POST['employerIdentificationNumber'])) {
    wc_add_notice( __( 'Please enter employer identification number.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['streetAddress']) && empty($_POST['streetAddress'])) {
    wc_add_notice( __( 'Please enter address.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['locality']) && empty($_POST['locality'])) {
    wc_add_notice( __( 'Please enter city.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['region']) && empty($_POST['region'])) {
    wc_add_notice( __( 'Please enter state.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['postalCode']) && empty($_POST['postalCode'])) {
    wc_add_notice( __( 'Please enter zip.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['phone']) && empty($_POST['phone'])) {
    wc_add_notice( __( 'Please enter phone number.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['phone']) && empty($_POST['phone'])) {
    wc_add_notice( __( 'Please enter phone number.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['website_url']) && empty($_POST['website_url'])) {
    wc_add_notice( __( 'Please enter website URL.', 'woocommerce' ), 'error' );
    $error = 1;
  }
 
  if (isset($_POST['TimeInBusiness']) && empty($_POST['TimeInBusiness'])) {
    wc_add_notice( __( 'Please choose a time in business.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  if (isset($_POST['AnnualRevenueLastYear']) && empty($_POST['AnnualRevenueLastYear'])) {
    wc_add_notice( __( 'Please choose a annual revenue last year.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  
  if (isset($_POST['productDescription']) && empty($_POST['productDescription'])) {
    wc_add_notice( __( 'Please enter tell us about your products.', 'woocommerce' ), 'error' );
    $error = 1;
  } 
  $SampleProductImages = $_POST['SampleProductImages'];
  if ($_POST['role'] == 'seller' && !isset($_POST['SampleProductImages'])) {
    wc_add_notice( __( 'Please choose product sample images.', 'woocommerce' ), 'error' );
    $error = 1;
  }
  
  if (isset($_POST['SampleProductImages']) && count($SampleProductImages) < 2) {
    wc_add_notice( __( 'Please choose minimum 2 sample images.', 'woocommerce' ), 'error' );
    $error = 1;
  }

  if ($error == 1) {
    $errors->add( 'signuperror', '');
  }
  
  return $errors;
}

//SAVE SELLER SIGNUP FORM DATA HERE.
add_action( 'woocommerce_created_customer', 'dd_created_customer', 35);
function dd_created_customer($customer_id){
  global $wpdb;
  if (isset($_POST['fname']) && !empty($_POST['fname'])) {
    $fname = sanitize_text_field($_POST['fname']);
    update_user_meta($customer_id, 'first_name', $fname);
  }
  if (isset($_POST['lname']) && !empty($_POST['lname'])) {
    $lname = sanitize_text_field($_POST['lname']);
    update_user_meta($customer_id, 'last_name', $lname);
  }

  $shopname = sanitize_text_field($_POST['shopname']);
  $businessType = sanitize_text_field($_POST['businessType']);
  $employerIdentificationNumber = sanitize_text_field($_POST['employerIdentificationNumber']);
  $streetAddress = sanitize_text_field($_POST['streetAddress']);
  $streetAddress2 = sanitize_text_field($_POST['streetAddress2']);
  $locality = sanitize_text_field($_POST['locality']);
  $region = sanitize_text_field($_POST['region']);
  $postalCode = sanitize_text_field($_POST['postalCode']);
  $phone = sanitize_text_field($_POST['phone']);
  $website_url = sanitize_text_field($_POST['website_url']);
  $TimeInBusiness = sanitize_text_field($_POST['TimeInBusiness']);
  $AnnualRevenueLastYear = sanitize_text_field($_POST['AnnualRevenueLastYear']); 
  $ProductDescription = sanitize_text_field($_POST['ProductDescription']);
  $SampleProductImages = $_POST['SampleProductImages'];
  
  $images = array();
  if (!empty($SampleProductImages)) {
    foreach ($SampleProductImages as $key => $image) {
      $images[] = upload_64_data_image_to_thumbnail($image);
    }
  }  
  
  $dokan_address = array('street_1' => $streetAddress, 'street_2' => $streetAddress2, 'city' => $locality, 'zip' => $postalCode, 'state' => $region);

  $dokan_settings = array(
    'BusinessFormation' => $businessType, 
    'employerIdentificationNumber' => $employerIdentificationNumber, 
    'address' => $dokan_address,
    'phone' => $phone,
    'owner_website'  => $website_url,
    'ProductCateogry' => $ProductCateogry,
    'TimeInBusiness' => $TimeInBusiness,
    'AnnualRevenueLastYear' => $AnnualRevenueLastYear,
    'IntendedDiscountRate' => $IntendedDiscountRate,
    'OfferFreeShipping' => $OfferFreeShipping,
    'ProductDescription' => $ProductDescription,
    'SampleProductImages' => $images,
  );
  if (!empty($dokan_settings) && $_POST['role'] == 'seller') {  
    $existing_dokan_settings = get_user_meta( $customer_id, 'dokan_profile_settings', true );
    $prev_dokan_settings     = ! empty( $existing_dokan_settings ) ? $existing_dokan_settings : array(); 
    $dokan_settings = array_merge( $prev_dokan_settings, $dokan_settings );
    update_user_meta($customer_id, 'dokan_profile_settings', $dokan_settings );
    
    if (isset($_POST['password']) && !empty($_POST['password'])) {
      update_user_meta($customer_id, '_vendor_password', sanitize_text_field($_POST['password']));
    }
    
    $username = sanitize_text_field( $_POST['shopname'] );
    $wpdb->update($wpdb->users, array('user_login' => $username), array('ID' => $customer_id));
  }
  
}


function upload_64_data_image_to_thumbnail($base_Image){     
  $base_Image = $base_Image;
  $upload_dir = wp_upload_dir();
  $file_url = $upload_dir['url'];
  $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
  $image_parts = explode(";base64,",$base_Image);
  $decoded = base64_decode($image_parts[1]);
  $filename = 'sample.png';
  $hashed_filename = md5( $filename . microtime() ) . '_' . $filename;   
  $file_url = $file_url.'/'.$hashed_filename;
  $image_upload = file_put_contents( $upload_path . $hashed_filename, $decoded );  
  return $file_url;
}


/**
 * Handles the become a vendor form
 *
 * @return void
 */
if ( !function_exists( 'custom_become_vendor_handler' ) ) {

    function custom_become_vendor_handler() {
        if ( isset( $_POST['account_migration'] ) && wp_verify_nonce( $_POST['account_migration_nonce'], 'account_migration_action' ) ) {

            $errors = new WP_Error();
            global $wpdb;

            $error = 0;
            if (isset($_POST['fname']) && empty($_POST['fname'])) {
              wc_add_notice( __( 'Please enter first name.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['lname']) && empty($_POST['lname'])) {
              wc_add_notice( __( 'Please enter last name.', 'woocommerce' ), 'error' );
              $error = 1;
            }  
            if (isset($_POST['shopname']) && empty($_POST['shopname'])) {
              wc_add_notice( __( 'Please enter company name.', 'woocommerce' ), 'error' );
              $error = 1;
            }

            $username = sanitize_text_field( $_POST['shopname'] );
            if (isset($_POST['shopname']) && username_exists( $username ) ) {
              wc_add_notice( __( 'Company name already used in the username. Please enter another.', 'woocommerce' ), 'error' );
              $error = 1;
            }


           /* $email = sanitize_text_field( $_POST['email'] );
            if (isset($_POST['email']) && email_exists( $email ) ) {
              wc_add_notice( __( 'Email already used. Please enter another.', 'woocommerce' ), 'error' );
              $error = 1;
            }*/

            if (isset($_POST['shopname']) && !empty($_POST['shopname'])) {
              $url_slug = isset( $_POST['shopname'] ) ? sanitize_text_field( $_POST['shopname'] ) : '';
                  $check    = true;
              $user     = get_user_by( 'slug', $url_slug );            
            }

            if (isset($_POST['shopurl']) && empty($_POST['shopurl'])) {
              wc_add_notice( __( 'Please enter shop URL.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['businessType']) && empty($_POST['businessType'])) {
              wc_add_notice( __( 'Please enter business formation.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            

            if (isset($_POST['employerIdentificationNumber']) && empty($_POST['employerIdentificationNumber'])) {
              wc_add_notice( __( 'Please enter employer identification number.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['streetAddress']) && empty($_POST['streetAddress'])) {
              wc_add_notice( __( 'Please enter address.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['locality']) && empty($_POST['locality'])) {
              wc_add_notice( __( 'Please enter city.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['region']) && empty($_POST['region'])) {
              wc_add_notice( __( 'Please enter state.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['postalCode']) && empty($_POST['postalCode'])) {
              wc_add_notice( __( 'Please enter zip.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['phone']) && empty($_POST['phone'])) {
              wc_add_notice( __( 'Please enter phone number.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['phone']) && empty($_POST['phone'])) {
              wc_add_notice( __( 'Please enter phone number.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['website_url']) && empty($_POST['website_url'])) {
              wc_add_notice( __( 'Please enter website URL.', 'woocommerce' ), 'error' );
              $error = 1;
            }
           
            if (isset($_POST['TimeInBusiness']) && empty($_POST['TimeInBusiness'])) {
              wc_add_notice( __( 'Please choose a time in business.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            if (isset($_POST['AnnualRevenueLastYear']) && empty($_POST['AnnualRevenueLastYear'])) {
              wc_add_notice( __( 'Please choose a annual revenue last year.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            
            if (isset($_POST['ProductDescription']) && empty($_POST['ProductDescription'])) {
              wc_add_notice( __( 'Please enter tell us about your products.', 'woocommerce' ), 'error' );
              $error = 1;
            } 
            $SampleProductImages = $_POST['SampleProductImages'];
            if (!isset($_POST['SampleProductImages'])) {
              wc_add_notice( __( 'Please choose product sample images.', 'woocommerce' ), 'error' );
              $error = 1;
            }
            
            if (isset($_POST['SampleProductImages']) && count($SampleProductImages) < 2) {
              wc_add_notice( __( 'Please choose minimum 2 sample images.', 'woocommerce' ), 'error' );
              $error = 1;
            }

            if ($error == 1) {
              $errors->add( 'signuperror', '');
            }
           
            if (!empty($errors->get_error_code())) {
              return $errors;
            }
           
            if (empty( $error)) {               

                $customer_id = get_current_user_id();
               if (isset($_POST['fname']) && !empty($_POST['fname'])) {
                  $fname = sanitize_text_field($_POST['fname']);
                  update_user_meta($customer_id, 'first_name', $fname);
                }
                if (isset($_POST['lname']) && !empty($_POST['lname'])) {
                  $lname = sanitize_text_field($_POST['lname']);
                  update_user_meta($customer_id, 'last_name', $lname);
                }

                $shopname = sanitize_text_field($_POST['shopname']);
                $businessType = sanitize_text_field($_POST['businessType']);
                $employerIdentificationNumber = sanitize_text_field($_POST['employerIdentificationNumber']);
                $streetAddress = sanitize_text_field($_POST['streetAddress']);
                $streetAddress2 = sanitize_text_field($_POST['streetAddress2']);
                $locality = sanitize_text_field($_POST['locality']);
                $region = sanitize_text_field($_POST['region']);
                $postalCode = sanitize_text_field($_POST['postalCode']);
                $phone = sanitize_text_field($_POST['phone']);
                $website_url = sanitize_text_field($_POST['website_url']);
                $TimeInBusiness = sanitize_text_field($_POST['TimeInBusiness']);
                $AnnualRevenueLastYear = sanitize_text_field($_POST['AnnualRevenueLastYear']); 
                $ProductDescription = sanitize_text_field($_POST['ProductDescription']);
                $SampleProductImages = $_POST['SampleProductImages'];
                
                $images = array();
                if (!empty($SampleProductImages)) {
                  foreach ($SampleProductImages as $key => $image) {
                    $images[] = upload_64_data_image_to_thumbnail($image);
                  }
                }  
                
                $dokan_address = array('street_1' => $streetAddress, 'street_2' => $streetAddress2, 'city' => $locality, 'zip' => $postalCode, 'state' => $region);

                $username = sanitize_text_field( $_POST['shopname'] );
                $dokan_settings = array(
                  'store_name'     => $username,
                  'BusinessFormation' => $businessType, 
                  'employerIdentificationNumber' => $employerIdentificationNumber, 
                  'address' => $dokan_address,
                  'phone' => $phone,
                  'owner_website'  => $website_url,
                  'ProductCateogry' => $ProductCateogry,
                  'TimeInBusiness' => $TimeInBusiness,
                  'AnnualRevenueLastYear' => $AnnualRevenueLastYear,
                  'IntendedDiscountRate' => $IntendedDiscountRate,
                  'OfferFreeShipping' => $OfferFreeShipping,
                  'ProductDescription' => $ProductDescription,
                  'SampleProductImages' => $images,
                );
                if (!empty($dokan_settings)) {
                  $user   = get_userdata( get_current_user_id() );
                  // Remove role
                  $user->remove_role( 'customer' );
                  // Add role
                  $user->add_role( 'seller' );

                  $existing_dokan_settings = get_user_meta( $customer_id, 'dokan_profile_settings', true );
                  $prev_dokan_settings     = ! empty( $existing_dokan_settings ) ? $existing_dokan_settings : array(); 
                  $dokan_settings = array_merge( $prev_dokan_settings, $dokan_settings );
                  update_user_meta($customer_id, 'dokan_profile_settings', $dokan_settings );
                  
                  update_user_meta( $customer_id, 'dokan_store_name', $username);

                  if ( dokan_get_option( 'new_seller_enable_selling', 'dokan_selling', 'on' ) == 'off' ) {
                      update_user_meta( $customer_id, 'dokan_enable_selling', 'no' );
                  } else {
                      update_user_meta( $customer_id, 'dokan_enable_selling', 'yes' );
                  }
                  $publishing = dokan_get_option( 'product_status', 'dokan_selling' );
                  update_user_meta( $customer_id, 'dokan_publishing', $publishing );

                  $wpdb->update($wpdb->users, array('user_login' => $username), array('ID' => $customer_id));
                  become_a_vendor_notification($user);

                  wc_add_notice( __( 'Congratulations - Your Application To Become A Vendor Has Been Submitted - Stay tuned to your email for additional instructions!', 'woocommerce' ));
                  $_POST = array();
                  return true;
                }
            }
        }
    }

}
add_action( 'template_redirect', 'custom_become_vendor_handler' );


//SEND BECOME A VENDOR EMAIL TO ADMIN
function become_a_vendor_notification($user){    

    $customer_name = $user->first_name.'&nbsp; '.$user->last_name;    
    $customer_email = $user->user_email;
    $profile_url = add_query_arg( 'user_id', $user->ID, self_admin_url( 'user-edit.php' ) );;
    $company_name = get_user_meta($user->ID, 'dokan_store_name', true);
    $site_name = get_option('blogname'); 

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['become_vendor_sub']; 
    $email_heading  = $wstheme_options['become_vendor_heading']; 
    $user_message = $wstheme_options['become_vendor_temp'];

    $data = array('customer_name'=> $customer_name,'customer_email' => $customer_email,'profile_url' => $profile_url,'company_name' => $company_name,'site_name' => $site_name);

    $search_key_array = array('[customer_name]' =>'customer_name','[customer_email]' => 'customer_email','[profile_url]' => 'profile_url', '[company_name]' => 'company_name','[site_name]' => 'site_name');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message);  
    dd_send_email_handler('', $user_subject, $user_message, $email_heading, true);
}


//ADD READ MORE TAG
//add_shortcode('read', 'read_more_sortcode_callback');
function read_more_sortcode_callback($atts, $content = null) {
  extract(shortcode_atts(array(
    'more' => 'READ MORE',
    'less' => 'READ LESS'
  ), $atts));

  mt_srand((double)microtime() * 1000000);
  $rnum = mt_rand();
   
  $new_string = '<span><a onclick="read_toggle(' . $rnum . ', \'' . get_option('rm_text') . '\', \'' . get_option('rl_text') . '\'); return false;" class="read-link" id="readlink' . $rnum . '" style="readlink" href="#">' . get_option('rm_text') . '</a></span>' . "\n";
  $new_string .= '<div class="read_div" id="read' . $rnum . '" style="display: none;">' . do_shortcode($content) . '</div>';

  return $new_string;
}

//CHANGE USER DASHBORD LINK 
function ChaneDashboardLink($links){
  $account_link =  site_url('/dashboard/');
  if(is_user_logged_in() && (current_user_can('administrator') || current_user_can('seller')) ) {
    unset($links[0]['url']);
    $links[0]['url'] = $account_link;
  }
  return $links;
}
//add_filter( 'basel_get_header_links', 'ChaneDashboardLink', 15);

//Woo change subjet of NEW ACCOUNT
function dd_email_subject_customer_new_account($subjet,$user){
  if ( in_array( 'seller', (array) $user->roles ) ) {
    return 'Your daffodeals application has been received!';
  }
  return $subjet;
}
add_filter( 'woocommerce_email_subject_customer_new_account', 'dd_email_subject_customer_new_account', 30, 2);

//REMOVE ENDED PRODUCT FROM CART
add_action( 'template_redirect', 'dd_remove_ended_product_from_cart' ); 
function dd_remove_ended_product_from_cart() {
  if ( is_admin() ) return;
  
  if ( WC()->cart->get_cart_contents_count() != 0 ) {
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
      $product_id = $cart_item['product_id'];
      $ywgc_product_id = isset($cart_item['ywgc_product_id'])?$cart_item['ywgc_product_id']:'';
      $date_data = get_deal_time_difference($product_id); 
      $dayleft = $date_data['dayleft'];
      if (empty($dayleft) && empty($ywgc_product_id)) {
        WC()->cart->remove_cart_item( $cart_item_key );
      }
    }
  }
}

//REMOVE CUSTOMER DASHBOARD MENU HERE
function woo_account_menu_items( $items ) { 
 //print_r($items);
  unset($items['following']);
  unset($items['support-tickets']);
  return $items; 
} 
add_filter( 'woocommerce_account_menu_items', 'woo_account_menu_items', 10, 1 );

//SEND SUBSCRIPTION EMAIL TO ADMIN
function dd_sub_email_send() { 
  $email = sanitize_text_field($_POST['sub_email']);
  $user_subject = 'Daffodeals Subscribe';
  $email_heading = 'Daffodeals Subscribe';
  $user_message = '<p>A new daffodeals subscriber recieved from below email.</p>
  <p>Email: '.$email.' </p>';
  dd_send_email_handler('', $user_subject, $user_message, $email_heading, true);
  echo 'send';
  die;
} 
add_action( 'wp_ajax_dd_sub_email', 'dd_sub_email_send');
add_action( 'wp_ajax_nopriv_dd_sub_email', 'dd_sub_email_send');

/*HOME PAGE PRODUCTS*/
function home_infinity_products(){ 

  $paged = 1;

  $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 12, 'paged' => $paged, 'tax_query' => array(array('taxonomy' => 'product_cat', 'field' => 'id', 'terms' => array( 48, 49,53, 57,64,69 ), 'operator' => 'IN')));
  
  $pro_query = new WP_Query( $args );

  ob_start();
    
    if ( $pro_query->have_posts() ) :
      woocommerce_product_loop_start();
      while ( $pro_query->have_posts() ) : $pro_query->the_post();
          wc_get_template_part( 'content', 'product' ); 
      endwhile;
      woocommerce_product_loop_end();
    endif;
    
    if ($pro_query->found_posts > 12) {
      echo '<div class="home_scroll_products"></div>';
      echo '<div class="loadingicon hide"><div class="lader-fix">
      <div class="loader">Loading...</div>
      </div></div>';
    }
    // Reset Post Data
    wp_reset_postdata();

  $popular_collections = ob_get_clean();

  return $popular_collections;
         
} 
add_shortcode('home_products','home_infinity_products');

add_action( 'wp_ajax_home_more_product', 'home_scroll_products');
add_action( 'wp_ajax_nopriv_home_more_product', 'home_scroll_products');
function home_scroll_products(){ 

  $paged = $_POST['page'];

 $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 12, 'paged' => $paged, 'tax_query' => array(array('taxonomy' => 'product_cat', 'field' => 'id', 'terms' => array( 48, 49,53, 57,64,69 ), 'operator' => 'IN')));

  $pro_query = new WP_Query( $args );
  // The Loop
  if ( $pro_query->have_posts() ){
    ob_start();
      woocommerce_product_loop_start();
        while ( $pro_query->have_posts() ) : $pro_query->the_post();
          wc_get_template_part( 'content', 'product' ); 
        endwhile;
      woocommerce_product_loop_end();      
    $popular_collections = ob_get_clean();
    // Reset Post Data
    wp_reset_postdata();
    echo $popular_collections; 
  }
  die;    
} 

