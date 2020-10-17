$ = jQuery;
jQuery(document).on('click', '.single_add_to_cart_button', function(event){
	var error = 0;
    var combine_attr = [];
    jQuery('.cart .attributes').each(function(){
    	var obj = jQuery(this);    	
    	if (jQuery.trim(obj.val()) == '') {
    		obj.css('border', '1px solid red');
    		error = 1;
    	}else{
            $combine = $(this).data('combine');
            if ($combine == 'yes') {
                combine_attr.push($(this).val());
            }
    		obj.css('border', '1px solid rgba(129,129,129,.25)');
    	}
    });     
	if (error == 0) {
        var combine_all = combine_attr.join(',');
        jQuery('form.cart input[name="cart_combine_attributes"]').val(combine_all);
		jQuery('form.cart button[type="submit"]').removeClass('hide');
		jQuery('form.cart button[type="button"]').addClass('hide');
	} 
});
jQuery(document).on('keyup change', 'form.cart .attributes' , function(event){
    var error = 0;
	var combine_attr = [];
    var vardata = jQuery(this).attr('varitions');
    jQuery('.cart .attributes').each(function(){
    	var obj = jQuery(this);    	
    	if (jQuery.trim(obj.val()) == '') {
    		obj.css('border', '1px solid red');
    		error = 1;
    	}else{
            $combine = $(this).data('combine');
            if ($combine == 'yes') {
                combine_attr.push($(this).val());
            }
    		obj.css('border', '1px solid rgba(129,129,129,.25)');
    	}
    }); 

    
    //console.log(vardata); 
    if (vardata != undefined) {
        var combine_var = combine_attr.join(',');
        var obj = jQuery.parseJSON(vardata);
        var qtyleft = obj[combine_var];
		if(qtyleft > 9){
			jQuery('#qtyleft').hide();
		}
        if (qtyleft != undefined) {
            jQuery('#qtyleft').html(qtyleft+' Left');
        }
		if (qtyleft > 1) {
            jQuery('#qtyleft').html(qtyleft+' Left');
        }
    }
    

	if (error == 0) {
        var combine_all = combine_attr.join(',');
        jQuery('form.cart input[name="cart_combine_attributes"]').val(combine_all);

		jQuery('form.cart button[type="submit"]').removeClass('hide');
		jQuery('form.cart button[type="button"]').addClass('hide');
	}   
});  
jQuery(document).on('change', '#changeEve', function(){
		var text = jQuery(this).parents('.cart').find('.qtyleft strong').html();
		var txt = jQuery(this).find(':selected').data('left');
		var textNum = parseInt(text);
	//console.log(textNum);
		if(textNum == 0){
		   jQuery(this).closest('.cart').find('.single_add_to_cart_button').attr('disabled', 'disabled');
		   jQuery(this).closest('.cart').find('.out-of-stock').html('This Item is Out of Stock');
		   }
	if(txt == 0){
		   jQuery(this).closest('.cart').find('.single_add_to_cart_button').attr('disabled', 'disabled');
		jQuery(this).closest('.cart').find('.out-of-stock').html('This Item is Out of Stock');
		   } else{
			   jQuery(this).closest('.cart').find('.single_add_to_cart_button').removeAttr('disabled', 'disabled');
			   jQuery(this).closest('.cart').find('.out-of-stock').html('');
		   }
	if (textNum > 0){
			   jQuery(this).closest('.cart').find('.single_add_to_cart_button').removeAttr('disabled', 'disabled');
			   jQuery(this).closest('.cart').find('.out-of-stock').html('');
		   }
	});
