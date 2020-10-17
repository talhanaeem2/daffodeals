var $ = jQuery.noConflict();
jQuery(document).on('click focusout', '.dropdown-toggle', function(event){
    jQuery(this).closest('.deal-dropdown').toggleClass('open');
    if (event.type == 'focusout') {
    	jQuery(this).closest('.deal-dropdown').removeClass('open');
    }
});

//SUBMIT PROFILE DATA HERE.
jQuery(document).on('submit', "form#seller-profile-form", function(event){
	event.preventDefault();
	var self = $( "form#seller-profile-form"),
	form_data = self.serialize() + '&action=seller_profile';
	self.find('.dokan-loading').remove();
	self.find('.ajax_prev').append('<span class="dokan-loading"> </span>');
	$.post(dokan.ajaxurl, form_data, function(resp) {

		self.find('span.dokan-loading').remove();
		$('html,body').animate({scrollTop:0});
		if ( resp.success ) {	
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-success',
				'html': '<p>' + resp.data.msg + '</p>',
			}) );

			$('.dokan-ajax-response').append(resp.data.progress);

		}else {
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-danger',
				'html': '<p>' + resp.data + '</p>'
			}) );
		}
	});
});   

//whatsold function
jQuery(document).on('click', ".whatsold", function(event){
    event.preventDefault();
    var pid = $(this).attr('dealid');
    jQuery.post(dokan.ajaxurl, {action: 'whatsolditems', pid: pid}, function(resp) {
        jQuery('#whatsoldmodal').find('.modal-body').html(resp);
        jQuery('#whatsoldmodal').modal('show');
    });
});  

//SUBMIT BUSINESS INFO DATA HERE.
jQuery(document).on('submit', "form#seller-business-info-form", function(event){
	event.preventDefault();
	var self = $( "form#seller-business-info-form"),
	form_data = self.serialize() + '&action=seller_business_info';
	self.find('.dokan-loading').remove();
	self.find('.ajax_prev').append('<span class="dokan-loading"> </span>');
	$.post(dokan.ajaxurl, form_data, function(resp) {

		self.find('span.dokan-loading').remove();
		$('html,body').animate({scrollTop:0});

		if ( resp.success ) {	
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-success',
				'html': '<p>' + resp.data.msg + '</p>',
			}) );

			$('.dokan-ajax-response').append(resp.data.progress);

		}else {
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-danger',
				'html': '<p>' + resp.data + '</p>'
			}) );
		}
	});
}); 

//SUBMIT EMAILS DATA HERE.
jQuery(document).on('submit', "form#seller-emails-form", function(event){
	event.preventDefault();
	var self = $( "form#seller-emails-form"),
	form_data = self.serialize() + '&action=seller_emails';
	self.find('.dokan-loading').remove();
	self.find('.ajax_prev').append('<span class="dokan-loading"> </span>');
	$.post(dokan.ajaxurl, form_data, function(resp) {

		self.find('span.dokan-loading').remove();
		$('html,body').animate({scrollTop:0});

		if ( resp.success ) {	
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-success',
				'html': '<p>' + resp.data.msg + '</p>',
			}) );

			$('.dokan-ajax-response').append(resp.data.progress);

		}else {
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-danger',
				'html': '<p>' + resp.data + '</p>'
			}) );
		}
	});
});


//SUBMIT CONTRACT FORM DATA HERE.
jQuery(document).on('submit', "form#contract-frm", function(event){
	event.preventDefault();
	var self = $( "form#contract-frm"),
	form_data = self.serialize() + '&action=seller_contract';
	self.find('.dokan-loading').remove();
    self.find('.fa-refresh').show();
	$.post(dokan.ajaxurl, form_data, function(resp) {
        self.find('.fa-refresh').hide();
        self.find('.accept').html("Accepted");
        self.find('.accept').attr('disabled','disabled');
		self.find('.terms_conditions').attr('disabled','disabled');
		//$('html,body').animate({scrollTop:0});

		if ( resp.success ) {	
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-success',
				'html': '<p>' + resp.data.msg + '</p>',
			}) );

			$('.dokan-ajax-response').append(resp.data.progress);

		}else {
			$('.dokan-ajax-response').html( $('<div/>', {
				'class': 'dokan-alert dokan-alert-danger',
				'html': '<p>' + resp.data + '</p>'
			}) );
		}
	});
});

