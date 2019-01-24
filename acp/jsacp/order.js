$(document).ready(function() {
	
    // initialize tooltipster on text input elements
       
    if($("#formorder-signin_v1 input[name='is_shipping']").length > 0)
    {
      if($("#formorder-signin_v1 input[name='is_shipping']").attr("defaultvalue") == 1)
      {
        $("#formorder-signin_v1 input[name='is_shipping']").prop("checked",true);
        $("#container_shipping_info").show();
      }
    }
  //Check chon service type
  if($("#formorder-signin_v1 input[name='service_type']").length > 0)
    { 
 
      if($("#formorder-signin_v1 input[name='service_type']:checked").val() == 1)
      {
 
        $(".block_service_type_1").show();
        $(".block_service_type_0").hide();
        
      }
      else
      {
        $(".block_service_type_1").hide();
        $(".block_service_type_0").show();
      }
    }


  $('#trx_cus_ajax').hide();
 
	hideShowAccount();
 

  $('body').click(function() {
     $('#suggesstion-box').html(""); 
     $("#suggesstion-box").css("display","none");
     
  });

  $('#suggesstion-box').click(function(event){
     event.stopPropagation();
  });

  autocomplete_order_quick_search();

  checked_payment_method();

  $('body').click(function() {
       $('.box_result_find').html(""); 
       $("#formorder-signin_v1 .find_product_result").css("display","none");
    });

    $("#formorder-signin_v1 .find_product_result").click(function(event){
       event.stopPropagation();
    });
	

    $("#data_order_table_tr").sortable({
     cursor: "move",
     handle: ".handle",
      axis: "y",
        placeholder: "sortable-placeholder",
    
  });
    // Default value checked store_id
  
    var store_id_param = $("#formorder-signin_v1 input[name='store_id_param']").val();
    if(store_id_param != "")
    {
        if( $("#formorder-signin_v1 input[name='store_id']").length > 0)
        { 
             $("#formorder-signin_v1 input[name='store_id'][value=" + store_id_param + "]").prop('checked', true);
        }
        else
        {
              $("#formorder-signin_v1 select[name='store_id']").val(store_id_param);
        }
    }

    $("#formorder-signin_v1").on('change', "[name='trx_discount_type']", function(e) {
    // Does some stuff and logs the event to the console
       $("[name='trx_discount_value']").val("").trigger("change");
 
        if($(e.currentTarget).val() == "1")
        {

            $("[name='trx_discount_value']").removeAttr("maxlength");
        }
        else 
        {
            $("[name='trx_discount_value']").prop("maxlength", "3");
        }
  });


   
    

    
   


});


function change_service_type(svc_type)
{ 
  if(svc_type == 1)
  {
    $(".block_service_type_1").show();
    $(".block_service_type_0").hide();
  }
  else
  {
    $(".block_service_type_1").hide();
    $(".block_service_type_0").show();
  }
}
//Submit action page edit
$("#form_submit_re_ls,#form_submit_re_ls_m").click(function(){

  
  $( "form#formorder-signin_v1 input[name='redirect']").val(0);
  $("#trigger_submit").trigger("click");

}); 
$("#form_submit_re_de,#form_submit_re_de_m").click(function(){

  
  $( "form#formorder-signin_v1 input[name='redirect']").val(1);
  $("#trigger_submit").trigger("click");

}); 


function autocomplete_order_quick_search()
{
  
  $("#o_quick_search").keyup(function(){

    if($(this).val().length < 2)
    {
       return false;
    }
    var key = $(this).val();
    $.ajax({
    type: "post",
    url: site_root_domain+"/?site=order&subact=autocomplete_quick_search",
    data:{ order_keyword : key },
    beforeSend: function(){
        $("#o_quick_search").css("background","#FFF url(/acp/images/fb-loading.gif) no-repeat 165px");
    },
    success: function(data){
              var obj = JSON.parse(data);
               $("#suggesstion-box").html("");
               if(obj.status == "success")
               {
                  output_li = "<ul class='list_goods'>"+obj.data_option+"</ul>"
                  $("#suggesstion-box").show();
                  $("#suggesstion-box").html(output_li);
                  $("#o_quick_search").css("background","#FFF");

               }
               else
               {
                  $("#suggesstion-box").hide();
                  $("#suggesstion-box").html("");
                  $("#o_quick_search").css("background","#FFF");
               }

    }
    });
  });

}

