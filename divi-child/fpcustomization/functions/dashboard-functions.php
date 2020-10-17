<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//OVERALL CURRENT MONTH RATING
function overall_current_month_rating($month, $user_id){
  global $wpdb;
  $current_month_rating = $wpdb->get_row( 
    $wpdb->prepare(
      "SELECT 
      AVG(cm.meta_value) as average,
      COUNT(cm.meta_value) as ratings  
      FROM 
      $wpdb->posts p
      INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
      INNER JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
      WHERE 
      p.post_author = %d 
      AND p.post_type = 'product' 
      AND p.post_status = 'publish'
      AND (cm.meta_key = 'rating' OR cm.meta_key IS NULL)
      AND wc.comment_approved = 1 
      AND MONTH(wc.comment_date) = $month
      ORDER BY 
      wc.comment_post_ID", 
      $user_id 
    ) 
  );
  return $current_month_rating;
}

//OVERALL RATING GRAPH
function overall_rating_graph($from_date, $end_date, $user_id){
  global $wpdb;
  $overall_rating_result = $wpdb->get_results( 
    $wpdb->prepare(
      "SELECT 
      MONTH(wc.comment_date) as month_name, 
      AVG(cm.meta_value) as rating 
      FROM 
      $wpdb->posts p
      INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
      INNER JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
      WHERE 
      p.post_author = %d 
      AND p.post_type = 'product' 
      AND p.post_status = 'publish'
      AND (cm.meta_key = 'rating' OR cm.meta_key IS NULL)
      AND wc.comment_approved = 1 
      AND wc.comment_date BETWEEN CAST('".$from_date."' AS DATE) AND CAST('".$end_date."' AS DATE)
      GROUP BY month_name
      ORDER BY 
      wc.comment_post_ID", 
      $user_id 
    ) 
  );
  return $overall_rating_result;
}

//GRAPH POSITIVE REVIEWS
function graph_positive_reviews($from_date, $end_date, $user_id){
  global $wpdb;
  $positive_review_result = $wpdb->get_results( 
    $wpdb->prepare(
      "SELECT 
      MONTH(wc.comment_date) as month_name, 
      COUNT(wc.comment_ID) as count 
      FROM 
      $wpdb->posts p
      INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
      INNER JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
      WHERE 
      p.post_author = %d 
      AND p.post_type = 'product' 
      AND p.post_status = 'publish'
      AND (cm.meta_key = 'rating' OR cm.meta_key IS NULL)
      AND (cm.meta_value = 4 OR cm.meta_value = 5) 
      AND wc.comment_approved = 1 
      AND wc.comment_date BETWEEN CAST('".$from_date."' AS DATE) AND CAST('".$end_date."' AS DATE)
      GROUP BY month_name
      ORDER BY 
      wc.comment_post_ID", 
      $user_id 
    ) 
  );
  return $positive_review_result;
}

//GRAPH NEGATIVE REVIEWS
function graph_negative_reviews($from_date, $end_date, $user_id){
  global $wpdb;
  $negative_review_result = $wpdb->get_results( 
    $wpdb->prepare(
      "SELECT 
      MONTH(wc.comment_date) as month_name, 
      COUNT(wc.comment_ID) as count 
      FROM 
      $wpdb->posts p
      INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
      INNER JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
      WHERE 
      p.post_author = %d 
      AND p.post_type = 'product' 
      AND p.post_status = 'publish'
      AND (cm.meta_key = 'rating' OR cm.meta_key IS NULL)
      AND (cm.meta_value = 1 OR cm.meta_value = 2 OR cm.meta_value = 3) 
      AND wc.comment_approved = 1 
      AND wc.comment_date BETWEEN CAST('".$from_date."' AS DATE) AND CAST('".$end_date."' AS DATE)
      GROUP BY month_name
      ORDER BY 
      wc.comment_post_ID", 
      $user_id 
    ) 
  );
  return $negative_review_result;
}


//ADD NEW CLASS ON WOOCOMMERCE RATING HTML
add_filter( 'woocommerce_product_get_rating_html', 'product_get_rating_html_callback'  , 20, 2);
function product_get_rating_html_callback($rating_html, $rating){
    if ( $rating > 0 ) { 
        $rating_html = '<div class="star-rating rating-'.$rating.'" title="' . sprintf( esc_attr__( 'Rated %s out of 5', 'woocommerce' ), $rating ) . '">'; 
        $rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . esc_html__( 'out of 5', 'woocommerce' ) . '</span>'; 
        $rating_html .= '</div>'; 
    } else { 
        $rating_html = ''; 
    } 

    return $rating_html;
}

/* 
----------------------------------
  DASHBOARD REVIEWS MENU FUNCTIONALITY
------------------------------------
*/

//REVIEWS FILTER HERE
add_action( 'wp_ajax_reviews_start_filter', 'reviews_start_filter_callback' );
function reviews_start_filter_callback(){
    global $wpdb;

    $rate = sanitize_text_field($_POST['rate']);
    $page = sanitize_text_field($_POST['page']);

    $id          = get_current_user_id();
    $post_type   = 'product';
    $limit       = 100;
    $status      = '1';
    $page_number = $page ? $page : get_query_var( 'paged' );
    $pagenum     = max( 1, $page_number );
    $offset      = ( $pagenum - 1 ) * $limit;

    $rating_text = '';
    $rating_filter = 'cm.meta_value != "" AND'; 
    if(!empty($rate) && $rate != 'Any') {
      $rating_text = $rate.' start';
      $rating_filter = 'cm.meta_value = '.$rate.' AND'; 
    }
    if(!empty($rate) && $rate == 'Positive') {
      $rating_text = '';
      $rating_filter = '(cm.meta_value = 4 OR cm.meta_value = 5) AND'; 
    }
    if(!empty($rate) && $rate == 'Negative') {
      $rating_text = '';
      $rating_filter = '(cm.meta_value = 1 OR cm.meta_value = 2 OR cm.meta_value = 3) AND'; 
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
      foreach ( $comments as $comment ) {
        $GLOBALS['comment'] = $comment;       
        $comment_date       = get_comment_date( '', $comment->comment_ID );
        $comment_author_img = get_avatar( $comment->comment_author_email, 32 );
        $eidt_post_url      = get_edit_post_link( $comment->comment_post_ID );
        $permalink          = get_comment_link( $comment );
        $comment_status     =  $comment->comment_approved;
        $page_status = '';
        dokan_get_template_part( 'review/listing-table-tr', '', array(
          'pro'                => true,
          'comment'            => $comment,
          'comment_date'       => $comment_date,
          'comment_author_img' => $comment_author_img,
          'eidt_post_url'      => $eidt_post_url,
          'permalink'          => $permalink,
          'page_status'        => $page_status,
          'post_type'          => $post_type,
          'comment_status'     => $comment_status
        ) );
      }
    }else{
      echo '<div class="nocommet"><p class="woocommerce-noreviews">No Reviews found.</p></div>';
    }   
  die;
}

//REVIEW DEAL FILTER HERE
add_action( 'wp_ajax_review_deal_star_filter', 'review_deal_star_filter' );
function review_deal_star_filter(){
  $post_id = sanitize_text_field($_POST['post_id']);
  $rate = sanitize_text_field($_POST['rate']);
  $page = sanitize_text_field($_POST['page']);
  $page = empty($page)?1:$page;
  review_deal($post_id,$rate,$page);
  die;
}

function review_deal($post_id,$rate, $paged = 1){

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
    if ($rate == 'Positive') {
      unset($args['meta_query']);
      $args['meta_query'] = array(
        'relation' => 'OR',
        array(
          'key'   => 'rating',
          'value' => 4,
          'compare' => '='
        ),
        array(
          'key'   => 'rating',
          'value' => 5,
          'compare' => '='
        )
      ); 
    }
    if ($rate == 'Negative') {
      unset($args['meta_query']);
      $args['meta_query'] = array(
        'relation' => 'OR',
        array(
          'key'   => 'rating',
          'value' => 1,
          'compare' => '='
        ),
        array(
          'key'   => 'rating',
          'value' => 2,
          'compare' => '='
        ),
        array(
          'key'   => 'rating',
          'value' => 3,
          'compare' => '='
        )
      ); 
    }

    $number = 20;

    $args['number'] = $number;
    $args['paged'] = $paged;

    
   // print_r($args);
    $comments_query = new WP_Comment_Query;
    $comments = $comments_query->query( $args );
    if( $comments ) :
      foreach( $comments as $comment ) :
        $GLOBALS['comment'] = $comment;       
        $comment_date       = get_comment_date( '', $comment->comment_ID );
        $comment_author_img = get_avatar( $comment->comment_author_email, 32 );
        $eidt_post_url      = get_edit_post_link( $comment->comment_post_ID );
        $permalink          = get_comment_link( $comment );
        $comment_status     =  $comment->comment_approved;
        $page_status = '';
        dokan_get_template_part( 'review/listing-table-tr', '', array(
          'pro'                => true,
          'comment'            => $comment,
          'comment_date'       => $comment_date,
          'comment_author_img' => $comment_author_img,
          'eidt_post_url'      => $eidt_post_url,
          'permalink'          => $permalink,
          'page_status'        => $page_status,
          'post_type'          => $post_type,
          'comment_status'     => $comment_status
        ) );           
      endforeach;
    else:
      echo '<div class="nocommet"><p class="woocommerce-noreviews">End of Reviews</p></div>';
    endif;
  } 
}

