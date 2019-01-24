// FUNTIION
// USE FOR NOTIFY POPUP
function call_notify(title_msg, msg, type_notify) {
    type_notify = type_notify ? type_notify : "error";

    var icon = "";
    if(type_notify == "error") {
        icon = "fa fa-exclamation-circle";
    } else if(type_notify == "success") {
        icon = "fa fa-check-circle";
    }

    new PNotify({
        title: title_msg,
        text: msg,
        type: type_notify,
        icon: icon,
        addclass: 'alert-with-icon'
    });
}

// USE FOR CART
function update_cart(onthis) {
    var quantity = $(onthis).val();
    var id = $(onthis).attr("cart_id");

    // Check quality
    if ( quantity <= 0 ) {
        quantity = 1;
    }
    
    //Ajax
    $.ajax({
        type: "post",
        url: "/cart/update",
        data: {quantity: quantity, id: id},
        success: function(html)
        {
            // console.log(html);
            var obj = JSON.parse(html);
            // set value
            if(obj.total_show && obj.amount)
            {
                $(".total_change_"+$(onthis).attr("cart_id")).html(obj.total_show);
                $(".amount_change").html(obj.amount);
            }

            if(obj.cart_data){
                $("#cart_tax").text(obj.cart_data[1]);
                $("#cart_discount_code_value").text(obj.cart_data[5]);
                $("#cart_subtotal").text(obj.cart_data[2]);
                $("#cart_payment_total").text(obj.cart_data[3]);
            }
        }
    });
}

function delItem(onthis) {
    var id = $(onthis).attr("cart_id");

    //Ajax
    $.ajax({
        type: "post",
        url: "/cart/delitem",
        data: {id: id},
        success: function(html)
        {
            // console.log(html);
            var obj = JSON.parse(html);
            // set value
            if(obj.amount)
            {
                // remove row
                $('.cart-details-items-'+id).remove();

                // change if empty
                if ( $('.cart-details-items').length <= 1 )
                {
                    $('.cart-details').append('<div class="media"><div class="media-body"><h4 class="media-heading">cart empty ...</h4></div></div>');
                }

                // set amount
                $(".amount_change").html(obj.amount);
            }

            if(obj.cart_data){
                $("#cart_tax").text(obj.cart_data[1]);
                $("#cart_discount_code_value").text(obj.cart_data[5]);
                $("#cart_subtotal").text(obj.cart_data[2]);
                $("#cart_payment_total").text(obj.cart_data[3]);
            }
        }
    });
}
// END USE FOR CART

