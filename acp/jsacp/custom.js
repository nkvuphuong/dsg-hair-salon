// Variant
var count_row = 1;
// var number_last
var module_name= module_name ? module_name : "";

$(document).ready(function(){
 
//  $("#btn_aaa").click(function(){

//   call_notify(cms_lang['gnotice'], "Tôi muốn ab11c", 'danger' ,"");

// });

    $.magnificPopup.instance._onFocusIn = function(e) {
        // Do nothing if target element is select2 input
        if( $(e.target).hasClass('select2-search__field') ) {
            return true;
        }
        // Else call parent method
        $.magnificPopup.proto._onFocusIn.call(this,e);
    };
 
//Hide susgestion autocomplete product subitem
 $('body').click(function() {
     $('.box_result_search').html(""); 
     $(".box_result_search").css("display","none");
     
  });

  $('.box_result_search  .list_goods, .box_result_search  .list_asset').click(function(event){
     event.stopPropagation();
  });

//End hide



$("#expand_formsearch").click(function() {
   $("#formsearch_adv").toggle();
 
});

  

    $('#data_table').click(function() {
       $('.box_result_find, .box_result_search').html(""); 
       $("#data_table .box_result_find, #data_table .box_result_search").css("display","none");
       
    });

    $('.box_result_find').click(function(event){
       event.stopPropagation();
    });

      $("#data_table").on("select",".grid-td",function(e){
          $(this).trigger("click");
      });

      $("#data_table").on("click",".grid-td",function(e){
         
        var echeck = e.target.getAttribute('check');

        if(echeck == "chtd")
        {
          var html_clone = $(this).parent().html();
          var focus_column = $(this).attr("for");
          // Check clone row
          var check = $(this).parent().attr("rowtr");
          count_row ++;
          backup_datatable();
          if(check == "last-row") /// Tang roww
          {
            // console.log(html_clone);
              // count_row ++;
             $('#table_item').DataTable().destroy();
             $(this).parent().attr("rowtr","");
             $("#data_table").append("<tr class='row-grid' rowtr='last-row'>"+html_clone+"</tr>");
            
         //   var newRow = "<tr    class='row-grid' rowtr='last-row'>"+html_clone+"</tr>";
 //$('#table_item').dataTable().fnAddData(newRow);

            
 
              $("#data_table tr[rowtr='last-row']").find("input,textarea,select").val('');
              $("#data_table tr[rowtr='last-row']").find("[name='product_discount_value[]']").val("0");
              $("#data_table tr[rowtr='last-row']").find("[name='product_discount_type[]']").val("0");
              $("#data_table tr[rowtr='last-row']").find("[name='product_commission_type[]']").val("0");
              $("#data_table tr[rowtr='last-row']").find("[name='product_commission_value[]']").val("0");

              if( $("#data_table tr[rowtr='last-row']").find(".commission_show").length)
              {
                  showCommissionItem(0,0,$("#data_table tr[rowtr='last-row']").find(".commission_show"));
              }

              // Plong: gán giá trị mặc định quantity = 1, thue - 10% khi them row mới
              $("#data_table tr[rowtr='last-row']").find(".quan_list").val(1);
              $("#data_table tr[rowtr='last-row']").find(".tax_list option[value=10]").prop("selected",true);
    
 
              // insert number
              var count_check = 1;
              var inc_item = 0;
              $("#data_table tr").each(function()
              {
                  $(this).find(".number").html("#"+count_check);
                  $(this).find(".item_id").val(inc_item);
                  $(this).find(".del_row_invoid").attr("item_id",inc_item);
                  count_check ++;
                  inc_item ++;
              });

                  
                 // $('#table_item').DataTable({
                 //     'responsive': true 
                 //  });

                 init_datatable();
                 backup_datatable();

          }
           
          // $(".grid-td").children().show();
          var row_tr = $(this).parent();
          var keyrow = row_tr.attr("keyrow");
          if (typeof keyrow !== typeof undefined && keyrow !== false) {        
          }
          else
          {
                keyrow = 1;
          }
          
          // Dong cac row khong active
          if(is_mobile == 0)
          {
            // Check trên mobile thi ẩn border input 
            $(this).parent().parent().find("input, textarea, select").addClass("hidden_border");
          }
     
          $(this).parent().parent().find("textarea").css("resize","none");
          $(this).parent().parent().find("span.dropdown_btn").hide();


          $("#keyrow_active").val(keyrow); 
          $(".row-grid").removeClass("active");
          $(this).parent().addClass("active");
          var check_row = $(this).parent().attr("class");
    
          if(check_row == "row-grid active") 
          {
          
            $(this).children("[for='"+focus_column+"']").focus();
            $(this).parent().find("input, textarea, select").removeClass("hidden_border");
             
            $(this).parent().find("textarea").css("resize","vertical");
            $(this).parent().find("span.dropdown_btn").show();
            $(".box_result_search").html("");
            $(".box_result_search").hide();
          
          }else
          {

          }

          

        }else if(echeck == "chspan")
        {

          var forkey = $(this).attr("for");
          search_goods("",forkey, $(this));

        }  

        var module_name;
        // Remove check total price if module is assets
        if(module_name != 'assets')
        {

           $("#data_table tr").each(function(e)
            {
            // Check quantity
            var numberC = $(this).find("input[for='dgrid-5']").val();
            var checkrow = $(this).attr("checkrow");
       
            if((numberC == 0 || numberC == '') && checkrow != "normal")
            {
                // $(this).find("input[for='dgrid-5']").val(1); Tanlv tạm thời đóng vì không muốn set don giá bằng 1 bên phiếu yêu cầu.
                var qty = 1;
                var tax = $(this).find("select[for='dgrid-7']").val();
                var price = $(this).find("input[for='dgrid-6']").val();
                
                qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 0;
                price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
                tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;

                var total = qty * price;
                var price_tax = Math.round((tax * total)/100);
                total = total+price_tax;

                $(this).find("input[for='dgrid-6']").val(total);

                // Total show
                show_total_goods();
            }
            }); 
        }
        


      });
      // $("#data_table").on("blur",".grid-td",function(e){
      //   var echeck = e.target.getAttribute('check');
      //   console.log(echeck);
          
      // });

      // show info goods
      $("#data_table").on("keyup",".search_product",function(e){
        var key_search = $(this).val();
        var forkey = $(this).attr("for");
        search_goods(key_search,forkey, $(this));
        // Xoá info product
        $(this).parent().find("input[name='product_id[]']").val('');
        var item_id = $(this).parent().find("input[name='item_id[]']").val();
        $.ajax({
              type: "post",
              url: site_root_domain + "/?site=store_request&subact=remove_product",
              data: {item_id: item_id},
              success: function(html) {
                // console.log(html);
              }
        });

      });

      // Xoá row
      $("#data_table").on("click",".del_row_invoid",function(){
        var obj = $(this).parent().parent();
        var count_del = $("#data_table tr").length;
        // console.log(count_del);
        if(count_del > 1)
        {
          $(this).parent().parent().remove();
          show_total_goods();

          // insert number
          var count_check = 1;
          var inc_item = 0;
          $("#data_table tr").each(function()
          {
              $(this).find(".number").html("#"+count_check);
              $(this).find(".item_id").val(inc_item);
              $(this).find(".del_row_invoid").attr("item_id",inc_item);
              count_check ++;
              inc_item ++;
              if(count_check == count_del)
              {
                $(this).attr("rowtr","last-row");
              }
              
          });

          // remove product
          var item_id = $(this).attr("item_id");
          $.ajax({
                type: "post",
                url: site_root_domain + "/?site=store_request&subact=remove_product",
                data: {item_id: item_id, type: 1},
                success: function(html) {
                  // console.log(html);
                }
          });

            if(typeof calculate_total_transaction == "function")
            {
                calculate_total_transaction();
            }
        }

      });


    /********************************************************************************** 
    *   ADD PRODUCT
    ***********************************************************************************/
    // open popup
    $("#data_table").on("click",".add_new_goods",function(){
     
      var row_main = $(this).parents(".row-grid.active");
      var product_name = row_main.find("input[for='dgrid-2']").val();
      var product_description = row_main.find("textarea[for='dgrid-3']").val();
      var product_price = row_main.find("input[for='dgrid-5']").val();
      var product_tax = row_main.find("select[for='dgrid-7']").val();
      var item_id = row_main.find("input[name='item_id[]']").val();
      var sup_id = $("#form_add_request select[name='supplier_id']").val();
      // console.log($(this).parent());  
      // console.log(row_main);
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
      
      $(".box_result_search").html("");
      $(".box_result_search").hide();
      $(".form_data .box_edit").html('');
      calculate_subitem();
      // Change title product and button product
      $(".change_title_product").html(lang_product_add);
      $(".box_control").html("<input class='btn act_popup_product_btn '  aclass='btn_add_product' type='button' value='"+lang_btn_add_save+"'/>");
      validate_form_custom("#edit_product_form",".act_popup_product_btn","box_custom");
      $.magnificPopup.open({
        type: 'inline',
        items: {
          src: '#box_product'
        },
      });
    
    });

    // Add product
    $("#edit_product_form").on("click", ".btn_add_product", function(){
      var pro_name = $("#edit_product_form input[name='product_name']").val();
      // Check product group
      var pg_id = $("#edit_product_form select[name='product_group']").val();
      
      $("#edit_product_form .error_msg").css("color", "red");
      $("#edit_product_form .error_msg").css("font-style", "italic");
      $("#edit_product_form .error_msg").css("font-size", "14px");
      $("#edit_product_form .error_msg").css("margin", "0px");
      if(pro_name == "")
      {
        $("#edit_product_form .error_msg").show();
        $("#edit_product_form .error_msg").html(cms_lang['gnotice_fill_product_name']);
        $("#edit_product_form input[name=product_name]").focus();
        return false;
      }

      if(pg_id == "")
      {
        $("#edit_product_form .error_msg").show();
        $("#edit_product_form .error_msg").html(cms_lang['gnotice_select_product_group']);
        $("#edit_product_form select[name=product_group]").focus();
        return false;
      }
        $("#box_product #ufile_output_b64").val('');
        var data = $("#edit_product_form").serialize();
        var file_data = $("input[name='product_image']").prop("files")[0];
        var item_id = $("input[name='item_id']").val();
        var form_data = new FormData();
        form_data.append("upload_img", file_data);
        $(".error_msg").hide();
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=product&subact=ajax_add_product&item_id="+item_id+"&"+data,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){},
            success: function(html)
            {
                csrf_token();

               var obj = JSON.parse(html);
             
               $("#edit_product_form .error_msg").html('');
               if(obj.status == "error")
               {
                  /*$("#edit_product_form .error_msg").css("color", "red");
                  $("#edit_product_form .error_msg").css("font-style", "italic");
                  $("#edit_product_form .error_msg").css("font-size", "14px");
                  $("#edit_product_form .error_msg").css("margin", "0px");
                  $("#edit_product_form .error_msg").show();
                  $("#edit_product_form .error_msg").html(obj.msg);*/
                  pNotifyACP(obj.msg);
                  $("#edit_product_form input[name=product_name]").focus();
                   
               }else
               {
                  // $("#edit_product_form")[0].reset();
                  clearForm($("#edit_product_form"));

                  // IF module is asset, will not set data table
                  if(module_name == "assets")
                  {
                      $("#ass_name").val(obj.data.product_name);
                      $("#product_id").val(obj.data.product_id);
                  }
                  // End assets
                  else
                  {
                        // Insert data just add new
                        var cur_row_active = $("#keyrow_active").val();

                        var row = $("#data_table tr.active");

                        $(row).find('input[name="product_name[]"]').val(obj.data.product_name);
                        $(row).find('input[name="product_id[]"]').val(obj.data.product_id);
                        $(row).find('textarea[name="product_description[]"]').val(obj.data.product_description);
                        $(row).find('input[name="product_quantity[]"]').val(1);
                        $(row).find('input[name="sup_name[]"]').val(obj.data.sup_name);
                        $(row).find('input[name="sup_id[]"]').val(obj.data.sup_id);
                        $(row).find('input[name="product_price[]"]').val(obj.data.product_price);
                        $(row).find('select[name="product_tax[]"]').val(obj.data.product_tax);
                        $(row).find('td.trash').html("" +
                            "<span style='cursor: pointer;' class='edit_row_product' id="+obj.data.product_id+" item_id='"+item_id+"'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span>" +
                            "<span class='del_row_invoid' item_id='"+item_id+"'><i class='fa fa-trash' aria-hidden='true'></i></span>");
                        calculate_money();

                         //Check loai hang thang va show cycle
                         let option = '';
                         if(obj.data.product_type == 0)//Hang hoa
                         {
                             option += "<option value='1'>"+cms_lang.cycle_once+"</option>";
                         }
                         else
                         {
                             if(obj.data.product_cycle == 1)//Hang tháng
                             {
                                 for(var i=1; i<=12; i++)
                                 {

                                     option += "<option value='"+i+"'>"+i+" "+cms_lang.cycle_monthly+"</option>";
                                 }
                             }
                             if(obj.data.product_cycle == 2)//Hang năm
                             {
                                 for(var i=1; i<=12; i++)
                                 {

                                     option += "<option value='"+i+"'>"+i+" "+ cms_lang.cycle_yearly+" </option>";
                                 }
                             }

                         }


                         $(row).find('select[name="product_cycle[]"]').html(option);

                        $("#product_id").val(obj.data.product_id);

                        $("#is_add_success").val(1);                  
                    }
                  
                  
                  $.magnificPopup.close();
                  //alertText(obj.msg, 'success');
                  call_notify("Product",obj.msg, 'success','');
                  
               }
            }
        });
    });


    // open popup edit product
    $("#data_table").on("click",".edit_row_product",function(){
      waitingDialog.show(cms_lang.waiting_dialog_msg);
      $(".row-grid").removeClass("active");
      $(this).parent().parent().addClass("active");
      var keyrow = $(this).parent().parent().attr("keyrow");
      if (typeof keyrow !== typeof undefined && keyrow !== false) {        
      }
      else
      {
            keyrow = 1;
      }
      // Dong cac row khong active
      $(this).parents("#data_table").find("input, textarea, select").addClass("hidden_border");
      $(this).parents("#data_table").find("textarea").css("resize","none");
      $(this).parents("#data_table").find("span.dropdown_btn").hide();

      // Active dòng hiện tại
      $(this).parent().parent().find("input, textarea, select").removeClass("hidden_border");
      $(this).parent().parent().find("textarea").css("resize","vertical");
      $(this).parent().parent().find("span.dropdown_btn").show();
       
      $("#keyrow_active").val(keyrow); 
      $(".box_result_search").html("");
      $(".box_result_search").hide();
      // Change title product and button product
      $(".change_title_product").html(lang_product_edit);
      $(".box_control").html("<input class='btn act_popup_product_btn' aclass='btn_edit_product_0' type='button' etype='0' value='"+lang_btn_edit_save+"'/> <input class='btn act_popup_product_btn' aclass='btn_add_to_request' type='button' value='"+lang_btn_save_request+"'/> <input class='btn act_popup_product_btn' aclass='btn_edit_product_1' type='button' etype='1' value='"+lang_btn_add_save+"'/>");
      validate_form_custom("#edit_product_form",".act_popup_product_btn","box_custom");
      // Load type_product_edit by site =order, transaction

      var type_product_edit = $(this).attr("type_product_edit");
 
      var product_id = $(this).attr("id");
      var item_id = $(this).attr("item_id");
      // Ajax get data
      $.ajax({
          type: "get",
          url: site_root_domain + "/?site=product&subact=ajax_edit_product&type_product_edit=1",
          data: {product_id: product_id, item_id: item_id,type_product_edit: type_product_edit},
          dataType: 'json',
          success: function(obj)
          {
              waitingDialog.hide();
              // var obj = JSON.parse(html);
             
              var manufacture_id = obj.product_manufacture > 0 ? obj.product_manufacture : "";
              var product_cycle = obj.product_cycle > 0 ? obj.product_cycle : "";
              var sup_id = obj.sup_id > 0 ? obj.sup_id : "";
              // $("#box_product input[name='product_type'][value='"+obj.product_type+"']").prop("checked",true);

              loadProductCycleType(obj.product_type);

              $("#box_product input[name='product_name']").val(obj.product_name);
              $("#box_product input[name='product_id']").val(product_id);
              $("#box_product input[name='item_id']").val(item_id);
              $("#box_product input[name='product_code']").val(obj.product_code);
              $("#box_product input[name='product_barcode']").val(obj.product_barcode);
              // Supplier
              $("#box_product select[name='sup_id'] option[value='"+sup_id+"']").prop("selected",true);
              $("#box_product select[name='sup_id']").trigger("change");

              // Product group
              $("#box_product .product_type").val(obj.product_type);
              load_productgroup_byType(obj.product_type,"#box_product select[name='product_group']");
              if(module_name == "module_order" || module_name == "module_transaction")
              {
                // Check module form order - init selected product type select
                //$("#box_product select[name='product_service_type'] option[value='"+obj.product_type+"']").prop("selected",true);
                $("#box_product select[name='product_service_type']").val(obj.product_type).trigger("change");
              }
              setTimeout(function(){
                  // $("#box_product select[name='product_group'] option[value='"+obj.product_group+"']").prop("selected",true);
                  select2SetValue($("#box_product select[name='product_group']"),obj.product_group)
                  }, 1000);
              
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

            

              $("#box_product .data_subitem[for='edit']").html(obj.html_subitem);
              $(".data_subitem select.auto_select").each(function(){
                var val_default = $(this).attr("defaultvalue");
                // console.log(val_default);
                $(this).find("option[value='"+val_default+"']").prop("selected",true);
              });
              if(obj.product_image)
              {
                  var url_img = site_parent_domain+"/uploads/product/"+obj.product_image;
                  $("#box_product #upload_img_show").attr("src",url_img);
                  // $("#box_product #upload_img_show").html("<img src='"+url_img+"' style='max-height: 90px; max-width: 90px; margin: 0 auto;' />");
              }

              
              $("#box_product textarea[name='product_description']").val(obj.product_description);
              $("#box_product input[name='product_price']").val(obj.product_price);
             // $("#box_product input[name='product_price_original']").val(obj.product_price_original);
              $("#box_product input[name='product_price_sell']").val(obj.product_price_sell);
              $("#box_product select[name='product_tax'] option[value='"+obj.product_tax+"'").prop("selected", true);

              var price = parseFloat(obj.product_price);
              //var price_original = parseFloat(obj.product_price_original);
             // var price_sell = parseFloat(obj.product_price_sell);
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

    
    });


    $("#edit_product_form").on("click", ".btn_edit_product", function(){

        var pro_name = $("#edit_product_form input[name='product_name']").val();
        // Check product group
        var pg_id = $("#edit_product_form select[name='product_group']").val();
        
        $("#edit_product_form .error_msg").css("color", "red");
        $("#edit_product_form .error_msg").css("font-style", "italic");
        $("#edit_product_form .error_msg").css("font-size", "14px");
        $("#edit_product_form .error_msg").css("margin", "0px");
        if(pro_name == "")
        {
          $("#edit_product_form .error_msg").show();
          $("#edit_product_form .error_msg").html(cms_lang['gnotice_fill_product_name']);
          $("#edit_product_form input[name=product_name]").focus();
          return false;
        }

        if(pg_id == "")
        {
          $("#edit_product_form .error_msg").show();
          $("#edit_product_form .error_msg").html(cms_lang['gnotice_select_product_group']);
          $("#edit_product_form select[name=product_group]").focus();
          return false;
        }

        $("#box_product #ufile_output_b64").val('');  
        var data = $("#edit_product_form").serialize();
        var file_data = $("#edit_product_form input[name='product_image']").prop("files")[0];
        var etype = $(this).attr("etype");
       
        var form_data = new FormData();
        var link_type = "";
        if(etype == 1)
        {
            link_type = "type=1&";
        }

        form_data.append("upload_img", file_data);
        $(".error_msg").hide();
        
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=product&subact=ajax_edit_product_do&"+link_type+data,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){},
            success: function(html)
            {
                csrf_token();
               // console.log(html);
               var obj = JSON.parse(html);
             
               $("#edit_product_form .error_msg").html('');
               if(obj.status == "error")
               {
         

                  /*$("#edit_product_form .error_msg").css("color", "red");
                  $("#edit_product_form .error_msg").css("font-style", "italic");
                  $("#edit_product_form .error_msg").css("font-size", "14px");
                  $("#edit_product_form .error_msg").css("margin", "0px");
                  $("#edit_product_form .error_msg").show();
                  $("#edit_product_form .error_msg").html(obj.msg);*/

                  pNotifyACP(obj.msg);

                  $("#edit_product_form input[name=product_name]").focus();
                   
               }else
               {
                  // $("#edit_product_form")[0].reset();
                  clearForm($("#edit_product_form"));
                  
                  // Insert data just add new
                  var cur_row_active = $("#keyrow_active").val();
       
                  /*$("#data_table tr").each(function(i,row){
                   var cur_key = i + 1;
                              
                   if(cur_key == cur_row_active)
                   {
                       // console.log(cur_row_active);
                        $(row).find('input[name="product_name[]"]').val(obj.data.product_name);
                        $(row).find('input[name="product_id[]"]').val(obj.data.product_id);
                        $(row).find('textarea[name="product_description[]"]').val(obj.data.product_description);
                        $(row).find('input[name="product_quantity[]"]').val(1);
                        $(row).find('input[name="product_price[]"]').val(obj.data.product_price);
                        $(row).find('select[name="product_tax[]"] option[value="'+obj.data.product_tax+'"]').prop("selected", true);
                        
                        calculate_money();
                   }
                    
                         
                  });*/

                  var parentSelector = $("#data_table tr.active");
                    // Convert date is zero
                  if(obj.data.sup_id == 0){obj.data.sup_id = '';}
                  if(obj.data.sup_id == 0){obj.data.sup_id = '';}
                   parentSelector.find('input[name="product_name[]"]').val(obj.data.product_name);
                   parentSelector.find('input[name="product_id[]"]').val(obj.data.product_id);
                   parentSelector.find('textarea[name="product_description[]"]').val(obj.data.product_description);
                   parentSelector.find('input[name="product_quantity[]"]').val(1);
                   parentSelector.find('input[name="sup_name[]"]').val(obj.data.sup_name);
                   parentSelector.find('input[name="sup_id[]"]').val(obj.data.sup_id);
                   parentSelector.find('input[name="product_price[]"]').val(obj.data.product_price);
                   parentSelector.find('select[name="product_tax[]"] option[value="'+obj.data.product_tax+'"]').prop("selected", true);

                   calculate_money();

                    $.magnificPopup.close();
 

                  loadProductCycle(obj.data.product_cycle, parentSelector.find("[name='product_cycle[]']"));
                  
                  $("#ass_name").val(obj.data.product_name);
                
                  //alertText(obj.msg, 'success');
                  call_notify("Sản phẩm",obj.msg, 'success', '' )


                  
               }
            }
        });
    });
    
    /********************************************************************************** 
    *   END ADD PRODUCT
    ***********************************************************************************/


    function show_alert_onpage(ele,msg,type='success') {
      // Hide all msg
      $("#"+ele +" .alert-danger").hide();
      $("#"+ele +" .alert-aquamarine").hide();
      if(type== 'success')
      {
          $("#"+ele +" .alert-aquamarine .error_msg").html(msg);
          $("#"+ele +" .alert-aquamarine .error_msg").show();
          $("#"+ele +" .alert-aquamarine").show();
            setTimeout(
            function() 
            {
               $.magnificPopup.close();
            }, 3000);
 
      }
      if(type== 'error')
      {
          $("#"+ele +" .alert-danger .error_msg").html(msg);
          $("#"+ele +" .alert-danger .error_msg").show();
          $("#"+ele +" .alert-danger").show();
      }
      $("#scrolltop_popup").focus();
    }


    /********************************************************************************** 
    *   ADD SUBITEM PRODUCT
    ***********************************************************************************/
    $(".data_subitem").on("select",".item-td",function(e){
          $(this).trigger("click");
      });

    $(".data_subitem").on("click",".item-td",function(e){
        var echeck = e.target.getAttribute('check');
        var cform = $(this).parent().parent().attr("for");
       
        if(echeck == "chtd")
        {
          var html_clone = $(this).parent().html();
          var focus_column = $(this).attr("for");
          // Check clone row
          var check = $(this).parent().attr("rowtr");
         
          if(check == "last-row")
          {
            
              // count_row ++;
              // console.log(html_clone.html());
              $(this).parent().attr("rowtr","");
              $(".data_subitem[for='"+cform+"']").append("<tr class='row-item' rowtr='last-row'>"+html_clone+"</tr>");
              $(".data_subitem[for='"+cform+"'] tr[rowtr='last-row']").find("input, textarea").val('');
              $(".data_subitem[for='"+cform+"'] tr[rowtr='last-row']").find(".quan_list").val(1);
              $(".data_subitem[for='"+cform+"'] tr[rowtr='last-row']").find(".tax_list option[value=10]").prop("selected",true);
              // insert number
              var count_check = 1;
              $(".data_subitem[for='"+cform+"'] tr").each(function()
              {
                  $(this).find(".number").html("#"+count_check);
                  count_check ++;
                  
              });
          }
        
          // $(".item-td").children().show();
          
          $(".data_subitem[for='"+cform+"'] .row-item").removeClass("active");
          $(this).parent().addClass("active");
          var check_row = $(this).parent().attr("class");
          // console.log(check_row);
          if(check_row == "row-item active") 
          {
            $(this).parent().parent().find("input, textarea, select").addClass("hidden_border");
            $(this).parent().parent().find("textarea").css("resize","none");
            $(this).parent().parent().find("span.dropdown_btn").hide();
            $(".box_result_find").html("");
          
          }

          $(this).children("[for='"+focus_column+"']").focus();
          $(this).parent().find("input, textarea, select").removeClass("hidden_border");
          $(this).parent().find("textarea").css("resize","vertical");
          $(this).parent().find("span.dropdown_btn").show();

          
        }

        $(".data_subitem[for='"+cform+"'] tr").each(function()
        {
            // Check quantity
            var numberC = $(this).find("input[for='item-4']").val();
        
            if(numberC == 0 || numberC == '')
            {
                $(this).find("input[for='item-4']").val(1);
                var qty = 1;
                // Total show
                calculate_money_subitem(cform);
            }
        }); 

      });
     

      // show info goods
      $(".data_subitem").on("keyup",".find_product",function(){
          var key_search = $(this).val();
          var forkey = $(this).attr("for");
          var cform = $(this).parents(".data_subitem").attr("for");
       
          var obj_element = $(this);
          var product_id_input = "";
          // reset 
          var checkreset = $(this).next().val();
          
          if(checkreset) 
          {
            $(this).parent().parent().find("input[name='sub_product_price[]'], input[name='sub_product_id[]'], textarea").val('');
            $(this).parent().parent().find("input[name='sub_product_quantity[]']").val(1);
            $(this).parent().parent().find("select[name='product_tax[]'] option[value=10]").prop("selected",true);
            calculate_money_subitem(cform);
          }

          var product_type = $("#edit_product_form [name=product_type]:first").val();
          if($("#edit_product_form [name=product_id]").length > 0)
          {
             product_id_input = $("#edit_product_form [name=product_id]").val();
          }


            $.ajax({
                type: "post",
                url: site_root_domain+"/?site=store_request&act=search&subact=search_goods",
                data: {key_search: key_search, product_type: product_type, product_id : product_id_input},
                success: function(html)
                {
                  
                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        $(".box_result_find").html("");
                        $.each(obj.data, function(index, value)
                        {
                            var description = value.product_description === null ?  "" : value.product_description;
                            var func = 'show_detail_product(this,"'+cform+'")';
                            if(value.product_type == 0)
                            {
                              
                              output_li += "<li class='typeahead-item' onclick='"+func+"' for='"+forkey+"' name='"+value.product_name+"' description='"+description+"' id_product='"+value.product_id+"'  price='"+value.product_price+"'><span class='name_show' title='"+description+"'>"+value.product_name+" <i class='fa fa-gift'></i></span></li>";
                            }
                            else
                            {
                             output_li += "<li class='typeahead-item' onclick='"+func+"' for='"+forkey+"' name='"+value.product_name+"' description='"+description+"' id_product='"+value.product_id+"'  price='"+value.product_price+"'><span class='name_show' title='"+description+"'>"+value.product_name+" <i class='fa fa-recycle'></i></span></li>"; 
                            }


                        });
                    }

                     
                    var html_show = "<ul class='list_goods typeahead-list'>"
                                  +   output_li
                                  + "</ul>";
 
                    obj_element.parent().find(".box_result_find").html(html_show);
                    obj_element.parent().find(".box_result_find").show();
                    
                }
            });
      });

      // Xoá row
      $(".data_subitem").on("click",".del_row_invoid",function(){
        var obj = $(this).parent().parent();
        var cform = $(this).parent().parent().parent().attr("for");
        var count_del = $(".data_subitem[for='"+cform+"'] tr").length;

        if(count_del > 1)
        {
          $(this).parent().parent().remove();
          calculate_money_subitem(cform);
          // insert number
          var count_check = 1;
          $(".data_subitem[for='"+cform+"'] tr").each(function()
          {
              $(this).find(".number").html("#"+count_check);
              count_check ++;
              if(count_check == count_del)
              {
                $(this).attr("rowtr","last-row");
              }
              
          });
        }

      });

    /********************************************************************************** 
    *   END ADD SUBITEM PRODUCT
    ***********************************************************************************/






    /********************************************************************************** 
    *   ADD NCC
    ***********************************************************************************/
    
    $(".add_new_supplier").click(function(e){
        call_form_add_supplier(e);
    });

    // Add Store
    $(".select_store").change(function(){
      var store_id = $(this).val();
      if( store_id )
      {
        if( pms['store_edit'] == 1 )
        {
          $(".box_edit_store").html("<a id='"+store_id+"' class='btn_gen btn_edit_store pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
        }
      }else
      {
        $(".box_edit_store").html("");
      }
    });
    $(".select_store").trigger("change");
    $(".add_new_store").click(function(e){
      call_form_add_store(e);
    });
    $("form#form-signin_v1").on("click", ".btn_edit_store", function(e){
      call_form_edit_store(e, $(this).attr('id'));
    });

    $("#box_add_supplier").on("click", ".btn_add_supplier",function(){
        if($("form#add_supplier_form").validnew()){   
            // do stuff if form is valid
            var data = $("form#add_supplier_form").serialize();
            checkDoubleClick(".btn_add_supplier",1);
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=supplier&subact=ajax_add_supplier",
                data: data,
                success: function(html)
                {
                    csrf_token();

                    checkDoubleClick(".btn_add_supplier",0);
                    $("p.error_msg").hide();
                    $("p.error_msg").html('');
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                        $(".select_supplier, .select_supplier_asset").html(obj.data_option);
                        $(".select_supplier, .select_supplier_asset").trigger("change");
                        var checkreturn = $("input[name='checkReturn']").val();
                        $.magnificPopup.close();
                        if(checkreturn == "")
                        {
                          alertText(obj.msg, "success");
                        }else
                        {
                            // alertText(obj.msg, "success", checkreturn);
                            // $("p.error_msg").show();
                            // $("p.error_msg").css("color","green");
                            // $("p.error_msg").css("font-size","14px");
                            // setTimeout(function(){ $("p.error_msg").fadeOut();$("p.error_msg").css("color","red");},2000);
                            // $("p.error_msg").html(obj.msg);
                            call_notify("Nhà cung cấp",obj.msg,"info","");

                        }
                    }else
                    {
                        pNotifyACP(obj.msg);
                        /*$("p.error_msg").show();
                        $("p.error_msg").css("color","red");
                        $("p.error_msg").css("font-size","14px");
                        $("p.error_msg").html(obj.msg);
                        setTimeout(function(){ $("p.error_msg").fadeOut();},2000);*/
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

    // Edit NCC
      $("form.form_request, form#edit_product_form, form#edit_product_form, form#edit_asset_form, form#transactionForm").on("click", ".btn_edit_supplier", function(e){
        // $("form#add_supplier_form")[0].reset();
        clearForm($("form#add_supplier_form"));
        var sup_id = $(this).attr("id");

        $(".change_title").html(lang_supplier_edit);
        $(".change_action").html("<input class='btn act_popup_btn_validate' aclass='btn_edit_do' type='button' value='"+lang_supplier_edit+"'/>");
        
        // Validation style alert moi
        validate_form_custom("#add_supplier_form",".act_popup_btn_validate","box_custom");
        validate_form_custom("#add_manufacture_form",".act_popup_btn_validate","box_custom");
        e.stopPropagation();
        var val_return = $(this).parent().parent().attr("checkreturn");
        if(typeof(checkreturn) == undefined || val_return == "")
        {
            $("input[name='checkReturn']").val("");
        }else
        {
            $("input[name='checkReturn']").val(val_return);
        }
        
        // Ajax get data
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=supplier&subact=ajax_get_data_supplier",
            data: {supplier_id: sup_id},
            success: function(html)
            {
                
                var obj = JSON.parse(html);
                
                $("#add_supplier_form input[name='supplier_name']").val(obj.supplier_name);
                $("#add_supplier_form input[name='supplier_id']").val(obj.supplier_id);
                $("#add_supplier_form input[name='supplier_code']").val(obj.supplier_code);
                $("#add_supplier_form input[name='supplier_phone']").val(obj.supplier_phone);
                $("#add_supplier_form input[name='supplier_email']").val(obj.supplier_email);
                $("#add_supplier_form input[name='supplier_taxcode']").val(obj.supplier_taxcode);
                $("#add_supplier_form input[name='supplier_idcard_number']").val(obj.supplier_idcard_number);

                $("#add_supplier_form input[name='supplier_address']").val(obj.supplier_address);
                $("#add_supplier_form input[name='supplier_bank']").val(obj.supplier_bank);
                $("#add_supplier_form input[name='supplier_branch']").val(obj.supplier_branch);
                $("#add_supplier_form input[name='supplier_bank_number']").val(obj.supplier_bank_number);
                $("#add_supplier_form input[name='supplier_bank_owner']").val(obj.supplier_bank_owner);
                $("#add_supplier_form textarea[name='supplier_note']").val(obj.supplier_note);

                $("#add_supplier_form select[name='supplier_get_invoice'] option[value='"+obj.supplier_get_invoice+"']").prop("selected",true);
                $("#add_supplier_form select[name='supplier_type'] option[value='"+obj.supplier_type+"']").prop("selected",true);
                $("#add_supplier_form select[name='city_id'] option[value='"+obj.city_id+"']").prop("selected",true);
                $("#add_supplier_form select[name='city_id']").trigger("change");
                
                setTimeout(function(){
                    select2SetValue($("#add_supplier_form select[name='district_id']"), obj.district_id);
                    // $("#add_supplier_form select[name='district_id'] option[value='"+obj.district_id+"']").prop("selected",true);
                    }, 1000);
                
                $("#add_supplier_form .data_subitem[for='edit']").html(obj.html_subitem);
                
                $.magnificPopup.open({
                  type: 'inline',
                  items: {
                    src: '#box_add_supplier'
                  },
                });
                
            }
        });
        
    });


     // Edit NCC
      $("form#form-signin_v1").on("click", ".btn_edit_supplier", function(){
        // $("form#add_supplier_form")[0].reset();
        clearForm($("form#add_supplier_form"));
        var sup_id = $(this).attr("id");
        $(".change_title").html(lang_supplier_edit);
        $(".change_action").html("<input class='btn btn_edit_do' type='button' value='"+lang_supplier_edit+"'/>");

        // Ajax get data
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=supplier&subact=ajax_get_data_supplier",
            data: {supplier_id: sup_id},
            success: function(html)
            {
                var obj = JSON.parse(html);
                $("#add_supplier_form input[name='supplier_name']").val(obj.supplier_name);
                $("#add_supplier_form input[name='supplier_id']").val(obj.supplier_id);
                $("#add_supplier_form input[name='supplier_code']").val(obj.supplier_code);
                $("#add_supplier_form input[name='supplier_phone']").val(obj.supplier_phone);
                $("#add_supplier_form input[name='supplier_email']").val(obj.supplier_email);
                $("#add_supplier_form input[name='supplier_taxcode']").val(obj.supplier_taxcode);
                $("#add_supplier_form input[name='supplier_idcard_number']").val(obj.supplier_idcard_number);

                $("#add_supplier_form input[name='supplier_address']").val(obj.supplier_address);
                $("#add_supplier_form input[name='supplier_bank']").val(obj.supplier_bank);
                $("#add_supplier_form input[name='supplier_branch']").val(obj.supplier_branch);
                $("#add_supplier_form input[name='supplier_bank_number']").val(obj.supplier_bank_number);
                $("#add_supplier_form input[name='supplier_bank_owner']").val(obj.supplier_bank_owner);
                $("#add_supplier_form textarea[name='supplier_note']").val(obj.supplier_note);

                $("#add_supplier_form select[name='supplier_get_invoice'] option[value='"+obj.supplier_get_invoice+"']").prop("selected",true);
                $("#add_supplier_form select[name='supplier_type'] option[value='"+obj.supplier_type+"']").prop("selected",true);
                $("#add_supplier_form select[name='city_id'] option[value='"+obj.city_id+"']").prop("selected",true);
                $("#add_supplier_form select[name='city_id']").trigger("change");
                
                setTimeout(function(){
                    // $("#add_supplier_form select[name='district_id'] option[value='"+obj.district_id+"']").prop("selected",true);
                    select2SetValue($("#add_supplier_form select[name='district_id']"),+obj.district_id);
                    }
                    , 1000
                );
                
                $("#add_supplier_form .data_subitem[for='edit']").html(obj.html_subitem);
                
                $.magnificPopup.open({
                  type: 'inline',
                  items: {
                    src: '#box_add_supplier'
                  },
                });
                
            }
        });
        
    });


    
    
    $("#add_supplier_form").on("click",".btn_edit_do", function(){

        if($("form#add_supplier_form").validnew())
        {
            checkDoubleClick(".btn_edit_do",1);   
            // do stuff if form is valid
            var data = $("form#add_supplier_form").serialize();
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=supplier&subact=ajax_edit_supplier",
                data: data,
                success: function(html)
                {
                    csrf_token();
                    checkDoubleClick(".btn_edit_do",0);
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                        $("form.form_request select[name='supplier_id'] option[value='"+obj.data.supplier_id+"']").html(obj.data.supplier_name);
                        $(".select_supplier, .select_supplier_asset").html(obj.data_option);
                        // $("form#form-signin_v1 select[name='p_supplier'] option[value='"+obj.data.supplier_id+"']").html(obj.data.supplier_name);

                        select2SetValue($("form#form-signin_v1 select[name='p_supplier']"),obj.data.supplier_id);
                        select2SetValue($("form#form-signin_v1 select[name='supplier_id']"),obj.data.supplier_id);
                        select2SetValue($("form#edit_product_form select[name='sup_id']"),obj.data.supplier_id);

                        var checkreturn = $("form#add_supplier_form input[name='checkReturn']").val();
                        $.magnificPopup.close();
                        if(checkreturn != "")
                        {
                          //alertText(obj.msg, "success");
                          call_notify("Nhà cung cấp",obj.msg,"success","");
                        }else
                        {
                          // e.stopPropagation();
                          // alertText(obj.msg, "success", checkreturn);
                          $("p.error_msg").show();
                          $("p.error_msg").css("color","green");
                          $("p.error_msg").css("font-size","14px");
                          setTimeout(function(){ $("p.error_msg").fadeOut();$("p.error_msg").css("color","red");},2000);
                          $("p.error_msg").html(obj.msg);

                          if($(".transactionForm #supplier_id").length) //Dùng cho form transaction
                          {
                              $(".transactionForm #supplier_id").trigger("change");
                          }

                        }
                    }else
                    {
                        /*$("p.error_msg").show();
                        $("p.error_msg").css("color","red");
                        $("p.error_msg").css("font-size","14px");
                        $("p.error_msg").html(obj.msg);*/

                        pNotifyACP(obj.msg);

                        if($(".transactionForm #supplier_id").length) //Dùng cho form transaction
                        {
                            $(".transactionForm #supplier_id").trigger("change");
                        }

                        setTimeout(function(){ $("p.error_msg").fadeOut();},2000);
                        return false;
                    }
                }
            })
        } else {
            // do stuff if form is not valid
            validator.focusInvalid();
            checkDoubleClick(".btn_edit_do",0); 
            return false;
        }
    });


     $("#add_manufacture_form").on("click",".btn_edit_do_manufacture", function(){
 
        if($("form#add_manufacture_form").validnew()){
            // do stuff if form is valid
          
            $("form#add_manufacture_form #ufile_output_b64").val('');
            var data = $("form#add_manufacture_form").serialize();
            if ( $( "form#add_manufacture_form #input[name='m_avartar']" ).length ) {
 
               var file_data = $("input[name='m_avartar']").prop("files")[0];
            }
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.manu_error_msg").hide();
            $("p.manu_error_msg").html('');
           
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=manufacture&subact=ajax_edit_manufacture&"+data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(html)
                {
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                        $("form#form-signin_v1 select[name='p_manufacture'] option[value='"+obj.data_option.manufacture_id+"']").html(obj.data_option.manufacture_name);
                        $(".select_manufacture").html(obj.data_option);
                        $(".select_manufacture").trigger("change");
                        var checkreturn = $("form#add_manufacture_form input[name='checkReturn']").val();
                        $.magnificPopup.close();
                        if(checkreturn)
                        {
                          // alertText(obj.msg, "success", checkreturn);
                          // $("p.error_msg").show();
                          // $("p.error_msg").css("color","green");
                          // $("p.error_msg").css("font-size","14px");
                          // setTimeout(function(){ $("p.error_msg").fadeOut();$("p.error_msg").css("color","red");},2000);
                          // $("p.error_msg").html(obj.msg);
                          call_notify("Brand",obj.msg,"info","");

                        }else
                        {
                         // alertText(obj.msg, "success");
                          call_notify("Brand",obj.msg,"success","");
                        }
                    }else
                    {
                        $("p.manu_error_msg").show();
                        $("p.manu_error_msg").css("color","red");
                        $("p.manu_error_msg").css("font-size","14px");
                        $("p.manu_error_msg").html(obj.msg);
                        setTimeout(function(){ $("p.manu_error_msg").fadeOut();},2000);
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



    var validator = $("form#add_supplier_form").validatenew({
          focusInvalid: true,
          rules: {
            // simple rule, converted to {required:true}
            supplier_name: "required",
            supplier_phone: {
              phoneFormat: true
            },
            supplier_email: {
              email: true
            }
          },
          messages:{
            supplier_name: lang_field_required,
          }
        });

    $(".select_supplier").change(function(){
        change_supplier_action($(this));
    });

     $(".select_manufacture").change(function(){
        var manufacture_id = $(this).val();
        if(manufacture_id)
        {
          if(pms['manufacture_edit'] == 1)
          {
            $(".box_edit_manufacture").html("<a id='"+manufacture_id+"' class='btn_gen btn_edit_manufacture pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
          }
        }else
        {
            $(".box_edit_manufacture").html("");
        }
    });

       $(".select_product_group").change(function(){

        var  product_group_id = $(this).val();

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
    });


      // Edit NCC
      $("form#form-signin_v1, #edit_product_form").on("click", ".btn_edit_manufacture", function(e){
        // $("form#add_manufacture_form")[0].reset();
        clearForm($("form#add_manufacture_form"));
        var checkreturn = $(this).parents(".box_action").attr("checkreturn");
        $("form#add_manufacture_form input[name='checkReturn']").val(checkreturn);
        var man_id = $(this).attr("id");
        $(".error").html('');
       
        $(".title_add_manufacture").html(lang_manufacture_edit);
        $(".change_action_manufacture").html("<input class='btn act_popup_btn_validate' aclass='btn_edit_do_manufacture' type='button' value='"+lang_manufacture_edit+"'/>");
      
        // init formvalidation new style alrt Plong
        validate_form_custom("#add_manufacture_form",".act_popup_btn_validate","box_custom"); 
        
        e.stopPropagation();
        // Ajax get data
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=manufacture&subact=ajax_get_data_manufacture",
            data: {man_id: man_id},
            success: function(html)
            {
           
                var obj = JSON.parse(html);
           
                $("#add_manufacture_form input[name='m_name']").val(obj.manufacture_name);
                $("#add_manufacture_form input[name='m_code']").val(obj.manufacture_code);
                $("#add_manufacture_form input[name='m_status']").val(obj.manufacture_status);
                $("#add_manufacture_form [name='m_description']").val(obj.manufacture_description);
                $("#add_manufacture_form input[name='m_manufacture_id']").val(obj.manufacture_id);
                if(obj.manufacture_avartar)
                {
                    var url_img = site_parent_domain+"/uploads/manufacture/"+obj.manufacture_avartar;
                    $("#add_manufacture_form #upload_img_show").attr("src",url_img);
                }
                // setTimeout(function(){ $("#add_manufacture_form select[name='m_manufacture_parent'] option[value='"+obj.manufacture_parent+"']").prop("selected",true); }, 1000);
                select2SetValue($("#add_manufacture_form select[name='m_manufacture_parent']"), obj.manufacture_parent);
                
                
                $.magnificPopup.open({
                  type: 'inline',
                  items: {
                    src: '#box_add_manufacture'
                  },
                });
                
            }
        });
        
    });



      $("form#add_manufacture_form").on("click", ".btn_add_manufacture", function(){
        if($("form#add_manufacture_form").validnew()){   
            // do stuff if form is valid
            $("form#add_manufacture_form #ufile_output_b64").val('');
            var data = $("form#add_manufacture_form").serialize();
            if ( $( "form#add_manufacture_form #input[name='m_avartar']" ).length ) {
 
                  var file_data = $("input[name='m_avartar']").prop("files")[0];
             
            }

          
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.manu_error_msg").hide();
            $("p.manu_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=product&subact=ajax_add_manufacture&"+data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(html)
                {
                   
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {
                        $(".select_manufacture").html(obj.data_option);
                        $(".select_manufacture").trigger("change");

                        var checkreturn = $("form#add_manufacture_form input[name='checkReturn']").val();
                        $.magnificPopup.close();
                        if(checkreturn)
                        {
                          // alertText(obj.msg, "success", checkreturn);
                          // $("p.error_msg").show();
                          // $("p.error_msg").css("color","green");
                          // $("p.error_msg").css("font-size","14px");
                          // setTimeout(function(){ $("p.error_msg").fadeOut();$("p.error_msg").css("color","red");},2000);
                          // $("p.error_msg").html(obj.msg);
                          call_notify("Brand",obj.msg,"info","");

                        }else
                        {
                          alertText(obj.msg, "success");
                        }
                    }else
                    {
                        $("p.manu_error_msg").show();
                        $("p.manu_error_msg").css("color","red");
                        $("p.manu_error_msg").css("font-size","14px");
                        $("p.manu_error_msg").html(obj.msg);
                        setTimeout(function(){ $("p.manu_error_msg").fadeOut();},2000);
                    }
                }
            })
        } else {
            // do stuff if form is not valid
            validator.focusInvalid();
            return false;
        }
    });


    // Validate NSX
    var validator_nsx = $("form#add_manufacture_form").validatenew({
          focusInvalid: true,
          rules: {
            // simple rule, converted to {required:true}
            m_name: "required" 
          },
          messages:{
            m_name: lang_field_required,
          }
        });



    // var valid_request = $("form.form_request").validatenew({
    //       focusInvalid: true,
    //       rules: {
    //         // simple rule, converted to {required:true}
    //         supplier_id: "required",
    //         store_id: "required",
    //         shi_name: "required",
    //         cus_name: "required",
    //         store_id_to: "required",
    //         request_reason: "required",
    //         request_subtype: "required",
    //       },
    //       messages:{
    //         supplier_id: lang_field_required,
    //         store_id: lang_field_required,
    //         shi_name: lang_field_required,
    //         cus_name: lang_field_required,
    //         store_id_to: lang_field_required,
    //         request_reason: lang_field_required,
    //         request_subtype: lang_field_required,
    //       }
    //     });

    

        // Add NSX
    $(".add_new_manufacture").click(function(e){
        e.stopPropagation();
        var val_return = $(this).parent().attr("checkreturn");
        $(".title_add_manufacture").html(lang_manufacture_add);
        $(".change_action_manufacture").html("<input class='btn act_popup_btn_validate' aclass='btn_add_manufacture' type='button' value='"+lang_manufacture_add+"'/>");
          

        // init validation new style alert
        validate_form_custom("#add_manufacture_form",".act_popup_btn_validate","box_custom");

        if(typeof(checkreturn) == undefined || val_return == "")
        {
            $("input[name='checkReturn']").val("");
        }else
        {
            $("input[name='checkReturn']").val(val_return);
        }
        $(".error").html('');
        // $("form#add_manufacture_form")[0].reset();
        clearForm($("form#add_manufacture_form"));
        $("#upload_img_show").attr("src","");
        $("form#add_manufacture_form textarea").val("");
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: '#box_add_manufacture'
          }
        });
    });




          // Add Nhóm sp
    $(".add_new_product_group").click(function(e){
        e.stopPropagation();

        let prePopup = $(this).parents(".mfp-content:first").children("*").first();
        if(prePopup.length && (checkReturn = prePopup.attr("id"))) {
            $("#add_group_product_form input[name=checkReturn]").val("#"+checkReturn);
        }
        else{
            $("#add_group_product_form input[name=checkReturn]").val("");
        }

        $(".title_group_product").html(cms_lang.add_group_product);
        $(".change_action_product_group").html("<input class='btn btn_add_product_group' type='button' value='"+cms_lang.add_group_product+"'/>");
        
     //   $(".error").html('');
     //    $("form#add_group_product_form")[0].reset();
        clearForm($("form#add_group_product_form"))
        var p_type = $("form#form-signin_v1 [name='p_type']").val();
   
        $("form#add_group_product_form input[name='pg_type'][value='"+p_type+"']").prop("checked","checked");
        $("form#add_group_product_form #upload_img_show").attr("src","");
        $("form#add_group_product_form input[name='base64_image']").val("");
        get_group_product_2(p_type);
   
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: '#box_add_group_product'
          }
        });
    });