//REVIEW RESPONSE
add_action( 'wp_ajax_deal_review_response', 'deal_review_response' );
function deal_review_response(){
  check_ajax_referer( 'dokan_reviews', 'nonce' );
  $comment_id = sanitize_text_field($_POST['rid']);
  $response = sanitize_text_field($_POST['leave_response']);
  if (!empty($comment_id) && !empty($response)){
    $response = array('add_date' => date('Y-m-d'), 'response' => $response);
    update_comment_meta( $comment_id, '_review_response', $response);
  }
  die;
}
/* 
----------------------------------
  DASHBOARD CALENDER MENU FUNCTIONALITY
------------------------------------
*/

add_action( 'wp_ajax_get_ws_calendar', 'get_ws_calendar_html' );
function get_ws_calendar_html(){
  if( current_user_can('administrator') ) {
    $calendar =  new adminCalendar();
  }else{
    $calendar = new Calendar();
  }
  echo $calendar->show();
  die;
}

class Calendar {  
     
    /**
     * Constructor
     */
    public function __construct(){     
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }
     
    /********************* PROPERTY ********************/  
    private $dayLabels = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
     
    private $currentYear=0;
     
    private $currentMonth=0;
     
    private $currentDay=0;
     
    private $currentDate=null;
     
    private $daysInMonth=0;

    private $PreMonthDays=0;

    private $PreMonth=0;

    private $PreYear=0;
    
    private $NextMonthDays=1;

    private $NextMonth=0;

    private $NextYear=0;
     
    private $naviHref= null;
     
    /********************* PUBLIC **********************/  
        
    /**
    * print out the calendar
    */
    public function show() {
        global $post;

        $year  = null;
         
        $month = null;
         
        if(null==$year&&isset($_POST['year'])){
 
            $year = $_POST['year'];
         
        }else if(null==$year){
 
            $year = date("Y",time());  
         
        }          
         
        if(null==$month&&isset($_POST['month'])){
 
            $month = $_POST['month'];
         
        }else if(null==$month){
 
            $month = date("m",time());
         
        }                  
         
        $this->currentYear=$year;
         
        $this->currentMonth=$month;
         
        $this->daysInMonth=$this->_daysInMonth($month,$year); 

        $this->PreMonth = ($this->currentMonth != 1) ? $this->currentMonth-1 : 12;
        $this->PreYear = ($this->PreMonth != 12) ? $this->currentYear : $this->currentYear-1;   

        $this->NextMonth = ($this->currentMonth == 12) ? 1 : $this->currentMonth+1;

        $this->NextYear = ($this->NextMonth == 1) ? $this->currentYear+1 : $this->currentYear;

        $total_Days_ofPrevMonth = cal_days_in_month(CAL_GREGORIAN,$this->PreMonth,$this->PreYear);
         
        $content='<div id="ws-calendar">'.
                        '<div class="box">'.
                        $this->_createNavi().
                        '</div>'.
                        '<div class="box-content">
                        <div id="calendar-loader" class="calendar-loader hide">
                          <div class="boxloader"><div class="loadernew"></div></div>
                      </div>
                        '.
                                '<ul class="label">'.$this->_createLabels().'</ul>';   
                                $content.='<div class="clear"></div>';     
                                $content.='<ul class="dates">';    
                                 
                                $weeksInMonth = $this->_weeksInMonth($month,$year);
                                $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
                                $this->PreMonthDays = $total_Days_ofPrevMonth-$firstDayOfTheWeek;
                                $filter_start_date = $this->PreMonthDays;
                                $this->PreMonthDays++;
                                // Create weeks in a month
                                for( $i=0; $i<$weeksInMonth; $i++ ){
                                     
                                    //Create days in a week
                                    for($j=0;$j<=6;$j++){
                                        $content.=$this->_showDay($i*7+$j);
                                    }
                                }
                                 
                                $content.='</ul>';
                                 
                                $content.='<div class="clear"></div>';     
             
                        $content.='</div>';
                 
        $content.='</div>';

        $content.='<div class="calendar-sidebar">
          <div class="calendar-deal">
            <ul class="deals">';
            $filter_start_date++;
            $filter_end_date = $this->NextMonthDays;
            $start_date = date('Y-m-d',strtotime($this->PreYear.'-'.$this->PreMonth.'-'.$filter_start_date));
            $filter_end_date--;
            $end_date = date('Y-m-d',strtotime($this->NextYear.'-'.$this->NextMonth.'-'.($filter_end_date)));

            $post_statuses = array( 'pending', 'beautify', 'settingup', 'upcoming', 'publish' );

            $args = array(
              'posts_per_page' => -1,
              'posts_type' => 'product',
              'post_status'    => $post_statuses,
              'author'         => get_current_user_id(),  
              'meta_query' => array(
                array(
                  'key'     => '_startDate',
                  'value'   => array( $start_date, $end_date ),
                  'type'    => 'date',
                  'compare' => 'BETWEEN',
                ),
              ), 
              'order' => 'ASC',  
              'orderby' => 'meta_value'         
            );
             
            $img_kses = apply_filters( 'dokan_product_image_attributes', array(
              'img' => array(
                'alt'    => array(),
                'class'  => array(),
                'height' => array(),
                'src'    => array(),
                'width'  => array(),
              ),
            ) );

            $deal_query = dokan()->product->all($args);
            
            //echo $deal_query->request;

            $seller_deal_status = seller_deal_status();
            
            $group_content = array();

            if ( $deal_query->have_posts() ) {

              while ($deal_query->have_posts()) {

                $deal_query->the_post();

                $product = wc_get_product( $post->ID );

                $post_meta = get_post_meta($post->ID);
               // print_r($post_meta);
                $startDate = isset( $post_meta['_startDate'])?$post_meta['_startDate'][0]:'';
                
                $post_status =  isset( $post_meta['_kate_post_status'])?$post_meta['_kate_post_status'][0]:'';
                $deal_status = '';
                if (!empty($post_status)) {
                  $deal_status = $seller_deal_status[$post_status];
                }
                $qty = isset( $post_meta['_totalQtyAvailable'])?$post_meta['_totalQtyAvailable'][0]:'';

                $sold = kc_get_deal_orders($post->ID);
                $percentage = !empty($qty)?($sold/$qty)*100:0;
                if (!empty($sold)) {
                 $qty = $sold.'/'.$qty;
                }
                $title = get_the_title();                
               
                $group_content[$startDate][] = '  <div class="deal">
                        <a href="'.esc_url( kate_edit_product_url( $post->ID ) ).'">
                          <div class="deal-img">
                            '.wp_kses( $product->get_image(), $img_kses ).'
                          </div>
                          <div class="deal-info">'.$title.'</div>
                          <div class="deal-status">
                            <span>'.$deal_status.'</span>
                            <span>'.$qty.'</span>
                          </div>
                          <div class="deal-progress">
                            <div class="amount" style="width:'.$percentage.'%"></div>
                          </div>
                        </a>
                      </div>';                
                
              }

              foreach ($group_content as $dealdate => $deals) {
                $deal_date = date('D - F d', strtotime($dealdate));
                $content.='<li id="'.date('Y-m-d', strtotime($dealdate)).'">
                  <div class="title-bar">'.$deal_date.'</div>
                      <div class="deals">';
                  foreach ($deals as $key => $deal) {
                    $content.= $deal;
                  }
                $content.='</div>
                </li>';
              }
            }  

            $content.='</ul>
          </div>
        </div>';  

        return $content;   
    }
     
    /********************* PRIVATE **********************/ 
    /**
    * create the li element for ul
    */
    private function _showDay($cellNumber){
        global $wpdb;

        $cl_class = '';

        if($this->currentDay==0){
             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));           
                    
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){
                $this->currentDay=1;                 
            }else{
              //PREVIOUS MONTH CONTENT
              $cellContent = $this->PreMonthDays++;
              $this->currentDate = date('Y-m-d',strtotime($this->PreYear.'-'.$this->PreMonth.'-'.($cellContent)));
            }
        }         
       
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
            $cellContent = $this->currentDay;
             
            $this->currentDay++;   
             
        }elseif($this->currentDay!=0 && $cellNumber>$this->currentDay){

            //NEXT MONTH CONTENT
            $cellContent=$this->NextMonthDays++;
            $this->currentDate = date('Y-m-d',strtotime($this->NextYear.'-'.$this->NextMonth.'-'.($cellContent)));
            $cl_class = 'future';
        } 

        $deals = $wpdb->get_results( 
          $wpdb->prepare(
            "SELECT 
            p.post_title 
            FROM 
            $wpdb->posts p
            INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
            WHERE 
            1=1 
            AND p.post_author = %d 
            AND p.post_type = 'product' 
            AND ((p.post_status = 'pending' OR  p.post_status = 'beautify' OR  p.post_status = 'settingup' OR  p.post_status = 'upcoming' OR  p.post_status = 'publish'))
            AND pm.meta_key = '_startDate'
            AND pm.meta_value = '".$this->currentDate."' 
            ", 
            get_current_user_id() 
          ) 
        );
        //print_r($deals);

        if ($this->currentDate==date('Y-m-d')) {
          $cl_class = 'today';
          $cellContent = '<span>'.$cellContent.'</span>';
        }elseif ($this->currentDate<=date('Y-m-d')) {
          $cl_class = 'past';
        }

        $deal_html = '';
        if (!empty($deals)) {
          $deal_html = '<div class="deals">';
          foreach ($deals as $key => $deal) {
            $deal_html .= '<div class="deal" title="'.$deal->post_title.'"><i class="fa fa-square" aria-hidden="true"></i></div>';
          }
          $deal_html .= '</div>';
          $cellContent = $cellContent.$deal_html;
        }
        //($this->currentDate==date('Y-m-d')?'today':'')

        return '<li data-date="'.$this->currentDate.'" id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).$cl_class.' cl-date">'.$cellContent.'</li>';
    }
     
    /**
    * create navigation
    */
    private function _createNavi(){
         
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
         
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
         
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
         
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
         
        return
            '<div class="header">
              <a class="today" data-month="'.date('m').'" data-year="'.date('Y').'" href="#">Today</a>
              <a class="prev" data-month="'.sprintf('%02d',$preMonth).'" data-year="'.$preYear.'" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
              <a class="next" data-month="'.sprintf('%02d',$nextMonth).'" data-year="'.$nextYear.'" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
              <span class="title">'.date('F Y ',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>
              <a class="new-deal" href="'.esc_url( dokan_get_navigation_url( 'new-product' ) ).'"><i class="fa fa-plus" aria-hidden="true"></i> NEW DEAL</a>
            </div>';
    }
         
    /**
    * create calendar week labels
    */
    private function _createLabels(){  
                 
        $content='';
         
        foreach($this->dayLabels as $index=>$label){
             
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
 
        }
         
        return $content;
    }     
     
    /**
    * calculate number of weeks in a particular month
    */
    private function _weeksInMonth($month=null,$year=null){
         
        if( null==($year) ) {
            $year =  date("Y",time()); 
        }
         
        if(null==($month)) {
            $month = date("m",time());
        }
         
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month,$year);
         
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
         
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
         
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
         
        if($monthEndingDay<$monthStartDay){
             
            $numOfweeks++;
         
        }
         
        return $numOfweeks;
    }
 
    /**
    * calculate number of days in a particular month
    */
    private function _daysInMonth($month=null,$year=null){
         
        if(null==($year))
            $year =  date("Y",time()); 
 
        if(null==($month))
            $month = date("m",time());
             
        return date('t',strtotime($year.'-'.$month.'-01'));
    }
     
}