// EVENTS JQUERY
$(document).ready(function() {

    // Mask Input
    var plholder = phoneFormat == "(000) 000-0000" ? "Phone (___) ___-____" : "Phone ____ ___ ____";
    $(".inputPhone").mask(phoneFormat, {placeholder: plholder});
    // End mask input

    // FLEX INPUT
    $('.fl-flex-label').flexLabel();

    // PAYMENT
    $("input[name='send_to_friend']").click(function() {
        var check_val = $(this).val();
        if(check_val == 0)
        {
            $(".box_recipient").show();
            $(this).val(1);
        }
        else
        {
            $(".box_recipient").hide();
            $(this).val(0);
        }
    });

    var check_send = parseInt($("input[name='send_to_friend']").val());
    if(check_send == 1)
    {
        $("input[name='send_to_friend'][value='1']").prop("checked", true);
        $(".box_recipient").show();
    }

    $('.payment-tab-choose-item').click(function(){
        $('input[name="payment_method"]').val($(this).data('payment-method'));
    });
    // END PAYMENT

    // Dropdown menu search
    $(".dropdown-select-search li a").click(function(){
        $("#search_concept").text($(this).text());
        $('input[name="group_id"]').val($(this).data('id'));
    });

    $('.dropdown-select-search li a[data-id="'+$('input[name="group_id"]').val()+'"]').trigger('click');

    // Trigger tab product frist click
    $('.tab-list-products-category li a[data-toggle="tab"]').first().trigger('click');

    // Tab all gallery and cotroller light
    $(window).load(function(){
        $('#lightgallery-tab-all-gallery').html('');
        $('.tab-gallery-items').each(function(){
            if ( $(this).find('.no-data-gallery').length == 0 ) {
                $('#lightgallery-tab-all-gallery').append($(this).html());
            }
        });

        // controller light
        $('.tab-control-light .tab-control-light-data').each(function(){
            $('#lightgallery-tab-'+$(this).data('light')).lightGallery({
              pager: true
            });
        });
    });

	// Triger click tab popup
	$('#user-form a').click(function(){
	    $('#user-tab a[href="'+$(this).attr('href')+'"]').trigger('click');
	});
	$('a[href="#Login_extend"]').click(function(){
	    $('#user-tab a[href="#Login"]').trigger('click');
	});
	$('a[href="#popup-forgot-extends"]').click(function(){
		$('#user-tab a[href="#popup-forgot"]').trigger('click');
	});

    // Trigger click menu sub-page
    var group_id = $('input[name="input_group_id"]').val();
    if ( typeof group_id != "undefined" ) {

        var parent_group_id = $('a[data-group-id="'+group_id+'"]').data('parent-group-id');
        if ( typeof parent_group_id != "undefined" ) {
            $('a[data-group-expand="'+parent_group_id+'"]').trigger('click');
        } else {
            $('a[data-group-expand="'+group_id+'"]').trigger('click');
        }
    }
    // End trigger click menu

    // Trigger click first tab product when load
    $('#myTabs1-home li a').first().trigger('click');

	// Validate From
    // Send news letter
	$("#send_newsletter").validate({
        submit: {
            settings: {
                button: ".btn_send_newsletter",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
            },
            callback: {
                onSubmit: function (node, formdata) 
                {
                    var url_send = $(node).attr("action");
                    var email = $("input[name='newsletter_email']").val();
                    // console.log(url_send);
                    $.ajax({
                        type: "post",
                        url: url_send,
                        data: formdata,
                        success: function(html)
                        {
                            var obj = JSON.parse(html);
                            call_notify("Notification", obj.message, obj.status);
                            $("input[name='newsletter_email']").val("");

                            // An form
                            if(obj.status == "success")
                            {
                                $(".newsletter_v1_inner").html('<h2 class="newsletter_tile" style="text-align:center">Thanks for subscribing!</h2>');
                            }
                        }
                    });
                }// End on before submit
            }
        }
    });

    // Send contact
    $("#send_contact").validate({
        submit: {
            settings: {
                button: ".btn_contact",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
            }
        }
    });

    // Send register
    $("#form-register").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });
    $("#form-register-header").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });

    // Send login
    $("#form-login").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });
    $("#form-login-header").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });

    // Send change password
    $("#form-changepwd").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });
    $("#form-changepwd-header").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });

    // Send password reset
    $("#form-forgot-pwd").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });
    $("#form-forgot-pwd-header").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',

            }
        }
    });

    // Check out payment
    $('form#payment').validate({
        submit: {
            settings: {
                clear: 'keypress',
                display: "inline",
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
            }
        }
    });

    // SEARCH BY PRICE
    $('input[name="slider_price_from_to"]').slider({
        from: 0, 
        to: 30000000, 
        step: 500, 
        smooth: true, 
        round: 0, 
        dimension: "&nbsp;vnđ", 
        skin: "plastic",
        onstatechange: function( value ){
            value = value.split(';');
            $('input[name="price_from"]').val(value[0]);
            $('input[name="price_to"]').val(value[1]);
        }
    });
    // END SEARCH BY PRICE

    // SOCIAL FAN PAGE
    $(window).on('load', function(){

        // use for load and resize
        function load_social ( social_block_width ) {

            // <!-- facebook fanpage -->
            if ( typeof facebook_id_fanpage != 'undefined' && facebook_id_fanpage ) {
                $('#fanpage_fb_container').html('<iframe src="https://www.facebook.com/plugins/page.php?href='+facebook_id_fanpage+'&width='+social_block_width+'&height='+( social_block_width - 20 )+'&tabs=timeline&hide_cover=false&show_facepile=true&hide_cta=false&small_header=true&adapt_container_width=false&appId" width="'+social_block_width+'" height="'+( social_block_width - 20 )+'" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>');
            }

            // <!-- google fanpage -->
            if ( typeof google_id_fanpage != 'undefined' && google_id_fanpage ) { 
                $('#fanpage_google_container').html('<div class="g-page" data-href="'+google_id_fanpage+'" data-width="'+social_block_width+'"></div><script src="https://apis.google.com/js/platform.js" async defer><\/script>');
            }

            // <!-- twitter fanpage -->
            $('#fanpage_twitter_container').html(''); // clear content
            if ( typeof twitter_id_fanpage != 'undefined' && twitter_id_fanpage ) { 
                twitter_id_fanpage = twitter_id_fanpage.split('/');
                for ( var i = twitter_id_fanpage.length - 1; i >= 0; i -= 1 ) {
                    if ( twitter_id_fanpage[i] != '' ) {
                        twitter_id_fanpage = twitter_id_fanpage[i];
                        break;
                    }
                }
                if ( typeof twttr != 'undefined' )
                {
                  twttr.widgets.createTweet(twitter_id_fanpage,document.getElementById('fanpage_twitter_container'),{width:social_block_width});
                }
            }
        }

        // calculator width
        var social_block_width = $('#social_block_width').width();
        if ( social_block_width <= 0 ) {
            social_block_width = 200;
        } else if ( social_block_width > 450 ) { social_block_width = 450; }

        // load facebook and google fanpage
        load_social(social_block_width);

        // When resize then reload social
        $(window).on('resize', function(){

            // Firing resize event only when resizing is finished
            clearTimeout(window.resizedFinished);
            window.resizedFinished = setTimeout(function(){

                // re-calculator width
                var social_block_width = $('#social_block_width').width();
                if ( social_block_width > 450 ) { social_block_width = 450; }

                // re-load facebook and google fanpage
                load_social(social_block_width);
            }, 250);
            
            // console.log('on resize:'+social_block_width+'px');
        });
    });
    // END SOCIAL FAN PAGE
});  

