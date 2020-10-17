<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/*
* Register a meta box using a class.
*/
class dd_custom_meta_box {
 
    /**
     * Constructor.
     */
    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }
 
    /**
     * Meta box initialization.
     */
    public function init_metabox() {
        //add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        //add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
    }
 
    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
            'seller-deal-status',
            __( 'dd Deal Status', 'dokan-lite' ),
            array( $this, 'render_metabox' ),
            'product',
            'side',
            'high'
        );

        /*add_meta_box(
            'seller-deal-info-status',
            __( 'Deal Information', 'dokan-lite' ),
            array( $this, 'seller_deal_render_metabox' ),
            'product',
            'normal',
            'high'
        );*/
 
    }
 
    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'seller_deal_action', 'seller_deal_nonce' );
        $seller_status = seller_deal_status();
        $save_status = get_post_meta($post->ID, '_dd_post_status',true);
        ?>
		<select name="seller_post_status" id="seller_post_status">
			<?php 
			foreach ($seller_status as $key => $status) {
				$selected = '';
				if (!empty($save_status) && $save_status == $key) {
					$selected = 'selected';
				}
			?>
				<option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $status; ?></option>
				<?php 
			} ?>
		</select>
        <?php
    }

    /**
     * Renders the meta box.
     */
    public function seller_deal_render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'seller_deal_action', 'seller_deal_nonce' );

        $seller_status = seller_deal_status();
        $product_options = get_post_meta($post->ID, '_product_options',true);
        
        $finalize_deal_date = get_post_meta($post->ID, '_finalize_deal_date',true);

       // $product_options = nl2br(htmlentities($product_options, ENT_QUOTES, 'UTF-8'));
        $isHandMade = get_post_meta($post->ID, '_isHandMade',true);
        $isPersonalized = get_post_meta($post->ID, '_isPersonalized',true);
        $startDate = get_post_meta($post->ID, '_startDate',true);
        $sale_price = get_post_meta($post->ID, '_sale_price',true);
        $regular_price = get_post_meta($post->ID, '_regular_price',true);
        $shipping_price = get_post_meta($post->ID, '_shipping_price',true);

        $shippingPriceAdditionalItems = get_post_meta($post->ID, '_shippingPriceAdditionalItems',true);
        $requireReturnWhenWrongItemSent = get_post_meta($post->ID, '_requireReturnWhenWrongItemSent',true);
        $requireReturnWhenStyleNotAsExpected = get_post_meta($post->ID, '_requireReturnWhenStyleNotAsExpected',true);
        $requireReturnWhenBadItem = get_post_meta($post->ID, '_requireReturnWhenBadItem',true);
        $requireReturnWhenItemNotAsDescribed = get_post_meta($post->ID, '_requireReturnWhenItemNotAsDescribed',true);

        $shipping_weight_lbs = get_post_meta($post->ID, '_shipping_weight_lbs',true);
        $shipping_weight_oz = get_post_meta($post->ID, '_shipping_weight_oz',true);
        $totalQtyAvailable = get_post_meta($post->ID, '_totalQtyAvailable',true);
        ?>
        <table cellpadding="10" cellspacing="10">
            <tr>
                <th valign="top"><?php esc_html_e( 'Finalize Deal By', 'dokan-lite' ); ?></th>
                <td><input type="text" id="dealby" class="dokan-form-control datepicker" value="<?php echo $finalize_deal_date; ?>" name="finalize_deal_date"></td>
            </tr>
            <tr>
                <th valign="top"><?php esc_html_e( 'Product Options', 'dokan-lite' ); ?></th>
                <td><textarea class="cmb2_textarea" name="product_options" id="product_options"><?php echo $product_options; ?></textarea></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><input type="checkbox" <?php if(!empty($isHandMade)){ echo 'checked'; }?> id="isHandMade" name="isHandMade" value="yes"> <label for="isHandMade" id="lblIsHandMade"> <?php esc_html_e( 'This product is', 'dokan-lite' ); ?> <strong><?php esc_html_e( 'handmade', 'dokan-lite' ); ?></strong>.</label></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><input type="checkbox" id="isPersonalized" <?php if(!empty($isPersonalized)){ echo 'checked'; }?> name="isPersonalized" value="yes"> <label for="isPersonalized" id="lblIspersonalized"> <?php esc_html_e( 'This product is', 'dokan-lite' ); ?> <strong><?php esc_html_e( 'personalized', 'dokan-lite' ); ?></strong>.</label></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Preferred Start Date', 'dokan-lite' ); ?></th>
                <td> <input type="text" id="pdealby" class="dokan-form-control datepicker" value="<?php echo $startDate; ?>" name="startDate"> </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Deal Price', 'dokan-lite' ); ?></th>
                <td> <input type="number" class="dokan-form-control dokan-product-sales-price" name="sale_price" placeholder="" value="<?php echo $sale_price; ?>" min="0" step="any"> </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Retail Price', 'dokan-lite' ); ?></th>
                <td> <input type="number" class="dokan-form-control dokan-product-regular-price" name="regular_price" placeholder="" value="<?php echo $regular_price; ?>" min="0" step="any"> </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Shipping Price', 'dokan-lite' ); ?></th>
                <td> <input type="number" class="dokan-form-control dokan-product-shipping-price" name="shipping_price" placeholder="" id="shipping-price" value="<?php echo $shipping_price; ?>" min="1" step="any"> </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Shipping Additional Price', 'dokan-lite' ); ?></th>
                <td> <input type="number" class="dokan-form-control dokan-product-shipping-price" name="shippingPriceAdditionalItems" placeholder=""  value="<?php echo $shippingPriceAdditionalItems; ?>" min="1" step="any">   </td>
            </tr>
            <tr>
                <th valign="top"><?php esc_html_e( 'Returns', 'dokan-lite' ); ?></th>
                <td>   
                    <p><?php esc_html_e( 'Which items would you like shipped back to you when refunded', 'dokan-lite' ); ?>?</p> 
                    <table>
                        <tr>
                            <td><input type="checkbox" <?php if(!empty($requireReturnWhenWrongItemSent)){ echo 'checked'; }?> id="requireReturnWhenWrongItemSent" name="requireReturnWhenWrongItemSent" value="yes"></td>
                            <th> 
                                <label for="requireReturnWhenWrongItemSent"><?php esc_html_e( '"Wrong" items', 'dokan-lite' ); ?>                            
                                <small class="return-reasons-pay-shipping"><?php esc_html_e( 'You pay shipping', 'dokan-lite' ); ?>.</small>
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <td><input type="checkbox" <?php if(!empty($requireReturnWhenStyleNotAsExpected)){ echo 'checked'; }?> id="requireReturnWhenStyleNotAsExpected" name="requireReturnWhenStyleNotAsExpected" value="yes">  </td>
                            <th> 
                                <label for="requireReturnWhenStyleNotAsExpected"><?php esc_html_e( 'Items with "unexpected style', 'dokan-lite' ); ?>
                                <small class="return-reasons-pay-shipping"><?php esc_html_e( 'Customer pays shipping.', 'dokan-lite' ); ?>.</small>
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <td><input type="checkbox" <?php if(!empty($requireReturnWhenBadItem)){ echo 'checked'; }?> id="requireReturnWhenBadItem" name="requireReturnWhenBadItem" value="yes">   </td>
                            <th> 
                                <label for="requireReturnWhenBadItem"><?php esc_html_e( '"Damaged / defective / not new" items', 'dokan-lite' ); ?> 
                                <small class="return-reasons-pay-shipping"><?php esc_html_e( 'You pay shipping', 'dokan-lite' ); ?>.</small>
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <td><input type="checkbox" <?php if(!empty($requireReturnWhenItemNotAsDescribed)){ echo 'checked'; }?> id="requireReturnWhenItemNotAsDescribed" name="requireReturnWhenItemNotAsDescribed" value="yes"> </td>
                            <th> 
                                <label for="requireReturnWhenBadItem"><?php esc_html_e( '"Damaged / defective / not new" items', 'dokan-lite' ); ?> 
                                <small class="return-reasons-pay-shipping"><?php esc_html_e( 'You pay shipping', 'dokan-lite' ); ?>.</small>
                                </label>
                            </th>
                        </tr>

                    </table>
                </td>
            </tr>
            <tr>
                <th valign="top"><?php esc_html_e( 'Shipping Weight', 'dokan-lite' ); ?></th>
                <td> 
                    <table>
                        <tr>
                            <td><input type="number" class="dokan-form-control dokan-product-shipping-price" name="shipping_weight_lbs" placeholder="0" id="shipping_weight_lbs" value="<?php echo $shipping_weight_lbs; ?>"></td>
                            <th><span class="dokan-input-group-addon">lbs</span></th>
                        </tr>
                         <tr>
                            <td><input type="number" class="dokan-form-control dokan-product-shipping-price" name="shipping_weight_oz" placeholder="0" id="shipping_weight_oz" value="<?php echo $shipping_weight_oz; ?>"></td>
                            <th><span class="dokan-input-group-addon">oz</span></th>
                        </tr>
                    </table>   
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Total Qty Available', 'dokan-lite' ); ?></th>
                <td> 
                    <input type="number" class="dokan-form-control dokan-product-shipping-price" name="totalQtyAvailable" placeholder="" id="total_qty" value="<?php echo $totalQtyAvailable; ?>" min="1" step="any">   
                </td>
            </tr>
        </table>
        <style type="text/css">
            th{text-align: left;}
        </style>
        <script>
        jQuery( function() {
            jQuery( ".datepicker" ).datepicker();
            jQuery(".datepicker").datepicker(  "option", "dateFormat", "yy-mm-dd" );
           
        } );
        </script>
        <?php
    }
 
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['seller_deal_nonce'] ) ? $_POST['seller_deal_nonce'] : '';
        $nonce_action = 'seller_deal_action';
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if (isset($_POST['finalize_deal_date'])) {
            $finalize_deal_date = sanitize_text_field($_POST['finalize_deal_date']);
            update_post_meta( $post_id, '_finalize_deal_date', $finalize_deal_date );
        }

        if (isset($_POST['seller_post_status'])) {
        	$seller_post_status = sanitize_text_field($_POST['seller_post_status']);
        	update_post_meta( $post_id, '_dd_post_status', $seller_post_status );
        }
        if ( isset( $_POST['product_options'] ) ) {
            $product_options = $_POST['product_options']; 
            update_post_meta( $post_id, '_product_options', $product_options );
        }
        if ( isset( $_POST['isHandMade'] ) ) {
            $isHandMade = sanitize_text_field( $_POST['isHandMade'] );
            update_post_meta( $post_id, '_isHandMade', $isHandMade );
        }
        if ( isset( $_POST['isPersonalized'] ) ) {
            $isPersonalized = sanitize_text_field( $_POST['isPersonalized'] );
            update_post_meta( $post_id, '_isPersonalized', $isPersonalized );
        }
        if ( isset( $_POST['startDate'] ) ) {
            $startDate = sanitize_text_field( $_POST['startDate'] );
            update_post_meta( $post_id, '_startDate', $startDate );
        }
        if ( isset( $_POST['sale_price'] ) ) {
            $sale_price = sanitize_text_field( $_POST['sale_price'] );
            update_post_meta( $post_id, '_sale_price', $sale_price );
        }
        if ( isset( $_POST['regular_price'] ) ) {
            $regular_price = sanitize_text_field( $_POST['regular_price'] );
            update_post_meta( $post_id, '_regular_price', $regular_price );
        }
        if ( isset( $_POST['shipping_price'] ) ) {
            $shipping_price = sanitize_text_field( $_POST['shipping_price'] );
            update_post_meta( $post_id, '_shipping_price', $shipping_price );
        }
        if ( isset( $_POST['shippingPriceAdditionalItems'] ) ) {
            $shippingPriceAdditionalItems = sanitize_text_field( $_POST['shippingPriceAdditionalItems'] );
            update_post_meta( $post_id, '_shippingPriceAdditionalItems', $shippingPriceAdditionalItems );
        }
        if ( isset( $_POST['requireReturnWhenWrongItemSent'] ) ) {
            $requireReturnWhenWrongItemSent = sanitize_text_field( $_POST['requireReturnWhenWrongItemSent'] );
            update_post_meta( $post_id, '_requireReturnWhenWrongItemSent', $requireReturnWhenWrongItemSent );
        }
        if ( isset( $_POST['requireReturnWhenStyleNotAsExpected'] ) ) {
            $requireReturnWhenStyleNotAsExpected = sanitize_text_field( $_POST['requireReturnWhenStyleNotAsExpected'] );
            update_post_meta( $post_id, '_requireReturnWhenStyleNotAsExpected', $requireReturnWhenStyleNotAsExpected );
        }
        if ( isset( $_POST['requireReturnWhenBadItem'] ) ) {
            $requireReturnWhenBadItem = sanitize_text_field( $_POST['requireReturnWhenBadItem'] );
            update_post_meta( $post_id, '_requireReturnWhenBadItem', $requireReturnWhenBadItem );
        }
        if ( isset( $_POST['requireReturnWhenItemNotAsDescribed'] ) ) {
            $requireReturnWhenItemNotAsDescribed = sanitize_text_field( $_POST['requireReturnWhenItemNotAsDescribed'] );
            update_post_meta( $post_id, '_requireReturnWhenItemNotAsDescribed', $requireReturnWhenItemNotAsDescribed );
        }
        if ( isset( $_POST['shipping_weight_lbs'] ) ) {
            $shipping_weight_lbs = sanitize_text_field( $_POST['shipping_weight_lbs'] );
            update_post_meta( $post_id, '_shipping_weight_lbs', $shipping_weight_lbs );
        }
        if ( isset( $_POST['shipping_weight_oz'] ) ) {
            $shipping_weight_oz = sanitize_text_field( $_POST['shipping_weight_oz'] );
            update_post_meta( $post_id, '_shipping_weight_oz', $shipping_weight_oz );
        }
        if ( isset( $_POST['totalQtyAvailable'] ) ) {
            $totalQtyAvailable = sanitize_text_field( $_POST['totalQtyAvailable'] );
            update_post_meta( $post_id, '_totalQtyAvailable', $totalQtyAvailable );
        }

    }
} 
new dd_custom_meta_box();