//CONTARCT MORE INFO CONTNET.
jQuery(document).on('click', ".more-info", function(event){
	event.preventDefault();
	$this = $(this);
	$id = $(this).data('id');
	//console.log($id);
	$('#'+$id).toggle();
	if ($('#'+$id).is(':visible')) {
		$this.html('- less info');
	}else{
		$this.html('+ more info');
	}
});

/*
------------------------------------
START REVIEW MENU JS HERE
-------------------------------------
*/
//REVIEW RESPONSE JS
jQuery(document).on('click', '.review-response', function(event){
    var email = jQuery(this).data('email'); 
    var rid = jQuery(this).data('id'); 
    if (jQuery.trim(email) != '') {
       jQuery('#review-response-modal').fadeIn(100);
       jQuery('.comment-author-email').text(email);
       jQuery('#btn-review-yes-reply').attr('dataid', rid);
       jQuery('.comment-author-email').attr('href', 'mailto:'+email);

    }    
});  
jQuery(document).on('click', '.btn-modal-close, .btn-cancel', function(event){
   jQuery("#review-response-modal").fadeOut('fast');
}); 
jQuery(document).on('click', '#btn-review-yes-reply', function(event){
	var rid = jQuery(this).attr('dataid');
	jQuery('.review-response-entry-container').addClass('hide');
	jQuery("#review-response-modal").fadeOut('fast');
   	jQuery("#review-"+rid).removeClass('hide');
   	jQuery('[data-id="'+rid+'"]').hide();
});   
jQuery(document).on('click', '.toggleAddResponse', function(event){
	var rid = jQuery(this).data('rid');	
	jQuery("#review-"+rid).addClass('hide');
   	jQuery('[data-id="'+rid+'"]').show();
}); 
jQuery(document).on('keyup', '.review-response-input', function(event){
    var self = jQuery(this); 
    if (jQuery.trim(self.val()) != '') {
        
        jQuery(this).closest('.comment-row').find('.review-response-text').text(self.val());
        jQuery(this).css('border', '1px solid #cacaca');
    }else{
        jQuery(this).css('border', '1px solid red');
    }
}); 
jQuery(document).on('click', '.saveResponse', function(event){
    var self = jQuery(this); 
    var selft_closest = jQuery(this).closest('.comment-row');
    var rid = jQuery(this).data('sid'); 
	var leave_response = jQuery('#review-input-'+rid).val();
    if (jQuery.trim(leave_response) != '') {
        jQuery('#review-input-'+rid).css('border', '1px solid #cacaca');
        self.append('<span class="dokan-loading"> </span>');
        $.post(dokan.ajaxurl, {action: 'deal_review_response', rid:rid, leave_response:leave_response, nonce: dokan.nonce}, function(resp) {
           self.find('span.dokan-loading').remove();
           jQuery("#review-"+rid).addClass('hide');
           selft_closest.find('.review-response-text-container').removeClass('hide');
        });
    }else{
        jQuery('#review-input-'+rid).css('border', '1px solid red');
    }
}); 