// check form
    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/security/create",
            success: function(token)
            {
                $("form").each(function(){
                    $(this).prepend("<input type='hidden' name='token' value='"+token+"' />");
                });
            }
        });
    });


function getDistrict(onthis, id_output)
{
    var city_id = $(onthis).val();

    $.ajax({
        type: "post",
        url: "/payment/getdistrict",
        data: {city_id: city_id},
        success: function(html)
        {
            // console.log(html);
            var obj = JSON.parse(html);
            var option = '<option>-- Vui lòng chọn Quận/Huyện --</option>';
            for(var x in obj)
            {
                option += `<option value="`+obj[x].district_id+`">`+obj[x].district_type+` `+obj[x].district_name+`</option>`;
            }

            $(id_output).html(option);
        }
    })
}

function applyDiscountCode()
{
    $("#loader_discount_code").show();
    $("#enter_discount_code").hide();
    $("#cart_discount_code").prop("disabled", true);

    let code =  $("#cart_discount_code").val();
    $.ajax({
        url: "/payment/discount_code/",
        data: {"code": code},
        dataType: "json",
        success: function(res){

            $("#loader_discount_code").hide();
            $("#enter_discount_code").show();
            $("#cart_discount_code").prop("disabled", false);

            if(res.status == 'ok')
            {
                $("#discount_code_input").hide();
                $("#discount_code_info").show();
                $("#cart_discount_code_text").text(res.code_data.code);
                $("#cart_discount_code_value").text(res.cart_data[5]);
                $("#cart_subtotal").text(res.cart_data[2]);
                $("#cart_payment_total").text(res.cart_data[3]);
                $("#cart_tax").text(res.cart_data[1]);
            }
            else
            {
                call_notify("Alert",res.msg,"error");
            }
        }
    })
}

function removeDiscountCode()
{
    $.ajax({
        url: "/payment/remove_code/",
        dataType: "json",
        success: function(res){
            if(res.status == 'ok')
            {
                $("#discount_code_input").show();
                $("#discount_code_info").hide();
                $("#cart_discount_code_text").text("");
                $("#cart_discount_code_value").text(res.cart_data[5]);
                $("#cart_subtotal").text(res.cart_data[2]);
                $("#cart_payment_total").text(res.cart_data[3]);
                $("#cart_tax").text(res.cart_data[1]);
            }
            else
            {
                call_notify("Alert",res.msg,"error");
            }
        }
    })
}

