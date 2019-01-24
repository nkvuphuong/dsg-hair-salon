
function change_cus_type(cus_type) {
    if (cus_type > 0) {
        $(".cus_type_1").show();
        //$(".cus_type_1").find("input, select, textarea").attr("emsg", function () {
            //return $(this).attr("emsg_bk");
        //});
    }
    else {
        $(".cus_type_1").hide();
        $(".cus_type_1").find("input, select, textarea").attr("emsg", "");
    } //
}

function change_map_location(address_input) {
    var address_name = "";
    if (address_input.length > 0) {
        address_name = address_input;
    }
    else {
        var country_name = trim($(".country_select option:selected").text());
        var city_name = trim($(".city_select option:selected").text());
        var district_name = trim($(".district_select option:selected").text());
        var town_name = trim($(".town_select option:selected").text());


        if (town_name.length > 0 && $(".town_select").val() > 0) {
            address_name += town_name + ",";
        }

        if (district_name.length > 0 && $(".district_select").val() > 0) {
            address_name += district_name + ",";
        }

        if (city_name.length > 0 && $(".city_select").val() > 0) {
            address_name += city_name + ",";
        }

        if (country_name.length > 0 && $(".country_select").val() > 0) {
            address_name += country_name + ",";
        }

        address_name = trim(address_name, ",");
    }


    if (address_name.length > 0) {
        $("#pac-input").val(address_name).focus();
    }

}

$('#add_customer').click(function (e) {
    var input = $('#customers_input').val();
    var cnt = $("li.customer").size();
    $("#list_customer").append('<li class="customer" onClick="return del_customers(' + cnt + ');"  id="customers_' + cnt + '">' + input + ' <input type="hidden" name="customers_id[]" value="' + input + '"/><a class="close" href="javascript: void();">close</a></li>');
    $("#wrap_customers").hide();
    $("#customers_input").val("");

});

function load_customer_autocomplete(inputID, wrapID, urlGetData) {
     
    $('#'+inputID).keyup(function (e) {
        clearTimeout($.data(this, 'timer'));
        
       
        if (e.keyCode == 13)
            search(true);
        else
            $(this).data('timer', setTimeout(search, 100));
    });
    function search(force) {
        
        var customers_id = $('#'+inputID).val();

        var customers_id = $.trim(customers_id);
        var cat_id;
        var cnt = 0;
        
        
        if (!force && customers_id.length < 2) {
            $("#"+wrapID).hide();
            return; //wasn't enter, not > 2 char
        }
        
        

        $.ajax({
            type: "POST",
            url: urlGetData,
            data: "keyword="+customers_id,
            success: function (data) {
                $("#"+wrapID).html(data);
                $("#"+wrapID).show();

            }
        });
    }

    return false;

};


function add_customers_temp(id, name) {
    var cnt = $("li.customer").size();
    $("#list_customer").append('<li class="customer" onClick="return del_customers(' + cnt + ');"  id="customers_' + cnt + '">' + name + ' <input type="hidden" name="customers_id[]" value="' + name + '"/><a class="close" href="javascript: void();">close</a></li>');
    $("#wrap_customers").hide();
    $("#customers_input").val("");
}

function del_customers(id) {
    $("#customers_" + id).remove();
}

$("body").click(function () {
    $("#wrap_customers").hide();
    $("#customers_input").val("");
});

$("#wrap_customers").click(function (e) {
    e.stopPropagation();
});

$("input[name='cus_username']").keypress( function(e) {
    var valid = (e.which >= 48 && e.which <= 57) || (e.which >= 65 && e.which <= 90) || (e.which >= 97 && e.which <= 122);
    if (!valid) {
        e.preventDefault();
    }
});

  $("input[name='cus_name_display']").keypress(function(event)
  {
      if(event.which == 32)
      {
        return false;
      }
  });