var checkabc = 0;

    // Check item in store request
    $(".btn_add_request").click(function(){

      var check_action = $(this).attr("page_type");
      if(typeof(check_action) != "undefined" || check_action != null)
      {
          $("input[name='page_type']").val(check_action);
      }

      if(checkabc == 0)
      {
        validate_form_custom("#form_add_request",".btn_add_request", "store_request");
        checkabc ++;
        $(this).trigger('click');
        
      }
      // 
//       // Check in form
//       if($("form.form_request").validnew())
//       {
//           var count = 0;
//           var check1 = -1;
//           var check2 = -1;
//           if($(".grid-td .product_id, .grid-td-asset .ass_id").length > 0)
//           {
//             check1 = 0;
//             $(".grid-td .product_id, .grid-td-asset .ass_id").each(function(){
//                 var id = $(this).val();
//                 if(id)
//                 {
//                     check1 ++;
//                     count ++;
//                 }
//             });
//           }
//           if($(".grid-td-asset .ass_id").length > 0)
//           {
//             check2 = 0;
//             $(".grid-td-asset .ass_id").each(function(){
//                 var id = $(this).val();
//                 if(id)
//                 {
//                     check2 ++;
//                     count ++;
//                 }
//             });
            
//           }

// // console.log(check2)
//           if(count == 0)
//           {
//               if(check1 == 0)
//               {
//                 alertText(cms_lang['gnotice_select_product'],"warning"); 
//               }

//               if(check2 == 0)
//               {
//                 alertText(cms_lang['gnotice_select_asset'],"warning"); 
//               }
//           }else{
//               $("form.form_request").submit();
//           }
//       }else
//       {
//           valid_request.focusInvalid();
//           return false;
//       }
    });

    // $(".search_shipment").keyup(function(e){
    //     var keytype = $(this).val();
    //     var id = $("form.form_request input[name='shi_id']").val();
    //     // $(".error_shipment").css("display","none");
    //     // $(".error_shipment").html("");
    //     // console.log(e);
    //     if(id && e.keyCode != 13)
    //     {
    //       $("input[name='shi_id']").val('');
    //     }

    //     if(keytype == "")
    //     {
    //         $(".box_edit_shipment").html("");
    //     }

    //     // $(".add_new_shipment").html("+ Thêm mới lô hàng <strong>\""+keytype+"\"</strong>");
    //     var urls = site_root_domain+'/?site=store_request&act=search&subact=search_shipment';
    //     $(this).autocomplete({
    //       source: urls,
    //       minLength: 2,
    //       select: function( event, ui ) {
    //         if(ui.item.id == "add_new")
    //         {
    //             $(".add_new_shipment").trigger("click");
    //             return false;
    //         }else
    //         {
    //             $('.data_input').val(ui.item.id);
    //             if(pms['shipment_edit'] == 1)
    //             {
    //               $(".box_edit_shipment").html("<a id='"+ui.item.id+"' class='btn_gen btn_edit_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
    //             }
    //         }
    //         // $(".add_new_shipment").html("+ Thêm mới lô hàng");
    //       }
    //     });


    // });

    $(".add_new_shipment").click(function(){
          // $("form#shipment_form")[0].reset();
          clearForm($("form#shipment_form"));
          $(".change_title_shipment").html(lang_shipment_add);
          $(".change_action_shipment").html("<input class='btn btn_add_shipment' type='button' value='"+lang_shipment_add+"'/>");
          checkDoubleClick(".btn_reset_attr",0);
          var shi_name = $("form.form_request input[name='shi_name']").val();
          $("#box_shipment input[name='shi_name']").val(shi_name);
          $.magnificPopup.open({
            type: 'inline',
            items: {
              src: '#box_shipment'
            },
          });
    });

    $("form.form_request").on("click",".btn_edit_shipment", function(){
          checkDoubleClick(".btn_reset_attr",0);
          $(".change_title_shipment").html(lang_shipment_edit);
          $(".change_action_shipment").html("<input class='btn edit_shipment_do' type='button' value='"+lang_shipment_edit+"'/>");
          $(".error_shipment").hide();
          $(".error_shipment").html('');
          var id = $(this).attr("id");
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=shipment&subact=ajax_data_shipment",
              data: {shi_id: id},
              success: function(html)
              {
                 
                  var obj = JSON.parse(html);
                  $("form#shipment_form input[name='shi_name']").val(obj.shi_name);
                  $("form#shipment_form input[name='shi_id']").val(obj.shi_id);
                  $("form#shipment_form textarea[name='shi_description']").val(obj.shi_description);
                  $.magnificPopup.open({
                    type: 'inline',
                    items: {
                      src: '#box_shipment'
                    },
                  });
              }
          });

    });

    $("form#shipment_form").on("click",".edit_shipment_do", function(){
        checkDoubleClick($(this),1);
        var data = $("form#shipment_form").serialize();
        $(".error_shipment").hide();
        $(".error_shipment").html('');
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=shipment&subact=ajax_edit_shipment_do",
            data: data,
            success: function(html){
               
                var obj = JSON.parse(html);
                if(obj.msg == "error")
                {
                    $(".error_shipment").html(obj.msg);
                    $(".error_shipment").show();
                    checkDoubleClick(".edit_shipment_do",0);
                }else
                {
                  $("form.form_request input[name='shi_name']").val(obj.data.shi_name);
                  // $("form.form_request input[name='shi_id']").val(obj.data.shi_id);
                  $.magnificPopup.close();
                  alertText(obj.msg, "success");
                }
            }
        });

    });
    

    var valid_shipment = $("form#shipment_form").validatenew({
          focusInvalid: true,
          rules: {
            shi_name: "required"
          },
          messages:{
            shi_name: lang_field_required,
          }
        });



    $("#box_shipment").on("click",".btn_add_shipment", function()
    {
        if($("form#shipment_form").validnew())
        {
            checkDoubleClick($(this),1);
            var shi_name = $("form#shipment_form input[name='shi_name']").val();
            var shi_description = $("form#shipment_form textarea[name='shi_description']").val();
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=shipment&subact=ajax_add_shipment",
                data: {shi_name: shi_name, shi_description: shi_description},
                success: function(html)
                {
                   
                    var obj = JSON.parse(html);
                    if(obj.status == "error")
                    {
                        $(".error_shipment").css("display","block");
                        $(".error_shipment").html(obj.msg);
                        if(obj.data)
                        {
                            $("input[name='shi_id']").val(obj.data.shi_id);
                        }
                        checkDoubleClick(".btn_add_shipment",0);
                        // $(".add_new_shipment").html("+ Thêm mới lô hàng");
                    }else
                    {
                        $(".error_shipment").hide();
                        // $(".error_shipment").html(obj.msg);
                        // $("form#shipment_form")[0].reset();
                        clearForm($("form#shipment_form"));
                        $("form.form_request input[name='shi_name']").val(obj.data.shi_name);
                        $("form.form_request input[name='shi_id']").val(obj.data.shi_id);
                        if(obj.data.shi_id)
                        {
                          if(pms['shipment_edit'] == 1)
                          {
                            $(".box_edit_shipment").html("<a id='"+obj.data.shi_id+"' class='btn_gen btn_edit_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                          }
                        }else
                        {
                            $(".box_edit_shipment").html("");
                        }

                        $.magnificPopup.close();
                        alertText(obj.msg, "success");
                    }
                }
            });
        }else
        {
            valid_shipment.focusInvalid();
            return false;
        }
    });

    // Duyet yeu cau
    $(".show_more_info").click(function(){
        var check = $(this).attr("check");
        $(".box_show").toggle();
        if(check == 0)
        {
          $(this).attr("check", 1);
          $(".btn_check").html('<i class="fa fa-minus-square-o" aria-hidden="true"></i>');
        }else
        {
          $(this).attr("check", 0);
          $(".btn_check").html('<i class="fa fa-plus-square-o" aria-hidden="true"></i>');
        }
    });

    // Auto select
    $("select.auto_select").each(function(){
      var val_default = $(this).attr("defaultvalue");
      if(val_default)
      {
        $(this).find("option[value='"+val_default+"']").prop("selected",true);
        $(this).trigger("change");
      }
    });

    // Add line
    $(".btn_add_line").click(function(){
        var html_line = $("#data_table tr[rowtr='last-row']").html();
        // Clear last-row
        $("#data_table tr").attr("rowtr","");
        html_line = "<tr class='row-grid' rowtr=''>"+html_line+"</tr>";
      
        // html_line = html_line.find("input,select,textarea").val('');
        // var check = 4;
        for (var i = 1; i <= 4; i++) 
        {
          $("#data_table").append(html_line);
          $("#data_table tr:last-child").find("input,textarea").val('');
          $("#data_table tr:last-child").find("select").val(10);
          $("#data_table tr:last-child .trash").find(".edit_row_product").remove();
          
        }

        // Add last-row
        $("#data_table tr:last-child").attr("rowtr",'last-row');
        // insert number
        var count_check = 1;
        var inc_item = 0;
        $("#data_table tr").each(function()
        {  
            $(this).find(".number").html("#"+count_check);
            $(this).find(".item_id").val(inc_item);
            $(this).find(".del_row_invoid").attr("item_id",inc_item);
            count_check ++;
            inc_item ++;
        });
    });

    // Clear all line
    $(".btn_del_line").click(function(){
        var html_line = $("#data_table tr[rowtr='last-row']").html();
        // Clear last-row
        // $("#data_table tr").attr("rowtr","");
        var html_row = "";
        for (var i = 1; i <= 2; i++) 
        {
          html_row += "<tr class='row-grid' rowtr=''>"+html_line+"</tr>";
        }

        // Add line moi
         $("#data_table").html(html_row);
        // Add last-row
        $("#data_table tr:last-child").attr("rowtr",'last-row');
        // insert number
        var count_check = 1;
        var inc_item = 0;
        $("#data_table tr").each(function()
        {
            $(this).find(".number").html("#"+count_check);
            $(this).find(".item_id").val(inc_item);
            $(this).find(".del_row_invoid").attr("item_id",inc_item);
            count_check ++;
            inc_item ++;
        });
        // remove product
        $.ajax({
              type: "post",
              url: site_root_domain + "/?site=store_request&subact=remove_product",
              data: {type: "all"},
              success: function(html) {
                
              }
        });
        calculate_money();
    });

    // load trang thi tính toán tổng tiền
    calculate_money();

    $("#box_product").on("click", ".btn_add_to_request", function(){
        var pro_name = $("#edit_product_form input[name='product_name']").val();
        // Check product group
        var pg_id = $("#edit_product_form select[name='product_group']").val();
        
        $("#edit_product_form .error_msg").css("color", "red");
        $("#edit_product_form .error_msg").css("font-style", "italic");
        $("#edit_product_form .error_msg").css("font-size", "14px");
        $("#edit_product_form .error_msg").css("margin", "0px");
        if(pro_name == "")
        {
          $("#edit_product_form .error_msg").show();
          $("#edit_product_form .error_msg").html(cms_lang['gnotice_fill_product_name']);
          $("#edit_product_form input[name=product_name]").focus();
          return false;
        }

        if(pg_id == "")
        {
          $("#edit_product_form .error_msg").show();
          $("#edit_product_form .error_msg").html(cms_lang['gnotice_select_product_group']);
          $("#edit_product_form select[name=product_group]").focus();
          return false;
        }
        var data = $(this).parents(".form_data").serialize();
        var file_data = $("input[name='product_image']").prop("files")[0];
        var item_id = $("input[name='item_id']").val();
        var form_data = new FormData();
        form_data.append("upload_img", file_data);
        $(".error_msg").hide();
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=store_request&subact=ajax_add_for_bill&"+data,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){},
            success: function(html)
            {
             
               var obj = JSON.parse(html);
             
               $("#edit_product_form .error_msg").html('');
               if(obj.status == "error")
               {
                  $("#edit_product_form .error_msg").css("color", "red");
                  $("#edit_product_form .error_msg").css("font-style", "italic");
                  $("#edit_product_form .error_msg").css("font-size", "14px");
                  $("#edit_product_form .error_msg").css("margin", "0px");
                  $("#edit_product_form .error_msg").show();
                  $("#edit_product_form .error_msg").html(obj.msg);
                  $("#edit_product_form input[name=product_name]").focus();
                   
               }else
               {
                  // $("#edit_product_form")[0].reset();
                  clearForm($("form#edit_product_form"));
                  // Insert data just add new
                  var cur_row_active = $("#keyrow_active").val();
       
                  /*$("#data_table tr").each(function(i,row){
                   var cur_key = i + 1;
                              
                   if(cur_key == cur_row_active)
                   {
                        $(row).find('input[name="product_name[]"]').val(obj.data.product_name);
                        $(row).find('input[name="product_id[]"]').val(obj.data.product_id);
                        $(row).find('textarea[name="product_description[]"]').val(obj.data.product_description);
                        $(row).find('input[name="product_quantity[]"]').val(1);
                        $(row).find('input[name="product_price[]"]').val(obj.data.product_price);
                        $(row).find('select[name="product_tax[]"]').val(obj.data.product_tax);
                        $(row).find('td.trash').html("<span class='edit_row_product' id="+obj.data.product_id+" item_id='"+item_id+"'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span><span class='del_row_invoid' item_id='"+item_id+"'><i class='fa fa-trash' aria-hidden='true'></i></span>");
                        calculate_money();
                   }
                    
                         
                  });*/

                   var parentSelector = $("#data_table tr.active");

                   parentSelector.find('input[name="product_name[]"]').val(obj.data.product_name);
                   parentSelector.find('input[name="product_id[]"]').val(obj.data.product_id);
                   parentSelector.find('textarea[name="product_description[]"]').val(obj.data.product_description);
                   parentSelector.find('input[name="product_quantity[]"]').val(1);
                   parentSelector.find('input[name="product_price[]"]').val(obj.data.product_price);
                   parentSelector.find('select[name="product_tax[]"] option[value="'+obj.data.product_tax+'"]').prop("selected", true);

                   loadProductCycleType(obj.data.product_type, parentSelector.find("[name='product_cycle[]']"));

                   calculate_money();

                  $.magnificPopup.close();
                 // / alertText(obj.msg, 'success');
                  call_notify("Sản phẩm",obj.msg, 'success','')
                  
               }
            }
        });
    });

  $.magnificPopup.instance.close = function() {
 
    if(typeof(this.items) !== "undefined")
    {
        var obj = this._lastFocusedEl;
        if(typeof(obj) !== "undefined" && $(obj).attr("class") == "btn view_detail")
        {
          $.magnificPopup.proto.close.call(this);
        }else
        {
            if(this.items["0"]["type"] == "image"){//Fix loi popup image
                $.magnificPopup.proto.close.call(this);
                return false;
            }

            var el = this.items["0"].src;
            var creturn = $(el+" input[name='checkReturn']").val();
            $.magnificPopup.proto.close.call(this);
            
            if(creturn && typeof(creturn) !== "undefined")
            {
                $.magnificPopup.open({
                      type: 'inline',
                      items: {
                        src: creturn
                      }
                    });
            }
        }


        
    }
       
    }
    /********************************************************************************** 
    *   ADD PRODUCT GROUP
    ***********************************************************************************/
    $(".add_product_group").click(function(e){
        e.stopPropagation();
        var val_return = $(this).parent().attr("checkreturn");
        $(".title_change_pg").html(lang_pg_add);
       $(".change_action_pg").children().attr("aclass","btn_add_pg");
       $(".change_action_pg").children().addClass("act_popup_btn_validate");
       $(".change_action_pg").find('.ladda-label').html(lang_pg_add);
       //.html("<button class='btn btn-inline btn-primary ladda-button act_popup_btn_validate' aclass='btn_add_pg' data-style='expand-left'><span class='ladda-label'>"+lang_pg_add+"</span><span class='ladda-spinner'></span></button>");
        //Plong: init formvalidation new style alert
         validate_form_custom("#form_product_group",".act_popup_btn_validate","box_custom");

        if(typeof(checkreturn) == undefined || val_return == "")
        {
            $("form#form_product_group input[name='checkReturn']").val("");
        }else
        {
            $("form#form_product_group input[name='checkReturn']").val(val_return);
        }
        $(".error").html('');
        // $("form#form_product_group")[0].reset();
        clearForm($("form#form_product_group"));
        $("form#form_product_group #upload_img_show").attr("src","");
        // $("form#add_manufacture_form textarea").val("");
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: '#box_product_group'
          }
        });
    });

    $("form#form_product_group").on("click",".btn_add_pg", function(){
        if($("form#form_product_group").validnew())
        {
 
          $("form#form_product_group #ufile_output_b64").val('');
          var data = $("form#form_product_group").serialize();
          var file_data = $("input[name='pg_avartar']").prop("files")[0];
          var form_data = new FormData();
          form_data.append("upload_img", file_data);
          $("p.pg_error_msg").hide();
          $("p.pg_error_msg").html('');
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=product_group&subact=ajax_add_pg&"+data,
              data: form_data,
              cache: false,
              contentType: false,
              processData: false,
              success: function(html)
              {
                  // console.log(html);
                  var obj = JSON.parse(html);
                  if(obj.status == "success")
                  {
          
                      $(".select_pg").html(obj.data.data_option);
                      $(".select_pg option[value='"+obj.data.product_group_id+"']").prop("selected", true);
                      $(".select_pg").trigger("change");
 
                      var checkreturn = $("form#form_product_group input[name='checkReturn']").val();
                      var url_redirect = $("form#form_product_group input[name='url_redirect']").val();
                      $.magnificPopup.close();
                      if(checkreturn)
                      {
                        // alertText(obj.msg, "success", checkreturn);
                        // $("p.error_msg").show();
                        // $("p.error_msg").css("color","green");
                        // $("p.error_msg").css("font-size","14px");
                        // setTimeout(function(){ $("p.error_msg").fadeOut();$("p.error_msg").css("color","red");},2000);
                        // $("p.error_msg").html(obj.msg);
                        load_productgroup_byType(obj.data.product_group_type,"#box_product select[name='product_group']");
                        call_notify(cms_lang.group_product_category,obj.msg,"info","");
                       // $("#box_product select[name='product_service_type'] option[value='"+obj.data.product_group_type+"']").prop("selected", true);
                          $("#box_product select[name='product_service_type']").val(obj.data.product_group_type).trigger("change");
                      }else
                      {
                         
                        //Plong: check nếu có link refresh thi redirect ve page trong bien url_redirect. Khong thi close popup
                          if(url_redirect)
                          {
                             var pg_type = obj.data.product_group_type;
                           // alertText(obj.msg, "success");
                            setTimeout(
                                        swal({
                                          title: '',
                                          text: obj.msg,
                                          type: 'success',
                                          confirmButtonClass: "btn-success",
                                          confirmButtonText: cms_lang['gnotice_ok'],
                                       }).then(function () {
                                          if (  url_redirect) {
                                              window.location = url_redirect+"&pg_type="+pg_type;
                                           }else
                                           {
                                              $.magnificPopup.close();
                                           }
                                                                                 
                                       }) 

                                    , 1000);



                        }
                        else
                        {
                          alertText(obj.msg, "success");  
                        }
                      }
                  }else
                  {
                      $("p.pg_error_msg").show();
                      $("p.pg_error_msg").css("color","red");
                      $("p.pg_error_msg").css("font-size","14px");
                      $("p.pg_error_msg").html(obj.msg);
                      setTimeout(function(){ $("p.pg_error_msg").fadeOut();},2000);
                  }
              }
          });
        }else
        {
            valid_shipment.focusInvalid();
            return false;
        }
    });

    

    var valid_pg = $("form#form_product_group").validatenew({
          focusInvalid: true,
          rules: {
            pg_name: "required"
          },
          messages:{
            pg_name: lang_field_required,
          }
        });

    $(".select_pg").change(function(){
          var pg_id = $(this).val();
          
          
          
          $(".select_pg option[value='"+pg_id+"']").prop("selected", true);
          if(pg_id)
          {
            if(pms['product_group_edit'] == 1)
            {
              $(".action_pg .box_edit").html("<a id='"+pg_id+"' class='edit_product_group btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
            }
          }else
          {
              $(".action_pg .box_edit").html("");
          }
    });

    $("#box_product").on("click", ".edit_product_group", function(e){
        e.stopPropagation();
        var pg_id = $(this).attr("id");
        var val_return = $(this).parents(".box_action").attr("checkreturn");
        $(".title_change_pg").html(lang_pg_edit);
       // $(".change_action_pg").html("<input class='btn act_popup_btn_validate' aclass='btn_edit_pg' type='button' value='"+lang_pg_edit+"'/>");
        
       $(".change_action_pg").children().attr("aclass","btn_edit_pg");
       $(".change_action_pg").children().addClass("act_popup_btn_validate");
       $(".change_action_pg").find('.ladda-label').html(lang_pg_edit);


        //Plong: init formvalidation new style alert
        validate_form_custom("#form_product_group",".act_popup_btn_validate","box_custom");

        //
        if(typeof(checkreturn) == undefined || val_return == "")
        {
            $("form#form_product_group input[name='checkReturn']").val("");
        }else
        {
            $("form#form_product_group input[name='checkReturn']").val(val_return);
        }
        $(".error").html('');
        // $("form#form_product_group")[0].reset();
        clearForm($("form#form_product_group"));
        $("form#form_product_group #upload_img_show").attr("src","");
        
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=product_group&subact=ajax_getinfo_pg",
            data: {pg_id: pg_id},
            success: function(html)
            {
              var obj = JSON.parse(html);
              
              $("form#form_product_group select[name='pg_parent']").html(obj.data.data_option);
              $("form#form_product_group input[name='pg_name']").val(obj.data.product_group_name);
              $("form#form_product_group input[name='pg_id']").val(obj.data.product_group_id);
              $("form#form_product_group input[name='pg_code']").val(obj.data.product_group_code);
              $("form#form_product_group textarea[name='pg_description']").val(obj.data.product_group_description);
              $("form#form_product_group input[name='pg_type'][value='"+obj.data.product_group_type+"'] ").prop("checked",true);

              // Reset checked pg_status
              $("form#form_product_group input[name='pg_status']").prop("checked",false);
              $("form#form_product_group input[name='pg_status'][value='"+obj.data.product_group_status+"'] ").prop("checked",true);
              var link_img = "";
              if(obj.data.product_group_avatar)
              {
                link_img =    "/uploads/product/"+ obj.data.product_group_avatar;
              }
 
              $("form#form_product_group #upload_img_show").attr("src",link_img);
            

              $.magnificPopup.open({
                type: 'inline',
                items: {
                  src: '#box_product_group'
                }
              });
            }
        });
        

    });


    $("#form_product_group, #add_group_product_form").on("click", ".btn_edit_pg", function(e){

      var from_id = $(this).closest('form').attr('id');
          from_id = (typeof from_id == 'undefined' || ! from_id) ? 'form_product_group' : from_id;
        
        if($("form#"+from_id).validnew())
        {
          var pg_id = $("form#"+from_id+" input[name='pg_id']").val();
          $("form#"+from_id+" #ufile_output_b64").val('');
          var data = $("form#"+from_id).serialize();
          var file_data = $("input[name='pg_avartar']").prop("files")[0];
          var form_data = new FormData();
          form_data.append("upload_img", file_data);
          $("p.pg_error_msg").hide();
          $("p.pg_error_msg").html('');
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=product_group&subact=ajax_edit_pg&"+data,
              data: form_data,
              cache: false,
              contentType: false,
              processData: false,
              success: function(html)
              {
                  // console.log(html);
                  var obj = JSON.parse(html);
                  if(obj.status == "success")
                  {
                   
                      $(".select_pg").html(obj.data.data_option);
                      // $(".select_pg option[value='"+pg_id+"']").prop("selected", true);
                      select2SetValue($(".select_pg"),pg_id);
                      $(".select_pg").trigger("change");

                      var checkreturn = $("form#"+from_id+" input[name='checkReturn']").val();
                      var url_redirect = $("form#"+from_id+" input[name='url_redirect']").val();
                      $.magnificPopup.close();
                      if(checkreturn)
                      {
                        // alertText(obj.msg, "success", checkreturn);
                        // $("p.error_msg").show();
                        // $("p.error_msg").css("color","green");
                        // $("p.error_msg").css("font-size","14px");
                        // setTimeout(function(){ $("p.error_msg").fadeOut();$("p.error_msg").css("color","red");},2000);
                     
                        call_notify(cms_lang.group_product_category,obj.msg,"info","");
                      
                      }else
                      {
                        if(url_redirect)
                        {
                          var pg_type = obj.data.product_group_type;
                         // alertText(obj.msg, "success");
                          setTimeout(
                                      swal({
                                        title: '',
                                        text: obj.msg,
                                        type: 'success',
                                        confirmButtonClass: "btn-success",
                                        confirmButtonText: cms_lang['gnotice_ok'],
                                     }).then(function () {
                                        if (  url_redirect) {
                                            window.location = url_redirect+"&pg_type="+pg_type;
                                         }else
                                         {
                                            $.magnificPopup.close();
                                         }
                                                                               
                                     }) 

                                    , 1000);



                        }
                        else
                        {
                          alertText(obj.msg, "success");  
                        }
                        
                      }
                  }else
                  {
                      $("p.pg_error_msg").css("color","red");
                      $("p.pg_error_msg").css("font-size","14px");
                      $("p.pg_error_msg").show();
                      $("p.pg_error_msg").html(obj.msg);
                      setTimeout(function(){ $("p.pg_error_msg").fadeOut();},2000);
                  }
              }
          });
        }else
        {
            valid_shipment.focusInvalid();
            return false;
        }
    });
    

    /********************************************************************************** 
    *   END ADD PRODUCT GROUP
    ***********************************************************************************/

    // var count_click = 1;
    // $(".btn_submit_form").click(function()
    // {
    //     $('#form_edit_request').validate('destroy');
    //     var obj = $(this);
    //     var title_alert = $(this).attr("title_alert");
    //     var is_enough = $(this).attr("val");
    //     var checkclick = $(this).attr('checkclick');
    //     $("input[name='enough_goods']").val(is_enough);
    //     if(count_click == 1 && checkclick == 1)
    //     {
    //           swal({
    //            title: '',
    //              text: title_alert,
    //              type: "warning",
    //              showCancelButton: true,
    //              confirmButtonClass: "btn-danger",
    //              confirmButtonText: cms_lang['gnotice_ok'],
    //              cancelButtonText: cms_lang['gnotice_cancel'],
    //              closeOnConfirm: true,
    //              closeOnCancel: true
    //          },
    //          function(isConfirm) {
    //              if (isConfirm) {
    //                validate_form_custom('#form_edit_request','.btn_submit_form', 'approve_request');
    //                if(count_click == 1)
    //                {
    //                   $(obj).trigger("click");
    //                   count_click ++; 
    //                }
                 
    //              }
    //          });
    //     }
        
        
    // });
  

    // Change subtype
    $("#request_subtype").change(function(){
        var request_subtype = $(this).val();
        var sub_id = parseInt($(this).attr('sub_id'));
        var invalid_store_id = $(this).attr('invalid_store_id');
 
        if(request_subtype == 1 || request_subtype == 2)
        {
            $(".btn_del_line_asset").trigger("click");
        }
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=store_request&subact=change_request_subtype",
            data: {request_subtype: request_subtype, sub_id: sub_id, invalid_store_id: invalid_store_id},
            success: function(html)
            {
                 
                $("#content_change").html(html);

            }
        });
    });


    $(".choose_store input[name='store_id']").click(function(){
 
        change_type_store($(this));

    });

    $(".choose_store select[name='store_id']").change(function(){
        change_type_store($(this));

    });

    $("#content_change").on("change","select[name='supplier_id']",function(){
        var sup_id = parseInt($(this).val());
        $(".choose_store input[name='check_sup_id']").val(sup_id);
        $(".btn_del_line_asset").trigger("click");
    });


    // TOOLTIP
    $('[data-toggle="tooltip"]').tooltip({
      html: true, 
      position: { my: "left top", at: "left bottom", collision: "flipfit" }
    });

    // VALID CUSTOMER
    var valid_customer = $("form#form_customer").validatenew({
          focusInvalid: true,
          rules: {
            cus_full_name: "required",
            cus_email: "required",
          },
          messages:{
            cus_full_name: lang_field_required,
            cus_email: lang_field_required,
          }
        });
    
    // taxable
    $("select[name='is_taxable']").change(function(){
        var tax_val = $(this).val();
        if(tax_val == 1)
        {
            $("select[for='dgrid-7'] option[value='10']").prop("selected", true);
        }else
        {
            $("select[for='dgrid-7'] option[value='0']").prop("selected", true);
        }

        calculate_money();
    });


   

     $(".cus_name_check").keyup(function(e){
        $(".box_edit_cus").html("");
        $("#box_choose_customer input[name='cus_id']").val('');
        
      });

     $(".input_mask").mask("9.999.999.999");

     // Highlight row
     $(".highlight_row").removeClass( "highlight_row", 3000, "easeInBack" );

      // flex label
      $('.fl-flex-label').flexLabel();

      /***************edit gallery***************/

      $(".btn_edit_gallery").click(function(){
          var gallery_id = $(this).attr("id");
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=gallery&subact=getData",
              data: {gallery_id: gallery_id},
              success: function(html)
              {
                var obj = JSON.parse(html);

                // $("form#gallery_edit_form select[name='cat_id'] option[value='"+obj.cat_id+"']").prop("selected", true);
                select2SetValue($("form#gallery_edit_form select[name='cat_id']"),obj.cat_id);
                $("form#gallery_edit_form input[name='gallery_name[en]']").val(obj.gallery_name.en);
                $("form#gallery_edit_form input[name='gallery_name[vn]']").val(obj.gallery_name.vn);
                $("form#gallery_edit_form input[name='gallery_name']").val(obj.gallery_name);
                $("form#gallery_edit_form input[name='gallery_image_alt']").val(obj.gallery_image_alt);
                $("form#gallery_edit_form input[name='id']").val(obj.gallery_id);
                $("form#gallery_edit_form textarea[name='gallery_description[en]']").val(obj.gallery_description.en);
                $("form#gallery_edit_form textarea[name='gallery_description[vn]']").val(obj.gallery_description.vn);
                $("form#gallery_edit_form textarea[name='gallery_description']").val(obj.gallery_description);
                $("form#gallery_edit_form img#upload_img_show").attr("src", obj.gallery_image);
                $("form#gallery_edit_form img#upload_img_show").css("display", "block");
                $("form#gallery_edit_form select[name='gallery_display'] option[value='"+obj.gallery_display+"']").prop("selected", true);

                  $("form#gallery_edit_form input[name='gallery_sort_order']").val(obj.gallery_sort_order);

               if( $("form#gallery_edit_form").find("select[name='hair_color']").length > 0)
               {
                 $("form#gallery_edit_form select[name='hair_color'] option[value='"+obj.hair_color+"']").prop("selected", true);

               }

                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                      src: '#box_edit_gallery',
                    },
                  });


              }
          });// ajax
      });
      
      $(".btn_edit_giftcard").click(function(){
          var giftcard_id = $(this).attr("id");
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=giftcards&subact=getData", 
              data: {giftcard_id: giftcard_id},
              success: function(html)
              {
                var obj = JSON.parse(html);
// console.log(obj);return false;
                $("form#giftcard_edit_form input[name='product_name']").val(obj.product_name);
                $("form#giftcard_edit_form input[name='product_price_sell']").val(obj.product_price_sell);
                $("form#giftcard_edit_form textarea[name='product_description']").val(obj.product_description);
                $("form#giftcard_edit_form [name='product_image_alt']").val(obj.product_image_alt);
                $("form#giftcard_edit_form input[name='id']").val(obj.product_id);
                $("form#giftcard_edit_form input[name='product_show'][value='"+obj.product_show+"']").prop("checked", true);
                
                var link = "";
                if(obj.product_image)
                {
                   link = upload_url +"/product/"+obj.product_image;
                }
                $("form#giftcard_edit_form img#upload_img_show").attr("src", link);
                $("form#giftcard_edit_form img#upload_img_show").css("display", "block");
                $("form#giftcard_edit_form img#upload_img_show").css("width", "auto");

                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                      src: '#box_edit_giftcard',
                    },
                  });


              }
          });// ajax
      });

    $(".btn_edit_coupon").click(function(){
        var coupon_id = $(this).attr("id");
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=coupons&subact=getData",
            data: {coupon_id: coupon_id},
            success: function(html)
            {
                var obj = JSON.parse(html);

                $("form#coupon_edit_form input[name='coupon_name']").val(obj.coupon_name);
                $("form#coupon_edit_form input[name='coupon_coupon_code']").val(obj.coupon_coupon_code);
                $("form#coupon_edit_form input[name='coupon_desc_1']").val(obj.coupon_desc_1);
                $("form#coupon_edit_form input[name='coupon_desc_2']").val(obj.coupon_desc_2);
                $("form#coupon_edit_form input[name='coupon_desc_3']").val(obj.coupon_desc_3);
                $("form#coupon_edit_form input[name='coupon_image_alt']").val(obj.coupon_image_alt);

                $("form#coupon_edit_form input[name='id']").val(obj.coupon_id);

                $("form#coupon_edit_form img#upload_img_show").attr("src", obj.coupon_image);
                $("form#coupon_edit_form img#upload_img_show").css("display", "block");

                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#box_edit_coupon',
                    },
                });


            }
        });// ajax
    });

    // Auto check box
    $(".autoCheckBox").each(function(){
        var valcheck = $(this).val();
        if(valcheck == 1)
        {
          $(this).attr("checked", "checked");
        }else
        {
          $(this).removeAttr("checked");
        }
     });

    // Mask Input
    if ( typeof phoneFormat != "undefined" )
    {
        var plholder = phoneFormat == "(000) 000-0000" ? "Phone (___) ___-____" : "Phone ____ ___ ____";
        $(".inputPhone").mask(phoneFormat, {placeholder: plholder});
    }
    // End mask input

    // Init Event Click Wrap
    initEventClickWrap('form_product', '[type="checkbox"]');
    initEventClickWrap('form_product', '.checkbox');
});// end document


  function check_enter_number(evt, onthis)
  {
    if(isNaN(onthis.value+""+String.fromCharCode(evt.charCode))) 
    {
       return false; 
    }
    else
    {
        // calculate_assets_warranty(onthis.value+evt.key);
    }
        
  }
 
