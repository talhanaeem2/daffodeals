<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

//ADD NEW STORE TAB HERE.
//add_filter( 'dokan_store_tabs', 'dd_dokan_store_tabs_callbakc', 30, 2);
function dd_dokan_store_tabs_callbakc($tabs, $store_id){
  $userstore = dokan_get_store_url( $store_id );
  $tabs['products']['title'] = 'Deals';
  $tabs['about'] = array(
            'title' => __( 'About', 'dokan-lite' ),
            'url'   => $userstore.'about');
  //print_r($tabs);
  return $tabs;
}

//ADD STORRE ABOUT REWRITE RULE
add_action( 'dokan_rewrite_rules_loaded', 'dd_rewrite_rules_loaded');
function dd_rewrite_rules_loaded($custom_store_url ){
  add_rewrite_rule( $custom_store_url . '/([^/]+)/about?$', 'index.php?' . $custom_store_url . '=$matches[1]&about=true', 'top' );
  add_rewrite_rule( $custom_store_url . '/([^/]+)/about/page/?([0-9]{1,})/?$', 'index.php?' . $custom_store_url . '=$matches[1]&paged=$matches[2]&about=true', 'top' );
}

//ADD ABOUT IN QUERY VARS
add_filter( 'query_vars', 'dd_register_query_var');
function dd_register_query_var( $vars ) {
  $vars[] = dokan_get_option( 'custom_store_url', 'dokan_general', 'store' );
  $vars[] = 'about'; 
  return $vars;
}

//ADD STORE ABOUT TEMPLATE
add_filter( 'template_include', 'dd_store_about_template',99);
function dd_store_about_template($template){
  global $wp_query;
  if ( get_query_var( 'about' ) ) {
    return dokan_locate_template( 'store-about.php' );
  }
  return $template;
}

/**
//store_deal query_filter 
**/
//add_filter( 'pre_get_posts', 'dd_store_query_filter');
function dd_store_query_filter( $query ) {
  global $wp_query;

  $author = get_query_var( $this->custom_store_url );

  if ( ! is_admin() && $query->is_main_query() && ! empty( $author ) ) {
    $seller_info = get_user_by( 'slug', $author );

    if ( ! $seller_info ) {
      return get_404_template();
    }
  }
}

//FILTER STORE REVIES HERE.
add_action( 'wp_ajax_store_filter_reviews', 'store_filter_reviews_callback' );
add_action( 'wp_ajax_nopriv_store_filter_reviews', 'store_filter_reviews_callback' );
function store_filter_reviews_callback(){
    $rate = $_POST['rate'];
    $page = $_POST['page'];
    $store_id = $_POST['store_id'];
    store_review_list($store_id, $rate,$page);   
    die;
}

//STORE REVIEWS LIST
function store_review_list($store_user, $rate='', $page='')
{
  global $wpdb;
  $id          = $store_user;
  $post_type   = 'product';
  $limit       = 10;
  $status      = '1';
  $page_number = $page ? $page : get_query_var( 'paged' );
  $pagenum     = max( 1, $page_number );
  $offset      = ( $pagenum - 1 ) * $limit;

  $rating_text = '';
  $rating_filter = 'cm.meta_value != "" AND'; 
  if (!empty($rate) && $rate != 'Any') {
    $rating_text = $rate.' start';
    $rating_filter = 'cm.meta_value = '.$rate.' AND'; 
  }

  $comments = $wpdb->get_results(
      "SELECT c.comment_content, c.comment_ID, c.comment_author,
          c.comment_author_email, c.comment_author_url,
          p.post_title, c.user_id, c.comment_post_ID, c.comment_approved,
          c.comment_date
      FROM $wpdb->comments as c, $wpdb->commentmeta as cm, $wpdb->posts as p
      WHERE p.post_author='$id' AND
          p.post_status='publish' AND
          c.comment_post_ID=p.ID AND
          cm.comment_id = c.comment_ID AND
          c.comment_approved='$status' AND
          cm.meta_key = 'rating' AND
          $rating_filter
          p.post_type='$post_type'  ORDER BY c.comment_ID DESC
      LIMIT $offset,$limit"
    );
  
  if (!empty($comments)) {
    foreach ( $comments as $single_comment ) {
      if ( $single_comment->comment_approved ) {
        $GLOBALS['comment'] = $single_comment;
        $comment_date       = get_comment_date( '', $single_comment->comment_ID );
        $comment_author_img = get_avatar( $single_comment->comment_author_email, 180 );
        $permalink          = get_comment_link( $single_comment );

        dokan_get_template(
          'templae-reviews.php',
          array(
            'single_comment' => $single_comment
          )
        );
      }
    }
  }else{
    echo '<p class="woocommerce-noreviews">Sorry, there are currently no '.$rating_text.' reviews.</p>';
  }
}