//REMOVE DASHBAORD WIDGETS
function remove_dashboard_widgets() {
    global $wp_meta_boxes;
   /* echo '<pre>';
    print_r($wp_meta_boxes['dashboard']);*/
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['e-dashboard-overview']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['widget_cssheronews']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['yith_dashboard_products_news']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['yith_dashboard_blog_news']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['wpe_dify_news_feed']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_recent_reviews']);

    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); 
} 
add_action('wp_dashboard_setup', 'remove_dashboard_widgets', 999 );


//DASHBOARD ADD NEW WIDGET
add_action( 'wp_dashboard_setup', 'dd_add_dashboard_widget' );
function dd_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'dd_dashboard_widget', 
        'Access Frontend Dashboard', 
        'access_seller_dashboard_widget'
    );
}
function access_seller_dashboard_widget(){   
   ?>
   <style type="text/css">
     ul.frontend-dashboard-list { display: flex; flex-wrap: wrap; align-items: center; margin: 0 -10px; }
     ul.frontend-dashboard-list li { width: 45%; padding: 10px; margin: 0; }
     ul.frontend-dashboard-list li a { background: #ffda67; color: #000; display: flex; text-align: center; height: 70px; flex-wrap: wrap; align-items: center; justify-content: center; padding:10px 0; font-weight: 700; font-size: 21px;}
     ul.frontend-dashboard-list li a img { display: block; margin: 0; }
     ul.frontend-dashboard-list li a span{width:100%; display: block; margin:-5px 0 0;}
     ul.frontend-dashboard-list li a:hover {background: #ffda67a6;}
   </style>
   <div id="woocommerce_dashboard_status">
    <ul class="frontend-dashboard-list">
      <li>
        <a target="_blank" href="<?php echo site_url('dashboard'); ?>">
          <span><?php _e('Dashboard')?></span>
        </a>
      </li>
       <li>
        <a target="_blank" href="<?php echo site_url('dashboard/pending-product'); ?>">
          <span><?php _e('Deals')?></span>
        </a>
      </li>
      <li>
        <a target="_blank" href="<?php echo site_url('dashboard/customer-reviews'); ?>">
          <span><?php _e('Customer Reviews')?></span>
        </a>
      </li>
       <li>
        <a target="_blank" href="<?php echo site_url('dashboard/payments'); ?>">
          <span><?php _e('Payments')?></span> 
        </a>
      </li>
    </ul>
  </div>
   <?php 
}

//REMOVE ALL NOTIFICATION HERE.
add_action('in_admin_header', function () {
  //if (!$is_my_admin_page) return;
  remove_all_actions('admin_notices');
  remove_all_actions('all_admin_notices'); 
}, 1000);

/*add_action( 'welcome_panel', 'my_custom_content', 9999);

function my_custom_content()
{
    ?>
    <h1>Hello!</h1>
    <?php
}*/

//ADMIN ENQUEUE SCRIPT AND CSS HERE
function wpdocs_enqueue_custom_admin_style() {
    if (isset($_GET['page']) && $_GET['page'] == 'ledger') {
        wp_register_style( 'custom_ddadmin', get_stylesheet_directory_uri() . '/fpcustomization/css/ddadmin.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_ddadmin' );
    }
    if (isset($_GET['page']) && $_GET['page'] == 'dd_email_settings') {
        wp_register_style( 'custom_admin_style', get_stylesheet_directory_uri() . '/fpcustomization/css/admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_admin_style' );
        wp_enqueue_script('admin-script', BASEL_THEME_DIR . '/fpcustomization/js/admin-script.js?'.time(), array(), '', '' );
    }
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

//ADD NEW ADMIN LEDGER MENU
add_action( 'dokan_admin_menu', 'load_admin_pages', 10, 2 );
function load_admin_pages($capability, $menu_position){
    if ( current_user_can( $capability ) ) {
        add_submenu_page( 'dokan', __( 'Ledger', 'dokan' ), __( 'Ledger', 'dokan' ), 'manage_options', 'ledger', 'admin_ledger');
    }
}
function admin_ledger(){

    global $wpdb, $wp_query;
    $userid = get_current_user_id();
    $ledger_entry = $wpdb->prefix."ledger_entry";

    $pagenum       = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
    $posts_per_page = 20;
    $start_index = ($pagenum - 1) * $posts_per_page;

    $search = '';
    if (isset($_GET['TypeFilter']) && !empty($_GET['TypeFilter'])){
        $search .= " AND entry_type='".trim($_GET['TypeFilter'])."'";
    }
    if (isset($_GET['seller']) && !empty($_GET['seller'])){
        $search .= " AND seller_id=".trim($_GET['seller']);
    }

    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$ledger_entry} WHERE 1=1 {$search}
     ORDER BY ID DESC LIMIT $start_index, $posts_per_page") );
   

    $total_results = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$ledger_entry} WHERE 1=1 {$search} ORDER BY ID DESC") );
    $total_pages = $total_results/$posts_per_page;
    $total_pages = round($total_pages);
    ?>
    <div class="wrap">
        <h1><?php _e( 'Ledger', 'dokan' ); ?></h3>
            <?php if(isset($_SESSION['ledger_admin']) && !empty($_SESSION['ledger_admin'])){ ?>
                <div class="notice notice-success is-dismissible"> 
                    <p><strong>Record saved successfully.</strong></p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            <?php unset($_SESSION['ledger_admin']); } ?>
            <form action="" id="frmTypeFilter" method="get">
                <input type="hidden" name="page" value="ledger">
                <select name="TypeFilter" id="TypeFilter" placeholder="View All" class="ledger-filter">
                    <option value=""><?php _e( 'View All', 'dokan-lite' ); ?></option>
                    <?php 
                    $types = GetLedgerTypes(); 
                    foreach ($types as $key => $type) {
                        $selected = isset($_GET['TypeFilter']) && $_GET['TypeFilter'] == $key?'selected':'';
                        echo '<option '. $selected.' value="'.$key.'">'.$type.'</option>';
                    }
                    ?>
                </select>
                <select name="seller" id="seller" placeholder="View All" class="ledger-filter">
                    <option value=""><?php _e( 'Seller', 'dokan-lite' ); ?></option>
                    <?php 
                    $users = get_users( 'orderby=nicename&role=seller' ); 
                    foreach ($users as $user) {
                        $selected = (isset($_GET['seller']) && $_GET['seller'] == $user->ID)?'selected':'';
                        echo '<option '. $selected.' value="'.$user->ID.'">'.$user->display_name.'</option>';
                    }
                    ?>
                </select>
                <button type="button" id="clearfilter" class="button"><?php _e( 'Clear', 'dokan-lite' ); ?></button>
            </form>
            <?php if(!empty($results)){ ?>
                <div class="ladger_tables"> 
                    <div class="loader" style="display: none;"></div>                   
                    <table class="widefat wp-list-table widefat fixed striped ladgermain_tabel">
                        <thead>
                            <tr>
                                <th><?php _e( 'DATE', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'DEAL', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'SELLER', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'TYPE', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'AMOUNT', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'ACTION', 'dokan-lite' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($results as $row){
                                $seller = get_user_by('id', $row->seller_id);                             
                                ?>
                                <tr class="<?php echo $row->entry_type; ?>" data-id="<?php echo $row->ID; ?>" data-date="<?php echo date('M d, Y',strtotime($row->created_date)) ; ?>">
                                    <td><?php echo date('M d, Y',strtotime($row->created_date)) ; ?></td>
                                    <td><?php echo $row->deal_id.' - '.$row->deal_titel; ?></td>
                                    <td><?php echo $seller->display_name; ?></td>
                                    <td><?php echo GetLedgerTypes($row->entry_type); ?></td>
                                    <td><?php echo wc_price($row->seller_commission); ?></td>
                                    <td>
                                        <?php 
                                        if($row->status == 'paid'){
                                           echo 'Paid';
                                        }else{
                                            ?>
                                             <button type="button" data-id="<?php echo $row->ID; ?>" class="button pay-seller"><?php _e( 'Pay', 'dokan-lite' ); ?></button>
                                            <?php
                                        } 
                                        ?>
                                       
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><?php _e( 'DATE', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'DEAL', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'SELLER', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'TYPE', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'AMOUNT', 'dokan-lite' ); ?></th>
                                <th><?php _e( 'ACTION', 'dokan-lite' ); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php 
                if ($total_pages > 1 ) {

                    echo '<div class="pagination-wrap">';

                    $big = 999999999; // need an unlikely integer
                    echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => $pagenum,
                    'total' => $total_pages
                    ) );
                }
                ?>
            <?php }else{ ?>
                <p><?php _e( 'No record found', 'dokan-lite' ); ?>.</p>
            <?php } ?>
        </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){            
            jQuery('.ledger-filter').change(function(){
                jQuery('#frmTypeFilter').submit();
            });
            jQuery('#clearfilter').click(function(){
                jQuery('#frmTypeFilter').find('select').prop('selectedIndex',-1);
                jQuery('#frmTypeFilter').submit();
            });
            jQuery('.pay-seller').click(function(){
                var dealid = jQuery(this).data('id');
               jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'get_deal_commission', lid: dealid, nonce: '<?php echo wp_create_nonce('pay_deal'); ?>'},function(data){
                    jQuery('#frm-pay-seller').html(data);
                    jQuery('#thickbox-pay-seller').click();
               });
            });
        });
    </script>
    <?php add_thickbox(); ?>
    <div id="pay-seller-popup" style="display:none;">
       <form method="post"  action="<?php echo admin_url('admin-ajax.php'); ?>">
            <div id="frm-pay-seller">
                
            </div>
            <input type="hidden" name="action" value="pay_deal_commission">
            <?php wp_nonce_field( 'admin_deal_action', 'pay_deal_nonce' ); ?>
       </form>
    </div>
    <!-- <div class="table-loading">
    <div class="table-loader-wrap"><div class="table-loader-center"><div class="table-loader">Loading</div></div></div></div> -->

    <a href="#TB_inline?&width=600&height=450&inlineId=pay-seller-popup" id="thickbox-pay-seller" style="display: none;" class="thickbox">View my inline content!</a>  
    <?php
}