function check_enter_number_max ( onthis ) 
{
  var maxvalue = $(onthis).attr('maxvalue');
  if ( typeof maxvalue != 'undefined' && maxvalue > 0 )
  {
    maxvalue = maxvalue*1;
    var number = $(onthis).val();
    if ( typeof number != 'undefined' && number > maxvalue)
    {
      $(onthis).attr('value', 100);
      $(onthis).val(100);
      console.log('b');
      console.log(number, maxvalue);
    }
  }
}


function calculate_money_asset_2() 
{
    var check_active = $(".row-grid-asset.active").length;

    if(check_active == 1)
    {
        var qty = $(".row-grid-asset.active").find("input[for='dgrid-4']").val();
        var price = $(".row-grid-asset.active").find("input[for='dgrid-5']").val();
        var tax = $(".row-grid-asset.active").find("select[for='dgrid-7']").val();
     
        // Check quantity
        if (qty <= 0 && qty != '') {
            $(".row-grid-asset.active").find("input[for='dgrid-4']").val(1);
            qty = 1;
        }
        qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 0;
        price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
        // Fixed price follow currency
        price = toFixedNumber(price);
        tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;

        var total = qty * price;
        var price_tax = Math.round((tax * total) / 100);
        total = total + price_tax;

        $(".row-grid-asset.active").find("input[for='dgrid-6']").val(total);

        // Total show
        show_total_assets();
        // Update asset list
        var item_id = $(".row-grid-asset.active").find("input[name='item_id[]']").val();
  
        $.ajax({
            type: "post",
            url: site_root_domain + "/?subact=update_asset_to_list",
            data: {quantity: qty, price: price, tax: tax, item_id: item_id},
            success: function (html) {
                 
            }
        });

    }
    else
    {
        if(typeof show_total_assets == "function" )
        {
            show_total_assets();
        }
    }

    if(typeof calculate_total_transaction == "function" )
    {
        calculate_total_transaction();
    }
    
}


 function check_enter_number_2( onthis, type = 0)
  {
 
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
             calculate_money_asset_2();
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
                 calculate_money_subitem_product();
                 calculate_money_asset_2();
              }  
              else
              {
                 $(onthis).val(number);
                 
                // Calculate assets warranty
                calculate_assets_warranty(number);
           
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
   


  function check_phone(evt)
  {
      var number = evt.key;
      var patt = new RegExp(/\d|\+/);
      var res = patt.test(number);
 
      if(res == true)
      {
        return true;
      }else
      {
        return false;
      }

  }


  function qsearch(table_name, id_result, event) 
  {
    var data;
    var urls;

      if(table_name == 'shipment')
      {
        
          var value_search = event.value;
          data = {key_search:value_search};
        
          urls = site_root_domain+"/?site=store_request&act=search&subact=search_shipment";
          // search ajax
          $.ajax({
              type:"post",
              url: urls,
              data: data,
              success: function(html)
              {
 
                  var obj = JSON.parse(html);
                  $('#'+id_result).html('');
                  if(obj.status == "success")
                  {
                      var html_li = "";
                      $.each(obj.data, function(index, value)
                      {
                        html_append_shipment(value.shi_id,value.shi_name,id_result, table_name);
                        html_li += "<li><div class='radio'><input type='radio' name='radio_choose' id='check-radio-"+value.shi_id+"' value='"+value.shi_id+"' onClick=\"apply_value('"+value.shi_id+"','"+value.shi_name+"','"+id_result+"','"+table_name+"');\" /><label for='check-radio-"+value.shi_id+"' name_show='"+value.shi_name+"'>"+value.shi_name+"</label></div></li>";
                      });

                      setTimeout(function(){
                          $('#'+id_result).html(html_li);
                      }, 1000);
                    
                  }else
                  {
                      $('#'+id_result).html('<li><font color=red> + '+obj.msg+'</font></li>');
                  }
                  
              }
          });
      }else if(table_name == 'user')
      {
         
          var value_search = event.value;
          data = {key_search:value_search};
     
          urls = site_root_domain+"/?site=user&act=search&subact=search_user";

          // search ajax
          $.ajax({
              type:"post",
              url: urls,
              data: data,
              success: function(html)
              {
                   
                  var obj = JSON.parse(html);
                  $('#'+id_result).html('');
                  if(obj.status == "success")
                  {
                      $.each(obj.data, function(index, value)
                      {
                        html_append(value.user_id,value.user_name,id_result, table_name);
                      });
                    
                  }else
                  {
                      $('#'+id_result).html('<li><font color=red>'+obj.msg+'</font></li>');
                  }
                  
              }
          });
      }

      
  }

  function html_append(id,name, id_result, table_name) 
  {
      // var html = "<li><input type='radio' name='radio_choose' onClick=\"apply_value('"+id+"','"+name+"','"+id_result+"','"+table_name+"');\" value='"+id+"' name_show='"+name+"'/><span>"+name+"</span></li>";
      var html = "<li><div class='radio'><input type='radio' name='radio_choose' id='radio-"+id+"' value='"+id+"' onClick=\"apply_value('"+id+"','"+name+"','"+id_result+"','"+table_name+"');\" /><label for='radio-"+id+"' name_show='"+name+"'>"+name+"</label></div></li>";
      
      setTimeout(function(){
          $('#'+id_result).append(html);
      }, 100);
      
  }
   function html_append_shipment(id,name, id_result, table_name) 
  {
      // var html = "<li><input type='radio' name='radio_choose' onClick=\"apply_value('"+id+"','"+name+"','"+id_result+"','"+table_name+"');\" value='"+id+"' name_show='"+name+"'/><span>#"+id+" - "+name+"</span></li>";
      var html = "<li><div class='radio'><input type='radio' name='radio_choose' id='check-radio-"+id+"' value='"+id+"' onClick=\"apply_value('"+id+"','"+name+"','"+id_result+"','"+table_name+"');\" /><label for='check-radio-"+id+"' name_show='"+name+"'>"+name+"</label></div></li>";
      setTimeout(function(){
          $('#'+id_result).append(html);
      }, 1000);
      
  }


  function apply_value(id,name,id_result, table_name) 
  {
 
    $('#'+id_result).html('');
    $('[for="'+table_name+'_id_apply"]').val(id);
    $('[for="'+table_name+'_name_apply"]').val(name);
  }

  // Search hang hoa
  function search_goods(key, forkey, obj_element)
  {
      var user_id = $("#formorder-signin_v1 #user_id").val();

      if(module_name == "module_order")
      {
        var cus_id = $("#formorder-signin_v1 #cus_id").val();

        if(  obj_element.attr("name") == "product_name[]")
        {
            cus_id = "";
        }
      }  
      var product_type = $("#product_type_xxx").val();

      // ThamLV d22-6-2018: add store id for search goods
      var store_id = module_name == "module_order" ? $('.store_id_param').val() : '';

      $.ajax({
          type: "post",
          url: site_root_domain+"/?site=store_request&act=search&subact=search_goods",
          data: {key_search: key, product_type : product_type, cus_id: cus_id, module_name: module_name, user_id: user_id, store_id: store_id},
          success: function(html)
          {
         
              var obj = JSON.parse(html);
              var output_li="";
              if(obj.status == "success")
              {
                  $(".box_result_search").html("");
                  $(".box_result_search").hide();
                  $.each(obj.data, function(index, value)
                  {
                   
                       var description = value.product_description != null ?  value.product_description : '';

                      if(value.product_type == 0)
                      {
                        if( typeof value.variants != 'undefined' && value.variants.length > 0 )
                        {
                          output_li += ""+
                          "<li>"+
                          " <div class='name-wrap'>"+
                          "   <div class='id'>#"+value.product_id+"</div>"+
                          "   <div class='content'>"+
                          "     <p class='main'>- "+value.product_name+"</p>"+
                          "";

                          // variants
                          $.each(value.variants, function(index2, value2)
                          {
                            output_li += ""+
                            "<p class='sub pointer' onclick='show_detail_goods(this)' for='"+forkey+"' name='"+value.product_name+"'   product_type='"+value.product_type+"'   product_cycle='"+value.product_cycle+"'  description='"+description  +"' id_product='"+value.product_id+"'  price='"+value2.var_price+"' tax='"+value.product_tax+"' sup_id='"+value.sup_id+"' sup_name='"+value.sup_name+"' commission_type='"+value.product_commission_type+"' commission_value='"+value.product_commission_value+"' store_id='"+value.store_id+"' id_var='"+value2.var_id+"'>"+
                            " <span class='name_show'>- "+value2.var_title +" - "+value2.var_price_c +"</span><i class='fa fa-gift'></i>"+
                            "</p>"+
                            "";
                          });
                          output_li += ""+
                          "   </div>"+
                          " </div>"+
                          "</li>"+
                          "";
                        }
                        else
                        {
                          output_li += "<li onclick='show_detail_goods(this)' for='"+forkey+"' name='"+value.product_name+"'   product_type='"+value.product_type+"'   product_cycle='"+value.product_cycle+"'  description='"+description  +"' id_product='"+value.product_id+"'  price='"+value.product_price+"' tax='"+value.product_tax+"' sup_id='"+value.sup_id+"' sup_name='"+value.sup_name+"' commission_type='"+value.product_commission_type+"' commission_value='"+value.product_commission_value+"' store_id='"+value.store_id+"' id_var='0'><span class='name_show'>#"+value.product_id+"- "+value.product_name+" - "+value.product_price_show +" <i class='fa fa-gift'></i></span></li>";
                        }
                      }
                      else
                      {
                        if( typeof value.variants != 'undefined' && value.variants.length > 0 )
                        {
                          output_li += ""+
                          "<li>"+
                          " <div class='name-wrap'>"+
                          "   <div class='id'>#"+value.product_id+"</div>"+
                          "   <div class='content'>"+
                          "     <p class='main'>- "+value.product_name+"</p>"+
                          "";

                          // variants
                          $.each(value.variants, function(index2, value2)
                          {
                            output_li += ""+
                            "<p class='sub pointer' onclick='show_detail_goods(this)' for='"+forkey+"' name='"+value.product_name+"'   product_type='"+value.product_type+"'   product_cycle='"+value.product_cycle+"'  description='"+description  +"' id_product='"+value.product_id+"'  price='"+value2.var_price+"' tax='"+value.product_tax+"' sup_id='"+value.sup_id+"' sup_name='"+value.sup_name+"' commission_type='"+value.product_commission_type+"' commission_value='"+value.product_commission_value+"' store_id='"+value.store_id+"' id_var='"+value2.var_id+"'>"+
                            " <span class='name_show'>- "+value2.var_title +" - "+value2.var_price_c +"</span><i class='fa fa-recycle'></i>"+
                            "</p>"+
                            "";
                          });
                          output_li += ""+
                          "   </div>"+
                          " </div>"+
                          "</li>"+
                          "";
                        }
                        else
                        {
                          output_li += "<li onclick='show_detail_goods(this)' for='"+forkey+"' name='"+value.product_name+"'   product_type='"+value.product_type+"'   product_cycle='"+value.product_cycle+"'  description='"+description  +"' id_product='"+value.product_id+"'  price='"+value.product_price+"' tax='"+value.product_tax+"' sup_id='"+value.sup_id+"' sup_name='"+value.sup_name+"' commission_type='"+value.product_commission_type+"' commission_value='"+value.product_commission_value+"' store_id='"+value.store_id+"' id_var='0'><span class='name_show'>#"+value.product_id+"- "+value.product_name+" - "+value.product_price_show +" <i class='fa fa-recycle'></i></span></li>";
                        }
                      }
                      
 
                  });
              }

              var html_show = "";
              if( typeof pFrmType != 'undefined' && pFrmType == 'transaction' )
              {
                  html_show += "<div class='add_new_el pointer'><i class='fa fa-plus'></i><a class='add_new_goods_transaction' href='#box_product'>Add new</a></div>";
              }
              else
              {
                html_show += "<div class='add_new_el pointer'><i class='fa fa-plus'></i><a class='add_new_goods' href='#box_product'>Add new</a></div>";
              }

              html_show += ""+
                  "<div class='box_result_search_inner'>"+
                  " <ul class='list_goods'>"+output_li+"</ul>"+
                  "</div>"+
                  "";

              obj_element.parent().find(".box_result_search").html(html_show);
              obj_element.parent().find(".box_result_search").show();
          }
      });
  }

  function show_detail_goods(obj) 
  {
 
      var name_product = obj.getAttribute("name");
      var description = obj.getAttribute("description");
      var price = obj.getAttribute("price");
      var old_price = obj.getAttribute("old_price");
      var forkey = obj.getAttribute("for");
      var id = obj.getAttribute("id_product");
      var tax = obj.getAttribute("tax");
      var product_type = obj.getAttribute("product_type");
      var product_cycle = obj.getAttribute("product_cycle");
      var sup_id = obj.getAttribute("sup_id");
      var sup_name = obj.getAttribute("sup_name");
      var product_commission_type = obj.getAttribute("commission_type");
      var product_commission_value = obj.getAttribute("commission_value");
      var var_id = obj.getAttribute("id_var");

      var item_id = $(".row-grid.active").find("input[name='item_id[]']").val();
      var option = "";

 
      $(".row-grid.active").find("input[for='dgrid-2']").val(name_product);
      $(".row-grid.active").find("textarea[for='dgrid-3']").val(description);
      $(".row-grid.active").find("input[for='dgrid-4']").val(1);
      $(".row-grid.active").find("input[for='dgrid-5']").val(price);
      $(".row-grid.active").find("input[name='product_old_price[]']").val(price);
      $(".row-grid.active").find("input[name='sup_name[]']").val(sup_name);
      $(".row-grid.active").find("input[name='sup_id[]']").val(sup_id);
      $(".row-grid.active").find("input[name='product_commission_type[]']").val(product_commission_type);
      $(".row-grid.active").find("input[name='product_commission_value[]']").val(product_commission_value);
      $(".row-grid.active").find("input.product_id").val(id);
      $(".row-grid.active").find("input.product_cycle_type").val(product_cycle);
      $(".row-grid.active").find("input.product_type").val(product_type);
      $(".row-grid.active").find("select[for='dgrid-7'] option[value='"+tax+"']").prop("selected", true);
      $(".row-grid.active").find('td.trash').html("<span class='edit_row_product' id="+id+" item_id='"+item_id+"'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span>" +
          '<span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>'+
          "<span class='del_row_invoid' item_id='"+item_id+"'><i class='fa fa-trash' aria-hidden='true'></i></span>");

      $(".row-grid.active").find("input[name='var_id[]']").val(var_id);
    
      //Check loai hang thang va show cycle 
      if(product_type == 0)//Hang hoa
      {
          option += "<option value='1'>"+cms_lang['gonce']+"</option>";
      }
      else
      {
        if(product_cycle == 0)//1 lan
        {
            option += "<option value='1'>"+cms_lang['gonce']+"</option>";
        }
        else if(product_cycle == 1)//Hang tháng
        {
            for(var i=1; i<=12; i++)
            {

              option += "<option value='"+i+"'>"+i+" "+cms_lang['gmonth']+"</option>";
            }
        }
        else if(product_cycle == 2)//Hang năm
        {
            for(var i=1; i<=12; i++)
            {

              option += "<option value='"+i+"'>"+i+" "+cms_lang['gyear']+"</option>";
            }
        }

      }

      // Check form co select dgrid-9 ???
      if( $(".row-grid.active").find("select[for='dgrid-9']").length > 0)
      {
         
        if(product_type == 0)//Hang hoa
        {
          $(".row-grid.active").find("select[for='dgrid-9']").html(option);
        }
        else
        {
          $(".row-grid.active").find("select[for='dgrid-9']").html(option).removeAttr("disabled");
        }
      }


      $(".box_result_search").html("");
      $(".box_result_search").hide();
      showCommissionItem(product_commission_type, product_commission_value, $(".row-grid.active").find(".commission_show"));
      calculate_money();

      // Save info selected
      $.ajax({
          type: "post",
          url: site_root_domain + "/?site=store_request&subact=save_to_list_product",
          data: {product_id: id, item_id: item_id},
          success: function(html) {
            
          }
      });

      if(typeof calculate_total_transaction == "function")
      {
          calculate_total_transaction();
      }

      // Recheck_row_product
     //var ele = $( "#data_table tr.row-grid" ).hasClass("active");
    // var pro_val = $(ele).find("input[name='product_id[]']").val();
     //console.log(pro_val+"a");

    // ThamLV d22-6-2018 add store id for search goods
    if( module_name == "module_order" )
    {
      var store_id = obj.getAttribute("store_id");
          store_id = typeof store_id != 'undefined' && store_id > 0 ? store_id : "";
      
      $('#formorder-signin_v1').find('select[name="store_id"]').val(store_id);
      $('#formorder-signin_v1').find('select[name="store_id"]').trigger('change');
    }
  }

  function check_max_value(el) 
  {
    if(el)
    {
        var checkdisable = 0;
     
        $(el+" .quan_list").each(function(){
          var check_val = $(this).attr("max_value");
          var number = parseInt($(this).val());
          
          if(typeof(check_val) !== "undefined")
          {
            check_val = parseInt(check_val);
            if(number >= check_val)
            {
              $(this).val(check_val);
            }else if(number < check_val || number == 0)
            {
              checkdisable ++;
            }
          }
        });

        if(checkdisable > 0)
        {
          $("button[for='not_enough']").prop("disabled", false);
          $("button[for='not_enough']").attr("checkclick", 1);
          $("a.btn_submit_form[for='not_enough']").attr('checkclick',1);
        }else
        {
          $("button[for='not_enough']").prop("disabled", true);
          $("button[for='not_enough']").attr("checkclick", 0);
          $("a.btn_submit_form[for='not_enough']").attr('checkclick',0);
        }
    }
      
  }

  function calculate_money(el)
  {

      if(module_name != "product_module")
      {
          // Check neu k phai module san pham dich vu thi vao day
          var is_zero = $("#is_zero").val();
          if(typeof(is_zero) == "undefined" || is_zero == "")
          {
            is_zero = 0;
          }
          var check_el = $(".row-grid.active").length;
          if(check_el > 0)
          {   
            // Check max value
            check_max_value(el);
       

            var qty = +$(".row-grid.active").find("input[for='dgrid-4']").val()*1;
            var price = +$(".row-grid.active").find("input[for='dgrid-5']").val()*1;
            var oldPrice = +$(".row-grid.active").find("input[name='product_old_price[]']").val()*1;
            var tax = +$(".row-grid.active").find("select[for='dgrid-7']").val()*1;
            var discountType = +$(".row-grid.active").find("[name='product_discount_type[]']").val()*1;
            var discountValue = +$(".row-grid.active").find("[name='product_discount_value[]']").val()*1;

            //Check exits element cycle
            if($(".row-grid.active").find("select[for='dgrid-9']").length > 0)
            {
              var cycle = $(".row-grid.active").find("select[for='dgrid-9']").val();
            }
            else
            {
              var cycle = 1;
            }
            // Check quantity
            if(qty <= 0 && qty != '' && is_zero == 0)
            {
              $(".row-grid.active").find("input[for='dgrid-4']").val(1);
              qty = 1;
            }


            let calResult = calculateItem( (oldPrice > price ? oldPrice : price), discountType, discountValue, tax, qty, cycle);

            $(".row-grid.active").find("[name='product_amount[]']").val(calResult.total.toFixed(2));


            // Total show
            show_total_goods();
            // if(qty > 0 && price > 0)
            // {
              // Update product list
              var item_id = $(".row-grid.active").find("input[name='item_id[]']").val();
 
              $.ajax({
                    type: "post",
                    url: site_root_domain + "/?site=store_request&subact=update_product_to_list",
                    data: {quantity: qty, price: price, tax: tax, item_id: item_id},
                    success: function(html) {
                       
                    }
              });
            // }

          }else
          {
            show_total_goods();
          }
      }

      if(typeof calculate_total_transaction == "function" )
      {
          calculate_total_transaction();
      }
    
  }

  function show_total_goods() 
  {
 
      var total_money_goods = 0;

      $("#data_table .row-grid").each(function(){
 
        var qty = +$(this).find("input[for='dgrid-4']").val();
        var price = +$(this).find("input[for='dgrid-5']").val();
        var oldPrice = +$(this).find("input[name='product_old_price[]']").val();
        var tax = +$(this).find("select[for='dgrid-7']").val();
        var discountType = +$(this).find("[name='product_discount_type[]']").val();
        var discountValue = +$(this).find("[name='product_discount_value[]']").val();

        // Check exits element cycle
        if($(this).find("select[for='dgrid-9']").length > 0)
        {
            var cycle = $(this).find("select[for='dgrid-9']").val();
        }
        else
        {
          var cycle = 1;
        }

          cycle = (!isNaN(parseInt(cycle)) && parseInt(cycle)>0) ? parseInt(cycle) : 1;
          qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 1;
          price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
          oldPrice = !isNaN(parseFloat(oldPrice)) ? parseFloat(oldPrice) : 0;
          tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;
          discountType = !isNaN(parseInt(discountType)) ? parseInt(discountType) : 0;
          discountValue = !isNaN(parseFloat(discountValue)) ? parseFloat(discountValue) : 0;

          let calResult = calculateItem( (oldPrice > price ? oldPrice : price), discountType, discountValue, tax, qty, cycle);

        $(this).find("input[for='dgrid-6']").val(calResult.total.toFixed(2));
        total_money_goods += calResult.total;

    });

      var total_show = formatNumberInput(total_money_goods);
      $("#total_show").html(total_show);
      $("#total_show").attr("total_product",total_money_goods);
      show_total();
  }


   

  function show_total() 
  {
 
       if($("form#formorder-signin_v1 #total_price").length > 0)
       {
          var total_asset_xxx = $("#total_show_asset").attr("total_asset");
          var total_product_xxx = $("#total_show").attr("total_product");
          var temp_total = parseFloat(total_product_xxx) + parseFloat(total_asset_xxx);
          var total_price_xxx =    formatNumberInput(temp_total);   
          $("#total_price").html(total_price_xxx);
       }
  }




  function show_detail_product(obj, forkey) 
  {
 
      var name_product = obj.getAttribute("name");
      var description = obj.getAttribute("description");
      var price = obj.getAttribute("price");
      // var forkey = obj.getAttribute("for");
      var id = obj.getAttribute("id_product");
  
      $(".data_subitem[for='"+forkey+"'] .row-item.active").find("input[for='item-2']").val(name_product);
      $(".data_subitem[for='"+forkey+"'] .row-item.active").find("textarea[for='item-3']").val(description);
      $(".data_subitem[for='"+forkey+"'] .row-item.active").find("input[for='item-4']").val(1);
      $(".data_subitem[for='"+forkey+"'] .row-item.active").find("input[for='item-5']").val(price);
      $(".data_subitem[for='"+forkey+"'] .row-item.active").find("input.product_id").val(id);
      $(".box_result_find").html("");
      calculate_money_subitem(forkey);
  }

  function calculate_money_subitem(forkey)
  {
    var total = 0;
    $(".data_subitem[for='"+forkey+"'] .row-item").each(function(){
  
        var qty = $(this).find("input[for='item-4']").val();
        var price = $(this).find("input[for='item-5']").val();
        var tax = $(this).find("input[for='item-6']").val();
        // Check quantity
        if(qty <= 0 && qty != '')
        {
          $(this).find("input[for='item-4']").val(1);
          qty = 1;
        }
        qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 0;
        price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
        //Fixed price
        price = toFixedNumber(price);
        tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;
        total += qty * price;
    });

    // var price_crr = $("form#edit_product_form input[name='product_price']").val();
    // price_crr = !isNaN(parseFloat(price_crr)) ? parseFloat(price_crr) : 0;
    // console.log(price_crr);
    // if(price_crr == 0)
    // {
        price_crr = total;
        $("form.form_data[for='"+forkey+"'] input[name='product_price']").val(price_crr);
    // }
    var tax_product = $("form.form_data[for='"+forkey+"'] select[name='product_tax']").val();
    var tax_crr = !isNaN(parseFloat(tax_product)) ? parseFloat(tax_product) : 0;
    var price_tax = Math.round((tax_crr * price_crr)/100);
    var product_price = price_crr+price_tax;
    var total_show = formatNumberInput(product_price);
    $("form.form_data[for='"+forkey+"'] span#product_total").html(total_show);
  }

  function calculate_subitem() 
  {
    var  price_sell = $("form.form_data input[name='product_price_sell']").val();
    var tax = $("form.form_data select[name='product_tax']").val();
    price_sell = !isNaN(parseFloat(price_sell)) ? parseFloat(price_sell) : 0;
    tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;
    var total = parseFloat(price_sell + (price_sell*tax)/100);
 
    var total_show = formatNumberInput(total);
    $("form.form_data #product_total").html(total_show);
  }

   function calculate_money_subitem_product()
  {
    var total = 0;
    $("#data_table .row-grid").each(function(){
 
        var qty = $(this).find("input[for='dgrid-5']").val();
        var price = $(this).find("input[for='dgrid-6']").val();
        var tax = $(this).find("input[for='dgrid-7']").val();
        qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 1;
        price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
        // Fixed price follow currency
        price = toFixedNumber(price);
        tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;
        total += qty * price;


    });
  
    var p_price = $("#p_price").val();
    
    $("#p_price").val(total);
    backup_datatable();
  }


  function change_supplier_type(type) 
  {
      $("input#supplier_idcard_number").val('');
      if(type == 1)
      {
        $(".change_supplier").show();
      }else
      {
        $(".change_supplier").hide();
      }
  }

  function change_city(city_id) 
  {
      if(city_id)
      {
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=store_request&subact=getDistrict",
              data: {city_id: city_id},
              success: function(html){
                 
                  $("select#district_id").html(html);
              }
          });
      }
  }

  function autocompleteAll(selector)
  {
      let tmp = $(selector).val();
      $(selector).val("").trigger("focus");
      $(selector).val(tmp);
  }

  function autocompleteSearch(selector, urls, input_response, type, min = 2)
  {
      $(selector).autocomplete({
      source: urls,
      minLength: min,
      selectFirst: true,
      select: function( event, ui ) {

          if(type == 'term')
          {
              action_term_trx(selector, ui);
              return false;
          }

        if(input_response)
        {
          $(input_response).val(ui.item.id);
        }

        // goi ham theo type
        if(type == "customer")
        {
            action_customer(ui.item.id);
        }
        
        if(type == "order")
        {   
            if(ui.item.id == "add_new")
            {
                $(".add_new_cus").trigger("click");
                return false;
            }
            else
            {
               action_customer_morder(ui.item.id,ui.item.cus_email,ui.item.value,ui.item.cus_address,ui.item.cus_phone, ui.item.cus_first_name, ui.item.cus_last_name);
            }

        }
        if(type == "sites")
        {   
              action_customer_msites(ui.item.id,ui.item.cus_email,ui.item.cus_full_name,ui.item.cus_address,ui.item.cus_phone);
        }
        if(type == 'trx')
        {

            if(typeof ui.item.cus_email_invoice != "undefined")
            {
                if(ui.item.cus_email_invoice !== "" && ui.item.cus_email_invoice != null)
                {
                    ui.item.cus_email = ui.item.cus_email_invoice;
                }
            }
            action_customer_trx(ui.item.id,ui.item.cus_email,ui.item.value,ui.item.cus_address,ui.item.cus_phone, $(selector));
        }

          if(type == 'trx2')
          {
              action_user_trx(ui.item.id,ui.item.email,ui.item.value,ui.item.address, $(selector));
          }

        if(type == 'returns')
        {
          window.location.href = site_root_domain+'/?site=returns&act=show&id='+ui.item.id;
        }

        if(type == "shipment")
        {
            if(ui.item.id == "add_new")
            {
                $(".add_new_shipment").trigger("click");
                return false;
            }else
            {
                if(pms['shipment_edit'] == 1)
                {
                  $(".box_edit_shipment").html("<a id='"+ui.item.id+"' class='btn_gen btn_edit_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                }
            }
        }

      },
      change: function (event, ui) {
          if(type != 'term')
          {
              if (ui.item === null)
              {
                  $(this).val('');
                  // reset val
                  if(input_response)
                  {
                      $(input_response).val('');
                  }

                  if(type == "shipment")
                  {
                    $(".box_edit_shipment").html("");
                  }
                  
              }

              if( type == "order" && !$(this).val() )
              {
                action_customer_morder(0, '', '', '', '', '', '');
              }
          }
       }
    }).focus(function() {
        // if(min == 0)
        {
    
            if(type != 'term')
            {
                //$(selector).val("");
            }

            // Check input
            if(type == "shipment")
            {
              var ship_val = $(selector).val();
              if(ship_val == "")
              {
                if(input_response)
                {
                    $(input_response).val('');
                }

                $(".box_edit_shipment").html("");
              }
            }

            $(this).autocomplete("search", $(this).val());
        }
      })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
        var html_addnew = "";
        if(item.id == "add_new")
        {
          var html = `<li class="el_addnew"><div class="ui-menu-item-wrapper"><i class="fa fa-plus"></i><a>`+item.label+`</a></div></li>`;
        }else
        {
          var html = `<li class="ui-menu-item"><div class="ui-menu-item-wrapper">` + item.label + `</div></li>`;
        }
      return $( html)
        .appendTo( ul );
    };
      
  }

  function confirmAction(text_show, theURL) 
  {
      swal({
            title: confirm_alert_title,
            text: text_show,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: cms_lang['gnotice_ok'],
            cancelButtonText: cms_lang['gnotice_cancel'],
            closeOnConfirm: true,
            closeOnCancel: true
       }).then(function () {
            if(theURL)
              {
                window.location.href=theURL;
              }
            return true;
       
       })
        
  }

  function alertText(text_show, type, checkreturn, typePopup=null, objPopup=null)
  {
    type = type ? type : 'success';
    swal({
      title: type == 'success' ? cms_lang['gnotice'] : '<span style="color: red;">'+cms_lang['gnotice']+'</span>',
      html: '<div>'+text_show+'</div>',
      showCloseButton: true,

      confirmButtonClass: type == 'success' ? 'btn-primary' : 'btn-danger',
      confirmButtonText: cms_lang['gnotice_ok'],

      customClass: 'modalStyle', 
    }).then(function (result) {
      if( checkreturn )
      {
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: checkreturn
          },
        });
      }
      else
      {
        if( typePopup == 'bootrap-modal' && objPopup.length )
        {
          objPopup.modal('hide');
        }
        else
        {
          $.magnificPopup.close();
        }
      }
    });

      // swal({
      //         title: '',
      //         text: text_show,
      //         type: type,
      //         confirmButtonClass: "btn-"+type,
      //         confirmButtonText: cms_lang['gnotice_ok'],
      //      }).then(function () {
      //         if (  checkreturn) {
      //              $.magnificPopup.open({
      //                 type: 'inline',
      //                 items: {
      //                   src: checkreturn
      //                 },
      //               });
      //          }else
      //          {
      //              if(typePopup == 'bootrap-modal' && objPopup.length)
      //              {
      //                  objPopup.modal('hide');
      //              }
      //              else
      //              {
      //                  $.magnificPopup.close();
      //              }
      //          }
                                                     
      //      }) 
  }

  function pNotifyACP(text, type='error')
  {
      new PNotify({
          text: text,
          hide: true,
          delay: 3000,
          remove: true,
          type: type //"notice", "info", "success", or "error".
      });
  }

  function checkDoubleClick(el,check)
  {
 
      if(check == 1)
      {
          // $(el).attr("disabled", "disabled");
          $(el).prop("disabled", true);
      }else
      {
          // $(el).removeAttr("disabled");
          $(el).prop("disabled", false);
      }
  }

  function formatNumberInput(number, dec_number)
  {
    //Check input num is NaN
    if(isNaN(number)) {
      number = 0;
    }

    //dec_number: Phần thập phân
    dec_number = !isNaN(dec_number) || typeof(dec_number) !== "undefined" ? parseInt(dec_number) : 0;
    if(currency_type == "$")
    {
     return "$ "+number.toFixed(2).replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });
    }
    else
    {
       return number.toFixed(dec_number).replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        })  + " đ ";
    }
  }


 function toFixedNumber(number) 
  {
     //Check input num is NaN
    if(isNaN(number)) {
      number = 0;
    }
    
    if(currency_type == "$")
    {

     return  number.toFixed(2);
    }
    else
    {
       return number.toFixed(); 
    }
  }