//DASHBOARD RECENT REVIEWS LIST
function dashboard_recent_review_list()
{
  global $wpdb;
  $id          = get_current_user_id();
  $post_type   = 'product';
  $limit       = 4;
  $status      = '1';
  $offset      = 0;

  $comments = $wpdb->get_results(
      "SELECT c.comment_content, c.comment_ID, c.comment_author,
          c.comment_author_email, c.comment_author_url,
          p.post_title, c.user_id, c.comment_post_ID, c.comment_approved,
          c.comment_date
      FROM $wpdb->comments as c, $wpdb->commentmeta as cm, $wpdb->posts as p
      WHERE p.post_author='$id' AND
          p.post_status='publish' AND
          c.comment_post_ID=p.ID AND
          cm.comment_id = c.comment_ID AND
          c.comment_approved='$status' AND
          cm.meta_key = 'rating' AND
          p.post_type='$post_type'  ORDER BY c.comment_ID DESC
      LIMIT $offset,$limit"
    );
  
  if (!empty($comments)) {
    foreach ( $comments as $single_comment ) {
      if ( $single_comment->comment_approved ) {
        $GLOBALS['comment'] = $single_comment;
        $comment_date       = get_comment_date( '', $single_comment->comment_ID );
        $comment_author_img = get_avatar( $single_comment->comment_author_email, 180 );
        $permalink          = get_comment_link( $single_comment );

        dokan_get_template(
          'dashboard/recent-reviews.php',
          array(
            'single_comment' => $single_comment
          )
        );
      }
    }
  }else{
    echo '<p class="woocommerce-noreviews">Sorry, there are currently no '.$rating_text.' reviews.</p>';
  }
}

//GET STROE GROUP COMMENTS
function get_store_group_comment($store_user){
    global $wpdb;   
    $result = $wpdb->get_results( $wpdb->prepare(
    "SELECT cm.meta_value as star, count(cm.meta_value) as total FROM $wpdb->posts p INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID WHERE p.post_type = 'product' AND p.post_status = 'publish' AND p.post_author=%d AND wc.comment_approved = 1 AND (cm.meta_key = 'rating') GROUP BY cm.meta_value ORDER BY cm.meta_value DESC", $store_user) );
    $stars = array(5=>0,4=>0,3=>0,2=>0,1=>0);
    if($result){
        foreach ($result as $key => $rate) {
            $stars[$rate->star] = $rate->total;
        }
    }
    return $stars;
}

//GET STROE TOTAL REVIEWS AND RATINGS
function get_store_total_reviews_and_ratings($store_user){
    global $wpdb;   
    $result = $wpdb->get_row( $wpdb->prepare(
    "SELECT AVG(cm.meta_value) as average, count(cm.meta_value) as ratings, count(wc.comment_ID) as reviews  FROM $wpdb->posts p INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID WHERE p.post_type = 'product' AND p.post_status = 'publish' AND p.post_author=%d AND wc.comment_approved = 1 AND (cm.meta_key = 'rating') ORDER BY cm.meta_value DESC", $store_user) );
    return $result;
}

