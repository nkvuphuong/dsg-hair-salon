 function add_shipment()
 {  
    // $("form#add_shipment_form")[0].reset();
    clearForm($("form#add_shipment_form"));
    $("p.shimanu_error_msg").html("");
    $(".title_add_shipment").html(cms_lang['gshipment_add']);
    $("#box_add_shipment .change_action_shipment").html('<input class="btn act_popup_shipment_btn" aclass="btn_add_shipment" type="button" value="'+cms_lang['gshipment_add']+'">');
    validate_form_custom("#box_add_shipment",".act_popup_shipment_btn","box_custom");
     $.magnificPopup.open({
        type: 'inline',
        items: {
          src: '#box_add_shipment'
        },
      });
 }

 function edit_shipment(shi_id)
 {
    // $("form#add_shipment_form")[0].reset();
    clearForm($("form#add_shipment_form"));
    $("p.shimanu_error_msg").html("");
     $.ajax({
            type: "post",
            url: site_root_domain + "/?site=shipment&subact=ajax_get_data_shipment",
            data: {id: shi_id},
            success: function(html)
            {   
                $("#box_add_shipment .title_add_shipment").html(cms_lang['gshipment_edit']);
                $("#box_add_shipment .change_action_shipment").html('<input class="btn act_popup_shipment_btn" aclass="btn_edit_shipment" type="button" value="'+cms_lang['gshipment_edit']+'">');
                 validate_form_custom("#box_add_shipment",".act_popup_shipment_btn","box_custom");

                var obj = JSON.parse(html);
                // console.log(obj);
               $("#add_shipment_form input[name='shi_id']").val(obj.data.shi_id);

                $("#add_shipment_form input[name='shi_name']").val(obj.data.shi_name);
                $("#add_shipment_form textarea[name='shi_description']").html(obj.data.shi_description);
                
                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                      src: '#box_add_shipment'
                    },
                  });
            }
    });
            
 }


 

 
$("#add_shipment_form").on("click",".btn_add_shipment", function(){
 
           
            var data = $("form#add_shipment_form").serialize();
            var form_data = new FormData();
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=shipment&act=add&"+data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(html)
                {
                     var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                       //alertText(obj.msg, "success");
                       window.location = site_root_domain+"/?site=shipment";
                    }
                    else
                    {
                        $("p.shimanu_error_msg").html(obj.msg);
                        $("p.shimanu_error_msg").css("color","red");
                        $("p.shimanu_error_msg").css("font-size","14px");
                        $("p.shimanu_error_msg").show();

                        setTimeout(function(){ $("p.shimanu_error_msg").fadeOut();},2000);
                        return false;
                    }
                }

            });
                
   
});
        
 $("#add_shipment_form").on("click",".btn_edit_shipment", function(){
 
            $("#shipment_submit").trigger("click");
            var data = $("form#add_shipment_form").serialize();
            var form_data = new FormData();
          

            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=shipment&act=edit&"+data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(html)
                {
                     var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                       //alertText(obj.msg, "success");
                       window.location = site_root_domain+"/?site=shipment";
                    }
                    else
                    {
                        $("p.shimanu_error_msg").html(obj.msg);
                        $("p.shimanu_error_msg").css("color","red");
                        $("p.shimanu_error_msg").css("font-size","14px");
                        $("p.shimanu_error_msg").show();

                        setTimeout(function(){ $("p.shimanu_error_msg").fadeOut();},2000);
                        return false;
                    }
                }

            });
                
  
});