function del_confirm(link)
{
      notie.confirm(cms_lang['gnotice_confirm_action'], cms_lang['gnotice_ok'], cms_lang['gnotice_cancel'], function() {
            notie.alert(1,
                 'aa'
              , 2);
        });
 
}


$("input#ufile").change(function() {
    $("#ufile_output ul").empty();
        var ele = document.getElementById($(this).attr('id'));
        var result = ele.files;
        for(var x = 0;x< result.length;x++)
        {
          var fle = result[x];
          
          readFile(ele,'upload_img_show', 'ufile_output_b64');
        }

});

$("input#ufile_mobile").change(function() {
    $("#ufile_mobile_output ul").empty();
    var ele = document.getElementById($(this).attr('id'));
    var result = ele.files;
    for(var x = 0;x< result.length;x++)
    {
        var fle = result[x];

        readFile(ele,'upload_img_mobile_show', 'ufile_mobile_output_b64');
    }

});

$("input#ufile_avatar").change(function() {
    var ele = document.getElementById($(this).attr('id'));
    var result = ele.files;
    for(var x = 0;x< result.length;x++)
    {
        var fle = result[x];

        readFile(ele,'upload_img_avatar', 'ufile_avatar_output_b64');
    }

});

$("input#checkinfile").change(function() {
    var ele = document.getElementById($(this).attr('id'));
    var result = ele.files;
    for(var x = 0;x< result.length;x++)
    {
        var fle = result[x];

        readFile(ele,'upload_img_checkin_show', 'checkinfile_output_b64');
    }

});