var review_page = 1;
var review_scroll = 1;
jQuery(document).on('click', '.dashboard-reviw-rating', function(event){
    var rating = jQuery(this).data('val'); 
    review_page = 1;   
    if (jQuery.trim(rating) != '') {
        jQuery('#loader-top').removeClass('hide');        
        jQuery('#review-rating-filter option[value='+rating+']').attr('selected','selected');
        review_comment_list(rating);        
    }    
});  
jQuery(document).on('change', '#review-rating-filter', function(event){
    review_page = 1;
    var rating = jQuery(this).val();
    if (jQuery.trim(rating) != '') {
        jQuery('#loader-top').removeClass('hide');
        review_comment_list(rating);        
    }
});
function review_comment_list(rating,page=''){    
    jQuery.post(woocommerce_params.ajax_url, {action:'reviews_start_filter', rate: rating, page:page}, function(data){
        jQuery('#loader-top').addClass('hide');
        jQuery('#loader-bottom').addClass('hide');
        if (page != '') {
            jQuery('#review_content_main').append(data);
        }else{            
            jQuery('#review_content_main').html(data);
        }
        review_scroll = 1;
    });
}
$(window).scroll(function() {    
    if($(window).scrollTop() == $(document).height() - $(window).height()) {        
        jQuery('#loader-bottom').removeClass('hide'); 
        var rating = jQuery('#review-rating-filter').val();
        var show_deals = jQuery('#review_content_main .comment-row').length;
        show_deals = parseInt(show_deals);
        var noreviews = jQuery('.woocommerce-noreviews').text();
        //console.log(show_deals);
        if (show_deals > 0 && noreviews == '' && review_scroll == 1) {
            review_scroll = 0;
            review_page++;
            review_comment_list(rating,review_page);
        }else{
            jQuery('#loader-bottom').addClass('hide');
        }
    }
});

var redeal_page = 1;
var redeal_scroll = 1;
jQuery(document).on('click', '.review-deal-rating', function(event){
    var rating = jQuery(this).data('val'); 
    redeal_page = 1;   
    if (jQuery.trim(rating) != '') {
        jQuery('#loader-top').removeClass('hide');       
        jQuery('#review-deal-filter option[value='+rating+']').attr('selected','selected');
        review_deal_comment_list(rating);        
    }    
});  
jQuery(document).on('change', '#review-deal-filter', function(event){
    redeal_page = 1;
    var rating = jQuery(this).val();
    if (jQuery.trim(rating) != '') {
        jQuery('#loader-top').removeClass('hide');
        review_deal_comment_list(rating);        
    }
});
function review_deal_comment_list(rating,page=''){  
	var post_id = jQuery('#review_post_id').val();  
    jQuery.post(woocommerce_params.ajax_url, {action:'review_deal_star_filter', rate: rating, page:page,post_id:post_id}, function(data){
        jQuery('#loader-top').addClass('hide');
        jQuery('#loader-bottom').addClass('hide');
        if (page != '') {
            jQuery('#review_deal_content').append(data);
        }else{            
            jQuery('#review_deal_content').html(data);
        }
        redeal_scroll = 1;
    });
}
$(window).scroll(function() {    
    if($(window).scrollTop() == $(document).height() - $(window).height()) {        
        jQuery('#loader-bottom').removeClass('hide'); 
        var rating = jQuery('#review-deal-filter').val();
        var show_deals = jQuery('#review_deal_content .comment-row').length;
        show_deals = parseInt(show_deals);
        var noreviews = jQuery('.woocommerce-noreviews').text();
        //console.log(show_deals);
        if (show_deals > 0 && noreviews == '' && redeal_scroll == 1) {
            redeal_scroll = 0;
            redeal_page++;
            review_deal_comment_list(rating,redeal_page);
        }else{
            jQuery('#loader-bottom').addClass('hide');
        }
    }
});

