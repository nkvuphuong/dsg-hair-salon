// All hidden js show in footer
$(document).ready(function(){
    $.validator.addMethod("phoneFormat", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^[+]*[(]{0,1}[0-9]{1,3}[)]{0,1}[-\s\./0-9]*$/g);
    }, "Please specify a valid phone number");




});
 
function seemore_comment(module_name, module_id)
{
 
  var numItems = $('#expand_comment_container .activity-line-item').length;
   $.ajax({
          type: "post",
          url: site_root_domain + "/?site=comment&sub_act=load_comment_ajax",
          data: {module_name: module_name, module_id: module_id, start: numItems},
          success: function(html)
          {
             
              var obj = JSON.parse(html);
              if(obj.status=='success')
              {
                 
                $('#expand_comment_container .activity-line').append( obj.data);
              }
              else
              {
                 $('#expand_comment_container .activity-line-more').hide();
              }
          }
          

     });
    
   
}



function seemore_logs(logs_key)
{
 
  var numItems = $('#expand_logs_container .activity-line-item').length;
   $.ajax({
          type: "post",
          url: site_root_domain + "/?site=logs&sub_act=load_logs_ajax",
          data: {log_key: logs_key, start: numItems},
          success: function(html)
          {
             
              var obj = JSON.parse(html);
              if(obj.status=='success')
              {
                 
                $('#expand_logs_container .activity-line').append( obj.data);
              }
              else
              {
                 $('#expand_logs_container .activity-line-more').hide();
              }
          }
          

     });
    
   
}



 
  $("#expand_comment").click(function() {
 
    var active = $(this).attr("active");
    if(active == 0)
    {
      $("#expand_comment_container").show();
      $(this).children("i").removeClass("fa-caret-right").addClass("fa-caret-down");
      $(this).attr("active",1);
    } 
    else
    {
       $("#expand_comment_container").hide();
       $(this).children("i").removeClass("fa-caret-down").addClass("fa-caret-right");
       $(this).attr("active",0);
    }
  }); 
 
   $("#expand_logs").click(function() {

    var active = $(this).attr("active");
    if(active == 0)
    {
      $("#expand_logs_container").show();
        $(this).children("i").removeClass("fa-caret-right").addClass("fa-caret-down");
      $(this).attr("active",1);
    } 
    else
    {
       $("#expand_logs_container").hide();
       $(this).children("i").removeClass("fa-caret-down").addClass("fa-caret-right");
       $(this).attr("active",0);
    }
  }); 
 
 
function show_popup_detail(log_id)
{

    var obj = $(this);
      $.magnificPopup.open({
        items: {
          src: '#big_logs' 
        },
        type: 'inline',
        closeOnBgClick: true,
        callbacks: {
            elementParse: function(item){
            
         
            show_detail_logs(log_id);
          }
        }
    });
 
}
 

function call_notify(title_msg,msg, type_notify ='info', url_target)
{


    $.notify({
      // options
      icon: 'glyphicon glyphicon-warning-sign',
      title: title_msg,
      message: msg,
      url:  url_target,
      target: '_blank'
    },{
      // settings
      element: 'body',
      position: null,
      type: type_notify,
      allow_dismiss: true,
      newest_on_top: false,
      showProgressbar: false,
      placement: {
        from: "top",
        align: "right"
      },
      offset: 20,
      spacing: 10,
      z_index: 9999,
      delay: 5000,
      timer: 1000,
      url_target: '_blank',
      mouse_over: null,
      animate: {
    enter: 'animated fadeInDown',
    exit: 'animated fadeOutUp'
      },
      onShow: null,
      onShown: null,
      onClose: null,
      onClosed: null,
      icon_type: 'class',
      template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<span data-notify="icon"></span> ' +
        '<span data-notify="title">{1}</span> ' +
        '<span data-notify="message">{2}</span>' +
        '<div class="progress" data-notify="progressbar">' +
          '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
        '</div>' +
        '<a href="{3}" target="{4}" data-notify="url"></a>' +
      '</div>' 
    });

}


// Plugin comment module

function action_send_comment(el)
{
   var msg;
   var plugin_comment_name;
   var plugin_comment_id;
   msg = $("#"+el).find("input[type=text]").val();
   plugin_comment_name = $("#"+el).find("input[name=plugin_comment_name]").val();
   plugin_comment_id = $("#"+el).find("input[name=plugin_comment_id]").val();

   if(msg.length == 0)
   {
      $("#"+el).find("#plugin_msg").html(lang_empty_comment).show();
   }
   else
   {
      $("#"+el).find("#plugin_msg").html("").hide();
   }

   action_senddo_comment(plugin_comment_name,plugin_comment_id,msg);
    $("#"+el).find("input[type=text]").val("");
}