/* 
------------------------------------------------
  DASHBOARD CALENDER MENU FUNCTIONALITY FOR ADMIN
-------------------------------------------------
*/

class adminCalendar {  
     
    /**
     * Constructor
     */
    public function __construct(){     
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }
     
    /********************* PROPERTY ********************/  
    private $dayLabels = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
     
    private $currentYear=0;
     
    private $currentMonth=0;
     
    private $currentDay=0;
     
    private $currentDate=null;
     
    private $daysInMonth=0;

    private $PreMonthDays=0;

    private $PreMonth=0;

    private $PreYear=0;
    
    private $NextMonthDays=1;

    private $NextMonth=0;

    private $NextYear=0;
     
    private $naviHref= null;
     
    /********************* PUBLIC **********************/  
        
    /**
    * print out the calendar
    */
    public function show() {
        global $post;

        $year  = null;
         
        $month = null;
         
        if(null==$year&&isset($_POST['year'])){
 
            $year = $_POST['year'];
         
        }else if(null==$year){
 
            $year = date("Y",time());  
         
        }          
         
        if(null==$month&&isset($_POST['month'])){
 
            $month = $_POST['month'];
         
        }else if(null==$month){
 
            $month = date("m",time());
         
        }                  
         
        $this->currentYear=$year;
         
        $this->currentMonth=$month;
         
        $this->daysInMonth=$this->_daysInMonth($month,$year); 

        $this->PreMonth = ($this->currentMonth != 1) ? $this->currentMonth-1 : 12;
        $this->PreYear = ($this->PreMonth != 12) ? $this->currentYear : $this->currentYear-1;   

        $this->NextMonth = ($this->currentMonth == 12) ? 1 : $this->currentMonth+1;

        $this->NextYear = ($this->NextMonth == 1) ? $this->currentYear+1 : $this->currentYear;

        $total_Days_ofPrevMonth = cal_days_in_month(CAL_GREGORIAN,$this->PreMonth,$this->PreYear);
         
        $content='<div id="ws-calendar">'.
                        '<div class="box">'.
                        $this->_createNavi().
                        '</div>'.
                        '<div class="box-content">
                        <div id="calendar-loader" class="calendar-loader hide">
                          <div class="boxloader"><div class="loadernew"></div></div>
                      </div>
                        '.
                                '<ul class="label">'.$this->_createLabels().'</ul>';   
                                $content.='<div class="clear"></div>';     
                                $content.='<ul class="dates">';    
                                 
                                $weeksInMonth = $this->_weeksInMonth($month,$year);
                                $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
                                $this->PreMonthDays = $total_Days_ofPrevMonth-$firstDayOfTheWeek;
                                $filter_start_date = $this->PreMonthDays;
                                $this->PreMonthDays++;
                                // Create weeks in a month
                                for( $i=0; $i<$weeksInMonth; $i++ ){
                                     
                                    //Create days in a week
                                    for($j=0;$j<=6;$j++){
                                        $content.=$this->_showDay($i*7+$j);
                                    }
                                }
                                 
                                $content.='</ul>';
                                 
                                $content.='<div class="clear"></div>';     
             
                        $content.='</div>';
                 
        $content.='</div>';
 

        return $content;   
    }
     
    /********************* PRIVATE **********************/ 
    /**
    * create the li element for ul
    */
    private function _showDay($cellNumber){
        global $wpdb;

        $cl_class = '';

        if($this->currentDay==0){
             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));           
                    
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){
                $this->currentDay=1;                 
            }else{
              //PREVIOUS MONTH CONTENT
              $cellContent = $this->PreMonthDays++;
              $this->currentDate = date('Y-m-d',strtotime($this->PreYear.'-'.$this->PreMonth.'-'.($cellContent)));
            }
        }         
       
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
            $cellContent = $this->currentDay;
             
            $this->currentDay++;   
             
        }elseif($this->currentDay!=0 && $cellNumber>$this->currentDay){

            //NEXT MONTH CONTENT
            $cellContent=$this->NextMonthDays++;
            $this->currentDate = date('Y-m-d',strtotime($this->NextYear.'-'.$this->NextMonth.'-'.($cellContent)));
            $cl_class = 'future';
        } 
     
        $deals = $wpdb->get_row( 
          $wpdb->prepare(
            "SELECT
            SUM(IF(IFNULL((SELECT COUNT(*) FROM $wpdb->postmeta WHERE p.ID = post_id AND meta_key = '_kate_post_status' AND (meta_value = 'in-review' OR meta_value = 'beautify' OR meta_value = 'setting-up') GROUP BY meta_value),0)=0,0,1)) as pending,
            SUM(IF(IFNULL((SELECT COUNT(*) FROM $wpdb->postmeta WHERE p.ID = post_id AND meta_key='_kate_post_status' AND (meta_value = 'active' OR meta_value = 'upcoming') GROUP BY meta_value)  ,0)=0,0,1)) as active
            FROM
            $wpdb->posts p
            INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
            WHERE
            1=1
            AND p.post_type = 'product'
            AND (p.post_status = 'pending' OR  p.post_status = 'beautify' OR  p.post_status = 'settingup' OR  p.post_status = 'upcoming' OR  p.post_status = 'publish')
            AND pm.meta_key = '_startDate'
            AND pm.meta_value = '".$this->currentDate."' 
            ", ) 
        );       
   
        if ($this->currentDate==date('Y-m-d')) {
          $cl_class = 'today';
          $cellContent = '<span>'.$cellContent.'<span>';
        }elseif ($this->currentDate<=date('Y-m-d')) {
          $cl_class = 'past';
        }

        $deal_html = '';
        if((!empty($deals->pending)) || (!empty($deals->active))){
          $deal_html = '<div class="deals">';          
            $deal_html .= '<div class="deal">';

            if(!empty($deals->pending)){
              $deal_html .= '<a href='.add_query_arg('filter_date', $this->currentDate,site_url('/dashboard/pending-product')).'>Pending: '.$deals->pending.' </a>';
            }

            if(!empty($deals->active)){
              $deal_html .= '<a href='.add_query_arg('filter_date', $this->currentDate,site_url('/dashboard/active-product')).'>
              Active: '.$deals->active.'</a>';
            }
            
            $deal_html .= '</div>';
          $deal_html .= '</div>';
          $cellContent = $cellContent.$deal_html;
        }       
      
        return '<li data-date="'.$this->currentDate.'" id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).$cl_class.' cldate">'.$cellContent.'</li>';
    }
    
    /**
    * create navigation
    */
    private function _createNavi(){
         
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
         
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
         
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
         
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
         
        return
            '<div class="header">
              <a class="today" data-month="'.date('m').'" data-year="'.date('Y').'" href="#">Today</a>
              <a class="prev" data-month="'.sprintf('%02d',$preMonth).'" data-year="'.$preYear.'" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
              <a class="next" data-month="'.sprintf('%02d',$nextMonth).'" data-year="'.$nextYear.'" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
              <span class="title">'.date('F Y ',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>
              <a class="new-deal" href="'.esc_url( dokan_get_navigation_url( 'new-product' ) ).'"><i class="fa fa-plus" aria-hidden="true"></i> NEW DEAL</a>
            </div>';
    }
         
    /**
    * create calendar week labels
    */
    private function _createLabels(){  
                 
        $content='';
         
        foreach($this->dayLabels as $index=>$label){
             
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
 
        }
         
        return $content;
    }     
     
    /**
    * calculate number of weeks in a particular month
    */
    private function _weeksInMonth($month=null,$year=null){
         
        if( null==($year) ) {
            $year =  date("Y",time()); 
        }
         
        if(null==($month)) {
            $month = date("m",time());
        }
         
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month,$year);
         
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
         
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
         
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
         
        if($monthEndingDay<$monthStartDay){
             
            $numOfweeks++;
         
        }
         
        return $numOfweeks;
    }
 
    /**
    * calculate number of days in a particular month
    */
    private function _daysInMonth($month=null,$year=null){
         
        if(null==($year))
            $year =  date("Y",time()); 
 
        if(null==($month))
            $month = date("m",time());
             
        return date('t',strtotime($year.'-'.$month.'-01'));
    }
     
}