jQuery(document).ready(function(){
 jQuery(".dashboard-review-deal button.btn-dropdown").click(function(){
   jQuery(".dashboard-review-deal ul.dropdown-menu").toggle(200);
 });
 jQuery(".dashboard-review-deal ul.dropdown-menu").mouseleave(function(){
 	jQuery(".dashboard-review-deal ul.dropdown-menu").hide();
 });
	
	
	$('#myTable').DataTable();

	
});
/*
------------------------------------
START CALENDAR MENU JS HERE
-------------------------------------
*/
jQuery(document).on('click', '#ws-calendar a.prev, #ws-calendar a.next, #ws-calendar a.today', function(event){
    event.preventDefault();
    var month = jQuery(this).data('month');
    var year = jQuery(this).data('year');
    $('#calendar-loader').removeClass('hide');
    if (jQuery.trim(month) != '' && jQuery.trim(year) != '') {
    	jQuery.post(woocommerce_params.ajax_url, {action:'get_ws_calendar', month: month, year:year}, function(data){
    		jQuery('#loader-bottom').addClass('hide');
    		if (data != '') {
    			jQuery('.calendar-content').html(data);
    			$('#calendar-loader').addClass('hide');
    		}
    	});        
    }
});
jQuery(document).on('click', '#ws-calendar .cl-date', function(event){
    event.preventDefault();
    var active_date = jQuery(this).data('date');
    if (active_date != '') {
		jQuery('.calendar-sidebar ul.deals li').removeClass('active');
		var elm = jQuery('.calendar-sidebar li#'+active_date);
		//console.log(elm);
		if (elm.length != 0) {
			elm.addClass('active');
			$('.calendar-sidebar').animate({scrollTop: $('#'+active_date).offset().top-200},'slow');
		}
    }
    
});
jQuery(document).on('click','#ws-calendar ul.dates li' , function(){
 jQuery(this).addClass('active').siblings().removeClass('active');
});

