$(document).ready(function(){
    // Check discount
    $("#checkdiscount").click(function(){
        var data_form = $("#booking_form").serializeArray(); 
        data_form.push({ name: "discount_code", value: 123 });
 
        $.ajax({
            type: "POST",
            data: data_form,
            url: "/payment/discount_code_dsg",
            success: function(data)
            {   
                 alert(data);
                $('#userError').html(data);
                $("#userError").html(userChar);
                $("#userError").html(userTaken);
            }
        });
    });

    $("#hair_type").on("change",function(){

        var svc_gid = $(this).val();
        $(".book_svgroup").hide();
        $("#sg_"+svc_gid).show();
        $("#output_total").html("0 đ");
        $("#show_svc_choice").html("");
        $(".list_service_dsg").removeClass("active");
        $("input[name='product_id[]']").removeAttr("value");
    });

     
    // Validate
    $('#booking_form').validate({
        submit: {
            settings: {
                button: ".btn_send_appointment",
                inputContainer: 'form-group',
                errorListClass: 'form-tooltip-error',
            },
            callback: {
                onSubmit: function(node, formdata) {
                    if(enableRecaptcha)
                    {
                        var check_google = $("#g-recaptcha-response").val();
                        if(typeof(check_google) != "undefined" && check_google != "")
                        {
                            $(".btn_send_appointment").attr("disabled", "disabled");
                            node[0].submit();
                        }
                    }
                    else
                    {
                        $(".btn_send_appointment").attr("disabled", "disabled");
                        node[0].submit();
                    }
                    return false;
                },
                onError: function (node, globalError) {
                    $("#booking_form .scroll_jumpto").trigger('click');
                }
            }
        }
    });
});
 
    function formatNumberInput(numberinput, dec_number)
      {
        numberinput = parseFloat(numberinput);
        //Check input num is NaN
        if(isNaN(numberinput)) {
          numberinput = 0;
        }

        //dec_number: Phần thập phân
        dec_number = !isNaN(dec_number) || typeof(dec_number) !== "undefined" ? parseInt(dec_number) : 0;
        if(currency_type == "$")
        {
         return "$ "+numberinput.toFixed(2).replace(/./g, function(c, i, a) {
            return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
            });
        }
        else
        { 
           return numberinput.toFixed(dec_number).replace(/./g, function(c, i, a) {
            return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
            })  + " đ ";
        }
      }
    $(document).ready(function(){
        cal_book_price();
    });

    $("#city_id").on('change',function(){
        var city_id = $("#city_id").val();
        get_storebycity(city_id);
    });
    
    
    function get_storebycity(id)
    {
        $("#liststore").html("");
        $.ajax({
          type: "post",
          url: "/salon/optionstore/id-"+id,
          data: {},
          success: function(response)
          {
              $("#choose_store").html(response);
          }
        });
    }
 

    $( ".list_service_dsg" ).toggle(function() {
        var other = $(this).attr('other');
        if(other == 1)
        {
            // $(".list_service_dsg[other='0']").removeClass('active');
        }else
        {
            //$(".list_service_dsg[other='1']").removeClass('active');
        }
        $(this).addClass('active');
        var svc_id = $(this).attr("service_id");
        $("#product_hidden_"+svc_id).val(svc_id);
        cal_book_price();

    }, function() {
        $(this).removeClass('active');
        var svc_id = $(this).attr("service_id");
        $("#product_hidden_"+svc_id).val("");
        cal_book_price();
    });


    $("select[name='staff_type'],select[name='cosmetic'],select[name='hair_length']").on("change",function(){
        cal_book_price();
    });
 
    function cal_book_price()
    {
        // Dich vu
        var price_svc = 0;
        var flag_other = 0;
        var cosmetic = 0;
        var svc_choice = "";
        var svc_html ;
        var flag_seniority = flag_cosmetic = flag_hair_length = flag_svc_length = 0;
        cosmetic = $("select[name='cosmetic'] option:selected").val();
        hair_length = $("select[name='hair_length'] option:selected").val();

        var seniority = $("select[name='staff_type'] option:selected").attr('seniority');
        $( "a.list_service_dsg.active" ).each(function( index ) {
            item_price_svc = $(this).attr("price");
        
            var other = $(this).attr('other');
            
            if(item_price_svc >= 100000)
            {
                flag_svc_length++;
            }
            svc_html = $(this).html();
            var svc_html_spit = svc_html.split(":");
            svc_choice += "<h5>- "+svc_html_spit['0']+": " +formatNumberInput(item_price_svc)+"</h5>";
            if(other == 0)
            {   
                if(seniority == 1){
                    flag_seniority++;
                    //item_price_svc = parseFloat(item_price_svc) + parseFloat(price_staff_advance);
                }
                if(item_price_svc >= 200000 && cosmetic == 2){
                    flag_cosmetic++;
                    item_price_svc  =  parseFloat(item_price_svc) + parseFloat(price_comestic_advance);
                    svc_choice += "<h5>+ Gói cao cấp : " +formatNumberInput(100000)+"</h5>";
                }
                if(item_price_svc >= 200000 && hair_length == 2)
                {
                    item_price_svc = parseFloat(item_price_svc) + parseFloat(price_hairlength_advance);
                    svc_choice += "<h5>+ Tóc nhiều : " +formatNumberInput(100000)+"</h5>";
                }
            }
            price_svc =  parseFloat(item_price_svc) + parseFloat(price_svc);  
           
        }); 
        



        if(flag_seniority >= 1)
        {
            // price_svc =   parseFloat(price_svc) + parseFloat(price_staff_advance);  
        }
        if(flag_cosmetic >= 1)
        {
             price_svc =    parseFloat(price_svc) + parseFloat(price_comestic_advance);  
        }
         
        
        if(cosmetic == 2){
           //  svc_choice += "<h5>- Gói cao cấp : " +formatNumberInput(200000)+"</h5>";
        }
        if(flag_svc_length >= 2)
        {
            price_svc_discount =     parseFloat(50000) * parseFloat(flag_svc_length);  
            
            price_svc =    parseFloat(price_svc) - parseFloat(price_svc_discount);  
            svc_choice += "<h5>- Khuyến mãi Combo : -" +formatNumberInput(price_svc_discount)+"</h5>";
        }

        $("#show_svc_choice").html(svc_choice);
        $("#output_total").html(formatNumberInput(price_svc));
    }

      