//TIMER SCRIPT
function getTimeRemaining(endtime) {
    var t = Date.parse(endtime) - Date.parse(new Date());
    var seconds = Math.floor((t / 1000) % 60);
    var minutes = Math.floor((t / 1000 / 60) % 60);
    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
    return {
        'total': t,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}
function initializeClock(id, endtime) {
    var clock = document.getElementById(id);
    var hoursSpan = clock.querySelector('.hours');
    var minutesSpan = clock.querySelector('.minutes');
    var secondsSpan = clock.querySelector('.seconds');
    function updateClock() {
        var t = getTimeRemaining(endtime);
        //console.log(t);
        if (t.hours >= 0) {
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
        }else{
            hoursSpan.innerHTML = '0';
            minutesSpan.innerHTML = '00';
            secondsSpan.innerHTML = '00';
        }
        
        if (t.total <= 0) {
            //console.log('timeout');
            clearInterval(timeinterval);
            var post_id = jQuery('#deal_comment_post_id').val();
            var enddeal = jQuery('#deal-expire-timer').data('enddeal');
            if (enddeal != 0) {
                jQuery.post(dd_ajax.ajaxurl, {action:'deal_ended_status', post_id: post_id, 'nonce':dd_ajax.nonce}, function(data){
                    window.location.href='';
                });
            }
        }
    }
    updateClock();
    var timeinterval = setInterval(updateClock, 1000);
}

function upcoming_initializeClock(id, endtime) {
    var clock = document.getElementById(id);
    var hoursSpan = clock.querySelector('.uphours');
    var minutesSpan = clock.querySelector('.upminutes');
    var secondsSpan = clock.querySelector('.upseconds');
    function updateClock() {
        var t = getTimeRemaining(endtime);
        //console.log(t);
        if (t.hours >= 0) {
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
        }else{
            hoursSpan.innerHTML = '0';
            minutesSpan.innerHTML = '00';
            secondsSpan.innerHTML = '00';
        }
        
        if (t.total <= 0) {
            console.log('timeout');
            clearInterval(timeinterval);
            window.location.href='';
        }
    }
    updateClock();
    var timeinterval = setInterval(updateClock, 1000);
}

jQuery(document).ready(function(){
    var dealtime = jQuery('#deal-expire-timer').data('time');    
    //console.log(dealtime);
    if (dealtime != undefined) {
        var dateVar = dealtime;
        var dsplit = dateVar.split("-");
        var deadline = new Date(dsplit[0],dsplit[1]-1,dsplit[2]);
        //console.log(deadline);
        initializeClock('clockdiv', deadline);
    }

    var upclockdiv = jQuery('#upclockdiv').data('time'); 
    if (upclockdiv != undefined) {
        var dateVar = upclockdiv;
        var dsplit = dateVar.split("-");
        var deadline = new Date(dsplit[0],dsplit[1]-1,dsplit[2]);
        console.log(deadline);
        upcoming_initializeClock('upclockdiv', deadline);
    }
   
});

//DEAL COUNT LIKES
jQuery(document).on( 'click', '.add_to_wishlist', function( ev ) {
    var t = $( this);
    var post_id = t.data('product-id');
    ev.preventDefault();
    setTimeout(function(){         
        jQuery.post(dd_ajax.ajaxurl, {action:'count_deal_like', post_id: post_id, 'nonce':dd_ajax.nonce}, function(data){
            //jQuery('#likecount').text(data);
            t.closest('.woocommerce-product-like').find('.likecount').text(data);
            t.closest('.woocommerce-product-list-like').find('.likecount').text(data);
        });
    }, 1000);
    return false;
});



//SELLER SIGNUP FORM JS.
$(function() {
    // Multiple images preview in browser    
    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            var gallery_images = '';
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                
                reader.onload = function(event) {
                    //$($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    gallery_images = '<li><img width="100" src="'+event.target.result+'"><input type="hidden" name="SampleProductImages[]" value="'+event.target.result+'"><span class="seller-pic-remove"><i class="fa fa-trash" aria-hidden="true"></i></span></li>';
                    $('#product-no-image').hide();
                    $(placeToInsertImagePreview).append(gallery_images);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }

    };
    $(document).on('click', '.seller-pic-remove', function(){
        jQuery(this).closest('li').remove();
        $li = $('ul#seller-product-preview-imgs li').length;
        console.log($li);
        if ($li == 0) {
            $('#product-no-image').show();
            $('#seller-product-preview').val('');
        }
    });
    $('#seller-product-preview').on('change', function() {
        imagesPreview(this, 'ul#seller-product-preview-imgs');
    });
    $('#sellerk-url').on('keyup', function() {
        var slugstr = convertToSlug(jQuery(this).val());
        //console.log(slugstr);
        $('#seller-url').val(slugstr);
    });
});

