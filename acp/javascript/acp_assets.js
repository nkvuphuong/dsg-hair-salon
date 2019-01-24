var customer_array = new Array();
var customer_record = new Array();
var customer_cnt = 0;
var customer_search_type = 0; // 0 = Default, 1 = Email, 2 = ID
var customer_field = "";
var customer_remove_if_no_data = 0;
var customer_add_url = "";
var result_field = "";
var list_item = "";

var customer_array2 = new Array();
var customer_record2 = new Array();
var customer_cnt2 = 0;
var customer_search_type2 = 0; // 0 = Default, 1 = Email, 2 = ID
var customer_field2 = "";
var customer_remove_if_no_data2 = 0;
var customer_add_url2 = "";
var result_field2 = "";
var set_field2 = "";
var set_field = ""; //

function set_customer( value,code,price,supplier,sup_name,default_warranty_month,group,result_field )
{
        result_field = result_field ? result_field : "customer_list";
        
		if(document.getElementById(result_field))
		{
				document.getElementById(result_field).style.marginBottom = "0px";
				document.getElementById(result_field).innerHTML=""; 
		}
		document.getElementById(set_field).value = value;	
		// Set for another value
                if(set_field != "shi_name")
                {
                    if(document.getElementById("pgroup_id") && group !='undefined')
                    {
                        document.getElementById("pgroup_id").value=group; //
                        var  product_group_id = $("#pgroup_id").val();
                        $('#pgroup_id').val(group).trigger("change");

                        if(product_group_id)
                        {
                          if(pms['product_group_edit'] == 1)
                          {
                            $(".box_product_group").html("<a id='"+product_group_id+"' class='btn_gen btn_edit_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                          }
                        }else
                        {
                            $(".box_product_group").html("");
                        }
                    }
                    
                    if(document.getElementById("supplier_id") && sup_name!='undefined')
                    {
                        document.getElementById("supplier_id").value=supplier;
                        var temp_sup = $("#supplier_id").val();
                        $('#supplier_id').val(supplier).trigger("change");
                        if(temp_sup)
                        {
                          if(pms['supplier_edit'] == 1)
                          {
                              $(".box_edit_supplier").html("<a id='"+temp_sup+"' class='btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                          }
                        }else
                        {
                            $(".box_edit_supplier").html("");
                        }
                    }
                    
                    (document.getElementById("ass_code") && code !='undefined') ? document.getElementById("ass_code").value=code : "";
                    document.getElementById("ass_purchase_price") ? document.getElementById("ass_purchase_price").value=price : "";
                    document.getElementById("ass_price") ? document.getElementById("ass_price").value=price : "";
					
                    if(document.getElementById("ass_guarantee_default") && default_warranty_month !='undefined' && default_warranty_month > 0)
                    {
                        document.getElementById("ass_guarantee_default").value=default_warranty_month;
                        calculate_assets_warranty(default_warranty_month);
                    }
					
                    $(".sp_list").val(supplier);
                }
                
 
		
		// Update sub item for assets
		if(list_item && list_item!='undefined')
		{
                    var first_row = $("#data_table tr:first");
                    $("#data_table").html("");
                    
                    // Append new list item
                    for(var i=0;i < list_item.length;i++)
                    {
                        var is_last_row = i == (list_item.length - 1) ? 1 : 0;
                        append_list_item(first_row,is_last_row,i);
                    }
					
					// Set value for tr
					var j = 0;
					$("#data_table tr").each(function()
              		{
                  		$(this).find(".tr_name").val(list_item[j]['product_name']);
						$(this).find(".tr_code").val(list_item[j]['product_code']);
						$(this).find(".tr_pprice").val(list_item[j]['product_price']);
						$(this).find(".tr_quantity").val(list_item[j]['product_quantity']);
                  		j ++;
              		});

		}
                
}

function print_customer_row(result_field,set_field)
{
        var i = customer_cnt;

        if ( customer_array.length-1 <= i )
        {
                document.getElementById(result_field+"_loading").innerHTML = "";
                return false;
        }
		
        customer_record = customer_array[i].split("|");
		
				
        var row = document.createElement("tr");
		customer_record[6] = JSON.parse(customer_record[6]);
		console.log(customer_record[6]);
		list_item = customer_record[6] ? customer_record[6] : "";

        var cell1 = document.createElement("td");
        cell1.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
        cell1.setAttribute("width", "25%");

        cell1.innerHTML = "<input type='radio' id='cus_username' name='cus_username_search' value='"+customer_record[1]+"' onclick='set_customer(this.value,\""+customer_record[2]+"\",\""+customer_record[3]+"\",\""+customer_record[4]+"\",\""+customer_record[5]+"\",\""+result_field+"\");'> ";// + customer_record[0];

        cell1.innerHTML += customer_record[1];
        row.appendChild(cell1);

        var cell3 = document.createElement("td");
        cell3.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
        cell3.setAttribute("width", "25%");
		cell3.setAttribute("align", "center");
        cell3.innerHTML = customer_record[2];
        row.appendChild(cell3);

        var cell6 = document.createElement("td");
        cell6.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
        cell6.setAttribute("width", "20%");
		cell6.setAttribute("align", "center");
        cell6.innerHTML = customer_record[3];
        row.appendChild(cell6);
		
		if(customer_record[5])
		{
			var cell4 = document.createElement("td");
			cell4.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
			cell4.setAttribute("width", "20%");
			cell4.setAttribute("align", "center");
			cell4.innerHTML = customer_record[5];
			row.appendChild(cell4);
		}


        if ( ! document.getElementById(result_field+"_tr") )
        {
                return false;	
        }

        document.getElementById(result_field+"_tr").appendChild(row);

        customer_cnt++;

        setTimeout("print_customer_row('"+result_field+"')", 100);
}

function print_customer( data,customer_field,result_field,set_field )
{
    
        customer_array = data.split("||||");
		
		

        customer_record = new Array();
        customer_cnt = 0;

    var customer_html = "";
        customer_html += "<table width='100%' cellspacing='0' cellpadding='0' id='customer_list_table'><tbody id='"+result_field+"_tr'>";
        customer_html += "</tbody></table><div style='margin-bottom:15px' id='"+result_field+"_loading'>"+lang_loading+"</div>";

document.getElementById(result_field).innerHTML = customer_html;

        if ( customer_array.length == 1 )
        {
                if ( customer_remove_if_no_data == 1 )
                {
                        //customer_field.value = "";
                }
				
				document.getElementById("customer_list_table").style.display = "none";
				
				
				if(result_field == "ass_list")
				{
					document.getElementById(result_field+"_loading").innerHTML = "<font style='font-size:11px' color='red'>" + add_new_note + "</font>";
				}
				else
				{
					document.getElementById(result_field+"_loading").innerHTML = "<img src='"+site_root_domain+"/images/icons/plus.png"+"'>" + "<font style='margin-left:5px' color='#41b4a0'>Add new</font>";
				}
                    
        }
        else
        {
                print_customer_row(result_field,set_field);
        }
}


function parent_search( cus, search_type, remove_if_no_data,result_field,search_name)
{
        customer_field = cus;
		
		set_field = cus.id;

        result_field = result_field ? result_field : "ass_list"

        customer_remove_if_no_data = remove_if_no_data;

        if ( search_type == 1 )
        {
                customer_search_type = 1;
        }
        else if ( search_type == 2 )
        {
                customer_search_type = 2;	
        }
        else
        {
                customer_search_type = 0;	
        }

        cus.value = trim(cus.value);

        if ( cus.value )
        {
            var url = site_root_domain + "/?site=assets&act=search&is_ajax=1&ass_input="+cus.value+customer_add_url+"&search_name="+search_name;
            
                $.ajax(
            {
                'url':''+url+''
                ,'beforeSend':function(req){ 
					document.getElementById(result_field).style.marginBottom = "15px";
					document.getElementById(result_field).innerHTML = lang_loading; }
                ,'success':function(req){print_customer(req,customer_field,result_field,cus.id);  }
            }
            );
        }
        else
        {
        document.getElementById(result_field).innerHTML = "";
        }
}

// Copy
function set_customer2( value, value2,result_field2,set_field )
{
        result_field2 = result_field2 ? result_field2 : "customer_list";
        
				if(document.getElementById(result_field2))
				{
						document.getElementById(result_field2).style.marginBottom = "0px";
						document.getElementById(result_field2).innerHTML=""; 
				}

				document.getElementById(set_field2).value = value;
                                
                                $(".sp_list").val(value2);
}

function print_customer_row2(result_field2,set_field)
{
        var i = customer_cnt2;

        if ( customer_array2.length-1 <= i )
        {
                document.getElementById(result_field2+"_loading").innerHTML = "";
                return false;
        }

        customer_record2 = customer_array2[i].split("|");

        var row = document.createElement("tr");

        var cell1 = document.createElement("td");
        cell1.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
        cell1.setAttribute("width", "25%");

        cell1.innerHTML = "<input type='radio' id='cus_username' name='cus_username_search' value='"+customer_record2[1]+"' onclick='set_customer2(this.value,\""+customer_record2[0]+"\",\""+result_field2+"\",\""+set_field+"\");'> ";// + customer_record[0];

        cell1.innerHTML += customer_record2[1];
        row.appendChild(cell1);

        var cell3 = document.createElement("td");
        cell3.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
        cell3.setAttribute("width", "25%");
		cell3.setAttribute("align", "center");
        cell3.innerHTML = customer_record2[2];
        row.appendChild(cell3);

        var cell6 = document.createElement("td");
        cell6.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
        cell6.setAttribute("width", "20%");
		cell6.setAttribute("align", "center");
        cell6.innerHTML = customer_record2[3];
        row.appendChild(cell6);
		
		if(customer_record2[4])
		{
			var cell4 = document.createElement("td");
			cell4.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
			cell4.setAttribute("width", "20%");
			cell4.setAttribute("align", "center");
			cell4.innerHTML = customer_record2[4];
			row.appendChild(cell4);
		}


        if ( ! document.getElementById(result_field2+"_tr") )
        {
                return false;	
        }

        document.getElementById(result_field2+"_tr").appendChild(row);

        customer_cnt2++;

        setTimeout("print_customer_row2('"+result_field2+"')", 100);
}

function print_customer2( data,customer_field2,result_field2,set_field,search_name )
{
        customer_array2 = data.split("||||");

        customer_record2 = new Array();
        customer_cnt2 = 0;

    var customer_html = "";
        customer_html += "<table width='100%' cellspacing='0' cellpadding='0' id='customer_list_table'><tbody id='"+result_field2+"_tr'>";
        customer_html += "</tbody></table><div style='margin-bottom:15px' id='"+result_field2+"_loading'>"+lang_loading+"</div>";

document.getElementById(result_field2).innerHTML = customer_html;

        if ( customer_array2.length == 1 )
        {
                if ( customer_remove_if_no_data2 == 1 )
                {
                        customer_field2.value = "";
                }
				
		document.getElementById("customer_list_table").style.display = "none";

                if(search_name == "supplier")
                {
                   document.getElementById(result_field2+"_loading").innerHTML = "<b><font color='red'>" + lang_no_result + "</font></b>";//; 
                }
                else
                {
                   document.getElementById(result_field2+"_loading").innerHTML = "<img src='"+site_root_domain+"/images/icons/plus.png"+"'>" + "<font style='margin-left:5px' color='#41b4a0'>Add new</font>";//<b><font color='red'>" + lang_no_result + "</font></b>"; 
                }
                
        }
        else
        {
                print_customer_row2(result_field2,set_field);
        }
}


function parent_search2( cus, search_type, remove_if_no_data2,result_field2,search_name)
{
        customer_field2 = cus;
        set_field2 = cus.id;

        result_field2 = result_field2 ? result_field2 : "ass_list"

        customer_remove_if_no_data2 = remove_if_no_data2;

        if ( search_type == 1 )
        {
                customer_search_type2 = 1;
        }
        else if ( search_type == 2 )
        {
                customer_search_type2 = 2;	
        }
        else
        {
                customer_search_type2 = 0;	
        }

        cus.value = trim(cus.value);

        if ( cus.value )
        {
            var url = site_root_domain + "/?site=assets&act=search&is_ajax=1&ass_input="+cus.value+customer_add_url2+"&search_name="+search_name;
            
            $.ajax(
            {
                'url':''+url+''
                ,'beforeSend':function(req){ 
					document.getElementById(result_field2).style.marginBottom = "15px";
					document.getElementById(result_field2).innerHTML = lang_loading; }
                ,'success':function(req){
                    
                    print_customer2(req,customer_field2,result_field2,cus.id,search_name);  }
            }
            );
        }
        else
        {
        document.getElementById(result_field2).innerHTML = "";
        }
}

function append_list_item(first_row,is_last,i)
{
    
            var f_row = first_row;
            var html_clone = f_row.html();
            var last_row = is_last == 1 ? "last-row" : "";
            
            count_row ++;
            
            $("#data_table").append("<tr keyrow='"+count_row+"' class='row-grid' rowtr='"+last_row+"'>"+html_clone+"</tr>");

            $("#data_table tr[rowtr='last-row']").find("input,textarea,select").val('');

            // Plong: gán giá trị mặc định quantity = 1, thue - 10% khi them row mới
            $("#data_table tr[rowtr='last-row']").find(".quan_list").val(1);
            $("#data_table tr[rowtr='last-row']").find(".tax_list option[value=10]").prop("selected",true);

            // insert number
            var count_check = 1;
            $("#data_table tr").each(function()
            {
                $(this).find(".number").html("#"+count_check);
                count_check ++;
            });
}

function popup_edit_product()
{
    waitingDialog.show(cms_lang.waiting_dialog_msg);
    var product_id = $(".edit_product").attr("id");
    
    $(".change_title_product").html("Sửa sản phẩm");
    $(".box_control").html("<input class='btn act_popup_product_btn' aclass='btn_edit_product_0' type='button' etype='0' value='Lưu & Cập nhật vào sản phẩm gốc'/> <input class='btn act_popup_product_btn' aclass='btn_add_product' type='button' value='Lưu & Tạo mới sản phẩm'>");
    validate_form_custom("#edit_product_form",".act_popup_product_btn","box_custom");
    // Ajax get data
      $.ajax({
          type: "get",
          url: site_root_domain + "/?site=product&subact=ajax_edit_product&type_product_edit=1",
          data: {product_id: product_id},
          dataType: 'json',
          success: function(obj)
          {
              waitingDialog.hide();
              
             
              var manufacture_id = obj.product_manufacture > 0 ? obj.product_manufacture : "";
              var product_cycle = obj.product_cycle > 0 ? obj.product_cycle : "";
              var sup_id = obj.sup_id > 0 ? obj.sup_id : "";

              loadProductCycleType(obj.product_type);

              $("#box_product input[name='product_name']").val(obj.product_name);
              $("#box_product input[name='product_id']").val(product_id);
              $("#box_product input[name='product_code']").val(obj.product_code);
              $("#box_product input[name='product_sku']").val(obj.product_sku);

              // Supplier
              $("#box_product select[name='sup_id'] option[value='"+sup_id+"']").prop("selected",true);
              $("#box_product select[name='sup_id']").trigger("change");

              // Product group
              $("#box_product select[name='product_group'] option[value='"+obj.product_group+"']").prop("selected",true);
              $("#box_product select[name='product_group']").trigger("change");

              // Manufacture
              $("#box_product select[name='product_manufacture'] option[value='"+manufacture_id+"']").prop("selected",true);
              $("#box_product select[name='product_manufacture']").trigger("change");
              
              // product_cycle
              if(product_cycle > 0)
              {
                $("#display_product_cycle").show();
                $("#box_product select[name='product_cycle'] option[value='"+product_cycle+"']").prop("selected",true);
                $("#box_product select[name='product_cycle']").trigger("change");
              }
              else
              {
                $("#display_product_cycle").hide();
                $("#box_product select[name='product_cycle'] option[value='"+product_cycle+"']").prop("selected",true);
                $("#box_product select[name='product_cycle']").trigger("change");
              }

              $("#box_product .product_type").val(obj.product_type);

              $("#box_product .data_subitem[for='edit']").html(obj.html_subitem);
              $(".data_subitem select.auto_select").each(function(){
                var val_default = $(this).attr("defaultvalue");
                $(this).find("option[value='"+val_default+"']").prop("selected",true);
              });
              if(obj.product_image)
              {
                  var url_img = site_parent_domain+"/uploads/product/"+obj.product_image;
                  $("#box_product #upload_img_show").attr("src",url_img);
              }

              
              $("#box_product textarea[name='product_description']").val(obj.product_description);
              $("#box_product input[name='product_price']").val(obj.product_price);
              $("#box_product input[name='product_price_sell']").val(obj.product_price_sell);
              $("#box_product select[name='product_tax'] option[value='"+obj.product_tax+"'").prop("selected", true);
              $("#box_product input[name='product_sku']").val(obj.product_sku);

              
              var price = parseFloat(obj.product_price);
              var price_sell = parseFloat(obj.product_price_sell);
              var tax = parseFloat(obj.product_tax);
              var price_tax = Math.round((tax * price)/100);
              var total = price + price_tax;
              var total_show = formatNumberInput(total);
              $("span#product_total").html(total_show); 

              $.magnificPopup.open({
                type: 'inline',
                items: {
                  src: '#box_product'
                },
              });
              
          }
      });

}

$(document).ready(function(){
	
    $('body').click(function() {
       $('#suggesstion-box').html(""); 
       $("#suggesstion-box").css("display","none");
       
    });

    $('#suggesstion-box').click(function(event){
       event.stopPropagation();
    });
    
    $(".mfp-content").click(function()
    {
        $("#ass_name").val('');
    });
    
    $(".mfp-close").click(function()
    {
        $("#ass_name").val('');
    });
    
    $(".mfp-container").click(function(){
    })
    
    $(".mfp-wrap").click(function(){
    })

    $(".mfp-bg").click(function(){
    })
	
	autocomplete_quick_search();
		
    $('#ass_name').keyup( function() {
        if ($('#ass_name').val() != '') {
            $.get(
                site_root_domain+"/?site=assets&act=search&is_ajax=1&ass_input="+($('#ass_name').val())+"&search_name=product",
                { key: $('#ass_name').val() },
                function(html) {
                    
                    var html2 = "<li class='add_new_el' value='add_new' onclick='update_data(this)'><i class='fa fa-plus'></i><a class='add_new_goods' href='#box_product'>Add new</a></li>";
                    html2 = html2 + html;
                    //$('#ass_list').html("<select multiple class=\"form-control\" onchange=\"update_data(this)\">" + html2 + "</select>");
					$('#ass_list').html("<ul class='list_goods'>" + html2 + "</ul>");
                                        
                                        $('#ass_list').show();
                }
            );
        } else {
            $('#ass_list').hide();
        }
    });
    

    $('#shi_name').keyup( function() {
        if ($('#shi_name').val() != '') {
            $('#shi_list').show();
            $('#shi_list').html(inputLoading());

            $.get(
                site_root_domain+"/?site=assets&act=search&is_ajax=1&ass_input="+($('#shi_name').val())+"&search_name=shipment",
                { key: $('#shi_id').val() },
                function(html) {

                    $('#shi_list').html("<select multiple class=\"form-control\" onchange=\"update_data_shipment(this)\">" + html + "</select>");
                }
            );
        } else {
            $('#shi_list').hide();
        }
    });
	
	$(".edit_all_assets").click(function(){
		$("#ass_is_edit_all").val(1);	
		$("form#form-signin_v1").submit();
	});
    
    $("#data_table").sortable({
     cursor: "move",
     handle: ".handle",
      axis: "y",
        placeholder: "sortable-placeholder",
    //       start: function(event, ui){        
    //    var text = $.trim(ui.item.text());
    //    var start =  ui.item.index();
    //    ui.item.startHtml = ui.item.html();
       
    //    start.css({
    //       'background-color': 'red' 
    //     });
    // },
    // stop: function(event, ui){ 
    //    ui.item.html(ui.item.startHtml);
    // }
  });
  
  
  

})
    



function update_data(ob)
{
    var temp = ob.getAttribute("value");
    
    if(temp == "add_new")
    {
        // Add new product
        $('#ass_list').hide();
        
        // Popup add product
        popup_add_product();
        
        return;
    }
    
    var p_id = ob.getAttribute("p_id");
    if(!p_id)
    {
        return false;
    }
	
	var p_id = ob.getAttribute("p_id");
	var p_name = ob.getAttribute("p_name");
	var p_code = ob.getAttribute("p_code");
	var p_price = ob.getAttribute("p_price");
	var sup_id = ob.getAttribute("sup_id"); 
	var sup_name = ob.getAttribute("sup_name");
	var p_item = ob.getAttribute("p_item");
	var p_guarantee = ob.getAttribute("p_guarantee");
        var p_group = ob.getAttribute("p_group");
    
    //var data = ob.value.split("|");
    //data[6] = JSON.parse(data[6]);
    //console.log(data[6]);
    //list_item = data[6] ? data[6] : "";
	
	p_item = JSON.parse(p_item);
	list_item = p_item ? p_item : "";
    result_field = "ass_list";
    set_field = "ass_name";
	$("#product_id").val(p_id);  
    //set_customer( data[1],data[2],data[3],data[4],data[5],data[7],result_field );
	set_customer( p_name,p_code,p_price,sup_id,sup_name,p_guarantee,p_group,result_field );
        
    $('#ass_list').hide();
}

function update_data_shipment(ob)
{
    if(!ob.value)
    {
        return false;
    }
    var data = ob.value.split("|");
    
    result_field = "shi_list";
    set_field = "shi_name";
    
    $("#shi_id").val(data[0]);
    
    set_customer( data[1],data[2],data[3],data[4],data[5],0,result_field );
}

function inputLoading() {
	var str = "<div class=\"cssload-container\" id=\"input_loading\">" 
		+ "<div class=\"cssload-progress cssload-float cssload-shadow\">"
			+ "<div class=\"cssload-progress-item\"></div>"
		+ "</div>"
	+ "</div>";
	return str;
}

      // show info goods
      $("#data_table").on("keyup",".find_product",function(){
          var key_search = $(this).val();
       
          var forkey = $(this).attr("for");
          var obj_element = $(this);
            $.ajax({
                type: "post",
                url: site_root_domain+"/?site=store_request&act=search&subact=search_goods",
                data: {key_search: key_search},
                success: function(html)
                {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        $(".box_result_find").html("");
                        $.each(obj.data, function(index, value)
                        {
                         
                            var description = value.product_description === null ?  "" : value.product_description;
                            output_li += "<li class='' onclick='show_detail_product_2(this)' for='"+forkey+"' sku='"+value.product_sku+"' name='"+value.product_name+"' description='"+description+"' id_product='"+value.product_id+"'  price='"+value.product_price+"'><span class='name_show'>"+value.product_name+"</span><span class='description_show'>"+description+"</span></li>";


                        });
                    }
                    else
                    {
                        output_li += "<li><span>"+cms_lang['gsearch_no_result']+": "+ key_search +"</span></li>";
                    }

                    var html_show = "<ul class='list_goods'>"
                                  +   output_li
                                  + "</ul>";
                    obj_element.parent().parent().find(".box_result_find").html(html_show);
                    obj_element.parent().parent().find(".box_result_find").css("display","block");
                }
            });
      });

  function show_detail_product_2(obj) 
  {
 
      var name_product = obj.getAttribute("name");
      var price = obj.getAttribute("price");
      var id = obj.getAttribute("id_product");
      var sku = obj.getAttribute("sku");
	  
	  $(".row-grid.active").find("input").val("");
	  
      name_product != 'undefined' ? $(".row-grid.active").find("input[name='sub_name[]']").val(name_product) : "";
      price != 'undefined' ? $(".row-grid.active").find("input[name='sub_purchase_price[]']").val(price) : "";
	  sku != 'undefined' ? $(".row-grid.active").find("input[name='sub_code[]']").val(sku) : "";
	  
      $(".row-grid.active").find("input.product_id").val(id);
      $(".box_result_find").html("");
      $("#data_table .box_result_find").css("display","none");
  }

var on_mouse = 1;
function form_checkbox_all(form_name)
{
    var checkboxs = document.forms[form_name].getElementsByClassName("checkbox");
    var checkalls = document.forms[form_name].getElementsByClassName("checkbox_all");
    
    if ( on_mouse == 0 )
    {
        if ( document.forms[form_name].all_top.checked == false && document.forms[form_name].all_bottom.checked == false )
        {
            for ( var i = 0; i < checkalls.length; i++)
            {
                checkalls[i].checked = true;                
            }
            for ( var i = 0; i < checkboxs.length; i++)
            {
                checkboxs[i].checked = true;
            }
        }
        else if ( document.forms[form_name].all_top.checked == true && document.forms[form_name].all_bottom.checked == true )
        {
            for ( var i = 0; i < checkalls.length; i++)
            {
                checkalls[i].checked = false;
            }
            for ( var i = 0; i < checkboxs.length; i++)
            {
                checkboxs[i].checked = false;
            }
        }
    }
    else
    {
        if ( document.forms[form_name].all_top.checked == false  ) // && document.forms[form_name].all_bottom.checked == false
        {
            for ( var i = 0; i < checkalls.length; i++)
            {
                checkalls[i].checked = true;                
            }
            for ( var i = 0; i < checkboxs.length; i++)
            {
                checkboxs[i].checked = true;
            }
        }
        else if ( document.forms[form_name].all_top.checked == true  ) // && document.forms[form_name].all_bottom.checked == true
        {
            for ( var i = 0; i < checkalls.length; i++)
            {
                checkalls[i].checked = false;
            }
            for ( var i = 0; i < checkboxs.length; i++)
            {
                checkboxs[i].checked = false;
            }
        }
    }
    //
    return false;
}

function form_checkbox(this_checkbox, form_name)
{
    if(this_checkbox == null || form_name == null || this_checkbox == "" || form_name == "")
    {
        return false;
    }
    //dieupt21072016
    if(!document.forms[form_name] || document.forms[form_name] == null)
    {
        return false;
    }
    
    //
    var checkbox = this_checkbox.parentElement.getElementsByClassName("checkbox");
    var checkalls = document.forms[form_name].querySelectorAll(".checkbox_all");
    var checkbox_name = "";
    //dieupt21072016
    if(!checkbox || checkbox == null || !checkalls || checkalls == null)
    {
        return false;
    }
    //
    for(var i=0; i<checkbox.length;i++){
        checkbox_name = checkbox[i].name;
    }
    //
    var total_checkbox = document.forms[form_name].querySelectorAll('.checkbox').length;
    var total_checked = document.forms[form_name].querySelectorAll('.checkbox:checked').length;
    //
    
    if(document.forms[form_name].elements[checkbox_name].checked == true)
    {
        for ( var i = 0; i < checkalls.length; i++)
        {
            checkalls[i].checked = false;
        }
    }
    else
    {
        if(total_checked + 1 == total_checkbox)
        {
            for ( var i = 0; i < checkalls.length; i++)
            {
                checkalls[i].checked = true;
            }
        }
    }    
}

function get_default_item(ass_value)
{
    $.get(
        site_root_domain+"/?site=assets&act=search&is_ajax=1&ass_input="+ass_value+"&search_name=product&is_get_one=1",
	function(html) 
        {
            list_item = JSON.parse(html) ? JSON.parse(html) : "";

            if(list_item && list_item!='undefined')
            {
                    var first_row = $("#data_table tr:first");
                    $("#data_table").html("");
                    
                    // Append new list item
                    for(var i=0;i < list_item.length;i++)
                    {
                        var is_last_row = i == (list_item.length - 1) ? 1 : 0;
                        append_list_item(first_row,is_last_row,i);
                    }
                    
                    
					
                    // Set value for tr
                    var j = 0;
                    $("#data_table tr").each(function()
                    {
                        $(this).find(".tr_name").val(list_item[j]['product_name']);
                        $(this).find(".tr_code").val(list_item[j]['product_code']);
                        $(this).find(".tr_pprice").val(list_item[j]['product_price']);
                        $(this).find(".tr_quantity").val(list_item[j]['product_quantity']);
                        
                        j ++;
                    });

		}
	}
    );
}

function get_group_product(type, defaultVal = '')
 {

   $.ajax({
                type: "post",
                url: site_root_domain+"/?site=product&subact=search_g_product_ajax",
                data: {p_type: 0},
                success: function(html)
                {

                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        output_li = "<option value=''>--- Chọn ---</option>";
                        $.each(obj.data_option, function(index, value)
                        {                       
                            output_li += "<option value='"+value.product_group_id+"' > "+value.product_group_name+"</option>";
                        });
                    }
  
                    $(".select_product_group").html(output_li);
                    select2SetValue($("form#form-signin_v1 [name='p_product_group']"), defaultVal);
                    select2SetValue($("form#form-signin_v1 [name='pgroup_id']"), defaultVal);
                }
            });
 }
 
 function popup_add_product()
 {
     // Reset param is add success
     $("#is_add_success").val(0);
      var row_main = $(this).parents(".row-grid.active");
      var product_name = $("#ass_name").val();
      var product_description = row_main.find("textarea[for='dgrid-3']").val();
      var product_price = row_main.find("input[for='dgrid-5']").val();
      var product_tax = row_main.find("select[for='dgrid-7']").val();
      var item_id = row_main.find("input[name='item_id[]']").val();
      var sup_id = $("#supplier_id").val();
      var group_id = $("#pgroup_id").val();
      // $("#edit_product_form")[0].reset();
      clearForm($("#edit_product_form"));
      var html_line = $("#edit_product_form .data_subitem tr:last-child").html();
      $("#edit_product_form .data_subitem").html("<tr class='row-item' rowtr='last-row'>"+html_line+"</tr>");
      $("#edit_product_form .data_subitem").find(".number").html("#1");
      // Đỗ dữ liệu
      $("#box_product input[name='product_name']").val(product_name);
      $("#box_product textarea[name='product_description']").val(product_description);
      $("#box_product input[name='product_price']").val(product_price);
      $("#box_product input[name='product_tax']").val(product_tax);
      $("#box_product input[name='item_id']").val(item_id);
      
      $("#box_product select[name='sup_id'] option[value='"+sup_id+"']").prop("selected", true);
      $("#box_product select[name='sup_id'] option[value='"+sup_id+"']").trigger("change");
      
      $(".select_pg option[value='"+group_id+"']").prop("selected", true);
      $(".select_pg").trigger("change");
      
      $(".box_result_search").html("");
      $(".box_result_search").hide();
      //$(".form_data .box_edit").html('');
      calculate_subitem();
      // Change title product and button product
      $(".change_title_product").html("Thêm sản phẩm");
      $(".box_control").html("<input class='btn btn_add_product' type='button' value='Thêm sản phẩm'/>");
      $.magnificPopup.open({
        type: 'inline',
        items: {
          src: '#box_product'
        },
        callbacks:{
            afterClose: function() {
                if($("#is_add_success").val() == 0)
                {
                   // Clear data ass_name
                   $("#ass_name").val("");
                }
                
            }
        }
      });
    }
    
    $(".move-subitem").click(function(e)
    {
                // $('#move_subitem_form')[0].reset();
        clearForm($("#move_subitem_form"));
		$(".change_action_move_subitem").html("<input class='btn btn_move_subitem' type='button' value='Di chuyển'>");
                $("#ass_name_to").prop('disabled', false); 
                
		var is_exist = 0;
		// Check subitem for move
		$('#list_subitem_for_move').val("");
            $(".subitem-for-move").each(function(){
              var id = $(this).val();
              if($(this).prop( "checked" ) && $(this).val())
              {
				var data = $('#list_subitem_for_move').val();
				$('#list_subitem_for_move').val(data+id+",");
				is_exist = 1;
              }
          });
		  
		  if(is_exist == 0)
		  {
			  alertText(cms_lang['gnotice_select_asset_before_move']);return false;
		  }
        
      $('#ass_list_to').hide(); 
      $('#ass_name_to').val("");
      e.stopPropagation();
      // $("#move_subitem_form")[0].reset();
    clearForm($("#move_subitem_form"));
      $.magnificPopup.open({
        type: 'inline',
        items: {
          src: '#box_move_subitem'
        },
      });
      
      return true;
  });
  
  $(document).ready(function(){
  $('#ass_is_move_to_item').click(function()
  {
      if($('#ass_is_move_to_item').prop( "checked" ))
      {
        $("#ass_name_to").val("");
        $("#ass_name_to").prop('disabled', true);
      }
      else
      {
        $("#ass_name_to").prop('disabled', false);  
      }
  });
  $('#ass_name_to').keyup( function() {
        if ($('#ass_name_to').val() != '') {
            $('#ass_list_to').show();
            $('#ass_list_to').html(inputLoading());

            $.get(
                site_root_domain+"/?site=assets&act=search&is_ajax=1&ass_input="+($('#ass_name_to').val())+"&search_name=assets&ass_expect_id="+ass_expect_id,
                { key: $('#ass_name_to').val() },
                function(html) {
                    console.log(html);
                    $('#ass_list_to').html("<select multiple class=\"form-control\" onchange=\"update_data_move(this)\">" + html + "</select>");
                }
            );
        } else {
            $('#ass_list_to').hide();
        }
    })
    });
    
function update_data_move(ob)
{
    if(!ob.value)
    {
        return false;
    }
    
    var data = ob.value.split("|");
    
    $("#product_id_to").val(data[0]);
    $("#ass_name_to").val(data[1]);
    $('#ass_list_to').hide();
}

  $(document).ready(function(){
    $("#box_move_subitem").on("click", ".btn_move_subitem",function(){
        if($("form#move_subitem_form").validnew()){ 
		
            // do stuff if form is valid
            var data = $("form#move_subitem_form").serialize();
            checkDoubleClick(".btn_move_subitem",1);
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=assets&act=move_subitem",
                data: data,
                success: function(html)
                {
                    $("p.error_msg").hide();
                    $("p.error_msg").html('');
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                        $.magnificPopup.close();
                        alertText(obj.msg, "success");
						
						// Disable tr subitem
						var list_subitem_moved = obj.list_sub_moved;
						list_subitem_moved = list_subitem_moved.split(",");
                                                console.log(list_subitem_moved);
						for(var i=0;i<list_subitem_moved.length;i++)
						{
							$('.move_subitem_row_'+list_subitem_moved[i]).html("");
						}
                    }else
                    {
                        $("p.error_msg").show();
                        $("p.error_msg").css("color","red");
                        $("p.error_msg").css("font-size","14px");
                        $("p.error_msg").html(obj.msg);
                        setTimeout(function(){ $("p.error_msg").fadeOut();},2000);
                        checkDoubleClick(".btn_move_subitem",0);
                        return false;
                    }
                }
            })
        } else {
            // do stuff if form is not valid
            validator.focusInvalid();
            return false;
        }
    });
	
	$('#ass_userg_id').change(function(){
		$.ajax({
				type: "post",
                url: site_root_domain + "/?site=assets&subact=get_list_user&group="+$(this).val(),
                success: function(html)
				{
					console.log(html);
					$("#ass_user_id").html(html);
				}
		})
	})
        
        $(".edit_product").click(function(){
            popup_edit_product();
        })
  })
  
