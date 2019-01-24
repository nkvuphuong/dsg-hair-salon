$(document).ready(function(){

  // Check show mobile
  checkShowLineByMobile();
  // Validate form
  validateForm("#popup_attribute","a.add_attr", "add_attribute");
  // tooltip bootstrap
  $('[data-toggle="tooltip"]').tooltip();
  // Tạo expand 
  createExpand(".content");
  $(".box_addtribute").on("click",".view_more", function(){
      var check = isNaN(parseInt($(this).attr("check"))) ? 0 : parseInt($(this).attr("check"));
      if(check == 0)
      {
          $(this).parent().find(".content").css("height","auto");
          $(this).html("-- collapse --");
          $(this).attr("check", 1);
      }else
      {
          $(this).parent().find(".content").css("height","30px");
          $(this).html("-- expand --");
          $(this).attr("check", 0);
      }
  });

  // Btn close magnific popup
  $("#box_attribute").on("click",".btn_cancel", function(){
    $.magnificPopup.close();
    $(this).parents("form")[0].reset();
  });

  // Btn more option
  $(".box_addtribute").on("click",".more_option", function(){
      var id_attr = $(this).attr("id");
      // trigger tab attribute
      $(".nav-item .nav-link:first").trigger("click");
      // Get information attribute
      $.ajax({
          type: "post",
          url: site_root_domain + "/?site=attribute&subact=getinfo_attribute",
          data: {id: id_attr},
          beforeSend: function(){},
          dataType: "json",
          error: function(xhr,status,error){
            // Show error in Console
            console.log(xhr.responseText);
          },
          success: function(obj){

            // data
            // var obj = JSON.parse(response);
            var option_list = obj.attribute_options;
            // input
            var action = site_root_domain + "/?site=attribute&subact=edit_attr&id="+id_attr;
            var title = "Edit attribute";
            var btn_html = "<a class='btn btn-primary pull-left edit_attr' type='edit' style='margin-left: 10px;'>Save</a><a class='btn btn-default pull-left btn_cancel' style='margin-left: 10px;'>Close</a>";

            $.magnificPopup.open({
              items: {
                src: '#box_attribute', 
              },
              type: 'inline'
            });

            // html options list
            var html_option = "";
            var countn = 1;
            if(option_list.length > 0)
            {
              for(var x in option_list)
              {
                  html_option += '<div class="line_option row">'
                                +      '<div class="col-md-1 col-xs-1">'
                                +          '<span class="number">'+countn+'</span>'
                                +      '</div>'
                                +      '<div class="col-md-6 col-xs-5">'
                                +          '<input class="form-control line_input" value="'+option_list[x].options_name+'" name="options_name[]" type="text" placeholder="Options name " />'
                                +          '<input type="hidden" class="line_input" value="'+option_list[x].options_id+'" name="options_id[]">'
                                +      '</div>'
                                +      '<div class="col-md-3 col-xs-5">'
                                +          '<input class="form-control line_input" value="'+option_list[x].options_value+'" name="options_value[]" type="text" placeholder="Options value" />'
                                +      '</div>'
                                +      '<div class="col-md-2 col-xs-1">'
                                +          '<div class="list_control">'
                                +              '<a class="option_add" data-toggle="tooltip" title="Add new line" onclick="addNewLine(this);"><i class="fa fa-plus-circle"></i></a>'
                                +              '<a class="option_del" data-toggle="tooltip" title="Delete this option" id="'+option_list[x].options_id+'" onclick="delLine(this);"><i class="fa fa-trash"></i></a>'
                                +          '</div>'
                                +      '</div>'
                                +  '</div>';
                  countn ++;
              };
            }else
            {
              html_option += '<div class="line_option row">'
                                +      '<div class="col-md-1 col-xs-1">'
                                +          '<span class="number">1</span>'
                                +      '</div>'
                                +      '<div class="col-md-6 col-xs-5">'
                                +          '<input class="form-control line_input" value="" name="options_name[]" type="text" placeholder="Options name " />'
                                +          '<input type="hidden" class="line_input" value="" name="options_id[]">'
                                +      '</div>'
                                +      '<div class="col-md-3 col-xs-5">'
                                +          '<input class="form-control line_input" value="" name="options_value[]" type="text" placeholder="Options value" />'
                                +      '</div>'
                                +      '<div class="col-md-2 col-xs-1">'
                                +          '<div class="list_control">'
                                +              '<a class="option_add" data-toggle="tooltip" title="Add new line" onclick="addNewLine(this);"><i class="fa fa-plus-circle"></i></a>'
                                +              '<a class="option_del" data-toggle="tooltip" title="Delete this option" id="" onclick="delLine(this);"><i class="fa fa-trash"></i></a>'
                                +          '</div>'
                                +      '</div>'
                                +  '</div>';
            }

            // Push data
            var form = "form#popup_attribute";
            $(form + " input[name='attr_name']").val(obj.attr_name);
            $(form + " input[name='attr_key']").val(obj.attr_key);
            // $(form + " textarea[name='attr_value']").val(obj.attr_value);
            $(form + " input[name='attr_unit']").val(obj.attr_unit);
            $(form + " input[name='attr_order']").val(obj.attr_order);
            $(form + " input[name='attr_description']").val(obj.attr_description);
            $(form + " select[name='attr_type']").attr("defaultvalue", obj.attr_type);
            $(form + " select[name='attr_type']").val(obj.attr_type);
            $(form + " input[name='attr_required'][value='"+obj.attr_required+"']").prop("checked", true);
            $(form + " input[name='attr_search'][value='"+obj.attr_search+"']").prop("checked", true);
            $(form + " .box_control").html(btn_html);
            $(form + " .title_form").html(title);
            $(form + " .box_line_option").html(html_option);
            $(form).attr("action", action);
            validateForm(form,"a.edit_attr", "edit_attribute"); 

          },
      });
  });

  // Btn add attribute
  $('.attribute-add').magnificPopup({
      type: 'inline',
      preloader: false,
      focus: '#name',
      callbacks: {
          beforeOpen: function() {
              if($(window).width() < 700) {
                  this.st.focus = false;
              } else {
                  this.st.focus = '#name';
              }

              // trigger tab attribute
              $(".nav-item .nav-link:first").trigger("click");

              // Reset form
              var action = site_root_domain + "/?site=attribute&subact=add_attr";
              var title = "Add attribute";
              var btn_html = "<a class='btn btn-primary pull-left add_attr' type='add' style='margin-left: 10px;'>Save & close</a><a class='btn btn-primary pull-left add_attr' type='add_more' style='margin-left: 10px;'>Save & add</a><a class='btn btn-default pull-left btn_cancel' style='margin-left: 10px;'>Close</a>";

              var form = "form#popup_attribute";
              $(form)[0].reset();
              $(form).find(".box_line_option .line_option").not(":first").remove();
              $(form).find(".box_line_option .line_option input").val("");
              $(form).find(".box_line_option .line_option .option_del").attr("id","");

              $(form + " .box_control").html(btn_html);
              $(form + " .title_form").html(title);
              $(form).attr("action", action);
              validateForm("#popup_attribute","a.add_attr", "add_attribute");
          }
      }
  });

  // Expand
  $(".box_addtribute").on("click",".expand", function(){
      var check = isNaN(parseInt($(this).attr("check"))) ? 0 : parseInt($(this).attr("check"));
      $(".box_line .line").fadeOut("slow");
      $(".box_line .expand").html("+");
      $(".box_line .expand").attr("check", 0);


      if(check == 0)
      {
        $(this).parents(".box_line").find(".line").show("slow");
        $(this).attr("check", 1);
        $(this).html("-");
      }else
      {
        $(this).parents(".box_line").find(".line").fadeOut("slow");
        $(this).attr("check", 0);
        $(this).html("+");
      }


  });

  // remove line attribue
  $(".box_addtribute").on("click",".remove_line", function(){

    var obj = $(this);
    swal({
            title: confirm_alert_title,
            // text: text_show,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: cms_lang['gnotice_ok'],
            cancelButtonText: cms_lang['gnotice_cancel'],
            closeOnConfirm: true,
            closeOnCancel: true,
            html:
            '<div class="checkbox checkbox-inline">'
            +   '<input type="checkbox" name="full_delete" value="1" id="check-full-delele">'
            +    '<label for="check-full-delele">I understand and I want to delete it on the database</label>'
            +'</div>',
       }).then(function () {
            $(obj).parents(".box_line").remove();
            var fulldel = isNaN($("input[name='full_delete']:checked").val()) ? 0 : $("input[name='full_delete']:checked").val();
                fulldel = isNaN(fulldel) ? 0 : fulldel;
            
            if(fulldel)
            {
              var attr_id = $(obj).attr("id");
              $.ajax({
                  type: "post",
                  url: site_root_domain + "/?site=attribute&subact=delattr",
                  data: {id:attr_id},
                  dataType: "json",
                  error: function(xhr,status,error){
                    // Show error in Console
                    console.log(xhr.responseText);
                  },
                  success: function(response)
                  {
                    // console.log(response);
                  }
              });
            }
       });
    
  });

});

