jQuery( document ).ready( function( $ ) {
	
	jQuery('.toggle-row-label').click(function(){
		 jQuery(this).toggleClass('active-row'); 
		 jQuery(this).closest('.field-row').find('.toggle_field_row').toggle('slow');  
	});

	jQuery('.option_remove_media' ).on( 'click', function() {
		current_upload_btn = jQuery(this).closest(".option-image-uploader"); 
		current_upload_btn.find('input[type="url"]').val('');
		current_upload_btn.find(".image-preview").attr( 'src','');
	}); 

	/*jQuery('.wsthem_dt_field').datetimepicker({format:'d-m-Y'}); 

	jQuery('.dep_date_time').datetimepicker({format:'Y-m-d',timepicker:false});  

	jQuery('.wsthem_date_field').datetimepicker({format:'d-m-Y',timepicker:false});  */

	jQuery('#wstheme_sel_all').click(function(){

		var thisvalue = jQuery(this).attr('checked');

		if(thisvalue){
			jQuery('.selected_items').attr('checked','checked');
		}else{
			jQuery('.selected_items').removeAttr('checked');
		}
	 	 
	});
	
	jQuery('#wstheme_del_confirm').click(function(){ 
		var r = confirm("Are you sure you want to delete?.");
		if(r!=true) { 
		   return false;
		}  
	});

});