/* 
----------------------------------
  DASHBOARD DEAL ORDERS
------------------------------------
*/

//add_action( 'init','order_listing_status_filtersss', 99 );
function order_listing_status_filtersss(){
  global $wp_filter;
//print_r($wp_filter);
  remove_all_actions( 'dokan_order_inside_content');  
}

function dokan_get_deal_orders( $seller_id, $order_search = null, $pid ) {
    global $wpdb;

    $cache_group = 'dokan_seller_data_'.$seller_id;
    $cache_key   = 'dokan-seller-orders-' . $pid . '-' . $seller_id;
    $orders      = wp_cache_get( $cache_key, $cache_group );
    //print_r($orders);
    $orders = false;
    $items = $wpdb->prefix.'woocommerce_order_items';
    $itemmeta = $wpdb->prefix.'woocommerce_order_itemmeta';

    $join        = $pid ? " INNER JOIN {$items} item ON p.ID = item.order_id" : '';
    $join        .= $pid ? " LEFT JOIN {$itemmeta} imeta ON item.order_item_id=imeta.order_item_id " : '';
    $where       = $pid ? sprintf( " imeta.meta_key = '_product_id' AND imeta.meta_value = %d AND ", $pid ) : '';  

    //echo $order_search;
    if (!empty($order_search) && is_numeric($order_search)) { 
      $where       .= $order_search ? sprintf( " p.ID = %d AND ", $order_search ) : '';
    }elseif(!empty($order_search)) { 
      $join        .= $order_search ? " LEFT JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_customer_user' " : '';
      $join        .= $order_search ? " LEFT JOIN $wpdb->usermeta fm ON pm.meta_value = fm.user_id " : '';
      $join        .= $order_search ? " LEFT JOIN $wpdb->usermeta lm ON pm.meta_value = lm.user_id " : '';

      $where       .= $order_search ? sprintf( " fm.meta_value LIKE '%%%s%%' AND ", '%'.$wpdb->esc_like($order_search).'%' ) : '';
      $where       .= $order_search ? sprintf( " lm.meta_value LIKE '%%%s%%' AND ", '%'.$wpdb->esc_like($order_search).'%' ) : '';
    }

    if ( $orders === false ) {
      if (current_user_can('administrator')) 
      {
        $orders = $wpdb->get_results( $wpdb->prepare( "SELECT do.order_id, p.post_date
            FROM {$wpdb->prefix}dokan_orders AS do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                {$where}
                (p.post_status != 'trash' AND p.post_status != 'wc-cancelled')
                {$date_query}
                {$status_where}
            GROUP BY do.order_id
            ORDER BY p.post_date DESC
            ",
        ) );
      }else{
        $orders = $wpdb->get_results( $wpdb->prepare( "SELECT do.order_id, p.post_date
            FROM {$wpdb->prefix}dokan_orders AS do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                do.seller_id = %d AND
                {$where}
                (p.post_status != 'trash' AND p.post_status != 'wc-cancelled')
                {$date_query}
                {$status_where}
            GROUP BY do.order_id
            ORDER BY p.post_date DESC
            ", $seller_id
        ) );
      }
      wp_cache_set( $cache_key, $orders, $cache_group );
      dokan_cache_update_group( $cache_key, $cache_group );
    }

    return $orders;
}

function dokan_search_orders($order_search) {
    global $wpdb;

    $cache_group = 'dokan_seller_data_'.$seller_id;
    $cache_key   = 'dokan-seller-orders-' . $pid . '-' . $seller_id;
    $orders      = wp_cache_get( $cache_key, $cache_group );
    //print_r($orders);
    $orders = false;
    $items = $wpdb->prefix.'woocommerce_order_items';
    $itemmeta = $wpdb->prefix.'woocommerce_order_itemmeta';

    $join        = $pid ? " INNER JOIN {$items} item ON p.ID = item.order_id" : '';
    $join        .= $pid ? " LEFT JOIN {$itemmeta} imeta ON item.order_item_id=imeta.order_item_id " : '';
    $where       = $pid ? sprintf( " imeta.meta_key = '_product_id' AND imeta.meta_value = %d AND ", $pid ) : '';  

    //echo $order_search;
    if (!empty($order_search) && is_numeric($order_search)) { 
      $where       .= $order_search ? sprintf( " p.ID = %d AND ", $order_search ) : '';
    }elseif(!empty($order_search)) { 
      $join        .= $order_search ? " LEFT JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_customer_user' " : '';
      $join        .= $order_search ? " LEFT JOIN $wpdb->users u ON pm.meta_value = u.ID " : '';
      $where       .= $order_search ? sprintf( " u.user_email = '%s' AND ", $wpdb->esc_like($order_search)) : '';
    }

    if ( $orders === false ) {
      $orders = $wpdb->get_results( $wpdb->prepare( "SELECT do.order_id, p.post_date
            FROM {$wpdb->prefix}dokan_orders AS do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                {$where}
                (p.post_status != 'trash' AND p.post_status != 'wc-cancelled')
                {$date_query}
                {$status_where}
            GROUP BY do.order_id
            ORDER BY p.post_date DESC
            ",
        ) );
      wp_cache_set( $cache_key, $orders, $cache_group );
      dokan_cache_update_group( $cache_key, $cache_group );
    }

    return $orders;
}

function dokan_get_deal_orders_summary( $seller_id, $order_search = null, $pid) {
    global $wpdb;

    $cache_group = 'dokan_seller_data_'.$seller_id;
    $cache_key   = 'dokan-seller-orders-summary-' . $pid . '-' . $seller_id;
    $orders      = wp_cache_get( $cache_key, $cache_group );
    //print_r($orders);
    $orders = false;
    $items = $wpdb->prefix.'woocommerce_order_items';
    $itemmeta = $wpdb->prefix.'woocommerce_order_itemmeta';

    $join        = $pid ? " INNER JOIN {$items} item ON p.ID = item.order_id" : '';
    $join        .= $pid ? " LEFT JOIN {$itemmeta} imeta ON item.order_item_id=imeta.order_item_id " : '';
    $where       = $pid ? sprintf( " imeta.meta_key = '_product_id' AND imeta.meta_value = %d AND ", $pid ) : '';  

    //echo $order_search;
    if(!empty($order_search)) { 
      $join        .= " LEFT JOIN {$itemmeta} imeta2 ON item.order_item_id=imeta2.order_item_id ";
      $where       .= $order_search ? sprintf( " imeta2.meta_value LIKE '%%%s%%' AND ", '%'.$wpdb->esc_like($order_search).'%' ) : '';
     
    }

    if ( $orders === false ) {
      if (current_user_can('administrator')) 
      {
        $orders = $wpdb->get_results( $wpdb->prepare( "SELECT do.order_id, imeta.order_item_id
            FROM {$wpdb->prefix}dokan_orders AS do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                {$where}
                (p.post_status != 'trash' AND p.post_status != 'wc-cancelled')            
            ORDER BY p.post_date DESC
            ",
        ) );
      }else{
        $orders = $wpdb->get_results( $wpdb->prepare( "SELECT do.order_id, imeta.order_item_id
            FROM {$wpdb->prefix}dokan_orders AS do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                do.seller_id = %d AND
                {$where}
                (p.post_status != 'trash' AND p.post_status != 'wc-cancelled')            
            ORDER BY p.post_date DESC
            ", $seller_id
        ) );

      }
      wp_cache_set( $cache_key, $orders, $cache_group );
      dokan_cache_update_group( $cache_key, $cache_group );
    }
    return $orders;
}

