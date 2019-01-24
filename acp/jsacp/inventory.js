$(document).ready(function() {
	
    // initialize tooltipster on text input elements
 
 
	hideShowPgroup();
 

   $('body').click(function() {
       $('.box_result_find').html(""); 
       $("#form-signin_v1 .find_product_result").css("display","none");
    });

    $("#form-signin_v1 .find_product_result").click(function(event){
       event.stopPropagation();
    });
 

    $("#data_order_table_tr").sortable({
     cursor: "move",
     handle: ".handle",
      axis: "y",
        placeholder: "sortable-placeholder",
    
  });
    // Default value checked store_id
  
    var store_id_param = $("#form-signin_v1 input[name='store_id_param']").val();
    if(store_id_param != "")
    {
        if( $("#form-signin_v1 input[name='store_id']").length > 0)
        { 
             $("#form-signin_v1 input[name='store_id'][value=" + store_id_param + "]").prop('checked', true);
        }
        else
        {
              $("#form-signin_v1 select[name='store_id']").val(store_id_param);
        }
    }

    var in_type_param = $("#form-signin_v1 select[name='inventory_type']").attr('defaultvalue');
    $("#form-signin_v1 select[name='inventory_type']").val(in_type_param);
    if(in_type_param == 2)
    {
      $('#hide_show_pgroup').show();
    }

  

});


function select_pgroup()
{
   var inventory_pgroup_tmp = $("#form-signin_v1 input[name='inventory_pgroup_tmp']").val();
  
     var res = inventory_pgroup_tmp.split(",");
     var cnt_res = res.length;
     var li = new Array();
     for (var i = 0; i <= cnt_res; i++) {
        if (res[i] === undefined || res[i] === null) {
             // do something 
        }
        else
        {  
            if(res[i] != "")
            {

              $("#form-signin_v1 select[name='inventory_pgroup[]'] option[value='"+res[i]+"']").prop("selected",true);
              var text_op =   $("#form-signin_v1 select[name='inventory_pgroup[]'] option[value='"+res[i]+"']").html();
           
              li.push({ 
                "id" : res[i],
                "text"  : text_op, 
               });  
            }
         


        }
      
      }
   
        $(".select2").select2({
          data: JSON.stringify(li)
        }) 
 

      
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



 
function reset_total_money()
{
  $("form#formorder-signin_v1 #total_show").html("").attr("total_product",0);
  $("form#formorder-signin_v1 #total_show_asset").html("").attr("total_asset",0);
  $("form#formorder-signin_v1 #total_price").html("0 <sup>đ</sup>");

  


}


function hideShowPgroup() {
	if ($("select[name='inventory_type']").val() == 2) {
		$('#hide_show_pgroup').show();
	} else {
		$('#hide_show_pgroup').hide();
	}
}

     

// Change payment method
$("select[name='inventory_type']").change(function(){
 
    if($(this).val() == 2)
    {
      $("#hide_show_pgroup").show();
    }
    else
    {
       $("#hide_show_pgroup").hide();
    }

});

  
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

  $("#total_amount").html(format_currency(total_amount) + "đ");
	$("#total").html(format_currency(total)  + "đ");
  $("#total_price").html(format_currency(total)  + "<sup>đ</sup>");
  

}

 $("#data_order_table").on("click",".del_row_order",function(){
         $(this).parent().parent().remove();
        cal_total();
  });    


 
 
$("#button_submit").click(function(){

   $("#trigger_submit").trigger("click");
});