//SELLER ACCOUNT ADD NEW MENU
add_action('dokan_render_settings_content', 'dd_render_settings_content',30);
function dd_render_settings_content($query_vars){
    global $wp;

    if ( isset( $wp->query_vars['settings'] ) && 'profile' == $wp->query_vars['settings'] ) {
        //current_user_can( 'dokan_view_store_profile_menu' )
        if ( ! current_user_can( 'dokan_view_store_payment_menu' ) ) {
            dokan_get_template_part('global/dokan-error', '', array(
                'deleted' => false,
                'message' => __( 'You have no permission to view this page', 'dokan-lite' )
            ) );
        } else {
            $currentuser  = dokan_get_current_user_id();
            $profile_info = dokan_get_store_info( dokan_get_current_user_id() );

            dokan_get_template_part( 'settings/profile', '', array(
                'current_user' => $currentuser,
                'profile_info' => $profile_info,
            ) );
        }
    }

    if ( isset( $wp->query_vars['settings'] ) && 'seller-profile' == $wp->query_vars['settings'] ) {
        //current_user_can( 'dokan_view_store_seller_profile_menu' )
        if ( ! current_user_can( 'dokan_view_store_payment_menu' ) ) {
            dokan_get_template_part('global/dokan-error', '', array(
                'deleted' => false,
                'message' => __( 'You have no permission to view this page', 'dokan-lite' )
            ) );
        } else {
            $currentuser  = dokan_get_current_user_id();
            $profile_info = get_seller_profile_info($currentuser);
            dokan_get_template_part( 'settings/seller-profile', '', array(
                'current_user' => $currentuser,
                'profile_info' => $profile_info,
            ) );
        }
    }
    if ( isset( $wp->query_vars['settings'] ) && 'business-info' == $wp->query_vars['settings'] ) {
        //current_user_can( 'dokan_view_store_business_info_menu' )
        if ( ! current_user_can( 'dokan_view_store_payment_menu' ) ) {
            dokan_get_template_part('global/dokan-error', '', array(
                'deleted' => false,
                'message' => __( 'You have no permission to view this page', 'dokan-lite' )
            ) );
        } else {
          $methods      = dokan_withdraw_get_active_methods();
          $currentuser  = dokan_get_current_user_id();
          $profile_info = dokan_get_store_info( dokan_get_current_user_id() );
          dokan_get_template_part( 'settings/business-info', '', array(
            'methods'      => $methods,
            'current_user' => $currentuser,
            'profile_info' => $profile_info,
          ) );
        }
    }
    if ( isset( $wp->query_vars['settings'] ) && 'emails' == $wp->query_vars['settings'] ) {
        //current_user_can( 'dokan_view_store_emails_menu' )
        if ( ! current_user_can( 'dokan_view_store_payment_menu' ) ) {
            dokan_get_template_part('global/dokan-error', '', array(
                'deleted' => false,
                'message' => __( 'You have no permission to view this page', 'dokan-lite' )
            ) );
        } else {
            $currentuser  = dokan_get_current_user_id();
            $seller_emails = get_user_meta( $currentuser, '_seller_emails', true);
            $profile_info = get_seller_profile_info($currentuser);
            dokan_get_template_part( 'settings/emails', '', array(
                'current_user' => $currentuser,
                'profile_info' => $profile_info,
                'seller_emails' => $seller_emails,
            ) );
        }
    }
    if ( isset( $wp->query_vars['settings'] ) && 'contract' == $wp->query_vars['settings'] ) {
        //current_user_can( 'dokan_view_store_contract_menu' )
        if ( ! current_user_can( 'dokan_view_store_payment_menu' ) ) {
            dokan_get_template_part('global/dokan-error', '', array(
                'deleted' => false,
                'message' => __( 'You have no permission to view this page', 'dokan-lite' )
            ) );
        } else {
            $currentuser  = dokan_get_current_user_id();
            //$profile_info = get_seller_profile_info($currentuser);
            $term_conditions = get_user_meta( $currentuser, '_seller_term_conditions', true);
            dokan_get_template_part( 'settings/contract', '', array(
                'current_user' => $currentuser,
                'term_conditions' => $term_conditions,
            ) );
        }
    }
}