function delete_fileToAttach(id, site)
{
  var img = $("#upload_img_show").attr("src");
  // Xoá hình theo id, module
  if(id && site && img)
  {
    swal({
          title: 'Are you sure?',
          text: "Are you sure delete this image?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ok'
        }).then(function () {
            waitingDialog.show(cms_lang.waiting_dialog_msg);
            $("#upload_img_show").removeAttr("src");
            $("#upload_img_show").css("display","none");
            $("#ufile_output_b64").val("");
            // ajax del image
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site="+site+"&subact=del_img",
                data: {id: id},
                success: function(response)
                {
                  waitingDialog.hide();
                  if(response == 1)
                  {
                    swal( 'Successful!', 'You have successfully deleted this photo', 'success');
                  }else{
                    swal( 'Unsuccessful!', 'Deleting this photo failed', 'error');
                  }
                }
            });

            
            
        });
  }else
  {
    $("#upload_img_show").removeAttr("src");
    $("#upload_img_show").css("display","none");
    $("#ufile_output_b64").val("");
  }

  $('.drop-zone-wrap').removeClass('exist-preview');
}
function performClick(elemId) {
   var elem = document.getElementById(elemId);
   if(elem && document.createEvent) {
      var evt = document.createEvent("MouseEvents");
      evt.initEvent("click", true, false);
      elem.dispatchEvent(evt);
   }
}

function readFile(onthis, element_img , element_b64 ) {
  
  if (onthis.files && onthis.files[0]) {
    
    var FR= new FileReader();
    
    FR.addEventListener("load", function(e) {
      document.getElementById(element_img).src       = e.target.result;
      document.getElementById(element_b64).value     = e.target.result;
      $("#"+element_img).css("display","block");
      $("#"+element_img).css("width","100%");
      $("#"+element_img).css("height","auto");
      $("#"+element_img).css("margin","0 auto");
      var objnew = $("#"+element_img).parent(".drop-zone");
      objnew.find(".font-icon-cloud-upload-2").css("display","none");
      objnew.find(".drop-zone-caption").css("display","none");
      
    }); 
    
    FR.readAsDataURL( onthis.files[0] );
  }
  
}


   var valid_frm_product_group = $("form#add_group_product_form").validatenew({
          focusInvalid: true,
          rules: {
            pg_name: "required"
          },
          messages:{
            pg_name: cms_lang['gnotice_fill_product_name'],
          }
        });

    $("#box_add_group_product").on("click",".btn_add_product_group", function()
    {

        if($("form#add_group_product_form").validnew())
        {

            $("form#add_group_product_form input[name='base64_image']").val("");
            var data = $("form#add_group_product_form").serialize();
            var file_data = $("input[name='pg_avartar']").prop("files")[0];
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.pgmanu_error_msg").hide();
            $("p.pgmanu_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=product_group&subact=ajax_add_product_group&"+data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(html)
                {
              
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {

                         var p_type = $("form#form-signin_v1 [name='p_type']").val();
                         get_group_product(p_type, obj.pg_id);
                       // $("form#form-signin_v1 #p_product_group").html(obj.data_option);
                      
                        if(pms['product_group_edit'] == 1)
                        {
                           if(  $("form#form-signin_v1 [name='p_type']").val()  == obj.pg_type  )
                           {
                       
                              $(".box_product_group").html("<a id='"+obj.pg_id+"' class='btn_edit_product_group btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                           }
                          
                        }
                        $.magnificPopup.close();
                        
                       // alertText(obj.msg, "success");
                       call_notify("Nhóm sản phẩm", obj.msg, "success", "");

                         
                    }else
                    {
                        $("p.pgmanu_error_msg").show();
                        $("p.pgmanu_error_msg").html(obj.msg);
                    }
                }
            })
        }
        else
        {
            return false;

        }
    });



      // Edit NHom sp
      $("form#form-signin_v1").on("click", ".btn_edit_product_group", function(e){

          let prePopup = $(this).parents(".mfp-content:first").children("*").first();
          if(prePopup.length && (checkReturn = prePopup.attr("id"))) {
              $("#add_group_product_form input[name=checkReturn]").val("#"+checkReturn);
          }
          else{
              $("#add_group_product_form input[name=checkReturn]").val("");
          }

        // $("form#add_group_product_form")[0].reset();
        clearForm($("form#add_group_product_form"));
        var group_product_id = $(this).attr("id");
        $(".pgmanu_error_msg").html('');
 
        $(".title_group_product  ").html(cms_lang.edit_group_product);
        $(".change_action_product_group").html("<input class='btn btn_edit_do_group_product' type='button' value='"+cms_lang.save_info+"'/>");
        e.stopPropagation();
        // Ajax get data
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=product_group&subact=ajax_get_data_product_group",
            data: {group_product_id: group_product_id},
            success: function(html)
            {
                 
                var obj = JSON.parse(html);

                if(obj.status == "success")
                {
                      select2SetValue($("#add_group_product_form select[name='pg_parent']"),obj.data.product_group_parent);
                      $("#add_group_product_form input[name='product_group_id']").val(obj.data.product_group_id);
                    
                      $("#add_group_product_form input[name='pg_name']").val(obj.data.product_group_name);
                      $("#add_group_product_form input[name='pg_code']").val(obj.data.product_group_code);
                      $("#add_group_product_form textarea[name='pg_description']").html(obj.data.product_group_description);
       

                      $("#add_group_product_form input[name='pg_type'][value='"+obj.data.product_group_type+"']").prop("checked",true);
                      $("#add_group_product_form input[name='pg_status'][value='"+obj.data.product_group_status+"']").prop("checked",true);


                      if(obj.product_group_avatar)
                      {
                          var url_img =  "/uploads/product/thumbnail/"+obj.data.product_group_avatar;
                          $("#add_group_product_form #upload_img_show").attr("src",url_img);
                      }
                       
                      $.magnificPopup.open({
                        type: 'inline',
                        items: {
                          src: '#box_add_group_product'
                        },
                      });

                }
                else
                {
                    alertText(obj.msg);
                }

              
                
            }
        });
        
    });

     // Edir nhom sp 
    $("#box_add_group_product").on("click",".btn_edit_do_group_product", function()
    {

        if($("form#add_group_product_form").validnew())
        {

            $("form#add_group_product_form input[name='base64_image']").val("");
            var data = $("form#add_group_product_form").serialize();
            var file_data = $("input[name='pg_avartar']").prop("files")[0];
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.pgmanu_error_msg").hide();
            $("p.pgmanu_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=product_group&subact=ajax_edit_product_group&"+data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(html)
                {
         
                    var obj = JSON.parse(html);
                    if(obj.status == "success")
                    {

                         var p_type = $("form#form-signin_v1 [name='p_type']").val();
                         get_group_product(p_type, obj.pg_id);
                      
                        if(pms['product_group_edit'] == 1)
                        {
                           if(  $("form#form-signin_v1 [name='p_type']").val()  == obj.pg_type  )
                           {
                       
                              $(".box_product_group").html("<a id='"+obj.pg_id+"' class='btn_edit_product_group btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                           }
                          
                        }
                        // $.magnificPopup.close();
                        
                        alertText(obj.msg, "success");

                         
                    }else
                    {
                        alertText(obj.msg );
                   
                    }
                }
            })
        }
        else
        {
            return false;

        }
    });

$("#data_table").on("click",".add_new_goods_transaction",function(){

  if(module_name == "module_order")
  {   // Show panel right add product/service
      $("body").addClass("control-panel");
      $("body").addClass("open");
      $(".control-panel-container").css("display","block");
      $(".ha-underlay").css("display","block").show();
      $(".ha-underlay").css("z-index","10002");
       
  }
  else
  {
    $.magnificPopup.open({
        type: 'inline',
        closeOnContentClick: false,
        items: {
            src: '#choose_product_type'
        },
    });
  }
    
})

function changeProductType(type) {
    $.magnificPopup.close();

  
    var i_product = $("#data_table tr.active").find("input[for='dgrid-2']").val();
 
    $("#box_product [name='product_name']").val(i_product);
    $("#box_product [name='product_type']").val(type);
    $("#box_product [name='product_id']").val("");
    $("#box_product [name='product_price']").val(0);
    $("#box_product [name='product_price_original']").val(0);
    $("#box_product [name='product_price_sell']").val(0);

    $("#box_product #product_total").html(formatNumberInput(0));
    // init product_service type in select form
    if(module_name == "module_order" || module_name == "module_transaction")
    {
       $("#box_product select[name='product_service_type']").val(type).trigger("change");
      // $("#box_product select[name='product_service_type'] option[value='"+type+"'] ").prop("selected", true);
    }
    if(type == 1)
    {
        $("#box_product #display_product_cycle").show();

        $(".change_title_product").html(lang_service_add);
    }
    if(type == 0)
    {
        
        $("#box_product #display_product_cycle").hide();
        $(".change_title_product").html(lang_product_add);

    } 
    
    $(".box_control").html("<input class='btn act_popup_product_btn'  aclass='btn_add_product' type='button' value='"+lang_btn_add_save+"'/>");
       validate_form_custom("#edit_product_form",".act_popup_product_btn","box_custom");

    // REmove cac danh muc product khac danh muc đang add
    if($("#box_product select[name='product_group']").length > 0)
    {
          // $( "#box_product select[name='product_group'] option" ).each(function( index ) {
          //      if($(this).attr("type") != type)
          //      {
          //         $(this).remove();
          //      }
          // });
    }
    
    load_productgroup_byType(type,"#box_product select[name='product_group']");
    setTimeout(() => {
        loadProductCycleType();
        $.magnificPopup.open({
            type: 'inline',
            items: {
                src: '#box_product'
            },
        });
    }, 500)

}

$("#box_product select[name='product_service_type']").on("change",function(){
  var p_type = $(this).val();
  $("#box_product input[name='product_type']").val(p_type);
  
  load_productgroup_byType(p_type,"#box_product select[name='product_group']");

});

function load_productgroup_byType(type , elem) 
{
  loadProductCycleType();

  if(type == 0)
  {
    $("#box_product #display_product_cycle").hide();
  }
  else
  {
    $("#box_product #display_product_cycle").show();
  }
    $.ajax({
        type: "post",
        url: site_root_domain + "/?site=store_request&subact=load_productgroup_ptype",
        data: {p_type: type},
        success: function(html)
        {
            var obj = JSON.parse(html);

            $(elem).html(obj.data);

        }
    });
}


 function init_database()
 {

                $('#table_item').DataTable({
                 order: [],
              responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                   
                ],
                paging: false,
                  searching: false,
                  info: false
              });
 }
             
      
 function init_datatable()
 {



     var table_item = $('#table_item').DataTable({
          'columnDefs': [
             {
                'targets': [1, 2, 3, 4, 5],
                'render': function(data, type, row, meta){
                   if(type === 'display'){
                      var api = new $.fn.dataTable.Api(meta.settings);

                      var el = $('input, select, textarea', api.cell({ row: meta.row, column: meta.col }).node());

                      var _html = $(data).wrap('<div/>').parent();
 
                    //   $(  'input,select,textarea').attr('name', el.prop('nodeName')+'_'+el.prop('name'));
                      if(el.prop('tagName') === 'INPUT'){
                         $('input', _html).attr('value', el.val());
                         if(el.prop('checked')){
                            $('input', _html).attr('checked', 'checked');
                         }
                      } else if (el.prop('tagName') === 'TEXTAREA'){
                         $('textarea', _html).html(el.val());

                      } else if (el.prop('tagName') === 'SELECT'){
                         
                        // $('option:selected', _html).removeAttr('selected');
                         $('option', _html).filter(function(){
                            return ($(this).attr('value') === el.val());
                         }).attr('selected', 'selected');
                      }

                      data = _html.html();
                   }

                   return data;
                }
             }
          ],
          'responsive': true,
          order: [],
          paging: false,
                  searching: false,
                  info: false

       });


// var table_item = $('#table_item').DataTable({
//                  order: [],
//               responsive: true,
//                 columnDefs: [
//                     { responsivePriority: 1, targets: 0 },
//                     { responsivePriority: 2, targets: 1 },
                   
//                 ],
//                 paging: false,
//                   searching: false,
//                   info: false
//               });
       $('#table_item tbody').on('keyup change', '.child input, .child select, .child textarea', function(){
           var el = $(this);
           var rowIdx = el.closest('ul').data('dtr-index');
           var colIdx = el.closest('li').data('dtr-index');
           var cell = table_item.cell({ row: rowIdx, column: colIdx }).node();
 

        //   $('input, select, textarea', cell).val(el.val());
          $(cell).children().children().val(el.val());

           if(el.is(':selected')){ $ (cell).children().children().find("option[value='"+el.val()+"']").prop('selected', true); }

           calculate_money_subitem_product();
       });

     

 }


 function backup_datatable()
 {

 
     $("#table_item tr.child").each(function()
      {
            var _this = $(this);
            var ulli = _this.children().children().find("li");

            $(ulli).each(function(n,e)
            {
      
                 var subval = $(this).find("input,textarea,select").val();
                 var subfor = $(this).find("input,textarea,select").attr("for");
             
                 if (typeof subval !== typeof undefined && subval !== false) {  
                  
                 //     var subprev = _this.prev().find("input,textarea,select").filter("[for='"+subfor+"']")[0].val(subval);
                   
                      _this.prev().find("input").filter("[for='"+subfor+"']").attr("value",subval);
                     // var subprev = _this.prev().find("textarea").filter("[for='"+subfor+"']").attr("value",subval);
                      _this.prev().find("textarea").filter("[for='"+subfor+"']").html( subval);
                      _this.prev().find("select").filter("[for='"+subfor+"'] option[value='"+subval+"']").prop("selected", true);
 
                  }
                   


                           
            });


              // $(this).find(".number").html("#"+count_check);
              // $(this).find(".item_id").val(inc_item);
              // $(this).find(".del_row_invoid").attr("item_id",inc_item);
              // count_check ++;
              // inc_item ++;
              // if(count_check == count_del)
              // {
              //   $(this).attr("rowtr","last-row");
              // }
              
      });

 }
 
 function calculate_assets_warranty(number)
 {
        if(parseInt(number) > 999)
        {
          return false;  
        }
  var d = new Date();
  
  var year = d.getFullYear();
  var month = d.getMonth()+1;
  var day = d.getDate();
  
  
  
  var new_month = parseInt(month) + parseInt(number);
  if ( new_month > 12 )
  {
    var temp = Math.floor(new_month / 12);
    month = new_month - (temp * 12 );         
    year = year + temp;
  }
  else
  {
    month = new_month;  
  }
  
  var data = (day < 10 ? '0'+day : day) + '/' + (month < 10 ? '0'+month : month) + '/' + year;
    
  $("#ass_warranty").val(data);
  
  $(".row-grid.active").find("input[name='sub_warranty[]']").val(data); 
}

// Convert product name become to sku_code
function convert_sku_code(onthis)
{

  var p_val = $(onthis).val();
  p_val = p_val.replace(/\s+/g, '');
  p_val = showUnsignedString(p_val);
   
 $(onthis).closest("tr").find("input[name='sub_product_sku[]']").val(p_val.toLowerCase());
}

