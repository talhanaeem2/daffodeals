$ = jQuery.noConflict();
jQuery(document).ready(function(){
// 	https://kateandcrew.com/wp-admin/js/image-edit.min.js?1601423919&#038;ver=5.5.1
	setTimeout(function(){
// 		console.log( $( '#image-edit-js' ).attr('src') );
		$( '#image-edit-js' ).removeAttr('src');
		$( '#image-edit-js' ).attr('src', 'https://daffodeals.com/wp-admin/js/image-edit.min.js' );
		console.log( $( '#image-edit-js' ).attr('src', ) );
	},1000);
// 	var old_ = $( '#image-edit-js' ).attr( 'src', 'http://daffodeals.com/wp-admin/js/image-edit.min.js');

		// DEAL NAV TABS
	jQuery('.nav-tabs li a').click(function (e) {
        e.preventDefault()
        if (jQuery(this).parent().hasClass("disabled")) {
        }else{
            jQuery('#dealTabs li').removeClass('active');
            jQuery('.tab-pane').addClass('hide');    
            var target = jQuery(this).attr('href');
            jQuery('#'+target).removeClass('hide');
            jQuery(this).parent().addClass('active');
            if (target == 'details' || target == 'images') {
            	$('.btn-see-dd').attr('disabled','disabled');
            	$('.btn-see-dd').addClass('disabled');
            	$('.btn-see-dd').removeAttr('title');
            }else if (target == 'options' || target == 'inventory') {
            	$('.btn-see-dd').removeAttr('disabled');
            	$('.btn-see-dd').attr('title','Preview Form');
            	$('.btn-see-dd').removeClass('disabled');
            }
        }
    });

	$startdate = $('#ships-date');
	if ($startdate.length > 0) {
		$startdate = $('#ships-date').data('startdate');
		$enddate = $('#ships-date').data('enddate');
		jQuery("#ships-date" ).datepicker({firstDay: 0, dateFormat:'mm/dd/yy', minDate: new Date($startdate), maxDate:new Date($enddate), dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']});
	}	

	//HOUSEHOLD LIMIT
    jQuery('#limit_per_household').change(function (e) {
        var limit = jQuery(this).val();
        if (limit == 'limited') {            
            jQuery('#per_household_quantity_limit_main').removeClass('hide');
        }else{
            jQuery('#per_household_quantity_limit_main').addClass('hide');            
            jQuery('#per_household_quantity_limit').val('');            
        }
    });    

    //EIDT FORM SUBMIT
    jQuery('#btn-deal-update').click(function (e) {
        jQuery('#frm-edit-deal').submit();
    });

     //BEAUTIFY OK FORM SUBMIT
    jQuery('.btn-finalized').click(function (e) {
    	var errors = false;
    	
        jQuery('.editreqfields').each(function () {
            if (jQuery(this).val() == '') {
               jQuery(this).css('border', '1px solid red');
                errors = true;
            }else{
                jQuery(this).css('border', '1px solid #d8d8d8');
            }
        });
        
        if (jQuery('#product_cat').val() == '-1') {
            jQuery('.select2').css('border', '1px solid red');
            errors = true;
        }else{
            jQuery('.select2').css('border', '1px solid #d8d8d8');
        }
        

        if (errors == false) {
        	var personalization = '';
        	var total_qty = jQuery('#total_qty').val();
        	
        	jQuery('.option-personalization').each(function(){
    			if ($(this).prop("checked") == false) {
    				personalization = 1;
    			}
    		});
			
        	if (personalization == 1) {
        		var total_input_qty = 0;
        		var check_options = jQuery('.option-qty').length;
        		if (check_options != 0) {
        			jQuery('.option-qty').each(function(){
	        			var selfval = jQuery.trim(jQuery(this).val());
						console.log(selfval);
	        			if (selfval != '') {
	        				total_input_qty += parseInt(selfval);
	        			}
	        		});
        		}
				var selfval = jQuery.trim(jQuery('.option-qty').val());
        		//console.log(total_qty+'===='+total_input_qty);
        		console.log(check_options);
        		console.log(total_input_qty);
				console.log(total_qty);
// 				if(jQuery('.total-quantity').is(":hidden")){
// 				   if(selfval == '∞'){
// 					  jQuery('#frm-edit-deal').submit();
// 					  }
// 				   }
        		if (total_input_qty != parseInt(total_qty) && check_options != 0) {
        			alert('Please check your inventory in the INVENTORY tab to make sure it matches the total inventory amount in the DETAILS tab of this deal.');
        		}else{
        			jQuery('#frm-edit-deal').submit();
        		}
        	}else{
        		jQuery('#frm-edit-deal').submit();
        	}
        }else{
        	alert('You have missed some required fields. So please fill all required fields.');
        }
    });

    //REMOVE DEAL MESSAGE
    jQuery('.remove-deal-message').click(function (e) {
    	$(this).closest('.deal_message').hide();
    });
    

	/*
	* ADD OPTION BUTTON.
	*/
	jQuery('#deal-add-option-btn').click(function(){
		//alert('sfdsfdsf');			
		jQuery('#options .deal-note-options').hide();
		jQuery('#inventory .deal-note-options').hide();

		jQuery('#options .options_details').removeClass('hide');
	});


	/*
	* ADD OPTION BUTTON INVENTORY TAB.
	*/
	jQuery('#btn-iv-deal-add-option, #btn-iv-deal-add-option-val').click(function(){	
		jQuery('#deal-add-option-btn').click();		
		jQuery('#dealTabs li').removeClass('active');
        jQuery('.tab-pane').addClass('hide'); 
        jQuery('#options').removeClass('hide');
        jQuery('a[href="options"]').parent().addClass('active');
        jQuery(this).closest('.deal-note-options').show();
	});
	

	/*
	* ADD MORE OPTIONS HERE.
	*/
	jQuery('.addmore_link').click(function(){
		var elm = $(this);
		jQuery('.add_option_list li').removeClass('activenow');


		$li_lenght = jQuery('.add_option_list li').length;
		$li_lenght = $li_lenght+1;

		//ADD OPTION IN LEFT PART OF OPTIONS TAB 
		jQuery('.add_option_list li').each(function(i){
			$index = i+1;
			$(this).attr('data-o_leftid',$index);
		});
		jQuery('.add_option_list').append('<li data-o_leftid="'+$li_lenght+'" class="activenow"><i class="fa fa-arrows-v sortableli" aria-hidden="true"></i> <a class="" href="javascript:void(0)">[New Option]</a></li>');

		//ADD OPTION IN RIGHT PART OF OPTIONS TAB 
		jQuery('.option_right_part ul.option-list li').each(function(i){
			$index = i+1;
			$(this).attr('data-o_rightid',$index);
		});
		var total_qty = jQuery('#total_qty').val();

		var optionhtml = '<div class="dokan-form-group"> <div class="label_ques"> <label>Option Title</label> <input type="text" class="form-control option-title" name="options[title][]" autocomplete="off" placeholder="Example: Size"> <span class="help-block">Title to describe this option such as Size, Color or Style.</span> </div> <div class="dokan-form-group variations-area"> <div class="label_ques"> <label>Option Values</label> </div> <div class="sort-option-values-main"> <div class="sort-option-values ui-sortable"> </div> <input type="text" class="form-control input-options" placeholder="Example: Small 0-4"> </div> <span class="help-block">Enter option values (e.g. Small, Medium , Large) separated by a comma.</span>  <div class="clar_data"> <a class="btn-remove-variation" href="javascript:void(0)"><i class="fa fa-trash-o"></i>Delete option values</a> </div> </div> <div class="dokan-form-group"> <div class="label_ques"> <div class="chek_box"> <input type="hidden" value="no" name="options[personalization][]" class="form-control"> <input type="checkbox" id="adition_info1" value="yes" name="options[personalization][]" class="option-personalization"> <label for="adition_info1">This option is a Personalization.</label> </div>  </div> <input type="number" class="form-control option-character" name="options[char_allowed][]" placeholder=""> <span class="help-block">Maximum characters allowed.</span> </div>';			
		//var optionhtml = jQuery('#option-html').html();			
		jQuery('.option_right_part ul.option-list li').addClass('hide');	
		jQuery('.option_right_part ul.option-list').append('<li data-o_rightid="'+$li_lenght+'" class="deal-options">'+optionhtml+'<div class="dokan-form-group remove-option-main"><button type="button" class="btn btn-remove-option"><i class="fa fa-trash" aria-hidden="true"></i> Remove Option </button></div></li>');


		//INVENTORY TAB 

		//ADD OPTION IN LEFT PART INVENTORY TAB 
		//jQuery('.iv_option_left_list li').removeClass('activenow');
		jQuery('.iv_option_left_list li').each(function(i){
			$index = i+1;
			$(this).attr('data-iv_leftid',$index);
		});
		jQuery('.iv_option_left_list').append('<li data-iv_leftid="'+$li_lenght+'" class="hide"><a class="" href="javascript:void(0)">[New Option]</a></li>');

		
		//ADD OPTION IN RIGHT PART INVENTORY TAB 
		jQuery('.iv_option_right_list li').each(function(i){
			$index = i+1;
			$(this).attr('data-iv_rightid',$index);
		});

		

		var optionhtml2 = '<div class="table-responsive"><table class="table"> <thead> <tr> <th class="invat_thstart inv-option-title"> <div class="inv-variation-titles"> <span></span> <a href="javascript:void(0)" class="btn btn-option-combine"><i class="fa fa-link" aria-hidden="true"></i>Combine</a> </div> <input type="hidden" class="inv-combine-input hide" name="combination[]" value="no"> </th> <th> CUSTOM SKU (OPTIONAL)</th> <th class="invat_thend"> <span>QTY</span> <a href="javascript:void(0)" class="btn btn-inve-unlimited hide">unlimited</a> <input type="hidden" class="inv-limited-input hide" name="limited[]" value="unlimited"> </th> </tr> </thead> <tbody> </tbody> <tfoot> <tr> <td></td><td class="invent_total">Total:</td><td><span class="total-quantity">0<span> / '+total_qty+'</span></span><span class="inventory-unlimited hide">∞<span></td> </tr> </tfoot> </table></div>';	
		
		//jQuery('.option_right_part ul.inventory-option-list li').addClass('hide');	
		jQuery('.option_right_part ul.inventory-option-list').append('<li data-iv_rightid="'+$li_lenght+'" class="deal-options hide newopion">'+optionhtml2+'</li>');
		//jQuery('.option_right_part ul.inventory-option-list li.newopion').find('tbody').find('tr').remove();

		//jQuery('.option_right_part .deal-options:visible').find('div.values-list').remove(); 

		sort_option_values();

	});

	//CALCULATION OF LBS AND OZ FIELDS HERE.
	jQuery('#shipping-weight-lbs').focusout(function(){
        $val = $(this);
        if (jQuery.trim($val.val()) == '') {  $val.val(0); }
    });

    jQuery('#shipping-weight-oz').focusout(function(){
        $obj = $(this);
        //alert('dffds');
        if (jQuery.trim($obj.val()) == '') 
        {  
            $obj.val(0); 
            jQuery('#shipping-weight-lbs').val(0);
        }else{
            $val = parseInt($obj.val());
            //console.log($val);
            $oz = $val%16;
            $lbs = $val/16;
            /*//$lbs = Math.round($lbs);
            console.log($lbs);
            console.log($oz);*/
            $lbsval = jQuery('#shipping-weight-lbs').val();
            $lastlbs = 0;
            if (jQuery.trim($lbsval) != '') 
            {  
               $lastlbs = parseInt($lbsval);
            }
            //console.log($lastlbs);
            if ($lbs >= 0) {
                $lbs = parseInt($lbs) + parseInt($lastlbs);
                jQuery('#shipping-weight-lbs').val($lbs);
            }
            //$oz =  Math.round($oz);
            //console.log($pending);
            if ($oz >= 0) {$obj.val($oz);}
            
        }
    });

});

//TABING OPTIONS
jQuery('.ptabs').on('click',function(e){
   e.preventDefault();  
   var shref = jQuery(this).attr('targetid');   
   jQuery('.ptabs').removeClass('active');
   jQuery(this).addClass('active');
   jQuery('.deal-tab-section').hide();
   jQuery(shref).show();
});

// REMOVE OPTION ALL VALUES HERE.
jQuery(document).on('click', '.btn-remove-variation', function(){
	jQuery(this).closest('li').find('div.values-list').remove(); 
	
	jQuery(this).parents('li').find('.sort-option-values-main .option-values-list').remove();
// 	jQuery(this).parents('li').find('.sort-option-values-main').find('.option-values-list').each(function(ii){
// 		jQuery(this).remove();
// 	});
});

// HIDE VARIONSTION AREA.
jQuery(document).on('click', 'input.option-personalization', function(){
	$id = jQuery(this).parents('li').data('o_rightid');
	if (jQuery(this).is(":checked")) {
		jQuery(this).closest('li').find('div.variations-area').hide(); 
		jQuery(this).closest('li').find('div.repeatTimes_field').hide(); 
		jQuery(this).prev().attr('name', '');
		jQuery(this).parents('li').find('.values-list').remove();
		jQuery('[data-iv_rightid="'+$id+'"]').find('table').hide();
		jQuery('[data-iv_rightid="'+$id+'"]').find('table').find('tbody').find('tr').remove();
		jQuery('[data-iv_leftid="'+$id+'"]').hide();
	}else{
		jQuery(this).closest('li').find('div.variations-area').show(); 
		jQuery(this).closest('li').find('div.repeatTimes_field').show();
		jQuery(this).parents('li').find('.inventory-list').show();
		jQuery('[data-iv_leftid="'+$id+'"]').show();
		jQuery(this).prev().attr('name', 'options[personalization][]');
	}
});


//OPTION REPEAT TIMES ACTION.
jQuery(document).on('click', 'a.repeatTimes_action', function(){
	jQuery(this).closest('li').find('div.repeatTimes').removeClass('hide');	
	jQuery(this).closest('li').find('div.repeatTimes_field').addClass('hide');	
});


//ENTER OPTION TITLE IN LEFT PART
jQuery(document).on('keyup', 'input.option-title', function(){
	var optitle = jQuery(this).val();
	var thisindex = jQuery(this).closest('li').index();
	//console.log(thisindex);		

	$option_li = $('.add_option_list li').eq(thisindex);
	$id = jQuery(this).closest('li').attr('data-o_rightid');
	
	if (jQuery.trim(optitle) != '') 
	{
		$option_li.find('a').text(optitle);
		jQuery('#btn-iv-deal-add-option').parent().parent('.deal-note-options').hide();				
		jQuery('.iv-deal-not-option-values').removeClass('hide');

		jQuery('.option_right_part .option-list li').each(function(i){
	    	//console.log(i);
	    	var titleval = jQuery(this).closest('li').find('input.option-title').val();
	    	$title_slug = creat_option_title_slug(titleval);
	    	jQuery(this).find('.variation-list-input').attr('name', 'options[values]['+$title_slug+'][]');
	    });
		
	}else{
		$option_li.find('a').text('[New Option]');
	}
	//console.log($id);
	$('.iv_option_left_list').find('li').each(function(){
		$thisid = $(this).attr('data-iv_leftid');		
		if ($thisid  == $id) {
			if (jQuery.trim(optitle) != '') 
			{
				$(this).find('a').text(optitle);
				$(this).removeClass('hide');
			}else{
				$(this).addClass('hide');				
			}
		}
	});
	$('.iv_option_right_list').find('li').each(function(){
		$thisid = $(this).attr('data-iv_rightid');
		if ($thisid  == $id) {
			if (jQuery.trim(optitle) != '') 
			{
				$(this).find('.inv-option-title').find('span').text(optitle);
				$title_slug = creat_option_title_slug(optitle);
		    	jQuery(this).find('input.option-sku').attr('name', 'options[sku]['+$title_slug+'][]');
		    	jQuery(this).find('input.option-qty').attr('name', 'options[qty]['+$title_slug+'][]');
		    	jQuery(this).find('input.variation-title').attr('name', 'options[variation_title]['+$title_slug+'][]');		    	
		    	jQuery(this).find('input.inv-combine-input').attr('name', 'options[combine]['+$title_slug+']');
		    	jQuery(this).find('input.inv-limited-input').attr('name', 'options[limited]['+$title_slug+']');
			}else{
				$(this).addClass('hide');				
			}
		}
	});

	//CHANGE VARIATIONS NAME AFTER OPTION NAME.
    //change_name_of_variations();

});

//REMOVE OPTION 
jQuery(document).on('click', '.btn-remove-option', function(e){
	e.preventDefault();	
	var delconfirm = confirm('Are you sure, you want to delete this option?');
	if (delconfirm == true) {
		$id = jQuery(this).parents('li').attr('data-o_rightid');
		jQuery(this).parents('li').remove();
		jQuery("[data-o_leftid="+$id+"]").remove();
		jQuery("[data-iv_leftid="+$id+"]").remove();
		jQuery("[data-iv_rightid="+$id+"]").remove();
	}

});

//CREATE OPTION VARIATIONS HERE
jQuery(document).on('keyup focusout', 'input.input-options', function(){
	var opval = jQuery(this).val();
	var titleval = jQuery(this).parents('li').find('input.option-title').val();
	var li_ID = jQuery(this).parents('li').attr('data-o_rightid');
	if (jQuery.trim(opval) != '') {
		var strs;
		if( opval.indexOf(',') != -1 ){
			strs = opval.split(',');
			if (jQuery.trim(strs[0]) != '') {
				$title_slug = creat_option_title_slug(titleval);
				jQuery(this).prev('div.sort-option-values').append('<div class="values-list"><input type="hidden" class="variation-list-input" name="options[values]['+$title_slug+'][]" value="'+strs[0]+'"><span><span class="valtext">'+strs[0]+'</span><i class="fa fa-times del-option-val" aria-hidden="true"></i></span></div>');

				jQuery('[data-iv_rightid="'+li_ID+'"]').find('#tbl-inv-list').show();
				jQuery('[data-iv_rightid="'+li_ID+'"]').find('tbody').append('<tr><td><span>'+strs[0]+'</span><input type="hidden" class="variation-title hide" value="'+strs[0]+'" name="options[variation_title]['+$title_slug+'][]" ></td><td><input type="text" class="option-sku" name="options[sku]['+$title_slug+'][]" ></td><td><input class="option-qty" type="number" name="options[qty]['+$title_slug+'][]" min="0" value="0"><div class="inventory-unlimited hide">∞</div></td></tr>');
			}
			jQuery(this).val('');			
		}
	}
});

//DELETE OPTOIN VALUE ONE BY ONE
jQuery(document).on('click', '.del-option-val', function(){
	var current_li = jQuery(this).parents('li');
	var div_index = jQuery(this).parents('.values-list').index();
	$id = jQuery(this).parents('li').attr('data-o_rightid');
	var titleval = current_li.find('input.option-title').val();
	//console.log(titleval);
	div_index = parseInt(div_index);
	console.log(div_index);
	jQuery('[data-iv_rightid="'+$id+'"]').find('.table').find('tbody').find('tr').each(function(ii){
		console.log(div_index+'=='+ii);
		if (div_index == ii) {
			console.log(ii);
			jQuery(this).remove();
		}
	});
	jQuery(this).closest('div.values-list').remove();	
});

//SHOW OPTOIN INPUT FIELD HERE
jQuery(document).on('dblclick', 'div.values-list', function(){
	jQuery('div.values-list').each(function(){
		jQuery(this).find('span').show();
		jQuery(this).find('input').attr('type', 'hidden');
	})
	jQuery(this).find('span').hide();
	jQuery(this).find('input').attr('type', 'text');
});

//EDIT OPTOIN INPUT VALUE ONE BY ONE
jQuery(document).on('keyup', '.variation-list-input', function(){	
	var current_li = jQuery(this).parents('li');
	$id = jQuery(this).closest('li').data('o_rightid');
	var titleval = current_li.find('.option-title').val();
	var div_index = jQuery(this).parent('.values-list').index();
	var valtext = jQuery(this).val();
	jQuery(this).parent('.values-list').find('.valtext').text(valtext);
	jQuery(this).find('input').attr('type', 'text');
	div_index = parseInt(div_index);
	jQuery('[data-iv_rightid="'+$id+'"]').find('.table').find('tbody').find('tr').each(function(ii){
		if (div_index == ii) {
			console.log(ii);
			jQuery(this).find('td:first-child').find('span').text(valtext);
			jQuery(this).find('td:first-child').find('input.variation-title').val(valtext);
		}
	});
});

//CLOSE OPTOIN AFTER EDIT ONE BY ONE
jQuery(document).on('focusout', 'input.variation-list-input', function(){
	jQuery('div.values-list').each(function(){
		jQuery(this).find('span').show();
		jQuery(this).find('input').attr('type', 'hidden');
		jQuery(this).val();
		console.log(jQuery(this).val());
	})
});


function creat_option_title_slug(str) {
	var $slug = '';
	var trimmed = $.trim(str);
	$slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
	replace(/-+/g, '-').
	replace(/^-|-$/g, '');
	return $slug.toLowerCase();
}

//OPTION ENABLE RIGHT PART OPTION ON CLICK LEFT PART
jQuery(document).on('click', '.add_option_list li', function(){
	var currentindex = jQuery('.add_option_list li').index( this );
	//console.log(currentindex);
	jQuery('.add_option_list li').removeClass('activenow');
	jQuery(this).addClass('activenow');
	jQuery('.option_right_part ul.option-list li').each(function(i){
		//console.log('sss='+i);
		if (currentindex == i) {				
			jQuery(this).removeClass('hide');
		}else{
			jQuery(this).addClass('hide');
		}
	})
});

//INVENTORY ENABLE RIGHT PART OPTION ON CLICK LEFT PART
jQuery(document).on('click', '.iv_option_left_list li', function(){
	var dataID = jQuery(this).attr( 'data-iv_leftid' );
	jQuery('.iv_option_left_list li').removeClass('activenow');
	jQuery(this).addClass('activenow');
	jQuery('.option_right_part ul.iv_option_right_list li').addClass('hide');
	jQuery('[data-iv_rightid="'+dataID+'"]').removeClass('hide');	
});

//HIDE INPUT ON UNLIMITED BUTTON IN INVENTORY TAB
jQuery(document).on('click', '.btn-inve-unlimited', function(){
	$btn = $(this);	
	$titel = jQuery(this).closest('li').find('div.inv-variation-titles').find('span').text();
	$combinations = jQuery(this).closest('li').find('input.combinations').length;
	if (jQuery(this).closest('li').find('div.inventory-unlimited').hasClass( "hide" )) {
		jQuery(this).closest('li').find('tbody').find('input').attr('type', 'hidden');
		jQuery(this).closest('li').find('tbody').find('input.option-qty').val(0);
		jQuery(this).closest('li').find('tbody').find('input.option-qty').change();
		jQuery(this).closest('li').find('span.total-quantity').hide();
		jQuery(this).closest('li').find('.inventory-unlimited').removeClass('hide');
		jQuery(this).closest('li').find('a.btn-option-combine').addClass('hide');
		if ($combinations == 1) {
			jQuery(this).closest('li').find('input.combinations').remove();	
		}	
		jQuery(this).closest('li').find('input.inv-limited-input').val('limited');	
		jQuery(this).closest('li').find('div.inventory-updater').find('div.form-group').addClass('hide');;	
		jQuery(this).closest('li').find('div.inventory-updater').find('div.form-group:first-child').removeClass('hide');;	

		$btn.text('LIMITED');
		
	}else{	console.log($titel);
		  console.log($combinations);
		jQuery(this).closest('li').find('tbody').find('input').attr('type', 'text');
		jQuery(this).closest('li').find('span.total-quantity').show();
		jQuery(this).closest('li').find('.inventory-unlimited').addClass('hide');
		//jQuery(this).closest('li').find('a.btn-option-combine').removeClass('hide');		
		jQuery(this).closest('li').find('input.inv-limited-input').val('unlimited');
		jQuery(this).closest('li').find('div.inventory-updater').find('select').removeAttr('disabled');	
		jQuery(this).closest('li').find('div.inventory-updater').find('input').removeAttr('disabled');;	
		jQuery(this).closest('li').find('div.inventory-updater').find('button').removeAttr('disabled');	
		
		/*jQuery(this).closest('li').find('div.inv-variation-titles').find('.btn-option-combine').after('<input type="hidden" name="combinations[]" class="combinations hide" value="'+$titel+'">');*/		
		$btn.text('UNLIMITED');
	}
});



//ARRAY COMBINATIONS FUNCTION HERE.
function combinations($arrays, $i = 0) {
    /*if (!isset($arrays[$i])) {
        return array();
    }*/
    if ($i == $arrays.length - 1) {
        return $arrays[$i];
    }

    // get combinations from subsequent arrays
    $tmp = combinations($arrays, $i + 1);

    $result = [];

    // concat each array from tmp with each element from $arrays[$i]
    //foreach ($arrays[$i] as $v) {
    jQuery.each( $arrays[$i], function( i, $v ) {
        //foreach ($tmp as $t) {
        jQuery.each($tmp, function( i, $t ) {
            $results = $.isArray($t) ? $.merge([$v], $t) : [$v, $t];
            $result.push($results);
        });
    });

    return $result;
}

//CREATE COMBINATION INVENTORY TAB
jQuery(document).on('click', '.btn-option-combine', function(){
	$btn = $(this);
	var li_index = jQuery('.iv_option_right_list li').index( jQuery(this).closest('li') );
	var div_index = jQuery(this).closest('div.values-list').index();
	var li_lenght = jQuery('.option_right_part .iv_option_right_list li').length;
	var btn_lenght = jQuery('.option_right_part .iv_option_right_list li a.btn-option-combine').length;
	
	/*console.log(li_lenght);
	console.log(btn_lenght);*/
	jQuery(this).next().remove();
	
	jQuery(this).closest('li').find('input.inv-combine-input').val('yes');
	jQuery(this).after('<input type="hidden" name="combinations[]" class="combinations hide" value="'+jQuery(this).prev('span').text()+'">');

	if (li_lenght == btn_lenght) {		
		jQuery(this).remove();
	}else{

		$attr_arr = [];
		$title_arr = [];	


		$comb_length = $('input.combinations').length;

		if ($comb_length > 1) {
			$li_id = [];
			$bluck_options = '';
			$('input.combinations').each(function(){
				$l_id = jQuery(this).closest('li').data('iv_rightid');
				$li_id.push($l_id);
				$title = $(this).val();
				$title_arr.push($title);
				var tempArray = [];
				$title_slug = creat_option_title_slug($title);
				//console.log($title_slug);
				$options = '';
				$('input[name="options[values]['+$title_slug+'][]"]').each(function(){
					//console.log($(this).val());
					$options += '<option value="'+jQuery(this).val()+'" label="'+jQuery(this).val()+'">'+jQuery(this).val()+'</option>';
					tempArray.push(jQuery(this).val()); 
				});
				$attr_arr.push(tempArray);
				$left_li_index = jQuery(this).closest('li').index();
				//console.log($left_li_index);

				$bluck_options += '<optgroup label="'+$title+'">'+$options+' </optgroup>';

				if ($left_li_index != -1) {
					jQuery('.iv_option_left_list li').eq(jQuery(this).closest('li').index()).remove();
				}
				//jQuery('.iv_option_left_list li').eq(jQuery(this).closest('li').index()).remove();
				jQuery(this).closest('li').remove();
			});
			$combinations = combinations($attr_arr);			
			combinatins_html($title_arr,$combinations,'combine', 'prepend',$li_id, $bluck_options);
		}	    
	    /*console.log($title_arr);
	    console.log($attr_arr);
	    console.log($combinations);*/
	    
	}
	
});

//EXCLUDE ATTRIBUTE FROM COMBINATION INVENTORY TAB
jQuery(document).on('click', '.btn-option-exclude', function(){
	$opt = $('.inventory-option-list').find('li');
	$opt.each(function(i, v){
		var txt = $( this ).find( 'a' ).text();
		//console.log(txt);
		if(txt == ''){
			//console.log( this );
			$( this ).hide();
	   }
	});
	$attr_arr = [];
	$title_arr = [];
	$attrbute = $(this).next().val();
	$(this).next().remove();
	//console.log($attrbute);
	$title_arr.push($attrbute);
	$title_slug = creat_option_title_slug($attrbute);
	var tempArray = [];
	$options = '';
	$bluck_options = '';
	$('input[name="options[values]['+$title_slug+'][]"]').each(function(){
		//console.log($(this).val());
		tempArray.push(jQuery(this).val()); 
		$options += '<option value="'+jQuery(this).val()+'" label="'+jQuery(this).val()+'">'+jQuery(this).val()+'</option>';
	});
	$attr_arr.push(tempArray);
	$combinations = combinations($attr_arr);
	$bluck_options += '<optgroup label="'+$attrbute+'">'+$options+' </optgroup>';
	//console.log($combinations);
	$lid = jQuery(this).data('li_id');
	combinatins_html($title_arr,$combinations,'exclude','append',$lid, $bluck_options);

	$attr_arr = [];
	$title_arr = [];	
	$li_id = [];
	//$left_li_index = -1;
	$bluck_options = '';
    $('input.combinations').each(function(i){
    	$l_id = jQuery(this).prev().data('li_id');
    	$li_id.push($l_id);
		$title = $(this).val();
		$title_arr.push($title);
		var tempArray = [];
		$title_slug = creat_option_title_slug($title);
		console.log($title_slug);
		$options = '';
		$('input[name="options[values]['+$title_slug+'][]"]').each(function(){
			//console.log($(this).val());
			tempArray.push(jQuery(this).val()); 
			$options += '<option value="'+jQuery(this).val()+'" label="'+jQuery(this).val()+'">'+jQuery(this).val()+'</option>';
		});
		$attr_arr.push(tempArray);
		$left_li_index = jQuery(this).closest('li').index();
		//console.log($left_li_index);
		$bluck_options += '<optgroup label="'+$title+'">'+$options+' </optgroup>';

		if ($left_li_index != -1) {
			jQuery('.iv_option_left_list li').eq(jQuery(this).closest('li').index()).remove();
		}
		jQuery(this).closest('li').remove();
	});    

	$combinations = combinations($attr_arr);
	//console.log($combinations);
	combinatins_html($title_arr,$combinations,'exclude','prepend',$li_id, $bluck_options);	
});

//VARITION COMBINATIONS HTML
function combinatins_html($title_arr,$combinations, $action = 'combine', $add_action = 'prepend',$li_id,$bluck_options){

	if ($.isArray($combinations)) 
	{
    	$combine_title = '';
    	$tfoot_td = '';
    	$title_lenght = $title_arr.length;

    	jQuery.each($title_arr, function( i, $t ) 
    	{
    		$combine_exclude = '';
    		$title_slug = creat_option_title_slug($t);
    		if ($action == 'exclude' && $title_lenght == 1) {
    			$combine_title += '<th class="invat_thstart inv-option-title"><div class="inv-variation-titles"> <span>'+$t+'</span> <a href="javascript:void(0)" class="btn btn-option-combine"><i class="fa fa-link" aria-hidden="true"></i> Combine</a> </div><input type="hidden" class="inv-combine-input hide" name="options[combine]['+$title_slug+']" value="no"></th>';
    		}else{
    			$combine_title += '<th class="invat_thstart"><div class="inv-variation-titles"> <span>'+$t+'</span> <a href="javascript:void(0)" data-li_id="'+$li_id[i]+'" class="btn btn-option-exclude"><i class="fa fa-chain-broken" aria-hidden="true"></i> Exclude</a><input type="hidden" class="combinations hide" name="combinations[]" value="'+$t+'"> </div><input type="hidden" class="inv-combine-input hide" name="options[combine]['+$title_slug+']" value="yes"></th>';
    		}            
            $tfoot_td += '<td>&nbsp;</td>';
        });

    	$combine_values = '';
        jQuery.each($combinations, function( i, $vt ) {
        	$var_titles = '';
        	$variation_arr = [];
        	if ($.isArray($vt)) 
			{
	        	jQuery.each($vt, function( ii, $vtitle ) {
	        		$var_titles += '<td>'+$vtitle+'</td>';
	        		$variation_arr.push($vtitle);
	        	});
        	}else{
        		$var_titles += '<td>'+$vt+'</td>';
        		$variation_arr.push($vt);
        	}
        	$title_str = $title_arr.join('_');
        	$variation_title = $variation_arr.join(',');
        	$title_slug = creat_option_title_slug($title_str);

            $combine_values += '<tr>'+$var_titles+'<input type="hidden" class="variation-title hide" value="'+$variation_title+'" name="options[variation_title]['+$title_slug+'][]" ><td><input type="text" class="option-sku" name="options[sku]['+$title_slug+'][]"></td><td><input class="option-qty" type="number" name="options[qty]['+$title_slug+'][]" min="0" value="0"><div class="inventory-unlimited hide">∞</div></td></tr>';
        });
        
        if ($action == 'combine') {
        	$li_id = '';
        }

        //jQuery('.iv_option_left_list li').hide();
        $total_qty = jQuery('input#total_qty').val();
        jQuery('.iv_option_right_list li').addClass('hide');
        $combine_html = '<li data-iv_rightid="'+$li_id+'" class="deal-options"><div class="table-responsive"><table class="table"> <thead> <tr> '+$combine_title+' <th>CUSTOM SKU (OPTIONAL) </th> <th class="invat_thend"> <span>QTY</span>  <input type="hidden" class="inv-limited-input hide" name="options[limited]['+$title_slug+']" value="unlimited"> </th> </tr> </thead> <tbody>'+$combine_values+'</tbody> <tfoot> <tr> '+$tfoot_td+' <td class="invent_total">Total:</td><td><span class="total-quantity">0<span> / '+$total_qty+'</span></span><span class="inventory-unlimited hide">∞<span></td> </tr> </tfoot> </table></div> </li>';

         //jQuery('.option_right_part .iv_option_right_list').append($combine_html);
         //$(".option_right_part .iv_option_right_list li").addClass('hide');

         $(".iv_option_left_list li").removeClass('activenow');
         $title_str = $title_arr.join(' / ');
         if ($add_action == 'prepend') {
         	$(".option_right_part .iv_option_right_list").prepend($combine_html);
         	$(".iv_option_left_list").prepend('<li data-iv_leftid="'+$li_id+'" class="activenow"><a href="javascript:void(0)">'+$title_str+'</a></li>');
         }else{
         	$(".option_right_part .iv_option_right_list").append($combine_html);
         	$(".iv_option_left_list").append('<li data-iv_leftid="'+$li_id+'" class="activenow"><a href="javascript:void(0)">'+$title_str+'</a></li>');
         }
    }
}


//BULK UPDATE SHOW
jQuery(document).on('click', 'a.inv-bulk-update-show', function(){
	$closest_li = $(this).closest('li');
	$inventory_updater = $(this).closest('.inventory-updater');
	$inventory_updater.find('.form-group').removeClass('hide');
	$(this).parent().addClass('hide');
	$qty_limit = $closest_li.find('.inv-limited-input').val();
	if ($qty_limit == 'limited'){
		$inventory_updater.find('select').attr('disabled', 'disabled');
		$inventory_updater.find('input').attr('disabled', 'disabled');
		$inventory_updater.find('button').attr('disabled', 'disabled');
	}else{
		$inventory_updater.find('select').removeAttr('disabled');
		$inventory_updater.find('input').removeAttr('disabled');
		$inventory_updater.find('button').removeAttr('disabled');
	}

});

//BULK UPDATE HIDE
jQuery(document).on('click', 'a.inv-bulk-update-hide', function(){
	$inventory_updater = $(this).closest('.inventory-updater');
	$inventory_updater.find('.form-group').addClass('hide');
	$inventory_updater.find('.form-group:first-child').removeClass('hide');
});

//BULK QUANTITY UPDATE IN INVENTORY TAB
jQuery(document).on('click', '.btn-bulk-update', function(){
	$btn = $(this);	
	$li = $(this).closest('li');
	$quantity = $li.find('input.bulk-quantity').val();
	console.log($quantity);
	$option = $li.find('select.bulk-option').val();
	console.log($option);	
	$('tr', $li.find('tbody')).each(function(){
		$tr = $(this);	
		if ($option == 0) {
			$tr.find('input.option-qty').val($quantity);
			$tr.find('input.option-qty').change();
		}else{
			$(this).find('td').each(function(){
				$text = $(this).text();
				if ($option == $text) {
					$tr.find('input.option-qty').val($quantity);
					$tr.find('input.option-qty').change();
				}
			});
		}		
	});
	
});

//ADD ACTIVE CLASS ON VARIATION ROW IN INVENTORY TAB
jQuery(document).on('focus', 'input.option-qty', function(){
	$thisqty = $(this).val();
	jQuery(this).closest('tr').addClass('active');
});

//SINGLE QUANTITY UPDATE IN INVENTORY TAB
jQuery(document).on('focusout', 'input.option-qty', function(){
	single_update_quantity(this);
});
jQuery(document).on('keyup', 'input.option-qty', function(){
	single_update_quantity(this);
});
function single_update_quantity($obj){
	$thisqty = $($obj).val();
	if (jQuery.trim($thisqty) == '') {
		$($obj).val(0);
	}
	jQuery($obj).closest('tr').removeClass('active');

	$li = jQuery($obj).closest('li');
	$total_qty = 0;
	jQuery('input.option-qty').each(function(){
		$qty = $(this).val();
		$total_qty += parseInt($qty);
	});
	$input_total_qty = jQuery('input#total_qty').val();
	jQuery('.total-quantity').html( $total_qty+'<span> / '+$input_total_qty+'</span>');
}


//SEE ON dd BUTTON FUNCTIONALITY
jQuery(document).on('click', 'button.btn-see-dd', function(){
	modal_preview_options();
	$('.model_heading_top').text('PREVIEW OPTIONS?');
	$('.btn-yes').addClass('hide');
	$('.btn-change').addClass('hide');
	$('.btn-ok').removeClass('hide');
	
});

//FINALIZE BUTTON FUNCTIONALITY
jQuery(document).on('click', 'button.btn-finalize-deal', function(){
	$('.model_heading_top').text('ARE YOUR OPTIONS CORRECT?');
	modal_preview_options();	
	$('.btn-yes').removeClass('hide');
	$('.btn-change').removeClass('hide');
	$('.btn-ok').addClass('hide');
});

//Model perview options
function modal_preview_options(){
	$comb_length = $('input.combinations').length;
	//console.length($comb_length);
	$option_html = '';
	$title_arr = [];

	if ($comb_length > 1) 
	{
		$('input.combinations').each(function(i){
			$combid = i;
			$li_id = jQuery(this).closest('li');
			$title = $(this).val();	
			$title_arr.push($title);
			$title_slug = creat_option_title_slug($title);		
			$options = '';
			$('input[name="options[values]['+$title_slug+'][]"]').each(function(){
				$vartion_option = jQuery(this).val();

				$qty = 0;
				$('tr', $li_id.find('tbody')).each(function(){
					$tr = $(this);	
					$(this).find('td').each(function(){
						$text = $(this).text();
						console.log($(this).siblings().text());
						if ($vartion_option == $text) {
							$thisqty = $tr.find('input.option-qty').val();
							$qty += parseInt($thisqty);
						}
					});		
				});
				$qty_left = '';
				if ($qty > 0) {
					$qty_left = '('+$qty+' left)';
				}else if ($qty == 0){
					$qty_left = '(Unlimited)';
				}
				$options += '<option value="'+$vartion_option+'">'+$vartion_option+' '+$qty_left+'</option>';
			});		

		    $disabled = '';
		    if($combid != 0){
		      $disabled = 'disabled="disabled"';
		    }

			$option_html += '<div class="form-group"><select '+$disabled+' id="'+$title+'" class="popup-variations"><option value="">'+$title+'</option>'+$options+'</select></div>';			
		});
	}

	$('.option_right_part .option-list li').each(function()
	{

		$title = jQuery(this).find('input.option-title').val();
		$personalization = jQuery(this).find('input.option-personalization');
		$character = jQuery(this).find('input.option-character').val();
		$o_rightid = $(this).data('o_rightid');
		$li_id = $('li[data-iv_rightid="'+$o_rightid+'"]');
		
		
		if(jQuery.inArray($title, $title_arr) == -1) 
		{
			
			$title_slug = creat_option_title_slug($title);		
			$options = '';
			$('input[name="options[values]['+$title_slug+'][]"]').each(function(){
				$vartion_option = jQuery(this).val();

				$qty = 0;
				$('tr', $li_id.find('tbody')).each(function(){
					$tr = $(this);	
					$(this).find('td').each(function(){
						$text = $(this).text();
						if ($vartion_option == $text) {
							$thisqty = $tr.find('input.option-qty').val();
							$qty += parseInt($thisqty);
						}
					});		
				});
				$qty_left = '';
				if ($qty > 0) {
					$qty_left = '('+$qty+' left)';
				}else if ($qty == 0){
					$qty_left = '(Unlimited)';
				}
				$options += '<option value="'+$vartion_option+'">'+$vartion_option+' '+$qty_left+'</option>';
			});		

			if ($($personalization).is(":checked")) {
				if ($.trim($character) == '') {
					$character = 0;
				}
				$option_html += '<div class="form-group"><input type="text" placeholder="'+$title+' (max '+$character+')" name=""></div>';	
			}else{

				if ($options != '') {
					$option_html += '<div class="form-group"><select id="'+$title+'" class=""><option value="">'+$title+'</option>'+$options+'</select></div>';
				}
			}	
			
		}
			
	});

	$('#popup-deal-options').html($option_html);
	$('.see_on_dd_popup').toggleClass('show_popup2');
}

jQuery(document).on('change', 'select.popup-variations', function(){
	$combid = $(this).attr('id');
	$combval = $(this).val();
	//alert($combid);
	var mainArray = [];
	var xxxx = [];
	$pervious = $(this).parent().prev().find('select.popup-variations').val();
	$variations = $(this).parent().next().find('select.popup-variations');
	
	$variations.find('option').each(function(){
		$option = $(this);
		$optionval = $(this).val();
		if ($optionval != '') {
			console.log($optionval);
			$iii = 0;
			$('.iv_option_right_list li').each(function(){
				$li_id = $(this);
				$('tr', $li_id.find('tbody')).each(function(){
					$tr = $(this);	
					$qty = 0;				
					$(this).find('td').each(function(itd){
						$text = $(this).text();
						$nextid = $(this).next().text();					
						if ($combval == $text && $.trim($nextid) != '' && $optionval == $nextid) {
							$iii++;
						}

						$qty = $iii*parseInt($thisqty);
						$qty_left = '';
						if ($qty > 0) {
							$qty_left = $qty;
						}else if ($qty == 0){
							$qty_left = '(Unlimited)';
						}
						$('.dd_large_popup option[value="'+$optionval+'"]').text($optionval+'('+$qty_left+' left)');
						$('.dd_large_popup option[value="'+$optionval+'"]').parent().removeAttr('disabled');
					});		
				});
			});
		}		
	});	
});

//SORT OPTION LIST HERE
$(function () {
    $(".option-list").sortable({
    	handle: ".sortableli",
        start: function (event, ui) {
            $(this).data("elPos", ui.item.index());
        },
        update: function (event, ui) {
            var origPos = $(this).data("elPos");	            
            $(".option-list").not($(this)).each(function (i, e) {
            	
                if (origPos > ui.item.index()) {
                    $(this).children("li:eq(" + origPos + ")").insertBefore($(this).children("li:eq(" + ui.item.index() + ")"));
                } else {
                    $(this).children("li:eq(" + origPos + ")").insertAfter($(this).children("li:eq(" + ui.item.index() + ")"));
                }
            });

            $(".iv_option_left_list").not($(this)).each(function (i, e) {
            	
                if (origPos > ui.item.index()) {
                    $(this).children("li:eq(" + origPos + ")").insertBefore($(this).children("li:eq(" + ui.item.index() + ")"));
                } else {
                    $(this).children("li:eq(" + origPos + ")").insertAfter($(this).children("li:eq(" + ui.item.index() + ")"));
                }
            });

            $(".iv_option_right_list").not($(this)).each(function (i, e) {
            	
                if (origPos > ui.item.index()) {
                    $(this).children("li:eq(" + origPos + ")").insertBefore($(this).children("li:eq(" + ui.item.index() + ")"));
                } else {
                    $(this).children("li:eq(" + origPos + ")").insertAfter($(this).children("li:eq(" + ui.item.index() + ")"));
                }
            });

        }
    }).disableSelection();

    sort_option_values();
});	

//SORTING OPTION VALUUES HERE
function sort_option_values(){
	$(".sort-option-values").sortable({
    	handle: ".sortoptval",
    	start: function (event, ui) {
            $(this).data("elPos", ui.item.index());
        },
        update: function (event, ui) {
        	var li_index = jQuery('.option_right_part .option-list li').index( jQuery(this).closest('li') );
        	console.log(li_index);
        	var origPos = $(this).data("elPos");
        	jQuery('.iv_option_right_list').find('li').each(function(i){
				if (li_index == i) {
					$(this).find("tbody").not($(this)).each(function (i, e) {            	
		                if (origPos > ui.item.index()) {
		                    $(this).children("tr:eq(" + origPos + ")").insertBefore($(this).children("tr:eq(" + ui.item.index() + ")"));
		                } else {
		                    $(this).children("tr:eq(" + origPos + ")").insertAfter($(this).children("tr:eq(" + ui.item.index() + ")"));
		                }
		            });
				}
			});
            	            
            
        }
    }).disableSelection();
}


//DEAL COMMENT WINDOW 
function OpenDealCommentWindow() {
	$("#deal-comment").show();
}
function CloseDealCommentWindow() {
	$("#deal-comment").hide();
}

//FILE PREVIW IMAGE.
function filePreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$('.reply .img-thumbnail').removeClass('hide');        	
            $('.reply #deal-comment-image-preview').attr('src', e.target.result);
            $('.dealcomment-box-second').addClass('reply-toller');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

//DELETE COMMENT IMAGE 
function DealdeleteImage(){
	$('.reply .img-thumbnail').addClass('hide');
	$('.reply #deal-comment-image-preview').removeAttr('src');
	$('#deal-comment-file').val('');
	$('.dealcomment-box-second').removeClass('reply-toller');
}

//FILE UPLOAD PREVIEW
jQuery(document).on('change', '#deal-comment-file', function(){
    filePreview(this);
});


//POST DEAL COMMENT
function deal_comment_post(){
	$replytextarea = $('#reply-textarea').val();
	if (jQuery.trim($replytextarea) != '') {
		$('.reply-textarea-wrapper').css('border', '1px solid #ddd');
		$DealCommentNonce = $('#deal-comment-nonce').val();
		$productid = $('#dokan-edit-product-id').val();
		$('.btn-leave-comment').append('<span class="dokan-loading"> </span>');
		var form = $('#frm-deal-comment')[0];
		// Create an FormData object 
        var data = new FormData(form);
         $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: woocommerce_params.ajax_url,
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            cache: false,
            timeout: 600000,
            success: function (data) {
            	if (data.status == 1) {
            		$('#comments-wrapper').html(data.msg);
            		$('.reply .img-thumbnail').addClass('hide');
            		$('.reply #deal-comment-image-preview').removeAttr('src');
            		$('#deal-comment-file').val('');
            		$('.reply').css('height', '250px');
            		$('#reply-textarea').val('');
            	}else{
            		$('.reply-textarea-wrapper').css('border', '1px solid red');
            	}
            	$('.btn-leave-comment').find('.dokan-loading').remove();
            },
            error: function (e) {

                $("#result").text(e.responseText);
                console.log("ERROR : ", e);
                $("#btnSubmit").prop("disabled", false);

            }
        });

	}else{
		$('.reply-textarea-wrapper').css('border', '1px solid red');
	}
}