//GET SELLER PROFILE DATA HARE.
function get_seller_profile_info($id){

  $defaults = array(
    'location'         => '',
    'gravatar_id'      => '',
    'banner_id'        =>'',
    'dokan_owner'      => '',
    'about_owner'      => '',
    'owner_website'    => '',
    'websiteIsPrivate' => ''
  );

  if (!$id) {    
    return;
  }

  $seller_profile_info = get_user_meta($id, 'seller_profile_settings', true );
  $seller_profile_info = is_array( $seller_profile_info ) ? $seller_profile_info : array();
  $seller_profile_info = wp_parse_args( $seller_profile_info, $defaults );

  return $seller_profile_info;
}


//ADD MENU CAP FOR SELLER USER
add_filter( 'dokan_get_all_cap', 'dd_get_all_cap', 30);
function dd_get_all_cap($capabilities){
    $capabilities['menu'] = $capabilities['menu'] + array(
        'dokan_view_store_profile_menu' => __( 'View profile settings menu', 'dokan-lite' ),
        'dokan_view_store_seller_profile_menu' => __( 'View seller profile settings menu', 'dokan-lite' ),
        'dokan_view_store_business_info_menu' => __( 'View business info settings menu', 'dokan-lite' ),
        'dokan_view_store_emails_menu' => __( 'View emails settings menu', 'dokan-lite' ),
        'dokan_view_store_contract_menu' => __( 'View contract settings menu', 'dokan-lite' ),
        );
    //print_r($capabilities);
    return $capabilities;
}

//SAVE SELLER PROFILE DATA HERE.
add_action( 'wp_ajax_vendor_information', 'save_vendor_information_data' );
function save_vendor_information_data(){
    $post_data = wp_unslash( $_POST );
   /* print_r($post_data);
    die;*/
    if (!wp_verify_nonce( $post_data['vendor_info_nonce'], 'vendor_info_action')){
        wp_die( esc_attr__( 'Are you cheating?', 'dokan-lite' ) );
    }

    $error = new WP_Error();

    $dokan_store_name = sanitize_text_field( $post_data['dokan_store_name'] );
    $phone = sanitize_text_field( $post_data['phone'] );
    $user_email = sanitize_text_field( $post_data['user_email'] );

    if ( empty( $dokan_store_name ) ) {
        $error->add( 'company_name', __( 'Company name is required.', 'dokan-lite' ) );
    }
    if ( empty( $phone ) ) {
        $error->add( 'phone', __( 'Phone is required.', 'dokan-lite' ) );
    }   
    if ( empty( $user_email ) ) {
        $error->add( 'dokan_owner', __( 'Company email is required.', 'dokan-lite' ) );
    }

    $exists = email_exists( $user_email );
    $current_user = wp_get_current_user();

    if ($exists && $current_user->user_email != $user_email){
      $error->add( 'dokan_owner', __( 'This E-mail is registered.', 'dokan-lite' ) );
    } 
    
    if ( $error->get_error_codes() ) {
        if ( is_wp_error( $error ) ) {
            $_SESSION['dd_error_msg'] = $error->get_error_message();
            wp_redirect(wp_get_referer());
            exit;
        }
    }    

    $store_id                = dokan_get_current_user_id();
    $existing_dokan_settings = get_user_meta( $store_id, 'dokan_profile_settings', true );
    $prev_dokan_settings     = ! empty( $existing_dokan_settings ) ? $existing_dokan_settings : array();  
   

    $dokan_settings = array(
      'store_name'               => $dokan_store_name,
      'address'                  => isset( $post_data['dokan_address'] ) ? array_map( 'sanitize_text_field', $post_data['dokan_address'] ) : $prev_dokan_settings['address'],
      'phone'                    => $phone,
    );

    if (isset($_FILES['vendor_logo']['name']) && !empty($_FILES['vendor_logo']['name'])) {

      if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
      }
      $uploadedfile = $_FILES['vendor_logo'];
     
      $upload_overrides = array(
          'test_form' => false
      );
       
      $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
       
      if ( $movefile && ! isset( $movefile['error'] ) ) {
        if (isset($prev_dokan_settings['vendor_logo']['file'])) {
          unlink($prev_dokan_settings['vendor_logo']['file']);
        }        
        $dokan_settings['vendor_logo'] = $movefile;
      } else {
          $_SESSION['dd_error_msg'] = $movefile['error'];
          wp_redirect(wp_get_referer());
          exit;
      }
    } 
    

    

    if ( isset( $post_data['settings']['bank'] ) ) {
      $bank = $post_data['settings']['bank'];

      $dokan_settings['payment']['bank'] = array(
        'ac_name'        => sanitize_text_field( $bank['ac_name'] ),
        'ac_number'      => sanitize_text_field( $bank['ac_number'] ),
        'bank_name'      => sanitize_text_field( $bank['bank_name'] ),
        'bank_addr'      => sanitize_text_field( $bank['bank_addr'] ),
        'routing_number' => sanitize_text_field( $bank['routing_number'] ),
        'iban'           => sanitize_text_field( $bank['iban'] ),
        'swift'          => sanitize_text_field( $bank['swift'] ),
      );
    }

    if ( isset( $post_data['settings']['paypal'] ) ) {
      $dokan_settings['payment']['paypal'] = array(
        'email' => sanitize_email( $post_data['settings']['paypal']['email'] ),
      );
    }

    if ( isset( $post_data['settings']['skrill'] ) ) {
      $dokan_settings['payment']['skrill'] = array(
        'email' => sanitize_email( $post_data['settings']['skrill']['email'] ),
      );
    }
    
    wp_update_user( array( 'ID' => $store_id, 'user_email' => $user_email) );

    update_user_meta( $store_id, 'dokan_store_name', $dokan_settings['store_name'] );
    $dokan_settings = array_merge( $prev_dokan_settings, $dokan_settings );
    update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );

    $success_msg = __( 'Your information has been saved successfully', 'dokan-lite' );
    $_SESSION['dd_success_msg'] = $success_msg;
    
    wp_redirect(wp_get_referer());

    exit();
}