/*
------------------------------------
START DEAL ORDER MENU JS HERE
-------------------------------------
*/
jQuery(document).on('click',"#PrintOrderSummary", function () { 
    var divToPrint=document.getElementById("summary-list");
    var htmlToPrint = '' +
        '<style type="text/css">table th{padding: 15px 30px; border: 0; border-top: 1px dashed #e5e5e5; border-bottom: 1px dashed #e5e5e5; text-transform: uppercase; font-size: 17px; font-weight: 500; color: #222; letter-spacing: 0;} table td {padding: 15px 30px !important; text-align: center; line-height: 24px; border-bottom: 1px dashed #e5e5e5; font-size: 14px; color: #333; font-weight: 300;}</style>';
    htmlToPrint += divToPrint.outerHTML;
    newWin = window.open("");
    newWin.document.write("<h3 align='center'>Summary</h3>");
    newWin.document.write(htmlToPrint);
    newWin.print();
    newWin.close();
});
jQuery(document).on('click',".order-details", function () { 
    $OrderID = jQuery(this).data('orderid');
    $orderpid = jQuery(this).data('orderpid');
    $orderdload = jQuery(this).find('.orderdload');
    $orderdload.show();
    if (jQuery.trim($OrderID) != '') {
        $.post(dokan.ajaxurl, {action: 'DashboardOrderDetails', OrderID:$OrderID,  OrderPID:$orderpid, nonce: dokan.nonce}, function(resp) {
           jQuery('#OrderDetailsModal').modal('show');
           jQuery('#OrderDetailsModalContent').html(resp);
           $orderdload.hide();
        });
    }
});
jQuery(document).on('click', '.btn-modal-close, .btn-cancel', function(event){
   jQuery("#OrderDetailsModal").fadeOut('fast');
   jQuery("#OrderTrackingModal").fadeOut('fast');
}); 
jQuery(document).on('change',"select.deal-order-download", function () { 
    $self = jQuery(this).val();
    if (jQuery.trim($self) != 'select') {
       jQuery(this).parent().submit();
    }
});
jQuery(document).on('click',".tracking-btn", function () { 
    $OrderID = jQuery(this).data('orderid');
    $orderpid = jQuery(this).data('orderpid');
    $tracking = jQuery(this).data('tracking');
    $shipping = jQuery(this).data('shipping');
    $carrier = jQuery(this).data('carrier');
    if (jQuery.trim($OrderID) != '') {        
        jQuery('#track_order_id').val($OrderID);
        jQuery('#trackingNumberText').val($tracking);
        jQuery('#shippingdate').val($shipping);
        jQuery('#carrierSelect').val($carrier);
        jQuery('#OrderTrackingModal').modal('show');

        jQuery('#OrderTrackingModal').fadeIn('fast');
    }
});
jQuery(document).on('click',".upload-tracking", function () { 
    jQuery('#UploadTrackingModal').modal('show');
});
jQuery(document).on('click',"#saveTracking", function (event) { 
    event.preventDefault();
    $required = 0;
    $("#frm-order-tracking .fieldrequired").each(function(){
        $self = $(this);
        if ($.trim($self.val()) == '') {
            $required = 1;
            $(this).css('border', '1px solid red');
        }else{
            $(this).css('border', '1px solid rgba(129,129,129,.25)');
        }
    });
    if ($required == 0) {
        $orderid = jQuery('#track_order_id').val();
        $trackingnumber = jQuery('#trackingNumberText').val();
        $shippingdate = jQuery('#shippingdate').val();
        $carrier = jQuery('#carrierSelect').val();
        $.post(dokan.ajaxurl, {action: 'DashboardOrderTracking', orderid:$orderid,trackingnumber:$trackingnumber,shippingdate:$shippingdate,carrier:$carrier,  nonce: dokan.nonce}, function(resp) {
           jQuery('#tracking-msg').text(resp);
           $("#frm-order-tracking")[0].reset();
           setTimeout(function(){ window.location.href=''; }, 3000);
           //jQuery("#OrderTrackingModal").fadeOut('fast');
        });
    }
});
jQuery(document).on('change',"#input-file-upload", function (event) { 
    var file = this.files[0];
    var form = new FormData();

    var orderid = $('#fieldmap_orderid').val();
    if (jQuery.trim(orderid) != '') { form.append('orderid', orderid);  }
    var trackingnumber = $('#fieldmap_trackingnumber').val();
    if (jQuery.trim(trackingnumber) != '') { form.append('trackingnumber', trackingnumber);  }
    var shipping_date = $('#fieldmap_shipping_date').val();
    if (jQuery.trim(shipping_date) != '') { form.append('shipping_date', shipping_date);  }
    var carrier = $('#fieldmap_carrier').val();
    if (jQuery.trim(carrier) != '') { form.append('carrier', carrier);  }
    
    form.append('uploadfile', file);
    form.append('action', 'DashboardUploadTracking');
    form.append('nonce', dokan.nonce);
    $.ajax({
        url : dokan.ajaxurl,
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        data : form,
        success: function(response){
            if (response.status == 'error') {
                $('#showFileError').text(response.msg);
            }else if (response.status == 'success'){
                $('#fieldmap_orderid').html(response.msg);
                $('#fieldmap_trackingnumber').html(response.msg);
                $('#fieldmap_shipping_date').html(response.msg);
                $('.fileupload-new').addClass('hide');
                $('#file-heading').addClass('hide');
                $('#cancelfieldmapping').addClass('hide');                
                $('#readyToSubmit').removeClass('hide');
                $('#submitfieldmapping').removeClass('hide');
                $('#closefieldmapping').removeClass('hide');
            }else if (response.status == 'working'){
                $('#uploadSuccessCount').removeClass('hide');
                $('#uploadSuccessCount').text(response.msg+' row(s) were imported successfully.');
                $("#input-file-upload").change();
            }else if (response.status == 'done'){
                $('#uploadSuccessCount').text('All data were imported successfully.');
                setTimeout(function(){ window.location.href=''; }, 3000);
            }
        }
    });
});
jQuery(document).on('click', '#submitfieldmapping', function(event){
    $(this).attr('disabled','disabled');
    $("#input-file-upload").change();
});
jQuery(document).on('click', '#UploadTrackingModal .btn-modal-close, #UploadTrackingModal .btn-cancel', function(event){
    $('.fileupload-new').removeClass('hide');
    $('#file-heading').removeClass('hide');
    $('#cancelfieldmapping').removeClass('hide'); 
    $('#readyToSubmit').addClass('hide');
    $('#submitfieldmapping').addClass('hide');
    $('#closefieldmapping').addClass('hide');
    $("#UploadTrackingModal").fadeOut('fast');
    $('#uploadSuccessCount').addClass('hide');
}); 
jQuery(document).on('click', '.order_search', function(event){
    if(event.which == 13) {
        jQuery('#frm_order_search').submit();
    }    
}); 