function showUnsignedString(input) {
    var signedChars     = "àảãáạăằẳẵắặâầẩẫấậđèẻẽéẹêềểễếệìỉĩíịòỏõóọôồổỗốộơờởỡớợùủũúụưừửữứựỳỷỹýỵÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬĐÈẺẼÉẸÊỀỂỄẾỆÌỈĨÍỊÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢÙỦŨÚỤƯỪỬỮỨỰỲỶỸÝỴ";
    var unsignedChars   = "aaaaaaaaaaaaaaaaadeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuyyyyyAAAAAAAAAAAAAAAAADEEEEEEEEEEEIIIIIOOOOOOOOOOOOOOOOOUUUUUUUUUUUYYYYY";
 
    var pattern = new RegExp("[" + signedChars + "]", "g");
    var output = input.replace(pattern, function (m, key, value) {
        return unsignedChars.charAt(signedChars.indexOf(m));
    });
    return output;
}

        function change_type_subid(obj) 
        {
          
            var request_subtype = 1;
            var sub_id = $(obj).val();
          
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=store_request&subact=change_request_subtype",
                data: {request_subtype: request_subtype, sub_id: sub_id},
                success: function(html)
                {
                    
                    $("#content_change").html(html);

                }
            });
        }

        function add_new_customer(parent_el) 
        {  

           $(parent_el).on("click",".add_new_cus", function(e){
          
                e.stopPropagation();
                var val_return = $(this).parent().attr("checkreturn");
                $(".title_change_cus").html(lang_cus_add);
                $(".change_action_cus").html("<input class='btn btn_add_cus' type='button' value='"+lang_cus_add+"'/>");
                if(typeof(checkreturn) == undefined || val_return == "")
                {
                    $("form#form_customer input[name='checkReturn']").val("");
                }else
                {
                    $("form#form_customer input[name='checkReturn']").val(val_return);
                }
                $(".error").html('');
                // $("form#form_customer")[0].reset();
               clearForm($("form#form_customer"));

                $.magnificPopup.open({
                  type: 'inline',
                  items: {
                    src: '#box_customer'
                  }
                });
           });
        }

        function add_do_new_customer(parent_el, content_el="#content_change")
        {

           $(parent_el).on("click",".btn_add_cus", function(e){

               let btn = $(this);

              if($("form#form_customer").validnew())
              {
                checkDoubleClick(btn, 1);
                var data = $("form#form_customer").serialize();

                $.ajax({
                    type: "post",
                    url: site_root_domain + "/?site=customer&act=add&subact=quickadd",
                    data: data,
                    success: function(html)
                    {
                        checkDoubleClick(btn, 0);
                        csrf_token();
                        var obj = JSON.parse(html);

                        if(obj.status == "error")
                        {
                            new PNotify({
                                text: obj.msg,
                                hide: true,
                                delay: 3000,
                                remove: true,
                                type: 'error'
                            });

                          /*$("form#form_customer .error_msg").html(obj.msg);
                          $("form#form_customer .error_msg").show();
                          setTimeout(function(){ $("form#form_customer .error_msg").show();}, 3000);*/
                        }else
                        {
                          action_customer(obj.data.cus_id);
                          alertText(obj.msg, "success");

                          if(content_el == '#form-signin_v1' || content_el == '#transactionForm')
                          {
                              $(content_el + " #trx_cus").val(obj.data.cus_full_name);
                              $(content_el + " #trx_email").val(obj.data.cus_email);
                              $(content_el + " #trx_address").val(obj.data.cus_address);
                              $(content_el + " #cus_id").val(obj.data.cus_id);
                              $(content_el + '#trx_cus_ajax').hide();
                              $('#item_line > thead > tr > td > button').prop('disabled', false);
                              $('#item_line > tbody').empty();
                              $("#item_line > tfoot > tr > td:nth-last-child(2) > input" ).val('0');
                          }
                          else if(content_el == '#formorder-signin_v1') // show cus infor after add new customer - module order
                          { 
                      
                              $(content_el + " #cus_name_display").html(obj.data.cus_full_name);
                              $(content_el + " #cus_email").html(obj.data.cus_email);
                              $(content_el + " #cus_address").html(obj.data.cus_address);
                              $(content_el + " #cus_phone").html(obj.data.cus_id);
                              $(content_el + ' .cus_show_info').show();
                              $(content_el + " input[name='cus_name']").val(obj.data.cus_full_name);
                              $(content_el + " #cus_id").val(obj.data.cus_id);
                              
                          }
                          else if(content_el == '#formsite-signin_v1') // show cus infor after add new customer - module order
                          { 
                   
                              $(content_el + " #firstname").val(obj.data.cus_full_name);
                              $(content_el + " #email").val(obj.data.cus_email);
                              $(content_el + " #address").val(obj.data.cus_address);
                              $(content_el + " #phone").val(obj.data.cus_id);
                              $(content_el + " input[name='cus_name']").val(obj.data.cus_full_name);
                              $(content_el + " #cus_id").val(obj.data.cus_id);
                              
                          }
                          else
                          {
                              $(content_el + " input[name='cus_name']").val(obj.data.cus_full_name);
                              $(content_el + " #cus_id").val(obj.data.cus_id);
                          }

                           $.magnificPopup.close();
                        }
                    }
                });

              }// End if
           });
        }

        function call_form_add_supplier(e) 
        {
          // $("form#add_supplier_form")[0].reset();
          clearForm($("form#add_supplier_form"));
          $(".change_title").html(lang_supplier_add);
          $(".change_action").html("<input class='btn act_popup_btn_validate' aclass='btn_add_supplier' type='button' value='"+lang_supplier_add+"'/>");
          validate_form_custom("#add_supplier_form",".act_popup_btn_validate","box_custom");
          e.stopPropagation();
          var val_return = $(this).parent().attr("checkreturn");

          if(typeof(val_return) == "undefined" || val_return == "")
          {
              val_return = $(e.currentTarget).parent().attr("checkreturn");

              if(typeof(val_return) == "undefined" || val_return == "")
              {
                  $("input[name='checkReturn']").val("");
              }
              else
              {
                  $("input[name='checkReturn']").val(val_return);
              }
          }else
          {
              $("input[name='checkReturn']").val(val_return);
          }
          $.magnificPopup.open({
            type: 'inline',
            items: {
              src: '#box_add_supplier'
            },
          });
        }

        function change_supplier_action(obj) 
        {
            var sup_id = $(obj).val();
            var actfor = $(obj).attr("for");

            if(actfor == "change")
            {
                $(".select_supplier option[value='"+sup_id+"']").prop("selected", true);
            }

            if(obj.parents("form:first").hasClass("transactionForm")) //Danh cho transaction
            {
                let frm = obj.parents("form:first");
                let email = frm.find("[name='trx_email']");
                let address = frm.find("[name='trx_address']");

                email.val(obj.find(`option[value='${sup_id}']`).attr("email"));
                address.val(obj.find(`option[value='${sup_id}']`).attr("address"));

                if(obj.attr("sub") == 2 || obj.attr("sub") == 7)
                {
                    getUnbilledByCustomer(sup_id, obj.attr("sub"), 2);
                }

                // return true;
            }

            if(sup_id)
            {
              if(pms['supplier_edit'] == 1)
              {
                if(actfor == "change")
                {
                  $(".box_edit_supplier").html("<a id='"+sup_id+"' class='btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                }else
                {
                  $("#box_product .box_edit_supplier").html("<a id='"+sup_id+"' class='btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                }
              }
            }else
            {
                $(".box_edit_supplier").html("");
            }
        }

        function action_customer(cus_id) 
        {
            if(pms['customer_edit'] == 1)
            {
              $(".box_edit_cus").html("<a id='"+cus_id+"' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
            }
        }

        function action_customer_morder(cus_id, cus_email, cus_full_name, cus_address, cus_phone, cus_first_name, cus_last_name) 
        {
            if(pms['customer_edit'] == 1)
            {
              if( cus_id )
              {
                $(".box_edit_cus").html("<a id='"+cus_id+"' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
              }
              else
              {
                $(".box_edit_cus").html('');
              }
            }

            $(".cus_show_info #cus_name_display").html( cus_full_name);
            $(".cus_show_info #cus_email").html( cus_email);
            $(".cus_show_info #cus_address").html( cus_address);
            $(".cus_show_info #cus_phone").html( cus_phone);
            $(".cus_show_info").show();

            // Use for theme merchant new
            if($("#apply_info").length > 0)
            {
              if( cus_id )
              {
                $("#apply_info").show();
                $("#apply_info .cus_full_name").text( cus_first_name+' '+cus_last_name);
                $("#apply_info .cus_phone").text( cus_phone);
                $("#apply_info .cus_email").text( cus_email);

                // Apply list address billing, shipping by cus
                getaddressbycus(cus_id, "[name='billing_address'], [name='shipping_address']");
              }
              else
              {
                $("#apply_info").hide();
                $("#apply_info .cus_full_name").text('');
                $("#apply_info .cus_phone").text('');
                $("#apply_info .cus_email").text('');

                // Apply list address billing, shipping by cus
                getaddressbycus(cus_id, "[name='billing_address'], [name='shipping_address']");
              }
            }
        }


function action_customer_msites(cus_id, cus_email, cus_full_name, cus_address, cus_phone) 
{
   if(pms['customer_edit'] == 1)
    {
      $(".box_edit_cus").html("<a id='"+cus_id+"' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
    }
    $("#firstname").val( cus_full_name);
    $("#email").val( cus_email);
    $("#address").val( cus_address);
    $("#phone").val( cus_phone);
   
}
function action_customer_trx(cus_id, cus_email, cus_full_name, cus_address, cus_phone, obj)
{
    if(pms['customer_edit'] == 1)
    {
        $(".box_edit_cus").html("<a id='"+cus_id+"' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
    }

    var frm = obj.parents("form:first");
    frm.find('#trx_cus').val(cus_full_name);
    frm.find('#trx_email').val(cus_email);
    frm.find('#trx_address').val(cus_address);
    frm.find('#cus_id').val(cus_id);
    action_customer(cus_id);
    // $('#trx_cus_ajax').hide();
    $('#item_line > thead > tr > td > button').prop('disabled', false);
    $('#item_line > tbody').empty();
    $("#item_line > tfoot > tr > td:nth-last-child(2) > input" ).val('0');

    var sub = obj.attr("sub");

    if(sub == 2 || sub == 7)
    {
        getUnbilledByCustomer(cus_id, sub, 1);
    }
}

function action_user_trx(user_id, user_email, user_display_name, user_address, obj)
{
    var frm = obj.parents("form:first");

    frm.find("[name='user_name_assign']").val(user_display_name);
    frm.find('#trx_email').val(user_email);
    frm.find('#trx_address').val(user_address);
    frm.find("[name='user_assign']").val(user_id);

    // $('#trx_cus_ajax').hide();
    $('#item_line > thead > tr > td > button').prop('disabled', false);
    $('#item_line > tbody').empty();
    $("#item_line > tfoot > tr > td:nth-last-child(2) > input" ).val('0');

    var sub = obj.attr("sub");

    if(sub == 2 || sub == 7)
    {
        getUnbilledByCustomer(user_id, sub, 3);
    }
}


function action_term_trx(selector, ui)
{
    $(selector).val(ui.item.value);
    $(selector).parents("form:first").find("#trx_terms").val(ui.item.id).trigger("change");
    if(pms['transaction_terms_edit'] == 1)
    {
        $(".box_edit_term").html("<a onclick='return edit_term(" + ui.item.key + ")' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
    }
    return false;
}

        function call_form_edit_customer(obj) 
        {
            waitingDialog.show(cms_lang.waiting_dialog_msg);
            // checkDoubleClick(".btn_reset_attr",0);
            $(".title_change_cus").html(lang_cus_edit);
            $(".change_action_cus").html("<input class='btn edit_cus_do' type='button' value='"+lang_cus_edit+"'/>");
            $("#form_customer .error_msg").hide();
            $("#form_customer .error_msg").html('');
            var id = $(obj).attr("id");
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=customer&subact=ajax_data_cus",
                data: {cus_id: id},
                success: function(html)
                {
                  setTimeout(function() {
                   // your code
                    waitingDialog.hide();
                    
                    var obj = JSON.parse(html);
                    if(obj.status == "error")
                    {
                      alertText(obj.msg, "error");
                    }else
                    {
                      $("form#form_customer input[name='cus_full_name']").val(obj.data.cus_full_name);
                      $("form#form_customer input[name='cus_address']").val(obj.data.cus_address);
                      $("form#form_customer input[name='cus_phone']").val(obj.data.cus_phone);
                      $("form#form_customer input[name='cus_email']").val(obj.data.cus_email);
                      $("form#form_customer input[name='cus_email_invoice']").val(obj.data.cus_email_invoice);
                      $("form#form_customer input[name='cus_id']").val(obj.data.cus_id);
                      $.magnificPopup.open({
                        type: 'inline',
                        items: {
                          src: '#box_customer'
                        },
                      });
                    }
                  }, 1000);

                   
                   
                    
                }
            });
        }

        function edit_do_customer(parent_el, content_el = '#content_change')
        {

           $(parent_el).on("click",".edit_cus_do", function(e){

              if($("form#form_customer").validnew())
              {
                checkDoubleClick($(this), 1);
                var data = $("form#form_customer").serialize();

                $.ajax({
                    type: "post",
                    url: site_root_domain + "/?site=customer&act=edit&subact=quickedit",
                    data: data,
                    success: function(html)
                    {
                        csrf_token();

                        var obj = JSON.parse(html);
                        if(obj.status == "error")
                        {
                            pNotifyACP(obj.msg);
                          /*$("form#form_customer .error_msg").html(obj.msg);
                          $("form#form_customer .error_msg").show();
                          setTimeout(function(){ $("form#form_customer .error_msg").show();}, 3000);*/
                        }else
                        {
                          action_customer(obj.data.cus_id);
                          alertText(obj.msg, "success");
                            if(content_el == '#form-signin_v1' || content_el == '#transactionForm')
                            {
                                $(content_el + " #trx_cus").val(obj.data.cus_full_name);
                                $(content_el + " #trx_email").val(obj.data.cus_email);
                                $(content_el + " #trx_address").val(obj.data.cus_address);
                                $(content_el + " #cus_id").val(obj.data.cus_id);
                                $(content_el + '#trx_cus_ajax').hide();

                                if(content_el != '#transactionForm')
                                {
                                    $('#item_line > thead > tr > td > button').prop('disabled', false);
                                    $('#item_line > tbody').empty();
                                    $("#item_line > tfoot > tr > td:nth-last-child(2) > input" ).val('0');
                                }
                            }
                            else if(content_el == '#formorder-signin_v1') // show cus infor after add new customer - module order
                            { 
                        
                                $(content_el + " #cus_name_display").html(obj.data.cus_full_name);
                                $(content_el + " #cus_email").html(obj.data.cus_email);
                                $(content_el + " #cus_address").html(obj.data.cus_address);
                                $(content_el + " #cus_phone").html(obj.data.cus_id);
                                $(content_el + ' .cus_show_info').show();
                                $(content_el + " input[name='cus_name']").val(obj.data.cus_full_name);
                                $(content_el + " #cus_id").val(obj.data.cus_id);
                                
                            }else if(content_el == '#formsite-signin_v1') // show cus infor after add new customer - module order
                            { 
                        
                                $(content_el + " #firstname").val(obj.data.cus_full_name);
                                $(content_el + " #email").val(obj.data.cus_email);
                                $(content_el + " #address").val(obj.data.cus_address);
                                $(content_el + " #phone").val(obj.data.cus_id);
                                $(content_el + " input[name='cus_name']").val(obj.data.cus_full_name);
                                $(content_el + " #cus_id").val(obj.data.cus_id);
                                
                            }
                            else
                            {
                                $(content_el + " input[name='cus_name']").val(obj.data.cus_full_name);
                                $(content_el + " #cus_id").val(obj.data.cus_id);
                            }

                           $.magnificPopup.close();
                        }
                    }
                });

              }// End if
           });
        }

function loadProductCycleType(productType = $("#edit_product_form .product_type").val(), selectObj = $("#edit_product_form select#product_cycle"))
{
    productType = parseInt(productType);

    let pType = {
        0: [{'id' : 0, 'name': cms_lang.p_cycle_00}],
        1: [{'id' : 0, 'name': cms_lang.p_cycle_00},{'id' : 1, 'name': cms_lang.p_cycle_01}, {'id' : 2, 'name': cms_lang.p_cycle_02}],
    };

    let option = "";

    if(pType[productType])
    {
        $.each(pType[productType], (k,v) => {
            option += `<option value="${v.id}">${v.name}</option>`;
        })
    }


    selectObj.html(option);
}

 

function loadProductCycle(cycleType, selectObj)
{
    cycleType = parseInt(cycleType);

    let pTypeDetail = {
        0: [{'id' : 1, 'name': cms_lang.p_cycle_00}],
        1: [
            {'id' : 1, 'name': `1 ${cms_lang.month}`},
            {'id' : 2, 'name': `2 ${cms_lang.month}`},
            {'id' : 3, 'name': `3 ${cms_lang.month}`},
            {'id' : 4, 'name': `4 ${cms_lang.month}`},
            {'id' : 5, 'name': `5 ${cms_lang.month}`},
            {'id' : 6, 'name': `6 ${cms_lang.month}`},
            {'id' : 7, 'name': `7 ${cms_lang.month}`},
            {'id' : 8, 'name': `8 ${cms_lang.month}`},
            {'id' : 9, 'name': `9 ${cms_lang.month}`},
            {'id' : 10, 'name': `10 ${cms_lang.month}`},
            {'id' : 11, 'name': `11 ${cms_lang.month}`},
            {'id' : 11, 'name': `12 ${cms_lang.month}`},
        ],
        2: [
            {'id' : 1, 'name': `1 ${cms_lang.year}`},
            {'id' : 2, 'name': `2 ${cms_lang.year}`},
            {'id' : 3, 'name': `3 ${cms_lang.year}`},
            {'id' : 4, 'name': `4 ${cms_lang.year}`},
            {'id' : 5, 'name': `5 ${cms_lang.year}`},
            {'id' : 6, 'name': `6 ${cms_lang.year}`},
            {'id' : 7, 'name': `7 ${cms_lang.year}`},
            {'id' : 8, 'name': `8 ${cms_lang.year}`},
            {'id' : 9, 'name': `9 ${cms_lang.year}`},
            {'id' : 10, 'name': `10 ${cms_lang.year}`},
            {'id' : 11, 'name': `11 ${cms_lang.year}`},
            {'id' : 11, 'name': `12 ${cms_lang.year}`},
        ],
    };

    let option = "";

    $.each(pTypeDetail[cycleType], (k,v) => {
        option += `<option value="${v.id}">${v.name}</option>`;
    })

    selectObj.html(option);
}

  function change_type_store(obj) 
  {
        var store_id = $(obj).val();

        var request_subtype = $("input[name='request_subtype']").val();
        // $("#request_subtype").attr("invalid_store_id", store_id);
        if(request_subtype == 2)
        {
            // $("#request_subtype").trigger("change");
            // var request_subtype = $("input[name='request_subtype']").val();
            var sub_id = $("input[name='sub_id']").val();
            var invalid_store_id = $(obj).val();

            $.ajax({
            type: "post",
            url: site_root_domain + "/?site=store_request&subact=change_request_subtype",
            data: {request_subtype: request_subtype, sub_id: sub_id, invalid_store_id: invalid_store_id},
            success: function(html)
            {
        
                $("#content_change").html(html);

            }
        });
        }

        // reset table
        $(".btn_del_line_asset").trigger("click");
  }


    function cus_assign_me(obj, el_assign_name, el_assign_id, el_assign_email="", el_assign_address="")
    {
        var name_assign = $(obj).attr("val_name");
        var id_assign = $(obj).attr("val_id");
        var email_assign = $(obj).attr("val_email");
        var address_assign = $(obj).attr("val_address");

        $("input[name='"+el_assign_name+"']").val(name_assign);
        $("input[name='"+el_assign_id+"']").val(id_assign);
        $("input[name='"+el_assign_email+"']").val(email_assign);
        $("input[name='"+el_assign_address+"']").val(address_assign);

        if($(obj).attr("sub") == 2)
        {
            getUnbilledByCustomer(id_assign, 2, 3);
        }

    }

    function call_check_form_return() 
    {
        var count = 0;
        var check1 = -1;
        var check2 = -1;
        
        if($(".grid-td-asset .ass_id").length > 0)
        {
          check2 = 0;
          $(".grid-td-asset .ass_id").each(function(){
              var id = $(this).val();
              if(id)
              {
                  check2 ++;
                  count ++;
              }
          });

        }

        if(count == 0)
        {
            if(check2 == 0)
            {
              call_notify(cms_lang['gnotice'], cms_lang['gnotice_select_asset'], "danger", ""); 
              return false;
            }
        }else
        {
          return true;
        }
    }


    function validate_form_custom2(el_form, callback, el_btn_check="[type='submit']")
    {
        $(el_form).validate({
            submit: {
                settings: {
                    button: el_btn_check,
                    inputContainer: '.form-group',
                    errorListClass: 'form-tooltip-error',

                },
                callback: {
                    onSubmit: function (node, formdata, obj)
                    {
                        callback();
                    },// End on before submit
                    onError: function (node, globalError) {

                        //$(".error").focus();
                        var container = $("html,body");
                        var scrollTo = $('.error:eq(0)');

                        container.animate({scrollTop: scrollTo.offset().top - container.offset().top - 100, scrollLeft: 0},20);
                    }
                }
            }
        });
    }

    function validate_form_custom(el_form, el_btn_check="[type='submit']", type)
    {
        $(el_form).validate({
            submit: {
                settings: {
                    button: el_btn_check,
                    inputContainer: '.form-group',
                    errorListClass: 'form-tooltip-error',

                },
                callback: {
                    onSubmit: function (node, formdata, obj) 
                    {
                      if(!type)
                      {
                          node[0].submit();
                      }else
                      {
                       
                            var count = -1;
                          // Custom ham theo type
                          // Mỗi hàm check phai return true hoac false
                            if(type=="returns")
                            {
                                check = call_check_form_return();
                                if(check == true)
                                {
                                    count = 1;
                                }
                            }else if(type=="store_request")
                            {
                                check = call_check_form_request();
                                if(check == true)
                                {
                                    count = 1;
                                }
                            }else if(type=="box_custom")
                            {
                                var cl = obj.attr("aclass");
                                $("."+cl).trigger("click");
                            }else if(type=="send_email")
                            {
                                tinyMCE.triggerSave();
                                sendEmailTrx();
                            }else if(type == "renew_order")
                            {  
                              swal({
                                title: cms_lang['gnotice'],
                                 text: cms_lang['gnotice_confirm_action'],
                                 type: "warning",
                                 showCancelButton: true,
                                 confirmButtonClass: 'btn-primary',
                                 cancelButtonClass: 'btn-danger',
                                 cancelButtonText: cms_lang['gnotice_cancel'],
                                 confirmButtonText: cms_lang['gnotice_ok'],                                           
                               }).then(function () {
                                  
                                  node[0].submit();
                              })
                            }
                            else if(type=="approve_request")
                            {
                              
                                check = call_check_form_request();
                                if(check == true)
                                {
                                  var title_alert = obj.attr("title_alert");
                                  var is_enough = obj.attr("val");
                                  var checkclick = obj.attr('checkclick');
                                  $("input[name='enough_goods']").val(is_enough);
                                  if(checkclick == 1)
                                  {
                                        swal({
                                         title: '',
                                           text: title_alert,
                                           type: "warning",
                                           showCancelButton: true,
                                           confirmButtonClass: "btn-danger",
                                           confirmButtonText: cms_lang['gnotice_ok'],
                                           cancelButtonText: cms_lang['gnotice_cancel'],
                                           closeOnConfirm: true,
                                           closeOnCancel: true
                                       }).then(function () {
                                            node[0].submit();
                                           
                                        });
                                        
                                  }
                                  
                                  
                                    
                                }
                            }else if(type == "site_change_domain")
                            {
                                  alert_confirm_overwrite(node);
                               
                              
                            }else if(type == "gallery")
                            {
                                submitGallery($(el_form));
                            }else if(type == "gallery_form")
                            {
                              waitingDialog.show(cms_lang.waiting_dialog_msg);
                              node[0].submit();
                            }
                            else if(type == "attachFiles")
                            {
                                attachFilesTrx($(el_form));
                                node[0].submit();
                            }else if(type=="giftcard")
                            {
                              $("#ufile_output_b64").val('');
                                //node[0].submit();
                                submitgiftcard($(el_form));
                            }else if(type=="giftcard_ibe")
                            {
                              $("#ufile_output_b64").val('');
                                //node[0].submit();
                                submitgiftcard_ibe($(el_form));
                            }else if(type=="coupon")
                            {
                              //$("#ufile_output_b64").val('');
                                 submitcoupon($(el_form));
                            } 
                            else if(type=="logos")
                            {
                              //$("#ufile_output_b64").val('');
                                 submitlogos($(el_form));
                                 setTimeout(function(){ node[0].submit(); }, 2000);
                                 
                            }else if(type=="check_order")
                            {
                                //submitorder($(el_form));
                                node[0].submit();
                                 
                            } else if(type == "product_quick_action")
                            {
                                productQuickAction();
                                return;
                            }else if( type == "add_shipping_fee" )
                            {
                              $("#multiselect_to").find("option").prop("selected", true);
                              count = 1;
                            }

                            // Check count == 1 thi submit
                            if(count == 1)
                            {
                              // return false;
                              node[0].submit();
                            }


                      }// End ! type

                    },// End on before submit
                    onError: function (node, globalError) {
                     
                        //$(".error").focus();
                        var container = $("html,body");
                        var scrollTo = $('.error:eq(0)');

                        // trigger tabs if exist
                        var tab = scrollTo.closest('.tab-pane');
                        if( tab.length > 0 )
                        {
                          $('.tabs-section [data-toggle="tab"][href="#'+tab.attr('id')+'"]').trigger('click');
                        }
                        
                        container.animate({scrollTop: scrollTo.offset().top - container.offset().top - 100, scrollLeft: 0},20); 
                    }
                }
            }
        });
    }

    // Upgrade package
    $(".upgrade_package").click(function(e){
        e.stopPropagation();
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: '#box_upgrade_package'
          }
        });
    });

   function alert_confirm_overwrite( node )
   {
 
    var html_confirm_sendsms = '';
    if(is_web_us == "1")
    {
      html_confirm_sendsms =   '<input type="checkbox"  id="chk_sendsms" value="1"> <label for="chk_sendsms">'+cms_lang['chk_sendsms_tocusomer']+'</label>'
    }
    swal({
         title: cms_lang['gnotice'],
      type: 'warning',
      html:
         '<span></span>' +
        '<div class="checkbox">' +
        '<input type="checkbox"  id="chk_overwrite_config" value="1"> ' +
        ' <label for="chk_overwrite_config">'+cms_lang['chk_overwrite_config']+'</label>'+
        html_confirm_sendsms +
        '</div>' ,
           showCancelButton: true,
           confirmButtonClass: 'btn-primary',
         cancelButtonClass: 'btn-danger',
           cancelButtonText: cms_lang['gnotice_cancel'],
           confirmButtonText: cms_lang['gnotice_ok'],
     }).then(function () {
        
        if ($("#chk_overwrite_config").prop( "checked" ) ) 
        {
            $("#overwrite_config").val(1);
        }
        else
        {
            $("#overwrite_config").val(0);
        }


        if ($("#chk_sendsms").prop( "checked" ) ) 
        {
            $("#overwrite_sendsms").val(1);
        }
        else
        {
            $("#overwrite_sendsms").val(0);
        }
        
         
       node[0].submit();
         
      })

   }

    function call_check_form_request()
    {
        // var check_action = $(obj).attr("page_type");
        // console.log(check_action);return false;
        // if(typeof(check_action) != "undefined" || check_action != null)
        // {
        //     $("input[name='page_type']").val(check_action);
        // }

        // Check in form
        // if($("form.form_request").validnew())
        // {
            var count = 0;
            var check1 = -1;
            var check2 = -1;
            if($(".grid-td .product_id").length > 0)
            {
              check1 = 0;
              $(".grid-td .product_id").each(function(){
                  var id = $(this).val();
                  if(id)
                  {
                      check1 ++;
                      count ++;
                  }
              });
            }
            if($(".grid-td-asset .ass_id").length > 0)
            {
              check2 = 0;
              $(".grid-td-asset .ass_id").each(function(){
                  var id = $(this).val();
                  if(id)
                  {
                      check2 ++;
                      count ++;
                  }
              });
              
            }

 
            if(count == 0)
            {
                if(check1 == 0)
                {
                  call_notify(cms_lang['gnotice'], cms_lang['gnotice_select_product'], "warning", "");
                  // alertText(cms_lang['gnotice_select_product'],"warning"); 
                }

                if(check2 == 0)
                {
                  call_notify(cms_lang['gnotice'], cms_lang['gnotice_select_asset'], "warning", "");
                  // alertText(cms_lang['gnotice_select_asset'],"warning"); 
                }

                return false;
            }else{
                return true;
                // $("form.form_request").submit();
            }
        // }else
        // {
        //     valid_request.focusInvalid();
        //     return false;
        // }
    }

                 
    function drawVisualization_new(data_chart, title_unit, id_el="chart_show", type_chart = "line",header = "",top=0,left=0,width=0,height=0,legend=0) 
    {
 
      data_chart = JSON.parse(data_chart);
 
        var data = google.visualization.arrayToDataTable(data_chart);
        
        var options = {
            legend: (type_chart == "pie" || legend==1) ? {position: 'right', textStyle: {color: 'blue', fontSize: 10}} : 'none',
            title: header,
            titleTextStyle: {fontSize: 14,},
            // tooltip: { trigger: 'none' },
            enableInteractivity: true,
            vAxis: {
                textStyle: {
                    color: '#919fa9',
                    fontName: 'Proxima Nova',
                    fontSize: 11
                },
                baselineColor: '#eff1f2',
                // ticks: [0,2500000,5000000,75000000,100000000,250000000,1500000000],
                title: title_unit,
                gridlines: {
                    color: '#eff1f2',
                    count: 7
                }
            },
            hAxis: {
                textStyle: {
                    color: '#919fa9',
                    fontName: 'Proxima Nova',
                    fontSize: 11
                }
            },
            chartArea:{
                left: left ? left : 70,
                top: top ? top : 10,
                width: width ? width : '90%',
                height: height ? height : 150
            },
            lineWidth: 2,
            seriesType: type_chart,
            series: {
                0: { 
                    type: type_chart, 
                    color: '#ac6bec', 
                    pointSize: 4,
                    pointShapeType: 'circle' },
                1: {
                    type: type_chart,
                    color: '#00a8ff',
                    pointSize: 4,
                    pointShapeType: 'circle'
                },
                2: {
                    type: type_chart,
                    color: '#46c35f',
                    pointSize: 4,
                    pointShapeType: 'circle'
                }
            }
        };

        if(type_chart == "pie")
        {
            var chart = new google.visualization.PieChart(document.getElementById(id_el));
        }
        else
        {
            var chart = new google.visualization.ComboChart(document.getElementById(id_el));
        }
        
        chart.draw(data, options);
    }


    function refreshForm(obj)
    {
        obj.find(":text,select,textarea,:checkbox,:radio").val("");
        obj.submit();
    }

    function previewTrxPopup(trxId=0,filePath='')
    {
        $('#previewTrxPopupLoading').show();
        tinymce.triggerSave();

        let content = tinymce.get('invoice_content').getContent();

        if(trxId)
        {
            $("#box_preview_model #trx_id").val(trxId);
        }

        trxId = trxId ? trxId : $("#box_preview_model #trx_id").val();

        $.ajax({
            type: 'post',
            url: site_root_domain + '/?site=transactions&subact=export_pdf',
            data: {
                'content': content,
                'type': 1,
                'trx_id': trxId,
                'file' : filePath,
            },
            dataType: 'json',
            success: function (res){
                if(res.status == 'ok')
                {
                    $("#box_compose_invoice,#getExportFiles").modal('hide');
                    $('#previewTrxPopupLoading').hide();
                    $("#box_preview_model #tab_box_review").find('iframe').attr('src',res.fileUrl);

                    //Set value send email tab
                    let emailTab = $("#box_preview_model #tab_send_email");
                    emailTab.find('[name="email_from"]').val(res.email.email_from);
                    emailTab.find('[name="email_to"]').val(res.email.email_to);
                    emailTab.find('[name="email_title"]').val(res.email.email_title);
                    tinymce.get('send_file_email_content').setContent(res.email.email_content);

                    if($("#box_preview_model").find('input[name=backTo]').length)
                    {
                        if(filePath)
                        {
                            $("#box_preview_model").find('input[name=backTo]').val('getExportFiles');
                        }
                        else
                        {
                            $("#box_preview_model").find('input[name=backTo]').val('box_compose_invoice');
                        }
                    }

                    $("#box_preview_model").modal();
                    setModalOpen();
                }
                else
                {
                    call_notify("Notification", res.msg, "danger", "");
                }
            }
        })
    }

    function composeTrx(url, sub, trxId=0)
    {
        $("#box_compose_invoice").modal();
        $("#box_compose_invoice").attr('ref', url);
        $("#box_compose_invoice").find('.nav-link').removeClass('active');
        $("#box_compose_invoice").find('.nav-link:first').addClass('active');
        // $('.modal-content').css('height',$( window ).height()*0.9);
        // $('.modal-content').css('overflow','auto');
        $('#box_preview_model #trx_id').val(trxId);
        $('#box_preview_model #preview_type').val('default');

        tinymce.get('invoice_content').setContent("Loading ...");

        if(sub == '1')
        {
            $('#box_compose_invoice #previewTabs').show();
        }
        else
        {
            $('#box_compose_invoice #previewTabs').hide();
        }

        $.ajax({
            url: url,
            type: 'post',
            success: function(res){
                tinymce.get('invoice_content').setContent(res);
            }
        })
    }

    function backToPreviewTrxPopup()
    {
        $("#box_preview_model").modal('hide');
        if($("#box_preview_model").find("input[name='backTo']:first").length)
        {
            let backToVal = $("#box_preview_model").find("input[name='backTo']:first").val() ? $("#box_preview_model").find("input[name='backTo']:first").val() : 'box_compose_invoice';
            $("#"+backToVal).modal('show');
            setModalOpen();
        }
    }

    function changeComposeTrxMode(mode='')
    {
        $("#box_compose_invoice").modal();

        tinymce.get('invoice_content').setContent("Loading ...");
        $('#box_preview_model #preview_type').val(mode);

        $.ajax({
            url: $("#box_compose_invoice").attr('ref'),
            data:  {
                'preview_type': mode
            },
            type: 'post',
            success: function(res){
                tinymce.get('invoice_content').setContent(res);
            }
        })
    }

    function setModalOpen()
    {
        let setModalOpen = setInterval(function(){
            if($('body').hasClass("modal-open"))
            {
                console.log("clear modal-open interval")
                clearInterval(setModalOpen);
            }
            else
            {
                console.log("set modal-open")
                $('body').addClass("modal-open");
            }
        },500)
    }

    function changePreviewType(type)
    {
        let src = $("#box_preview_invoice").find("iframe").attr("src");

        src = src.replace('&preview_type=commercial', '');

        if(type == 'commercial')
        {
            src += '&preview_type=commercial';
        }

        $("#box_preview_invoice").find("iframe").attr("src", src);
    }

    function printTrxPopup()
    {
        let src = $('#box_preview_model #tab_box_review').find('iframe:first').attr('src');
        window.open(src, '_blank');
    }

    function clearForm(frm) {
        frm.find(":text, textarea").val("");
        frm.find("checkbox").prop("checked", false);
        frm.find("select").val(function(){
          return $(this).find('option:first').attr('value');
        }).trigger('change');
    }



$(".multiple_upload").change(function() {
    // $("#ufile_output ul").empty();
        if($(".list_image").length)
        {
          $(".list_image").html("");
        }
        var ele = document.getElementById($(this).attr('id'));
        var result = ele.files;
        let max_size = 5;

        for(var x = 0;x< result.length;x++)
        {
          var fle = result[x];

          let fileExt = fle.name.split('.').pop();

          fileExt = fileExt.toLowerCase();

          $(".box_list_image").css("display","block");

          if(typeof acceptedAttachFiles != "undefined")
          {
              acceptedAttachFilesDecode = json_decode(acceptedAttachFiles);

              if(acceptedAttachFilesDecode.indexOf(fileExt) == -1)
              {
                  call_notify(cms_lang['gnotice'], result[x].name + " "+cms_lang['ginvalid'], "warning", "");
                  continue;
              }

              if(typeof maxSizeAttachFiles != "undefined")
              {
                  max_size = parseFloat(maxSizeAttachFiles);
              }
          }

          var size_per = 1024*1024*max_size;// 749651

          if(result[x].size <= size_per)
          {
            $(".title_upload").show();
            readFileMulti(fle, x);
          }else
          {
            call_notify("Notification", result[x].name + ": Upload file is too large. You can upload file " + max_size + "MB", "warning", "");
          }
        }

        // For gallery
        if($(".list_image").length)
        {
          $(".num_files").html(" ( " +result.length + " image )");
          setTimeout(function(){
            $(".list_image").find("button.uploading-list-item-close").remove();  
          }, 200);
        }

});

function delFile(obj, check_type=0)
{
  if(check_type)
  {
      var gali_id = $(obj).attr("id");
      $.ajax({
          type: "post",
          url: site_root_domain + "/?site=gallery&subact=delImg",
          data: {gali_id: gali_id},
          success: function(html)
          {
 
          }
      });
  }
  $(obj).parent().remove();
  if($(".list_image li").length ==0)
  {
    $(".box_list_image").css("display","none");
  }
}

function readFileMulti(onthis, key=0) 
{
  
  if (onthis) 
  {
   
    var FR= new FileReader();
    
    FR.addEventListener("load", function(e) {
    
      var dl = onthis.size/(1024);
      dl = dl.toFixed(2);

      let fileExt = onthis.name.split('.').pop();
      fileExt = fileExt.toLowerCase();
      let fileIcon = "font-icon font-icon-cam-photo";

      let removeItemFunc = `removeItemGallery(this,${key})`;

      if(typeof attachFrmTrx != "undefined")
      {
          removeItemFunc = `removeAttachFile(this,${key})`;
      }

      if(typeof attachFileIcons != "undefined")
      {
          var attachFileIconsDecode = json_decode(attachFileIcons);
          if(attachFileIconsDecode[fileExt])
          {
              fileIcon = attachFileIconsDecode[fileExt];
          }
      }

      var html_img = `
        <li class="uploading-list-item">
          <div class="uploading-list-item-wrapper">
              <div class="uploading-list-item-name">
                  <i class="${fileIcon}"></i>
                  `+onthis.name+`
              </div>
              <div class="uploading-list-item-size">`+dl+` kb</div>
              <button type="button" class="uploading-list-item-close" onclick="${removeItemFunc}">
                  <i class="font-icon-close-2"></i>
              </button>
          </div>
          <progress class="progress progress-bar-`+key+`" value="0" max="100">
              
          </progress>
          <div style="display: none;" class="uploading-list-item-progress status-`+key+`">0% done</div>
      </li>
    `;

        $("ul.list_upload").prepend(html_img);
    }); 
    
    FR.readAsDataURL( onthis);
  }
 
}

   function removeItemGallery(obj, key)
   {
      var form = $(obj).parents("form");
      $(obj).parents(".uploading-list-item").remove();
      // var files_list = $(form)[0].elements['list_image[]'].files;
      // console.log(typeof(files_list));
      // console.log(typeof(files_list[key]));
      // console.log(typeof(files_list[key].File));
      delete $(form)[0].elements['list_image[]'].files;
      // console.log(typeof($(form)[0].elements['list_image[]'].files));
      // var abc = files_list.slice(key);
      // console.log($(form)[0].elements['list_image[]'].files);
      // console.log($(form)[0].elements['list_image[]'].files[key]);
   }


  function submitGallery(obj, event)
  {
     
     //configuration
    var max_file_size       = 2048576; //allowed file size. (1 MB = 1048576)
    var allowed_file_types    = ['image/png', 'image/gif', 'image/jpeg', 'image/pjpeg']; //allowed file types
    var result_output       = '#output'; //ID of an element for response output
    var my_form_id        = $(obj).attr("id"); //ID of an element for response output
    var progress_bar_id     = '.list_upload'; //ID of an element for response output
    var total_files_allowed   = 10; //Number files allowed to upload
    // var obj = $("#"+my_form_id); 
    // console.log($(obj)); return false;
    //on form submit
    // $("#"+my_form_id).on( "submit", function(event) { 
      // event.preventDefault();
      var proceed = true; //set proceed flag
      var error = []; //errors
      var total_files_size = 0;
      // console.log("asdfa");
      // return false;
      //reset progressbar
      // $(progress_bar_id +" .progress-bar").css("width", "0%");
      // $(progress_bar_id + " .status").text("0%");

      var upload_type = $(obj).find("[name=upload_type]:checked").val();

      if(upload_type == 'file')
      {
          if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
              error.push("Your browser does not support new File API! Please upgrade."); //push error text
          }else{
              var total_selected_files = $(obj)[0].elements['list_image[]'].files.length; //number of files
              // var obj = $(this);
              var post_url = $(obj).attr("action"); //get action URL of form
              //limit number of files allowed
              // if(total_selected_files > total_files_allowed)
              // {
              //  error.push( "You have selected "+total_selected_files+" file(s), " + total_files_allowed +" is maximum!"); //push error text
              //  proceed = false; //set proceed flag to false
              // }
              var submit_btn  = $(obj).find("button[type=submit]"); //form submit button
              var data_form = $('#'+my_form_id).serialize();

              //iterate files in file input field
              var check_submit = 0;


              $($(obj)[0].elements['list_image[]'].files).each(function(i, ifile)
              {
                  check_submit = 1;
                  // $(progress_bar_id).prepend(`<div class="progress-wrp"><div class="progress-bar progress-bar-`+i+`"></div ><div class="status status-`+i+`">0%</div></div>`);
                  if(ifile.value !== "")
                  { //continue only if file(s) are selected
                      if(allowed_file_types.indexOf(ifile.type) === -1)
                      { //check unsupported file
                          error.push( "<b>"+ ifile.name + "</b> is unsupported file type!"); //push error text
                          proceed = false; //set proceed flag to false
                      }

                      // total_files_size = total_files_size + ifile.size; //add file size to total size

                      if(proceed)
                      {
                          //submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
                          var form_data = new FormData(); //Creates new FormData object
                          form_data.append("list_image", ifile);

                          // console.log(form_data);
                          // console.log(form_data);
                          //jQuery Ajax to Post form data
                          $.ajax({
                              url : post_url+"&"+data_form,
                              type: "POST",
                              data : form_data,
                              contentType: false,
                              cache: false,
                              processData:false,
                              xhr: function(){
                                  //upload Progress
                                  var xhr = $.ajaxSettings.xhr();
                                  if (xhr.upload) {
                                      xhr.upload.addEventListener('progress', function(event) {
                                          var percent = 0;
                                          var position = event.loaded || event.position;
                                          var total = event.total;
                                          if (event.lengthComputable) {
                                              percent = Math.ceil(position / total * 100);
                                          }
                                          //update progressbar
                                          // $(progress_bar_id +" .progress-bar-"+i).css("width", + percent +"%");
                                          $(progress_bar_id +" .progress-bar-"+i).attr("value", percent);
                                          $(progress_bar_id + " .status-"+i).text(percent +"%");
                                      }, true);
                                  }
                                  return xhr;
                              },
                              mimeType:"multipart/form-data"
                          }).done(function(res){ //
                              location.reload();
                              // $(my_form_id)[0].reset(); //reset form
                              // console.log(res);
                              // $(result_output).html(res); //output response from server
                              // submit_btn.val("Upload").prop( "disabled", false); //enable submit button once ajax is done
                          });

                      }




                  }
              });

              //if total file size is greater than max file size
              // if(total_files_size > max_file_size)
              // {
              //  error.push( "You have "+total_selected_files+" file(s) with total size "+total_files_size+", Allowed size is " + max_file_size +", Try smaller file!"); //push error text
              //  proceed = false; //set proceed flag to false
              // }



              //if everything looks good, proceed with jQuery Ajax

          }
      }
      else
      {
          //Upload by img url
          var post_url = $(obj).attr("action"); //get action URL of form
          var submit_btn  = $(obj).find("button[type=submit]"); //form submit button
          var data_form = $('#'+my_form_id).serialize();
          check_submit = 0;
      }
      
      $(result_output).html(""); //reset output
      $(error).each(function(i){ //output any error to output element
        $(result_output).append('<div class="error">'+error[i]+"</div>");
        check_submit = 1;
      });

      if(check_submit == 0)
      {
        //submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
        var form_data = new FormData(); //Creates new FormData object
        form_data.append("list_image", "");

        //jQuery Ajax to Post form data
        $.ajax({
          url : post_url+"&"+data_form,
          type: "POST",
          data : form_data,
          contentType: false,
          cache: false,
          processData:false,
          xhr: function(){
            //upload Progress
            var xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
              xhr.upload.addEventListener('progress', function(event) {
                var percent = 0;
                var position = event.loaded || event.position;
                var total = event.total;
                if (event.lengthComputable) {
                  percent = Math.ceil(position / total * 100);
                }
                //update progressbar
                // $(progress_bar_id +" .progress-bar-"+i).css("width", + percent +"%");
                $(progress_bar_id +" .progress-bar-"+i).attr("value", percent);
                $(progress_bar_id + " .status-"+i).text(percent +"%");
              }, true);
            }
            return xhr;
          },
          mimeType:"multipart/form-data"
        }).done(function(res){ //
          location.reload();
        });
      }

        
    // }); 

return false;
  }
  
  function submitgiftcard(obj, event)
  {
      blockLoading($("#giftcards_form"));

      //configuration
    var max_file_size       = 2048576; //allowed file size. (1 MB = 1048576)
    var allowed_file_types    = ['image/png', 'image/gif', 'image/jpeg', 'image/pjpeg']; //allowed file types
    var result_output       = '#output'; //ID of an element for response output
    var my_form_id        = $(obj).attr("id"); //ID of an element for response output
    var progress_bar_id     = '.list_upload'; //ID of an element for response output
    var total_files_allowed   = 3; //Number files allowed to upload
 
      var proceed = true; //set proceed flag
      var error = []; //errors
      var total_files_size = 0;
    var option_upload_method = $('#'+my_form_id + ' input[name=option_upload_image]:checked').val();
 

      if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
        error.push("Your browser does not support new File API! Please upgrade."); //push error text
      }else{
        var total_selected_files = $(obj)[0].elements['product_image'].files.length; //number of files
        // var obj = $(this);
        var post_url = $(obj).attr("action"); //get action URL of form
       
        var submit_btn  = $(obj).find("button[type=submit]"); //form submit button 
        var data_form = $('#'+my_form_id).serialize();
        
        //iterate files in file input field
        if(total_selected_files == 0)
        {
              var form_data = new FormData();
              if(option_upload_method == 1)
              {
                var p_design = [];
                var style_push = "";
                var style_p_push = "";
                var angle = "";
                // Remove element and style in canvas
                
                $( "#printable .t" ).each(function( index ) {
                    
                    var style_t = $(this).attr("style");
                    var style_p = $(this).find("p").attr("style"); 
                    style_push = style_t + "|||" + style_push;
                    style_p_push = style_p + "|||" + style_p_push;
                    $( this ).find("p").css("border","1px");
                    $(this).find(".icon-remove").css("visibility", "hidden");
                    $(this).find(".icon-edit").css("visibility", "hidden");
                    $(this).find(".icon-rotation").css("visibility", "hidden");
                    $(this).find(".ui-resizable-handle").css("visibility", "hidden");
                 
                    var style_transform_bk =  $(this).attr("style");
                    var res = style_transform_bk.split(";");

                    var arrayLength = res.length;
                    for (var i = 0; i < arrayLength; i++) {
                       var res_2 = res[i].split(":");
                       if(res_2[0] == " transform")
                       {
                          var angle = res_2[1]; 
                       }
                        //Do something
                    }
      
                    $(this).find("p").css("transform",angle);
                     $(this).css("transform","");
                   

                });
                var file_data = $("input[name='upload_image_method_1']").prop("files")[0];
                if (typeof file_data !== typeof undefined && file_data !== false) {        
                }
                else
                {
                    file_data = "";
                    

                }
                var src_image_library = $('#src_image_library').val();
     
                if(src_image_library != "" && typeof src_image_library !== typeof undefined )
                {
                    form_data.append("upload_img_original", dataURLtoBlob(src_image_library));

                }else
                {
                    form_data.append("upload_img_original", file_data);
                }
              


                var element = $("#printable"); // global variable
                html2canvas(element, {
                     onrendered: function (canvas) {
                      var getCanvas=  canvas;
        
                      form_data.append("product_image", dataURLtoBlob(canvas.toDataURL()));
                      form_data.append("style_push", style_push); 
                      form_data.append("style_p_push", style_p_push);

                       $.ajax({
                          url : post_url+"&"+data_form,
                          type: "POST",
                          data : form_data,
                          contentType: false,
                          cache: false,
                          processData:false,
                          mimeType:"multipart/form-data"
                      }).done(function(res){
                         unblockLoading($("#giftcards_form"));
                         var obj = JSON.parse(res);
      
                         if(obj.status == "error")
                         {
                           call_notify("Giftcard",obj.msg, 'danger','');
                         }
                         else
                         {
                             window.location = site_root_domain+"/?site=giftcards";
                         }

                          return false;

                   
                          
                      });

                
                    }

                });

               
            }//End if
            else
            {
              $.ajax({
                      url : post_url+"&"+data_form,
                      type: "POST",
                      data : form_data,
                      contentType: false,
                      cache: false,
                      processData:false,                   
                      mimeType:"multipart/form-data"
                    }).done(function(res){
                        unblockLoading($("#giftcards_form"));

                      //location.reload();
                       var obj = JSON.parse(res);
                       if(obj.status == "error")
                       {
                         call_notify("Giftcard",obj.msg, 'danger','');
                       }
                       else
                       {
                           window.location = site_root_domain+"/?site=giftcards";
                       }

                        return false;
                     
                    }); 
            }
                             
        }
        else
        {

            $($(obj)[0].elements['product_image'].files).each(function(i, ifile)
            {

              // $(progress_bar_id).prepend(`<div class="progress-wrp"><div class="progress-bar progress-bar-`+i+`"></div ><div class="status status-`+i+`">0%</div></div>`);
              if(ifile.value !== "")
              { //continue only if file(s) are selected
                
                if(allowed_file_types.indexOf(ifile.type) === -1)
                { //check unsupported file
                  error.push( "<b>"+ ifile.name + "</b> is unsupported file type!"); //push error text
                  proceed = false; //set proceed flag to false
                }

                // total_files_size = total_files_size + ifile.size; //add file size to total size

                if(proceed)
                {
                  //submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
                  var form_data = new FormData(); //Creates new FormData object
                  form_data.append("product_image", ifile);

                  // console.log(form_data);
                  // console.log(form_data);
                  //jQuery Ajax to Post form data

                  $.ajax({
                    url : post_url+"&"+data_form,
                    type: "POST",
                    data : form_data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    xhr: function(){
                      //upload Progress
                      var xhr = $.ajaxSettings.xhr();
                      if (xhr.upload) {
                        xhr.upload.addEventListener('progress', function(event) {
                          var percent = 0;
                          var position = event.loaded || event.position;
                          var total = event.total;
                          if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                          }
                          //update progressbar
                          // $(progress_bar_id +" .progress-bar-"+i).css("width", + percent +"%");
                          $(progress_bar_id +" .progress-bar-"+i).attr("value", percent);
                          $(progress_bar_id + " .status-"+i).text(percent +"%");
                        }, true);
                      }
                      return xhr;
                    },
                    mimeType:"multipart/form-data"
                  }).done(function(res){ //
                      blockLoading($("#giftcards_form"));
                       var obj = JSON.parse(res);
                       if(obj.status == "error")
                       {
                         call_notify("Giftcard",obj.msg, 'danger','');
                       }
                       else
                       {
                           window.location = site_root_domain+"/?site=giftcards";
                       }

                        return false;
                    //location.reload();
                   });
                }
              }

            });        
        }
        
        
       
      }
      
      $(result_output).html(""); //reset output 
      $(error).each(function(i){ //output any error to output element
      $(result_output).append('<div class="error">'+error[i]+"</div>");
      });
        
    // }); 

return false;
  }


  function submitgiftcard_ibe(obj,  event)
  {
      blockLoading($("#giftcards_form"));
 
      //configuration
    var max_file_size       = 2048576; //allowed file size. (1 MB = 1048576)
    var allowed_file_types    = ['image/png', 'image/gif', 'image/jpeg', 'image/pjpeg']; //allowed file types
    var result_output       = '#output'; //ID of an element for response output
    var my_form_id        = $(obj).attr("id"); //ID of an element for response output
    var progress_bar_id     = '.list_upload'; //ID of an element for response output
    var total_files_allowed   = 3; //Number files allowed to upload
 
      var proceed = true; //set proceed flag
      var error = []; //errors
      var total_files_size = 0;
    var option_upload_method = $('#'+my_form_id + ' input[name=option_upload_image]:checked').val();
    var cus_id = $('#'+my_form_id + ' input[name=cus_id]').val();
 

      if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
        error.push("Your browser does not support new File API! Please upgrade."); //push error text
      }else{
        var total_selected_files = $(obj)[0].elements['product_image'].files.length; //number of files
        // var obj = $(this);
        var post_url = $(obj).attr("action"); //get action URL of form
       
        var submit_btn  = $(obj).find("button[type=submit]"); //form submit button 
        var data_form = $('#'+my_form_id).serialize();
 
        //iterate files in file input field
        if(total_selected_files == 0)
        {
              var form_data = new FormData();
              if(option_upload_method == 1)
              {
                var p_design = [];
                var style_push = "";
                var style_p_push = "";
                var angle = "";
                // Remove element and style in canvas
                
                $( "#printable .t" ).each(function( index ) {
                    
                    var style_t = $(this).attr("style");
                    var style_p = $(this).find("p").attr("style"); 
                    style_push = style_t + "|||" + style_push;
                    style_p_push = style_p + "|||" + style_p_push;
                    $( this ).find("p").css("border","1px");
                    $(this).find(".icon-remove").css("visibility", "hidden");
                    $(this).find(".icon-edit").css("visibility", "hidden");
                    $(this).find(".icon-rotation").css("visibility", "hidden");
                    $(this).find(".ui-resizable-handle").css("visibility", "hidden");
                 
                    var style_transform_bk =  $(this).attr("style");
                    var res = style_transform_bk.split(";");

                    var arrayLength = res.length;
                    for (var i = 0; i < arrayLength; i++) {
                       var res_2 = res[i].split(":");
                       if(res_2[0] == " transform")
                       {
                          var angle = res_2[1]; 
                       }
                        //Do something
                    }
      
                    $(this).find("p").css("transform",angle);
                     $(this).css("transform","");
                   

                });
                var file_data = $("input[name='upload_image_method_1']").prop("files")[0];
                if (typeof file_data !== typeof undefined && file_data !== false) {        
                }
                else
                {
                    file_data = "";
                    

                }
                var src_image_library = $('#src_image_library').val();
     
                if(src_image_library != "" && typeof src_image_library !== typeof undefined )
                {
                    form_data.append("upload_img_original", dataURLtoBlob(src_image_library));

                }else
                {
                    form_data.append("upload_img_original", file_data);
                }
              


                var element = $("#printable"); // global variable
                html2canvas(element, {
                     onrendered: function (canvas) {
                      var getCanvas=  canvas;
        
                      form_data.append("product_image", dataURLtoBlob(canvas.toDataURL()));
                      form_data.append("style_push", style_push); 
                      form_data.append("style_p_push", style_p_push);

                       $.ajax({
                          url : post_url+"&"+data_form,
                          type: "POST",
                          data : form_data,
                          contentType: false,
                          cache: false,
                          processData:false,
                          mimeType:"multipart/form-data"
                      }).done(function(res){
                         unblockLoading($("#giftcards_form"));
                         var obj = JSON.parse(res);
      
                         if(obj.status == "error")
                         {
                           call_notify("Giftcard",obj.msg, 'danger','');
                         }
                         else
                         {
                             window.location = site_root_domain+"/?site=giftcards&cus_id="+cus_id;
                         }

                          return false;

                   
                          
                      });

                
                    }

                });

               
            }//End if
            else
            {
              $.ajax({
                      url : post_url+"&"+data_form,
                      type: "POST",
                      data : form_data,
                      contentType: false,
                      cache: false,
                      processData:false,                   
                      mimeType:"multipart/form-data"
                    }).done(function(res){
                        unblockLoading($("#giftcards_form"));

                      //location.reload();
                       var obj = JSON.parse(res);
                       if(obj.status == "error")
                       {
                         call_notify("Giftcard",obj.msg, 'danger','');
                       }
                       else
                       {
                           window.location = site_root_domain+"/?site=giftcards&cus_id="+cus_id;
                       }

                        return false;
                     
                    }); 
            }
                             
        }
        else
        {

            $($(obj)[0].elements['product_image'].files).each(function(i, ifile)
            {

              // $(progress_bar_id).prepend(`<div class="progress-wrp"><div class="progress-bar progress-bar-`+i+`"></div ><div class="status status-`+i+`">0%</div></div>`);
              if(ifile.value !== "")
              { //continue only if file(s) are selected
                
                if(allowed_file_types.indexOf(ifile.type) === -1)
                { //check unsupported file
                  error.push( "<b>"+ ifile.name + "</b> is unsupported file type!"); //push error text
                  proceed = false; //set proceed flag to false
                }

                // total_files_size = total_files_size + ifile.size; //add file size to total size

                if(proceed)
                {
                  //submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
                  var form_data = new FormData(); //Creates new FormData object
                  form_data.append("product_image", ifile);

                  // console.log(form_data);
                  // console.log(form_data);
                  //jQuery Ajax to Post form data

                  $.ajax({
                    url : post_url+"&"+data_form,
                    type: "POST",
                    data : form_data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    xhr: function(){
                      //upload Progress
                      var xhr = $.ajaxSettings.xhr();
                      if (xhr.upload) {
                        xhr.upload.addEventListener('progress', function(event) {
                          var percent = 0;
                          var position = event.loaded || event.position;
                          var total = event.total;
                          if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                          }
                          //update progressbar
                          // $(progress_bar_id +" .progress-bar-"+i).css("width", + percent +"%");
                          $(progress_bar_id +" .progress-bar-"+i).attr("value", percent);
                          $(progress_bar_id + " .status-"+i).text(percent +"%");
                        }, true);
                      }
                      return xhr;
                    },
                    mimeType:"multipart/form-data"
                  }).done(function(res){ //
                      blockLoading($("#giftcards_form"));
                       var obj = JSON.parse(res);
                       if(obj.status == "error")
                       {
                         call_notify("Giftcard",obj.msg, 'danger','');
                       }
                       else
                       {
                           window.location = site_root_domain+"/?site=giftcards&cus_id="+cus_id;
                       }

                        return false;
                    //location.reload();
                   });
                }
              }

            });        
        }
        
        
       
      }
      
      $(result_output).html(""); //reset output 
      $(error).each(function(i){ //output any error to output element
      $(result_output).append('<div class="error">'+error[i]+"</div>");
      });
        
    // }); 

