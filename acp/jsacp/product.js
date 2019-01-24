$(document).ready(function() {

  // remove row child product
  $("#combination").on("click", ".remove_cp", function(){
      $(this).parents("tr").remove();
  });
  
    $('body').click(function() {
       $('#suggesstion-box').html(""); 
       $("#suggesstion-box").css("display","none");
       
    });

    $('#suggesstion-box').click(function(event){
       event.stopPropagation();
    });

  autocomplete_quick_search();
  var p_group_default = $("select[name='p_product_group']").attr("defaultvalue");
 
    $('select#p_product_group option[value="'+p_group_default+'"]').attr("selected",true);

 $("form#form-signin_v1 [name='p_type']").change(function() {
 
    if ($("[name='p_type']").val() != 0) {
      $($("#p_cycle")).empty();
      $($("#p_cycle")).html("<option value='1'>"+cms_lang['gmonthly']+"</option><option value='2'>"+cms_lang['gyearly']+"</option>");
    } else {
      $($("#p_cycle")).empty();
      $($("#p_cycle")).html("<option value='1' selected>"+cms_lang['gonce']+"</option>");
    }
 
      var p_type =$("[name='p_type']").val() ;
 
      get_group_product(p_type);

      
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
 

});

function product_submit_saveoption()
{

  $("tbody#data_table tr.child").remove();
  $("#form-signin_v1 input[name='add_product_option']").attr('value','1');
}

function product_submit()
{
 
  $("tbody#data_table tr.child").remove();
  // $("#form-signin_v1 input[name='add_product_option']").attr('value','1');
}



function autocomplete_quick_search()
{
  
  $("#p_quick_search").keyup(function(){

    if($(this).val().length < 2)
    {
       return false;
    }
    var key = $(this).val();
    var p_type = $(this).attr("p_type");
    if(p_type == 0)//product
    {
      var type_site = "product";
    }
    else
    {
      var type_site = "service";
    }
    $.ajax({
    type: "post",
    url: site_root_domain+"/?site="+type_site+"&subact=autocomplete_quick_search",
    data:{ product_keyword : key },
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

function autosubmit_frm_qs_product()
{
  $("form#frm_quickserch_product").submit();
}


 function get_group_product(type, defaultVal = '')
 {
    if(type == 0)
    {
      var site_p = "product";
    }
    else
    {
       var site_p = "service";
    }
 
   $.ajax({
                type: "post",
                url: site_root_domain+"/?site="+site_p+"&subact=search_g_product_ajax",
                data: {p_type: type},
                success: function(html)
                {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        output_li = "<option value=''>--- "+cms_lang['gselect']+" ---</option>";
                        $.each(obj.data_option, function(index, value)
                        {
                            output_li += "<option   value='"+value.product_group_id+"' > "+value.product_group_name+"</option>";
                          //  var data_item = value.data_item; 
                            if(value.data_item)
                            {
                              $.each(value.data_item, function(index_2, value_2)
                              {

                                output_li += "<option   value='"+value_2.product_group_id+"' >|__ "+value_2.product_group_name+"</option>";
                              });
                            }
                        });
                    }
  
                    $("#p_product_group").html(output_li);
                    select2SetValue($("#p_product_group"), defaultVal);
                    select2SetValue($("form#form-signin_v1 select[name='pgroup_id']"), obj.pg_id);
                    
                }
            });
 

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
                    // console.log(output_li);
                    var html_show = "<ul class='list_goods'>"
                                  +   output_li
                                  + "</ul>";
      // console.log(obj);
                    obj_element.parent().parent().find(".box_result_find").html(html_show);
                    obj_element.parent().parent().find(".box_result_find").css("display","block");
                }
            });
      });

  function show_detail_product_2(obj) 
  {
 
      var name_product = obj.getAttribute("name");
      var description = obj.getAttribute("description");
      var price = obj.getAttribute("price");
      var forkey = obj.getAttribute("for");
      var id = obj.getAttribute("id_product");
      var sku = obj.getAttribute("sku");
      // console.log(description); 
      $(".row-grid.active").find("input[for='dgrid-2']").val(name_product);
      $(".row-grid.active").find("input[for='dgrid-3']").val(sku);
      $(".row-grid.active").find("textarea[for='dgrid-4']").val(description);
      $(".row-grid.active").find("input[for='dgrid-5']").val(1);
      $(".row-grid.active").find("input[for='dgrid-6']").val(price);
      $(".row-grid.active").find("input.product_id").val(id);
      $(".box_result_find").html("");
       $("#data_table .box_result_find").css("display","none");

       calculate_money_subitem_product();

  }


function extra_pname(onthis)
{
  // var op_pgname = $(onthis).val();
  // var text_pgname = $(onthis).find(":selected").attr("text");
  // var type_pgname = $(onthis).find(":selected").attr("type");
 
  // if(type_pgname == 1) // Root   cate
  // {
  //   $("#form-signin_v1 input[name=p_name]").val(text_pgname).attr("disabled","disabled");
  // }else
  // {
  //   $("#form-signin_v1 input[name=p_name]").val('').removeAttr("disabled","");
  // }
}


function renderAttribute(obj, ele_result, type, ele_error, product_group_id)
{
    var val_choose = $.isNumeric(obj) == true ? obj : $(obj).val();
    // console.log($.isNumeric(obj));
    $(".box_attribute").html("");
    $.ajax({
        type: "post",
        url: site_root_domain + "/?site=product&subact=gethtml_attr",
        data: {attr_group:val_choose, type:type},
        success: function(response)
        {
            // console.log(response);
            $(ele_result).html(response);
            // select 2
            $(".listattr").select2();

            $(ele_error).html('');
            if( $(ele_error).length && ! response )
            {
              var str_msg = cms_lang['notes_product_group_attribute_none'];
                  // str_msg = str_msg.replace('[link]', 'href="'+site_root_domain + "/?site=product_group&pg_type=0&subact=open_popup_edit&id=" + (product_group_id*1) +'" target="_blank"');
                  str_msg = str_msg.replace('[link]', 'class="pointer" onclick="return edit_sub_pgroup('+(product_group_id*1)+', event, \'\', \'\', \'none\');"');
                  
              var ele_error_html = `
              <div class="alert box_alert" role="alert" style="border-color: #cccccc; background-color: #e7e7e7; color: #ff5200;">
                <i class="font-icon font-icon-warning"></i>
                <span>`+str_msg+`</span>
              </div>
              `;
              $(ele_error).html(ele_error_html);
            }
        }
    });

}

function changelistAttribute(obj)
{
    var val_choose = $(obj).find("option:selected").attr("attrgroup");
    var val_id = $(obj).find("option:selected").attr("value"); // product group id

    // Select attr group
    $("select[name='attr_group'] option[value='"+val_choose+"']").prop("selected", true);
    $("select[name='attr_group']").trigger("change");

    // Apply bên attribute
    renderAttribute(val_choose, '.result_attribute', 1, '.alert_attribute', val_id);

    // Auto check attribute
    if( typeof(list_attribute) != "undefined" && $.isEmptyObject(list_attribute) == false )
    {
      setTimeout(function(){
          for(var x in list_attribute)
          {
              select2SetValue($("select.attr_"+x), list_attribute[x]);
          }
      }, 5000);
    }


}

function changeCombination(obj)
{
  var count_arr = 0;
  var arrloop = [];
  $(".clist").each(function(){
      var checkinput = $(this).val();
      // console.log(checkinput);
      if(checkinput != null)
      {
        // Danh sách số thứ tự sẽ lăp
        arrloop.push($(this).attr("inc"));
        count_arr++;
      }
  });
// console.log(arrloop);
  var inc = 0;
  var crr_val = [];
  crr_val.id = "";
  crr_val.name = "";
  crr_val.name_ext = "";
  crr_val.code_ext = "";
  crr_val.html_tr = "";
  $("#combination").html("");
  loopv(crr_val, count_arr, arrloop, inc);
// console.log(data);
  // var list_a = [1,5,"táo","mận", "đào"];
  // var list_b = ["chuối","bơ", "mít"];
  // var list_c = ["đào", "sampoche"];
  // var list_d = ["đu dủ", "ổi", 3];
  // var list_e = [9, 2, "nho", "xoài"]; 
  // var count = 5;

  // var str = "";
  // for(var x in list_a)
  // {
  //   for(var j in list_b)
  //   {
  //     for(var k in list_c)
  //     {
  //       for (var l in list_d) 
  //       {
  //         for(var m in list_e)
  //         {
  //           str = list_a[x]+" "+list_b[j] + " "+list_c[k] + " "+list_d[l] + " "+list_e[m];
  //           console.log(str+"\n");
  //         }
  //       }
  //     }
  //   }
    

  // }





}



function loopv(crr_val, count, arrloop, inc)
{
  var arrele = $("#sel_"+arrloop[inc]).val();
  // console.log(arrele);
  if(typeof(arrele) !== "undefined")
  {
    for (var x in arrele)
    {
      // console.log(arrele[x]);
      var data = JSON.parse(arrele[x]);
      var namegen = data['name']+data['unit'];
      var nameOption = crr_val.name ? crr_val.name+ " " +namegen : namegen;
      var nameExt = crr_val.name_ext ? (crr_val.name_ext+ " " +namegen) : " "+namegen;
      var codeOption = remove_unicode(nameOption);
          codeOption = codeOption.replace(/\s/g, '');
      var new_arr = [];
      // Change value
      new_arr.id = crr_val.id ? crr_val.id+","+data['id'] : data['id'];
      new_arr.attr_id = crr_val.attr_id ? crr_val.attr_id+","+data['attr_id'] : data['attr_id'];
      new_arr.name = nameOption;
      new_arr.name_ext = nameExt;
      new_arr.code_ext = "_"+codeOption;
      // console.log(new_arr.attr_id);

      // new_arr.html_tr = new_arr.html_tr+html_tr;
      var html_tr = '<tr>'
                    + '<td>'
                    + '<a class="remove_cp"><i class="fa fa-trash"></i></a>'
                    + '</td>'
                    + '<td>'
                    + '<span>'+new_arr.name+'</span>'
                    + '<input type="hidden" name="cp_attr_id[]" value="'+new_arr.attr_id+'" />'
                    + '<input type="hidden" name="cp_option_id[]" value="'+new_arr.id+'" />'
                    + '<input type="hidden" name="cp_name_ext[]" value="'+new_arr.name_ext+'" />'
                    + '<input type="hidden" name="cp_code_ext[]" value="'+new_arr.code_ext+'" />'
                    + '</td>'
                    + '<td>'
                    + '<span>- '+new_arr.name_ext+'</span>'
                    + '</td>'
                    + '<td>'
                    + '<span>'+new_arr.code_ext+'</span>'
                    + '</td>'
                    + '<td>'
                    + '<input class="form-control" type="number" min="0" name="cp_quantity[] value="" /></span>'
                    + '</td>'
                    +'</tr>';

      // Array.isArray(data) == true && 
      if(count > inc + 1) 
      {
        loopv(new_arr, count, arrloop, ++inc);
        --inc;
      }else
      {
        // console.log(new_arr);
        // Đổ html
        $("#combination").append(html_tr);
      }
      
    }

  }
  
}

function remove_unicode(str) 
{  
   str= str.toLowerCase();  
   str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");  
   str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");  
   str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");  
   str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");  
   str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");  
   str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");  
   str= str.replace(/đ/g,"d");  
   str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g,"-"); 
 
   str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1- 
   str= str.replace(/^\-+|\-+$/g,"");  
 
   return str;  
} 

function isObject(o) {
  return o instanceof Object && o.constructor === Object;
}


function autoProductSearch(selector, urls, input_receive, min = 2)
{
    $(selector).autocomplete({
      source: urls,
      minLength: min,
      // selectFirst: true,
      // autoFill: true,
      select: function( event, ui ) {
        // console.log($(selector));
        event.preventDefault();
        $(input_receive).val(ui.item.product_id);
        $(this).val(ui.item.product_name_show);
        
      }, change: function(event, ui)
      {
        // console.log(ui);
        if(ui.item==null)
        {
          $(this).val('');
          $(input_receive).val("");
        }
      }
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        var html = `<li class="ui-menu-item"><div class="ui-menu-item-wrapper">` + item.product_name_show + `</div></li>`;
      return $( html)
        .appendTo( ul );
    };

}



function changeCombinationOption()
{
  var option_value1 = $("#option_value1").val();
  var option_value2 = $("#option_value2").val();
  var option_value3 = $("#option_value3").val();

  var option1 = $("#option1").val();
  var option2 = $("#option2").val();
  var option3 = $("#option3").val();
  var name_product = $("input[name='product_name']").val();
  option_value1 = option_value1 ? option_value1 : [];
  option_value2 = option_value2 ? option_value2 : [];
  option_value3 = option_value3 ? option_value3 : [];
// console.log(option_value2); return false;
  // check color
  
  $("#combination_option").html("");
  var pre_curency = currency_type ? currency_type : "$";
  var variant_name = "";
  var sku_prefix = $("input[name='p_sku']").val();
  var baseCost = $("input[name='p_price']").val();
  var baseCost_show = baseCost ? pre_curency+baseCost : "";
  
  var html_row = "";
  var count_variant = 0;
  // array option 1
  if(option_value1.length > 0)
  {
    $(".th_option1").html(option1);
    for(var x in option_value1)
    {
        var nOption1 = option_value1[x]+"";
            nOption1 = nOption1.toUpperCase();
        // array option 2
        if(option_value2.length > 0)
        {
          $(".th_option2").html(option2);
          $(".th_option2").show();
          for(var j in option_value2)
          {
            var nOption2 = option_value2[j]+"";
                nOption2 = nOption2.toUpperCase();
            // array option 3
            if(option_value3.length > 0)
            {
              $(".th_option3").html(option3);
              $(".th_option3").show();

              for(var k in option_value3)
              {
                var nOption3 = option_value3[k]+"";
                    nOption3 = nOption3.toUpperCase();

                variant_name = nOption1 + " " + nOption2 + " " + nOption3 ;
                variant_name = variant_name.toUpperCase();
                var sku_name = getCodeSku(nOption1, nOption2);
                sku_name = sku_prefix+"-"+sku_name;
                sku_name = sku_name.toUpperCase();
                
                // Tăng count vairiant
                count_variant ++ ;
                // Create html row
                html_row += htmlVariant(variant_name, sku_name, pre_curency, baseCost_show, baseCost, nOption1, nOption2, nOption3);

              }// End for option value 3

            }else{
                $(".th_option3").hide();
                variant_name = nOption1 + " " + nOption2;
                variant_name = variant_name.toUpperCase();
                var sku_name = getCodeSku(nOption1, nOption2);
                sku_name = sku_prefix+"-"+sku_name;
                sku_name = sku_name.toUpperCase();
                
                // Tăng count vairiant
                count_variant ++ ;
                // Create html row
                html_row += htmlVariant(variant_name, sku_name, pre_curency, baseCost_show, baseCost, nOption1, nOption2);

            }// End if option value 3


          }// End for option value 2

        }else {
          $(".th_option2").hide();
          variant_name = nOption1;
          variant_name = variant_name.toUpperCase();
          var sku_name = getCodeSku(nOption1);
          sku_name = sku_prefix+"-"+sku_name;
          sku_name = sku_name.toUpperCase();
          
          // Tăng count vairiant
          count_variant ++ ;
          // Create html row
          html_row += htmlVariant(variant_name, sku_name, pre_curency, baseCost_show, baseCost, nOption1);
        }// End if option value 2


      }// End for option value 1
  } // End if option value 1
  var title_varitan = "Creating "+ name_product + (count_variant > 0 ? " (<span class='num_variants'>"+count_variant+"</span> Variants)" : "");
  // Thay title variant
  $(".title_variant").html(title_varitan);
  // Đổ html
  $("#combination_option").html(html_row);
  
}

// Html variant
function htmlVariant(variant_name, sku_name, pre_curency, baseCost_show, baseCost, option_value1, option_value2, option_value3)
{
  var html_row = '<tr>'+
                '<td><span>'+variant_name+'</span><input value="'+variant_name+'" name="variant_name[]" type="text" style="display:none;" class="form-control input_variant" /></td>'+ 
                '<td><span>'+sku_name+'</span><input value="'+sku_name+'" name="variant_sku[]" type="text" style="display:none;" class="form-control input_variant" /></td>'+
                '<td><span>'+baseCost_show+'</span><input onkeypress="return btnVariant.cNumber(event, this);" maxlength="7" data-option-front="'+pre_curency+'" value="'+baseCost+'" name="variant_basecost[]" style="display:none;" class="form-control input_variant" /></td>'+
                '<td><span>'+option_value1+'</span><input name="variant_option1[]" value="'+option_value1+'" type="text" style="display:none;" class="form-control input_variant" /></td>';  
    if(option_value2)
    {
      html_row += '<td><span>'+option_value2+'</span><input name="variant_option2[]" value="'+option_value2+'" type="text" style="display:none;" class="form-control input_variant" /></td>';
    }

    if(option_value3)
    {
      html_row += '<td><span>'+option_value3+'</span><input name="variant_option3[]" value="'+option_value3+'" type="text" style="display:none;" class="form-control input_variant" /></td>';
    }

      html_row +='<td>'+
                  '<div class="btn-group btn-group-hide">'+
                    '<button type="button" class="btn" onclick="btnVariant.editRow(this);"><i class="fa fa-edit"></i></button>'+
                    '<button type="button" class="btn" onclick="btnVariant.deleteRow(this);"><i class="fa fa-trash"></i></button>'+
                  '</div>'+
                '</td>'+
              '</tr>';
  return html_row;
}

function getCodeSku(color, size)
{
  var str_output = "";
  // xu ly color
  var arr = color.split(" ");
  
  if(arr.length > 1)
  {
    // xư lý chuỗi đầu
    var word_1 = arr[0];
    var char_1 = word_1.slice(0, 1);
    var char_2 = word_1.slice(-1);
    // xử lý chuổi sau
    var word_2 = arr[1];
    var chword_2 = word_2.slice(0, 1);

    // output
    str_output = char_1+char_2+chword_2;   
  }else{
    var word_1 = arr[0];
    var char_1 = word_1.slice(0, 2);
    var char_2 = word_1.slice(-1);
    str_output = char_1+char_2;
  }

  // full code
  str_output = size ? str_output + "-"+size : str_output;

  return str_output;
}


var countOption=1;
var defaultOption=["Color","Size",""];
var OpVariants = {
      init: function(){
      },
      addOption: function(divClone, divAppend, obj) {
        // divClone is a class name and limit 3
        // divAppend is a class name
        var htmlClone = $("."+divClone).clone();
        
        // remove Control
        if(countOption>=2)
        {
          $(obj).hide();
        }

        if(countOption<=2)
        {
          countOption ++;
          $(htmlClone).removeClass("html_clone");
          $(htmlClone).css("display","flex");
          // // replace name
          $(htmlClone).find(".option_name").attr("name", "option"+countOption);
          $(htmlClone).find(".option_name").attr("id", "option"+countOption);
          $(htmlClone).find(".option_name").val(defaultOption[countOption-1]);
          // console.log(htmlClone);
          // set up select2
          $(htmlClone).find(".option_value").attr("name", "option_value"+countOption+"[]");
          $(htmlClone).find(".option_value").attr("id", "option_value"+countOption);
          
          $(htmlClone).find(".box_deloption").html('<a class="btn btn_deloption" onclick="OpVariants.delOption(\'line_option\', \'btn_addoption\', this)"><i class="fa fa-trash"></i></a>');
          $("."+divAppend).append(htmlClone);
          $(htmlClone).find(".option_value").select2({tags: true, placeholder: function(){
              $(this).data('placeholder');
          }});
        }

        // check change 
        changeCombinationOption();
      },
      delOption: function(divClone, btnAdd, obj) {
        // divClone is a class name
        // btnAdd is a class name
        // Remove line
        $(obj).parents(".line_option").first().remove();
        // show btnAdd
        if($("."+divClone).length <= 3)
        {
            $("."+btnAdd).show();
        }

        // down
        countOption --;
        // check Change 
        changeCombinationOption();
      },
      arrangeOption: function(classOrder) {
        // classOrder is element change rank
        var inc = 0;
        if($("."+classOrder).length > 0)
        {
          $("."+classOrder).each(function(){
            inc++;
            $(this).attr("inc", inc);
          });
        }

      }
}

var btnVariant = {
      editRow: function(obj) {
        var parentTr = $(obj).parents("tr");
        parentTr.find("span").hide();
        parentTr.find("input").show();
        
        var parentdiv = $(obj).parent();
        var html_button_save = '<button type="button" class="btn" onclick="btnVariant.saveRow(this);"><i class="fa fa-check"></i></button>';
        parentdiv.prepend(html_button_save);
        $(obj).remove();
      },
      saveRow: function(obj){
        var parentTr = $(obj).parents("tr");
        var data = parentTr.find(".input_variant");
        $.each(data, function(k,v){
          // console.log($(v));return false;
            var value_input = $(v).val();
            var unit_front = typeof($(v).attr("data-option-front")) != "undefined" ? $(v).attr("data-option-front") : "";
            var unit_back = typeof($(v).attr("data-option-back")) != "undefined" ? $(v).attr("data-option-back") : "";
            var value_show = value_input ? unit_front+value_input+unit_back : "";
            // show value
            $(v).parent().find("span").html(value_show).show();
            // hide input
            $(v).hide();
        })
        
        var parentdiv = $(obj).parent();
        var html_button_edit = '<button type="button" class="btn" onclick="btnVariant.editRow(this);"><i class="fa fa-edit"></i></button>';
        parentdiv.prepend(html_button_edit);
        $(obj).remove();
      },
      deleteRow: function(obj){
          swal({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then(function () {
                // remove tr
                $(obj).parents("tr").remove();
                // change title variants
                var count_variant = $("tbody#combination tr").length; 
                $(".title_variant .num_variants").html(count_variant);                                   
             }); 
      },
      cNumber: function(evt, onthis){
          if(isNaN(onthis.value+""+String.fromCharCode(evt.charCode))) 
          {
             return false; 
          }
      }
} 
