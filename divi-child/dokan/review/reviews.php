<?php
/**
 * Dokan Dahsbarod Review Main Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<?php do_action( 'dokan_dashboard_wrap_start' ); ?>
<div class="dokan-dashboard-wrap" id="dashboard_main1">
    <?php
    /**
    *  dokan_dashboard_content_before hook
    *  dokan_dashboard_settings_store_content_before hook
    *
    *  @hooked get_dashboard_side_navigation
    *
    *  @since 2.4
    */
    do_action( 'dokan_dashboard_content_before' );
    //do_action( 'dokan_dashboard_settings_content_before' );
    do_action( 'dokan_review_content' );
    ?>
</div><!-- .dokan-dashboard-wrap -->
<?php do_action( 'dokan_dashboard_wrap_end' ); ?>