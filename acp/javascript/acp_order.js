$(document).ready(function(){
  $("select.auto_select2").each(function(){
      var val_default = $(this).attr("defaultvalue");
      var obj = $(this);
      setTimeout( function() {
        obj.find("option[value='"+val_default+"']").prop("selected",true);
      },1200);
  });

  // Change type discount
  // $(".b_gen").click(function(){
  //     var type = $(this).attr("value");
  //     $(".b_gen").removeClass("active");
  //     $(this).addClass("active");
  //     $("#total_discount_type").val(type);
  //     $("#total_discount_type").trigger("change");
  //     // Dùng chung với product
  //     $("#product_commission_type").val(type);
  // });


});

function changeType(obj, ele_gen, ele_result)
{
    var type = $(obj).attr("value");
    $(ele_gen).removeClass("active");
    $(obj).addClass("active");
    $(ele_result).val(type);
    $(ele_result).trigger("change");
}

function getaddressbycus(cus_id, element)
{
    $.ajax({
      type: "post",
      url: site_root_domain+"/?site=order&subact=getshipbiladdress",
      data: {cus_id: cus_id},
      success: function(response)
      {
        // console.log(response);
        var html_build = "";
        var obj = JSON.parse(response);
        for(var x in obj)
        {
          html_build += "<option value='"+obj[x].addr_id+"'>"+obj[x].addr_address_full+"</option>";
        }
        $(element).html("<option value>-- None --</option>"+html_build);
        $(".apply_id").attr("cus_id", cus_id);
      }
    })
}

function getDistrict(onthis, id_output)
{
    var city_id = $(onthis).val();

    $.ajax({
        type: "post",
        url: site_root_domain+"/?site=order&subact=getdistrict",
        data: {city_id: city_id},
        success: function(html)
        {
            $(id_output).html(html);
        }
    })
}

function applyInformation(onthis)
{
  var addr_id = parseInt($(onthis).val());
  var key = $(onthis).attr("type");
  var type = key == "bill" ? "billing" : "shipping";
  var cus_id = parseInt($(onthis).attr("cus_id")); 
  $.ajax({
      type: "post",
      url: site_root_domain + "/?site=order&subact=getdatashipbill",
      data: {addr_id: addr_id, type: type, cus_id: cus_id},
      success: function(response)
      {
        // apply information
        var obj = JSON.parse(response);

        $("[name='"+key+"_first_name']").val(obj.addr_first_name).attr('value', obj.addr_first_name);
        $("[name='"+key+"_last_name']").val(obj.addr_last_name).attr('value', obj.addr_last_name);
        $("[name='"+key+"_email']").val(obj.addr_email).attr('value', obj.addr_email);
        $("[name='"+key+"_phone']").val(obj.addr_phone).attr('value', obj.addr_phone);
        $("[name='"+key+"_company']").val(obj.addr_company).attr('value', obj.addr_company);
        $("[name='"+key+"_address']").val(obj.addr_address).attr('value', obj.addr_address);
        $("[name='"+key+"_address2']").val(obj.addr_address2).attr('value', obj.addr_address2);
        $("[name='"+key+"_city']").val(obj.addr_city_name).attr('value', obj.addr_city_name);
        $("[name='"+key+"_state'] option[value='"+obj.addr_province+"']").prop("selected", true).trigger('change');
        $("[name='"+key+"_province']").val(obj.addr_province_name).attr('value', obj.addr_province_name);
        $("[name='"+key+"_zipcode']").val(obj.addr_zipcode).attr('value', obj.addr_zipcode);
        $("[name='"+key+"_country'] option[value='"+obj.addr_country+"']").prop("selected", true).trigger('change');
        
        // $("[name='"+key+"_first_name']").val(obj.first_name);
        // $("[name='"+key+"_last_name']").val(obj.last_name);
        // $("[name='"+key+"_email']").val(obj.email);
        // $("[name='"+key+"_phone']").val(obj.phone);
        // $("[name='"+key+"_address']").val(obj.address);
        // $("[name='"+key+"_zipcode']").val(obj.zipcode);
        // $("[name='"+key+"_city'] option[value='"+obj.city+"']").prop("selected", true);
        // $("[name='"+key+"_city']").trigger("change");
        // $("[name='"+key+"_district']").attr("defaultvalue", obj.district);
        
        // setTimeout( function() {
        //   var val_default = obj.district;
        //   $("[name='"+key+"_district']").find("option[value='"+val_default+"']").prop("selected",true);
        // },1000);
      }
  });
}


function changestatus(obj, ele_result)
{
  var ordstatus = $(obj).val();
  var valdefault = $(obj).attr("defaultvalue");
  // Check insert comment
  if(ordstatus != valdefault)
  {
    $("input[name='on_comment']").val(1);
  }else
  {
    $("input[name='on_comment']").val(0);
  }

  $.ajax({
    type: "post",
    url: "/acp/?site=order&subact=getstatus",
    data: {ordstatus:ordstatus},
    success: function(response)
    {
      $(ele_result).find("select").html("");
      if(response)
      {
        $(ele_result).show();
        var data = JSON.parse(response);
        var option_html = "<option value=''>-- Choose status extension --</option>";
        for(var x in data)
        {
            option_html += "<option value='"+data[x].status_id+"'>"+data[x].status_name+"</option>";
        }

        $(ele_result).find("select").html(option_html);
      }else
      {
        $(ele_result).hide();
      }
    }
  });
}


function changecustomstatus(obj, ele_result)
{
  var ordstatus = $(obj).val();
  var valdefault = $(obj).attr("defaultvalue");
  // Check insert comment
  if(ordstatus != valdefault)
  {
    $(ele_result).val(1);
  }else
  {
    $(ele_result).val(0);
  }
}


function changeCusStatus(obj, ele_result)
{
  var ordstatus = $(obj).val();
  var data_json = $(obj).attr("data-json");
  var html_option = "<option value=''>"+cms_lang['title_custom_status'] +"</option>";
  if(data_json)
  {
    var json = JSON.parse(data_json);
    for(var x in json)
    {
      if(x == ordstatus)
      {
        for(var j in json[x])
        {
          var dataStatus = json[x];
          html_option += '<option value="'+dataStatus[j]+'">'+cms_lang['title_custom_status_'+dataStatus[j]] +'</option>';

        }
      }
    }
  }

  $(ele_result).html(html_option);
  
}

function trigger_tab(obj)
{
      var active_tab = $(obj).attr("tab_change");
      $("a.nav-link[href='"+active_tab+"']").trigger("click");
}

function renderUrl(obj)
{
  var curr_link = window.location.href;
  var tab = $(obj).attr("href");
  tab = tab.replace("#","");
  var base_link = curr_link.split('&tab')[0];
  base_link = base_link+"&tab="+tab;
  //rewrite link
  if (window.history.replaceState && base_link) 
  {
    //prevents browser from storing history with each change:
    window.history.replaceState("", "Filter", base_link);
  }

  $('[name="tab"]').val(tab).attr('value', tab);
}


