<?php
/**
 * Dokan Dashboard Product Listing status filter
 * Template
 *
 * @since 2.4
 *
 * @package dokan
 */

global $wp;
?>
<div class="menu-deal">
   <ul>     
     <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'new-product' ) ); ?>" <?php echo isset( $wp->query_vars['new-product'] )? ' class="active"' : ''; ?>>New Deal</a></li>
     <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'pending-product' ) ); ?>" <?php echo isset( $wp->query_vars['pending-product'] )? ' class="active"' : ''; ?>>In Review</a></li>
     <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'scheduled' ) ); ?>" <?php echo isset( $wp->query_vars['scheduled'] )? ' class="active"' : ''; ?>>Scheduled</a></li>
     <li><a href="<?php echo esc_url( dokan_get_navigation_url( 'deals-ended' ) ); ?>" <?php echo isset( $wp->query_vars['deals-ended'] )? ' class="active"' : ''; ?>>Deals Ended</a></li>
   </ul>
</div> 

