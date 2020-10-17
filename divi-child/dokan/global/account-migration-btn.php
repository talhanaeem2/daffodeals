<?php
/**
 * Dokan Account Migration Button Template
 *
 * @since 2.4
 *
 * @package dokan
 */

?>

<p>&nbsp;</p>
<style type="text/css">
    
</style>
<ul class="dokan-account-migration-lists">
    <li>
        <div class="dokan-w8 left-content">
            <p><strong><?php _e( 'SELL WITH DAFFODEALS', 'dokan' ) ?></strong></p>
            <p><?php _e( 'Are you interested in having your products featured on Daffodeals.com? We’re always looking for new items and would love to check out your shop! Click the link to the right to fill out our Vendor Application.', 'dokan' ) ?></p>
        </div>
        <div class="dokan-w4 right-content">
            <a href="<?php echo site_url( 'seller-signup'); ?>" class="btn btn-primary"><?php _e( 'Become a Vendor', 'dokan' ); ?></a>
        </div>
        <div class="dokan-clearfix"></div>
    </li>

    <?php do_action( 'dokan_customer_account_migration_list' ); ?>
</ul>