//GET DEAL COMMISSION HERE.
add_action('wp_ajax_get_deal_commission', 'GetDealCommission');
function GetDealCommission(){
    check_ajax_referer( 'pay_deal', 'nonce' );
    global $wpdb;
    $lid = sanitize_text_field($_POST['lid']);
    $data = '';
    if (!empty($lid))
    {
        $ledger_entry = $wpdb->prefix."ledger_entry";
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ledger_entry} WHERE ID = %d", $lid));
        $sold = dd_get_deal_orders($row->deal_id);
        $ldate = date('M d, Y', strtotime($row->created_date));

        $ctype = '';
        if ('percentage' == $row->commission_type ) {
            $ctype = '%';
        }

        $data = '
        <input type="hidden" name="ledger_id" value="'.$lid.'"> 
        <div class="lader_hading">  
            <h2>Commission Details</h2>
            <h4 class="commission-date">Commission Date: '.$ldate.'</h4>             
        </div>
        <div class="lader_name">
        
        </div>
        <table class="table table-rasponsive">
        <tbody>
        <tr>
        <td><a target="_blank" href="'.get_permalink($row->deal_id).'">'.$row->deal_titel.'</a></td>
        <td></td>
        </tr>
        <tr>
        <td>Items Sold</td>
        <td>'.$sold.'</td>
        </tr>
        <tr>
        <td>Product Revenue</td>
        <td>'.wc_price($row->amount).'</td>
        </tr>
        <tr>
        <td>Shipping Revenue</td>
        <td>'.wc_price($row->shipping_amount).'</td>
        </tr>
        <tr>
        <td>Daffodeals Commission ('.$row->admin_percentage.$ctype.')</td>
        <td><span class="red">- '.wc_price($row->admin_commission).'</span></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
        <td>Total Net Revenue</td>
        <td><span class="green">'.wc_price($row->seller_commission).'</span></td>
        </tr>
        <tr>
        <td></td>
        <td><button type="submit" class="button">Pay</button></td>
        </tr>
        </tfoot>
        </table>';

        echo $data;
    }
    die;
}