function validateForm(el_form, el_btn_check="[type='submit']", type="")
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
                        var check = 0;
                        if(type == "add_attribute" || type=="edit_attribute")
                        {
                          var type_btn = obj.attr("type");
                          formAttrCheck("#popup_attribute", type_btn);
                          check = 1;
                        }


                        if(check == 0)
                        {
                          node[0].submit();
                        }
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


function formAttrCheck(formele, type)
{
    // formele is ID form
    var url_get = $(formele).attr("action"); 
    var data = $(formele).serialize();
    // An thong bao loi
    $(".p_error").css("display", "none");
    $.ajax({
        type: "post",
        url: url_get,
        data: data,
        dataType: "json",
        error: function(xhr,status,error){
          // Show error in Console
          console.log(xhr.responseText);
        },
        success: function(obj)
        {
          // console.log(response); return false;
            // var obj = JSON.parse(response);
            var data = obj.data;
            // console.log(data); return false;
            if(obj.status == "success")
            {
              // Reset form
              $(formele)[0].reset();
              $(formele).find(".box_line_option .line_option").not(":first").remove();

              if(type === "add_more")
              {
                $(".p_error").html(obj.msg);
                $(".p_error").css("display", "block");
                $(".p_error").removeClass("alert-danger");
                $(".p_error").addClass("alert alert-success");
                $(formele).find("input:text:visible:first").focus();
              }else{
                swal("Successfull",obj.msg,'success').then((value) => {$.magnificPopup.close();});
              }

              // Apply row to list
              applyRow(".box_list_attribute",".box_line[rowid='line_"+data.attr_id+"']", data);
              
            }else{
              $(".p_error").html(obj.msg);
              $(".p_error").css("display", "block");
              $(".p_error").removeClass("alert-success");
              $(".p_error").addClass("alert alert-danger");
            }

            // auto close notify
            setTimeout(function(){
              $(".p_error").css("display", "none");
            }, 7000);
        }
    });
}

