 $("input[name='pg_type']").change(function() {
 
 
       var p_type =$("input[name='pg_type']:checked").val() ;
       get_group_product_2(p_type);

      
  });




 function get_group_product_2(type)
 {

   $.ajax({
                type: "post",
                url: site_root_domain+"/?site=product_group&subact=search_gp_product_ajax",
                data: {p_type: type},
                success: function(html)
                {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    var output_li="";
                    if(obj.status == "success")
                    {
                        output_li = "<option value=''>"+cms_lang.select+"</option>";
                        $.each(obj.data_option, function(index, value)
                        {                       
                            output_li += "<option   value='"+value.product_group_id+"' > "+value.product_group_name+"</option>";
                        });
                    }
  
                    $("#pg_parent").html(output_li);
                    
                }
            });
 

 }

 
function generate_pg_code(onthis,element_output)
{
    var value = $(onthis).val();
    var ret = value.split(/ +/);
    var i;
    var string = "";
    for (i = 0; i < ret.length; ++i) {
    // do something with `substr[i]`
        string = string+''+ret[i].slice(0,1);

    }
    var res = string.substr(0, 3);
    $(element_output).val(res.toUpperCase());
}

  

function add_sub_pgroup(parent_id,pgroup_type,event)
{
      event.stopPropagation();
       
        $(".title_change_pg").html(lang_pg_add);
        $(".change_action_pg").html("<input class='btn act_popup_btn_validate' aclass='btn_add_pg' type='button' value='"+lang_pg_add+"'/>");
        //Plong: init formvalidation new style alert
        validate_form_custom("#form_product_group",".act_popup_btn_validate","box_custom");
        $(".font-icon-cloud-upload-2").css("display","block");
        $(".drop-zone-caption").css("display","block");
       
        $(".error").html('');
        // $("form#form_product_group")[0].reset();
        clearForm($("form#form_product_group"));

        $("form#form_product_group #upload_img_show").attr("src","");

        if(parent_id != "")
        {
          $("form#form_product_group select[name='pg_parent']").val(parent_id);     
        }

        if(pgroup_type != "")
        {
          $("form#form_product_group input[name='pg_type'][value='"+pgroup_type+"']").prop("checked",true);      
        }
       
      


        // $("form#add_manufacture_form textarea").val("");
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: '#box_product_group'
          }
        });
}

function add_sub_pgroup_ibe(parent_id,pgroup_type,event)
{
      event.stopPropagation();
 
        $(".title_change_pg").html(lang_pg_add);
        $(".change_action_pg").html("<input class='btn act_popup_btn_validate' aclass='btn_add_pg' type='button' value='"+lang_pg_add+"'/>");
        //Plong: init formvalidation new style alert
        validate_form_custom_ibe("#form_product_group",".act_popup_btn_validate","box_custom");
        $(".font-icon-cloud-upload-2").css("display","block");
        $(".drop-zone-caption").css("display","block");
       
        $(".error").html('');
        // $("form#form_product_group")[0].reset();
       // clearForm($("form#form_product_group"));

        $("form#form_product_group #upload_img_show").attr("src","");

        if(parent_id != "")
        {
          $("form#form_product_group select[name='pg_parent']").val(parent_id);     
        }

        if(pgroup_type != "")
        {
          $("form#form_product_group input[name='pg_type'][value='"+pgroup_type+"']").prop("checked",true);      
        }
       
      


        // $("form#add_manufacture_form textarea").val("");
        $.magnificPopup.open({
          type: 'inline',
          items: {
            src: '#box_product_group'
          }
        });
}


function edit_sub_pgroup(pg_id,event, form_id, box_id, url_redirect)
{
  event.stopPropagation();

  if( typeof form_id == 'undefined' || ! form_id )
  {
    var form_id = 'form_product_group';
  }

  if( typeof box_id == 'undefined' || ! box_id )
  {
    var box_id = 'box_product_group';
  }

  if( typeof url_redirect == 'undefined' || ! url_redirect )
  {
    var url_redirect = site_root_domain+"/?site=product_group";
  }

        //var pg_id = $(this).attr("id");
        //var val_return = $(this).parents(".box_action").attr("checkreturn");
        $(".title_change_pg").html(lang_pg_edit);
        $(".change_action_pg").html("<input class='btn act_popup_btn_validate' aclass='btn_edit_pg' type='button' value='"+lang_pg_edit+"'/>");
       
        //Plong: init formvalidation new style alert
        validate_form_custom("#"+form_id,".act_popup_btn_validate","box_custom");

        if( url_redirect != 'none' )
        {
          $("form#"+form_id+" input[name='url_redirect']").val(url_redirect);
        }
        
        $(".error").html('');
        // $("form#"+form_id)[0].reset();
        clearForm($("form#"+form_id));
        $("form#"+form_id+" #upload_img_show").attr("src","");
        waitingDialog.show(lang_loading); 
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=product_group&subact=ajax_getinfo_pg",
            data: {pg_id: pg_id},
            success: function(html)
            {
              // console.log(html);
              var obj = JSON.parse(html);
              // console.log(obj);
              $("form#"+form_id+" select[name='pg_parent']").html(obj.data.data_option);
              $("form#"+form_id+" input[name='pg_name']").val(obj.data.product_group_name);
              $("form#"+form_id+" input[name='pg_id']").val(obj.data.product_group_id);
              $("form#"+form_id+" input[name='pg_code']").val(obj.data.product_group_code);
              $("form#"+form_id+" input[name='pg_order']").val(obj.data.product_group_order);
              $("form#"+form_id+" textarea[name='pg_description']").val(obj.data.product_group_description);
              $("form#"+form_id+" input[name='pg_type'][value='"+obj.data.product_group_type+"'] ").prop("checked",true);
              $("form#"+form_id+" input[name='pg_status']").prop("checked",false);
              $("form#"+form_id+" input[name='pg_status'][value='"+obj.data.product_group_status+"'] ").prop("checked",true);
              $("form#"+form_id+" select[name='cat_gallery_id'] option[value='"+obj.data.cat_gallery_id+"']").prop("selected",true);
              $("form#"+form_id+" select[name='attr_group'] option[value='"+obj.data.attr_group+"']").prop("selected",true);
             $("form#"+form_id+" .del_file_img").attr("onclick","return delete_fileToAttach("+obj.data.product_group_id+", 'product_group');")
              var link_img = "";
              if(obj.data.product_group_avatar)
              {
                link_img =  upload_url+"/product/"+ obj.data.product_group_avatar;
              }
 
              $("form#"+form_id+" #upload_img_show").attr("src",link_img);
              $("form#"+form_id+" #upload_img_show").css("display","block");
              $("form#"+form_id+" .drop-zone i, form#"+form_id+" .drop-zone-caption").hide();
              waitingDialog.hide();
              $.magnificPopup.open({
                type: 'inline',
                items: {
                  src: '#'+box_id
                }
              });

              
            }
        });
        

}