//PAY DEAL COMMISSION TO SELLER
add_action('wp_ajax_pay_deal_commission', 'PayDealCommission');
function PayDealCommission(){
    if (isset($_POST['pay_deal_nonce']) && wp_verify_nonce($_POST['pay_deal_nonce'], 'admin_deal_action' )){
        global $wpdb;
        
        $ledger_id = sanitize_text_field($_POST['ledger_id']);

        if (!empty($ledger_id)) {
            
            $ledger_entry = $wpdb->prefix."ledger_entry";
            
            $hasentry = $wpdb->get_row( $wpdb->prepare( "SELECT *
            FROM {$ledger_entry}
            WHERE
            ID = %d 
            ", $ledger_id
            ) );

            $where = array('ID' => $ledger_id);
            $where_format = array('%d');
            $wpdb->update($ledger_entry,array('status' => 'paid'),$where,array('%s'),$where_format);

            $data = array();
            $format = array();
            $user_id = get_current_user_id();
            $data['seller_id'] = $hasentry->seller_id;
            $format = '%d';
            $data['admin_id'] = $user_id;
            $format = '%d';
            $data['deal_id'] = $hasentry->deal_id;
            $format = '%d';
            $data['deal_titel'] = 'Payout';
            $format = '%s';
            $data['entry_type'] = 'PaidSeller';
            $format = '%s';
            $data['amount'] = $hasentry->amount;
            $format = '%f';
            $data['shipping_amount'] = $hasentry->shipping_amount;
            $format = '%f';
            $data['seller_commission'] = $hasentry->seller_commission;
            $format = '%f';
            $data['admin_commission'] = $hasentry->admin_commission;
            $format = '%f';
            $data['admin_percentage'] = $hasentry->admin_percentage;
            $format = '%f';
            $data['commission_type'] = $hasentry->commission_type;
            $format = '%s';
            $data['status'] = 'paid';
            $format = '%s';
            $data['revenue_date'] = $hasentry->created_date;
            $format = '%s';
            $data['created_date'] = date('Y-m-d H:i:s');
            $format = '%s';
            $wpdb->insert($ledger_entry,$data,$format);
            $_SESSION['ledger_admin'] = 1;

            pay_a_deal_to_vendor_notification($hasentry->seller_id, $hasentry->seller_commission, $hasentry->deal_id);
        }
    } 
    wp_safe_redirect( wp_get_referer() );
    die;
}