function convertToSlug( str ) {
    
  //replace all special characters | symbols with a space
  str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ').toLowerCase();
    
  // trim spaces at start and end of string
  str = str.replace(/^\s+|\s+$/gm,'');
    
  // replace space with dash/hyphen
  str = str.replace(/\s+/g, '-');   
  //document.getElementById("slug-text").innerHTML= str;
  return str;
}



 
      jQuery('.categorysalider').slick({
         dots: false,
         arrows: true,
         autoplay: false,
         infinite: true,
         slidesToShow: 6,
         slidesToScroll: 6,
         pauseOnHover: true,
         responsive: [
         {
            breakpoint: 992,
            settings: {
               slidesToShow: 5,
               slidesToScroll:5
            }
         },
         {
            breakpoint: 768,
            settings: {
                autoplay: true,
               slidesToShow: 3,
               slidesToScroll: 3
            }
         },
         {
            breakpoint: 481,
            settings: {
             autoplay: true,
               slidesToShow: 2,
               slidesToScroll: 2,
            }
         }
         ]
      });  
      $('#product_tabs a.nav-link').on('click', function (e) {
          e.preventDefault(); 
          var thisdata = $(this).attr('cat_slug');
          $(this).closest('#product_tabs').find('a.nav-link').removeClass('active'); 
          $(this).addClass('active');  
          $(this).closest('#product_tabs').find('.tab-content').find('.tab-pane').hide();     
          $(this).closest('#product_tabs').find('.tab-content').find('#pillstab'+thisdata+'').show();
      }) 

/*$(".right_menu_toggle").click(function(){
    $(".toggle_right_menu").slideToggle();
  });*/ 

   $(".right_menu_hide").click(function(e){
    e.preventDefault(); 
    $(".toggle_right_menu").removeClass('active'); 
  });
   $(".right_menu_toggle").click(function(e){
    e.preventDefault();   
    $(".toggle_right_menu").addClass('active'); 
  }); 

   $(".toggle_right_menu .menu-item-has-children > a").click(function(e){   
    e.preventDefault();     
    $(this).closest('li').find('.sub-menu').toggle(); 
  });  
 
  $('.tab_shop').on('click', function (e) { 
    e.preventDefault();
    var catLocation = $(this).attr('data-location');     
    window.location.href=catLocation;  
  });  

  $('.faqs-list .faqs-head').on('click', function (e) { 
    $(this).next().toggle(100);  
 });

jQuery('#frm-sub-home').on('submit', function (e) { 
    e.preventDefault();
    var sub_email_field = jQuery(this).find('#sub_email');
    var sub_email = sub_email_field.val();
    var faspin = jQuery(this).find('.fa-spin');
    var frmmsg = jQuery(this).find('.frm-msg');
    faspin.show();    
    if (sub_email != '') {
        jQuery.post(dd_ajax.ajaxurl, {action:'dd_sub_email', sub_email: sub_email, 'nonce':dd_ajax.nonce}, function(data){
            faspin.hide();
            frmmsg.text('You have subscribed successfully.');
            sub_email_field.val('');
        });
    }    
}); 

var busy = false;
jQuery(document).ready(function() {
    var win = jQuery(window);
    // Each time the user scrolls
    var page = 2;
    win.scroll(function() {
        // End of the document reached?
        //console.log(jQuery(document).height() - win.height());
        //console.log(win.scrollTop());
        if (jQuery(document).height() - win.height() <= win.scrollTop()+500) {
            jQuery('#loading').show();
            if (busy == false) {
                busy = true;
                jQuery('.loadingicon').removeClass('hide');
                jQuery.post(woocommerce_params.ajax_url, {action:'home_more_product', 'page': page}, function(data){
                    if (data != '') {
                        jQuery('.home_scroll_products').append(data);
                        window.busy = false;
                    }
                    jQuery('.loadingicon').addClass('hide');
                }); 
                page++; 
            }else{
                //jQuery('.loadingicon').addClass('hide');
            }
        }
    });
});
                      