//SAVE SELLER PROFILE DATA HERE.
add_action( 'wp_ajax_seller_profile', 'save_seller_profile_callback' );
function save_seller_profile_callback(){
    $post_data = wp_unslash( $_POST );

    if ( ! wp_verify_nonce( $post_data['_wpnonce'], 'seller_profile_nonce' ) ) {
        wp_die( esc_attr__( 'Are you cheating?', 'dokan-lite' ) );
    }

    $error = new WP_Error();

    $dokan_gravatar = sanitize_text_field( $post_data['dokan_gravatar'] );
    //$dokan_banner = sanitize_text_field( $post_data['dokan_banner'] );
    $store_location = sanitize_text_field( $post_data['store_location'] );
    $dokan_owner = sanitize_text_field( $post_data['dokan_owner'] );
    $about_owner = sanitize_textarea_field( $post_data['about_owner'] );
    $owner_website = sanitize_text_field( $post_data['owner_website'] );
    $websiteIsPrivate = sanitize_text_field( $post_data['websiteIsPrivate'] );

    if ( empty( $dokan_gravatar ) ) {
        $error->add( 'dokan_gravatar', __( 'Seller profile image required.', 'dokan-lite' ) );
    }
    if ( empty( $store_location ) ) {
        $error->add( 'store_location', __( 'Location required.', 'dokan-lite' ) );
    }
    /*if ( empty( $dokan_banner ) ) {
        $error->add( 'dokan_banner', __( 'Owner image required.', 'dokan-lite' ) );
    }*/
    if ( empty( $dokan_owner ) ) {
        $error->add( 'dokan_owner', __( 'Owner(s) required.', 'dokan-lite' ) );
    }
    if ( empty( $about_owner ) ) {
        $error->add( 'dokan_gravatar', __( 'About you required.', 'dokan-lite' ) );
    }
    
    if ( $error->get_error_codes() ) {
        if ( is_wp_error( $error ) ) {
            wp_send_json_error( $error->get_error_message() );
            exit;
        }
    }  

    $store_id  = dokan_get_current_user_id();

    $dokan_settings = array(
        'location'       => $store_location,
        'gravatar_id'    => isset( $dokan_gravatar ) ? absint($dokan_gravatar ) : 0,
        'banner_id'      => isset( $dokan_banner ) ? absint($dokan_banner ) : 0,
        'dokan_owner'    => $dokan_owner,
        'about_owner'    => $about_owner,
        'owner_website'  => $owner_website,
        'websiteIsPrivate' => $websiteIsPrivate,
    );

    update_user_meta( $store_id, 'seller_profile_settings', $dokan_settings );

    $success_msg = __( 'Your information has been saved successfully', 'dokan-lite' );

    $data = array('msg' => $success_msg);
    
    wp_send_json_success( $data );

    exit();
}