//PAY DEAL FOR VENDOR
function pay_a_deal_to_vendor_notification($seller_id,$seller_commission,$deal_id){    

    $post_data = get_post( $deal_id );
    $deal_title = $post_data->post_title;
    $author_id = $post_data->post_author;

    $user   = get_userdata($author_id);
    $seller_email = $user->user_email; 
    $vendor_name = $user->first_name.'&nbsp;'.$user->last_name;
    $site_name = get_option('blogname'); 

    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['paid_deal_sub']; 
    $email_heading  = $wstheme_options['paid_deal_heading']; 
    $user_message = $wstheme_options['paid_deal_temp'];

    $data = array('deal_title' => $deal_title,'vendor_name' => $vendor_name,'total_commission' => wc_price($seller_commission));
    $search_key_array = array('[deal_title]' => 'deal_title','[vendor_name]' => 'vendor_name','[total_commission]' => 'total_commission');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }

    $user_message  = wpautop($user_message);     
    dd_send_email_handler('nanchhu@internetbusinesssolutionsindia.com', $user_subject, $user_message, $email_heading);
    //dd_send_email_handler($seller_email, $user_subject, $user_message, $email_heading);
}

//ADD NEW USER COLUMN
add_filter('manage_users_columns', 'dd_add_user_columns');
function dd_add_user_columns($columns){
    unset($columns['posts']);
    $columns['dd_approve'] = 'Approve';
    $columns['dd_contract'] = 'Contract';
    return $columns;
} 
add_action('manage_users_custom_column','dd_columns_data', 10, 3);
function dd_columns_data($value, $column_name, $user_id){
    if ( 'dd_approve' == $column_name ){
        $vendor_approved = get_user_meta($user_id, '_vendor_approved', true);
        $checked = '';
        if (!empty($vendor_approved)) {
            $checked = 'checked="checked"';
        }
        $user = get_userdata( $user_id );
        $user_roles = $user->roles;
        if ( in_array( 'seller', $user_roles, true ) || in_array( 'administrator', $user_roles, true )) {
            return '<input type="checkbox" '.$checked.' class="vendor_approve" value="'.$user_id.'"><img class="loadingprocess" style="display:none" src="'.get_stylesheet_directory_uri().'/fpcustomization/images/processing.gif">';
        }else{
            return '';
        }
    }
    if ( 'dd_contract' == $column_name ){
        $vendor_approved = get_user_meta($user_id, '_seller_term_conditions', true);
        $checked = '';
        if (!empty($vendor_approved)) {
            $checked = 'checked="checked"';
        }
        $user = get_userdata( $user_id );
        $user_roles = $user->roles;
        if ( in_array( 'seller', $user_roles, true ) || in_array( 'administrator', $user_roles, true )) {
            return '<input type="checkbox" '.$checked.' disabled="disabled  value="'.$user_id.'">';
        }else{
            return '';
        }
    }
}
//SEND APPROVE EMAIL TO SELLER
add_action('wp_ajax_dd_vendor_approve', 'dd_vendor_approve_call');
function dd_vendor_approve_call(){
    check_ajax_referer( 'vendor_approved', 'security' );
    $uid = sanitize_text_field( $_POST['uid'] );
    $approved = sanitize_text_field( $_POST['approved'] );

    if (!empty($uid) && !empty($approved)){
        $user = get_userdata( $uid );       
        $vendor_password = get_user_meta($uid, '_vendor_password', true);
        $password = !empty($vendor_password)?$vendor_password:'';
        $base_url = site_url('dashboard/settings/profile/');
        $verifications_key = get_user_meta($user->ID, '_dokan_email_verification_key', true );

        $wstheme_options = get_wstheme_options();

        if(!empty($verifications_key)){            
            if ( in_array( 'seller', $user->roles ) && dokan_get_option( 'disable_welcome_wizard', 'dokan_selling' ) == 'off' ) {
                $verification_link = add_query_arg( array( 'dokan_email_verification' => $verifications_key, 'id' => $user->ID, 'page' => 'dokan-seller-setup' ), $base_url );
            } else {
                $verification_link = add_query_arg( array( 'dokan_email_verification' => $verifications_key, 'id' => $user->ID ), $base_url);
            }  
        }else{
            $verification_link = '#';
        } 

        $email_subject  = $wstheme_options['vendor_approved_sub']; 
        $email_head  = $wstheme_options['vendor_approved_heading']; 
        $email_message = $wstheme_options['vendor_approved_temp'];
        $blogname = get_option('blogname');
        
        $data = array('vendor_name'=> ucfirst($user->first_name), 'vendor_username' => $user->user_login, 'vendor_password' => $password, 'verification_link' => $verification_link, 'site_name' => $blogname );
        $search_key_array = array('[vendor_name]' => 'vendor_name','[vendor_username]' => 'vendor_username','[vendor_password]' => 'vendor_password','[verification_link]' => 'verification_link','[site_name]' => 'site_name');
        foreach ($search_key_array as $key => $value) {
          $key_value = @$data[$value];  
          $email_subject = str_replace($key,$key_value,$email_subject);
          $email_head = str_replace($key,$key_value,$email_head); 
          $email_message = str_replace($key,$key_value,$email_message); 
        }
        $email_message  = wpautop($email_message); 

        dd_send_email_handler($user->user_email, $email_subject, $email_message, $email_head);  
        update_user_meta($uid, '_vendor_approved', 1);
        update_user_meta($uid, 'dokan_enable_selling', 'yes');
        delete_user_meta($uid, '_vendor_password');
        //wp_set_password( $password, $uid );
    }else{
        update_user_meta($uid, 'dokan_enable_selling', 'no');
        update_user_meta($uid, '_vendor_approved', 0);
    }
    wp_die();
}
add_action('admin_footer', 'dd_admin_footer');
function dd_admin_footer(){
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.users .vendor_approve').click(function(){
                var self = jQuery(this);
                var uid = jQuery(this).val();
                if (uid != ''){
                    jQuery(self).next('.loadingprocess').show();
                    var approved = 0;
                    if (self.is(':checked')){
                        approved = 1;
                    }
                    jQuery.post(dokan_admin.ajaxurl,{action:'dd_vendor_approve', uid: uid, approved:approved, security: '<?php echo wp_create_nonce( "vendor_approved" ); ?>'}, function(data){
                        jQuery(self).next('.loadingprocess').hide();
                    });
                }
            });
        });
    </script>
    <style type="text/css">
        body.users-php table.users td{ position: relative; }
        body.users-php table.users td img.loadingprocess{ width: 30px; height: 30px; position: absolute; z-index: 999; left: 1px;top: 5px;}
    </style>
    <?php 
}
//SELLER INFORMATION SHOWING HERE
add_action( 'edit_user_profile', 'show_extra_profile_fields' );
function show_extra_profile_fields( $user ) { 
    
    $postdata = get_user_meta($user->ID, 'dokan_profile_settings',true);    
    $seller_term_conditions = get_user_meta($user->ID, '_seller_term_conditions',true);    
    $product_cats = array('Accessories','Apparel','Beauty','Craft/DIY','Footwear','Furniture','Home Goods','Jewelry','Kids/Baby','Tech/Gadgets','Other');
    $TimeInBusiness = array('Brand new/Haven\'t opened yet','Less than a year','1-3 years','3-5 years','Over 5 years');
    $AnnualRevenueLastYear = array('$0-$10,000','$10,000-$50,000','$50,000-$100,000','$100,000-$500,000','$500,000-$1,000,000','$1,000,000+');
    $IntendedDiscountRate = array('10-25%','25-50%','More than 50%');
    $free_shipping = array('Yes','No');
    $businessType = array('Individual/Sole Proprietor', 'Partnership', 'C Corporation', 'S Corporation', 'Limited Liability Company');
    if ( in_array( 'seller', $user->roles )) {
    ?>
    <br>
    <h3>Seller Information</h3> 
    <table class="form-table">
        <tr>
            <th><label for="business-formation"><?php esc_html_e( 'Business Formation', 'dokan-lite' ); ?></label></th> 
            <td>
                <select id="business-formation" name="businessType"  placeholder="Please select one..." class="regular-text">
                <option value="">Please select one...</option>
                <?php foreach ($businessType as $key => $type) { 
                $selected = '';
                if (isset($postdata['BusinessFormation']) && $postdata['BusinessFormation'] == $type) {
                    $selected = 'selected';
                }
                ?>
                <option <?php echo $selected; ?> value="<?php echo $type; ?>"><?php echo $type; ?></option>
                <?php } ?>
                </select>   
            </td>
        </tr>
        <tr>
            <th><label for="apartments"><?php esc_html_e( 'Employer Identification Number-EIN', 'dokan-lite' ); ?></label></th> 
            <td>
                <input type="text" class="input-text regular-text" name="employerIdentificationNumber" id="employerIdentificationNumber" value="<?php if ( ! empty( $postdata['employerIdentificationNumber'] ) ) echo esc_attr($postdata['employerIdentificationNumber']); ?>" required="required" />
            </td>
        </tr>
        <tr>
            <th><label for="apartments"><?php esc_html_e( 'Website URL', 'dokan-lite' ); ?></label></th> 
            <td>
                <input type="text" class="input-text regular-text" name="website_url" id="website" value="<?php if ( ! empty( $postdata['owner_website'] ) ) echo esc_attr($postdata['owner_website']); ?>" required="required" />
            </td>
        </tr>
        <tr>
            <th><label for="documents"><?php esc_html_e( 'Product Category', 'dokan-lite' ); ?></label></th> 
            <td>
                <select id="ProductCateogry" name="ProductCateogry" required="required" placeholder="Please select one..." class="regular-text">
                <option value="">Please select one...</option>
                <?php foreach ($product_cats as $key => $cat) {
                $selected = '';
                if (isset($postdata['ProductCateogry']) && $postdata['ProductCateogry'] == $cat) {
                    $selected = 'selected';
                }
                ?>
                <option <?php echo $selected; ?> value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                <?php 
                } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="TimeInBusiness"><?php esc_html_e( 'Time in Business', 'dokan-lite' ); ?></label></th> 
            <td>
                <select id="TimeInBusiness" name="TimeInBusiness" required="required" placeholder="Please select one..." class="regular-text">
                    <option value="">Please select one...</option>
                    <?php foreach ($TimeInBusiness as $key => $business) {
                        $selected = '';
                        if (isset($postdata['TimeInBusiness']) && $postdata['TimeInBusiness'] == $business) {
                            $selected = 'selected';
                        }
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $business; ?>"><?php echo $business; ?></option>
                        <?php 
                    } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="AnnualRevenueLastYear"><?php esc_html_e( 'Annual Revenue Last Year', 'dokan-lite' ); ?></label></th> 
            <td>
                <select id="AnnualRevenueLastYear" name="AnnualRevenueLastYear" required="required" placeholder="Please select one..." class="regular-text">
                    <option value="">Please select one...</option>
                    <?php foreach ($AnnualRevenueLastYear as $key => $revenue) {
                        $selected = '';
                        if (isset($postdata['AnnualRevenueLastYear']) && $postdata['AnnualRevenueLastYear'] == $revenue) {
                            $selected = 'selected';
                        }
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $revenue; ?>"><?php echo $revenue; ?></option>
                        <?php 
                    } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="IntendedDiscountRate"><?php esc_html_e( 'Intended Discount Rate', 'dokan-lite' ); ?></label></th> 
            <td>
                <select id="IntendedDiscountRate" name="IntendedDiscountRate" required="required" placeholder="Please select one..." class="regular-text">
                    <option value="">Please select one...</option>
                    <?php 
                    foreach ($IntendedDiscountRate as $key => $discount) {
                        $selected = '';
                        if (isset($postdata['IntendedDiscountRate']) && $postdata['IntendedDiscountRate'] == $discount) {
                            $selected = 'selected';
                        }
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $discount; ?>"><?php echo $discount; ?></option>
                        <?php 
                    } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="OfferFreeShipping"><?php esc_html_e( 'Are you willing to offer free shipping?', 'dokan-lite' ); ?></label></th> 
            <td>
                <select id="OfferFreeShipping" name="OfferFreeShipping" required="required" placeholder="Please select one..." class="regular-text">
                    <option value="">Please select one...</option>
                    <?php foreach ($free_shipping as $key => $shippng) {
                        $selected = '';
                        if (isset($postdata['OfferFreeShipping']) && $postdata['OfferFreeShipping'] == $shippng) {
                            $selected = 'selected';
                        }
                        ?>
                        <option <?php echo $selected ; ?> value="<?php echo $shippng; ?>"><?php echo $shippng; ?></option>
                        <?php 
                    } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="ProductDescription"><?php esc_html_e( 'Tell us about your products', 'dokan-lite' ); ?></label></th> 
            <td>
                <textarea required="required" id="ProductDescriptions" placeholder="Tell us about your products..." name="ProductDescription" class="regular-text"><?php if ( ! empty( $postdata['ProductDescription'] ) ) echo esc_attr($postdata['ProductDescription']); ?></textarea>
            </td>
        </tr>
        <tr>
            <th><label for="seller-product-preview-imgs"><?php esc_html_e( 'Sample Images of Products', 'dokan-lite' ); ?></label></th> 
            <td>
                <ul id="seller-product-preview-imgs">
                    <?php 
                    if ( ! empty( $postdata['SampleProductImages'] ) ){ 
                        foreach ($postdata['SampleProductImages'] as $SampleProductImages) {
                            echo '<li style="display: inline-block; margin-right: 20px;"><img width="100"  height="100" src="'.$SampleProductImages.'"></li>';
                        }                        
                    } ?>
                    
                </ul>
            </td>
        </tr>
        <tr>
            <th><label for="seller-product-preview-imgs"><?php esc_html_e( 'Contract Terms and Conditions', 'dokan-lite' ); ?></label></th> 
            <td>
                <ul id="seller-product-preview-imgs">
                    <input type="checkbox" name="terms_conditions" <?php if (!empty($seller_term_conditions)){ echo 'checked="checked"'; } ?>>
                </ul>
            </td>
        </tr>

    </table>
    <?php
    }
}
//ADD EMAIL SETTINGS MENU HERE.
add_action('admin_menu','dd_theme_menu_pages');
function dd_theme_menu_pages() {
    add_menu_page( 
        __( 'Email Settings', 'dokan-lite' ),
        'Email Settings',
        'manage_options',
        'dd_email_settings',
        'dd_email_settings_callback',
        'dashicons-admin-generic',
        30
    );  
}
function dd_email_settings_callback(){
    echo "<div id='wstheme-options'>"; 
        ddtheme_options_header(); 
        echo "<div id='right-option-panel'>"; 
            require_once(get_stylesheet_directory().'/fpcustomization/functions/email-setting.php'); 
        echo "</div>"; 
        dd_options_footer(); 

    echo "</div>";
}