function submit_action_control_assets(onthis, form)
{
	var act = $(onthis).val();
	if(act == ""){return false;}
        
        if(act == "assets_move")
        {
           popup_move_assets();
           return true;
        }
        
	$("form#"+form+" input[name='act']").val(act);
	$("form#"+form+" input[name='subact']").val(act);
	$("form#"+form).submit();

}
var ass_expect_id = 0;
function popup_move_assets()
{
        var is_exist = 0;
        var is_done = 1;
        $(".ass_record").each(function()
        {
           if($(this).prop("checked") && $(this).val())
           {
               if($(this).attr("is_exist_subitem") == 1)
               { 
                  $("#subact").val(""); 
                  is_done = 0;
                  alertText(cms_lang['gnotice_exist_related_asset']);
                  return false;
               }
               
                var data = $('#list_subitem_for_move').val();
		$('#list_subitem_for_move').val(data+($(this).val())+","); 
                
               is_exist = 1;
           }
        });
        
        if(is_exist == 0 && is_done == 1)
        {
           $("#subact").val(""); 
           alertText(cms_lang['gnotice_select_asset']);
           return false;
        }
        
      $('#ass_list_to').hide(); 
      $('#ass_name_to').val("");
      
      ass_expect_id = $('#list_subitem_for_move').val();
      
      if(is_done == 1)
      {
            // $("#move_subitem_form")[0].reset();
            clearForm($("#move_subitem_form"));
            $.magnificPopup.open({
            type: 'inline',
            items: {
            src: '#box_move_subitem'
            },
        }); 
      }
    }
	