add_action( 'wp_ajax_whatsolditems', 'whatsolditems_handle' );
function whatsolditems_handle(){
  global $wpdb;
  $seller_id    = dokan_get_current_user_id();
  $pid  = isset( $_POST['pid'] ) ? sanitize_text_field( $_POST['pid'] ) : null;
  $startDate = get_post_meta($pid , '_startDate', true);
  $ships_date = get_post_meta($pid , '_ships_date', true);

  $user_orders_summary  = dokan_get_deal_orders_summary( $seller_id, $order_search, $pid );
  $product_title = get_the_title($pid);
  //print_r($user_orders_summary);
  $tbl_attributes = $wpdb->prefix."product_attributes"; 
  $attributes = $wpdb->get_results( $wpdb->prepare( "SELECT title
      FROM $tbl_attributes
      WHERE
      product_id = %d ", $pid
  ));

  ?>

   <div class="order-summary">
      <div class="deal-title">
          <h3><?php echo get_the_title($pid); ?></h3>
          <span><?php esc_attr_e( 'Deal Ran', 'dokan-lite' ); ?> <?php echo date('M d',strtotime($startDate)); ?> - <?php echo date('M d',strtotime("+3 day", strtotime($startDate))); ?>&nbsp; | &nbsp;<?php esc_attr_e( 'Ships by', 'dokan-lite' ); ?> - <?php echo date('M d',strtotime($ships_date)); ?></span>
      </div>
      <div class="dokan-order-filter-serach">
          <button id="PrintOrderSummary" class="upload-tracking dokan-left"><i class="fa fa-print" aria-hidden="true"></i> <?php esc_attr_e( 'PRINT', 'dokan-lite' ); ?></button>
          <!-- <form action="" method="GET" class="dokan-right">
              <div class="dokan-form-group">
                  <input type="hidden" name="pid" value="<?php //echo base64_encode($pid); ?>">
                  <input type="text" class="search" name="order_search" id="" placeholder="<?php //esc_attr_e( 'Filter by Date', 'dokan-lite' ); ?>" value="<?php //echo esc_attr( $order_search ); ?>">
                  <input type="submit" name="dokan_order_filter" class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme" value="<?php //esc_attr_e( 'Search', 'dokan-lite' ); ?>">
                  <input type="hidden" name="order_status" value="<?php //echo  esc_attr( $order_status ); ?>">

              </div>
          </form> -->
      </div>
      <?php if ( $user_orders_summary ) { ?>
          <div id="summary-list" class="summary-list">
              <table class="dokan-table dokan-table-striped">
                  <thead>
                      <tr>
                          <?php 
                          if (!empty($attributes)) {
                                 foreach ($attributes as $key => $attr) {
                                  ?>
                                  <th><?php echo $attr->title; ?></th>
                                  <?php 
                              }
                          }else{
                              ?>
                             <th><?php _e('Product', 'dokan-lite'); ?></th>
                              <?php 
                          } 
                          ?>
                          <th><?php _e('QUANTITY', 'dokan-lite'); ?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php
                      $total_qty = 0;
                      $colspans = 0;
                      foreach ($user_orders_summary as $order) {
                          $qty = wc_get_order_item_meta( $order->order_item_id, '_qty', true );
                          $product_attributes = wc_get_order_item_meta( $order->order_item_id, 'product_attributes', true );
                          $colspans = !empty($product_attributes)?count($product_attributes):0;
                          $total_qty += $qty;
                          ?>
                              <tr>
                                  <?php 
                                  if (!empty($product_attributes)) {
                                      foreach ($product_attributes as $val) {
                                          ?>
                                          <td class="dokan-order-id" data-title="Order">
                                              <?php echo ($val)?ucfirst($val):'N/A'; ?>
                                          </td>
                                          <?php 
                                      }
                                  }else{
                                  ?>
                                      <td class="dokan-order-id" data-title="Order">
                                      <?php echo $product_title; ?>
                                      </td>
                                  <?php  
                                  }
                                  ?>
                                  <td><?php echo $qty; ?></td>
                              </tr>
                          <?php 
                      }
                      ?>
                      <td style="text-align: left" colspan="<?php echo $colspans; ?>"><strong><?php _e('TOTAL', 'dokan-lite'); ?></strong></td>
                      <td><strong><?php echo $total_qty; ?></strong></td>
                  </tbody>
              </table>
          </div>
      <?php }else{ ?>
          <div class="dokan-error">
              <?php esc_html_e( 'No orders summary found', 'dokan-lite' ); ?>
          </div>
      <?php } ?>
  </div>
  <?php 
die;

}


add_action( 'wp_ajax_DashboardOrderDetails', 'DashboardOrderDetails' );
function DashboardOrderDetails(){
  check_ajax_referer( 'dokan_reviews', 'nonce' );
  $OrderID = sanitize_text_field($_POST['OrderID']);
  $OrderPID = sanitize_text_field($_POST['OrderPID']);
  if (!empty($OrderID)) {
    dokan_get_template_part( 'orders/order-details', '', array(
          'pro'       => true,
          'order_id'  => $OrderID,
          'pid'       => $OrderPID
        ) );
  }  
  die;
}

add_filter( 'woocommerce_get_formatted_order_total',  'woocommerce_get_formatted_order_total', 10, 2 );
function woocommerce_get_formatted_order_total($formatted_total, $object ){  
  $shipping_price = get_post_meta($object->get_id(), '_shipping_price', true);
  $order_total = $object->get_subtotal();
  if (!empty($shipping_price)) {
    $order_total = $order_total + $shipping_price;
  }
  return $formatted_total = wc_price( $order_total, array( 'currency' => $object->get_currency() ) );
}

