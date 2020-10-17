<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

function divi_child_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'frontend-style', site_url() . '/wp-content/themes/divi-child/fpcustomization/css/frontend.css?var='.time() );  

    wp_enqueue_style( 'slick-theme-style', site_url() . '/wp-content/themes/divi-child/fpcustomization/css/slick-theme.css?var='.time() );
    wp_enqueue_style( 'slick-style', site_url() . '/wp-content/themes/divi-child/fpcustomization/css/slick.css?var='.time() );  
    wp_enqueue_script( 'slick-js', site_url() . '/wp-content/themes/divi-child/fpcustomization/js/slick.min.js','','',true); 
	
	// DataTable
	wp_enqueue_style( 'datatable-style', '//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css' ); 	
	wp_enqueue_script( 'datatable-script', '//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', '','', true );
}
add_action( 'wp_enqueue_scripts', 'divi_child_enqueue_styles' );     


add_theme_support( 'woocommerce' );
add_theme_support( 'wc-product-gallery-zoom' );

define( 'BASEL_THEME_DIR', 		get_stylesheet_directory_uri() );
define( 'BASEL_THEMEROOT', 		get_stylesheet_directory() );

/*----------FP ADD NEW FILE HERE.--------------*/
//echo BASEL_THEMEROOT.'/fpcustomization/index.php';
require_once(BASEL_THEMEROOT.'/fpcustomization/index.php');


/*Shop by category Slider*/
function shop_by_category_home_slider(){  
	$catHTML = '<div class="category_block"> 
               <div class="category_list">
                  <ul class="categorysalider slider">';  
	$product_cats = get_terms( array(
	    'taxonomy' => 'product_cat',
	    'hide_empty' => false, 
	    'exclude'=> array(15),    
	) );    
	if($product_cats){ 
		foreach ($product_cats as $product_cat) { 
			$thumnailID = get_term_meta($product_cat->term_id,'thumbnail_id',true); 
			$thumnail_URL = wp_get_attachment_image_src($thumnailID);    
			$thumnail_URL = isset($thumnail_URL[0])?$thumnail_URL[0]:site_url() . '/wp-content/themes/divi-child/fpcustomization/images/list_logoimg.jpg';        
			$catHTML .= ' 
			<li> 
			<a href="'.get_category_link( $product_cat->term_id ).'"> 
			<img class="img-fluid" src="'.$thumnail_URL.'" alt="Image" />
			<div class="cat_name"><h4>'.$product_cat->name.'</h4></div> 
			</a> 
			</li>';  
		}     
	}    
	 
	$catHTML .= '</ul> 
               </div>
            </div>';      
	return $catHTML;      
} 
add_shortcode('shop_by_category_slider','shop_by_category_home_slider'); 