/*
------------------------------------
START ORDER SEARCH JS HERE
-------------------------------------
*/
jQuery(document).on('submit', '#order_search', function(event){
    event.preventDefault();
    var search_query = jQuery('#search_query').val();
    $.post(dokan.ajaxurl, {action: 'DashboardOrderSearch', search_query:search_query,nonce: dokan.nonce}, function(resp) {        
        jQuery(".top-menu-order-result").html(resp);     
    });    
});
jQuery(document).on('click', '.order-search .head', function(event){
    event.preventDefault();
    $(this).next('.order-details').removeClass('hide');
    $(this).addClass('hide');
});
jQuery(document).on('click', '.order-search .order-header', function(event){
    event.preventDefault();
    $(this).closest('.order').find('div.head').removeClass('hide');
    $(this).closest('.order').find('.order-details').addClass('hide');
});

function deselect(e) {
    $('.pop').slideFadeToggle(function() {
        e.removeClass('selected');
    });    
}

jQuery(document).on('click ', '.dash_search', function(event){
    
    if($(this).hasClass('selected')) {
        deselect($(this));               
    } else {
        $(this).addClass('selected');
        $('.messagepop.pop').slideFadeToggle();
    }
    return false;
});
/*jQuery(document).on('click', '.close', function(event){
deselect($('.dash_search'));
return false;
});
*/
$.fn.slideFadeToggle = function(easing, callback) {
  return this.animate({ opacity: 'toggle', height: 'toggle' }, 'fast', easing, callback);
};
/*
------------------------------------
DASHBAORD TOGGLE MENU JS HERE
-------------------------------------
*/
$(document).ready(function(){
    $(".sidebar_toggle").click(function(){
        $("#leftsidebar").show('200');
        $(".dokan-dash-sidebar").show('400');
        $(".subsidebar").show('400');
    });
    $("#sidebarclose").click(function(){
        $("#leftsidebar").hide();
        $(".dokan-dash-sidebar").hide();
        $(".subsidebar").hide();
    });
    jQuery("#smsclose").on("click", function(){
        jQuery(".messagepop.pop").fadeOut('fast');
    });

    jQuery('#order_search').keypress(function (e) {
        if (e.which == 13) {
            jQuery('form#frm_order_search').submit();
            return false;    //<---- Add this line
        }
    });
});
/*
------------------------------------
DEAL COMMENT NOTIFICATION MENU JS HERE
-------------------------------------
*/
jQuery(document).on('click', ".dash_rightbar li.ball_dash", function(){
   jQuery("#dashboard-unseen-noti-list").toggle();
   jQuery(this).focus();
   //jQuery("#dashboard-unseen-noti-list ul").focus();
 });  
jQuery(document).on('mouseleave', "#dashboard-unseen-noti-list", function(e){
    jQuery("#dashboard-unseen-noti-list").toggle();
});
/*jQuery(document).on('click', function(e){
  console.log(e.target);
 
    if(e.target.parentNode.parentNode.parentNode.classList.contains('new-notifications') || e.target.parentNode.parentNode.classList.contains('ball_dash')){
      return false;
    }
   if( jQuery('#dashboard-unseen-noti-list').is(':visible') ){
    jQuery('#dashboard-unseen-noti-list').hide();
  }
});*/