add_action( 'init', 'DealOrderDownloads' );
function DealOrderDownloads(){
  //ORDER SUMMARY
  if (isset($_POST['order_download_list']) 
    && !empty($_POST['order_download_list']) 
    && $_POST['order_download_list'] == 'download_summary' 
    && isset($_POST['order_download_nonce']) 
    && wp_verify_nonce( $_POST['order_download_nonce'], 'order_download_action')) 
  {
      global $wpdb;
      $pid = sanitize_text_field($_POST['deal_id']);
      $seller_id    = dokan_get_current_user_id();
      $tbl_attributes = $wpdb->prefix."product_attributes"; 

      $attributes = $wpdb->get_results( $wpdb->prepare( "SELECT title
        FROM $tbl_attributes
        WHERE
        product_id = %d ", $pid
      ) );      
      $fp = fopen('php://output', 'w');
      $bottom = array();
      $bottom2 = array();
      foreach ($attributes as $key => $attr) {
        $header[] = ucfirst($attr->title);
        $bottom[] = '';
        $bottom2[] = '';
      }
      $header[] = 'Sku';   
      $bottom[] = '';   
      $bottom2[] = 'Total'; 
      $header[] = 'Quantity';      

      $user_orders_summary  = dokan_get_deal_orders_summary( $seller_id, '', $pid );

      if ($_POST['download_summary'] == 'summaryxls') {
        $filename = $pid."_summary.xlsx";
        header('Content-type: application/vnd-ms-excel');
      }else{
        $filename = $pid."_summary.csv";
        header('Content-type: application/csv');
      }
      header('Content-Disposition: attachment; filename='.$filename);
      fputcsv($fp, $header);

      $total_qty = 0;
      $colspans = 0;
      foreach ($user_orders_summary as $order) {
        $qty = wc_get_order_item_meta( $order->order_item_id, '_qty', true );
        $sku = wc_get_order_item_meta( $order->order_item_id, 'sku', true );
        $product_attributes = wc_get_order_item_meta( $order->order_item_id, 'product_attributes', true );      
        //$colspans = count($product_attributes);
        $total_qty += $qty;
        $res = array();
        if (!empty($product_attributes)) {
          foreach ($product_attributes as $val) {
            $res[] = $val;
          }
        }
        $res[] = $sku;
        $res[] = $qty;
        fputcsv($fp, $res);
      }
      $bottom[] = '';
      $bottom2[] = $total_qty;
      fputcsv($fp, $bottom);
      fputcsv($fp, $bottom2);
      exit();
  }
  //ORDER FULFILLMENT
  if (isset($_POST['order_download_list']) 
    && !empty($_POST['order_download_list']) 
    && $_POST['order_download_list'] == 'order_fulfillment' 
    && isset($_POST['order_download_nonce']) 
    && wp_verify_nonce( $_POST['order_download_nonce'], 'order_download_action')) 
  {
    global $wpdb;
    $pid = sanitize_text_field($_POST['deal_id']);
    $seller_id    = dokan_get_current_user_id();
    $tbl_attributes = $wpdb->prefix."product_attributes"; 
    $attributes = $wpdb->get_results( $wpdb->prepare( "SELECT title
      FROM $tbl_attributes
      WHERE
      product_id = %d ", $pid
    ) );      
    $fp = fopen('php://output', 'w');
	  $orders  = dokan_get_deal_orders( $seller_id, '', $pid );
	  $user_orders_summary  = dokan_get_deal_orders_summary( $seller_id, '', $pid );
	  foreach($user_orders_summary as $OrderObj){
		$product_attributes = wc_get_order_item_meta( $OrderObj->order_item_id, 'product_attributes', true );  
	  
    $header = array();
    $header[] = 'OrderID';
    $header[] = 'Customer Name';
	$header[] = 'City';
    $header[] = 'State / Country';
	$header[] = 'Address 1';
	$header[] = 'Address 2';
	$header[] = 'Zip';
	$header[] = 'Item Name';
	//$header[] = 'Shipping Zip';
    foreach ($attributes as $key => $attr) {
      $header[] = ucfirst($attr->title);
    }
		  $count = 1;
	  foreach ($product_attributes as $key => $val) {
          $header[] = 'Option Title / Value '.$count;
		  $count++;
        }
	$header[] = 'Sku';
    $header[] = 'Quantity';
		  $header[] = 'Regular Price';
		  $header[] = 'Sale Price';
		  $header[] = 'Shipping Price';
		  $header[] = 'Shipping Price Additional Items';
		  foreach ($product_attributes as $key => $val) {
          $header[] = ucfirst($key);
        }
	$header[] = 'Tracking Number'; 
	$header[] = 'Date';
}

    if ($_POST['order_fulfillment'] == 'fulfillmentxls') {
      $filename = $pid."_fulfillment.xlsx";
      header('Content-type: application/vnd-ms-excel');
    }else{
      $filename = $pid."_fulfillment.csv";
      header('Content-type: application/csv');
    }
    header('Content-Disposition: attachment; filename='.$filename);
    fputcsv($fp, $header);

    $total_qty = 0;
    $colspans = 0;
    $order_tracking = $wpdb->prefix."order_tracking";
    foreach ($user_orders_summary as $OrderObj) {
      $res = array();
      $res[] = $OrderObj->order_id;
      $order    = new WC_Order( $OrderObj->order_id );
      $customer_user = absint( get_post_meta( dokan_get_prop( $order, 'id' ), '_customer_user', true ) );
      if ( $customer_user && $customer_user != 0 ) {
          $customer_userdata = get_userdata( $customer_user );
          $display_name =  $customer_userdata->first_name.' '.$customer_userdata->last_name;
      } else {
          $display_name = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_first_name', true ). ' '. get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_last_name', true );
      }

      $res[] = $display_name;
		$billingCity = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_city', true );
		$billingAddress1 = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_address_1', true );
		$billingAddress2 = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_address_2', true );
		$regularPrice = get_post_meta( $pid, '_regular_price', true );
		$SalePrice = get_post_meta( $pid, '_sale_price', true );
		$ShippingPrice = get_post_meta( $pid, '_shipping_price', true );
		$ShippingPriceAdd = get_post_meta( $pid, '_shippingPriceAdditionalItems', true );
		$res[] = $billingCity;
      $res[] = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_state', true ). ' / '. get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_country', true );
		$billingZip = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_postcode', true );
		//$shippingZip = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_postcode', true );
		//$title = get_post_meta( dokan_get_prop( $order, 'id' ), 'order_date', true );
      $qty = wc_get_order_item_meta( $OrderObj->order_item_id, '_qty', true );
      $sku = wc_get_order_item_meta( $OrderObj->order_item_id, 'sku', true );
		$productTitle = get_the_title($pid);
      $product_attributes = wc_get_order_item_meta( $OrderObj->order_item_id, 'product_attributes', true );      
      //$colspans = count($product_attributes);
      $total_qty += $qty;
		$res[] = $billingAddress1;
		$res[] = $billingAddress2;
      $res[] = $billingZip;
		$res[] = $productTitle;
		//$res[] = $shippingZip; 
      if (!empty($product_attributes)) {
        foreach ($product_attributes as $key => $val) {
          $res[] = ucfirst($key).' / '.ucfirst($val);
        }
      }
      $trackingnumber = $wpdb->get_var( $wpdb->prepare( "SELECT trackingnumber FROM {$order_tracking} WHERE order_id = %d", $OrderObj->order_id));   
      $res[] = $sku;
      $res[] = $qty;
		$res[] = $regularPrice;
		$res[] = $SalePrice;
		$res[] = $ShippingPrice;
		$res[] = $ShippingPriceAdd;
		foreach ($product_attributes as $key => $val) {
          $res[] = ucfirst($val);
        }
	  $res[] = $trackingnumber;
      $res[] = $orders[0]->post_date;
		
      fputcsv($fp, $res);
    }
    exit();
    
  }
  //ORDER SHIPPING
  if (isset($_POST['order_download_list']) 
    && !empty($_POST['order_download_list']) 
    && $_POST['order_download_list'] == 'order_shipping' 
    && isset($_POST['order_download_nonce']) 
    && wp_verify_nonce( $_POST['order_download_nonce'], 'order_download_action')) 
  {
    global $wpdb;
    $pid = sanitize_text_field($_POST['deal_id']);
    $seller_id    = dokan_get_current_user_id();
    $user_orders_summary  = dokan_get_deal_orders_summary( $seller_id, '', $pid );
    $fp = fopen('php://output', 'w');
	  foreach($user_orders_summary as $user_ord){
		$product_attributes = wc_get_order_item_meta( $user_ord->order_item_id, 'product_attributes', true );  
	  }
	  
    $header = array();
    $header[] = 'Order Number';
    $header[] = 'Order Date';
    $header[] = 'First Name'; 
    $header[] = 'Last Name';      
    $header[] = 'Address 1';  
    $header[] = 'Address 2';  
    $header[] = 'City';
    $header[] = 'State';
    $header[] = 'Postal Code';
    $header[] = 'Tracking Number';
	$header[] = 'Quantity';
	$header[] = 'Sku';
	$count = 1;
	foreach($product_attributes as $key => $val){
		$header[] = 'Option Title / Value '.$count;
		$count++;
	  }

    $orders  = dokan_get_deal_orders( $seller_id, '', $pid );

    if ($_POST['order_shipping'] == 'shippinglabelsxls') {
      $filename = $pid."_shipping_labels.xlsx";
      header('Content-type: application/vnd-ms-excel');
    }else{
      $filename = $pid."_shipping_labels.csv";
      header('Content-type: application/csv');
    }
    header('Content-Disposition: attachment; filename='.$filename);
    fputcsv($fp, $header);

    $total_qty = 0;
    $colspans = 0;
    $order_tracking = $wpdb->prefix."order_tracking";
    foreach ($orders as $OrderObj) {
      $res = array();
      $res[] = $OrderObj->order_id;
      $res[] = date('m/d/Y', strtotime($OrderObj->post_date));
      $order    = new WC_Order( $OrderObj->order_id );
      $customer_user = absint( get_post_meta( dokan_get_prop( $order, 'id' ), '_customer_user', true ) );
      if ( $customer_user && $customer_user != 0 ) {
          $customer_userdata = get_userdata( $customer_user );
          $res[] = $customer_userdata->first_name;
          $res[] = $customer_userdata->last_name;
      } else {
          $shipping_first_name = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_first_name', true );
          $res[] = !empty($shipping_first_name)?$shipping_first_name : get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_first_name', true );
          $shipping_last_name = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_last_name', true );
          $res[] = !empty($shipping_last_name)?$shipping_last_name : get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_last_name', true );
      }
      $shipping_address_1 = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_address_1', true );
      $res[] = !empty($shipping_address_1)?$shipping_address_1:get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_address_1', true );
      $shipping_address_2 = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_address_2', true );
      $res[] = !empty($shipping_address_2)?$shipping_address_2:get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_address_2', true );
      $shipping_city = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_city', true );
      $res[] = !empty($shipping_city)?$shipping_city:get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_city', true );
      $shipping_state = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_state', true );
      $res[] = !empty($shipping_state)?$shipping_state:get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_state', true );
      $shipping_postcode = get_post_meta( dokan_get_prop( $order, 'id' ), '_shipping_postcode', true );
      $res[] = !empty($shipping_postcode)?$shipping_postcode:get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_postcode', true );  

      $trackingnumber = $wpdb->get_var( $wpdb->prepare( "SELECT trackingnumber FROM {$order_tracking} WHERE order_id = %d", $OrderObj->order_id));   
      $res[] = $trackingnumber;
		foreach($user_orders_summary as $user_ord){
			$qty = wc_get_order_item_meta( $user_ord->order_item_id, '_qty', true );
			$res[] = $qty;
			$sku = wc_get_order_item_meta( $user_ord->order_item_id, 'sku', true );
			$res[] = $sku;
			break;
		}
		foreach($product_attributes as $key => $val){
			//$res[] = ucfirst($key);
			$res[] = ucfirst($key).' / '.ucfirst($val);
		}
      fputcsv($fp, $res);
    }
    exit();
  }

}
add_action( 'wp_ajax_DashboardOrderTracking', 'DashboardOrderTracking' );
function DashboardOrderTracking(){
  check_ajax_referer( 'dokan_reviews', 'nonce' );
  global $wpdb;
  $orderid = sanitize_text_field($_POST['orderid']);
  $trackingnumber = sanitize_text_field($_POST['trackingnumber']);
  $shippingdate = sanitize_text_field($_POST['shippingdate']);
  $carrier = sanitize_text_field($_POST['carrier']);
  $message = '';
  if (!empty($orderid) && !empty($trackingnumber) && !empty($shippingdate) && !empty($carrier)) 
  {
    SaveTrackingData($orderid,$trackingnumber,$carrier,$shippingdate,'manual');
    $message = 'Tracking number added successfully.';
  }else{
    $message = 'All fields are required.';
  }
  echo $message;
  die;
}

function SaveTrackingData($orderid,$trackingnumber,$carrier,$shippingdate,$trackby){
  global $wpdb;
  $order_tracking = $wpdb->prefix."order_tracking";
  $hasorder = $wpdb->get_var( $wpdb->prepare( "SELECT ID
          FROM {$order_tracking}
          WHERE
              order_id = %d 
          ", $orderid
      ) );
  if (empty($hasorder)) {
    if ($trackby == 'manual') {
      $order = wc_get_order( $orderid );
      if (!empty($order)) {
        $order->update_status( 'completed' );
      }      
    }
    $data = array('order_id' => $orderid, 'trackingnumber' => $trackingnumber, 'carrier' => $carrier, 'shippingdate' => date('Y-m-d', strtotime($shippingdate)), 'trackingdate' => date('Y-m-d'));
    $format = array('%d','%d','%s', '%s', '%s');
    $wpdb->insert($order_tracking,$data,$format);  
  }
}