function autocomplete_quick_search()
{
  
  $("#ass_quick_search").keyup(function(){

    if($(this).val().length < 2)
    {
       return false;
    }
    var key = $(this).val();
    $.ajax({
    type: "post",
    url: site_root_domain+"/?site=assets&subact=autocomplete_quick_search",
    data:{ ass_keyword : key },
    beforeSend: function(){
        $("#ass_quick_search").css("background","#FFF url(/acp/images/fb-loading.gif) no-repeat 165px");
    },
    success: function(data){
              var obj = JSON.parse(data);
               $("#suggesstion-box").html("");
               if(obj.status == "success")
               {
                  output_li = "<ul class='list_goods'>"+obj.data_option+"</ul>"
                  $("#suggesstion-box").show();
                  $("#suggesstion-box").html(output_li);
                  $("#ass_quick_search").css("background","#FFF");

               }
               else
               {
                  $("#suggesstion-box").hide();
                  $("#suggesstion-box").html("");
                  $("#ass_quick_search").css("background","#FFF");
               }

    }
    });
  });

}

function autosubmit_frm_qs_product()
{
  $("form#frm_quick_search_ass").submit();
}

function quick_search_submit()
{
	$('#quick_search_keyword').val($('#ass_quick_search').val());
	$("form#formsearch_adv").submit();
	//return false;
}