$("#find_asset_inventory").keypress(function(){

   var store_id = $(this).attr("store_id");
   var find_asset = $(this).val();
 
  if(find_asset.length >= 2)
  {
       $.ajax({
                type: "post",
                url: site_root_domain+"/?site=assets&subact=find_asset_inventory",
                data: {key_search: find_asset, store_id : store_id},
                success: function(html)
                {
                      
                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        $(".box_result_find").html("");
                        $.each(obj.data, function(index, value)
                        {
                   
                               output_li += "<li onclick='append_table(this)' ass_id='"+value.ass_id+"' ass_name='"+value.ass_name+"' ass_purchase_price='"+value.ass_purchase_price+"' ass_keyname='"+value.ass_keyname+"' ass_code='"+value.ass_code+"' ass_key='"+value.ass_key+"'  ass_quantity='"+value.quantity+"'   ><span>"+value.ass_name +" </span></li>";

                        });
                    }

                    // console.log(output_li);
                    var html_show = "<ul class='list_goods'>"
                                  +   output_li
                                  + "</ul>";
   
                    $("#suggesstion-asset").html(html_show);
                    $("#suggesstion-asset").css("display","block");
                    
                }
            });
  }

});
   
 
function getall_asset(onthis)
{
   var store_id = $(onthis).prev().attr("store_id");
    $.ajax({
                  type: "post",
                  url: site_root_domain+"/?site=assets&subact=find_asset_inventory",
                  data: {key_search: '', store_id : store_id},
                  success: function(html)
                  {
                        
                      var obj = JSON.parse(html);
                      var output_li="";
                      if(obj.status == "success")
                      {
                          $(".box_result_find").html("");
                          $.each(obj.data, function(index, value)
                          {
                             
                                 output_li += "<li onclick='append_table(this)' ass_id='"+value.ass_id+"' ass_name='"+value.ass_name+"' ass_purchase_price='"+value.ass_purchase_price+"' ass_keyname='"+value.ass_keyname+"' ass_code='"+value.ass_code+"' ass_key='"+value.ass_key+"'  ass_quantity='"+value.quantity+"'   ><span>"+value.ass_name +" </span></li>";

                          });
                      }

                      // console.log(output_li);
                      var html_show = "<ul class='list_goods'>"
                                    +   output_li
                                    + "</ul>";
     
                      $("#suggesstion-asset").html(html_show);
                      $("#suggesstion-asset").css("display","block");
                      
                  }
              });
 
}
function append_table(obj)
{
      var ass_id = obj.getAttribute("ass_id");
      var ass_keyname = obj.getAttribute("ass_keyname");
      var ass_name = obj.getAttribute("ass_name");
      var ass_code = obj.getAttribute("ass_code");
      var ass_key = obj.getAttribute("ass_key");
      var ass_quantity = obj.getAttribute("ass_quantity");
      var ass_purchase_price =  format_currency(obj.getAttribute("ass_purchase_price"));
  
      var tr = "";
      var flag = true;
    $( "#data_table tr" ).each(function( index, value ) {
      
        var ls_ass_key = $(this).find("input[name='ass_key[]']").val();
         
        if(ls_ass_key === ass_key)
        {
 
          call_notify("Phiếu kiểm kho","Tài sản đã tồn tại trong danh sách tài sản kiểm kho","warning","");
          $("#suggesstion-asset .list_goods").html("").hide();
          $("#find_asset_inventory").val(" ");
           flag =  false;
             
        }
        else
        {

        }
     });
      if(flag == false)
      {
        return false;
      }
 
      tr += "<tr>";
      tr += "<td><div class='desc'>";
      tr +=   "<a href='"+site_root_domain+"/?site=assets&act=show&id="+ass_id+"'>"+ass_keyname+"</a>"
      tr +=  "</div></td>";  
  
      tr += "<td><div class='desc'>";
      tr +=   "<p>"+ass_name+"</p>"
      tr +=  "</div></td>";  
     tr +=  "<td>"; 
      tr +=   "<textarea   class='form-control' maxlength='100'  name='ass_description[]'></textarea>";
      tr +=  "</td>"; 
      
      tr += "<td>";
      tr +=   "<span>"+ass_purchase_price+"<sup>đ</sup></td> </span>";
      tr +=  "</td>"; 

      tr += "<td>";
        tr +=   "<span>"+ass_quantity+" </span>";
      tr +=  "</td>"; 
 
      tr += "<td>";
      tr +=   "<input type='text' class='form-control'  name='ass_check[]'  onkeypress='return check_enter_number(event,this);'   onkeyup='cal_quantity("+ass_quantity+",this)'  maxlength='6' />";
      tr +=  "</td>"; 
      tr += "<td>";
      tr +=   "<span id='container_ass_quantity'></span>";
      tr +=  "</td>"; 

      tr += "<td class='trash'><span class='del_row_order'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";
    tr += "<input type='hidden' name='ass_name[]' value='"+ass_name+"'/>";
    tr += "<input type='hidden' name='ass_key[]' value='"+ass_key+"'/>";
    tr += "<input type='hidden' name='ass_code[]' value='"+ass_code+"'/>";
    tr += "<input type='hidden' name='ass_quantity[]' value='"+ass_quantity+"'/>";
 
      tr += "</tr>";
      $("#data_table").append(tr);
      $("#find_asset_inventory").val(" ");
       $("#suggesstion-asset .list_goods").html("").hide();
}    

 
function cal_quantity(quantity,onthis)
{

  var value_kk = $(onthis).val();
  var obj_tr = $(onthis).parent().parent();
  
  var new_value_kk = parseInt(value_kk) - parseInt(quantity);
  $(obj_tr).find("#container_ass_quantity").html(new_value_kk);

}


 $("#data_table").on("click",".del_row_order",function(){
         $(this).parent().parent().remove();
 
  });    