function applyRow(ele_list, ele_row, data)
{
  // console.log(data);
  var dataJson = JSON.stringify(data.attribute_options);
  var options_count = '<a class="view_list_options" onclick="openPopup(\'#box_list_options\', viewListOptions(this));" dataJson=\''+dataJson+'\' id="'+data.attr_id+'">'+data.options_count+'</a>';
      // console.log(ele_row.length);
  if($(ele_row).length > 0)
  {
    // Update row hien tại
    $(ele_row + " .mini_line .line_name").html(data.attr_name);
    $(ele_row + " .line .name").html(data.attr_name);
    $(ele_row + " .line .key").html(data.attr_key);
    $(ele_row + " .line .value").html(options_count);
    $(ele_row + " .line .unit").html(data.attr_unit);
    $(ele_row + " .line .order").html(data.attr_order);
    $(ele_row + " .line .description").html(data.attr_description);
  }else
  {
    var css_mobile
    // Append row mới
    var html_row = '<div class="box_line" rowid="line_'+data.attr_id+'">'
                  +     '<div class="mini_line" style="display: none;">'
                  +          '<div class=" col-md-12">'
                  +              '<p class="line_name">'+data.attr_name+'</p>'
                  +              '<span class="expand">+</span>'
                  +          '</div>'
                  +      '</div>'
                  +      '<div class="line">'
                  +          '<div class="box_css col-md-2">'
                  +              '<label class="lbl_row" style="display: none;">Name</label>'
                  +              '<span class="name">'+data.attr_name+'</span>'
                  +              '<input type="hidden" value="'+data.attr_id+'" name="attr_id[]">'
                  +          '</div>'
                  +          '<div class="box_css col-md-2">'
                  +              '<label class="lbl_row" style="display: none;">Value</label>'
                  +              '<p class="content value">'+options_count+'</p>'
                  +          '</div>'
                  +          '<div class="box_css col-md-2">'
                  +              '<label class="lbl_row" style="display: none;">Key</label>'
                  +              '<span class="key">'+data.attr_key+'</span>'
                  +          '</div>'
                  +          '<div class="box_css col-md-2">'
                  +              '<label class="lbl_row" style="display: none;">Unit</label>'
                  +              '<span class="unit">'+data.attr_unit+'</span>'
                  +          '</div>'
                  +          '<div class="box_css col-md-1">'
                  +              '<label class="lbl_row" style="display: none;">Order</label>'
                  +              '<span class="order">'+data.attr_order+'</span>'
                  +          '</div>'
                            
                  +          '<div class="box_css col-md-2">'
                  +              '<label class="lbl_row" style="display: none;">Description</label>'
                  +              '<p class="content description">'+data.attr_description+'</p>'
                  +          '</div>'
                  +          '<div class="box_css col-md-1">'
                  +              '<label class="lbl_row" style="display: none;">&nbsp;</label>'
                  +              '<a class="more_option" id="'+data.attr_id+'">More...</a>'
                  +              '<a class="remove_line" id="'+data.attr_id+'" data-toggle="tooltip" title="Remove this attribute line"><i class="fa fa-trash"></i></a>'
                  +          '</div>'
                  +      '</div>'
                  +  '</div>';

      // Append list attribute
      $(ele_list).append(html_row);
      // check show theo mobile
      checkShowLineByMobile();
  }

  // Tạo expand
  createExpand(".content");
}

