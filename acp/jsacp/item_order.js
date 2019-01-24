
// var clicked_item_product = 0;
// var clicked_item_asset = 0;

$(document).ready(function(){

  $("body").on("click",function(e){
      var flag_product = $("#flag_product").val();
      if($(e.target).hasClass("search_product") || $(e.target).hasClass("search_asset") )
      {
          $("#flag_product").val(1);
      }
      else
      {
        if(flag_product == 1)
        {
          check_item_order();
          $("#flag_product").val(0);    
        }
       
      }
      
  });

   
$("#data_table input[name='product_name[]'],#data_table_asset input[name='ass_name[]']").on('focus',function(){
     $("#flag_product").val(1);
});

$(".add_table #tblProduct").on('click',function(){
      reset_form_item_asset();
});

$(".add_table #data_table_asset").on('click',function(){
      reset_form_item_product();
});




});


function reset_form_item_asset()
{ 
  $(".btn_del_line_asset").trigger("click");
}
function reset_form_item_product()
{
  $(".btn_del_line").trigger("click");
}

// $("#data_table input[name='product_name[]']").focus(function(){

//     clicked_item_product += 1;
// });

function check_item_order()
{
    
  var cnt_product = 0;
  var cnt_assets = 0;
   // Check product item
   
   $( "#data_table .row-grid" ).each(function( index ) {
        var pro_val_tmp = $(this).find("input[name='product_id[]']").val();
        var pro_val = !isNaN(parseInt(pro_val_tmp)) ? pro_val_tmp : 0;
        var pro_name = $(this).find("input[name='product_name[]']").val();
        if(pro_val == 0 && pro_name.length > 0)
        {
          if( $(this).hasClass("active") == true)
          {
             cnt_product += 1;
          }
         
        }
  });

   
   // Check assets item
   
   $( "#data_table_asset .row-grid-asset" ).each(function( index ) {
        var ass_val = $(this).find("input[name='ass_id[]']").val();
         
        var ass_name = $(this).find("input[name='ass_name[]']").val();
        if(ass_val.length == 0 && ass_name.length > 0)
        {
          if( $(this).hasClass("active") == true)
          {
             cnt_assets += 1;
          }
        }
  }); 

   if(cnt_product > 0 || cnt_assets > 0)//cnt_assets == 0
   {
      $("body").addClass("control-panel");
      $("body").addClass("open");
      $(".control-panel-container").css("display","block");
      $(".ha-underlay").css("display","block").show();
      $(".ha-underlay").css("z-index","10002");
      return false;
   }
   else
   {
     return true;
   }
}