add_action( 'wp_ajax_DashboardUploadTracking', 'DashboardUploadTracking' );
function DashboardUploadTracking(){
  check_ajax_referer( 'dokan_reviews', 'nonce' );
  global $wpdb;

  $orderid = isset($_POST['orderid'])?sanitize_text_field($_POST['orderid']):'';
  $trackingnumber = isset($_POST['trackingnumber'])?sanitize_text_field($_POST['trackingnumber']):'';
  $shipping_date = isset($_POST['shipping_date'])?sanitize_text_field($_POST['shipping_date']):'';
  $carrier = isset($_POST['carrier'])?sanitize_text_field($_POST['carrier']):'';

  $message = array('status' => 'error', 'msg' => '');

  if(is_uploaded_file($_FILES['uploadfile']['tmp_name']))
  {

    $filename = basename($_FILES['uploadfile']['name']);
    $file_parts = pathinfo($filename);
    $extension = $file_parts['extension'];

    if($extension == 'csv'){
      $options = '';
      $tmpfile = $_FILES['uploadfile']['tmp_name'];
      if (($fh = fopen($tmpfile, "r")) !== FALSE) {
        $i = 0;
        if ($orderid == '') {
          $items = fgetcsv($fh, 10000, ",");
          $message['status'] = 'success';
          foreach ($items as $key => $row) {
            $options .= '<option value="'.$key.'">'.$row.'</option>';
          }
          $message['msg'] = $options;
        }else{

          $limit =1; 
          $counter =0; 
          $exitcounter =0; 
          $workingmode='';
          if(isset($_SESSION['upload_starting_row']) && !empty($_SESSION['upload_starting_row'])){
            $upload_starting_row  = $_SESSION['upload_starting_row'];
          }else{
            $upload_starting_row  = 0;
          }
          while (($items = fgetcsv($fh, 10000, ",")) !== FALSE) 
          {
            if($counter > $upload_starting_row && !empty($items[0])){              
              $item_orderid = trim($items[$orderid]);
              $item_shipping_date = trim($items[$shipping_date]);
              $item_trackingnumber = trim($items[$trackingnumber]);
              if (!empty($item_orderid)) {
                SaveTrackingData($item_orderid,$item_trackingnumber,$carrier,$item_shipping_date,'manual');
                $exitcounter++;
                $upload_starting_row++;
                $workingmode= 'working';
                $_SESSION['upload_starting_row'] = $upload_starting_row;
                $message['msg'] = $upload_starting_row;
                $message['status'] = 'working';
              }
            } 
            $counter++;  
            if($exitcounter==$limit && $exitcounter!=''){ break;  } 
          }
          if($workingmode==''){      
            unset($_SESSION['upload_starting_row']);
            $message['msg'] = 'done';
            $message['status'] = 'done';
          }
        }
      }
      fclose($fh);
    }
    else{
      $message['msg'] = 'Invalid file format uploaded. Please upload CSV.';
    }
  }
  else{
    $message['msg'] ='Please upload a CSV file.';
  }
  echo json_encode($message);
  die;
}

//ENTER SHIPPING DATA HERE.
add_action( 'woocommerce_shipstation_shipnotify','WooShipstationSave',30,2);
function WooShipstationSave($order,$trackdata){
    $orderid = $order->get_id();
    $trackingnumber = $trackdata['tracking_number'];
    $carrier = $trackdata['carrier'];
    $shippingdate = date_i18n( get_option( 'date_format' ), $trackdata['ship_date'] );
    $xml = $trackdata['xml'];
    SaveTrackingData($orderid,$trackingnumber,$carrier,$shippingdate,'shipstation');
    //wp_mail('nanchhu@internetbusinesssolutionsindia.com', 'shipstation request', $orderid.'====='.$xml);
}

/* 
----------------------------------
  DASHBOARD LEDGER MENU
------------------------------------
*/
//SAVE LADGER ENTRY HERE.
function SaveLedgerEntry($deal_id,$data,$format){
  global $wpdb;
  $ledger_entry = $wpdb->prefix."ledger_entry";
  $hasentry = $wpdb->get_var( $wpdb->prepare( "SELECT ID
          FROM {$ledger_entry}
          WHERE
            deal_id = %d 
          ", $deal_id
      ) );
  if(!empty($hasentry)){  
   $where = array('ID' => $hasentry);
   $where_format = array('%d');
   $wpdb->update($ledger_entry,$data,$where,$format,$where_format);  
  }else{    
   $wpdb->insert($ledger_entry,$data,$format); 
  }
}
//GET LADGER ENTRY HERE.
function GetLedgerEntry($deal_id){
  global $wpdb;
  $ledger_entry = $wpdb->prefix."ledger_entry";
  return $row = $wpdb->get_row( $wpdb->prepare( "SELECT *
          FROM {$ledger_entry}
          WHERE
            deal_id = %d 
          ", $deal_id
      ) );
}
//PAYMENT COMPLETED HOOK
add_action('woocommerce_order_status_completed', 'SaveLegerEntryPaymentCompleted', 10, 1);
function SaveLegerEntryPaymentCompleted($OrderID){
  $order = wc_get_order( $OrderID );
  $data = array();
  $format = array();
  foreach ($order->get_items() as $item_key => $item)
  {
    $item_id = $item->get_id();
    $product    = $item->get_product();
    $item_name  = $item->get_name();
    $product_id = $item->get_product_id();
    $quantity   = $item->get_quantity();  
    $line_total = $item->get_total();
    $shipping_price = wc_get_order_item_meta($item_id, '_deal_shipping_price', true); 

    $ledger = GetLedgerEntry($product_id);

    $seller_id = $product->post->post_author;
    $sub_total = $line_total;
    $carr = GetAdminCommissionType($seller_id);
    $commission = $carr['commission'];
    $commission_type = $carr['ctype'];
    $total = 0;
    if ('percentage' == $commission_type ) {
      $admin_commission = ($sub_total*$commission/100);
      $total = ($sub_total+$shipping_price)-$admin_commission;
    }else{
      $admin_commission = $commission;
      $total = ($sub_total+$shipping_price)-$admin_commission;
    }

    if (!empty($ledger)){
      $data['amount'] = !empty($ledger->amount)?$line_total+$ledger->amount:$line_total;
      $format = '%f';
      $data['shipping_amount'] = !empty($ledger->shipping_amount)?$shipping_price+$ledger->shipping_amount:$shipping_price;
      $format = '%f';
      $data['seller_commission'] = !empty($ledger->seller_commission)?$total+$ledger->seller_commission:$total;
      $format = '%f';
      $data['admin_commission'] = !empty($ledger->admin_commission)?$admin_commission+$ledger->admin_commission:$admin_commission;
      $format = '%f';
    }else{
      $data['seller_id'] = $seller_id;
      $format = '%d';
      $data['deal_id'] = $product_id;
      $format = '%d';
      $data['deal_titel'] = $item_name;
      $format = '%s';
      $data['entry_type'] = 'SellerCommission';
      $format = '%s';
      $data['amount'] = $line_total;
      $format = '%f';
      $data['shipping_amount'] = $shipping_price;
      $format = '%f';
      $data['seller_commission'] = $total;
      $format = '%f';
      $data['admin_commission'] = $admin_commission;
      $format = '%f';
      $data['admin_percentage'] = $commission;
      $format = '%f';
      $data['commission_type'] = $commission_type;
      $format = '%s';
      $data['created_date'] = date('Y-m-d H:i:s');
      $format = '%s';
    }    
    SaveLedgerEntry($product_id,$data,$format);
  }
  //wp_mail('nanchhu@internetbusinesssolutionsindia.com', 'OrderID', $OrderID);
}
//LEDGER ENTRY TYPES
function GetLedgerTypes($type=''){
  $TypesArr = array('SellerCommission' => 'Commissions', 'PaidSeller' => 'Payments');
  /* $TypesArr = array('PaidSeller' => 'Payments','Refunds' => 'Refunds','SellerCommission' => 'Commissions','RefundPenalty' => 'Non-fulfillment Fees','ManualAdjustment' => 'Manual Adjustments');*/
  if (!empty($type)) {
    return $TypesArr[$type];
  }else{
    return $TypesArr;
  }  
}
//GET SELLER ADMIN COMMISSION 
function GetAdminCommissionType($seller_id){
  $seller_admin_percentage = get_user_meta( $seller_id, 'dokan_admin_percentage', true);
  $seller_admin_percentage_type = get_user_meta( $seller_id, 'dokan_admin_percentage_type',true);

  $options = get_option( 'dokan_selling', array() );
  $gloabl_admin_percentage = ! empty( $options['admin_percentage'] ) ? $options['admin_percentage'] : '';
  $global_commission_type = ! empty( $options['commission_type'] ) ? $options['commission_type'] : 'percentage';

  $commission_arr = array();
  if (!empty($seller_admin_percentage)) {
    $commission_arr['commission'] = $seller_admin_percentage;
  }elseif (!empty($gloabl_admin_percentage)){
    $commission_arr['commission'] = $gloabl_admin_percentage;
  }
  if (!empty($seller_admin_percentage_type)){
    $commission_arr['ctype'] = $seller_admin_percentage_type;
  }elseif (!empty($global_commission_type)){
    $commission_arr['ctype'] = $global_commission_type;
  }
  return $commission_arr;
}

//GET COMMISSION DETAILS.
add_action( 'wp_ajax_lader_commission', 'LaderCommissionDetails' );
function LaderCommissionDetails(){
  check_ajax_referer( 'dokan_reviews', 'nonce' );
  global $wpdb;
  $lid = sanitize_text_field($_POST['lid']);
  $data = array();

  if (!empty($lid))
  {
      $ledger_entry = $wpdb->prefix."ledger_entry";
      $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ledger_entry} WHERE ID = %d", $lid));
      $sold = kc_get_deal_orders($row->deal_id);
      $ldate = date('M d, Y', strtotime($row->created_date));

      $ctype = '';
      if ('percentage' == $row->commission_type ) {
         $ctype = '%';
      }

      $data['content'] = '<div class="lader_name">
               <a target="_blank" href="'.get_permalink($row->deal_id).'">'.$row->deal_titel.'</a>
            </div>
            <table class="table table-rasponsive">
               <tbody>
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
                     <td>Kate&Crew Commission ('.$row->admin_percentage.$ctype.')</td>
                     <td><span class="red">- '.wc_price($row->admin_commission).'</span></td>
                  </tr>
               </tbody>
               <tfoot>
                  <tr>
                     <td>Total Net Revenue</td>
                     <td><span class="green">'.wc_price($row->seller_commission).'</span></td>
                  </tr>
               </tfoot>
           </table>';
    echo json_encode($data);
  }
  die;
}