//SAVE BUSINESS INFO DATA HERE.
add_action( 'wp_ajax_seller_business_info', 'seller_business_info_callback' );
function seller_business_info_callback(){

    $post_data = wp_unslash( $_POST );
    
    if (! wp_verify_nonce($post_data['_wpnonce'], 'business_info_nonce' )){
        wp_die( esc_attr__( 'Are you cheating?', 'dokan-lite' ) );
    }

    $store_id                = dokan_get_current_user_id();
    $existing_dokan_settings = get_user_meta( $store_id, 'dokan_profile_settings', true );
    $prev_dokan_settings     = ! empty( $existing_dokan_settings ) ? $existing_dokan_settings : array();

    $error = new WP_Error();

    $phone = sanitize_text_field( $post_data['phone'] );
    $dokan_store_name = sanitize_text_field( $post_data['dokan_store_name'] );
    
    if (empty( $phone )){
        $error->add( 'dokan_phone', __( 'Company phone required.', 'dokan-lite' ) );
    }
    if (empty( $dokan_store_name )){
        $error->add( 'dokan_company', __( 'Company name required.', 'dokan-lite' ) );
    }    
    
    if ( $error->get_error_codes() ) {
        if ( is_wp_error( $error ) ) {
            wp_send_json_error( $error->get_error_message() );
            exit;
        }
    }  

    $store_id  = dokan_get_current_user_id();

    $dokan_settings = array(
      'store_name'               => $dokan_store_name,
      'address'                  => isset( $post_data['dokan_address'] ) ? array_map( 'sanitize_text_field', $post_data['dokan_address'] ) : $prev_dokan_settings['address'],
      'phone'                    => $phone,
      
    );

    if ( isset( $post_data['settings']['bank'] ) ) {
      $bank = $post_data['settings']['bank'];

      $dokan_settings['payment']['bank'] = array(
        'ac_name'        => sanitize_text_field( $bank['ac_name'] ),
        'ac_number'      => sanitize_text_field( $bank['ac_number'] ),
        'bank_name'      => sanitize_text_field( $bank['bank_name'] ),
        'bank_addr'      => sanitize_text_field( $bank['bank_addr'] ),
        'routing_number' => sanitize_text_field( $bank['routing_number'] ),
        'iban'           => sanitize_text_field( $bank['iban'] ),
        'swift'          => sanitize_text_field( $bank['swift'] ),
      );
    }

    if ( isset( $post_data['settings']['paypal'] ) ) {
      $dokan_settings['payment']['paypal'] = array(
        'email' => sanitize_email( $post_data['settings']['paypal']['email'] ),
      );
    }

    if ( isset( $post_data['settings']['skrill'] ) ) {
      $dokan_settings['payment']['skrill'] = array(
        'email' => sanitize_email( $post_data['settings']['skrill']['email'] ),
      );
    }

    update_user_meta( $store_id, 'dokan_store_name', $dokan_settings['store_name'] );
    $dokan_settings = array_merge( $prev_dokan_settings, $dokan_settings );
    update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );

    //update_user_meta( $store_id, 'seller_profile_settings', $dokan_settings );

    $success_msg = __( 'Your information has been saved successfully', 'dokan-lite' );

    $data = array('msg' => $success_msg);
    
    wp_send_json_success( $data );

    exit();
}


