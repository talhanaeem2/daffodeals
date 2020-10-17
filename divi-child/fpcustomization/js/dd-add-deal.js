jQuery(document).ready(function(){

    jQuery("#preferred-start-date" ).datepicker({firstDay: 0, dateFormat:'mm/dd/yy', minDate: '11/10/2020', dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']}).on( "change", function() {
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
        console.log(date3);
    });

    jQuery('#preferred-ship-date').datepicker({firstDay: 0, dateFormat:'mm/dd/yy', dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']});
    /*var dateFormat = "mm/dd/yy";
    
    function getDate( element ) {
        var date;
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }
        return date;
    }*/
    

    //ADD VALIDATION ON ADD NEW DEAL FORM
    jQuery('#submit_from').submit(function (event) {
        var errors = false;
        jQuery(this).find('.req_field').each(function () {
            if (jQuery(this).val() == '') {
                jQuery(this).css('border', '1px solid red');
                jQuery('.dokan-feat-image-upload').css('border', '4px dashed red');
                errors = true;
            }else{
                jQuery('.dokan-feat-image-upload').css('border', '4px dashed #DDDDDD');
                jQuery(this).css('border', '1px solid rgba(129,129,129,.25)');
            }
        });
        
        if (jQuery('#product_cat').val() == '-1') {
            jQuery(this).find('span[aria-labelledby="select2-product_cat-container"]').css('border', '1px solid red');
            errors = true;
        }else{
            jQuery(this).find('span[aria-labelledby="select2-product_cat-container"]').css('border', '1px solid rgba(129,129,129,.25)');
        }

        if (errors == true) {
            event.preventDefault();
        }
    });


    jQuery('.block-btn').click(function(){
        jQuery('.block-btn').removeClass('active');
        jQuery(this).addClass('active');
        var section = jQuery(this).data('section');

        if (section == 'new-deal') {
            jQuery('#dokan-new-product-area').show();
            jQuery('#dokan-rebook-product-area').addClass('hide');
            jQuery('input[type="text"]').val('');
            jQuery('input[type="number"]').val('');
            jQuery('input[name="feat_image_id"]').val('');
            jQuery('.image-wrap').find('img').attr('src', '');
            jQuery('.instruction-inside').removeClass('dokan-hide');
            jQuery('input[name="product_image_gallery"]').val('');
            jQuery('.product_images').find('.image').each(function(){
                jQuery(this).remove();
            });
            jQuery('input[type="checkbox"]').removeAttr('checked');
            jQuery('select option:selected').removeAttr('selected');
            jQuery("select").trigger('change.select2'); 
            jQuery('textarea').val('');
        }else{
            jQuery('#dokan-new-product-area').hide();
            jQuery('#dokan-rebook-product-area').removeClass('hide');
            jQuery('.loading').removeClass('hide');
            jQuery('.rebook-deals').hide();
            jQuery.post(woocommerce_params.ajax_url, {action: 'kate_rebook_deals'}, function(data){
                jQuery('.rebook-deals').html(data);
                jQuery('.loading').addClass('hide');
                jQuery('.rebook-deals').show();
            });
        }                    
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

    jQuery('#global-search-input').keyup(function(event){
        event.preventDefault();
        var search = jQuery(this).val();
        
        if (jQuery.trim(search) != '') {
            jQuery('#search-remove').removeClass('hide');
        }else{
            jQuery('#search-remove').addClass('hide');
        }

        jQuery('.loading').removeClass('hide');
        jQuery('.rebook-deals').hide();
        jQuery.post(woocommerce_params.ajax_url, {action: 'kate_rebook_deals', 'product_search_name' : search}, function(data){
            jQuery('.rebook-deals').html(data);
            jQuery('.rebook-deals').show();
            jQuery('.loading').addClass('hide');
        });
    });

    jQuery('#search-remove').click(function(){
        jQuery('#global-search-input').val('');
        jQuery('#global-search-input').trigger('keyup');
    });

    jQuery('.add_newship').click(function(){
        jQuery(this).hide();
        jQuery('#additional-shipping-price').removeClass('hide');
    });
    
    
});

jQuery(document).on('click','.page-numbers', function(event){
    event.preventDefault();
    jQuery('.loading').removeClass('hide');
    jQuery('.rebook-deals').hide();

    if (jQuery(this).hasClass("next")) {
        var href = jQuery(this).attr('href');
        var res = href.split("=");
        var pagenumber = res[1];
        console.log(res);
    }else if (jQuery(this).hasClass("prev")) {
        var href = jQuery(this).attr('href');
        var res = href.split("=");
        var pagenumber = res[1];
    }else{
        var pagenumber = jQuery(this).text();
    }

    var search = jQuery('#global-search-input').val();

    jQuery.post(woocommerce_params.ajax_url, {action: 'kate_rebook_deals', 'pagenum' : pagenumber,'product_search_name' : search}, function(data){
        jQuery('.rebook-deals').html(data);
        jQuery('.rebook-deals').show();
        jQuery('.loading').addClass('hide');
    });
});