/*
* Theme Option Header Function
*/
function ddtheme_options_header(){

$site_title = get_bloginfo( 'name' );
wp_enqueue_media();
?>
<form action="#" method="post">
<input type="hidden" name="option_action_changes" value="option_action_changes">
 <div class="header-setting-panel"> 
    <h2><?php _e( 'Email Settings','wstheme'); ?></h2>
    <button type="submit" class="option_save_changes" name="option_save_changes"><?php _e( 'Save Changes','wstheme'); ?></button> 
</div>
<?php
}

/*
* Theme Option Footer Function
*/
function dd_options_footer(){
 $site_title = get_bloginfo('name');
?>
    <div class="footer-setting-panel">
    <h2><?php _e( 'Email Settings','wstheme'); ?></h2>
    <button type="submit" class="option_save_changes" name="option_save_changes"><?php _e( 'Save Changes','wstheme'); ?></button>
    </div>
</form>
<script type='text/javascript'>
    jQuery(document).ready( function( $ ) { 
        // Uploading files 
        var file_frame; 
        var wp_media_post_id = wp.media.model.settings.post.id;
        // Store the old id 
        var current_upload_btn =''; 
        var set_to_post_id = ''; // Set this  
        jQuery('.option_upload_media_button').on('click', function( event ){
            //file_frame.open(); 
            current_upload_btn = jQuery(this).closest(".option-image-uploader"); 
            event.preventDefault(); 
            if ( file_frame ) { 
                file_frame.uploader.uploader.param( 'post_id', set_to_post_id ); 
                file_frame.open(); 
                return; 
            } else {  
                wp.media.model.settings.post.id = set_to_post_id; 
            } 
            file_frame = wp.media.frames.file_frame = wp.media({ 
                title: 'Select a image to upload', 
                button: { 
                    text: 'Use this image', 
                }, 
                multiple: false  
            }); 
            file_frame.on( 'select', function(nameinputa) { 
                attachment = file_frame.state().get('selection').first().toJSON(); 
                current_upload_btn.find('input[type="url"]').val(attachment.url); 
                current_upload_btn.find(".image-preview").attr( 'src', attachment.url); 
                wp.media.model.settings.post.id = wp_media_post_id; 
            }); 
            file_frame.open();
        }); 
        jQuery( 'a.add_media' ).on( 'click', function() {
            wp.media.model.settings.post.id = wp_media_post_id; 
        });
    }); 
</script> 
<?php 
} 
/* 
* Save Theme Options Values 
*/ 
function dd_save_wstheme_options() { 
    if (current_user_can( 'edit_theme_options' ) && (! wp_doing_ajax()) && isset($_POST['wstheme_options']) && !empty($_POST['wstheme_options'])) {   
        $wstheme_options_key = dd_options_key();   
        $old_data = get_option($wstheme_options_key);
        $new_data = $_POST['wstheme_options'];   
        if(is_array($old_data)){ 
            $new_data = array_merge($old_data, $new_data);  
        }elseif(!isset($old_data) || !is_array($old_data)){  
            $default_theme_option = get_option('wstheme_options');  
            if(is_array($default_theme_option)){   
                $new_data = array_merge($default_theme_option, $new_data); 
            }  
        }  
        if(isset($_POST['optin_location']) && $_POST['optin_location']=='home-setting' && !isset($_POST['wstheme_options']['home_slider'])){   
            $new_data['home_slider'] ='';  
        }   
        update_option($wstheme_options_key,$new_data);  
    } 
}
add_action( 'admin_init', 'dd_save_wstheme_options', 1 ); 

