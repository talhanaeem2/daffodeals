<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

//VALIDATE ADD NEW PRODUCT FIELDS
add_filter('dokan_can_add_product', 'add_new_validation_add_product');
function add_new_validation_add_product($errors){
    $postdata = wp_unslash( $_POST );
    $startDate = sanitize_text_field( $postdata['startDate'] );
    $post_content = sanitize_textarea_field( $postdata['post_content'] );
    $ship_by = sanitize_text_field( $postdata['ship_by'] );
    $sale_price = sanitize_text_field( $postdata['_sale_price'] );
    $regular_price = sanitize_text_field( $postdata['_regular_price'] );
    $shipping_price = sanitize_text_field( $postdata['shipping_price'] );
    $shippingPriceAdditionalItems = sanitize_text_field( $postdata['shippingPriceAdditionalItems'] );

    $totalQtyAvailable = sanitize_text_field($postdata['totalQtyAvailable']);
    $feat_image_id = sanitize_text_field($postdata['feat_image_id']);
    if( empty( $post_content ) ) {
        $errors[] = __( 'Please enter product description', 'dokan-lite' );
    }
    if( empty( $startDate ) ) {
        $errors[] = __( 'Please enter deal start date', 'dokan-lite' );
    }
    if( empty( $ship_by ) ) {
        //$errors[] = __( 'Please enter ship by date', 'dokan-lite' );
    }
    if( empty( $sale_price ) ) {
        $errors[] = __( 'Please enter deal price', 'dokan-lite' );
    }
    if( empty( $regular_price ) ) {
        $errors[] = __( 'Please enter MSRP(retail) price', 'dokan-lite' );
    }
    if(empty( $shipping_price )) {
		$shipping_price = 0;
        //$errors[] = __( 'Please enter shipping price', 'dokan-lite' );
    }
    if( empty( $totalQtyAvailable ) ) {
        $errors[] = __( 'Please enter total units', 'dokan-lite' );
    }
    if( empty( $feat_image_id ) ) {
        $errors[] = __( 'Please select images', 'dokan-lite');
    }
    return $errors;
}