//SAVE SELLER EMAILS DATA HERE.
add_action( 'wp_ajax_seller_emails', 'seller_emails_callback' );
function seller_emails_callback(){

    $post_data = wp_unslash( $_POST );
    
    if (! wp_verify_nonce($post_data['_wpnonce'], 'seller_emails_nonce' )){
        wp_die( esc_attr__( 'Are you cheating?', 'dokan-lite' ) );
    }

    $store_id                = dokan_get_current_user_id();
    $existing_dokan_settings = get_user_meta( $store_id, 'dokan_profile_settings', true );
    $prev_dokan_settings     = ! empty( $existing_dokan_settings ) ? $existing_dokan_settings : array();

    /*$error = new WP_Error();

    $seller_emails = $post_data['seller_emails'];
        
    if (empty( $seller_emails )){
        $error->add( 'dokan_phone', __( 'Company phone required.', 'dokan-lite' ) );
    }
        
    if ( $error->get_error_codes() ) {
        if ( is_wp_error( $error ) ) {
            wp_send_json_error( $error->get_error_message() );
            exit;
        }
    }  */

    $store_id  = dokan_get_current_user_id();

    $seller_emails = isset( $post_data['seller_emails'] ) ? array_map( 'sanitize_text_field', $post_data['seller_emails'] ) : '';

    if (isset($seller_emails) && !empty($seller_emails)) {
      update_user_meta( $store_id, '_seller_emails', $seller_emails);
    }

    $success_msg = __( 'Your information has been saved successfully', 'dokan-lite' );

    $data = array('msg' => $success_msg);
    
    wp_send_json_success( $data );

    exit();
}

//SAVE SELLER CONTRACT DATA HERE.
add_action( 'wp_ajax_seller_contract', 'seller_contract_callback' );
function seller_contract_callback(){

    $post_data = wp_unslash( $_POST );
    
    if (! wp_verify_nonce($post_data['_wpnonce'], 'seller_contract_nonce' )){
        wp_die( esc_attr__( 'Are you cheating?', 'dokan-lite' ) );
    }

    $store_id                = dokan_get_current_user_id();
    $existing_dokan_settings = get_user_meta( $store_id, 'dokan_profile_settings', true );
    $prev_dokan_settings     = ! empty( $existing_dokan_settings ) ? $existing_dokan_settings : array();

     $terms_conditions = isset( $post_data['terms_conditions'] ) ? sanitize_text_field($post_data['terms_conditions']) : '';

    $error = new WP_Error();

    if (empty( $terms_conditions )){
        $error->add( 'dokan_phone', __( 'Please enter your name.', 'dokan-lite' ) );
    }
        
    if ( $error->get_error_codes() ) {
        if ( is_wp_error( $error ) ) {
            wp_send_json_error( $error->get_error_message() );
            exit;
        }
    }  

    $store_id  = dokan_get_current_user_id();

    if (isset($terms_conditions) && !empty($terms_conditions)) {
      update_user_meta( $store_id, '_seller_term_conditions', $terms_conditions);
    }

    $success_msg = __( 'Your information has been saved successfully', 'dokan-lite' );

    $data = array('msg' => $success_msg);
    
    wp_send_json_success( $data );

    exit();
}

//ADD HTTP IN THE URL
function add_http_to_url($url){
  if (strpos($url,'http://') === false){
    $url = 'http://'.$url;
  }
  return $url;
}

//SAVE SELLER CONTRACT DATA HERE.
add_action( 'wp_ajax_dd_owner_image', 'dd_owner_image_callback' );
function dd_owner_image_callback(){
    global $wp_query, $user_ID;

    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
     
    $uploadedfile = $_FILES['avatar'];
     
    $upload_overrides = array(
        'test_form' => false
    );
     
    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
     
    if ( $movefile && ! isset( $movefile['error'] ) ) {       
        $seller_owner_image = get_user_meta($current_user, '_seller_owner_image',true);
        if (isset($seller_owner_image['image_path']) && !empty($seller_owner_image['image_path'])) {
          unset($seller_owner_image['image_path']);
        }
        update_user_meta($user_ID, '_seller_owner_image', array('image_path' => $movefile['file'], 'image_url' => $movefile['url']));
    } else {
        echo $movefile['error'];
    }
    die;
}