/* 
* Get Theme Option Key
*/ 
function dd_options_key(){ 
    return $wstheme_options_key = 'wstheme_options'; 
} 
/* 
* Get WS theme Option Value 
*/
function get_wstheme_options(){
    $wstheme_options_key = dd_options_key(); 
    $wstheme_options = get_option($wstheme_options_key); 
    if(!isset($wstheme_options) || !is_array($wstheme_options)){ 
            $wstheme_options = get_option('wstheme_options'); 

    } 
    $wstheme_options_data = array(); 
    if(!empty($wstheme_options)){ 
        foreach ($wstheme_options as $key => $value) { 
            if(is_array($value)){ 
                $wstheme_options_data[$key]=$value; 
            }elseif(!empty($value)){ 
                $wstheme_options_data[$key] = stripcslashes($value); 

            } 
        } 
    } 
    return $wstheme_options_data; 
} 


//ADD NEW FIELD IN SELLER REGISTRATION EMAIL
add_filter('woocommerce_settings_api_form_fields_dokan_new_seller', 'wc_settings_api_form_fields_dokan_new_seller');
function wc_settings_api_form_fields_dokan_new_seller($form_fields){  
  $form_fields['email_content'] = array('title' => 'Email Content', 'type' => 'textarea', 'description' => 'Available placeholders: {site_name}, {store_name}, {seller_name}, {seller_edit}', 'placeholder' => 'Enter content here');
  return $form_fields;
}
//ADD NEW FIELD IN ORDER PROCESSING EMAIL
add_filter('woocommerce_settings_api_form_fields_customer_processing_order', 'wc_settings_api_form_fields_customer_processing_order');
function wc_settings_api_form_fields_customer_processing_order($form_fields){  
  $form_fields['email_content'] = array('title' => 'Email Content', 'type' => 'textarea', 'description' => 'Available placeholders: {site_name}, {customer_name}, {order_number}', 'placeholder' => 'Enter content here');
  return $form_fields;
}
//ADD NEW FIELD IN ORDER COMPLETED EMAIL
add_filter('woocommerce_settings_api_form_fields_customer_completed_order', 'wc_settings_api_form_fields_customer_completed_order');
function wc_settings_api_form_fields_customer_completed_order($form_fields){  
  $form_fields['email_content'] = array('title' => 'Email Content', 'type' => 'textarea', 'description' => 'Available placeholders: {site_name}, {customer_name}, {order_number}', 'placeholder' => 'Enter content here');
  return $form_fields;
}
//ADD NEW FIELD IN NEW ACCOUNT EMAIL
add_filter('woocommerce_settings_api_form_fields_customer_new_account', 'wc_settings_api_form_fields_customer_customer_new_account');
function wc_settings_api_form_fields_customer_customer_new_account($form_fields){  
  $form_fields['seller_email_content'] = array('title' => 'Seller Email Content', 'type' => 'textarea', 'description' => 'Available placeholders: {site_name}, {vendor_name}', 'placeholder' => 'Enter content here');
  $form_fields['customer_email_content'] = array('title' => 'Customer Email Content', 'type' => 'textarea', 'description' => 'Available placeholders: {site_name}, {customer_name}, {my_account_url}', 'placeholder' => 'Enter content here');
  return $form_fields;
}