/*
* KATE SAVE NEW FIELD ON ADD NEW PRODUCT
*/
add_action( 'dokan_new_product_added', 'dokan_new_product_added_handle', 99, 2);
function dokan_new_product_added_handle($product_id, $postdata ){

	if ( isset( $postdata['startDate'] ) ) {
		$startDate = sanitize_text_field( $postdata['startDate'] );
        update_post_meta( $product_id, '_startDate', date('Y-m-d', strtotime($startDate)));
		update_post_meta( $product_id, '_endDate', date('Y-m-d', strtotime("+3 day", strtotime($startDate))));
	}
    if ( isset( $postdata['ship_by'] ) ) {
        $ship_by = sanitize_text_field( $postdata['ship_by'] );
        update_post_meta( $product_id, '_ships_date', date('Y-m-d', strtotime($ship_by)) );
    }
	if ( isset( $postdata['shipping_price'] ) ) {
		$shipping_price = sanitize_text_field( $postdata['shipping_price'] );
		update_post_meta( $product_id, '_shipping_price', $shipping_price );
	}
	if ( isset( $postdata['shippingPriceAdditionalItems'] ) ) {
		$shippingPriceAdditionalItems = sanitize_text_field( $postdata['shippingPriceAdditionalItems'] );
		update_post_meta( $product_id, '_shippingPriceAdditionalItems', $shippingPriceAdditionalItems );
	}
    if ( isset( $postdata['personalized_handmade'] ) ) {
        $personalized_handmade = sanitize_text_field( $postdata['personalized_handmade'] );
        update_post_meta( $product_id, '_personalized_handmade', $personalized_handmade );
    }
	
	if ( isset( $postdata['totalQtyAvailable'] ) ) {
		$totalQtyAvailable = sanitize_text_field( $postdata['totalQtyAvailable'] );
        update_post_meta( $product_id, '_totalQtyAvailable', $totalQtyAvailable );
        update_post_meta( $product_id, '_manage_stock', 'yes');
        update_post_meta( $product_id, '_stock', $totalQtyAvailable);
        update_post_meta( $product_id, '_backorders', 'no');
		update_post_meta( $product_id, '_low_stock_amount', 2);
	}

    if ( isset( $postdata['additional_information'] ) ) {
        $additional_information = sanitize_textarea_field( $postdata['additional_information'] );
        update_post_meta( $product_id, '_additional_information', $additional_information );
    }

	if ( isset( $postdata['seller_post_status'] ) ) {
		$seller_post_status = sanitize_text_field( $postdata['seller_post_status'] );
		update_post_meta( $product_id, '_post_status', $seller_post_status );
	}

    //SEND NEW DEAL EMAIL TO CUSTOMER AND ADMIN USERS   
    $deal_title = get_the_title($product_id);    
    $wstheme_options = get_wstheme_options();

    if (isset($_POST['rebook_deal']) && !empty($_POST['rebook_deal'])){

        global $wpdb;
        $rebook_deal = $_POST['rebook_deal'];
        $product_attributes = $wpdb->prefix . "product_attributes"; 
        $product_variations = $wpdb->prefix . "product_variations"; 
        update_post_meta( $product_id, '_rebook', 1);
        $attributes = $wpdb->get_results("SELECT * FROM ".$product_attributes." WHERE product_id = $rebook_deal "); 
        //print_r($attributes);
        if (!empty($attributes)) {
          foreach ($attributes as $attr) {
            $attr_id = $attr->ID;
            $variations = $wpdb->get_results("SELECT * FROM ".$product_variations." WHERE attr_id = $attr_id ");
            //print_r($variations);
            $data = array('product_id' => $product_id, 'title' => $attr->title, 'personalization' => $attr->personalization, 'char_allowed' => $attr->char_allowed, 'buy_limit' => $attr->buy_limit, 'variations' => $attr->variations, 'combine_status' => $attr->combine_status, 'quantity_status' => $attr->quantity_status);
            $format = array('%d','%s','%s', '%d', '%d', '%s', '%s', '%s');
            //print_r($data);
            $wpdb->insert($product_attributes,$data,$format);
            $new_attr_id = $wpdb->insert_id;
            if (!empty($variations)) {
              foreach ($variations as $variation) {
                $data = array('attr_id' => $new_attr_id, 'title' => $variation->title, 'sku' => $variation->sku, 'qty' => $variation->qty, 'variation_type' => $variation->variation_type);                                   
                $format = array('%d','%s','%s','%d','%s');
                //print_r($data);
                $wpdb->insert($product_variations,$data,$format);
              }
            }
          }
        }
        
        $user_subject  = $wstheme_options['rebook_email_sub']; 
        $add_deal_email_head  = $wstheme_options['rebook_email_head']; 
        $user_message = $wstheme_options['rebook_email_temp'];

    }else{
        $user_subject  = $wstheme_options['add_deal_email_sub']; 
        $add_deal_email_head  = $wstheme_options['add_deal_email_head']; 
        $user_message = $wstheme_options['add_deal_email_temp'];
    }   

    $data = array('deal_title'=> $deal_title, 'deal_edit_url' => dd_edit_product_url($product_id));
    $search_key_array = array('[deal_title]' =>'deal_title','[deal_edit_url]' => 'deal_edit_url');
    foreach ($search_key_array as $key => $value) {
      $key_value = @$data[$value];  
      $user_subject = str_replace($key,$key_value,$user_subject);
      $email_heading = str_replace($key,$key_value,$add_deal_email_head); 
      $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    dd_send_email_handler('', $user_subject, $user_message, $email_heading, true);    
}


/*
* NEW PRODUCT REDIRECT
*/
add_filter( 'dokan_add_new_product_redirect', 'dd_add_new_product_redirect', 69);
function dd_add_new_product_redirect($redirect){
	if ( isset( $_POST['seller_post_status'] ) ) {
		return $redirect = dokan_get_navigation_url( 'pending-product');
	}
}


/**
 * ---------------------------------------------------------------------
 * DD EDIT DEAL HERE
 * ---------------------------------------------------------------------
 */
//add_action( 'wp_ajax_dd_update_deal','dd_update_deal_handle');
add_action( 'template_redirect','dd_update_deal_handle', 12);
function dd_update_deal_handle(){
//var_dump('this is a test');
    if ( ! is_user_logged_in() ) {
        return;
    }

    if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
        return;
    }

    if ( ! isset( $_POST['dokan_edit_deal_nonce'] ) ) {
        return;
    }


    $postdata = wp_unslash( $_POST );

    if ( ! wp_verify_nonce( sanitize_key( $postdata['dokan_edit_deal_nonce'] ), 'dokan_edit_deal' ) ) {
        return;
    }

    if ( ! current_user_can( 'dokan_add_product' ) ) {
        return;
    }
   
    if (!isset($_POST['dokan_product_id']) && !empty($_POST['dokan_product_id'])) {
        return;
    }

    $product_id = sanitize_text_field($_POST['dokan_product_id']);
    $post_status = sanitize_text_field($_POST['post_status']);

    $error = array();

    $post_title = sanitize_text_field($_POST['post_title']);
    $ships_date = sanitize_text_field($_POST['ships_date']);
    $totalQtyAvailable = sanitize_text_field($_POST['totalQtyAvailable']);

    if (isset($_POST['post_title']) && empty($post_title)) {
       $error[] = 'Please enter product name.';
    }
     

    $postdata = wp_unslash( $_POST );
    $startDate = sanitize_text_field( $postdata['startDate'] );
    $post_content = sanitize_textarea_field( $postdata['post_content'] );
    $ship_by = sanitize_text_field( $postdata['ship_by'] );
    $sale_price = sanitize_text_field( $postdata['_sale_price'] );
    $regular_price = sanitize_text_field( $postdata['_regular_price'] );
    $shipping_price = sanitize_text_field( $postdata['shipping_price'] );

    $totalQtyAvailable = sanitize_text_field($postdata['totalQtyAvailable']);
    $feat_image_id = sanitize_text_field($postdata['feat_image_id']);

    if( empty( $post_content ) ) {
        $errors[] = __( 'Please enter product description', 'dokan-lite' );
    }
    if( empty( $startDate ) ) {
        $errors[] = __( 'Please enter deal start date', 'dokan-lite' );
    }
    if( empty( $ship_by ) ) {
        $errors[] = __( 'Please enter ship by date', 'dokan-lite' );
    }
    if( empty( $sale_price ) ) {
        $errors[] = __( 'Please enter deal price', 'dokan-lite' );
    }
    if( empty( $regular_price ) ) {
        $errors[] = __( 'Please enter MSRP(retail) price', 'dokan-lite' );
    }
    if( empty( $shipping_price ) ) {
        $errors[] = __( 'Please enter shipping price', 'dokan-lite' );
    }
    if( empty( $totalQtyAvailable ) ) {
        $errors[] = __( 'Please enter total units', 'dokan-lite' );
    }
    if( empty( $feat_image_id ) ) {
        $errors[] = __( 'Please select images', 'dokan-lite');
    }

    /* print_r($_POST);
    die;*/
    if (!$error) 
    {

        global $wpdb;
        $product_attributes = $wpdb->prefix . "product_attributes"; 
        $product_variations = $wpdb->prefix . "product_variations";     

       /** set images **/
        if ( isset( $_POST['feat_image_id'] ) && ! empty( $_POST['feat_image_id'] ) ) {
            set_post_thumbnail( $product_id, absint( $_POST['feat_image_id'] ) );
        }

        // Gallery Images
        if (isset( $_POST['product_image_gallery'] ) && ! empty( $_POST['product_image_gallery'] ) ) {
            $attachment_ids = array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) );
            update_post_meta( $product_id, '_product_image_gallery', implode( ',', $attachment_ids ) );
        }

        //UPDATE METAS
        update_deal_metas($product_id);        

        //SEND CHANGES MAIL TO VENDOR 
        if (current_user_can('administrator'))
        {
            if (isset($_POST['post_status']) && !empty($post_status)) {
                $update_post = array(
                    'ID'           => $product_id,
                    'post_status'   => $post_status,
                );
                wp_update_post( $update_post );
            }
            
            $post_data = get_post( $product_id );
            $deal_title = $post_data->post_title;
            $author_id = $post_data->post_author;
            $author_obj = get_user_by('id', $author_id);
            $startDate = get_post_meta( $product_id, '_startDate', true );
            delete_post_meta( $product_id, '_rebook');

            if ($post_status == 'declined') {
               dd_deal_declined_notification($product_id);
            }elseif ($post_status == 'upcoming') {
               //dd_deal_upcoming_notification($product_id);
            }else{                

                $seller_emails = get_user_meta($author_id, '_seller_emails', true);
                $seller_email = isset($seller_emails['deal'])?$seller_emails['deal']:$author_obj->user_email;

                $wstheme_options = get_wstheme_options();
                $user_subject  = $wstheme_options['proposal_email_sub']; 
                $email_heading  = $wstheme_options['proposal_email_heading']; 
                $user_message = $wstheme_options['proposal_email_temp'];

                $finalize_deal_date = date('Y-m-d', strtotime('-5 day', strtotime($startDate)));
                update_post_meta( $product_id, '_finalize_deal_date', $finalize_deal_date);

                $data = array('deal_title'=> $deal_title,'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));
                $search_key_array = array('[deal_title]' =>'deal_title','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');
                foreach ($search_key_array as $key => $value) {
                    $key_value = @$data[$value];  
                    $user_subject = str_replace($key,$key_value,$user_subject);
                    $email_heading = str_replace($key,$key_value,$email_heading); 
                    $user_message = str_replace($key,$key_value,$user_message); 
                }
                $user_message  = wpautop($user_message); 
                dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
            }            
        }

         //var_dump($post_status);
        // UPDATE DEAL IN FINALIZED STEP
        if ($post_status == 'editing' || $post_status == 'shipping' || $post_status == 'publish' || $post_status == 'finalized') 
        {
            
            if (current_user_can('seller') || current_user_can('administrator') && isset($_POST['post_status'])){
                $update_post = array(
                    'ID'           => $product_id,
                    'post_status'   => $post_status,
                );
                wp_update_post( $update_post );
                //update_post_meta( $product_id, '_finalize_deal_date', date('Y-m-d'));

                $deal_step = 'Editing';
                $deal_info = ' has finalized his deal.';
                dd_vendor_changes_notification($product_id, $deal_step,$deal_info);
                dd_deal_upcoming_notification($product_id);
            }
           

            //DELETE ATTRIBUTES AND VARIATIONS IF AVALABE IN THE PRODUCT
            $attr_count = $wpdb->get_var("SELECT ID FROM ".$product_attributes." WHERE product_id = ".$product_id);
            if(!empty($attr_count) && !isset($_POST['rebook']))
            { //var_dump($attr_count);
                $wpdb->query("DELETE ".$product_attributes.", ".$product_variations." FROM ".$product_attributes." LEFT JOIN ".$product_variations."  ON ".$product_variations.".attr_id = ".$product_attributes.".ID WHERE ".$product_attributes.".product_id = ".$product_id."");
              /*  $wpdb->show_errors();
                $wpdb->print_error();
                die;*/
            }
            
            //ADD PRODUCT ATTRIBUTES AND VARIATIONS
			$options = $_POST['options'];
            $combinations = $_POST['combinations'];
            $limited = $_POST['limited'];
			/*print_r($_POST['limited']);
           */
            if (isset($options['title']) && !empty($options['title'][0])) 
            {
				//var_dump($options['title']);
                $attr_ids = array();
                $varition_ids = array();

                foreach ($options['title'] as $key => $option) 
                {
                    $att_title = sanitize_text_field($option);
                    if (!empty($att_title)) 
                    {
                        $personalization = sanitize_text_field($options['personalization'][$key]);
                        $char_allowed = sanitize_text_field($options['char_allowed'][$key]);
                        $repeatTimes = sanitize_text_field($options['repeatTimes'][$key]);

                        $variations_key = sanitize_title($att_title);

                        $att_variations = '';
                        if (!empty($options['values'][$variations_key])) {
                            $att_variations = $options['values'][$variations_key];
                            $att_variations = implode(',', $att_variations);
                        }

                        $combine_status = sanitize_text_field($options['combine'][$variations_key]);

                        $limit = sanitize_text_field($limited[$key]);

                        if ($combine_status == 'yes') {
                            $combine_key = array();
                            foreach ($options['combine'] as $key22 => $rt) {
                               if ($rt == 'yes') {
                                    $combine_key[] = $key22;
                                }
                            }
                            $variations_key = implode('-', $combine_key);
                        }
                       // echo $variations_key;

                        $data = array('product_id' => $product_id, 'title' => $att_title, 'personalization' => $personalization, 'char_allowed' => $char_allowed, 'buy_limit' => $repeatTimes, 'variations' => $att_variations, 'combine_status' => $combine_status, 'quantity_status' => $limit);
                        $format = array('%d','%s','%s', '%d', '%d', '%s', '%s', '%s');

                        $wpdb->insert($product_attributes,$data,$format);
                        $attr_id = $wpdb->insert_id;
                        $attr_ids[] = $attr_id;
                        
                       
                        if (!empty($option) && $personalization == 'no') {
                            $variations = $options['variation_title'][$variations_key];
                            if (!empty($variations)) {
                                foreach ($variations as $vkey => $variation) {
                                    $title = sanitize_text_field($variation);
                                    $sku = sanitize_text_field($options['sku'][$variations_key][$vkey]);
                                    $qty = sanitize_text_field($options['qty'][$variations_key][$vkey]);
                                    if (isset($_POST['combinations']) && !empty($combinations) && in_array($att_title, $combinations)){ 
                                        $variation_type = "variable"; 
                                    }else{ 
                                        $variation_type = "simple"; 
                                    } 
                                    $data = array('attr_id' => $attr_id, 'title' => $title, 'sku' => $sku, 'qty' => $qty, 'variation_type' => $variation_type);                                   
                                    $format = array('%d','%s','%s','%d','%s');
                                     //print_r($data);
                                    $wpdb->insert($product_variations,$data,$format);                                   
                                    $var_id = $wpdb->insert_id;
                                   
                                    $varition_ids[] = $var_id; 
                                }
                            }
                        }

                    }                    
                }
            }

        }
      
       

        // UPDATE DEAL IN FINALIZED STEP
        if ($post_status == 'finalized') 
        {
            $options = $_POST['options'];
            $combinations = $_POST['combinations'];
            $limited = $_POST['limited'];

            $attributes = $wpdb->get_results("SELECT * FROM ".$product_attributes." WHERE product_id = ".$product_id);
            //print_r($attributes);
            if (!empty($attributes)) 
            {
                foreach ($attributes as $key => $attr) 
                {
                    $att_title = sanitize_text_field($attr->title);
                    $personalization = $attr->personalization;
                    $variations_key = sanitize_title($att_title);

                    $combine_status = sanitize_text_field($options['combine'][$variations_key]);
                    $limit = sanitize_text_field($limited[$key]);

                    /*$data = array('combine_status' => $combine_status, 'quantity_status' => $limited);
                    $format = array('%s', '%s');
                    //print_r($data);
                    $wpdb->update($product_attributes,$data,array('ID' => $attr->ID),$format, array('%d'));*/

                    if ($combine_status == 'yes') {
                        $combine_key = array();
                        foreach ($options['combine'] as $key22 => $rt) {
                           if ($rt == 'yes') {
                                $combine_key[] = $key22;
                            }
                        }
                        $variations_key = implode('-', $combine_key);
                    }

                    $attr_id = $attr->ID;
                    $attr_ids[] = $attr_id;

                    if ($personalization == 'no') {
                       
                        $wpdb->query('DELETE FROM '.$product_variations.' WHERE attr_id = '.$attr_id);
                        $variations = $options['variation_title'][$variations_key];

                        if (!empty($variations)) {
                            foreach ($variations as $vkey => $variation) {
                                $title = sanitize_text_field($variation);
                                $sku = sanitize_text_field($options['sku'][$variations_key][$vkey]);
                                $qty = sanitize_text_field($options['qty'][$variations_key][$vkey]);
                                if (isset($_POST['combinations']) && !empty($combinations) && in_array($att_title, $combinations)){ 
                                    $variation_type = "variable"; 
                                }else{ 
                                    $variation_type = "simple"; 
                                } 
                                $data = array('attr_id' => $attr_id, 'title' => $title, 'sku' => $sku, 'qty' => $qty, 'variation_type' => $variation_type);
                                //print_r($data);
                                $format = array('%d','%s','%s','%d','%s');
                                $test = $wpdb->insert($product_variations,$data,$format);
// 								if($test){
// 									var_dump( "success" );
// 								} else{
// 									$wpdb->show_errors();
//                                     $wpdb->print_error();
// 								}
		
                                
                                $var_id = $wpdb->insert_id;
                                $varition_ids[] = $var_id; 
                            }
                        }
                    } 

                }
                $deal_step = 'Finalized';
                $deal_info = ' has changed deal images or inventory information.';
                dd_vendor_changes_notification($product_id, $deal_step,$deal_info);
            }
        }

       /* echo '<pre>';
        print_r($_POST);
        die;*/

        $_SESSION['deal_success'] = 'Your changes were saved.';

    }else{
        $_SESSION['deal_error'] = $error;
    }    

}


//VENDOR CHANGES MESSAGE HERE.
function dd_vendor_changes_notification($product_id, $deal_step,$deal_info){
    global $wpdb;
    $userid = get_current_user_id();

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = isset($seller_emails['deal'])?$seller_emails['deal']:$author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['vdc_email_sub']; 
    $email_heading  = $wstheme_options['vdc_email_heading']; 
    $user_message = $wstheme_options['vdc_email_temp'];
    $dokan_store_name = get_user_meta($userid, 'dokan_store_name', true);
    $deal_info = $dokan_store_name.' '.$deal_info;
    $data = array('deal_title'=> $deal_title,'deal_live_url' => get_permalink($product_id),'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)),'deal_step' => $deal_step, 'deal_info' => $deal_info);

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_live_url]' => 'deal_live_url','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date','[deal_step]' => 'deal_step','[deal_info]' => 'deal_info');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    if (current_user_can('seller')) {

        $admin_ids = $wpdb->get_var($wpdb->prepare("SELECT GROUP_CONCAT(user_id) FROM $wpdb->usermeta WHERE meta_key = 'wp_capabilities' AND meta_value  REGEXP '%s' ",'administrator'));

        $deal_comments = $wpdb->prefix . "deal_comments";
        $data = array('post_id' => $product_id, 'sender' => $userid, 'receiver' => $admin_ids, 'comment' => $deal_info,  'comment_date' => date('Y-m-d H:i:s'), 'seen' => $userid);
        //print_r($data);
        $format = array('%d','%d','%s','%s','%s','%s','%s');

        $wpdb->insert($deal_comments,$data,$format);
        $var_id = $wpdb->insert_id;

        dd_send_email_handler('', $user_subject, $user_message, $email_heading, true);
        //dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
    }
}