function autosubmit_frm_qs_product()
{
  $("form#frm_quickserch_product").submit();
}

  
function checked_service_type()
{
 
  $( "form#formorder-signin_v1 input[name='service_type']" ).each(function( index ) {
      if($(this).attr("defaultvalue") == $(this).val())
      {
        $(this).attr("checked", true);
      }
  });
}

function checked_payment_method()
{
 
  $( "form#formorder-signin_v1 input[name='order_payment_method']" ).each(function( index ) {
      if($(this).attr("defaultvalue") == $(this).val())
      {
         $(this).attr("checked", true);
         if($(this).attr("defaultvalue") == 1)
         {
            $("#hide_show_account").show();
         }
      }
  });
}



//Change store

$("form#formorder-signin_v1 input[name='store_id']").change( function() {

  var store_id = $(this).val();
  
  // Remove html  table product. asset
 
  reset_total_money();
  show_total();
  $(".btn_del_line").trigger("click");
  $(".btn_del_line_asset").trigger("click");
});

function reset_total_money()
{
  $("form#formorder-signin_v1 #total_show").html("").attr("total_product",0);
  $("form#formorder-signin_v1 #total_show_asset").html("").attr("total_asset",0);
  var total_reset = formatNumberInput(0);
  $("form#formorder-signin_v1 #total_price").html(total_reset);

  


}


function hideShowAccount() {
	if ($("input[name='order_payment_method']:checked").val() == 1) {
		$('#hide_show_account').show();
	} else {
		$('#hide_show_account').hide();
	}
}


function inputLoading() {
	var str = "<div class=\"cssload-container\" id=\"input_loading\">" 
		+ "<div class=\"cssload-progress cssload-float cssload-shadow\">"
			+ "<div class=\"cssload-progress-item\"></div>"
		+ "</div>"
	+ "</div>";
	return str;
}
$('form#formorder-signin_v1 #cus_name').keyup( function() {
	if ($('#cus_name').val() != '') {
    $("#suggesstion-cus").html("");
		$("#suggesstion-cus").show();
		$("#suggesstion-cus").html(inputLoading());
		$.get(
			site_root_domain+"/?site=customer&subact=search_customer_ajax",
			{ key: $('#cus_name').val() },
			function(html) {

              var obj = JSON.parse(html);
              var output_li="";
            
              if(obj.status == "success")
              {
                 
                  $.each(obj.data_option, function(index, value)
                  {    
                      output_li += "<li onclick='changeCusAjax(this)' cus_id='"+value.cus_id+"' cus_name='"+value.cus_full_name+"' cus_email='"+value.cus_email+"'  cus_address='"+value.cus_address+"'   ><span>"+value.cus_full_name+"( "+value.cus_email+") </span></li>";
                  });
                  var html_show = "<ul class='list_goods'>"
                                  +   output_li
                                  + "</ul>";
   
                  $("#suggesstion-cus").html(html_show);
                  $("#suggesstion-cus").css("display","block");

              }



				
			}
		);
	} else {
		$('#suggesstion-cus').hide();
	}
});




$('form#formorder-signin_v1 #user_name').keyup( function() {
  if ($('#user_name').val() != '') {
    getall_user($('#user_name').val() );
  } else {
    $('#suggesstion-user').hide();
  }
});




function getall_user(s_key = "")
{
      $('#suggesstion-user').hide();
      $("#suggesstion-user").html("");
      $("#suggesstion-user").show();
      $("#suggesstion-user").html(inputLoading());
      $.get(
        site_root_domain+"/?site=user&subact=search_user_ajax",
        { key: s_key },
        function(html) {

                var obj = JSON.parse(html);
                var output_li="";
              
                if(obj.status == "success")
                {
                   
                    $.each(obj.data_option, function(index, value)
                    {    
                        output_li += "<li onclick='changeUserAjax(this)' user_id='"+value.user_id+"' user_name='"+value.user_display_name+"' ><span>"+value.user_display_name+" / "+value.user_email+" </span></li>";
                    });
                    var html_show = "<ul class='list_goods'>"
                                    +   output_li
                                    + "</ul>";
     
                    $("#suggesstion-user").html(html_show);
                    $("#suggesstion-user").css("display","block");

                }



          
        }
      );
}