function edit_sub_pgroup_ibe(pg_id,cus_id,event)
{

        event.stopPropagation();
        //var pg_id = $(this).attr("id");
        //var val_return = $(this).parents(".box_action").attr("checkreturn");
        $(".title_change_pg").html(lang_pg_edit);
        $(".change_action_pg").html("<input class='btn act_popup_btn_validate' aclass='btn_edit_pg' type='button' value='"+lang_pg_edit+"'/>");
       
        //Plong: init formvalidation new style alert
        validate_form_custom("#form_product_group",".act_popup_btn_validate","box_custom");

        $("form#form_product_group input[name='url_redirect']").val(site_root_domain+"/?site=product_group&cus_id="+cus_id);


        $(".error").html('');
        // $("form#form_product_group")[0].reset();
       //clearForm($("form#form_product_group"));
        $("form#form_product_group #upload_img_show").attr("src","");
        waitingDialog.show(lang_loading); 
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=product_group&subact=ajax_getinfo_pg",
            data: {pg_id: pg_id,cus_id: cus_id},
            success: function(html)
            {
              // console.log(html);
              var obj = JSON.parse(html);
              if(obj.data.product_group_parent > 0)
              {
                $("form#form_product_group input[name='opt_cate'][value='1']").prop("checked",true);      
                init_rebuil_form_pg(1);
              }
              else
              {
                 $("form#form_product_group input[name='opt_cate'][value='2']").prop("checked",true);      
                 init_rebuil_form_pg(2);
              }
              $("form#form_product_group select[name='pg_parent']").html(obj.data.data_option);
              $("form#form_product_group input[name='pg_name']").val(obj.data.product_group_name);
              $("form#form_product_group input[name='pg_id']").val(obj.data.product_group_id);
       
              $("form#form_product_group input[name='pg_order']").val(obj.data.product_group_order);
              $("form#form_product_group textarea[name='pg_description']").val(obj.data.product_group_description);
              $("form#form_product_group input[name='pg_type'][value='"+obj.data.product_group_type+"'] ").prop("checked",true);
              $("form#form_product_group input[name='pg_status']").prop("checked",false);
              $("form#form_product_group input[name='pg_status'][value='"+obj.data.product_group_status+"'] ").prop("checked",true);
              $("form#form_product_group select[name='cat_gallery_id'] option[value='"+obj.data.cat_gallery_id+"']").prop("selected",true);
             $("form#form_product_group .del_file_img").attr("onclick","return delete_fileToAttach("+obj.data.product_group_id+", 'product_group');")
              var link_img = "";
              if(obj.data.product_group_avatar)
              {
                link_img =  upload_url+"/product/"+ obj.data.product_group_avatar;
              }
 
              $("form#form_product_group #upload_img_show").attr("src",link_img);
               
               $("form#form_product_group #upload_img_show").css("display","block");

               waitingDialog.hide();
              $.magnificPopup.open({
                type: 'inline',
                items: {
                  src: '#box_product_group'
                }
              });

              
            }
        });
        

}


function init_rebuil_form_pg(opt_cate)
{
   if(opt_cate == 1) { 
               
      $('.opt-cate-1').show(); $('.opt-cate-2').hide();
      var pg_name_old = $("#form_product_group").find("input[name=pg_name]").attr("data-validation");
      var pg_code_old = $("#form_product_group").find("input[name=pg_code]").attr("data-validation");

      $("#form_product_group").find("input[name=pg_name]").removeAttr("data-validation");
      $("#form_product_group").find("input[name=pg_name]").attr("nocheck-data-validation",pg_name_old);
      

      $("#form_product_group").find("input[name=pg_code]").removeAttr("data-validation");
      $("#form_product_group").find("input[name=pg_code]").attr("nocheck-data-validation",pg_code_old);

  }else{ 
      $('.opt-cate-2').show(); 
      $('.opt-cate-1').hide();
      var pg_name_old = $("#form_product_group").find("input[name=pg_name]").attr("nocheck-data-validation");
      var pg_code_old = $("#form_product_group").find("input[name=pg_code]").attr("nocheck-data-validation");

      $("#form_product_group").find("input[name=pg_name]").removeAttr("nocheck-data-validation");
      $("#form_product_group").find("input[name=pg_name]").attr("data-validation",pg_name_old);
      

      $("#form_product_group").find("input[name=pg_code]").removeAttr("nocheck-data-validation");
      $("#form_product_group").find("input[name=pg_code]").attr("data-validation",pg_code_old);

    

  }

}


