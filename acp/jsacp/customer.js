
$(document).ready(function() {

    $('body').click(function() {
        $('#suggesstion-box').html("");
        $("#suggesstion-box").hide();

    });

    $('#suggesstion-box').click(function(event){
        event.stopPropagation();
    });

    autocomplete_quick_search();
});



function autocomplete_quick_search()
{

    $("#p_quick_search").keyup(function(){

        if($(this).val().length < 2)
        {
            return false;
        }
        var key = $(this).val();
        $.ajax({
            type: "post",
            url: site_root_domain+"/?site=customer&subact=autocomplete",
            data:{ keyword : key },
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
    });

}

function locationDependency(countryObj = $('.country_dependency'), cityObj = $('.city_dependency'), districtObj = $('.district_dependency'))
{
    if(cityObj.find("option").length<=1) cityObj.prop("disabled", true);
    if(districtObj.find("option").length<=1) districtObj.prop("disabled", true);

    cityObj.change(function(){
        districtObj.prop("disabled", true);
        districtObj.find("option:not(:first)").remove();
        districtObj.trigger("change");

        let id = $(this).val();

        if(id=='' || id == 0)
        {
            districtObj.trigger("change");
            return false;
        }

        $.ajax({
            url: site_root_domain + "/?subact=load_district_ajax&id="+id,
            success: function(res) {
                districtObj.prop("disabled", false);
                $.each(res, function(id, name){
                    districtObj.append(`<option value="${id}">${name}</option>`);
                })
            }
        })
    })

    countryObj.change(function(){
        cityObj.prop("disabled", true);
        cityObj.find("option:not(:first)").remove();
        cityObj.trigger("change");

        let id = $(this).val();

        if(id=='' || id == 0)
        {
            cityObj.trigger("change");
            return false;
        }

        $.ajax({
            url: site_root_domain + "/?subact=load_city_ajax&id="+id,
            dataType: 'json',
            success: function(res) {
                cityObj.prop("disabled", false);
                $.each(res, function(id, name){
                    cityObj.append(`<option value="${id}">${name}</option>`);
                })
            }
        })
    })
}

 