function changeCusAjax(onthis) {
 
	$('#cus_name').val($(onthis).attr("cus_name"));
	 
	$('#formorder-signin_v1 #cus_id').val($(onthis).attr("cus_id"));
	$('#suggesstion-cus').hide();

  // Info customer
  $('.cus_show_info').css("display","block");
 
  $("#formorder-signin_v1 #cus_name_display").html( $(onthis).attr("cus_name"));
 $('#formorder-signin_v1 #cus_email').html( $(onthis).attr("cus_email"));
  $('#formorder-signin_v1 #cus_address').html( $(onthis).attr("cus_address"));
}


function changeUserAjax(onthis) {
 
  $('#formorder-signin_v1  #user_name').val($(onthis).attr("user_name"));
   
  $('#formorder-signin_v1 #user_id').val($(onthis).attr("user_id"));
  $('#suggesstion-user').hide();
 
}


// Change payment method
$("input[name='order_payment_method']").change(function(){
 
    if($("input[name='order_payment_method']:checked").val() == 0)
    {
      $("#hide_show_account").hide();
    }
    else
    {
       $("#hide_show_account").show();
    }

});


$("#find_product").keypress( function() {
	var find_product = $(this).val();
	var service_type = $("input[name='service_type']:checked").val(); 
	if(find_product.length >= 2)
	{
		   $.ajax({
                type: "post",
                url: site_root_domain+"/?site=order&act=add&subact=find_product_ajax",
                data: {key_search: find_product, service_type : service_type},
                success: function(html)
                {
                      
                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        $(".box_result_find").html("");
                        $.each(obj.data_option, function(index, value)
                        {
                            if(service_type == 0)
                            {
                            	  output_li += "<li onclick='append_table(this)' service_type='"+service_type+"' ass_id='"+value.ass_id+"' ass_name='"+value.ass_name+"'  ass_price='"+value.ass_price+"' store_id='"+value.store_id+"' store_name='"+value.store_name+"' ><span>"+value.ass_name +" (kho:"+value.store_name+")</span></li>";
                            }
                            else
                            {
                            	 output_li += "<li onclick='append_table(this)' service_type='"+service_type+"' product_id='"+value.product_id+"' product_name='"+value.product_name+"'  product_price='"+value.product_price+"' product_cycle='"+value.product_cycle+"' ><span>"+value.product_name +" </span></li>";

                            }
                          


                        });
                    }

                    // console.log(output_li);
                    var html_show = "<ul class='list_goods'>"
                                  +   output_li
                                  + "</ul>";
   
                    $(".find_product_result").html(html_show);
                     $(".find_product_result").css("display","block");
                    
                }
            });
	}
	 
});