$(window).load(function() {
	$pid = $('#dokan-edit-product-id').val();
	if(jQuery.trim($pid) != '') {
		jQuery.post(woocommerce_params.ajax_url, {action: 'deal_load_comments', pid : $pid}, function(data){
	        $('#comments-wrapper').html(data);
	    });
	}	
	$deal_comment_id = $('#deal_comment_id').val();
	if(jQuery.trim($deal_comment_id) != '') {
		jQuery.post(woocommerce_params.ajax_url, {action: 'deal_read_comment', cid : $deal_comment_id}, function(data){
			if (data == 'seen'){
				$("#deal-comment").show();
			}
	    });
	}
});		

jQuery(document).on('change','#seller_post_status', function(e){
    self = jQuery(this).val();
    if (self == 'declined') {
        jQuery('#vendor_status_message').show();
    }else{
        jQuery('#vendor_status_message').hide();
    }
});

//Date Picker for Start Date and  Finalize Deal 

/*jQuery("#dealby" ).datepicker({firstDay: 0, dateFormat:'mm/dd/yy', maxDate:new Date($enddate), dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']});*/

jQuery("#preferred-start-date").datepicker({firstDay: 0, dateFormat:'mm/dd/yy', minDate: '11/10/2020', dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']}).on( "change", function() {
	var date2 = jQuery(this).datepicker('getDate', '+1d');
	var date3 = jQuery(this).datepicker('getDate', '+1d');
	var ph = jQuery('#personalized_handmade').val();
	date2.setDate(date2.getDate()+5); 	         
	if (ph == 'yes') {
	    date3.setDate(date2.getDate()+13);
	}else{
	    date3.setDate(date2.getDate()+6);
	}
	jQuery('#preferred-ship-date').datepicker( "option", "minDate", date2 );
	jQuery('#preferred-ship-date').datepicker( "option", "maxDate", date3 );
	console.log(date2);
});

$enddate = jQuery('#preferred-ship-date').attr('enddate');
$shipbydate = jQuery('#preferred-ship-date').attr('shipbydate');
jQuery('#preferred-ship-date').datepicker({firstDay: 0, dateFormat:'mm/dd/yy',  minDate:new Date($enddate),  maxDate:new Date($shipbydate), dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']});