add_action('woocommerce_order_status_changed', 'send_custom_email_notifications', 10, 4 );
function send_custom_email_notifications( $order_id, $old_status, $new_status, $order ){
    if ( $new_status == 'cancelled' || $new_status == 'failed' ){
        $wc_emails = WC()->mailer()->get_emails(); // Get all WC_emails objects instances
        $customer_email = $order->get_billing_email(); // The customer email
    }

    if ( $new_status == 'cancelled' ) {
       order_cancelled_to_customer($order_id, $order);
       order_cancelled_to_vendor($order_id, $order);
    } 
    elseif ( $new_status == 'failed' ) {
       order_cancelled_to_customer($order_id, $order);
       order_cancelled_to_vendor($order_id, $order);
    } 
}

//ORDER CANCELLED EMAIL TO CUSTOMER
function order_cancelled_to_customer($order_id, $order){

    $customer_email = $order->get_billing_email();
    $full_name = $order->get_formatted_billing_full_name();
    
    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['customer_cancelled_sub']; 
    $email_heading  = $wstheme_options['customer_cancelled_heading']; 
    $user_message = $wstheme_options['customer_cancelled_temp'];
    $blogname = get_option('blogname');

    $data = array('customer_name'=> $full_name,'order_number' => $order_id,'site_name' => $blogname);
    $search_key_array = array('[customer_name]' =>'customer_name','[order_number]' => 'order_number','[site_name]' => 'site_name');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message);     

    ob_start();

    echo $user_message;

    do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

    /*
    * @hooked WC_Emails::order_meta() Shows order meta data.
    */
    do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

    /*
    * @hooked WC_Emails::customer_details() Shows customer details
    * @hooked WC_Emails::email_address() Shows email address
    */
    do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

    $message = ob_get_clean();

    dd_send_email_handler($customer_email, $user_subject, $message, $email_heading);
}

//ORDER CANCELLED EMAIL TO VENDOR
function order_cancelled_to_vendor($order_id, $order){
    global $wpdb;

    $seller_id = $wpdb->get_var("SELECT seller_id FROM {$wpdb->prefix}dokan_orders WHERE order_id={$order_id}");
    $user_info = get_userdata($seller_id);

    $customer_email = $user_info->user_email;
    $full_name = $user_info->first_name;
    
    $wstheme_options = get_wstheme_options();
    $user_subject  = $wstheme_options['vendor_cancelled_sub']; 
    $email_heading  = $wstheme_options['vendor_cancelled_heading']; 
    $user_message = $wstheme_options['vendor_cancelled_temp'];
    $blogname = get_option('blogname');

    $data = array('vendor_name'=> $full_name,'order_number' => $order_id,'site_name' => $blogname);
    $search_key_array = array('[vendor_name]' =>'vendor_name','[order_number]' => 'order_number','[site_name]' => 'site_name');

    foreach ($search_key_array as $key => $value) {
        $key_value = @$data[$value];  
        $user_subject = str_replace($key,$key_value,$user_subject);
        $email_heading = str_replace($key,$key_value,$email_heading); 
        $user_message = str_replace($key,$key_value,$user_message); 
    }
    $user_message  = wpautop($user_message);     

    ob_start();

    echo $user_message;

    do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

    /*
    * @hooked WC_Emails::order_meta() Shows order meta data.
    */
    do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

    /*
    * @hooked WC_Emails::customer_details() Shows customer details
    * @hooked WC_Emails::email_address() Shows email address
    */
    do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

    $message = ob_get_clean();

    dd_send_email_handler($customer_email, $user_subject, $message, $email_heading);
    //dd_send_email_handler('nanchhu@internetbusinesssolutionsindia.com', $user_subject, $message, $email_heading);
}