/*Shop by category Slider*/
function popular_category_home_slider(){  
	$popular_collections = '<div id="product_tabs">'; 
	$product_cats = get_terms( array(
	    'taxonomy' => 'product_cat',
	    'hide_empty' => false,  
	    'number'    => 5,
	    'hide_empty'=>1, 
	    'exclude'=> array(15), 
	    'orderby' => 'count',
		'order' => 'DESC',  
	) );    
	if($product_cats){
		$i = 0; 
		foreach ($product_cats as $product_cat) {  
			$thumnailID = get_term_meta($product_cat->term_id,'thumbnail_id',true); 
			$thumnail_URL = wp_get_attachment_image_src($thumnailID,'full');    
			$thumnail_URL = isset($thumnail_URL[0])?$thumnail_URL[0]:site_url() . '/wp-content/themes/divi-child/fpcustomization/images/list_logoimg.jpg';    
			if(!$i){
				$popular_collections .= '<div class="left_categories">'; 
			} 
			if($i == 2){
				$popular_collections .= '</div><div class="right_categories">';   
			} 
					$popular_collections .= '
		         <a class="nav-link '.(($i)?'':'active').'" cat_slug="'.$product_cat->slug.'"  href="'.get_category_link( $product_cat->term_id ).'"> 
		            <div class="main_tab">
		               <img class="img-fluid" src="'.$thumnail_URL.'" alt="Image" />
		               <div class="heading_tab"><h4>'.$product_cat->name.'</h4></div>
		               <div class="tab_hover"> 
		                 <strong>'.$product_cat->name.'</strong> 
		                 <button type="button" class="tab_shop" data-location="'.get_category_link( $product_cat->term_id ).'">Shop Now</button>    
		               </div>
		            </div>
		         </a>'; 
		    if($i == 4){
				$popular_collections .= '</div>';  
			}   
				       
			$i++; 
		} 
	}   
	$popular_collections .= '<div class="tab-content" id="pills-tabContent">';    
	if($product_cats){ 
		$i = 0;
		foreach ($product_cats as $product_cat) {   
			$args = array(
			    'post_type'             => 'product',
			    'post_status'           => 'publish',
			    'ignore_sticky_posts'   => 1,  
			    'posts_per_page'        => 9, 
			    'tax_query'             => array(
			        array(
			            'taxonomy'      => 'product_cat',
			            'field' => 'term_id',  
			            'terms'         => $product_cat->term_id,
			            'operator'      => 'IN'   
			        )
			    )
);
		$cat_products = new WP_Query($args);	 
			$popular_collections .= '<div class="tab-pane" style="display:'.((!$i)?'block':'').'" id="pillstab'.$product_cat->slug.'"><div class="product_row_container">';
			    while ( $cat_products->have_posts() ) : $cat_products->the_post();
				global $product; 
				$imageSRc = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ), 'single-post-thumbnail' );
				$imageSRc = @$imageSRc[0]?$imageSRc[0]:site_url().'/wp-content/uploads/woocommerce-placeholder-800x800.png'; 
				$price_html = $product->get_price_html();  
				 $popular_collections .= '<div class="product_row"> 
				 							<div class="product_img"><img src="'.$imageSRc.'"> 
				 							<div class="product_overlay"> 
				 							<div class="product_overlay_details"> 
				 							<ul class="product_overlay_list">  
				 							<li><a href="#"><img class="img-fluid" src="'.site_url().'/wp-content/themes/divi-child/fpcustomization/images/cart_fav.png" alt="Image" /><span></span></a></li>    
				 							<li><a href="'.get_permalink().'"><img class="img-fluid" src="'.site_url().'/wp-content/themes/divi-child/fpcustomization/images/cart_upload.png" alt="Image" /></a></li> 
				 							</ul>
				 							</div>
				 							</div>
				 							</div>
				 							<div class="product_content">
				 							<div class="left_product_data">
					 							<h3>'.get_the_title($product->ID).'</h3>
					 							<span>'.$price_html.'</span>
											</div>
											<div class="right_product_data">  
					 							 <a href="'.get_permalink().'">More Colors</a>
											</div>
				 							</div>
				 						 </div>';  

				endwhile;wp_reset_query(); 
			$popular_collections .= '</div></div>'; 
		 $i++;
		}
	}     
$popular_collections .= ' </div> 
</div>';  
	return $popular_collections;       
} 
add_shortcode('popular_collections','popular_category_home_slider');  
 

add_shortcode( 'faq_list', 'ws_faq_list_func' );
function ws_faq_list_func($atts,$content = ""){

$args = array('numberposts' => 100,'post_type'=> 'faqs');

$latest_faqs = get_posts($args);

ob_start();

if($latest_faqs){

echo "<ul class='faqs-list'>";

foreach ($latest_faqs as $faq) {

echo "<li>";

echo "<h2 class='faqs-head'>".$faq->post_title."</h2>";

echo "<div class='faqs-content'>".$faq->post_content."</div>";

echo "</li>";

} 
echo "</ul>";
}

$faqs = ob_get_clean();

   return $faqs;
}  

function register_custom_menu(){
	register_nav_menus(
  array( 
   'toggle-menu' => __( 'Header Toggle Menu', 'wstheme' ),  
 )
); 
}
add_action( 'after_setup_theme', 'register_custom_menu' ); 

add_filter( 'woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');
function wc_refresh_mini_cart_count($fragments){
    ob_start();
    ?>
    <span id="mini-cart-count" class="cart-items">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php
        $fragments['#mini-cart-count'] = ob_get_clean();
    return $fragments;
}


function save_post_prices( $post_id ) {
	global $post; 
    if ( $post->post_type == 'page' && $post->post_name = 'dashboard' ) {
		update_post_meta( $post_id, '_sale_price', $_POST['_sale_price'] );
		update_post_meta( $post_id, '_regular_price', $_POST['_regular_price'] );
	}
}
add_action( 'save_post' , 'save_post_prices' );

//Customize Login Page

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(https://daffodealstest.wpengine.com/wp-content/uploads/2020/05/dashlogo.png);
		height: 234px;
    width: 234px;
    background-size: 234px !important;
    background-repeat: no-repeat;
    padding-bottom: 0px;
    margin-bottom: -100px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );
