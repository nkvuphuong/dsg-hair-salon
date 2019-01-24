function autocomplete_quick_search()
{
    $("#p_quick_search").keyup(function(){

        if($(this).val().length < 2)
        {
            return false;
        }
        var key = $(this).val();
        $.ajax({
            type: "get",
            url: site_root_domain+"/?site=smstpl&subact=autocomplete",
            data:{ keyword : key},
            beforeSend: function(){
                $("#p_quick_search").css("background","#FFF url(/acp/images/fb-loading.gif) no-repeat 165px");
            },
            success: function(data){
                var obj = JSON.parse(data);
                $("#suggesstion-box").html("");
                if(obj.status == "success")
                {
                    output_li = "<ul class='list_goods'>"+obj.data_option+"</ul>"
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(output_li);
                    $("#p_quick_search").css("background","#FFF");

                }
                else
                {
                    $("#suggesstion-box").hide();
                    $("#suggesstion-box").html("");
                    $("#p_quick_search").css("background","#FFF");
                }

            }
        });
    }).blur(() => {
        setTimeout(() => {
            $("#suggesstion-box").hide();
        },100)
    });
}

function select2Required(defaultValue = [])
{
    let tagSelector = $(".tag-selector").select2({
        tags: true,
        tokenSeparators: [',', ' ',';']
    });

    try {
        defaultValue = $.parseJSON(defaultValue);
        console.log(defaultValue);
        if(defaultValue.length)
        {
            $.each(defaultValue, (k, v) => {
                if(!tagSelector.find('option:contains(' + v + ')').length)
                    tagSelector.append($('<option>').text(v));
            });

            tagSelector.val(defaultValue).trigger("change");
        }
    }
    catch (e) {
        return false;
    }
}