function check_quick_search()
{
	$('#quick_search_keyword').val($('#ass_quick_search').val());
}



function print_barcode_popup(key)
{
    $("#preview_barcode_button").attr("onclick", `preview_barcode_popup('${key}')`);

    let assInfo = $('[a_key="' + key +  '"]');
    let name = assInfo.attr('a_name')
    let quantity = assInfo.attr('a_quantity')
    let price = assInfo.attr('a_price')

    $("#print_to_excel_button").attr("href",`?site=assets&act=print_to_excel&name=${name}&quantity=${quantity}&price=${price}` )
    $.magnificPopup.open({
        type: 'inline',
        items: {
            src: '#print_barcode_popup'
        },
    });
}

function preview_barcode_popup(key)
{
    $.ajax({
        type: 'get',
        url: site_root_domain + '/?site=assets&act=get_barcode_info&key=' + key,
        dataType: 'json',
        success: (res) => {
            if(res.status == 'ok')
            {
                $("#preview_barcode_popup").find("iframe").attr("src", site_root_domain + `?site=assets&act=preview_barcode&name=${res.data.name}&group=${res.data.group}&barcode=${res.data.barcode}&price=${res.data.price}`);
                $("#preview_barcode_popup").find("a#print_barcode").attr("href", site_root_domain + `?site=assets&act=preview_barcode&name=${res.data.name}&group=${res.data.group}&barcode=${res.data.barcode}&price=${res.data.price}`);
                $("#preview_barcode_popup").find("a#export_barcode_to_excel").attr("href", site_root_domain + `?site=assets&act=preview_barcode&name=${res.data.name}&group=${res.data.group}&barcode=${res.data.barcode}&price=${res.data.price}&save_as=excel`);
                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#preview_barcode_popup'
                    },
                });
            }
            else
            {
                call_notify(cms_lang['gnotice'], res.msg, "danger", "");
            }
        }
    });


}
    



