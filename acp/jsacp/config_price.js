function change_price(onthis,product_id)
{
	var price = $(onthis).find(":selected").attr("price");
    var price_output = formatNumberInput(parseInt(price));
    $("#price_default_"+product_id).html(price_output);
    $("input[name='p_price_"+product_id+"']").val($(onthis).val());
}


function calc_action(onthis,product_id)
{
	$(".calc_"+product_id).removeClass("btn-success").addClass("btn-default");
	$(onthis).addClass("btn-success");
	$("input[name='calc_type_"+product_id+"']").val($(onthis).attr("calc"));
}
function prevent_dropdown_cfprice(e)
{
	e.stopPropagation();
}



function calc_valueaction(onthis,product_id)
{
	$(".calc_valuetype_"+product_id).removeClass("btn-success").addClass("btn-default");
	$(onthis).addClass("btn-success");
	$("input[name='tmp_calc_price_input_"+product_id+"']").val(0);
	$("input[name='calc_price_input_"+product_id+"']").val(0);
	if($(onthis).attr("valuetype") == "%")
	{
		$("input[name='calc_price_input_"+product_id+"']").attr("maxlength","3");
		$("input[name='calc_price_input_"+product_id+"']").val($(onthis).attr("valuetype"));
	}
	else
	{
		$("input[name='calc_price_input_"+product_id+"']").attr("maxlength","9");
		$("input[name='calc_price_input_"+product_id+"']").val($(onthis).attr("valuetype"));
	}
}


function active_input_calc_price(onthis)
{
	$("input.inputdefault_calc_price").removeClass("active");
	$(onthis).addClass("active");
}


function tablesave_calc(onthis)
{
 

	$(".inputdefault_calc_price").each(function() {
	    // ...
	    if($(this).hasClass( "active" ))
	    {
	    	var def_value_price = $(this).attr("defaultvalue");
	    	var value = $(this).val();
	    	var product_id = $(this).attr("product_id");
	    	 
	    	// compare different value
	    	if(def_value_price != value && value != '')
	    	{
	    		// Ajax :save price
	    		$.ajax({
	                type: "post",
	                url: site_root_domain + "/?site=config_price&act=edit&subact=save_price_sell",
	                data: {product_id: product_id, product_price_sell: value},
	                success: function(html)
	                {
	                	var obj = JSON.parse(html);

                		if(obj.status == "success")
               			{
               				call_notify(cms_lang.title_config_price,obj.msg,"success");
               				 setTimeout(function() {
								  window.location.replace(site_root_domain+"/?site=config_price");
								}, 2000); 
               			}
               			else
               			{
               				call_notify(cms_lang.title_config_price,obj.msg,"warrning");
               			}
	                }
	            });

	    	}

	    }

	});

	
}



function save_config_price(onthis,product_id)
{
	var calc_price_type = $("input[name='p_price_"+product_id+"']").val();
	var calc_type = $("input[name='calc_type_"+product_id+"']").val();
	var calc_price =  $("input[name='calc_price_input_"+product_id+"']").val();
	var calc_valuetype = $("input[name='calc_valuetype_"+product_id+"']").val();

	//console.log("product_id:" +product_id +", calc_price_type:" +calc_price_type+", calc_type:"+ calc_type+", calc_price: "+calc_price+", calc_valuetype: "+calc_valuetype );
	//return false;
	$.ajax({
        type: "post",
        url: site_root_domain + "/?site=config_price&act=edit&subact=save_price",
        data: {product_id: product_id, calc_price_type: calc_price_type, calc_type: calc_type, calc_price: calc_price, calc_valuetype: calc_valuetype },
        success: function(html)
        {
        	var obj = JSON.parse(html);

    		if(obj.status == "success")
   			{
   				call_notify(cms_lang.title_config_price,obj.msg,"success");
   			}
   			else
   			{
   				call_notify(cms_lang.title_config_price,obj.msg,"warrning");
   			}

   			setTimeout(function() {
				  window.location.replace(site_root_domain+"/?site=config_price");
				}, 2000);
        }
    }); 



}


function check_enter_number_cprice( onthis, type = 0)
{
	  var product_id = $(onthis).attr("product_id");	
      var number = $(onthis).val();
      if($.isNumeric(number) == true)
      {   
          if(number < 0)
          {

            if(type == 1)
            {
              $(onthis).val(1);
            }
            else
            {
              $(onthis).val("");
            }
          }
          else
          {
              if(number.substring(0, 1) === '0')
              {
                if(type == 0)
                {
                   $(onthis).val(""); 
                }
                else
                {
                   $(onthis).val(1);                 
                } 
              }  
              else
              {
                 $(onthis).val(number);
                 $("input[name='calc_price_input_"+product_id+"']").val(number);

              }   
          }
         
      }else
      {
          if(type == 1)
          {
            $(onthis).val(1);
          }
          else
          {
            $(onthis).val("");
          }
      }
 
  }


  function check_enter_number_cfprice(evt, onthis)
  {
  	var product_id = $(onthis).attr("product_id");
    if(isNaN(onthis.value+""+String.fromCharCode(evt.charCode))) 
    {
       return false; 
    }
    else
    {
        $("input[name='calc_price_input_"+product_id+"']").val($(onthis).val());
    }
        
  }