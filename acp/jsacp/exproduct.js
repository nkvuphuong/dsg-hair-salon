var currentColorPickerInput = null;

$(document).ready(function(){
    $(".choose_color").ColorPicker({
        onSubmit: function(hsb, hex, rgb, el) {
            $(el).val(hex);
            $(el).ColorPickerHide();
        },
        onBeforeShow: function () {
            $(this).ColorPickerSetColor("#"+this.value);
        },onChange: function (hsb, hex, rgb, el) {
            currentColorPickerInput.val(hex);
            $('.ex_color').css('backgroundColor', '#' + hex);
        }
    }).bind('keyup', function(){
        $(this).ColorPickerSetColor("#"+this.value);
    });

    $("#exproduct").on("click",".choose_color",(function(){
        currentColorPickerInput = $(this);
    })).on("focus",".choose_color",(function(){
        currentColorPickerInput = $(this);
    }))

    checkedAttr();
    validateFrm("exproduct");
    $("#lstSize, #lstColor").find("input.checked-attr").trigger("change");
})

function addAttribute(type)
{
    var data = {};
    if(type==1)
    {
        var templateRow = jQuery.validator.format($.trim($("#template-row-color").val()));
        data = {
            'attr_name' : $("#exproduct [name='attr_color_name']").val(),
            'attr_color' : $("#exproduct [name='attr_color']").val(),
            'attr_group' : type,
        };
    }
    else if(type==2)
    {
        var templateRow = jQuery.validator.format($.trim($("#template-row-size").val()));
        data = {
            'attr_name' : $("#exproduct [name='attr_size_name']").val(),
            'attr_size' : $("#exproduct [name='attr_size']").val(),
            'attr_price' : $("#exproduct [name='attr_price']").val(),
            'attr_group' : type,
        };
    }

    $.ajax({
        'method': 'post',
        'url': site_root_domain + "/?site=attribute&act=add_do&is_ajax=1",
        'data': data,
        'dataType': 'json',
        'success': function (response) {
            if(response.status == 'ok')
            {
                if(type==1)
                {
                    $("#lstColor").append(templateRow(response.data.attr_id, response.data.attr_name, response.data.attr_value));
                    $("#exproduct [name='attr_color_name']").val("");
                    $("#exproduct [name='attr_color']").val("");

                    var objDiv = document.getElementById("lstColor");
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
                else if(type==2)
                {
                    $("#lstSize").append(templateRow(response.data.attr_id, response.data.attr_name, response.data.attr_value));
                    $("#exproduct [name='attr_size_name']").val("");
                    $("#exproduct [name='attr_size']").val("");
                    $("#exproduct [name='attr_price']").val("");

                    var objDiv = document.getElementById("lstSize");
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            }
            else
            {
                alert(response.msg);
            }
        }
    })
}

function checkedAttr() {
        $("#exproduct").on('change', '.checked-attr', function(){
            var id = $(this).attr('id');
            if($(this).is(':checked'))
            {
                $("[id='default_" + id + "']").prop('disabled', false);
            }
            else
            {
                $("[id='default_" + id + "']").prop('disabled', true).prop('checked', false);
            }
        })
}

function validateFrm(frmID)
{
    $("#"+frmID).validate({
        rules: {
            "exp_name" : {
                required: true,
            },
            "colors[]" : {
                required: true,
            },
            "sizes[]" : {
                required: true,
            },
            "exp_status" : {
                required: true,
            },
        },
        messages: {
            "colors[]" : {
                required: cms_lang.emsg_group_attr,
            },
            "sizes[]" : {
                required: cms_lang.emsg_group_attr_size,
            },
            "exp_name" : {
                required: cms_lang.emsg_exp_name,
            },
            "exp_status" : {
                required: cms_lang.emsg_exp_status,
            },
        },
        errorPlacement: function(error, element) {
           if(element.attr("class")=="checked-attr error")
           {
               error.insertAfter(element.parents("ul.row:first").parents("div:first"));
           }
           else
           {
               error.insertAfter(element);
           }
        },
        submitHandler: function(form) {
            $(form).ajaxSubmit();
        }
    });
}

function checkAllAttr(obj)
{
   var status = obj.is(":checked");

  obj.parents("ul:first").parents("div:first").find("input.checked-attr").prop("checked", status).trigger("change");
}