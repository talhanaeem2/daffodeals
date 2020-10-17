<?php
if(!defined('ABSPATH')){die("304 Not Found");} 
$wstheme_options = get_wstheme_options();
$settings = array('textarea_rows'=> 15);
?>
<input type="hidden" name="optin_location" value="email-setting">
<div class="general-setting-panel">

    <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Add Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[add_deal_email_sub]" value="<?php echo @$wstheme_options['add_deal_email_sub']; ?>">  
         <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[add_deal_email_head]" value="<?php echo @$wstheme_options['add_deal_email_head']; ?>">
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
         <?php 
            $editor_id = 'add_deal_email_temp';
            $settings['textarea_name'] = 'wstheme_options[add_deal_email_temp]'; 
            $content =  @$wstheme_options['add_deal_email_temp'];   
            wp_editor( $content, $editor_id, $settings );             
            ?>
         <strong><?php _e('Note:You can put this "[deal_title],[deal_edit_url]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Rebook Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[rebook_email_sub]" value="<?php echo @$wstheme_options['rebook_email_sub']; ?>">  
         <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[rebook_email_head]" value="<?php echo @$wstheme_options['rebook_email_head']; ?>">
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
         <?php 
            $editor_id = 'rebook_email_temp'; 
            $settings['textarea_name'] = 'wstheme_options[rebook_email_temp]'; 
            $content =  @$wstheme_options['rebook_email_temp'];   
            wp_editor( $content, $editor_id, $settings );             
            ?>
         <strong><?php _e('Note:You can put this "[deal_title],[deal_edit_url]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

  <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Proposal Changes Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[proposal_email_sub]" value="<?php echo @$wstheme_options['proposal_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[proposal_email_heading]" value="<?php echo @$wstheme_options['proposal_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
         <?php 
            $editor_id = 'proposal_email_temp'; 
            $settings['textarea_name'] = 'wstheme_options[proposal_email_temp]'; 
            $content =  @$wstheme_options['proposal_email_temp'];   
            wp_editor( $content, $editor_id, $settings ); 
            ?>
         <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_start_date],[finalize_date]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Upcoming Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[upcoming_deal_email_sub]" value="<?php echo @$wstheme_options['upcoming_deal_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[upcoming_deal_email_heading]" value="<?php echo @$wstheme_options['upcoming_deal_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'upcoming_deal_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[upcoming_deal_email_temp]'; 
        $content =  @$wstheme_options['upcoming_deal_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
         <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_live_url],[deal_start_date]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Active Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[active_deal_email_sub]" value="<?php echo @$wstheme_options['active_deal_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[active_deal_email_heading]" value="<?php echo @$wstheme_options['active_deal_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'active_deal_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[active_deal_email_temp]'; 
        $content =  @$wstheme_options['active_deal_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
         <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_live_url],[deal_start_date]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Shipping Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[shipping_deal_email_sub]" value="<?php echo @$wstheme_options['shipping_deal_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[shipping_deal_email_heading]" value="<?php echo @$wstheme_options['shipping_deal_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'shipping_deal_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[shipping_deal_email_temp]'; 
        $content =  @$wstheme_options['shipping_deal_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
         <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_live_url],[deal_start_date]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Ended Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[ended_deal_email_sub]" value="<?php echo @$wstheme_options['ended_deal_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[ended_deal_email_heading]" value="<?php echo @$wstheme_options['ended_deal_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'ended_deal_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[ended_deal_email_temp]'; 
        $content =  @$wstheme_options['ended_deal_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
         <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_live_url],[deal_start_date]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Declined Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[declined_deal_email_sub]" value="<?php echo @$wstheme_options['declined_deal_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[declined_deal_email_heading]" value="<?php echo @$wstheme_options['declined_deal_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'declined_deal_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[declined_deal_email_temp]'; 
        $content =  @$wstheme_options['declined_deal_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
         <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[declined_msg]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   
   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Vendor Deal Changes','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vdc_email_sub]" value="<?php echo @$wstheme_options['vdc_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vdc_email_heading]" value="<?php echo @$wstheme_options['vdc_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'vdc_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[vdc_email_temp]'; 
        $content =  @$wstheme_options['vdc_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_step],[deal_info]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Vendor Failed to Finalize Deal Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vffd_email_sub]" value="<?php echo @$wstheme_options['vffd_email_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vffd_email_heading]" value="<?php echo @$wstheme_options['vffd_email_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'vffd_email_temp'; 
        $settings['textarea_name'] = 'wstheme_options[vffd_email_temp]'; 
        $content =  @$wstheme_options['vffd_email_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_start_date],[finalize_date]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   

  <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Vendor Approve Account Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vendor_approved_sub]" value="<?php echo @$wstheme_options['vendor_approved_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vendor_approved_heading]" value="<?php echo @$wstheme_options['vendor_approved_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'vendor_approved_temp'; 
        $settings['textarea_name'] = 'wstheme_options[vendor_approved_temp]'; 
        $content =  @$wstheme_options['vendor_approved_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[vendor_name],[vendor_username],[vendor_password],[verification_link],[site_name]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Vendor New Customer Review Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[customer_review_sub]" value="<?php echo @$wstheme_options['customer_review_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[customer_review_heading]" value="<?php echo @$wstheme_options['customer_review_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'customer_review_temp'; 
        $settings['textarea_name'] = 'wstheme_options[customer_review_temp]'; 
        $content =  @$wstheme_options['customer_review_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[vendor_name],[deal_name],[customer_review_link],[site_name]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Order Cancelled to Customer Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[customer_cancelled_sub]" value="<?php echo @$wstheme_options['customer_cancelled_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[customer_cancelled_heading]" value="<?php echo @$wstheme_options['customer_cancelled_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'customer_cancelled_temp'; 
        $settings['textarea_name'] = 'wstheme_options[customer_cancelled_temp]'; 
        $content =  @$wstheme_options['customer_cancelled_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[customer_name],[order_number],[site_name]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Order Cancelled to Vendor Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vendor_cancelled_sub]" value="<?php echo @$wstheme_options['vendor_cancelled_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[vendor_cancelled_heading]" value="<?php echo @$wstheme_options['vendor_cancelled_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'vendor_cancelled_temp'; 
        $settings['textarea_name'] = 'wstheme_options[vendor_cancelled_temp]'; 
        $content =  @$wstheme_options['vendor_cancelled_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[vendor_name],[order_number],[site_name]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Reminder Ship Orders to Vendor Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[reminder_ship_order_sub]" value="<?php echo @$wstheme_options['reminder_ship_order_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[reminder_ship_order_heading]" value="<?php echo @$wstheme_options['reminder_ship_order_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'reminder_ship_order_temp'; 
        $settings['textarea_name'] = 'wstheme_options[reminder_ship_order_temp]'; 
        $content =  @$wstheme_options['reminder_ship_order_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[vendor_name],[site_name],[deal_title],[order_url]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Reminder Finalize Deal to Vendor Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[reminder_finalize_deal_sub]" value="<?php echo @$wstheme_options['reminder_finalize_deal_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[reminder_finalize_deal_heading]" value="<?php echo @$wstheme_options['reminder_finalize_deal_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'reminder_finalize_deal_temp'; 
        $settings['textarea_name'] = 'wstheme_options[reminder_finalize_deal_temp]'; 
        $content =  @$wstheme_options['reminder_finalize_deal_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[deal_edit_url],[deal_start_date],[finalize_date],[site_name]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Become a Vendor to admin Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[become_vendor_sub]" value="<?php echo @$wstheme_options['become_vendor_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[become_vendor_heading]" value="<?php echo @$wstheme_options['become_vendor_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'become_vendor_temp'; 
        $settings['textarea_name'] = 'wstheme_options[become_vendor_temp]'; 
        $content =  @$wstheme_options['become_vendor_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[customer_name],[customer_email],[profile_url],[company_name],[site_name]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

   <div class="field-row">
      <label class="toggle-row-label"><?php _e( 'Paid Deal to Vendor Email','wstheme'); ?></label>  
      <div class="toggle_field_row">
         <label><?php _e( 'Email Subject','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[paid_deal_sub]" value="<?php echo @$wstheme_options['paid_deal_sub']; ?>">  
          <label><?php _e( 'Email Heading','wstheme'); ?></label>  
         <input type="text" name="wstheme_options[paid_deal_heading]" value="<?php echo @$wstheme_options['paid_deal_heading']; ?>">  
         <label><?php _e( 'Email Temlate','wstheme'); ?></label>  
        <?php 
        $editor_id = 'paid_deal_temp'; 
        $settings['textarea_name'] = 'wstheme_options[paid_deal_temp]'; 
        $content =  @$wstheme_options['paid_deal_temp'];   
        wp_editor( $content, $editor_id, $settings ); 
        ?>
        <strong><?php _e('Note:You can put this "[deal_title],[vendor_name],[total_commission]" code to send dynamic values in email template.','wstheme');?></strong> 
         <div class="clear"></div>
      </div>
   </div>

</div>