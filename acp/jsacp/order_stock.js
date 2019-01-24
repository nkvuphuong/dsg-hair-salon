function check_stock_product(p_id)
{
	waitingDialog.show(cms_lang.waiting_dialog_msg);
	var output_tr;
	 
	$.ajax({
          type: "get",
          url: site_root_domain + "/?site=assets&subact=ajax_checkstock_assets",
          data: {product_id: p_id},
          success: function(html)
          {
            waitingDialog.hide();
        	var obj = JSON.parse(html);

            if(obj.status == "error")
            {   
            	output_tr += "<tr><td colspan='3'>"+obj.msg+"</td></tr>";
            	 $("#tbody_order_stock").html(output_tr);      
            }
            else
            {
            	$.each(obj.data, function(index, value)
                {
                	output_tr += "<tr><td>"+value.store_name+"</td><td>"+value.stock+"</td><td><a href='"+site_root_domain+"/?site=assets&store_id="+value.store_id+"'>Chi tiết kho</a></td></tr>";
               
                });
                var btn_detail = "<a stlye='float:right' class='btn btn_add_cus btn-inline btn-primary' href='"+site_root_domain+"/?site=product&act=show&id="+p_id+"'>Chi tiết sản phẩm</a>";
                $("#box_check_stock #btn_boxstock_detail").html(btn_detail);
                 $("#tbody_order_stock").html(output_tr);      
            }
          }
    });

  
    $.magnificPopup.open({
            type: 'inline',
            items: {
                src: '#box_check_stock'
            }
        
    });
}