function add_term()
{
    // $("form#add_term_form")[0].reset();
    clearForm($("form#add_term_form"));
    $("form#add_term_form [name='term_id']").val("");
    $("p.shimanu_error_msg").html("");
    $(".title_add_transaction_terms").html(cms_lang.add_form);
    $("#box_add_transaction_terms .change_action_transaction_terms").html('<input class="btn btn_add_term" type="button" value="' + cms_lang.add_submit + '">');

    $.magnificPopup.open({
        type: 'inline',
        items: {
            src: '#box_add_transaction_terms'
        },
    });
}

function edit_term(term_id)
{
    // $("form#add_term_form")[0].reset();
    clearForm($("form#add_term_form"));
    $("p.shimanu_error_msg").html("");
    $.ajax({
        type: "post",
        url: site_root_domain + "/?site=transaction_terms&subact=ajax_get_data_transaction_terms",
        data: {id: term_id},
        dataType: 'json',
        success: function(obj)
        {
            if(obj.status == 'success')
            {
                $("#box_add_transaction_terms .title_add_transaction_terms").html(cms_lang.edit_form);
                $("#box_add_transaction_terms .change_action_transaction_terms").html('<input class="btn btn_edit_term" type="button" value="' + cms_lang.edit_submit + '">');
                // console.log(html);
                // console.log(obj);
                $("#add_term_form input[name='term_id']").val(obj.data.term_id);
                $("#add_term_form input[name='term_days']").val(obj.data.term_days);
                $("#add_term_form input[name='term_name']").val(obj.data.term_name);
                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#box_add_transaction_terms'
                    },
                });
            }
            else
            {
                alert(cms_lang.no_data)
            }
        }
    });

}


$(document).ready(() => {
    var validator = $("form#add_term_form").validate({
        focusInvalid: true,
        rules: {
            // simple rule, converted to {required:true}
            term_days: "required",

        },
        messages:{
            term_days: cms_lang.incomplete_term_days,
        }
    });

    $("#add_term_form").on("click",".btn_add_term", function(){

        if($("form#add_term_form").validnew()){

            var data = $("form#add_term_form").serialize();
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=transaction_terms&ajax=1&act=add_do",
                data: data,
                dataType: 'json',
                success: function(obj)
                {
                    csrf_token();

                    if(obj.status == "success")
                    {
                        if(typeof pFrmType != 'undefined' && pFrmType =="transaction")
                        {
                            $("#trx_terms").val(obj.days).trigger("change");
                            $("#trx_term_name").val(obj.name);
                            $.magnificPopup.close();
                            alertText(obj.msg, "success");
                            return true;
                        }
                        else
                        {
                            window.location = site_root_domain+"/?site=transaction_terms";
                        }
                    }
                    else
                    {
                        pNotifyACP(obj.msg)
                        /*$("p.shimanu_error_msg").html(obj.msg);
                        $("p.shimanu_error_msg").css("color","red");
                        $("p.shimanu_error_msg").css("font-size","14px");
                        $("p.shimanu_error_msg").show();

                        setTimeout(function(){ $("p.shimanu_error_msg").fadeOut();},2000);*/
                        return false;
                    }
                }

            });

        }else {
            // do stuff if form is not valid
            validator.focusInvalid();
            return false;
        }
    });

    $("#add_term_form").on("click",".btn_edit_term", function(){

        if($("form#add_term_form").validnew()){

            var data = $("form#add_term_form").serialize();


            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=transaction_terms&act=edit_do&ajax=1",
                data: data,
                dataType: 'json',
                success: function(obj)
                {
                    csrf_token();

                    if(obj.status == "success")
                    {
                        if(typeof pFrmType != 'undefined' && pFrmType =="transaction")
                        {
                            $("#trx_terms").val(obj.days).trigger("change");
                            $("#trx_term_name").val(obj.name);
                            $.magnificPopup.close();
                            alertText(obj.msg, "success");
                            return true;
                        }
                        else
                        {
                            window.location = site_root_domain+"/?site=transaction_terms";
                        }
                    }
                    else
                    {
                        pNotifyACP(obj.msg)
                        /*$("p.shimanu_error_msg").html(obj.msg);
                        $("p.shimanu_error_msg").css("color","red");
                        $("p.shimanu_error_msg").css("font-size","14px");
                        $("p.shimanu_error_msg").show();

                        setTimeout(function(){ $("p.shimanu_error_msg").fadeOut();},2000);*/
                        return false;
                    }
                }

            });

        }else {
            // do stuff if form is not valid
            validator.focusInvalid();
            return false;
        }
    });
})