//DEAL UPCOMING MESSAGE HERE.
function dd_deal_upcoming_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = isset($seller_emails['deal'])?$seller_emails['deal']:$author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['upcoming_deal_email_sub']; 
    $email_heading  = $wstheme_options['upcoming_deal_email_heading']; 
    $user_message = $wstheme_options['upcoming_deal_email_temp'];

    $data = array('deal_title'=> $deal_title,'deal_live_url' => get_permalink($product_id),'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_live_url]' => 'deal_live_url','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}

//DEAL ACTIVE MESSAGE HERE.
function dd_deal_active_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = $author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['active_deal_email_sub']; 
    $email_heading  = $wstheme_options['active_deal_email_heading']; 
    $user_message = $wstheme_options['active_deal_email_temp'];

    $data = array('deal_title'=> $deal_title,'deal_live_url' => get_permalink($product_id),'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_live_url]' => 'deal_live_url','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}

//DEAL SHIPPING MESSAGE HERE.
function dd_deal_shipping_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = $author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['shipping_deal_email_sub']; 
    $email_heading  = $wstheme_options['shipping_deal_email_heading']; 
    $user_message = $wstheme_options['shipping_deal_email_temp'];

    $data = array('deal_title'=> $deal_title,'deal_live_url' => get_permalink($product_id),'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_live_url]' => 'deal_live_url','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}

//DEAL ENDED MESSAGE HERE.
function dd_deal_ended_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = $author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['ended_deal_email_sub']; 
    $email_heading  = $wstheme_options['ended_deal_email_heading']; 
    $user_message = $wstheme_options['ended_deal_email_temp'];

    $data = array('deal_title'=> $deal_title,'deal_live_url' => get_permalink($product_id),'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_live_url]' => 'deal_live_url','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}

//DEAL DECLINED MESSAGE HERE.
function dd_deal_declined_notification($product_id){
    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );
    $declined_msg = $_POST['vendor_status_message'];
    
    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = isset($seller_emails['deal'])?$seller_emails['deal']:$author_obj->user_email;

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['declined_deal_email_sub']; 
    $email_heading  = $wstheme_options['declined_deal_email_heading']; 
    $user_message = $wstheme_options['declined_deal_email_temp'];

    $data = array('deal_title'=> $deal_title,'deal_live_url' => get_permalink($product_id),'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)),'declined_msg' => $declined_msg);

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_live_url]' => 'deal_live_url','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date', '[declined_msg]' => 'declined_msg');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message); 
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}


//DEAL UPDATE METAS
function update_deal_metas($product_id){

    if (isset($_POST['post_title'])) 
    {   
        $post_title = sanitize_text_field($_POST['post_title']);
        $update_post = array(
            'ID'           => $product_id,
            'post_title'   => $post_title
        );
        // Update the post into the database
        wp_update_post( $update_post );
    }
    if (isset($_POST['post_content'])) 
    {   
        $post_content = sanitize_textarea_field($_POST['post_content']);
        $update_post = array(
            'ID'           => $product_id,
            'post_content'   => $post_content,
        );
        // Update the post into the database
        wp_update_post( $update_post );
    }

    $post_status = sanitize_text_field($_POST['post_status']);

    if ($post_status == 'upcoming' || $post_status == 'active') {
        $deal_step = 'Ready';
        $deal_info = ' has changed deal images or inventory.';
        //dd_vendor_changes_notification($product_id, $deal_step,$deal_info);
    }

    /** set product category * */
    if (isset($_POST['product_cat'])) {
       if ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
            wp_set_object_terms( $product_id, (int) $_POST['product_cat'], 'product_cat' );
        } else {
            if ( isset( $_POST['product_cat'] ) && ! empty( $_POST['product_cat'] ) ) {
                $cat_ids = array_map( 'absint', (array) $_POST['product_cat'] );
                wp_set_object_terms( $product_id, $cat_ids, 'product_cat' );
            }
        }
    }
    
    if ( isset( $_POST['startDate'] ) ) {
        $startDate = sanitize_text_field( $_POST['startDate'] );
        update_post_meta( $product_id, '_startDate', date('Y-m-d', strtotime($startDate)));
        update_post_meta( $product_id, '_endDate', date('Y-m-d', strtotime("+3 day", strtotime($startDate))));
    }
    if ( isset( $_POST['shipping_price'] ) ) {
        $shipping_price = sanitize_text_field( $_POST['shipping_price'] );
        update_post_meta( $product_id, '_shipping_price', $shipping_price );
    }
    if ( isset( $_POST['shippingPriceAdditionalItems'] ) ) {
        $shippingPriceAdditionalItems = sanitize_text_field( $_POST['shippingPriceAdditionalItems'] );
        update_post_meta( $product_id, '_shippingPriceAdditionalItems', $shippingPriceAdditionalItems );
    }
    if ( isset( $_POST['personalized_handmade'] ) ) {
        $personalized_handmade = sanitize_text_field( $_POST['personalized_handmade'] );
        update_post_meta( $product_id, '_personalized_handmade', $personalized_handmade );
    }
    
    if ( isset( $_POST['ship_by'] ) ) {
        $ships_date = sanitize_text_field( $_POST['ship_by'] );
        update_post_meta( $product_id, '_ships_date', date('Y-m-d', strtotime($ships_date)) );
    }
    
    if ( isset( $_POST['totalQtyAvailable'] ) ) {
        $totalQtyAvailable = sanitize_text_field( $_POST['totalQtyAvailable'] );
        update_post_meta( $product_id, '_totalQtyAvailable', $totalQtyAvailable );
        update_post_meta( $product_id, '_stock', $totalQtyAvailable);
    }
    if ( isset( $_POST['additional_information'] ) ) {
        $additional_information = sanitize_textarea_field( $_POST['additional_information'] );
        update_post_meta( $product_id, '_additional_information', $additional_information );
    }

}


//SANITIZE ARRAY FIELD
function sanitize_array_field( $array ) {
   foreach ( (array) $array as $k => $v ) {
      if ( is_array( $v ) ) {
          $array[$k] =  sanitize_array_field( $v );
      } else {
          $array[$k] = sanitize_text_field( $v );
      }
   }

  return $array;                                                       
}

function combinations($arrays, $i = 0) {
    if (!isset($arrays[$i])) {
        return array();
    }
    if ($i == count($arrays) - 1) {
        return $arrays[$i];
    }

    // get combinations from subsequent arrays
    $tmp = combinations($arrays, $i + 1);

    $result = array();

    // concat each array from tmp with each element from $arrays[$i]
    foreach ($arrays[$i] as $v) {
        foreach ($tmp as $t) {
            $result[] = is_array($t) ? array_merge(array($v), $t) : array($v, $t);
            
        }
    }

    return $result;
}

/**
 * ------------------------------------------------------------------------------------------------
 * NEW DOKAN LINKS
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'dokan_new_dashboard_links' ) ) {
	add_filter( 'dokan_query_var_filter', 'dokan_new_dashboard_links', 85 );

	function dokan_new_dashboard_links($query_vars) {
		$query_vars[] = 'scheduled';
		$query_vars[] = 'deals-ended';
		$query_vars[] = 'pending-product';
		return $query_vars;
	}
}

/*
* LOAD CUSTOM TEMPLATE HERE.
*/
add_action( 'dokan_load_custom_template', 'fp_load_custom_template', 77);
function fp_load_custom_template($query_vars){
	if ( isset( $query_vars['scheduled'] ) ) {
        if ( ! current_user_can( 'dokan_add_product' ) ) {
            dokan_get_template_part( 'global/no-permission' );
        } else {
            dokan_get_template_part( 'products/scheduled-product' );
        }
    }
    if ( isset( $query_vars['deals-ended'] ) ) {
        if ( ! current_user_can( 'dokan_add_product' ) ) {
            dokan_get_template_part( 'global/no-permission' );
        } else {
            dokan_get_template_part( 'products/ended-product' );
        }
    }
    if ( isset( $query_vars['pending-product'] ) ) {
        if ( ! current_user_can( 'dokan_add_product' ) ) {
            dokan_get_template_part( 'global/no-permission' );
        } else {
            dokan_get_template_part( 'products/pending-product' );
        }
    }
}

/*
* REBOOK DEALS LIST HERE
*/ 
add_action( 'wp_ajax_dd_rebook_deals', 'dd_rebook_deals_callback' );
function dd_rebook_deals_callback() {
	global $wp;    
    dokan_get_template_part( 'products/rebook-product' );
    exit();
}

//EDIT PRODUCT LINK FOR PUBLISH POST
function dd_edit_product_url( $product_id ) {
    $new_product_url = dokan_get_navigation_url( 'products' );

    return add_query_arg( array(
        'product_id' => $product_id,
        'action'     => 'edit',
    ), $new_product_url );
}


/*
* SELLER DEAL STATUS
*/
function add_deal_status(){
    /*register_post_status( 'inreview', array(
        'label'                     => _x('In Review', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'In Review <span class="count">(%s)</span>', 'In Review <span class="count">(%s)</span>' ),
    ) );*/
    register_post_status( 'editing', array(
        'label'                     => _x('Editing', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Editing <span class="count">(%s)</span>', 'Editing <span class="count">(%s)</span>' ),
    ) );
    register_post_status( 'finalized', array(
        'label'                     => _x('Finalized', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Finalized <span class="count">(%s)</span>', 'Finalized <span class="count">(%s)</span>' ),
    ) );
   /* register_post_status( 'upcoming', array(
        'label'                     => _x('Upcoming', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Upcoming <span class="count">(%s)</span>', 'Upcoming <span class="count">(%s)</span>' ),
    ) );*/
     register_post_status( 'declined', array(
        'label'                     => _x('Declined', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Declined <span class="count">(%s)</span>', 'Declined <span class="count">(%s)</span>' ),
    ) );
    /*register_post_status( 'active', array(
        'label'                     => _x('Active', 'woocommerce'),
        'exclude_from_search'       => false,
        'public'                    => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>' ),
    ) );*/
    register_post_status( 'shipping', array(
        'label'                     => _x('Shipping', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Shipping <span class="count">(%s)</span>', 'Shipping <span class="count">(%s)</span>' ),
    ) );
    register_post_status( 'ended', array(
        'label'                     => _x('Endend', 'woocommerce'),
        'exclude_from_search'       => false,
        'protected'                 => true,
        'post_type'                 => array( 'product' ),
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Endend <span class="count">(%s)</span>', 'Endend <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'add_deal_status' );

function seller_deal_status(){
	return $seller_status = array('pending' => 'In Review', 'editing' => 'Editing', 'finalized' => 'Finalized', 'publish' => 'Live',  'declined' => 'Declined',  'shipping' => 'Shipping', 'ended' => 'Endend');
}

function custom_status_add_in_quick_edit() {
    echo "<script>
    jQuery(document).ready( function() {
        /*jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"inreview\">In Review</option>' );*/ 
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"editing\">Editing</option>' ); 
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"finalized\">Finalized</option>' ); 
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"publish\">Live</option>' );
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"declined\">Declined</option>' );
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"shipping\">Shipping</option>' ); 
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"ended\">Endend</option>' );  
    }); 
    </script>";
}
add_action('admin_footer-edit.php','custom_status_add_in_quick_edit');
function custom_status_add_in_post_page() {
    echo "<script>
    jQuery(document).ready( function() {        
       /* jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"inreview\">In Review</option>' ); */
        jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"editing\">Editing</option>' ); 
        jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"finalized\">Finalized</option>' );
        jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"publish\">Live</option>' );
        jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"declined\">Declined</option>' );
        jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"shipping\">Shipping</option>' ); 
        jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"ended\">Endend</option>' );
    });
    </script>";
}
add_action('admin_footer-post.php', 'custom_status_add_in_post_page');
add_action('admin_footer-post-new.php', 'custom_status_add_in_post_page');

//CHANGE STAR RATING POSITION
add_action( 'woocommerce_single_product_summary', 'change_stars_rating_location', 6 );
function change_stars_rating_location() {
    global $product;
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 11 );
}

//ADD ATTRIBUTES ON THE PRODUCT DETAILS PAGE.
add_action( 'woocommerce_before_add_to_cart_button', 'add_attributes_on_product');
function add_attributes_on_product() {
    global $product,$wpdb;
    $post_id = get_the_ID();
    $product_attributes = $wpdb->prefix . "product_attributes"; 
    $product_variations = $wpdb->prefix . "product_variations"; 
    $attributs = $wpdb->get_results("SELECT * FROM {$product_attributes} WHERE product_id=$post_id");
    if (!empty($attributs)) {
        foreach ($attributs as $key => $attribut) {
           ?>
           <?php if($attribut->personalization == 'no'){ 
                $attr_id = $attribut->ID;
                $variations = $wpdb->get_results("SELECT * FROM {$product_variations} WHERE attr_id=$attr_id ");
                //print_r($attributs);
                $var_arr = array();
// 			   	$var_left = array();
                foreach ($variations as $key => $variation) {
// 					$left = ($variation->sold_qty)?$variation->qty-$variation->sold_qty:$variation->qty;
// 					array_push( $var_left, $variation->qty );
                   $var_arr[$variation->title] = ($variation->sold_qty)?$variation->qty-$variation->sold_qty:$variation->qty;
                }
            ?>
               <div class="dokan-form-group  form-group-select">
                    <span><?php echo $attribut->title;?></span>
				   <?php //var_dump($var_arr); //var_dump($attribut->variations); ?>
                    <select id="changeEve" class="select attributes" varitions='<?php echo json_encode($var_arr); ?>' data-combine="<?php echo $attribut->combine_status; ?>" name="product_attributes[<?php echo strtolower($attribut->title); ?>]">
                        <?php 
                        $variations = $attribut->variations;
                        $variations_arr = explode(',', $variations);
			   			if( $variations_arr ){ var_dump( $variations_arr ); }
                        if(!empty($variations_arr)){ ?>
                            <option value=" "><?php _e('Select an option...', 'woocommerce'); ?></option>
                            <?php //$disabled = 'disabled="disabled"';
								foreach ($variations_arr as $key => $variation) { 
									if($var_arr[$variation] <= 9 && $var_arr[$variation] == '0'){
										$var_num = '/ ('.$var_arr[$variation].' Left)';
									} else{
										if($var_arr[$variation] > 9 ){
											$var_num = '';
										} else{
											$no = $var_arr[$variation];
										( $no ) ? $var_num = '/ ('.strval($no) . ' Left)' : '';
										//$var_num = strval($no) . 'Left';	
										}
									}
								
						?>                  
                                <option data-left="<?php echo strval($var_arr[$variation]); ?>" <?php //if($var_arr[$variation] == '0'){echo $disabled;} ?> value="<?php echo $variation; ?>"><?php echo ucfirst($variation). " ". $var_num; ?></option>
                            <?php } ?>
                        <?php } ?>                
                    </select>
                </div>
            <?php }elseif($attribut->personalization == 'yes'){
					if($attribut->char_allowed == '0'){
						$allowed_char = '30';
					} else{
						$allowed_char = $attribut->char_allowed;
					}
								
?>       
                <div class="dokan-form-group form-group-text">
                    <span><?php echo $attribut->title; ?></span>
                    <input class="form-control attributes" maxlength="<?php echo $allowed_char; ?>" data-char="<?php echo $allowed_char; ?>"  type="text" name="product_attributes[<?php echo strtolower($attribut->title); ?>]">
                </div>
            <?php } 
        }
        ?>
		<p class="out-of-stock"></p>
        <p class="qtyleft"><strong id="qtyleft"></strong></p> 
        <input type="hidden" name="cart_combine_attributes">
        <?php 
    }
}

//ADD CART ITEM DATA HERE.
add_filter( 'woocommerce_add_cart_item_data', 'dd_save_cart_item_data', 10, 2 );
function dd_save_cart_item_data( $cart_item_meta, $product_id ) {

    global $woocommerce;
    
    if(isset($_POST['product_attributes']) && !empty($_POST['product_attributes'])){  
        $cart_item_meta['product_attributes'] = $_POST['product_attributes'];
    }
    if(isset($_POST['cart_combine_attributes']) && !empty($_POST['cart_combine_attributes'])){  
        $cart_item_meta['cart_combine_attributes'] = $_POST['cart_combine_attributes'];
    }
    
  return $cart_item_meta; 
} 

function dd_get_item_data($item_data_arr, $cart_item ){ 
    //print_r($cart_item['product_attributes']);
    $quantity = count($item_data_arr); 

    if(isset($cart_item['product_attributes']) && !empty($cart_item['product_attributes'])){
        foreach ($cart_item['product_attributes'] as $key => $attr) {
            $item_data_arr[$quantity] = array('key'=>ucfirst($key),'value'=>$attr);
            $quantity = $quantity+1;
        }
    
    }

    return $item_data_arr;
}  
add_filter( 'woocommerce_get_item_data', 'dd_get_item_data', 10, 2 );

//SAVE ATTRIBUTES IN CART ITEMS
add_action('woocommerce_add_order_item_meta','SaveItemMetaToOrder',10,3);  
function SaveItemMetaToOrder($item_id, $values,$cart_item_key){ 
    global $wpdb;
    $tbl_attributes = $wpdb->prefix."product_attributes"; 
    $tbl_variations = $wpdb->prefix."product_variations"; 
        
    $product_id = $values['product_id'];
    $quantity = $values['quantity'];
    $cart_combine_attributes = $values['cart_combine_attributes'];

    $shipping_price =  get_post_meta( $product_id, '_shipping_price', true);
    $Additional_shipping_price =  get_post_meta( $product_id, '_shippingPriceAdditionalItems', true);
    if ($quantity > 1 && !empty($Additional_shipping_price)) {
        $shipping_price = $shipping_price+($Additional_shipping_price*($quantity-1));
    }
    if (!empty($shipping_price)) {
        wc_update_order_item_meta($item_id,'_deal_shipping_price',$shipping_price); 
    }

    if(isset($values['product_attributes']) && !empty($values['product_attributes']))
    {
        //print_r($values['product_attributes']);
        wc_update_order_item_meta($item_id,'product_attributes',$values['product_attributes']); 
        $product_attributes = $values['product_attributes'];

        foreach ($product_attributes as $key => $pattr) 
        {
            
            $combine_attr_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT ID FROM {$tbl_attributes}
                WHERE LOWER(title)=%s AND product_id=%d AND combine_status='yes' ", strtolower($key),  $product_id) );            
            if (!empty($combine_attr_id)) {
                $row = $wpdb->get_row( $wpdb->prepare(
                "SELECT sold_qty,sku FROM {$tbl_variations}
                WHERE attr_id=%d AND title='%s' ", $combine_attr_id,  $cart_combine_attributes) );
                $sku = wc_get_order_item_meta( $item_id, 'sku', true );
                if (empty($sku)) {
                   $sku = $row->sku;
                }
                wc_update_order_item_meta($item_id,'sku',$sku);
                $data = [ 'sold_qty' => $quantity+$row->sold_qty ]; 
                $format = ['%d']; 
                $where = [ 'attr_id' => $combine_attr_id, 'title' => $cart_combine_attributes ];
                $where_format = ['%d','%s']; 
                $wpdb->update( $tbl_variations, $data, $where, $format, $where_format );
            }            
            $single_attr_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT ID FROM {$tbl_attributes}
                WHERE LOWER(title)=%s AND product_id=%d AND combine_status='no' ", strtolower($key),  $product_id) );
            if (!empty($single_attr_id)) {
                $row = $wpdb->get_row( $wpdb->prepare(
                "SELECT sold_qty,sku FROM {$tbl_variations}
                WHERE attr_id=%d AND title='%s' ", $single_attr_id,  trim($pattr)) );
                $sku = wc_get_order_item_meta( $item_id, 'sku', true );
                if (empty($sku)) {
                   $sku = $row->sku;
                }
                wc_update_order_item_meta($item_id,'sku',$sku);
                $data = [ 'sold_qty' => $quantity+$row->sold_qty ]; 
                $format = ['%d']; 
                $where = [ 'attr_id' => $single_attr_id, 'title' => trim($pattr) ];
                $where_format = ['%d','%s']; 
                $wpdb->update( $tbl_variations, $data, $where, $format, $where_format );
            }
        }
    }    
}

remove_action( 'woocommerce_order_item_meta_start', 'dokan_attach_vendor_name', 10, 2);

function dokan_attach_new_vendor_name( $item_id, $order ) {
    $product_id = $order->get_product_id();

    if ( ! $product_id ) {
        return;
    }

    $vendor_id = get_post_field( 'post_author', $product_id );
    $user_email = get_the_author_meta('user_email',$vendor_id); // retrieve user email

    $vendor    = dokan()->vendor->get( $vendor_id );

    if ( ! is_object( $vendor ) ) {
        return;
    }

    printf( '<br>%s: %s', esc_html__( 'Vendor', 'dokan-lite' ), esc_html__( $vendor->get_shop_name() ) );
    printf( '<br>%s: %s', esc_html__( 'Email', 'dokan-lite' ), $user_email );
}

add_action( 'woocommerce_order_item_meta_start', 'dokan_attach_new_vendor_name', 10, 2 );

add_action('woocommerce_order_item_meta_end', 'woo_add_order_item_meta', 30,3);
function woo_add_order_item_meta($item_id, $item, $order){
    $product_id = $item->get_product_id();
    $ships_date = get_post_meta( $product_id, '_ships_date', true);
    if (!empty($ships_date)) {
        echo '<br>';
        ?>
        <strong>Estimated to ship by <?php echo date('D, M d',strtotime($ships_date)); ?></strong><br>
        <?php         
    }
    $product_attributes = wc_get_order_item_meta($item_id,'product_attributes',true);
    if (!empty($product_attributes)) {       
       foreach ($product_attributes as $key => $attr) {
           ?>
           <strong><?php echo ucfirst($key); ?></strong>: <?php echo $attr; ?><br>
           <?php 
       }
    }
    //print_r($product_attributes);
}

//ADD PRODUCT VALIDATION ON ADD CART BUTTON
function add_the_product_item_validation( $passed ) { 
    global $wpdb;
    $tbl_attributes = $wpdb->prefix."product_attributes"; 
    $tbl_variations = $wpdb->prefix."product_variations"; 

    //print_r($_REQUEST['product_attributes']);
    $product_attributes = $_REQUEST['product_attributes'];
    if (isset($_REQUEST['product_attributes'])) {
        foreach ($product_attributes as $key => $value) {
            if (empty($value)) {
                wc_add_notice( __( 'Please select item type.', 'woocommerce' ), 'error' );
                $passed = false;
            }
        }
    }  

    if (isset($_REQUEST['product_attributes']))
    {
        $product_id = $_REQUEST['add-to-cart'];
        
        foreach ($product_attributes as $key => $pattr) 
        {
            $single_attr_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT ID FROM {$tbl_attributes}
                WHERE LOWER(title)=%s AND product_id=%d AND combine_status='no' ", strtolower($key),  $product_id) );
            $row = $wpdb->get_row( $wpdb->prepare(
                "SELECT sold_qty,qty FROM {$tbl_variations}
                WHERE attr_id=%d AND title='%s' ", $single_attr_id,  trim($pattr)) );

            if ($row->qty == $row->sold_qty && !empty($row->sold_qty) && !empty($row->qty)) {                
                wc_add_notice( __( 'This '.strtolower($key).' sold out.', 'woocommerce' ), 'error' );
                $passed = false;
            }
        }
    }
    
    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'add_the_product_item_validation', 10, 5 );  

//WOO CHANGE NO STOCK EMAIL CONTENT HERE.
add_filter('woocommerce_email_content_no_stock', 'WooChange_NoStock_Email');
function WooChange_NoStock_Email($message){
    $mailer = WC()->mailer();
    $email = new WC_Email(); 
    $email_heading = __('Product out of stock', 'woocommerce');
    return $message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $message ) ) );
}
//WOO CHANGE LOW STOCK EMAIL CONTENT HERE.
add_filter('woocommerce_email_content_low_stock', 'WooChange_LowStock_Email');
function WooChange_LowStock_Email($message){
    $mailer = WC()->mailer();
    $email = new WC_Email(); 
    $email_heading = __('Product low in stock', 'woocommerce');
    return $message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $message ) ) );
}

//CHANGE CART ITEM PRICE HERE
//add_filter( 'woocommerce_cart_product_subtotal', 'dd_cart_product_subtotal_callback', 99, 4 );
function dd_cart_product_subtotal_callback($product_subtotal, $product, $quantity, $object ){

    $price = $product->get_price();

    $product_id = $product->get_id();

    $qty = $quantity;
    $shipping_price =  get_post_meta( $product_id, '_shipping_price', true);
    $Additional_shipping_price =  get_post_meta( $product_id, '_shippingPriceAdditionalItems', true);
    if ($qty > 1 && !empty($Additional_shipping_price)) {
        $shipping_price = $shipping_price+($Additional_shipping_price*($qty-1));
    }

    if ( $product->is_taxable() ) {

        if ( $object->display_prices_including_tax() ) {
            $row_price        = wc_get_price_including_tax( $product, array( 'qty' => $quantity ) );
            $product_subtotal = wc_price( $row_price+$shipping_price );

            if ( ! wc_prices_include_tax() && $object->get_subtotal_tax() > 0 ) {
                $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
            }
        } else {
            $row_price        = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
            $product_subtotal = wc_price( $row_price+$shipping_price );

            if ( wc_prices_include_tax() && $object->get_subtotal_tax() > 0 ) {
                $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
            }
        }
    } else {
        $row_price        = $price * $quantity;
        $product_subtotal = wc_price( $row_price+$shipping_price );
    }

    return $product_subtotal;
}

//ADD SHIPPING SECTION ON CART PAGE.
add_action('woocommerce_cart_totals_before_order_total', 'dd_cart_totals_before_order_total' );
function dd_cart_totals_before_order_total(){
    if (isset($_SESSION['shipping_price'])) 
    {
        ?>
        <tr class="order-shipping">
            <th><?php _e( 'Shipping', 'woocommerce' ); ?></th>
            <td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php echo wc_price($_SESSION['shipping_price']); ?></td>
        </tr>
        <?php 
    }
}

//CHANGE CART TOTAL
add_filter( 'woocommerce_calculated_total', 'action_cart_calculate_totals', 99, 2 );
function action_cart_calculate_totals($total, $cart_object ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( !WC()->cart->is_empty() ){
        $shipping_cost = 0;
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            $qty = $cart_item['quantity'];
            $shipping_price =  get_post_meta( $product_id, '_shipping_price', true);
            if (!empty($shipping_price)) {
                $Additional_shipping_price =  get_post_meta( $product_id, '_shippingPriceAdditionalItems', true);
                if ($qty > 1 && !empty($Additional_shipping_price)) {
                    $shipping_price = $shipping_price+($Additional_shipping_price*($qty-1));
                }
                $shipping_cost += $shipping_price;
            }
            
        }
        
        return $total = $total+$shipping_cost;
    }
}

add_action( 'woocommerce_checkout_update_order_meta', 'SaveOrderCheckoutFields', 10, 2 );
function SaveOrderCheckoutFields( $order_id, $posted ){
    if (isset($_SESSION['shipping_price'])) 
    {
        update_post_meta( $order_id, '_shipping_price',sanitize_text_field($_SESSION['shipping_price']));
        unset($_SESSION['shipping_price']);
    }
}

/**
 * --------------------------------------------------------------------
 * Play with woocommerce hooks
 * -------------------------------------------------------------------
 */
/*function dd_woocommerce_hooks(){
    remove_action( 'basel_woocommerce_after_sidebar', 'woocommerce_output_related_products', 20 );
    remove_action( 'basel_woocommerce_after_sidebar', 'woocommerce_upsell_display', 10 );
    add_action( 'dd_related_product_content', 'woocommerce_output_related_products', 20 );
    add_action( 'dd_upsell_product_content', 'woocommerce_upsell_display', 20 );
    remove_all_actions('woocommerce_after_single_product_summary');
}
add_action( 'wp', 'dd_woocommerce_hooks', 1001 );*/


//SELLER COUNT TOTAL REVIEWS AND RATINGS
function seller_count_review_ratings($author_id){
    global $wpdb;
      
    $result = $wpdb->get_row( $wpdb->prepare(
    "SELECT AVG(cm.meta_value) as average, COUNT(wc.comment_ID) as count FROM $wpdb->posts p
    INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
    LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
    WHERE p.post_author = %d AND p.post_type = 'product' AND p.post_status = 'publish'
    AND ( cm.meta_key = 'rating' OR cm.meta_key IS NULL) AND wc.comment_approved = 1
    ORDER BY wc.comment_post_ID", $author_id ) );
    
    return $result;
}


/*GET SELLER TOTAL RATING*/
function seller_get_readable_rating($author_id) {

    global $wpdb;
      
    $result = seller_count_review_ratings($author_id);

    $rating = array(  'rating' => number_format( $result->average, 2 ),
            'count'  => (int) $result->count);

    if ( ! $rating['count'] ) {
        $html = __( 'No ratings found yet!', 'dokan-lite' );
    } else {
        $long_text   = _n( '%d review', '%d reviews', $rating['count'], 'dokan-lite' );
        $text        = sprintf( __( 'Rated %s out of %d', 'dokan-lite' ), $rating['rating'], number_format( 5 ) );
        $width       = ( $rating['rating']/5 ) * 100;
        $review_text = sprintf( $long_text, $rating['count'] );

        $review_text = $review_text;

        $html = '<span class="seller-rating">
                    <span title=" '. esc_attr( $text ) . '" class="star-rating" itemtype="http://schema.org/Rating" itemscope="" itemprop="reviewRating">
                        <span class="width" style="width: ' . $width . '%"></span>
                    </span>
                </span>
                ';
    }

    return $html ;
}

//GET GROUP COUNT RATINGS
function get_post_group_comment($pid){
    global $wpdb;   
    $result = $wpdb->get_results( $wpdb->prepare(
    "SELECT cm.meta_value as star, count(cm.meta_value) as total FROM $wpdb->posts p INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID WHERE p.post_type = 'product' AND p.post_status = 'publish' AND p.ID=%d AND wc.comment_approved = 1 AND (cm.meta_key = 'rating') GROUP BY cm.meta_value ORDER BY cm.meta_value DESC", $pid) );
    $stars = array(5=>0,4=>0,3=>0,2=>0,1=>0);
    if($result){
        foreach ($result as $key => $rate) {
            $stars[$rate->star] = $rate->total;
        }
    }
    return $stars;
}

//ADD REVIEWS ON BOTTOM OF THE PRODUCT
add_action( 'dd_reviews_content', 'comments_template');

//CHANGE COMMENT AUTHOR NAME
add_filter( 'get_comment_author', 'dd_get_comment_author_callback', 99, 3);
function dd_get_comment_author_callback($author, $comment_ID, $comment){
    $user = $comment->user_id ? get_userdata( $comment->user_id ) : false;
    if ( empty( $comment->comment_author ) ) {
        if ( $user ) {
            $author = !empty($user->first_name)?$user->first_name:$comment->comment_author;
        } else {
            $author = __( 'Anonymous' );
        }
    } else {
        $author = !empty($user->first_name)?$user->first_name:$comment->comment_author;
    }
    return $author;
}

//GET COMMENT LIST BY AJAX
add_action( 'wp_ajax_filter_comments', 'filter_comments_callback' );
add_action( 'wp_ajax_nopriv_filter_comments', 'filter_comments_callback' );
function filter_comments_callback(){

    $rate = $_POST['rate'];
    $post_id = $_POST['post_id'];
    
    if (!empty($rate) && !empty($post_id)) 
    {
        $args = array(
            'post__in' => $post_id,
            'meta_query' => array(
                array(
                    'key'   => 'rating',
                    'value' => $rate,
                    'compare' => '='
                )
            )
        );

        if ($rate == 'Any') {
            unset($args['meta_query']);
        }

        $comments_query = new WP_Comment_Query;
        $comments = $comments_query->query( $args );
        if( $comments ) :
            echo '<ol class="commentlist">';
            foreach( $comments as $comment ) :
                $GLOBALS['comment'] = $comment; // WPCS: override ok.
                wc_get_template(
                    'single-product/review.php',
                    array(
                        'comment' => $comment
                    )
                );           
            endforeach;
            echo '</ol>';
        else:
            echo '<p class="woocommerce-noreviews">There are no reviews yet.</p>';
        endif;
    }    
    die;
}


//GET DEAL END TIME DIFFERENT
function get_deal_time_difference($pid) {
    $startDate = get_post_meta($pid, '_startDate', true); 
    $endDate = get_post_meta($pid, '_endDate', true); 
    $data = array();
    if (!empty($startDate)) {
        $datetime1 = new DateTime(date('Y-m-d'));
        $enddate = date('Y-m-d', strtotime($endDate));        
        if (strtotime(date('Y-m-d')) <= strtotime($enddate)) {
            $datetime2 = new DateTime($enddate);
            $difference = $datetime1->diff($datetime2);
            $dayleft = $difference->d;
            $data['dayleft'] = $dayleft;
        }else{
            $data['dayleft'] = 0;
        }
        $data['enddate'] = $enddate;    
    }
    
    return $data;
}


//GET DEAL END TIME DIFFERENT
function get_deal_upcoming_time_difference($pid) {
    $startDate = get_post_meta($pid, '_startDate', true); 
    $data = array();
    if (!empty($startDate)) {
        $datetime1 = new DateTime(date('Y-m-d'));
        $enddate = date('Y-m-d', strtotime($startDate));        
        if (strtotime(date('Y-m-d')) <= strtotime($enddate)) {
            $datetime2 = new DateTime($enddate);
            $difference = $datetime1->diff($datetime2);
            $dayleft = $difference->d;
            $data['dayleft'] = $dayleft;
        }else{
            $data['dayleft'] = 0;
        }
        $data['enddate'] = $enddate;    
    }
    
    return $data;
}

//SET DEAL SHIPPING STATUS 
add_action( 'wp_ajax_deal_ended_status', 'deal_ended_status_callback' );
add_action( 'wp_ajax_nopriv_deal_ended_status', 'deal_ended_status_callback' );
function deal_ended_status_callback(){

    check_ajax_referer( 'dd-nonce', 'nonce' );

    $post_id = sanitize_text_field($_POST['post_id']);
    if (!empty($post_id)) 
    {        
        $update_post = array(
            'ID'           => $post_id,
            'post_status'   => 'shipping',
        );
        wp_update_post( $update_post );
        dd_deal_shipping_notification($post_id);
    }    
    die;
}

//GET PRODUCT ORDERS
function dd_get_deal_orders($pid,$record='count'){
    global $wpdb;   
    $items = $wpdb->prefix.'woocommerce_order_items';
    $itemmeta = $wpdb->prefix.'woocommerce_order_itemmeta';
    $sql = "SELECT SUM(imeta2.meta_value) FROM $wpdb->posts p, {$items} item, {$itemmeta} imeta, {$itemmeta} imeta2 WHERE p.post_type='shop_order' AND (p.post_status != 'trash' AND p.post_status != 'wc-cancelled')  AND p.ID = item.order_id AND item.order_item_id=imeta.order_item_id AND imeta.meta_key='_product_id' AND imeta.meta_value=%d AND item.order_item_id=imeta2.order_item_id AND imeta2.meta_key='_qty'";
    //echo $wpdb->prepare($sql, $pid);
    if ($record == 'count') {
        $result = $wpdb->get_var( $wpdb->prepare($sql, $pid) );
    }else{
        $result = $wpdb->get_results( $wpdb->prepare($sql, $pid) );
    }
    return $result;
}

//GET PRODUCT SHIPPED ORDERS
function dd_get_deal_shipped_orders($pid){
    global $wpdb;   
    $items = $wpdb->prefix.'woocommerce_order_items';
    $itemmeta = $wpdb->prefix.'woocommerce_order_itemmeta';
    $tracking = $wpdb->prefix.'order_tracking';
    $sql = "SELECT SUM(imeta2.meta_value) FROM $wpdb->posts p, {$items} item, {$itemmeta} imeta, {$itemmeta} imeta2, {$tracking} tc WHERE p.post_type='shop_order' AND (p.post_status != 'trash' AND p.post_status != 'wc-cancelled') AND p.ID = tc.order_id AND tc.trackingnumber != '' AND p.ID = item.order_id AND item.order_item_id=imeta.order_item_id AND imeta.meta_key='_product_id' AND imeta.meta_value=%d AND item.order_item_id=imeta2.order_item_id AND imeta2.meta_key='_qty'";
    $result = $wpdb->get_var( $wpdb->prepare($sql, $pid) );
    //print_r($result);
    return $result;
}

//COUNT DEAL LIKES
add_action( 'wp_ajax_count_deal_like', 'count_deal_like_callback');
add_action( 'wp_ajax_nopriv_count_deal_like', 'count_deal_like_callback');
function count_deal_like_callback(){
    
    check_ajax_referer( 'dd-nonce', 'nonce' );
    $post_id = sanitize_text_field($_POST['post_id']);
    if (!empty($post_id)) 
    {
        echo dd_count_product_likes($post_id);
    }    
    die;
}
function dd_count_product_likes($pid){
    global $wpdb;   
    $yith_wcwl = $wpdb->prefix.'yith_wcwl';
    $sql = "SELECT COUNT(ID) FROM {$yith_wcwl} WHERE prod_id=%d";
    $result = $wpdb->get_var( $wpdb->prepare($sql, $pid) );
    return $result;
}

//CHANGE PRODUCT TITLE HTML HERE.
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'dd_template_loop_product_title', 10 );
function dd_template_loop_product_title() {
    $title = get_the_title();
    $len = strlen($title);
    //$title = ($len > 15 && is_single())?substr($title,0,15).'...':$title;   
    echo '<h3 class="product-title"><a href="' . get_the_permalink() . '">' .$title. '</a></h3>';
}

//ADD SHIPPING COST ON CHECKOUT PAGE
add_filter('woocommerce_get_order_item_totals', 'add_shipping_cost_on_checkout', 19, 3 );
function add_shipping_cost_on_checkout($total_rows, $obj, $tax_display){
    $order_id = $obj->get_id();
    $shipping_price = get_post_meta( $order_id, '_shipping_price',true);
    if (!empty($shipping_price)) {
        $total_rows = array_slice($total_rows, 0, 1, true) +
        array("shipping_price" => array('label' => 'Shipping:', 'value' => wc_price($shipping_price)) )+
        array_slice($total_rows, 1, count($total_rows) - 1, true) ;
    }
    return $total_rows;
}
//CHANGE COMMENT EMAIL CONTENT HERE.
add_filter('comment_notification_text', 'change_comment_notification_content', 99, 2);
function change_comment_notification_content($message,$comment_ID){
    $mailer = WC()->mailer();
    $email = new WC_Email(); 

    $comment = get_comment( $comment_ID );
    $post   = get_post( $comment->comment_post_ID );
    $author = get_userdata( $post->post_author );
    $name = $author->first_name;
    $deal_title = $post->post_title;

    $wstheme_options = get_wstheme_options();
    $email_heading  = $wstheme_options['customer_review_heading']; 
    $user_message = $wstheme_options['customer_review_temp'];
    $blogname = get_option('blogname');

    $data = array('deal_name'=> $deal_title,'site_name' => $blogname,'vendor_name' => ucfirst($name),'customer_review_link' => dokan_get_navigation_url( 'customer-reviews' ));
    $search_key_array = array('[deal_name]' => 'deal_name','[site_name]' => 'site_name','[vendor_name]' => 'vendor_name','[customer_review_link]' => 'customer_review_link');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $content  = wpautop($user_message);     

    $message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $content ) ) );

    return $message;
}
//CHANGE COMMENT EMAIL SUBJECT HERE.
add_filter( 'comment_notification_subject', 'change_comment_notification_subject',99,2);
function change_comment_notification_subject($subject,$comment_ID){
    $comment = get_comment( $comment_ID );
    $post = get_post( $comment->comment_post_ID );
    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['customer_review_sub']; 
    $blogname = get_option('blogname');
    $data = array('deal_name'=> $post->post_title,'site_name' => $blogname);
    $search_key_array = array('[deal_name]' => 'deal_name','[site_name]' => 'site_name');
    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
    }
    return $subject = $user_subject;
}

//DEALS DOWNLOADS
add_action( 'init', 'DealsCsvDownloads' );
function DealsCsvDownloads(){

    //ACTIVE DEAL DOWNLOAD
    if (isset($_POST['deal_download']) 
        && $_POST['deal_download'] == 'active' 
        && isset($_POST['deal_active_nonce']) 
        && wp_verify_nonce( $_POST['deal_active_nonce'], 'deal_download_action')) 
    {
        $args = array(
            'posts_per_page' => -1,
            'post_status'    => array( 'publish','upcoming'),
            'author'         => get_current_user_id(),
        );

        if (current_user_can( 'administrator' ) ) {
            unset($args['author']);
        }       

        $filename = "deal_summaries_".date('Ymd').".csv";
        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            unset($args['meta_query']);
            $args['meta_query'][] = array(
                'key'       => '_dd_post_status',
                'value'     => $_GET['filter'],
                'compare'   => '=',
            );

            $filename = "deal_summaries_".$_GET['filter'].'_'.date('Ymd').".csv";
        }

        $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );
        $seller_deal_status = seller_deal_status();

        if ( $product_query->have_posts() ) {

            $fp = fopen('php://output', 'w');

            $header = array();
            $header[] = 'DealId';
            $header[] = 'Status';
            $header[] = 'StartDate'; 
            $header[] = 'EndDate';      
            $header[] = 'ShippingEndDate';  
            $header[] = 'Title';  
            $header[] = 'PrimaryCategory';
            $header[] = 'Price';
            $header[] = 'Retail';
            $header[] = 'ShippingFirstItem';
            $header[] = 'ShippingAdditionalItems';
            $header[] = 'Quantity';
            $header[] = 'QuantitySold';
            $header[] = 'QuantityShipped';

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            fputcsv($fp, $header);

            while ($product_query->have_posts()) {
                global $post;
                $product_query->the_post();
                $product = wc_get_product( $post->ID );
                $post_metas =  get_post_meta($post->ID);
               
                $post_status =  ($post_metas['_dd_post_status'])?$post_metas['_dd_post_status'][0]:'';                        
                $startDate =  ($post_metas['_startDate'])?$post_metas['_startDate'][0]:'';                        
                $ships_date =  ($post_metas['_ships_date'])?$post_metas['_ships_date'][0]:'';                        
                $term = wp_get_post_terms($post->ID, 'product_cat');
                $sale_price =  ($post_metas['_sale_price'])?$post_metas['_sale_price'][0]:'';  
                $regular_price =  ($post_metas['_regular_price'])?$post_metas['_regular_price'][0]:'';                
                $shipping_price =  ($post_metas['_shipping_price'])?$post_metas['_shipping_price'][0]:'';  
                $shippingPriceAdditionalItems =  ($post_metas['_shippingPriceAdditionalItems'])?$post_metas['_shippingPriceAdditionalItems'][0]:'';  
                $totalQtyAvailable =  ($post_metas['_totalQtyAvailable'])?$post_metas['_totalQtyAvailable'][0]:'';  
                $sold = dd_get_deal_orders($post->ID);

                $res = array();
                $res[] = $post->ID;
                $res[] = $seller_deal_status[$post_status];
                $res[] = date('Y-m-d', strtotime($startDate));
                $res[] = date('Y-m-d', strtotime("+3 day", strtotime($startDate)));
                $res[] = date('Y-m-d', strtotime($ships_date));
                $res[] = $product->get_title();
                $res[] = $term[0]->name;
                $res[] = $sale_price;
                $res[] = $regular_price;
                $res[] = $shipping_price;
                $res[] = $shippingPriceAdditionalItems;
                $res[] = $totalQtyAvailable;
                $res[] = $sold;
                $res[] = '';

                fputcsv($fp, $res);
            }
            exit();
        }
    }

    //SHIPPING DEAL DOWNLOAD
    if (isset($_POST['deal_download']) 
        && $_POST['deal_download'] == 'shipping' 
        && isset($_POST['deal_shipping_nonce']) 
        && wp_verify_nonce( $_POST['deal_shipping_nonce'], 'deal_download_action')) 
    {
        $args = array(
            'posts_per_page' => -1,
            'post_status'    => array( 'publish' ),
            'post_status'    => array( 'shipping'),
            'author'         => get_current_user_id(),
        );

        if (current_user_can( 'administrator' ) ) {
            unset($args['author']);
        }

        $args['meta_query'][] = array(
                        'key'       => '_dd_post_status',
                        'value'     => 'shipping',
                        'compare'   => '=',
                    );

        $filename = "deal_summaries_".date('Ymd').".csv";
        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            unset($args['meta_query']);
            $args['meta_query'][] = array(
                'key'       => '_dd_post_status',
                'value'     => $_GET['filter'],
                'compare'   => '=',
            );

            $filename = "deal_summaries_".$_GET['filter'].'_'.date('Ymd').".csv";
        }

        $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );
        $seller_deal_status = seller_deal_status();

        if ( $product_query->have_posts() ) {

            $fp = fopen('php://output', 'w');

            $header = array();
            $header[] = 'DealId';
            $header[] = 'Status';
            $header[] = 'StartDate'; 
            $header[] = 'EndDate';      
            $header[] = 'ShippingEndDate';  
            $header[] = 'Title';  
            $header[] = 'PrimaryCategory';
            $header[] = 'Price';
            $header[] = 'Retail';
            $header[] = 'ShippingFirstItem';
            $header[] = 'ShippingAdditionalItems';
            $header[] = 'Quantity';
            $header[] = 'QuantitySold';
            $header[] = 'QuantityShipped';

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            fputcsv($fp, $header);

            while ($product_query->have_posts()) {
                global $post;
                $product_query->the_post();
                $product = wc_get_product( $post->ID );
                $post_metas =  get_post_meta($post->ID);
               
                $post_status =  ($post_metas['_dd_post_status'])?$post_metas['_dd_post_status'][0]:'';                        
                $startDate =  ($post_metas['_startDate'])?$post_metas['_startDate'][0]:'';                        
                $ships_date =  ($post_metas['_ships_date'])?$post_metas['_ships_date'][0]:'';                        
                $term = wp_get_post_terms($post->ID, 'product_cat');
                $sale_price =  ($post_metas['_sale_price'])?$post_metas['_sale_price'][0]:'';  
                $regular_price =  ($post_metas['_regular_price'])?$post_metas['_regular_price'][0]:'';                
                $shipping_price =  ($post_metas['_shipping_price'])?$post_metas['_shipping_price'][0]:'';  
                $shippingPriceAdditionalItems =  ($post_metas['_shippingPriceAdditionalItems'])?$post_metas['_shippingPriceAdditionalItems'][0]:'';  
                $totalQtyAvailable =  ($post_metas['_totalQtyAvailable'])?$post_metas['_totalQtyAvailable'][0]:'';  
                $sold = dd_get_deal_orders($post->ID);

                $res = array();
                $res[] = $post->ID;
                $res[] = $seller_deal_status[$post_status];
                $res[] = date('Y-m-d', strtotime($startDate));
                $res[] = date('Y-m-d', strtotime("+3 day", strtotime($startDate)));
                $res[] = date('Y-m-d', strtotime($ships_date));
                $res[] = $product->get_title();
                $res[] = $term[0]->name;
                $res[] = $sale_price;
                $res[] = $regular_price;
                $res[] = $shipping_price;
                $res[] = $shippingPriceAdditionalItems;
                $res[] = $totalQtyAvailable;
                $res[] = $sold;
                $res[] = '';

                fputcsv($fp, $res);
            }
            exit();
        }
    }

    //PENDING DEAL DOWNLOAD
    if (isset($_POST['deal_download']) 
        && $_POST['deal_download'] == 'pending' 
        && isset($_POST['deal_pending_nonce']) 
        && wp_verify_nonce( $_POST['deal_pending_nonce'], 'deal_download_action')) 
    {
        $args = array(
            'posts_per_page' => -1,
            'post_status'    => array( 'pending', 'beautify', 'settingup' ),
            'author'         => get_current_user_id(),
        );

        if (current_user_can( 'administrator' ) ) {
            unset($args['author']);
        }

        $filename = "deal_summaries_".date('Ymd').".csv";
        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            unset($args['post_status']);
            $args['post_status'] = array($_GET['filter']);

            $filename = "deal_summaries_".$_GET['filter'].'_'.date('Ymd').".csv";
        }

        $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );
        $seller_deal_status = seller_deal_status();

        if ( $product_query->have_posts() ) {

            $fp = fopen('php://output', 'w');

            $header = array();
            $header[] = 'DealId';
            $header[] = 'Status';
            $header[] = 'StartDate'; 
            $header[] = 'EndDate';      
            $header[] = 'ShippingEndDate';  
            $header[] = 'Title';  
            $header[] = 'PrimaryCategory';
            $header[] = 'Price';
            $header[] = 'Retail';
            $header[] = 'ShippingFirstItem';
            $header[] = 'ShippingAdditionalItems';
            $header[] = 'Quantity';
            $header[] = 'QuantitySold';
            $header[] = 'QuantityShipped';

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            fputcsv($fp, $header);

            while ($product_query->have_posts()) {
                global $post;
                $product_query->the_post();
                $product = wc_get_product( $post->ID );
                $post_metas =  get_post_meta($post->ID);
               
                $post_status =  ($post_metas['_dd_post_status'])?$post_metas['_dd_post_status'][0]:'';                        
                $startDate =  ($post_metas['_startDate'])?$post_metas['_startDate'][0]:'';                        
                $ships_date =  ($post_metas['_ships_date'])?$post_metas['_ships_date'][0]:'';                        
                $term = wp_get_post_terms($post->ID, 'product_cat');
                $sale_price =  ($post_metas['_sale_price'])?$post_metas['_sale_price'][0]:'';  
                $regular_price =  ($post_metas['_regular_price'])?$post_metas['_regular_price'][0]:'';                
                $shipping_price =  ($post_metas['_shipping_price'])?$post_metas['_shipping_price'][0]:'';  
                $shippingPriceAdditionalItems =  ($post_metas['_shippingPriceAdditionalItems'])?$post_metas['_shippingPriceAdditionalItems'][0]:'';  
                $totalQtyAvailable =  ($post_metas['_totalQtyAvailable'])?$post_metas['_totalQtyAvailable'][0]:'';  
                $sold = dd_get_deal_orders($post->ID);

                $res = array();
                $res[] = $post->ID;
                $res[] = $seller_deal_status[$post_status];
                $res[] = date('Y-m-d', strtotime($startDate));
                $res[] = date('Y-m-d', strtotime("+3 day", strtotime($startDate)));
                $res[] = date('Y-m-d', strtotime($ships_date));
                $res[] = $product->get_title();
                $res[] = $term[0]->name;
                $res[] = $sale_price;
                $res[] = $regular_price;
                $res[] = $shipping_price;
                $res[] = $shippingPriceAdditionalItems;
                $res[] = $totalQtyAvailable;
                $res[] = $sold;
                $res[] = '';

                fputcsv($fp, $res);
            }
            exit();
        }
    }
    //HISTORY DEAL DOWNLOAD
    if (isset($_POST['deal_download']) 
        && $_POST['deal_download'] == 'history' 
        && isset($_POST['deal_history_nonce']) 
        && wp_verify_nonce( $_POST['deal_history_nonce'], 'deal_download_action')) 
    {
        $args = array(
            'posts_per_page' => -1,
            'post_status'    => 'ended',
            'author'         => get_current_user_id(),
        );

        if (current_user_can( 'administrator' ) ) {
            unset($args['author']);
        }

        $filename = "deal_summaries_".date('Ymd').".csv";
        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            unset($args['post_status']);
            $args['post_status'][] = $_GET['filter'];
            $filename = "deal_summaries_".$_GET['filter'].'_'.date('Ymd').".csv";
        }

        $product_query = dokan()->product->all( apply_filters( 'dokan_product_listing_arg', $args ) );
        $seller_deal_status = seller_deal_status();

        if ( $product_query->have_posts() ) {

            $fp = fopen('php://output', 'w');

            $header = array();
            $header[] = 'DealId';
            $header[] = 'Status';
            $header[] = 'StartDate'; 
            $header[] = 'EndDate';      
            $header[] = 'ShippingEndDate';  
            $header[] = 'Title';  
            $header[] = 'PrimaryCategory';
            $header[] = 'Price';
            $header[] = 'Retail';
            $header[] = 'ShippingFirstItem';
            $header[] = 'ShippingAdditionalItems';
            $header[] = 'Quantity';
            $header[] = 'QuantitySold';
            $header[] = 'QuantityShipped';

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename);
            fputcsv($fp, $header);

            while ($product_query->have_posts()) {
                global $post;
                $product_query->the_post();
                $product = wc_get_product( $post->ID );
                $post_metas =  get_post_meta($post->ID);
               
                $post_status =  ($post_metas['_dd_post_status'])?$post_metas['_dd_post_status'][0]:'';                        
                $startDate =  ($post_metas['_startDate'])?$post_metas['_startDate'][0]:'';                        
                $endDate =  ($post_metas['_endDate'])?$post_metas['_endDate'][0]:'';                        
                $ships_date =  ($post_metas['_ships_date'])?$post_metas['_ships_date'][0]:'';                        
                $term = wp_get_post_terms($post->ID, 'product_cat');
                $sale_price =  ($post_metas['_sale_price'])?$post_metas['_sale_price'][0]:'';  
                $regular_price =  ($post_metas['_regular_price'])?$post_metas['_regular_price'][0]:'';                
                $shipping_price =  ($post_metas['_shipping_price'])?$post_metas['_shipping_price'][0]:'';  
                $shippingPriceAdditionalItems =  ($post_metas['_shippingPriceAdditionalItems'])?$post_metas['_shippingPriceAdditionalItems'][0]:'';  
                $totalQtyAvailable =  ($post_metas['_totalQtyAvailable'])?$post_metas['_totalQtyAvailable'][0]:'';  
                $sold = dd_get_deal_orders($post->ID);

                $res = array();
                $res[] = $post->ID;
                $res[] = $seller_deal_status[$post_status];
                $res[] = date('Y-m-d', strtotime($startDate));
                $res[] = date('Y-m-d', strtotime($endDate));
                $res[] = date('Y-m-d', strtotime($ships_date));
                $res[] = $product->get_title();
                $res[] = $term[0]->name;
                $res[] = $sale_price;
                $res[] = $regular_price;
                $res[] = $shipping_price;
                $res[] = $shippingPriceAdditionalItems;
                $res[] = $totalQtyAvailable;
                $res[] = $sold;
                $res[] = '';

                fputcsv($fp, $res);
            }
            exit();
        }
    }
}