return false;
  }


function submitcoupon(obj, event) {

    blockLoading($("#coupon_form"));

    //configuration
    var max_file_size = 2048576; //allowed file size. (1 MB = 1048576)
    var allowed_file_types = ['image/png', 'image/gif', 'image/jpeg', 'image/pjpeg']; //allowed file types
    var result_output = '#output'; //ID of an element for response output
    var my_form_id = $(obj).attr("id"); //ID of an element for response output
    var progress_bar_id = '.list_upload'; //ID of an element for response output
    var total_files_allowed = 3; //Number files allowed to upload
    // var obj = $("#"+my_form_id); 
    // console.log($(obj)); return false;
    //on form submit
    // $("#"+my_form_id).on( "submit", function(event) { 
    // event.preventDefault();
    var proceed = true; //set proceed flag
    var error = []; //errors
    var total_files_size = 0;

    // return false;
    //reset progressbar
    // $(progress_bar_id +" .progress-bar").css("width", "0%");
    // $(progress_bar_id + " .status").text("0%");

    if (!window.File && window.FileReader && window.FileList && window.Blob) { //if browser doesn't supports File API
        error.push("Your browser does not support new File API! Please upgrade."); //push error text
    } else {
        var total_selected_files = $(obj)[0].elements['list_image[]'].files.length; //number of files
        // var obj = $(this);
        var post_url = $(obj).attr("action"); //get action URL of form
        //limit number of files allowed
        // if(total_selected_files > total_files_allowed)
        // {
        //  error.push( "You have selected "+total_selected_files+" file(s), " + total_files_allowed +" is maximum!"); //push error text
        //  proceed = false; //set proceed flag to false
        // }
        var submit_btn = $(obj).find("input[name=add_coupons]"); //form submit button
        var data_form = $('#' + my_form_id).serialize();

        var option_upload_method = $('#' + my_form_id + ' input[name=option_upload_image]:checked').val();


        //iterate files in file input field
        if (total_selected_files == 0) {
            var form_data = new FormData();
            if (option_upload_method == 1) {
                var p_design = [];
                var style_push = "";
                var style_p_push = "";
                var angle = "";
                // Remove element and style in canvas

                $("#printable .t").each(function (index) {

                    var style_t = $(this).attr("style");
                    var style_p = $(this).find("p").attr("style");
                    style_push = style_t + "|||" + style_push;
                    style_p_push = style_p + "|||" + style_p_push;
                    $(this).find("p").css("border", "1px");
                    $(this).find(".icon-remove").css("visibility", "hidden");
                    $(this).find(".icon-edit").css("visibility", "hidden");
                    $(this).find(".icon-rotation").css("visibility", "hidden");
                    $(this).find(".ui-resizable-handle").css("visibility", "hidden");

                    var style_transform_bk = $(this).attr("style");
                    var res = style_transform_bk.split(";");

                    var arrayLength = res.length;
                    for (var i = 0; i < arrayLength; i++) {
                        var res_2 = res[i].split(":");
                        if (res_2[0] == " transform") {
                            var angle = res_2[1];
                        }
                        //Do something
                    }

                    $(this).find("p").css("transform", angle);
                    $(this).css("transform", "");


                });
                var file_data = $("input[name='upload_image_method_1']").prop("files")[0];
                if (typeof file_data !== typeof undefined && file_data !== false) {
                }
                else {
                    file_data = "";
                }

                var src_image_library = $('#src_image_library').val();

                if (src_image_library != "" && typeof src_image_library !== typeof undefined) {
                    form_data.append("upload_img_original", dataURLtoBlob(src_image_library));

                } else {
                    form_data.append("upload_img_original", file_data);
                }


                var element = $("#printable"); // global variable
                html2canvas(element, {
                    onrendered: function (canvas) {
                        var getCanvas = canvas;

                        form_data.append("list_image", dataURLtoBlob(canvas.toDataURL()));
                        form_data.append("style_push", style_push);
                        form_data.append("style_p_push", style_p_push);
                        $.ajax({
                            url: post_url + "&" + data_form,
                            type: "POST",
                            data: form_data,
                            contentType: false,
                            cache: false,
                            processData: false,
                            mimeType: "multipart/form-data"
                        }).done(function (res) {
                            unblockLoading($("#coupon_form"));
                            var obj = JSON.parse(res);
                            if (obj.status == "error") {
                                call_notify("Coupon", obj.msg, 'danger', '');
                            }
                            else {
                                window.location = site_root_domain + "/?site=coupons";
                            }

                            return false;

                        });


                    }

                });


            }//End if
            else {
                $.ajax({
                    url: post_url + "&" + data_form,
                    type: "POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    mimeType: "multipart/form-data"
                }).done(function (res) {
                    unblockLoading($("#coupon_form"));
                    var obj = JSON.parse(res);
                    if (obj.status == "error") {
                        call_notify("Coupon", obj.msg, 'danger', '');
                    }
                    else {
                        window.location = site_root_domain + "/?site=coupons";
                    }

                    return false;

                });
            }

        }
        else {
            $($(obj)[0].elements['list_image[]'].files).each(function (i, ifile) {

                // $(progress_bar_id).prepend(`<div class="progress-wrp"><div class="progress-bar progress-bar-`+i+`"></div ><div class="status status-`+i+`">0%</div></div>`);
                if (ifile.value !== "") { //continue only if file(s) are selected

                    if (allowed_file_types.indexOf(ifile.type) === -1) { //check unsupported file
                        error.push("<b>" + ifile.name + "</b> is unsupported file type!"); //push error text
                        proceed = false; //set proceed flag to false
                    }

                    // total_files_size = total_files_size + ifile.size; //add file size to total size

                    if (proceed) {
                        //submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
                        var form_data = new FormData(); //Creates new FormData object
                        form_data.append("list_image", ifile);

                        // console.log(form_data);
                        // console.log(form_data);
                        //jQuery Ajax to Post form data

                        $.ajax({
                            url: post_url + "&" + data_form,
                            type: "POST",
                            data: form_data,
                            contentType: false,
                            cache: false,
                            processData: false,
                            xhr: function () {
                                //upload Progress
                                var xhr = $.ajaxSettings.xhr();
                                if (xhr.upload) {
                                    xhr.upload.addEventListener('progress', function (event) {
                                        var percent = 0;
                                        var position = event.loaded || event.position;
                                        var total = event.total;
                                        if (event.lengthComputable) {
                                            percent = Math.ceil(position / total * 100);
                                        }
                                        //update progressbar
                                        // $(progress_bar_id +" .progress-bar-"+i).css("width", + percent +"%");
                                        $(progress_bar_id + " .progress-bar-" + i).attr("value", percent);
                                        $(progress_bar_id + " .status-" + i).text(percent + "%");
                                    }, true);
                                }
                                return xhr;
                            },
                            mimeType: "multipart/form-data"
                        }).done(function (res) { //
                            unblockLoading($("#coupon_form"));
                            //console.log(res);
                            var obj = JSON.parse(res);

                            if (obj.status == "error") {
                                call_notify("Coupon", obj.msg, 'danger', '');
                            }
                            else {
                                window.location = site_root_domain + "/?site=coupons";
                            }


                            // location.reload();

                            // $(my_form_id)[0].reset(); //reset form
                            // console.log(res);
                            // $(result_output).html(res); //output response from server
                            // submit_btn.val("Upload").prop( "disabled", false); //enable submit button once ajax is done
                        });
                    }
                }

            });
        }


        //if total file size is greater than max file size
        // if(total_files_size > max_file_size)
        // { 
        //  error.push( "You have "+total_selected_files+" file(s) with total size "+total_files_size+", Allowed size is " + max_file_size +", Try smaller file!"); //push error text
        //  proceed = false; //set proceed flag to false
        // }
        //if everything looks good, proceed with jQuery Ajax

    }

    $(result_output).html(""); //reset output 
    $(error).each(function (i) { //output any error to output element
        $(result_output).append('<div class="error">' + error[i] + "</div>");
    });

    // }); 

    return false;
}


 function dataURLtoBlob (dataURL)
    {
      // console.log(dataURL);return false;
        var img = dataURL.split(',');
        binary = atob(img[1]);
        // # Create 8-bit unsigned array
        array = [];
        i = 0;
        while (i < binary.length)
        {
          array.push(binary.charCodeAt(i));
          i++
        }
        // # Return our Blob object
      return new Blob([ new Uint8Array(array)], {type: 'image/png'});
    }

    function getExportTrxFiles(id)
    {
        blockLoading();
        $.ajax({
            url: site_root_domain + '/?site=transactions&type=1&subact=get_export_files&id='+id,
            dataType: 'json',
            success: function(res){
                unblockLoading();
                if(res.status == 'success')
                {
                    $('#getExportFiles').modal();
                    if($.fn.dataTable.isDataTable( '#getExportFilesTable' ))
                    {
                        $('#getExportFilesTable').DataTable().destroy();
                    }
                    $('#getExportFilesTable').DataTable( {
                        data: res.files,
                        columns: [
                            { title: "Name" },
                            { title: "Date" },
                        ]
                    } );
                }
                else
                {
                    call_notify("Notification", res.msg, "danger", "");
                }
            }
        })
    }

