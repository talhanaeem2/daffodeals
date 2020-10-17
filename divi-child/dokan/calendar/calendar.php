<?php
global $wpdb;
/**
 *  Dokan Calender Template
 *
 *  Dokan Main Calender template for Fron-end
 *
 *  @since 2.4
 *
 *  @package dokan
 */
$user_id = get_current_user_id();
?>
<?php do_action( 'dokan_dashboard_wrap_start' ); ?>
    <div class="dokan-dashboard-wrap dashboard-calendar <?php echo current_user_can('administrator') ?  'admindash': null; ?>" id="calendar_body">
        <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_before' );
        ?>       
        <div class="dokan-dashboard-content">
            <?php

                /**
                 *  dokan_dashboard_content_before hook
                 *
                 *  @hooked show_seller_dashboard_notice
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_dashboard_content_inside_before' );
            ?>
            <article class="dashboard-content-area">                
                <div class="dokan-w6 dokan-dash-left">
                    <div class="calendar-content">
                        <?php 
                        if ( current_user_can('administrator') ){
                            $calendar = new adminCalendar();
                        }else{ 
                            $calendar = new Calendar();
                        }                     
                        echo $calendar->show();
                        ?>
                    </div>                    
                </div> <!-- .col-md-6 -->
            </article><!-- .dashboard-content-area -->
             <?php

                /**
                 *  dokan_dashboard_content_inside_after hook
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_dashboard_content_inside_after' );
            ?>
        </div><!-- .dokan-dashboard-content -->
        <?php

            /**
             *  dokan_dashboard_content_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_after' );
        ?>       
    </div><!-- .dokan-dashboard-wrap -->
<?php do_action( 'dokan_dashboard_wrap_end' ); ?>