<?php
if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Dokan Settings Main Template
 *
 * @since 2.4
 *
 * @package dokan
 */

global $wpdb, $wp_query;
$userid = get_current_user_id();
$ledger_entry = $wpdb->prefix."ledger_entry";

$pagenum       = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$posts_per_page = 20;
$start_index = ($pagenum - 1) * $posts_per_page;

$search = '';
if (isset($_GET['TypeFilter']) && !empty($_GET['TypeFilter']) && $_GET['TypeFilter'] != 'all'){
    $search .= " AND entry_type='".trim($_GET['TypeFilter'])."'";
}

$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$ledger_entry}
WHERE seller_id = %d {$search} ORDER BY ID DESC LIMIT $start_index, $posts_per_page", $userid) );

$total_results = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$ledger_entry} WHERE seller_id = %d {$search} ORDER BY ID DESC", $userid) );
$total_pages = $total_results/$posts_per_page;

$balance_data = $wpdb->get_row( $wpdb->prepare( "SELECT sum(l1.seller_commission) as commission, (SELECT sum(l2.seller_commission) FROM {$ledger_entry} l2 WHERE l2.seller_id = %d AND l2.entry_type='PaidSeller') as paid FROM {$ledger_entry} l1 WHERE l1.seller_id = %d AND l1.entry_type='SellerCommission'  ", $userid,$userid) );
$balance = !empty($balance_data->commission)?$balance_data->commission-$balance_data->paid:0;
?>
<?php do_action( 'dokan_dashboard_wrap_start' ); ?>
 <div class="dashboard-ledger">
    <div class="dokan-dashboard-wrap dash_ledger" id="dashboard_main1">
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
            do_action( 'dokan_dashboard_settings_content_before' );
        ?>
         <section class="profile-section">
              <div class="container">
                <div class="payment-block">
                    <?php if(!empty($results)){ ?>
                  <table>
                    <thead>
                      <th>Deal Title</th>
                      <th>Run Dates</th>
                      <th>Amount Earned</th>
                      <th>Paid</th>
                      <th>Date Paid</th>
                    </thead>
                     <tbody>
                      
                      <?php 
                        foreach($results as $row){    
                         $startDate = get_post_meta($row->deal_id, '_startDate', true);
                         $endDate = get_post_meta($row->deal_id, '_endDate', true);                         
                        ?>
                            <tr class="<?php echo $row->entry_type; ?>" data-id="<?php echo $row->ID; ?>" data-date="<?php echo date('M d, Y',strtotime($row->created_date)) ; ?>">
                                <td><?php echo $row->deal_titel; ?></td>
                                <td><?php echo date('m/d/y',strtotime($startDate)).' - '.date('m/d/y',strtotime($endDate)); ?></td>
                                <td>
                                    <div class="paid-toggle"><?php echo wc_price($row->seller_commission); ?> <span><i class="fa fa-chevron-down updown-down" aria-hidden="true"></i></span></div>
                                    <div class="pay-detail">
                                    <p>Product Comission: <span> <?php echo wc_price($row->amount); ?></span></p>
                                    <p>Daffodeals Cut: <span> <?php echo wc_price($row->admin_commission); ?></span></p>
                                    <p>Shipping Collected: <span> <?php echo wc_price($row->shipping_amount); ?></span></p>
                                    </div>
                                </td>
                                <td><?php echo 'No'; ?></td>
                                <td><?php //echo wc_price($row->seller_commission); ?></td>
                            </tr>
                        <?php } ?>
                     </tbody>
                  </table>
                  <?php 
                    if ($total_pages > 1 ) {
                        echo '<div class="pagination-wrap">';
                        $page_links = paginate_links( array(
                            'current'   => $pagenum,
                            'total'     => $total_pages,
                            'base'      => $base_url. '%_%',
                            'format'    => '?pagenum=%#%',
                            'add_args'  => false,
                            'type'      => 'array',
                            'prev_text' => __( '&laquo; Previous', 'dokan-lite' ),
                            'next_text' => __( 'Next &raquo;', 'dokan-lite' )
                        ) );

                        echo '<ul class="pagination"><li>';
                        echo join("</li>\n\t<li>", $page_links ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
                        echo "</li>\n</ul>\n";
                        echo '</div>';
                    }
                    ?>
                    <?php }else{ ?>
                        <p><?php _e( 'No record found', 'dokan-lite' ); ?>.</p>
                    <?php } ?>
                </div>
              </div>
           </section>
    </div><!-- .dokan-dashboard-wrap -->
</div>
<?php do_action( 'dokan_dashboard_wrap_end' ); ?>
<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('.updown-down').click(function(){
      var parentel = jQuery(this).parents('td').find('.pay-detail');
      if (jQuery(this).hasClass( "fa-chevron-down" )) {
        parentel.show();
        jQuery(this).removeClass( "fa-chevron-down" );
        jQuery(this).addClass( "fa-chevron-up" );
      }else{
        parentel.hide();
        jQuery(this).removeClass( "fa-chevron-up" );
        jQuery(this).addClass( "fa-chevron-down" );
      }
    });
  });
</script>