$('#box_send_comment input[type=text]').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    action_send_comment('box_send_comment');
    return false;  
  }
});



function action_draf_comment(el)
{
   $("#"+el).find("input[type=text]").val("");
}

function action_senddo_comment(module_name, module_id, msg)
{
    

   $.ajax({
          type: "post",
          url: site_root_domain + "/?site=comment&act=add_do",
          data: {module_name: module_name, module_id: module_id, comment_content:msg},
          success: function(html)
          {
             
              var obj = JSON.parse(html);
              if(obj.status=='success')
              {
                $('#expand_comment_container .activity-line').prepend( obj.last_msg);
          

              }
          }
          

     });
    

}

// var flag = false;
// var abc = 100;
// var destnew = 0;
// $('#comment_container').bind(
//     'jsp-scroll-y',
//           function(event, destY)
//           {
//              if(destY > 900 && destY - destnew > abc)
//              {
//                 destnew = destY;
                 
                  
                
//              }
//              else
//              {
//                 flag = false;
//              }
             
//           }
// );

 
 
 
function action_list_comment(module_name,module_id)
{
    
 
   $.ajax({
          type: "post",
          url: site_root_domain + "/?site=comment",
          data: {module_name: module_name, module_id: module_id},
          success: function(html)
          {
             
              var obj = JSON.parse(html);
              if(obj.status=='success')
              {
                $("#expand_comment_container .activity-line").prepend(obj.data);
           

              }
          }
          

     });
    

}

function check_submit_form(text_show, id_form, obj)
{
   var t = $(obj).val();

    if($(obj).val() != "" && $("#"+id_form+ " input[type='checkbox']:checked").length > 0)
    {
      if( t == 'assign_to_store' )
      {
        // call add store
        $.ajax({
          type: "post",
          url: site_root_domain + "/?site=store&subact=ajax_get_option_store",
          success: function(html)
          {
            var obj = JSON.parse(html);
            if( obj.status == "success" )
            {
              var lang_choose_store = typeof cms_lang['choose_store'] != 'undefined' ? cms_lang['choose_store'] : 'Choose store';
              var html =`
              <div class="clearfix">
                <p><span>`+text_show+`</span></p>
                <fieldset class="form-group">
                  <label class="form-label" for="store_id" style="flex: 1;">`+lang_choose_store+`</label>
                  <select name="store_id" id="store_id" class="form-control select_store" onchange="setStoreId(this);">
                    `+obj.data_option+`
                  </select>
                </fieldset>
              </div>
              `;

              if( $("#"+id_form).find('input[name="store_id"]').length <= 0 )
              {
                $("#"+id_form).append('<input class="store_id" name="store_id" value="" type="hidden">');
              }

              swal({
                title: confirm_alert_title,
                type: 'warning',
                html: html,
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
               }).then(function () {
                $('.select_store').trigger("change");
                $("#"+id_form).submit();
                return true;
              }).catch(swal.noop);
            }
          }
        });
      }
      else
      {
        swal({
              title: confirm_alert_title,
              text: text_show,
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              // confirmButtonText: cms_lang['gnotice_ok'],
              // cancelButtonText: cms_lang['gnotice_cancel'],
              // closeOnConfirm: true,
              // closeOnCancel: true
         }).then(function () {
              $("#"+id_form).submit();
              return true;
         
         }).catch(swal.noop);
      }
    }
    if(t == "arrange")
    {
         swal({
            title: confirm_alert_title,
            text: text_show,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            // confirmButtonText: cms_lang['gnotice_ok'],
            // cancelButtonText: cms_lang['gnotice_cancel'],
            // closeOnConfirm: true,
            // closeOnCancel: true
       }).then(function () {
            $("#"+id_form).submit();
            return true;
       
       })
    }
}

function setStoreId(obj, element)
{
  if( typeof element == 'undefined' )
  {
    element = 'input[name="store_id"]';
  }
  $(element).val($(obj).val());
}

// function reinit_srollpane()
// {
//      //$('.scrollable-block').destroy();
//     $('.scrollable-block').jScrollPane(
//       {
//         autoReinitialise: true
//       }
//     );
 
    
// }