function rebuild_field_empty(onthis )
{
  var cur_val = $(onthis).val();
  if(cur_val == 0 || cur_val == "")
  {
    $(onthis).val("");
  }
}
function rebuild_product_price(onthis,form_name)
{
  var cur_field_price = $(onthis).attr("name");
  var cur_price = $(onthis).val();
  if(cur_field_price == "product_price")
  {
      var  field_price_1 = parseInt($(form_name+ " input[name='product_price_original']").val());
      var  field_price_2 = parseInt($(form_name+ " input[name='product_price_sell']").val());
      if( field_price_1 == 0 || isNaN(field_price_1))// orginal
      {
          $(form_name+ " input[name='product_price_original']").val(cur_price);
      }
      if( field_price_2 == 0 || isNaN(field_price_2) )// sell
      {
          var new_price = Math.round(cur_price * 1.1);
          $(form_name+ " input[name='product_price_sell']").val(new_price);
      }

  }

  if(cur_field_price == "product_price_original")
  {
      var  field_price_1 = parseInt($(form_name+ " input[name='product_price']").val());
      var  field_price_2 = parseInt($(form_name+ " input[name='product_price_sell']").val());
      if( field_price_1 == 0 || isNaN(field_price_1))// orginal
      {
          $(form_name+ " input[name='product_price']").val(cur_price);
      }
      if( field_price_2 == 0 || isNaN(field_price_2))// sell
      {
          var new_price = Math.round(cur_price * 1.1);
          $(form_name+ " input[name='product_price_sell']").val(new_price);
      }

  }

  if(cur_field_price == "product_price_sell")
  {
      var  field_price_1 = parseInt($(form_name+ " input[name='product_price']").val());
      var  field_price_2 = parseInt($(form_name+ " input[name='product_price_original']").val());
       var new_price = Math.round(cur_price / 1.1);
      if( field_price_1 == 0)// firts price
      {
          $(form_name+ " input[name='product_price']").val(new_price);
      }
      if( field_price_2 == 0)// original
      {
          $(form_name+ " input[name='product_price_original']").val(new_price);
      }

  }

  calculate_subitem();

}

function ger_pgcode(onthis,el)
{
  var this_val = $.trim($(onthis).val());
  var new_string =  this_val.split(" ");
   
  if( $(el).val() == " ")
  {
    if(new_string.length == 1)
    {
        $(el).val(new_string[0].substring(0,3).toUpperCase());
    }
    else
    {
      var new_val = "";
      for(var i = 0; i < new_string.length ; i++ )
      {
        if(typeof(new_string[i]) != undefined || new_string[i] != "")
        {
           new_val += new_string[i].substring(0,1).toUpperCase();
        }
      }
      $(el).val(new_val);
    }
  }
  
}



function rebuild_asset_price(onthis,form_name)
{
  var cur_field_price = $(onthis).attr("name");
  var cur_price = $(onthis).val();
 
  if(cur_field_price == "ass_purchase_price")
  {
      var  field_price_1 = parseInt($(form_name+ " input[name='ass_original_price']").val());
      var  field_price_2 = parseInt($(form_name+ " input[name='ass_price']").val());
      if( field_price_1 == 0  || isNaN(field_price_1))// orginal
      {
          $(form_name+ " input[name='ass_original_price']").val(cur_price);
      }
      if( field_price_2 == 0  || isNaN(field_price_2))// sell
      {
          var new_price = Math.round(cur_price * 1.1);
          $(form_name+ " input[name='ass_price']").val(new_price);
      }

  }

  if(cur_field_price == "ass_original_price")
  {
      var  field_price_1 = parseInt($(form_name+ " input[name='ass_purchase_price']").val());
      var  field_price_2 = parseInt($(form_name+ " input[name='ass_price']").val());
      if( field_price_1 == 0  || isNaN(field_price_1))// orginal
      {
          $(form_name+ " input[name='ass_purchase_price']").val(cur_price);
      }
      if( field_price_2 == 0  || isNaN(field_price_2))// sell
      {
          var new_price = Math.round(cur_price * 1.1);
          $(form_name+ " input[name='ass_price']").val(new_price);
      }

  }

  if(cur_field_price == "ass_price")
  {
      var  field_price_1 = parseInt($(form_name+ " input[name='ass_purchase_price']").val());
      var  field_price_2 = parseInt($(form_name+ " input[name='ass_original_price']").val());
      var new_price = Math.round(cur_price / 1.1);
    
      if( field_price_1 == 0 || isNaN(field_price_1))// firts price
      {
          $(form_name+ " input[name='ass_purchase_price']").val(new_price);
      }
      if( field_price_2 == 0 || isNaN(field_price_2))// original
      {
          $(form_name+ " input[name='ass_original_price']").val(new_price);
      }

  }

}



function submitorder(obj)
{
   var cnt_product = 0;
   var cnt_assets = 0;
   // Check product item

        //var 
        // var pro_val_tmp = $(obj).find("input[name='product_id[]']").val();
        // var pro_val = !isNaN(parseInt(pro_val_tmp)) ? pro_val_tmp : 0;
        // var pro_cont = $(obj).find("input[name='product_name[]']").val();
        // if(pro_val == 0 && pro_cont != " ")
        // {
        //    console.log("false");
        // }else
        // {
        //     console.log("true");
        // }


  //  // Check assets item
   
   var cnt_product = 0;
   var cnt_assets = 0;
   // Check product item
   
   $( "#data_table .row-grid" ).each(function( index ) {
        var pro_val = $(this).find("input[name='product_id[]']").val();
        if(pro_val > 0)
        {
          cnt_product += 1;
        }
  });

   // Check assets item
   
   $( "#data_table_asset .row-grid-asset" ).each(function( index ) {
        var ass_val = $(this).find("input[name='ass_id[]']").val();
        if(ass_val != "")
        {
          cnt_assets += 1;
        }
  }); 

   if(cnt_product == 0 && cnt_assets == 0)
   {
      $("body").addClass("control-panel");
      $("body").addClass("open");
      $(".control-panel-container").css("display","block");
      $(".ha-underlay").css("display","block");
      return false;
   }
   else
   {
     return true;
   }
}

$(".drawer-close").click(function(){
    $("body").removeClass("control-panel");
    $("body").removeClass("open");
    $(".control-panel-container").css("display","none");
    $(".ha-underlay").css("display","none");

     
    $(".ha-underlay").css("z-index","100");  
    reset_value_item();
});


function reset_value_item()
{
  var i_pro = $("#data_table .row-grid.active");
  if(i_pro.find("input[name='product_name[]']").val() != "" &&  i_pro.find("input[name='product_id[]']").val() == "" )
  {
    i_pro.find("input[name='product_name[]']").val(" "); 
  }

  var i_ass = $("#data_table_asset .row-grid-asset.active");
  if(i_ass.find("input[name='ass_name[]']").val() != "" &&  i_ass.find("input[name='ass_id[]']").val() == "" )
  {
    i_ass.find("input[name='ass_name[]']").val(" "); 
  }


}

$("#panel_add_product").click(function(){


   $(".ha-underlay").css("display","none");
  $(".ha-underlay").css("z-index","100");
  reset_value_item();
   $( "#data_table .row-grid" ).each(function( index ) {
        var pro_val = $(this).find("input[name='product_id[]']").val();
        if(pro_val == 0)
        {
           $(this).addClass("active");
          changeProductType(0);
          $("#data_table .row-grid").not(':first').remove();
          $("#data_table .row-grid:first").attr("rowtr","last-row");
          //Hide panel
          $("body").removeClass("control-panel");
          $("body").removeClass("open");
          $(".control-panel-container").css("display","none");

           return false;
        }
  });

});


$("#panel_add_service").click(function(){


  $(".ha-underlay").css("display","none");
  $(".ha-underlay").css("z-index","100");
  reset_value_item();
   $( "#data_table .row-grid" ).each(function( index ) {
        var pro_val = $(this).find("input[name='product_id[]']").val();
        if(pro_val == 0)
        {
           $(this).addClass("active");
          changeProductType(1);
          $("#data_table .row-grid").not(':first').remove();
          $("#data_table .row-grid:first").attr("rowtr","last-row");
          //Hide panel
          $("body").removeClass("control-panel");
          $("body").removeClass("open");
          $(".control-panel-container").css("display","none");

           return false;
        }
  });

});

$("#panel_add_assets").click(function(){

   $(".ha-underlay").css("display","none");
  $(".ha-underlay").css("z-index","100");
  reset_value_item();
   $( "#data_table_asset .row-grid-asset" ).each(function( index ) {
        var pro_val = $(this).find("input[name='ass_id[]']").val();
        if(pro_val == 0)
        {
           $(this).addClass("active");
          $("#asset_submit_type").val("add");
            $.magnificPopup.open({
                type: 'inline',
                closeOnContentClick: false,
                items: {
                    src: '#box_asset'
                },
            });
          $("#data_table_asset .row-grid-asset").not(':first').remove();
          $("#data_table_asset .row-grid-asset:first").attr("rowtr","last-row");
          
          //Hide panel
          $("body").removeClass("control-panel");
          $("body").removeClass("open");
          $(".control-panel-container").css("display","none");

           return false;
        }
  });

});

function select2SetValue(obj,value)
{
    if(!obj.find("option[value="+value+"]").length)
    {
        obj.val(function(){
            return $(this).find("option:first").attr("value");
        });
    }
    else
    {
        obj.val(value);
    }

    if(obj.hasClass('select2-hidden-accessible'))
    {
        obj.trigger("change");
    }
}

function dropdownInit(obj, indexTrigger = 0)
{
    $.each(obj, function(key, subObj){
        $(subObj).find(".dropdown-item").click(function(){
            $(subObj).find("[data-toggle='dropdown']").text($(this).text());
        });
    });
    obj.find(".dropdown-item:eq("+indexTrigger+")").trigger("click");
}

    function calculateItem(oldprice=0, discountType=0, discountValue=0, taxPercent=0, quantity=1, cycle=1)
    {
        cycle = (!isNaN(parseInt(cycle)) && parseInt(cycle)>0) ? parseInt(cycle) : 1;
        quantity = !isNaN(parseInt(quantity)) ? parseInt(quantity) : 1;
        oldprice = !isNaN(parseFloat(oldprice)) ? parseFloat(oldprice) : 0;
        taxPercent = !isNaN(parseFloat(taxPercent)) ? parseFloat(taxPercent) : 0;
        discountValue = !isNaN(parseFloat(discountValue)) ? parseFloat(discountValue) : 0;
        discountType = !isNaN(parseInt(discountType)) ? parseInt(discountType) : 0;

        let subTotal = oldprice * cycle * quantity;
        let totalDiscount = discountType == 0 ? (subTotal * discountValue) / 100 : discountValue * quantity;
        let total = subTotal - totalDiscount;
        total = total <= 0 ? 0 : total;
        let totalTax = (total * taxPercent) / 100;
        total = total + totalTax;

        let result = {'subTotal':subTotal,
            'totalDiscount':totalDiscount,
            'totalTax':totalTax,
            'total':total,
        };

        return result;
    }


function call_form_add_store(e) 
{
  e.stopPropagation();

  // Clear form and change title and default type
  clearForm($("#box_add_store form#add_store_form"));
  $("#box_add_store .change_title").html(lang_store_add);
  $("#box_add_store .change_action").html("<input class='btn act_popup_btn_validate' aclass='btn_add_store' type='button' value='"+lang_store_add+"'/>");
  update_type_store(1);

  // add event validate
  validate_form_custom("#add_store_form", ".act_popup_btn_validate", "box_custom");

  // Add return
  var val_return = $(this).parent().attr("checkreturn");
  if( typeof(val_return) == "undefined" || val_return == "" )
  {
    val_return = $(e.currentTarget).parent().attr("checkreturn");
    if(typeof(val_return) == "undefined" || val_return == "")
    {
      $("input[name='checkReturn']").val("");
    }
    else
    {
      $("input[name='checkReturn']").val(val_return);
    }
  }
  else
  {
    $("input[name='checkReturn']").val(val_return);
  }

  // call popup
  $.magnificPopup.open({
    type: 'inline',
    items: {
      src: '#box_add_store'
    },
  });
}

function call_ajax_add_store()
{
  if($("form#add_store_form").validnew())
  {
    // do stuff if form is valid
    var data = $("form#add_store_form").serialize();
    checkDoubleClick(".btn_add_store", 1); // deny click

    // call add store
    $.ajax({
      type: "post",
      url: site_root_domain + "/?site=store&subact=ajax_add_store",
      data: data,
      beforeSend: function(){
        $("form#add_store_form p.error_msg").hide();
        $("form#add_store_form p.error_msg").html('');
      }, 
      success: function(html)
      {
        // create new token
        csrf_token();
        checkDoubleClick(".btn_add_store", 0); // revert click

        var obj = JSON.parse(html);
        if( obj.status == "success" )
        {
          $(".select_store").html(obj.data_option);
          $(".select_store").trigger("change");

          var checkreturn = $("input[name='checkReturn']").val();
          if( checkreturn == "" )
          {
            alertText(obj.msg, "success");
          }
          else
          {
            call_notify("Store", obj.msg, "info", "");
          }

          $(".select_store").trigger("change");

          $.magnificPopup.close();
        }
        else
        {
          $("form#add_store_form p.error_msg").html(obj.msg);
          $("form#add_store_form p.error_msg").show();
          
          pNotifyACP(obj.msg);
          return false;
        }
      }
    });
  } 
  else 
  {
    // do stuff if form is not valid
    validator.focusInvalid();
    return false;
  }
}

function call_form_edit_store(e, store_id) 
{
  e.stopPropagation();

  // Clear form and change title and default type
  clearForm($("#box_add_store form#add_store_form"));
  $("#box_add_store .change_title").html(lang_store_update);
  $("#box_add_store .change_action").html("<input class='btn act_popup_btn_validate' aclass='btn_edit_do_store' type='button' value='"+lang_store_update+"'/>");

  // add event validate
  validate_form_custom("#add_store_form", ".act_popup_btn_validate", "box_custom");

  // Add return
  var val_return = $(this).parent().attr("checkreturn");
  if( typeof(val_return) == "undefined" || val_return == "" )
  {
    val_return = $(e.currentTarget).parent().attr("checkreturn");
    if(typeof(val_return) == "undefined" || val_return == "")
    {
      $("input[name='checkReturn']").val("");
    }
    else
    {
      $("input[name='checkReturn']").val(val_return);
    }
  }
  else
  {
    $("input[name='checkReturn']").val(val_return);
  }

  // Call get info and set data of form and call popup
  // Ajax get data
  if( typeof store_id == "undefined" )
  {
    var store_id = $('.btn_edit_store').attr('id');
  }

  $.ajax({
    type: "post",
    url: site_root_domain + "/?site=store&subact=ajax_get_data_store",
    data: {store_id: store_id},
    success: function(html)
    {
      var obj = JSON.parse(html);
      
      $("#add_store_form input[name='store_id']").val(obj.store_id);
      $("#add_store_form input[name='store_name']").val(obj.store_name);
      $("#add_store_form input[name='store_address']").val(obj.store_address);
      $("#add_store_form select[name='city_id'] option[value='"+obj.city_id+"']").prop("selected",true);
      $("#add_store_form select[name='city_id']").trigger("change");
      $("#add_store_form input[name='store_phone']").val(obj.store_phone);
      $("#add_store_form textarea[name='googlemap_code']").val(obj.googlemap_code);
      $("#add_store_form input[name='store_type'][value='"+obj.store_type+"']").prop("checked", true);
      $("#add_store_form input[name='store_type'][value='"+obj.store_type+"']").trigger('click');
      $("#add_store_form input[name='store_backend_url']").val(obj.store_backend_url);
      $("#add_store_form select[name='store_backend_type'] option[value='"+obj.store_backend_type+"']").prop("selected",true);
      $("#add_store_form select[name='store_backend_type']").trigger("change");
      $("#add_store_form input[name='store_backend_key']").val(obj.store_backend_key);
      $("#add_store_form input[name='store_backend_secret']").val(obj.store_backend_secret);
      $("#add_store_form input[name='store_backend_sync'][value='"+obj.store_backend_sync+"']").prop("checked", true);
      $("#add_store_form input[name='store_backend_sync'][value='"+obj.store_backend_sync+"']").trigger('click');

      // call popup
      $.magnificPopup.open({
        type: 'inline',
        items: {
          src: '#box_add_store'
        },
      });      
    }
  });
}

function call_ajax_edit_store()
{
  if($("form#add_store_form").validnew())
  {
    // do stuff if form is valid
    var data = $("form#add_store_form").serialize();
    checkDoubleClick(".btn_add_store", 1); // deny click

    // call add store
    $.ajax({
      type: "post",
      url: site_root_domain + "/?site=store&subact=ajax_edit_store",
      data: data,
      beforeSend: function(){
        $("form#add_store_form p.error_msg").hide();
        $("form#add_store_form p.error_msg").html('');
      }, 
      success: function(html)
      {
        // create new token
        csrf_token();
        checkDoubleClick(".btn_add_store", 0); // revert click

        var obj = JSON.parse(html);
        if( obj.status == "success" )
        {
          $(".select_store").html(obj.data_option);
          $(".select_store").trigger("change");

          var checkreturn = $("input[name='checkReturn']").val();
          if( checkreturn == "" )
          {
            alertText(obj.msg, "success");
          }
          else
          {
            call_notify("Store", obj.msg, "info", "");
          }

          $(".select_store").trigger("change");

          $.magnificPopup.close();
        }
        else
        {
          $("form#add_store_form p.error_msg").html(obj.msg);
          $("form#add_store_form p.error_msg").show();
          
          pNotifyACP(obj.msg);
          return false;
        }
      }
    });
  } 
  else 
  {
    // do stuff if form is not valid
    validator.focusInvalid();
    return false;
  }
}

function initEventClickWrap(elementId, input, closest)
{
  $('#'+elementId).on('click', '.initEventClickWrap', function(e){
    e.preventDefault();

    if( typeof closest != 'undefined' && closest )
    {
      $(this).closest(closest).find(input).first().trigger('click');
    }
    else
    {
      $(this).parent().find(input).first().trigger('click');
    }
  });
}

function activaTab(tab)
{
  $('.nav a[href="#' + tab + '"]').tab('show');
};

function changeAttr(onthis, name, value)
{
  $(onthis).attr(name, value);
}

/*
* Redirect to url
*/
function redirectUrl ( url, target ) 
{
    // Check target
    if ( typeof target == 'undefined' ) 
    {
        target = '_self';
    }

    // append element
    var redirect_url = 'redirect_url_' + new Date().getTime();
    $('body').append('<div style="display:none;"><a class="' + redirect_url + '" target="' + target + '">&nbsp;</a></div>');

    // Call event
    var redirect = $('.' + redirect_url);
        redirect.attr('href',url);
        if( target == '_blank' )
        {
          redirect.attr('onclick',"window.open('" + url + "', '_blank'); return false;");
        }
        else
        {
          redirect.attr('onclick',"document.location.replace('" + url + "'); return false;");
        }
        redirect.trigger('click');
}

/*
* Scroll to element
*/
function scrollJumpto ( jumpto, headerfixed, redirect ) 
{
    if ( $(jumpto).length > 0 ) // check exits element for jumpto
    {
        // Calculator position and call jumpto with effect
        jumpto = $(jumpto).offset().top;
        headerfixed = ( $(headerfixed).length > 0 ) ? $(headerfixed).height() : 0;

        $('html, body').animate({
            scrollTop: parseInt(jumpto - headerfixed) + 'px'
        }, 500, 'swing');
    }
    else if ( redirect ) // Check exits redirect
    {
        // Call redirect
        redirectUrl(redirect);
        return true;
    }
    else
    {
        console.log(jumpto + ' Not found.');
    }
}

/**
* open print
*/
function composePrint( url, callback )
{
  var box_preview = $("#box_preview");
  var box_compose = $("#box_compose");

  if( !callback )
  {
    // Hide old modal
    box_preview.modal('hide');

    // Show new modal
    box_compose.modal();

    setModalOpen();
  }

  // Inputs
  if( url )
  {
    box_compose.find('#url_compose').val(url).attr('value', url);
  }
  url = url ? url : box_preview.find('#url_compose').val();
  box_compose.attr('ref', url);

  // Render compose
  box_preview.find('#url_preview').val('').attr('value', '');
  box_preview.find('#url_send_email').val('').attr('value', '');

  box_compose.find('.loading_content').show();
  box_compose.find('.main_content').hide();
  box_compose.find('.btn_preview_print').attr('disabled', 'disabled');

  tinymce.get('content_compose').setContent("");

  $.ajax({
      type: 'post',
      url: url,
      data: {},
      dataType: 'json',
      success: function(res)
      {
        tinymce.get('content_compose').setContent(res.content);
        box_preview.find('#url_preview').val(res.urlPreview).attr('value', res.urlPreview);
        box_preview.find('#url_send_email').val(res.urlSendEmail).attr('value', res.urlSendEmail);
        
        box_compose.find('.loading_content').hide();
        box_compose.find('.main_content').show();
        box_compose.find('.btn_preview_print').removeAttr('disabled');

        if( callback )
        {
          var x = eval(callback)
          if( typeof x == 'function' )
          {
            x();
          }
          else
          {
            call_notify("Notification", "Error when render content", "danger");
          }
        }
      }
  });
}

function previewPrint( url, filePath )
{
  var box_compose = $("#box_compose");
  var box_preview = $("#box_preview");

  // Hide old modal
  box_compose.modal('hide');

  // Show new modal
  box_preview.modal();
  setModalOpen();

  // Inputs
  tinymce.triggerSave(); // Important save content before get
  var content = tinymce.get('content_compose').getContent();

  if( url )
  {
    box_preview.find('#url_preview').val(url).attr('value', url);
  }
  url = url ? url : box_preview.find('#url_preview').val();
  box_preview.attr('ref', url);

  // Render pdf
  box_preview.find('.loading_content').show();
  box_preview.find('.main_content').hide();
  box_preview.find('.btn_do_print').attr('disabled', 'disabled');
  box_preview.find('.btn_send_email').attr('disabled', 'disabled');
  box_preview.find('#tab_box_review iframe').attr('src', '');

  $.ajax({
    type: 'post',
    url: url,
    data: {
      'content': content,
      'file' : filePath,
    },
    dataType: 'json',
    success: function (res)
    {
      if( res.status == 'ok' )
      {
        // Set data preview tab
        var previewTab = box_preview.find('#tab_box_review');
            previewTab.find('iframe').attr('src', res.fileUrl);

        //Set data send email tab
        var emailTab = box_preview.find('#tab_send_email');
            emailTab.find('[name="email_from"]').val(res.email.email_from).attr('value', res.email.email_from);
            emailTab.find('[name="email_to"]').val(res.email.email_to).attr('value', res.email.email_to);
            emailTab.find('[name="email_cc"]').val(res.email.email_cc).attr('value', res.email.email_cc);
            emailTab.find('[name="email_bcc"]').val(res.email.email_bcc).attr('value', res.email.email_bcc);
            emailTab.find('[name="email_title"]').val(res.email.email_title).attr('value', res.email.email_title);
          
        // Set textarea
        tinymce.get('send_file_email_content').setContent(res.email.email_content);

        // Set data back to
        if( box_preview.find('input[name=backTo]').length > 0 )
        {
          if( filePath )
          {
            box_preview.find('input[name=backTo]').val('getExportFiles').attr('value', 'getExportFiles');
          }
          else
          {
            box_preview.find('input[name=backTo]').val('box_compose_invoice').attr('value', 'box_compose');
          }
        }

        box_preview.find('.loading_content').hide();
        box_preview.find('.main_content').show();
        box_preview.find('.btn_do_print').removeAttr('disabled');
        box_preview.find('.btn_send_email').removeAttr('disabled');
      }
      else
      {
        call_notify("Notification", res.msg, "danger", "");
      }
    }
  });
}
function backToComposePrint()
{
  var box_preview = $('#box_preview');
      box_preview.modal('hide');

  var backToVal = box_preview.find("input[name='backTo']:first").val();
  if( typeof backToVal == 'undefined' || !backToVal )
  {
    backToVal = 'box_compose';
  }

  $("#"+backToVal).modal('show');
  setModalOpen();
}
function doPrint()
{
  var src = $('#box_preview #tab_box_review').find('iframe:first').attr('src');
  window.open(src, '_blank');
}
function sendEmailPrint( url )
{
  var email_tab = $("#tab_send_email");
  var email_to = email_tab.find("[name='email_to']").val();
  var email_from = email_tab.find("[name='email_from']").val();
  var email_cc = email_tab.find("[name='email_cc']").val();
  var email_bcc = email_tab.find("[name='email_bcc']").val();
  var email_title = email_tab.find("[name='email_title']").val();
  var email_content = tinymce.get('send_file_email_content').getContent();

  var box_preview = $('#box_preview');
  var ord_id = box_preview.find('#ord_id').val();
  var backTo = box_preview.find('input[name=backTo]:first').val();
  var iFrame = box_preview.find('iframe').attr('src');

  if( url )
  {
    box_preview.find('#url_send_email').val(url).attr('value', url);
  }
  url = url ? url : box_preview.find('#url_send_email').val();

  // Ajax
  $.ajax({
    type: 'post',
    url: url,
    dataType: 'json',
    data: {
      email_to: email_to,
      email_from: email_from,
      email_cc: email_cc,
      email_bcc: email_bcc,
      email_title: email_title,
      email_content: email_content,
      backTo: backTo,
      iFrame: iFrame,
    },
    beforeSend: function(){
      $("#sendEmailLoading").show();
    }, 
    success: function (res)
    {
      $("#sendEmailLoading").hide();

      if( res.status == 'success' )
      {
        alertText(res.msg, "success", null, 'bootrap-modal', $('#box_preview'));
      }
      else
      {
        alertText(res.msg, "error", null, 'bootrap-modal', $('#box_preview'));
      }
    }
  });
}

function openPreviewPrint( url )
{
  var box_compose = $("#box_compose");
  var box_preview = $("#box_preview");

  // Hide old modal
  box_compose.modal('hide');

  // Show new modal
  box_preview.modal();
  setModalOpen();
  
  box_preview.find('.loading_content').show();
  box_preview.find('.main_content').hide();
  box_preview.find('.btn_do_print').attr('disabled', 'disabled');
  box_preview.find('.btn_send_email').attr('disabled', 'disabled');

  // Set content for compose
  composePrint( url, previewPrint );
}

function doGeneralSearch( element, url )
{
  if( !url )
  {
    console.log('missed url');
    return false;
  }
  
  if( !url.includes('?') )
  {
    url += '?';
  }
  url = site_root_domain + url.replace(site_root_domain, '');

  var items = $(element).find('input, select, textarea');
  items.each(function(){
    var _this = $(this);
    url += '&'+_this.attr('name')+'='+_this.val();
  });
  redirectUrl(url);
}

/**
* Mouse up out side div
*/
function setEventMouseUpOutSize( element = "", element_event = "" )
{
    var container = $(element);
    var container_event = $(element_event);

    if( container.length > 0 )
    {
        if( container_event.length <= 0 )
        {
            container_event = container;
        }
        
        $(document).mouseup(function(e) 
        {
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                $(element_event).hide();
            }
        });
    }
}

/*
* Auto completed quick search
*/
function autocompleteNew( element_input, element_timer, url, data )
{ 
    if ( typeof element_input == 'undefined' || !element_input || $(element_input).length <= 0 || typeof element_timer == 'undefined' ) 
    {
        console.log('miss input element');
        return false; 
    }

    //on keyup, start the countdown
    $(element_input).on('keyup', function () {
        if( element_timer )
        {
            clearTimeout(element_timer);
        }

        $(element_input+'_loading').css({
            "background-image": "url('/acp/images/fb-loading.gif')", 
            "background-position-x": "165px", 
            "background-position-y": "center", 
            "background-repeat": "no-repeat", 
            "background-attachment": "scroll", 
            "background-size": "auto auto", 
            "background-origin": "padding-box", 
            "background-clip": "border-box", 
        });
        element_timer = setTimeout(function(){
            autocompleteNewDo( element_input, url, data ) 
        }, 300);
    });
    
    //on keydown, clear the countdown 
    $(element_input).on('keydown', function () 
    {
        clearTimeout(element_timer);
    });
}

//user is "finished typing," do something
function autocompleteNewDo( element_input, url, data ) 
{
    if( typeof url == 'undefined' || !url )
    {
        console.log('missed url');
        return false;
    }

    if ( typeof data == 'undefined' ) 
    {
        data = {};
    }

    var _this = $(element_input);
    var keywords = _this.val();

    // Add keyword
    data.keywords = keywords;

    // Ajax
    $.ajax({
        type: 'post',
        url: site_root_domain + url.replace(site_root_domain, ''),
        data: data,
        beforeSend: function(){
            $(element_input+'_loading').css({
                "background-image": "url('/acp/images/fb-loading.gif')", 
                "background-position-x": "165px", 
                "background-position-y": "center", 
                "background-repeat": "no-repeat", 
                "background-attachment": "scroll", 
                "background-size": "auto auto", 
                "background-origin": "padding-box", 
                "background-clip": "border-box", 
            });
        },
        success: function(data){
            var obj = JSON.parse(data);
            
            if ( obj.status == "success" )
            {
                $(element_input+'_suggestion').html(obj.data);
                $(element_input+'_suggestion').show();
            }

            $(element_input+'_loading').css({
                "background-image": "", 
                "background-position-x": "", 
                "background-position-y": "", 
                "background-repeat": "", 
                "background-attachment": "", 
                "background-size": "", 
                "background-origin": "", 
                "background-clip": "", 
            });
        }
    });
}