//GET PAYOUT COMMISSION DETAILS.
add_action( 'wp_ajax_payout_commission', 'LaderPayoutCommissionDetails' );
function LaderPayoutCommissionDetails(){
  check_ajax_referer( 'dokan_reviews', 'nonce' );
  global $wpdb;
  $lid = sanitize_text_field($_POST['lid']);
  $data = array();

  if (!empty($lid))
  {
      $ledger_entry = $wpdb->prefix."ledger_entry";
      $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ledger_entry} WHERE ID = %d", $lid));
      $sold = kc_get_deal_orders($row->deal_id);
      $revenue_date = date('M d, Y', strtotime($row->revenue_date));

      $ctype = '';
      if ('percentage' == $row->commission_type){
         $ctype = '%';
      }

      $data['content'] = '
            <table class="table payout-tbl table-rasponsive">
              <thead>
                  <tr>
                     <td>REVENUE DATE</td>
                     <td>DEAL</td>
                     <td>ITEMS TOTAL</td>
                     <td>SHIPPING TOTAL</td>
                     <td>Kate&Crew COMMISSION</td>
                     <td>TOTAL</td>
                  </tr>
              </thead>
              <tbody>
                  <tr>
                     <td>'.$revenue_date.'</td>
                     <td><a target="_blank" href="'.get_permalink($row->deal_id).'">'.$row->deal_id.'-'.get_the_title($row->deal_id).'</a></td>
                     <td>'.$sold.'</td>
                     <td>'.wc_price($row->shipping_amount).'</td>
                     <td><span class="red">- '.wc_price($row->admin_commission).'</span> ('.$row->admin_percentage.$ctype.')</td>
                     <td>'.wc_price($row->seller_commission).'</td>
                  </tr>
              </tbody>
            </table>
            <table class="table payout-tbl2 table-rasponsive">
               <tbody>
                  <tr>
                     <td>All Items Total</td>
                     <td>'.$sold.'</td>
                  </tr>
                  <tr>
                     <td>All Items Shipping Total</td>
                     <td>'.wc_price($row->shipping_amount).'</td>
                  </tr>
                  <tr>
                     <td>Kate&Crew Commission Total</td>
                     <td><span class="red">- '.wc_price($row->admin_commission).'</span></td>
                  </tr>
               </tbody>
               <tfoot>
                  <tr>
                     <td>Total Payment</td>
                     <td><span class="green">'.wc_price($row->seller_commission).'</span></td>
                  </tr>
               </tfoot>
           </table>';
    echo json_encode($data);
  }
  die;
}

//LEDGER DOWLOAD CSV
add_action( 'init', 'LedgerDownloadCsv' );
function LedgerDownloadCsv(){  
  if (isset($_POST['download_csv']) 
    && !empty($_POST['download_csv']) 
    && $_POST['download_csv'] == 'csv' 
    && isset($_POST['download_ledger_nonce']) 
    && wp_verify_nonce( $_POST['download_ledger_nonce'], 'download_action')) 
  {
      global $wpdb;
      $seller_id    = dokan_get_current_user_id();
           
      $fp = fopen('php://output', 'w');

      $header = array();
      $header[] = 'ledgerEntryId';
      $header[] = 'dealId';
      $header[] = 'dealTitle'; 
      $header[] = 'entryType';      
      $header[] = 'amount';  
      $header[] = 'description';  
      $header[] = 'createdDate';

      $ledger_entry = $wpdb->prefix."ledger_entry";
      $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$ledger_entry} WHERE seller_id = %d ORDER BY ID DESC", $seller_id) );

      $profile_info = dokan_get_store_info( dokan_get_current_user_id() );
      $storename = isset( $profile_info['store_name'] ) ? $profile_info['store_name'] : '';
      $filename = "account-legder-".sanitize_title($storename)."_".date('Y').".csv";
      header('Content-type: application/csv');

      header('Content-Disposition: attachment; filename='.$filename);
      fputcsv($fp, $header);

      $total_qty = 0;
      $colspans = 0;
      foreach ($results as $row) {
        $res = array();
        $res[] = $row->ID;
        $res[] = $row->deal_id;        
        $res[] = $row->deal_titel;
        $res[] = $row->entry_type;
        $res[] = $row->seller_commission;
        $res[] = '';
        $res[] = date('d/m/Y H:i:s A',strtotime($row->created_date));
        fputcsv($fp, $res);
      }
      exit();
  }    
}
//SEARCH ORDER BY ID AND CUSTOMER EMAIL
add_action("wp_ajax_DashboardOrderSearch", "DashboardOrderSearch");
function DashboardOrderSearch(){
  global $wpdb;
  $search_query = sanitize_text_field($_POST['search_query']);

  if (!empty($search_query))
  {
    $seller_id    = dokan_get_current_user_id();
    if (!empty($search_query) && is_numeric($search_query)) { 
      $where = sprintf( " p.ID = %d AND ", $search_query );
    }else{
      $join = " INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id ";
      $where .= sprintf( " pm.meta_key = '_billing_email' AND pm.meta_value='%s' AND ", $search_query);
    }

    $orders = $wpdb->get_results( $wpdb->prepare( "SELECT do.order_id
            FROM {$wpdb->prefix}dokan_orders AS do
            INNER JOIN $wpdb->posts p ON do.order_id = p.ID
            {$join}
            WHERE
                do.seller_id = %d AND
                {$where}
                p.post_status != 'trash'
            GROUP BY do.order_id
            ORDER BY p.post_date DESC
            ", $seller_id
        ) );

    dokan_get_template_part( 'orders/order-search', '', array('orders' => $orders));   
    //echo json_encode($orders);
  }  
  die;
}

//ADD NEW DASHBOARD MENUS
add_filter( 'dokan_query_var_filter', 'dokan_load_query_var', 19 );
function dokan_load_query_var($query_vars){
   // $query_vars[] = 'deal-summary';
    $query_vars[] = 'customer-reviews';
    $query_vars[] = 'calendar';
    //$query_vars[] = 'orders-summary';
    $query_vars[] = 'order-search';
    $query_vars[] = 'fulfillment';
    $query_vars[] = 'payments';
    $query_vars[] = 'tools';
    $query_vars[] = 'integrations';
    return $query_vars;
}
add_action( 'dokan_load_custom_template', 'load_deal_summary_template');
function load_deal_summary_template( $query_vars ) {
  if ( isset( $query_vars['deal-summary'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view deal summary page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'review/deal-summary/deal-summary', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['customer-reviews'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' )) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view review deal page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'review/reviews', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['calendar'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view calender page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'calendar/calendar', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['order-search'] )) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) || (!isset($_GET['order_search']) || empty($_GET['order_search']) )) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view order search page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'orders/search-orders', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['fulfillment'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view order downaload page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'orders/orders-fulfillment', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['payments'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view order downaload page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'payments/payments', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['tools'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view order downaload page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'tools/tools', '', array( 'pro'=>true ) );
      return;
    }
  }
  if ( isset( $query_vars['integrations'] ) ) {
    if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
      dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view order downaload page', 'dokan' ) ) );
      return;
    } else {
      dokan_get_template_part( 'resources/integrations', '', array( 'pro'=>true ) );
      return;
    }
  }
}

//CUSTOMER CHANGE PASSWORD AFTER LOGIN
add_action('woocommerce_save_account_details', 'SaveChangedPasswordData');
function SaveChangedPasswordData($userID){
  if (!empty($userID) && isset($_POST['password_1']) && !empty($_POST['password_1'])) {
    update_user_meta($userID,'has_changed_password',1);
  }
}

//CHANGE CHART QUERY FOR FORNTEND
add_filter( 'woocommerce_reports_get_order_report_query', 'wc_change_reports_get_order_report_query');
function wc_change_reports_get_order_report_query($query){
  //echo $query;
  global $wpdb;
  $user_id = get_current_user_id();
  if (!is_admin()) {
    $query['join'] = $query['join']." INNER JOIN {$wpdb->prefix}dokan_orders AS do ON do.order_id = posts.ID ";
    $query['where'] = $query['where']." AND do.seller_id = $user_id ";
  }
  return $query;
}

//add_action( 'woocommerce_after_my_account', 'AddChangPasswordPopup',89);
//add_action( 'add_change_password_popup', 'AddChangPasswordPopup',89);
function AddChangPasswordPopup(){
  if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $has_changed = get_user_meta($user_id,'has_changed_password',true);
    if (empty($has_changed)){ 
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function(){
        jQuery('#btn-change-pass-popup').click();
      });
    </script>
    <button type="button" data-kcmodal="kcmodal" data-target="#dash-change-pass-popup" id="btn-change-pass-popup" class="button alt hide"></button>

    <div class="kcmodal-wrapper" id="dash-change-pass-popup">
      <div class="kcmodal">
        <button class="closeModalBtn"><i class="fa fa-times" aria-hidden="true"></i></button>
        <div class="kcmodal-header">
          <h3><?php _e( 'Change your password','woocommerce'); ?>?</h3>
        </div>
        <div class="kcmodal-body">
          <p>
            <?php _e( 'Welcome! Because your account was set up with a system generated password, we ask you that please change it.','woocommerce'); ?>. 
          </p>        
        </div>
        <div class="kcmodal-footer">
         <button onclick="window.location.href = '<?php echo site_url('my-account/edit-account'); ?>';"><?php _e('Change Password','woocommerce'); ?></button>
        </div>
      </div>
    </div>
    <?php
    }
  }
}