/*
* ADD CRON JOB SCHEDULE
*/
if ( ! wp_next_scheduled( 'deal_daily_cron_event' ) ) {
    wp_schedule_event(time(), 'twicedaily', 'deal_daily_cron_event');
}
add_action('deal_daily_cron_event', 'deal_daily_cron_event_handler');
//add_action('init', 'deal_daily_cron_event_handler', 9999);
function deal_daily_cron_event_handler() {

    //if (isset($_GET['dealstatus']) && !empty($_GET['dealstatus'])){

        global $wpdb;

        $postmeta = $wpdb->prefix.'postmeta';
        $posts = $wpdb->prefix.'posts';
        $cdate = date('Y-m-d');


        //VENDOR FAILED TO FINALIZED DEAL SEND EMAIL TO ADMIN  
        $failed_finalized_deals = $wpdb->get_results("
            SELECT 
                DISTINCT p.ID 
            FROM 
                {$posts} p 
                INNER JOIN {$postmeta} pm ON pm.post_id = p.ID 
            WHERE 
                p.post_type='product' 
                AND p.post_status = 'editing'
                AND pm.meta_key = '_finalize_deal_date' 
                AND pm.meta_value = '$cdate'
            GROUP BY 
                p.ID 
            ");
       
        if (!empty($failed_finalized_deals)) {
            foreach ($failed_finalized_deals as $post) {
                $product_id = $post->ID;
                $not_finalized = get_post_meta($product_id, '_vendor_not_finalized',true);
                if (empty($not_finalized)) {
                    vendor_not_finalized_deal_admin_notification($product_id);
                    update_post_meta($product_id, '_vendor_not_finalized',1);
                }
            }
        }

        //CHANGE DEAL UPCOMING STATUS  
        $finalize_date = date('Y-m-d', strtotime($cdate.' +2 day'));
        $finalize_deals = $wpdb->get_results("
            SELECT 
                DISTINCT p.ID 
            FROM 
                {$posts} p 
                INNER JOIN {$postmeta} pm ON pm.post_id = p.ID 
            WHERE 
                p.post_type='product' 
                AND p.post_status = 'editing'
                AND pm.meta_key = '_finalize_deal_date' 
                AND pm.meta_value = '$finalize_date'
            GROUP BY 
                p.ID 
            ");
       
        if (!empty($finalize_deals)) {
            foreach ($finalize_deals as $post) {
                $product_id = $post->ID;
                /*$update_post = array(
                    'ID'           => $product_id,
                    'post_status'   => 'finalized',
                );
                wp_update_post( $update_post );*/
                $not_finalized = get_post_meta($product_id, '_reminder_vendor_not_finalized',true);
                if (empty($not_finalized)) {
                    vendor_not_finalized_deal_vendor_notification($product_id);
                    update_post_meta($product_id, '_reminder_vendor_not_finalized',1);
                }
                
            }
        }

        //CHANGE DEAL ACTIVE STATUS
        $upcoming_deals = $wpdb->get_results("
            SELECT 
               DISTINCT p.ID 
            FROM 
                {$posts} p 
                INNER JOIN {$postmeta} pm ON pm.post_id = p.ID 
            WHERE 
                p.post_type='product' 
                AND p.post_status = 'finalized'
                AND pm.meta_key = '_startDate'
                AND pm.meta_value = '$cdate' 
            GROUP BY 
                p.ID
            ");   
       
        if (!empty($upcoming_deals)) {
            foreach ($upcoming_deals as $post) {
                $product_id = $post->ID;
                //update_post_meta( $product_id, '_dd_post_status', 'active' );
                $update_post = array(
                    'ID'           => $product_id,
                    'post_status'   => 'publish',
                );
                wp_update_post( $update_post );
                dd_deal_active_notification($product_id);
            }
        }

        //CHANGE DEAL SHIPPING STATUS
        $active_deals = $wpdb->get_results("
            SELECT 
               DISTINCT p.ID 
            FROM 
                {$posts} p 
                INNER JOIN {$postmeta} pm ON pm.post_id = p.ID 
            WHERE 
                p.post_type='product' 
                AND p.post_status = 'publish'
                AND pm.meta_key = '_endDate' 
                AND pm.meta_value = '$cdate'
            GROUP BY 
                p.ID
            ");
        
        if (!empty($active_deals)) {
            foreach ($active_deals as $post) {
                $product_id = $post->ID;
                //update_post_meta( $product_id, '_dd_post_status', 'shipping' );
                $update_post = array(
                    'ID'           => $product_id,
                    'post_status'  => 'shipping',
                );
                wp_update_post( $update_post );
                dd_deal_shipping_notification($product_id);
            }
        }

        //SEND REMINDER TO VENDOR SHIP ORDER
        $ship_reminder_date = date('Y-m-d', strtotime($cdate.' +2 day'));
        $shipping_orders = $wpdb->get_results("
            SELECT 
               DISTINCT p.ID 
            FROM 
                {$posts} p 
                INNER JOIN {$postmeta} pm ON pm.post_id = p.ID 
            WHERE 
                p.post_type='product' 
                AND p.post_status = 'shipping'
                AND pm.meta_key = '_ships_date' 
                AND pm.meta_value = '$ship_reminder_date' 
            GROUP BY 
                p.ID
            ");
       
        //print_r($shipping_deals);
        if (!empty($shipping_orders)) {
            foreach ($shipping_orders as $post) {
                $product_id = $post->ID;
                $sold = dd_get_deal_orders($product_id);
                $shipped = dd_get_deal_shipped_orders($product_id);
                $vendor_not_shipped = get_post_meta($product_id, '_vendor_not_shipped',true);
                if (!empty($sold) && $sold != $shipped && empty($vendor_not_shipped)) {
                    reminder_to_ship_orders_to_vendor_notification($product_id);
                    update_post_meta($product_id, '_vendor_not_shipped',1);
                }         
            }
        }

        //CHANGE DEAL ENDED STATUS
        $shipping_deals = $wpdb->get_results("
            SELECT 
               DISTINCT p.ID 
            FROM 
                {$posts} p 
                INNER JOIN {$postmeta} pm ON pm.post_id = p.ID 
            WHERE 
                p.post_type='product' 
                AND p.post_status = 'shipping'
                AND pm.meta_key = '_ships_date' 
                AND pm.meta_value = '$cdate' 
            GROUP BY 
                p.ID
            ");
       
        //print_r($shipping_deals);
        if (!empty($shipping_deals)) {
            foreach ($shipping_deals as $post) {
                $product_id = $post->ID;
                //update_post_meta( $product_id, '_dd_post_status', 'ended' );
                $update_post = array(
                    'ID'           => $product_id,
                    'post_status'   => 'ended',
                );
                wp_update_post( $update_post );   
                dd_deal_ended_notification($product_id);             
            }
        }   
    //}
}

add_action('wp', 'testheemilhere');
function testheemilhere(){
    if (isset($_GET['testheemilhere'])) {
       vendor_not_finalized_deal_vendor_notification(2752);
    }
}

//VENDOR NOT FINALIZED DEAL EMAIL 
function vendor_not_finalized_deal_vendor_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = isset($seller_emails['deal'])?$seller_emails['deal']:$author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['reminder_finalize_deal_sub']; 
    $email_heading  = $wstheme_options['reminder_finalize_deal_heading']; 
    $user_message = $wstheme_options['reminder_finalize_deal_temp'];

    $data = array('deal_title'=> $deal_title,'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message);     
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}


//VENDOR NOT FINALIZED DEAL EMAIL 
function reminder_to_ship_orders_to_vendor_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);

    $seller_email = $author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['reminder_ship_order_sub']; 
    $email_heading  = $wstheme_options['reminder_ship_order_heading']; 
    $user_message = $wstheme_options['reminder_ship_order_temp'];

    $order_url = add_query_arg('pid', base64_encode($product_id), esc_url( dokan_get_navigation_url('orders')));
    $data = array('deal_title'=> $deal_title,'order_url' => $order_url,'vendor_name' => ucfirst($author_obj->first_name));
    $search_key_array = array('[deal_title]' =>'deal_title','[order_url]' => 'order_url','[vendor_name]' => 'vendor_name');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message);     
    dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}

//VENDOR NOT FINALIZED DEAL EMAIL TO ADMIN 
function vendor_not_finalized_deal_admin_notification($product_id){

    $post_data = get_post( $product_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;
    $author_obj = get_user_by('id', $author_id);
    $startDate = get_post_meta( $product_id, '_startDate', true );
    $finalize_deal_date = get_post_meta( $product_id, '_finalize_deal_date', true );

    $seller_emails = get_user_meta($author_id, '_seller_emails', true);
    $seller_email = isset($seller_emails['deal'])?$seller_emails['deal']:$author_obj->user_email;    

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['vffd_email_sub']; 
    $email_heading  = $wstheme_options['vffd_email_heading']; 
    $user_message = $wstheme_options['vffd_email_temp'];

    $data = array('deal_title'=> $deal_title,'deal_edit_url' => dd_edit_product_url($product_id),'vendor_name' => ucfirst($author_obj->first_name),'deal_start_date' => date('l, F d, Y', strtotime($startDate)),'finalize_date' => date('F d', strtotime($finalize_deal_date)));

    $search_key_array = array('[deal_title]' =>'deal_title','[deal_edit_url]' => 'deal_edit_url','[vendor_name]' => 'vendor_name', '[deal_start_date]' => 'deal_start_date','[finalize_date]' => 'finalize_date');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message);     
    dd_send_email_handler('', $user_subject, $user_message, $email_heading, true);
}


//CHANGE WOOCOMMERCE PRODUCT TABS
function product_shipping_info() {
    wc_get_template( 'single-product/tabs/shipping_info.php' );
}
function product_vendor_info() {
    wc_get_template( 'single-product/tabs/vendor_info.php' );
}
function product_retun_policy() {
    wc_get_template( 'single-product/tabs/retun_policy.php' );
}

add_filter( 'woocommerce_product_tabs', 'custom_wc_product_tabs', 30);
function custom_wc_product_tabs($tabs){    
    unset($tabs['shipping']);
    unset($tabs['seller']);
    $tabs['shipping_info'] = array(
        'title'    => __( 'Shipping Info', 'woocommerce' ),
        'priority' => 13,
        'callback' => 'product_shipping_info',
    );
    $tabs['vendor'] = array(
        'title'    => __( 'Vendor Info', 'woocommerce' ),
        'priority' => 15,
        'callback' => 'product_vendor_info',
    );
     $tabs['retun_policy'] = array(
        'title'    => __( 'Return Policy', 'woocommerce' ),
        'priority' => 17,
        'callback' => 'product_retun_policy',
    );
    //print_r($tabs);
    return $tabs;
}


//CROP PRODUCT IMAEG HERE.
add_action( 'wp_ajax_dd_crop_deal_image', 'dd_crop_deal_image_callback' );
function dd_crop_deal_image_callback(){

    $attach_id = $_POST['attach_id'];
    $isfeat = $_POST['isfeat'];
   
    //check_ajax_referer( 'dd_image_crop', 'imgnonce' );

    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
     
    $uploadedfile = $_FILES['dealimg'];
     
    $upload_overrides = array(
        'test_form' => false
    );
     
    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
    //print_r($movefile);
    if ( $movefile && !isset( $movefile['error'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
        //print_r($attach_data);
        update_post_meta($attach_id,'_wp_attached_file', $attach_data['file']);
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        global $wpdb;
        $posts = $wpdb->prefix.'posts';

        $data  = array('guid' => $movefile['url']);
        $where  = array('ID' => $attach_id);
        $format  = array('%s');
        $where_format  = array('%d');
        $wpdb->update( $posts, $data, $where, $format, $where_format );
        /*if (! empty($isfeat) ) {
           set_post_thumbnail( $post_id, absint( $attach_id ) );
        }*/
        echo 'Image croped successfully.';
    } else {
        echo $movefile['error'];
    }
    die;
    //print_r(array('image_path' => $image_path, 'image_url' => $image_url));
}