function createExpand(ele)
{
  // reset height
  $(ele).css("height","auto");
  $(ele).css("word-wrap","break-word");
  $(".view_more").remove();

  $(ele).each(function(){
      var height = $(this).height();
      if(height > 30)
      {
          $(this).css("height", "30px");
          $(this).css("overflow", "hidden");
          $(this).parent().append("<a class='view_more'>-- expand --</a>");
      }
  });
}
function log( message , id) 
{
  if($(".p-"+id).length <= 0)
  {
    $( "<p class='p-"+id+"'>" ).text( message ).appendTo( "#listattr" );
    $( "#listattr" ).scrollTop( 0 );
  }else
  {
    $(".show_error").html("This attribute already exists in the list");
    $(".show_error").css("color", "red");
    $(".show_error").css("font-size", "12px");
    $(".show_error").show();
    setTimeout(function(){
        $(".show_error").hide();
    }, 3000);
  }
}
function autoAttrSearch(selector, urls, type, min = 2)
{
    $(selector).autocomplete({
      source: urls,
      minLength: min,
      selectFirst: true,
      select: function( event, ui ) {
        // console.log(ui);
        // var listvalue = JSON.parse(ui.item.attr_value);
            // ui.item.attr_value = listvalue.join();
        applyRow(".box_list_attribute",".box_line[rowid='line_"+ui.item.attr_id+"']",ui.item);
        // show log
        log(ui.item.attr_name, ui.item.attr_id);

      },
      change: function (event, ui) {
          // console.log(ui);
       }
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        var html = `<li class="ui-menu-item"><div class="ui-menu-item-wrapper">` + item.attr_name + `</div></li>`;
      return $( html)
        .appendTo( ul );
    };

}


function checkShowLineByMobile()
{
  if(is_mobile == 1)
  {
      $(".box_header .line").css("display","none");
      $(".box_line").each(function(){
          $(this).find(".lbl_row").css("display","block");
          $(this).find(".line").css("display","none");
          $(this).find(".mini_line").css("display","block");

          // Expand
          $(this).find(".expand").attr("check", 0);
          $(this).find(".expand").html("+");
      });
  }else
  {
      $(".box_header .line").css("display","block");
      $(".box_line").each(function(){
          $(this).find(".lbl_row").css("display","none");
          $(this).find(".line").css("display","block");
          $(this).find(".mini_line").css("display","none");
      });
  }
}

function addNewLine(obj)
{
  var html_curr = $(obj).parents(".line_option").clone();
      html_curr.find(".line_input").val("");
  // Add new line
  $(".box_line_option").append(html_curr);

  // reset row
  var row = 1;
  $(".line_option").each(function(){
      $(this).find(".number").html(row);
      row ++;
  });
}

function delLine(obj)
{
  // Check ID exist
  var id = isNaN(parseInt($(obj).attr("id"))) ? 0 : parseInt($(obj).attr("id"));
  // Check length
  var check = $(".line_option").length;
  var line = $(obj).parents(".line_option");
  if(id)
  {
    // Confirm trước khi xoá
    swal({
            title: confirm_alert_title,
            text: "You will not be able to recover this information",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: cms_lang['gnotice_ok'],
            cancelButtonText: cms_lang['gnotice_cancel'],
            closeOnConfirm: true,
            closeOnCancel: true
       }).then(function () {
          if(check > 1)
          {
            // Remove current line
            line.remove();
            // reset row
            var row = 1;
            $(".line_option").each(function(){
                $(this).find(".number").html(row);
                row ++;
            });
          }else
          {
            // line <= 1 thì ko xoá line chi reset thong tin
            line.find(".line_input").val("");
            line.find(".option_del").attr("id","");
          }

          // Xoá thông tin Ajax
          $.ajax({
            type: "post",
            url: site_root_domain + "/?site=attribute&subact=delvalueoption",
            data: {id: id},
            dataType: "json",
            error: function(xhr,status,error){
              // Show error in Console
              console.log(xhr.responseText);
            },
            success: function(response)
            {
              // console.log(response);
            }
          });


       });

  }else
  {
    // Không có id thi xoa html thường
    if(check > 1)
    {
      line.remove();
      // reset row
      var row = 1;
      $(".line_option").each(function(){
          $(this).find(".number").html(row);
          row ++;
      });
    }else
    {
      // line <= 1 thì ko xoá line chi reset thong tin
      line.find(".line_input").val("");
    }
  }
}

function delRowOption(obj)
{
  // Check ID exist
  var id = isNaN(parseInt($(obj).attr("id"))) ? 0 : parseInt($(obj).attr("id"));
  var x = $(obj).attr("inc");
  var attr_id = $(obj).attr("attr_id");
  // Check length
  var rows = $(obj).parents("tr");
  if(id)
  {
    // Confirm trước khi xoá
    swal({
            title: confirm_alert_title,
            text: "You will not be able to recover this information",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: cms_lang['gnotice_ok'],
            cancelButtonText: cms_lang['gnotice_cancel'],
            closeOnConfirm: true,
            closeOnCancel: true
       }).then(function () {
          
          // Xoá thông tin Ajax
          $.ajax({
            type: "post",
            url: site_root_domain + "/?site=attribute&subact=delvalueoption",
            data: {id: id},
            dataType: "json",
            error: function(xhr,status,error){
              // Show error in Console
              console.log(xhr.responseText);
            },
            success: function(response)
            {
              // console.log(response);
              var ele = $(".box_line[rowid='line_"+attr_id+"']").find(".view_list_options");
              var dataJson = ele.attr("datajson");
                  dataJson = JSON.parse(dataJson);
                  dataJson.splice(x, 1);
              var countdata = dataJson.length;
              // Đổ lại datajson
              ele.attr("datajson", JSON.stringify(dataJson));
              ele.html(countdata);
            }
          });

          // remove row
          rows.remove();

       });

  }
}

function viewListOptions(obj, linkback)
{
    var attr_id = $(obj).attr("id");
    var dataJson = $(obj).attr("dataJson");
        dataJson = JSON.parse(dataJson);
    var html_table = "";
    if(dataJson.length > 0)
    {
      for(var x in dataJson)
      {
          html_table += '<tr>'
                  +  '<th scope="row">'+dataJson[x].options_id+'</th>'
                  +  '<td>'+dataJson[x].options_name+'</td>'
                  +  '<td>'+dataJson[x].options_value+'</td>'
                  +  '<td style="text-align:center;"><a class="option_del" inc="'+x+'" attr_id="'+dataJson[x].attr_id+'" data-toggle="tooltip" title="Delete this option" id="'+dataJson[x].options_id+'" onclick="delRowOption(this);"><i class="fa fa-trash"></i></a></td>'
                  +'</tr>';
      }
    }else
    {
        html_table += '<tr>'
                  +     '<td colspan="4">Not found</td>'
                  +   '</tr>';
    }
    if(linkback)
    {
      $(".headtitle").append("<a class='pull-right' onclick='event.stopPropagation(); openPopup(\""+linkback+"\");' style='font-size: 12px;position: absolute;right: 15px;bottom: 10px;'>Back to list</a>");
    }else
    {
      $(".headtitle a").remove();
    }
    
    $("#listOption").html(html_table);

}

function openPopup(idPopup="", callback=null)
{
  $.magnificPopup.close(); 
  $.magnificPopup.open({
      items: {
        src: idPopup, 
      },
      type: 'inline',
      preloader: true,
      callbacks: {
        elementParse: function(item) {
          if (callback instanceof Function) {callback();}
        }
      }
    });

}


function closePopup()
{
  $.magnificPopup.close();
}


function nospace(evt)
{
  if(evt == 32) { return false; }
}

function listAttribute()
{
  $.ajax({
    type: "post",
    url: "/acp/?site=attribute&subact=listattribute",
    beforeSend: function(){},
    dataType: "json",
    error: function(xhr,status,error){
      // Show error in Console
      console.log(xhr.responseText);
    },
    success: function(obj)
    {
      // var obj = JSON.parse(response);
      var html_row = "";
      if(obj.length > 0)
      {
        for(var x in obj)
        {
          html_row += '<tr>'
                    +  '<th scope="row">#'+obj[x].attr_id+'</th>'
                    +  '<td>'+obj[x].attr_name+'</td>'
                    +  '<td>'+obj[x].attr_key+'</td>'
                    +  '<td>'+obj[x].attr_unit+'</td>'
                    +  '<td style="text-align:center;">'+obj[x].view_quantity+'</td>'
                    +  '<td><a style="color: red;" onclick="deleteAttribute(this,'+obj[x].attr_id+');" data-toggle="tooltip" title="Delete attribute" aria-describedby="ui-id-6"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
        }
      }else
      {
        html_row += '<tr><td colspan="5">No data</td></tr>';
      }
      
      $("#listAttribute").html(html_row);
    }
  });
}

function deleteAttribute(obj,attr_id)
{
  var rows = $(obj).parents("tr");
  var rows_2 = $(".box_line[rowid='line_"+attr_id+"']");
  // Confirm trước khi xoá
    swal({
            title: confirm_alert_title,
            text: "You will not be able to recover this information",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: cms_lang['gnotice_ok'],
            cancelButtonText: cms_lang['gnotice_cancel'],
            closeOnConfirm: true,
            closeOnCancel: true
       }).then(function () {
          
          // Xoá thông tin Ajax
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=attribute&subact=delattr",
              data: {id:attr_id},
              dataType: "json",
              error: function(xhr,status,error){
                // Show error in Console
                console.log(xhr.responseText);
              },
              success: function(response)
              {
                // console.log(response);
              }
          });

          // remove row
          rows.remove();
          rows_2.remove();

       });
}