function append_table(obj)
{
 
	var svc_type =  obj.getAttribute("service_type");
	if(svc_type == 0)
	{
	  var ass_id = obj.getAttribute("ass_id");
      var ass_name = obj.getAttribute("ass_name");
      var ass_price = obj.getAttribute("ass_price");
      var store_id = obj.getAttribute("store_id");
      var store_name = obj.getAttribute("store_name");
      var tr = "";
 

      tr += "<tr>";
      tr += " <td><i class='fa fa-th handle'></i></td>";
      tr += "<td><div class='desc'>";
      tr += 	"<p>"+ass_name+"</p>"
      tr +=     "<span>  - Kho: "+store_name+"</span>";
      tr +=  "</div></td>";  

      tr += "<td>";
      tr += 	"<input type='text' class='form-control' maxlength='3'  onkeypress='return check_enter_number(event,this);'  onchange='return cal_total();' name='quantity[]' value='1' />";
      tr +=  "</td>"; 

      tr += "<td>";
      tr += 	"<span>"+format_currency(ass_price)+"</span>";
      tr +=  "</td>"; 

      tr += "<td>";
      tr += 	"<span class='amount'>"+format_currency(ass_price)+"</span>";
      tr +=  "</td>"; 

      tr += "<td class='trash'><span class='del_row_order'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";
	
	  tr += "<input type='hidden' name='ass_id[]' value='"+ass_id+"'/>";
	  tr += "<input type='hidden' name='store_id[]' value='"+store_id+"'/>";
	  tr += "<input type='hidden' name='price[]' value='"+ass_price+"'/>";
	  tr += "<input type='hidden' name='svc_type[]' value='"+svc_type+"'/>";
	  tr += "<input type='hidden' name='ass_name[]' value='"+ass_name+"'/>";
      tr += "</tr>";

	}
	else
	{
	  var prod_id = obj.getAttribute("product_id");
      var prod_name = obj.getAttribute("product_name");
      var prod_price = obj.getAttribute("product_price");
      var prod_cycle = obj.getAttribute("prod_cycle");
 		
      tr += "<tr>";
       tr += " <td><i class='fa fa-th handle'></i></td>";
      tr += "<td><div class='desc'>";
      tr +=   "<p>"+prod_name+"</p>"
       tr +=  "</div></td>";  
      tr += "<td>";
         tr += 	"<select onchange='return cal_total();'  class='form-control' name='cycle[]'>";
      if(prod_cycle == 1)//tháng
      {
      		for(var i = 1; i <= 12; i ++)
      		{
      			 tr += 	"<option value='"+i+"'>"+i+" tháng</option>";
      		}
      }
      else
      {
      		for(var i = 1; i <= 10; i ++)
      		{
      			 tr += 	"<option value='"+i+"'>"+i+" năm</option>";
      		}
      }
   
      tr +=  "</select>"; 

      tr += "<td>";
      tr += 	"<span>"+format_currency(prod_price)+"</span>";
      tr +=  "</td>"; 

      tr += "<td>";
      tr += 	"<span class='amount'>"+format_currency(prod_price)+"</span>";
      tr +=  "</td>"; 

      tr += "<td class='trash'><span class='del_row_order'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";
	
	  tr += "<input type='hidden' name='prod_id[]' value='"+prod_id+"'/>";
	  tr += "<input type='hidden' name='price[]' value='"+prod_price+"'/>";
	  tr += "<input type='hidden' name='svc_type[]' value='"+svc_type+"'/>";
	  tr += "<input type='hidden' name='prod_name[]' value='"+prod_name+"'/>";
	   tr += "<input type='hidden' name='prod_cycle_type[]' value='"+prod_cycle+"'/>";
      tr += "</tr>";
	}

	$("#data_order_table_tr").append(tr);
	$("#form-signin_v1 .find_product_result").html('');
  $("#form-signin_v1 .find_product_result").css("display","none");
	cal_total();
}

function format_currency(number)
{
	return (number + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
}

function cal_total()
{

	var total = 0;
  var total_amount = 0;
  var amount = 0;
  var is_vat = 0;
  if( $("input[name='is_ord_vat']").is(":checked"))
  {
    is_vat = 1;
  }
 
	$("#data_order_table tbody tr").each(function() {
		var price = $(this).find('input[name="price[]"]').val();
	 	var svc_type  = $(this).find('input[name="svc_type[]"]').val();
	 	var amount;
	 	if(svc_type == 0)
	 	{
	 		var quantity = $(this).find('input[name="quantity[]"]').val();
			amount = price * quantity;
	 	}
		else
		{
			var cycle = $(this).find('select[name="cycle[]"]').val();
			amount = price * cycle;
		}
  

		total_amount = total_amount + amount;


		$(this).find(".amount").html(format_currency(amount));
	});

   if(is_vat == 1)//10%
    {
      total= total_amount + (total_amount * 0.1);
    }
    else
    {
      total = total_amount;
    }

  $("#total_amount").html(format_currency(total_amount));
	$("#total").html(format_currency(total));
  $("#total_price").html(format_currency(total));
  

}

 $("#data_order_table").on("click",".del_row_order",function(){
         $(this).parent().parent().remove();
        cal_total();
  });    


 
 
$("#button_submit").click(function(){

   $("#trigger_submit").trigger("click");
});

function cus_assign_me_ord(onthis)
{
  var user_id = $(onthis).attr("val_id");
   

   example_select2_photo.val(user_id).trigger("change");
}


$("#formorder-signin_v1 input[name='is_shipping']").click(function(){

  $("#container_shipping_info").toggle();
  $("#formorder-signin_v1 input[name='ship_receive_name']").focus();
});


/* 
* change form validate
* type 0: novalidate; 1: validate
*/
if( typeof changeElementValidate != 'function' )
{
    function changeElementValidate( element = "" , type = 0 )
    {
        if( type == 0 )
        {
            $(element).attr('data-validation-bk', $(element).attr('data-validation'));
            $(element).removeAttr('data-validation');
        }
        else
        {
            if( typeof $(element).attr('data-validation-bk') != 'undefined' )
            {
                $(element).attr('data-validation', $(element).attr('data-validation-bk'));
                $(element).removeAttr('data-validation-bk');
            }
        }
    }
}