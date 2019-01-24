$(document).ready(function(){
    // REPORT
    $(".change_time").click(function(){
      var type = $(this).attr("btn_type");
      
      $.ajax({
          type: "post",
          url: site_root_domain + "/?site=report&subact=getTime",
          data: {type: type},
          success: function(html)
          {
            var obj = JSON.parse(html);
            $("input[name='time_from']").val(obj.time_from);
            $("input[name='time_to']").val(obj.time_to);
            var title_chart = obj.time_from + " - " + obj.time_to;

            $(".time_title").html(title_chart);

            // view report
            $(".btn_view_report").trigger("click"); //
          }
      });
    });

    $(".btn_view_report").click(function(){


      var page = $("input[name='page_name']").val();
      var subact = $("input[name='page_subact']").val();
      var time_from = $("input[name='time_from']").val();
      var time_to = $("input[name='time_to']").val();
      var view_type = $("select[name='type_view']").val();
      var type_status = $("select[name='type_status']").length > 0 ? $("select[name='type_status']").val() : -1;
      var p_group = $('#p_group').val() != "" ? $('#p_group').val()  : '';
      var store_id = $("select[name='store_id']").val();
      var product = $("input[name='product_name']").val();
      var price_from = $("input[name='price_from']").val();
      var price_to = $("input[name='price_to']").val();
      var supplier = $("select[name='supplier']").val();
      var user = $("select[name='user_id']").val();
      var range = $("select[name='range']").val();
      var p_status = $("select[name='p_status']").val();
      var ord_status = $("select[name='ord_status']").val();
      var price_type = $("select[name='price_type']").val();
      var buy_times = $("input[name='buy_times']").val();
      var city = $("select[name='city']").val();
      var district = $("select[name='district']").val();
      var customer = $("input[name='customer']").val();
      var cus_type = $("select[name='cus_type']").val();
      var object = $("select[name='object']").val();
      var ass_avaiable = $("select[name='ass_avaiable']").val(); 
      var ass_name = $("input[name='ass_name']").val();
      var ass_id_data = $("input[name='ass_id_data']").val(); // Ass name can duplicate, use id
      var user_id = $("select[name='user_id']").val();
      // Set value for chart
      var top = 0; // Number
      var left = 0; // Number
      var width = '90%'; // %
      var height = 0; // Number
      var legend = 0;
      
      // Get more input // 
      if(page)
      {
        // Update detail tile
        if(subact == "date")
        {
            // Set detail title
            if(view_type == "view_day")
            {
                if(page == "sales"){ $('#detail_title').html(cms_lang.title_menu_sales+" ("+cms_lang.detail_title_view_day+")"); }
                if(page == "order"){ $('#detail_title').html(cms_lang.title_menu_order+" ("+cms_lang.detail_title_view_day+")"); }
                if(page == "product"){ $('#detail_title').html(cms_lang.title_menu_product+" ("+cms_lang.detail_title_view_day+")"); }
                if(page == "assets"){ $('#detail_title').html(cms_lang.title_menu_assets+" ("+cms_lang.detail_title_view_day+")"); }
            }
            if(view_type == "view_month")
            {
                if(page == "sales"){ $('#detail_title').html(cms_lang.title_menu_sales+" ("+cms_lang.detail_title_view_month+")"); }
                if(page == "order"){ $('#detail_title').html(cms_lang.title_menu_order+" ("+cms_lang.detail_title_view_month+")"); }
                if(page == "product"){ $('#detail_title').html(cms_lang.title_menu_product+" ("+cms_lang.detail_title_view_month+")"); }
                if(page == "assets"){ $('#detail_title').html(cms_lang.title_menu_assets+" ("+cms_lang.detail_title_view_month+")"); }

            }
            if(view_type == "view_year")
            {
                if(page == "sales"){ $('#detail_title').html(cms_lang.title_menu_sales+" ("+cms_lang.detail_title_view_year+")"); }
                if(page == "order"){ $('#detail_title').html(cms_lang.title_menu_order+" ("+cms_lang.detail_title_view_year+")"); }
                if(page == "product"){ $('#detail_title').html(cms_lang.title_menu_product+" ("+cms_lang.detail_title_view_year+")"); }
                if(page == "assets"){ $('#detail_title').html(cms_lang.title_menu_assets+" ("+cms_lang.detail_title_view_year+")"); }   
            }
        }
        
          $.ajax({
              type: "post",
              url: site_root_domain + "/?site=report&subact=getReport",
              data: {page: page,sub: subact, time_from: time_from, time_to: time_to, view_type: view_type, type_status: type_status, p_group: p_group, store_id: store_id, user:user, product:product,price_from:price_from,price_to:price_to,supplier:supplier,range:range,ord_status:ord_status,p_status:p_status,price_type:price_type,buy_times:buy_times,city:city, district:district,customer:customer,cus_type:cus_type, object:object,ass_avaiable:ass_avaiable,ass_name:ass_name,ass_id_data:ass_id_data,user_id:user_id},
              beforeSend: function(){
                  //waitingDialog.show(cms_lang.waiting_dialog_msg);
                  $(".content_report").html("<div align='center'><img src='/acp/images/icons/icon_loadingcircle.gif'></div>");
                  $("#chart_show").html("<div align='center'><img src='/acp/images/icons/icon_loadingcircle.gif'/></div>");
                  $("#chart_show_2").html("<div align='center'><img src='/acp/images/icons/icon_loadingcircle.gif'/></div>");
                  $("#chart_show_3").html("<div align='center'><img src='/acp/images/icons/icon_loadingcircle.gif'/></div>");
              },
              success: function(html)
              {
                  
                  
                  //waitingDialog.hide();
                  var obj = JSON.parse(html);
                  var temp = obj.html_report.data_1;
             
                  var html_content = "";
                  $(".chart_name").html(obj.title_chart.main_title); //
                  $("#chart_show").html('');
                  $("#chart_show_2").html('');
                  $("#chart_show_3").html('');
                  
                  //======================= Report sales
                  if(page == "sales")
                  {
                      // Report sales follow date
                      if(subact == "date")
                      {
                          html_content = html_sales(obj.html_report);
                      }
                      // Report sales follow store
                      else if(subact == "store")
                      {
                          html_content = html_sales_store(obj.html_report);
                      }
                      // Report sales follow Product group
                      else if(subact == "pgroup")
                      {
                          // Set chart
                          top = 30;
                          obj.type_chart = "pie"; // Pie Chart
                          html_content = html_sales_pgroup(obj.html_report);
                      }
                      // Report sales follow Product
                      else if(subact == "product")
                      {
                          html_content = html_sales_product(obj.html_report);
                      }
                      // Report sales follow Product
                      else if(subact == "assets")
                      {
                          html_content = html_sales_assets(obj.html_report);
                      }
                      // Report sales follow Supplier
                      else if(subact == "supplier")
                      {
                          temp = [];
                          html_content = html_sales_supplier(obj.html_report,obj.days,obj.day_total,obj.link_export_bk);
                      }
                      // Report sales follow User
                      else if(subact == "user")
                      {
                          html_content = html_sales_user(obj.html_report);
                      }
                  }
                  //======================= End report sale
                  
                  //======================= Report Order
                  else if(page == "order")
                  {
                      // Report order follow date
                      if(subact == "date")
                      {
                          html_content = html_order(obj.html_report);
                      }
                      // Report order follow store
                      else if(subact == "price")
                      {
                          // Set chart
                          top = 30;
                          height = 250;
                          left = 200;
                          html_content = html_order_price(obj.html_report);
                      }
                      // Report order follow Product
                      else if(subact == "product")
                      {
                          html_content = html_order_product(obj.html_report);
                      }
                      // Report order follow Product
                      else if(subact == "assets")
                      {
                          html_content = html_order_assets(obj.html_report);
                      }
                      // Report order follow status
                      else if(subact == "status")
                      {
                          legend = 1;
                          width= '80%';
                          html_content = html_order_status(obj.html_report,obj.days,obj.day_total,obj.link_export_bk);
                      }
                      // Report order follow User
                      else if(subact == "user")
                      {
                          temp = "";
                          html_content = html_order_user(obj.html_report);
                      }                      
                  }
                  //======================= End report order
                  
                  //======================= Report product
                  else if(page == "product")
                  {
                      // Report order follow bestseller
                      if(subact == "bestseller")
                      {
                          temp = "";
                          html_content = html_product_bestseller(obj.html_report);
                      }
                      // Report order follow price
                      else if(subact == "price")
                      {
                          // Set chart
                          top = 30;
                          height = 250;
                          left = 270;
                          html_content = html_product_price(obj.html_report);
                      }
                      // Report order follow date
                      else if(subact == "date")
                      {
                          if(product == '')
                          {
                              // Hide chart
                              temp = '';
                              html_content = cms_lang.title_product_null;
                          }
                          else
                          {
                              html_content = html_product_date(obj.html_report);
                          }
                          
                      }
                      // Report order follow status
                      else if(subact == "store")
                      {
                          // Hide chart
                          temp = '';
                          html_content = html_product_store(obj.html_report, obj.list_store);
                      }
                  }
                  //======================= End report product
                  
                  //======================= Report assets
                  else if(page == "assets")
                  {
                      // Report order follow bestseller
                      if(subact == "bestseller")
                      {
                          temp = "";
                          html_content = html_assets_bestseller(obj.html_report);
                      }
                      // Report order follow price
                      else if(subact == "price")
                      {
                          // Set chart
                          top = 30;
                          height = 250;
                          left = 270;
                          html_content = html_assets_price(obj.html_report);
                      }
                      // Report order follow date
                      else if(subact == "date")
                      {
                          if(ass_name == '')
                          {
                              // Hide chart
                              temp = '';
                              html_content = cms_lang.title_assets_null;
                          }
                          else
                          {
                              html_content = html_assets_date(obj.html_report);
                          }
                          
                      }
                      // Report order follow status
                      else if(subact == "store")
                      {
                          // Hide chart
                          temp = '';
                          html_content = html_assets_store(obj.html_report, obj.list_store);
                      }
                  }
                  //======================= End report assets
                               
                  //=======================  Report Customer
                  else if(page == "customers")
                  {
                      // Report customer overview
                      if(subact == "overview")
                      {
                          // Set chart
                          top = 30;
                          left = 100;
                          height = 200;
                          
                          html_content = html_customer_overview(obj.html_report);
                      }
                      // Report customer follow sales
                      else if(subact == "sales")
                      {
                          // Set chart
                          top = 30;
                          height = 250;
                          left = 270;
                          html_content = html_customer_sales(obj.html_report);
                      }
                      // Report customer follow product
                      else if(subact == "product")
                      {
                            html_content = html_customer_product(obj.html_report);
                      }
                      // Report customer follow assets
                      else if(subact == "assets")
                      {
                            html_content = html_customer_assets(obj.html_report);
                      }
                      // Report customer follow store
                      else if(subact == "store")
                      {
                          // Hide chart
                          temp = '';
                          html_content = html_customer_store(obj.html_report, obj.list_month);
                      }
                  }
                  //=======================  End Report Customer
                  
                  //=======================  Report Finance
                  else if(page == "finance")
                  {

                      // Report finance daily retail
                      if(subact == "daily")
                      {
                          html_content = html_finance_daily(obj.html_report);
                      }
                      // Report finance follow record transacion
                      else if(subact == "record")
                      {
                          html_content = html_finance_record(obj.html_report);
                      }
                      // Report finance follow revenue
                      else if(subact == "revenue")
                      {
                            html_content = html_finance_revenue(obj.html_report);
                      }
                      // Report finance follow revenue
                      else if(subact == "commission_staff")
                      { 
                        
                            html_content = html_finance_commission_staff(obj.html_report);
                      }
                      else if(subact == "commission_service")
                      {

                          html_content = html_finance_commission_service(obj.html_report);
                      }
                  }
                  //=======================  End Report Finance
                  
                  //=======================  Report Inventory
                  else if(page == "inventory")
                  {
                      // Report inventory quantity
                      if(subact == "quantity")
                      {
                          html_content = html_inventory_quantity(obj.html_report);
                      }
                      // Report inventory follow product
                      else if(subact == "product")
                      {
                          html_content = html_inventory_product(obj.html_report);
                      }
                      // Report inventory follow assets
                      else if(subact == "assets")
                      {
                            html_content = html_inventory_assets(obj.html_report);
                      }
                      // Report inventory follow group
                      else if(subact == "group")
                      {
                            html_content = html_inventory_group(obj.html_report);
                      }
                      // Report inventory follow total import / export
                      else if(subact == "total")
                      {
                            html_content = html_inventory_total(obj.html_report);
                      }
                  }
                  //=======================  End Report Finance

                  // Output html
                  $(".content_report").html(html_content);
                  
                  // Get row count for set css last line
                  var rowCount = $('.report-table-border tr').length;
                  if(rowCount < 7)
                  {
                     $('.report-table-border tr:last td').css({"border-bottom": "1px solid #d8e2e7"})
                  }
                  
                  // Get row count for set css last line - data list 2
                  var rowCount2 = $('.report-table-border-2 tr').length;
                  if(rowCount2 < 7)
                  {
                     $('.report-table-border-2 tr:last td').css({"border-bottom": "1px solid #d8e2e7"})
                  }
                  
                  // Check for pie chart
                  // If data_chart - which the first chart is empty, will hide box chart
                  if((temp.length > 0 || (typeof(temp) == 'object') ) && subact != "product" && subact != "supplier" && obj.data_chart!=null && obj.data_chart!='') // Page Sale + sub product & supplier is empty chart
                  {
                      $(".box_chart").show();
                      $("#chart_show").html = '';
                      
                      drawVisualization_new(obj.data_chart, obj.title_chart.title_unit_y, "chart_show", obj.type_chart,obj.title_chart.title_hearer,top,left,width,height,legend);
                      
                      // Add more chart
                      if(obj.data_chart_2)
                      {
                         drawVisualization_new(obj.data_chart_2, obj.title_chart.title_unit_y, "chart_show_2", obj.type_chart,obj.title_chart.title_hearer_2,top,left,width,height,legend);
                      }
                      
                      if(obj.data_chart_3)
                      {
                         drawVisualization_new(obj.data_chart_3, obj.title_chart.title_unit_y, "chart_show_3", obj.type_chart,obj.title_chart.title_hearer_3,top,left,width,height,legend);
                      }
                  }
                  else
                  {                    
                      $(".box_chart").hide();
                  }
              }
          });
      }
    });

    $(".box_date_change").on("dp.change", ".change_pick_time", function(){

      var time_from = $("input[name='time_from']").val();
      var time_to = $("input[name='time_to']").val();
      var title_chart = time_from + " - " + time_to;

      $(".time_title").html(title_chart);
    })



});// End document


  function html_sales(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_sales_follow_time+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>  
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td><div>`+obj_data[x].date+`<span><a href='`+site_root_domain+obj_data[x].link_search+`'> <img src='/acp/images/icons/icon_search.png'/></span></a></div></td>
                                        <td>`+formatNumberInput(obj_data[x].total_price_bk)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].total_buy_bk)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].profit_bk)+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="5">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;


    return html;
  }

  function html_sales_store(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_sales_follow_store+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>  
                              <th><div>`+cms_lang.title_store+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total = 0;
                        var total_cost = 0;
                        var total_profit = 0;
                        
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].store+`</td>
                                        <td>`+obj_data[x].total_price+`</td>
                                        <td>`+obj_data[x].total_buy+`</td>
                                        <td>`+ obj_data[x].profit +`</td>
                                    </tr>`;
                            
                                    total += obj_data[x].total_price_bk;
                                    total_cost += obj_data[x].total_buy_bk;
                                    total_profit += obj_data[x].profit_bk;
                          }
                          
                          html += `
                        <tr>
                            <td colspan="2" style="text-align:center">Total</td>
                            <td>`+formatNumberInput(total)+`</td>
                            <td>`+formatNumberInput(total_cost)+`</td>
                            <td>`+formatNumberInput(total_profit)+`</td>
                        </tr>`;
          
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="5">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html+=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  } 
  
  function html_sales_product(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_sales_follow_product+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_product+`</div></th>
                              <th><div>`+cms_lang.title_pgroup+`</div>
                              <th><div>`+cms_lang.title_quantity+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total = 0;
                        var total_cost = 0;
                        var total_profit = 0;
                        var total_qty = 0;
                        
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].ordi_name+`</td>
                                        <td>`+obj_data[x].group+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].total_price+`</td>
                                        <td>`+obj_data[x].total_buy+`</td>
                                        <td>`+ obj_data[x].profit +`</td>
                                    </tr>`;
                            
                                    total += obj_data[x].total_price_bk;
                                    total_cost += obj_data[x].total_buy_bk;
                                    total_profit += obj_data[x].profit_bk;
                                    total_qty += obj_data[x].qty;
                          }
                          
                          html += `
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:bold">Total</td>
                            <td style="font-weight:bold">`+total_qty+`</td>
                            <td style="font-weight:bold">`+formatNumberInput(total)+`</td>
                            <td style="font-weight:bold">`+formatNumberInput(total_cost)+`</td>
                            <td style="font-weight:bold">`+formatNumberInput(total_profit)+`</td>
                        </tr>`;
                          
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="7">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html +=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_sales_assets(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_sales_follow_assets+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_assets+`</div></th>
                              <th><div>`+cms_lang.title_pgroup+`</div>
                              <th><div>`+cms_lang.title_quantity+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total = 0;
                        var total_cost = 0;
                        var total_profit = 0;
                        var total_qty = 0;
                        
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].ordi_name+`</td>
                                        <td>`+obj_data[x].group+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].total_price+`</td>
                                        <td>`+obj_data[x].total_buy+`</td>
                                        <td>`+ obj_data[x].profit +`</td>
                                    </tr>`;
                            
                                    total += obj_data[x].total_price_bk;
                                    total_cost += obj_data[x].total_buy_bk;
                                    total_profit += obj_data[x].profit_bk;
                                    total_qty += obj_data[x].qty;
                          }
                          
                          html += `
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:bold">Total</td>
                            <td style="font-weight:bold">`+total_qty+`</td>
                            <td style="font-weight:bold">`+formatNumberInput(total)+`</td>
                            <td style="font-weight:bold">`+formatNumberInput(total_cost)+`</td>
                            <td style="font-weight:bold">`+formatNumberInput(total_profit)+`</td>
                        </tr>`;
                          
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="7">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html +=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_sales_pgroup(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_sales_follow_pgroup+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_pgroup+`</div>
                              <th><div>`+cms_lang.title_quantity+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total = 0;
                        var total_cost = 0;
                        var total_profit = 0;
                        var total_qty = 0;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].group+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].total_price+`</td>
                                        <td>`+obj_data[x].total_buy+`</td>
                                        <td>`+ obj_data[x].profit +`</td>
                                    </tr>`;
                            
                                    total += obj_data[x].total_price_bk;
                                    total_cost += obj_data[x].total_buy_bk;
                                    total_profit += obj_data[x].profit_bk;
                                    total_qty += obj_data[x].qty;
                          }
                          
                          html += `
                        <tr>
                            <td colspan="2" style="text-align:center">Total</td>
                            <td>`+total_qty+`</td>
                            <td>`+formatNumberInput(total)+`</td>
                            <td>`+formatNumberInput(total_cost)+`</td>
                            <td>`+formatNumberInput(total_profit)+`</td>
                        </tr>`;
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="6">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html +=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_sales_supplier(data,days,day_total,link_export)
  {
    var html = "";
    
    
    
    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_sales_follow_supplier+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+link_export+`')" link="`+link_export+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                                <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                                <th><div>`+cms_lang.title_supplier+`</div></th>
                                <th colspan="2" align="center"><div>`+cms_lang.title_total_sales_sup+`</div></th>`;
                                
                                for(var x in days)
                                {
                                    // Loop for tr date
                                    html += `<th colspan="2" align="center"><div>`+days[x]+`</div></th>`;
                                }
                      
                        html +=`</tr>`;
                        
                        data = data.data_1;
                        var total = 0;
                        var total_profit = 0;
                        if(jQuery.isEmptyObject(data) == false)
                        {
                            // Data array supplier 
                            for(var x in data)
                            {
                                    html += `
                                        <tr>
                                            <td>`+data[x].order+`</td>
                                            <td>`+data[x].name+`</td>
                                            <td>`+formatNumberInput(data[x].all.total)+`</td>
                                            <td>`+formatNumberInput(data[x].all.profit)+`</td>`;

                                    // Data days from + to
                                    for(var y in days)
                                    {
                                        // Check Undefined value
                                        if(typeof data[x][days[y]] != 'undefined')
                                        {
                                            html +=`<td>`+formatNumberInput(data[x][days[y]].total)+`</td>
                                                    <td>`+formatNumberInput(data[x][days[y]].profit)+`</td>  
                                                `;
                                        }
                                        // Null data
                                        else
                                        {
                                            html += `<td></td><td></td>`;
                                        }
                                    }

                                    html += `</tr>`;

                                    total += data[x].all.total;
                                    total_profit += data[x].all.profit;
                            }
                        
                            html += `
                            <tr>
                            <td colspan="2" style="text-align:center">Total</td>
                            <td>`+formatNumberInput(total)+`</td>
                            <td>`+formatNumberInput(total_profit)+`</td>`;
                            
                            for(var y in days)
                            {
                                if(typeof day_total[days[y]] != 'undefined')
                                {
                                    html+= `<td>`+formatNumberInput(day_total[days[y]].total)+`</td>
                                   <td>`+formatNumberInput(day_total[days[y]].profit)+`</td>`;
                                }
                                else
                                {
                                    html+= `<td></td><td></td>`;
                                }
                            }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="3">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                   
                        
                            
                            
                        html+=`</tr>
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }

  function html_order(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_order_follow_date+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td><div>`+obj_data[x].datefm+`<span><a href='`+site_root_domain+obj_data[x].link_search+`'> <img src='/acp/images/icons/icon_search.png'/></span></a></div></td>
                                        <td>`+obj_data[x].quantity+`</td>
                                        <td>`+obj_data[x].amount+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="4">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_product_date(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_product_follow_date+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_product_code+`</div></th>
                              <th><div>`+cms_lang.title_product_name+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td><div>`+obj_data[x].datefm+`<span><a href='`+site_root_domain+obj_data[x].link_search+`'> <img src='/acp/images/icons/icon_search.png'/></span></a></div></td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].product_price+`</td>
                                        <td>`+obj_data[x].product_price_sell+`</td>
                                        <td>`+obj_data[x].total+`</td>
                                        <td>`+obj_data[x].profit+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="10">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_assets_date(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_assets_follow_date+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_assets_code+`</div></th>
                              <th><div>`+cms_lang.title_assets_name+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td><div>`+obj_data[x].datefm+`<span><a href='`+site_root_domain+obj_data[x].link_search+`'> <img src='/acp/images/icons/icon_search.png'/></span></a></div></td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].product_price+`</td>
                                        <td>`+obj_data[x].product_price_sell+`</td>
                                        
                                        <td>`+obj_data[x].total+`</td>
                                        <td>`+obj_data[x].profit+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="10">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_product_store(data, list_store)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_product_follow_store+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_product+`</div></th>
                              <th><div>`+cms_lang.title_all_price+`</div></th>`;
      
                              for(var x in list_store)
                              {
                                 html +=`<th><div>`+list_store[x].name+`</div></th>`; 
                              }
                              
                        html +=`<th><div>`+cms_lang.title_quantity+`</div></th>
                                <th><div>`+cms_lang.title_report_sale_total_price+`</div></th> 
                                </tr>`;
                        
                        var obj_data = data.data_1;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {
                            for(var x in obj_data)
                              {
                                html += `
                                        <tr>
                                            <td>`+`<p>`+obj_data[x].name+`</p><p>`+obj_data[x].code+`</p>`+`</td>
                                            <td>`+`<p>`+formatNumberInput(obj_data[x].product_price)+`</p><p>`+formatNumberInput(obj_data[x].product_price_sell)+`</p>`+`</td>`;

                                            // Loop for total product sold with each store
                                            var temp_total = 0;
                                            for(var y in list_store)
                                            {
                                                if(typeof(obj_data[x][list_store[y].id]) != 'undefined')
                                                {

                                                    html +=`<td>`+obj_data[x][list_store[y].id]+`</td>`;
                                                    temp_total += obj_data[x][list_store[y].id];
                                                }
                                                else
                                                {
                                                    html +=`<td></td>`;
                                                }
                                            }

                                            html+=`
                                            <td>`+temp_total+`</td>
                                            <td>`+formatNumberInput(obj_data[x].total_sales)+`</td>
                                        </tr>`;
                              }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="8">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                                                  
                          
                        
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_assets_store(data, list_store)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_assets_follow_store+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_assets+`</div></th>
                              <th><div>`+cms_lang.title_all_price+`</div></th>`;
      
                              for(var x in list_store)
                              {
                                 html +=`<th><div>`+list_store[x].name+`</div></th>`; 
                              }
                              
                        html +=`<th><div>`+cms_lang.title_quantity+`</div></th>
                                <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                                </tr>`;
                        
                        var obj_data = data.data_1;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {
                            for(var x in obj_data)
                              {
                                html += `
                                        <tr>
                                            <td>`+`<p>`+obj_data[x].name+`</p><p>`+obj_data[x].code+`</p>`+`</td>
                                            <td>`+`<p>`+formatNumberInput(obj_data[x].product_price)+`</p><p>`+formatNumberInput(obj_data[x].product_price_sell)+`</p>`+`</td>`;

                                            // Loop for total product sold with each store
                                            var temp_total = 0;
                                            for(var y in list_store)
                                            {
                                                if(typeof(obj_data[x][list_store[y].id]) != 'undefined')
                                                {

                                                    html +=`<td>`+obj_data[x][list_store[y].id]+`</td>`;
                                                    temp_total += obj_data[x][list_store[y].id];
                                                }
                                                else
                                                {
                                                    html +=`<td></td>`;
                                                }
                                            }

                                            html+=`
                                            <td>`+temp_total+`</td>
                                            <td>`+formatNumberInput(obj_data[x].total_sales)+`</td>
                                        </tr>`;
                              }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="8">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                                                  
                          
                        
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_order_user(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_order_follow_user+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_user+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_1 = 0;
                        var total_2 = 0;
                        var total_3 = 0;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+(obj_data[x].user_name ? obj_data[x].user_name : 'N/A')+(obj_data[x].user_display_name ? ' ['+obj_data[x].user_display_name+']' : '')+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                        <td>`+formatNumberInput(obj_data[x].total_bk)+`</td>
                                    </tr>`;
                            
                                    total_1 += obj_data[x].qty;
                                    total_2 += obj_data[x].qty_items;
                                    total_3 += obj_data[x].total_bk;
                          }
                          
                          html += `<tr>
                                <td>`+cms_lang.title_report_sale_total_price+`</td>
                                <td>`+total_1+`</td>
                                <td>`+total_2+`</td>
                                <td>`+formatNumberInput(total_3)+`</td>
                              </tr>`;
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="4">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                        
                        
                        
                      
                        html+=`      
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_order_status(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_order_follow_status+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_store+`</div></th>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_order_status_0+`</div></th>
                              <th><div>`+cms_lang.title_order_status_1+`</div></th>
                              <th><div>`+cms_lang.title_order_status_2+`</div></th>
                              <th><div>`+cms_lang.total+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var i=1;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {
                            for(var x in obj_data)
                            {
                                var total = 0;
                                var store = obj_data[x].store ? obj_data[x].store : "All";
                                // Date
                                html += `<tr><td>`+i+`</td><td>`+store+`<td>`+obj_data[x].datefm+`</td>`;
                                i++;
                                
                                // Status 1
                                if(typeof obj_data[x][0] != 'undefined')
                                {
                                    html += `<td>`+obj_data[x][0].qty+`</td>`;
                                    total += obj_data[x][0].qty;
                                }
                                else
                                {
                                    html += `<td></td>`;
                                }

                                // Status 2
                                if(typeof obj_data[x][1] != 'undefined')
                                {
                                    html += `<td>`+obj_data[x][1].qty+`</td>`;
                                    total += obj_data[x][1].qty;
                                }
                                else
                                {
                                    html += `<td></td>`;
                                }

                                // Status 3
                                if(typeof obj_data[x][2] != 'undefined')
                                {
                                    html += `<td>`+obj_data[x][2].qty+`</td>`;
                                    total += obj_data[x][2].qty;
                                }
                                else
                                {
                                    html += `<td></td>`;
                                }

                                html += `<td>`+total+`</td></tr>`;
                            }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="6">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                        
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_order_product(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_order_follow_product+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_product_id+`</div></th>
                              <th><div>`+cms_lang.title_product_code+`</div></th>
                              <th><div>`+cms_lang.title_product_name+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_1 = 0; // Total qty
                        var total_2 = 0; // Total items
                        var total_3 = 0; // Total price
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].product_id+`</td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                        <td>`+obj_data[x].total+`</td>
                                    </tr>`;
                            
                                    total_1+=obj_data[x].qty;
                                    total_2+=obj_data[x].qty_items;
                                    total_3+=obj_data[x].total_bk;
                          }
                          
                          html += `<tr>
                                <td colspan="3" style="text-align:center">`+cms_lang.title_report_sale_total_price+`</td>
                                <td>`+total_1+`</td>
                                <td>`+total_2+`</td>
                                <td>`+formatNumberInput(total_3)+`</td>
                               </tr>`;
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="6">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      
                        html +=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  
  function html_order_assets(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_order_follow_assets+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_assets_id+`</div></th>
                              <th><div>`+cms_lang.title_assets_code+`</div></th>
                              <th><div>`+cms_lang.title_assets_name+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_1 = 0; // Total qty
                        var total_2 = 0; // Total items
                        var total_3 = 0; // Total price
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].product_id+`</td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                        <td>`+obj_data[x].total+`</td>
                                    </tr>`;
                            
                                    total_1+=obj_data[x].qty;
                                    total_2+=obj_data[x].qty_items;
                                    total_3+=obj_data[x].total_bk;
                          }
                          
                          html += `<tr>
                                <td colspan="3" style="text-align:center">`+cms_lang.title_report_sale_total_price+`</td>
                                <td>`+total_1+`</td>
                                <td>`+total_2+`</td>
                                <td>`+formatNumberInput(total_3)+`</td>
                               </tr>`;
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="6">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      
                        html +=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }

function html_order_price(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_order_follow_price+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_quantity+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].total+`</td>
                                        <td>`+obj_data[x].quantity+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="3">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }  
  
  function html_product_price(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_product_follow_price+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_total_product+`</div></th>
                              <th><div>`+cms_lang.title_total_sell+`</div></th>
                              <th><div>`+cms_lang.title_total_price+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {
                            for(var x in obj_data)
                            {
                                html += `
                                        <tr>
                                            <td>`+obj_data[x].order+`</td>
                                            <td>`+obj_data[x].price_type+`</td>
                                            <td>`+obj_data[x].total_product+`</td>
                                            <td>`+obj_data[x].total_qty_sold+`</td>
                                            <td>`+obj_data[x].total_price+`</td>
                                        </tr>`;
                            }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="5">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                        
                        
                        html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  } 
  
  function html_assets_price(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_assets_follow_price+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_total_product+`</div></th>
                              <th><div>`+cms_lang.title_total_sell+`</div></th>
                              <th><div>`+cms_lang.title_total_price+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {
                            for(var x in obj_data)
                            {
                                html += `
                                        <tr>
                                            <td>`+obj_data[x].order+`</td>
                                            <td>`+obj_data[x].price_type+`</td>
                                            <td>`+obj_data[x].total_product+`</td>
                                            <td>`+obj_data[x].total_qty_sold+`</td>
                                            <td>`+obj_data[x].total_price+`</td>
                                        </tr>`;
                            }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="5">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                        
                        
                        html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  } 
  
  function html_assets_price(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_assets_follow_price+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_total_product+`</div></th>
                              <th><div>`+cms_lang.title_total_sell+`</div></th>
                              <th><div>`+cms_lang.title_total_price+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        
                        if(jQuery.isEmptyObject(obj_data) == false)
                        {
                            for(var x in obj_data)
                            {
                                html += `
                                        <tr>
                                            <td>`+obj_data[x].order+`</td>
                                            <td>`+obj_data[x].price_type+`</td>
                                            <td>`+obj_data[x].total_product+`</td>
                                            <td>`+obj_data[x].total_qty_sold+`</td>
                                            <td>`+obj_data[x].total_price+`</td>
                                        </tr>`;
                            }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="5">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                        
                        
                        html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  } 

  function html_product_bestseller(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_product_best_seller+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_product_code+`</div></th>
                              <th><div>`+cms_lang.title_product_name+`</div></th>
                              <th><div>`+cms_lang.title_product_img+`</div></th>
                              <th><div>`+cms_lang.title_total_sell+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+obj_data[x].product_image+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                        <td>`+obj_data[x].p_sell+`</td>
                                        <td>`+obj_data[x].p_original+`</td>
                                        <td>`+obj_data[x].total+`</td>
                                        <td>`+obj_data[x].profit+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="10">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;

    return html;
  }
  
  function html_assets_bestseller(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_assets_best_seller+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th><div>`+cms_lang.title_assets_code+`</div></th>
                              <th><div>`+cms_lang.title_assets_name+`</div></th>
                              <th><div>`+cms_lang.title_total_sell+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_pprice+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                        <td>`+obj_data[x].p_sell+`</td>
                                        <td>`+obj_data[x].p_original+`</td>
                                        <td>`+obj_data[x].total+`</td>
                                        <td>`+obj_data[x].profit+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="10">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;

    return html;
  }

  function html_customer_overview(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-6">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_customer_list_location+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_location+`</div></th>
                              <th><div>`+cms_lang.title_customer_quantity+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total = 0;
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].location+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                    </tr>`;
                            
                                    total+=obj_data[x].qty;
                          }
                          
                          html += `<tr><td>`+cms_lang.title_total+`</td><td>`+total+`</td></tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="2">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                        html +=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    // Data 2
    html +=`
        <div class="col-xl-6">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_customer_list_times+`</h3>
                      </div>
                      `;
                      if(data.link_export_2!= null && data.link_export_2!= '')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border-2">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_customer_times_buy+`</div></th>
                              <th><div>`+cms_lang.title_customer_quantity+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_2;
                        var total = 0;
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].range+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                    </tr>`;
                                    total += obj_data[x].qty;
                          }
                          
                          html += `<tr><td>`+cms_lang.title_total+`</td><td>`+total+`</td></tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="2">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html+=`
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;

    return html;
  }
  
  function html_customer_sales(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_customer_follow_sales+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>  
                              <th><div>`+cms_lang.title_customer+`</div></th>
                              <th><div>`+cms_lang.title_phone+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                              <th><div>`+cms_lang.title_cost+`</div></th>
                              <th><div>`+cms_lang.title_total_price+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_order = 0;
                        var total_item = 0;
                        var total_price = 0;
                        var total_profit = 0;
                        var total_cost = 0;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].name+`</td>
                                        <td>`+obj_data[x].phone+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+obj_data[x].total_item+`</td>
                                        <td>`+formatNumberInput(obj_data[x].ord_original)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].ord_total)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].profit)+`</td>
                                    </tr>`;
                            
                                    total_order+=obj_data[x].qty;
                                    total_item+=obj_data[x].total_item;
                                    total_price+=obj_data[x].ord_total;
                                    total_profit+=obj_data[x].profit;
                                    total_cost+=obj_data[x].ord_original;
                          }
                          
                          html += `<tr>
                                    <td colspan="3" align="right"><b>`+cms_lang.title_total+`</b></td>
                                    <td><b>`+total_order+`</b></td>
                                    <td><b>`+total_item+`</b></td>
                                    <td><b>`+formatNumberInput(total_cost)+`</b></td>
                                    <td><b>`+formatNumberInput(total_price)+`</b></td>
                                    <td><b>`+formatNumberInput(total_profit)+`</b></td>
                               </tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="8">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }
  
  function html_finance_daily(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_finance_daily+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_store+`</div></th>  
                              <th><div>`+cms_lang.title_total_sell+`</div></th>
                              <th><div>`+cms_lang.title_discount+`</div></th>
                              <th><div>`+cms_lang.title_cash_receipts+`</div></th>
                              <th><div>`+cms_lang.titel_cash_payment+`</div></th>
                              <th><div>`+cms_lang.title_cash_left+`</div></th>
                              <th><div>`+cms_lang.title_transfer_receipts+`</div></th>
                              <th><div>`+cms_lang.title_transfer_payment+`</div></th>
                              <th><div>`+cms_lang.title_debt+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_1 = 0;
                        var total_2 = 0;
                        var total_3 = 0;
                        var total_4 = 0;
                        var total_5 = 0;
                        var total_6 = 0;
                        var total_7 = 0;
                        var total_8 = 0;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].name+`</td>
                                        <td>`+(obj_data[x].total ? formatNumberInput(obj_data[x].total) : '')+`</td>
                                        <td>`+(obj_data[x].total_discount ? formatNumberInput(obj_data[x].total_discount) : '')+`</td>
                                        <td>`+(obj_data[x].total_cash ? formatNumberInput(obj_data[x].total_cash) : '')+`</td>
                                        <td>`+(obj_data[x].total_cash_out ? formatNumberInput(obj_data[x].total_cash_out) : '')+`</td>
                                        <td>`+(obj_data[x].total_cash_left ? formatNumberInput(obj_data[x].total_cash_left) : '')+`</td>
                                        <td>`+(obj_data[x].total_transfer_in ? formatNumberInput(obj_data[x].total_transfer_in) : '')+`</td>
                                        <td>`+(obj_data[x].total_transfer_out ? formatNumberInput(obj_data[x].total_transfer_out) : '')+`</td>
                                        <td>`+(obj_data[x].total_debt ? formatNumberInput(obj_data[x].total_debt) : '')+`</td>
                                    </tr>`;
                            
                                    total_1+=obj_data[x].total;
                                    total_2+=obj_data[x].total_discount;
                                    total_3+=obj_data[x].total_cash;
                                    total_4+=obj_data[x].total_cash_out;
                                    total_5+=obj_data[x].total_cash_left;
                                    total_6+=obj_data[x].total_transfer_in;
                                    total_7+=obj_data[x].total_transfer_out;
                                    total_8+=obj_data[x].total_debt;
                          }
                          
                          html += `<tr>
                                    <td align="right"><b>`+cms_lang.title_total+`</b></td>
                                    <td><b>`+(total_1 ? formatNumberInput(total_1) : '')+`</b></td>
                                    <td><b>`+(total_2 ? formatNumberInput(total_2) : '')+`</b></td>
                                    <td><b>`+(total_3 ? formatNumberInput(total_3) : '')+`</b></td>
                                    <td><b>`+(total_4 ? formatNumberInput(total_4) : '')+`</b></td>
                                    <td><b>`+(total_5 ? formatNumberInput(total_5) : '')+`</b></td>
                                    <td><b>`+(total_6 ? formatNumberInput(total_6) : '')+`</b></td>
                                    <td><b>`+(total_7 ? formatNumberInput(total_7) : '')+`</b></td>
                                    <td><b>`+(total_8 ? formatNumberInput(total_8) : '')+`</b></td>
                               </tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="9">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }
  
  function html_finance_record(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_finance_record+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th rowspan="2" align="center"><div>`+cms_lang.title_report_sale_num_order+`</div></th>
                              <th rowspan="2" align="center"><div>`+cms_lang.title_at_code+`</div></th>
                              <th rowspan="2" align="center"><div>`+cms_lang.title_at_name+`</div></th>
                              <th colspan="2" align="center"><div>`+cms_lang.title_open_balance+`</div></th>
                              <th colspan="2" align="center"><div>`+cms_lang.title_accrued_balance+`</div></th>
                              <th colspan="2" align="center"><div>`+cms_lang.title_close_balance+`</div></th>
                          </tr>
                          <tr>
                              <th align="center"><div>`+cms_lang.title_debit+`</div></th>
                              <th align="center"><div>`+cms_lang.title_credit+`</div></th>
                              <th align="center"><div>`+cms_lang.title_debit+`</div></th>
                              <th align="center"><div>`+cms_lang.title_credit+`</div></th>
                              <th align="center"><div>`+cms_lang.title_debit+`</div></th>
                              <th align="center"><div>`+cms_lang.title_credit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var open_debit = 0;
                        var open_credit = 0;
                        var accr_debit = 0;
                        var accr_credit = 0;
                        var close_debit = 0;
                        var close_credit = 0;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td>`+obj_data[x].code+`</td>
                                        <td>`+obj_data[x].name+`</td>
                                        <td>`+(obj_data[x].open_balance.debit ? formatNumberInput(obj_data[x].open_balance.debit) : '')+`</td>
                                        <td>`+(obj_data[x].open_balance.credit ? formatNumberInput(obj_data[x].open_balance.credit) : '')+`</td>
                                        <td>`+(obj_data[x].balance_debit ? formatNumberInput(obj_data[x].balance_debit) : '')+`</td>
                                        <td>`+(obj_data[x].balance_credit ? formatNumberInput(obj_data[x].balance_credit) : '')+`</td>
                                        <td>`+(obj_data[x].close_balance.debit ? formatNumberInput(obj_data[x].close_balance.debit) : '')+`</td>
                                        <td>`+(obj_data[x].close_balance.credit ? formatNumberInput(obj_data[x].close_balance.credit) : '')+`</td>
                                    </tr>`;
                            
                                    open_debit  +=obj_data[x].open_balance.debit;
                                    open_credit +=obj_data[x].open_balance.credit;
                                    accr_debit  +=obj_data[x].balance_debit;
                                    accr_credit +=obj_data[x].balance_credit;
                                    close_debit +=obj_data[x].close_balance.debit;
                                    close_credit+=obj_data[x].close_balance.credit;
                          }
                          
                          html += `<tr>
                                    <td colspan="3" align="right"><b>`+cms_lang.title_total+`</b></td>
                                    <td><b>`+(open_debit ? formatNumberInput(open_debit) : '')+`</b></td>
                                    <td><b>`+(open_credit ? formatNumberInput(open_credit) : '')+`</b></td>
                                    <td><b>`+(accr_debit ? formatNumberInput(accr_debit) : '')+`</b></td>
                                    <td><b>`+(accr_credit ? formatNumberInput(accr_credit) : '')+`</b></td>
                                    <td><b>`+(close_debit ? formatNumberInput(close_debit) : '')+`</b></td>
                                    <td><b>`+(close_credit ? formatNumberInput(close_credit) : '')+`</b></td>
                               </tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="9">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }
  
  function html_finance_revenue(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_finance_revenue+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_cash_plus+`</div></th>
                              <th><div>`+cms_lang.title_cash_sub+`</div></th>
                              <th><div>`+cms_lang.title_total_cash+`</div></th>
                              <th><div>`+cms_lang.title_bank_plus+`</div></th>
                              <th><div>`+cms_lang.title_bank_sub+`</div></th>
                              <th><div>`+cms_lang.title_total_bank+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td><div>`+obj_data[x].date+`<span><a href='`+site_root_domain+obj_data[x].link_search+`'> <img src='/acp/images/icons/icon_search.png'/></span></a></div></td>
                                        <td>`+(typeof(obj_data[x].credit_cash)!='undefined' ? formatNumberInput(obj_data[x].credit_cash) : '')+`</td>
                                        <td>`+(typeof(obj_data[x].debit_cash)!='undefined' ? formatNumberInput(obj_data[x].debit_cash) : '')+`</td>
                                        <td>`+(obj_data[x].total_cash ? formatNumberInput(obj_data[x].total_cash) : '')+`</td>
                                        <td>`+(typeof(obj_data[x].credit_bank)!='undefined' ? formatNumberInput(obj_data[x].credit_bank) : '')+`</td>
                                        <td>`+(typeof(obj_data[x].debit_bank)!='undefined' ? formatNumberInput(obj_data[x].debit_bank) : '')+`</td>
                                        <td>`+(obj_data[x].total_bank ? formatNumberInput(obj_data[x].total_bank) : '')+`</td>
                                    </tr>`;
                          }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="9">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }
  

  function html_finance_commission_staff(data)
  {
    var html = "";
console.log(data);
    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_finance_commission_staff+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_staff+`</div></th>
                              <th><div>`+cms_lang.title_total_commission+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+(typeof(obj_data[x].username)!='undefined' ?   obj_data[x].username  : '')+`</td>
                                        <td>`+(obj_data[x].commission ?  obj_data[x].commission  : '')+`</td>
                                    </tr>`;
                          }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="2">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }

    function html_finance_commission_service(data)
    {
        var html = "";
        console.log(data);
        // Data 1
        html +=`
            <div class="col-xl-12">
              <section class="box-typical box-typical-dashboard mb30">
                  <header class="box-typical-header">
                      <div class="tbl-row">
                          <div class="tbl-cell tbl-cell-title">
                              <h3>`+cms_lang.title_finance_commission_service+`</h3>
                          </div>
                          `;
        if(data.link_export_1!=null && data.link_export_1!='')
        {
            html +=`
                                <div class="tbl-cell tbl-cell-actions">
                                    <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                                </div>`;
        }
        html +=`
                      </div>
                  </header>
                  <div class="box-typical-body" style=" height: 326px;">
                      <table class="tbl-typical report-table-border">
                          <tbody>
                              <tr>
                                  <th><div>`+cms_lang.title_service+`</div></th>
                                  <th><div>`+cms_lang.title_total_commission+`</div></th>
                              </tr>
                            `;
        var obj_data = data.data_1;

        if(obj_data != null && obj_data != '')
        {
            for(var x in obj_data)
            {
                html += `
                                        <tr>
                                            <td>`+(typeof(obj_data[x].product_name)!='undefined' ?   obj_data[x].product_name  : '')+`</td>
                                            <td>`+(obj_data[x].commission ?  obj_data[x].commission  : '')+`</td>
                                        </tr>`;
            }
        }
        else
        {
            html += `
                                        <tr>
                                            <td colspan="2">`+cms_lang.title_no_data+`</td>
                                        </tr>`;
        }
        html += `
                          </tbody>
                      </table>
                  </div><!--.box-typical-body-->
              </section><!--.box-typical-dashboard-->
          </div>
        `;

        return html;
    }
  


  function html_customer_store(data, list_month)
  {
    var html = "";
    var obj_data = data.data_1;
    
    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_customer_follow_store+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_store+`</div></th>` ;
                      
                              // Loop for list store
                              for(var x in list_month)
                              {
                                  html+=`<th><div>`+list_month[x]+`</div></th>`;
                              }
                          html+=`</tr>`;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                              html += `<tr><td>`+obj_data[x].name+`</td>`;
                              for(var y in list_month)
                              {
                                  if(typeof(obj_data[x][list_month[y]])!='undefined')
                                  {
                                    html += `<td>`+obj_data[x][list_month[y]]+`</td>`; 
                                  }
                                  else
                                  {
                                      html += `<td></td>`; 
                                  }
                              }
                              html += `</tr>`;
                          }
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="6">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }
  
  function html_customer_product(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_customer_follow_sales+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>  
                              <th><div>`+cms_lang.title_customer+`</div></th>
                              <th><div>`+cms_lang.title_product_code+`</div></th>
                              <th><div>`+cms_lang.title_product_name+`</div></th>
                              <th><div>`+cms_lang.title_cost+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                              <th><div>`+cms_lang.title_total_price+`</div></th>
                              <th><div>`+cms_lang.title_total_cost+`</div></th>
                              
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_order = 0;
                        var total_price = 0;
                        var total_cost = 0;
                        var total_discount = 0;
                        var total_profit = 0;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td><p>`+obj_data[x].name+`</p><p>`+obj_data[x].phone+`</p><p>`+obj_data[x].email+`</p></td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+formatNumberInput(obj_data[x].cost)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].price)+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+formatNumberInput(obj_data[x].total_price)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].total_cost)+`</td>
                                        
                                        <td>`+formatNumberInput(obj_data[x].profit)+`</td>
                                    </tr>`;
                            
                                    total_order+=obj_data[x].qty;
                                    total_price+=obj_data[x].total_price;
                                    total_cost+=obj_data[x].total_cost;
                                    total_profit+=obj_data[x].profit;
                                    total_discount+=obj_data[x].discount;
                          }
                          
                          html += `<tr>
                                    <td colspan="6" align="right"><b>`+cms_lang.title_total+`</b></td>
                                    <td><b>`+total_order+`</b></td>
                                    <td><b>`+formatNumberInput(total_price)+`</b></td>
                                    <td><b>`+formatNumberInput(total_cost)+`</b></td>
                                    
                                    <td><b>`+formatNumberInput(total_profit)+`</b></td>
                               </tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="11">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }
  
  function html_customer_assets(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_customer_follow_assets+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!=null && data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_report_sale_num_order+`</div></th>  
                              <th><div>`+cms_lang.title_customer+`</div></th>
                              <th><div>`+cms_lang.title_assets_code+`</div></th>
                              <th><div>`+cms_lang.title_assets_name+`</div></th>
                              <th><div>`+cms_lang.title_cost+`</div></th>
                              <th><div>`+cms_lang.title_price+`</div></th>
                              <th><div>`+cms_lang.title_items_assets+`</div></th>
                              <th><div>`+cms_lang.title_total_price+`</div></th>
                              <th><div>`+cms_lang.title_total_cost+`</div></th>
                              
                              <th><div>`+cms_lang.title_report_sale_profit+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        var total_order = 0;
                        var total_price = 0;
                        var total_cost = 0;
                        var total_discount = 0;
                        var total_profit = 0;
                        
                        if(obj_data != null && obj_data != '')
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].order+`</td>
                                        <td><p>`+obj_data[x].name+`</p><p>`+obj_data[x].phone+`</p><p>`+obj_data[x].email+`</p></td>
                                        <td>`+obj_data[x].product_code+`</td>
                                        <td>`+obj_data[x].product_name+`</td>
                                        <td>`+formatNumberInput(obj_data[x].cost)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].price)+`</td>
                                        <td>`+obj_data[x].qty+`</td>
                                        <td>`+formatNumberInput(obj_data[x].total_price)+`</td>
                                        <td>`+formatNumberInput(obj_data[x].total_cost)+`</td>
                                        
                                        <td>`+formatNumberInput(obj_data[x].profit)+`</td>
                                    </tr>`;
                            
                                    total_order+=obj_data[x].qty;
                                    total_price+=obj_data[x].total_price;
                                    total_cost+=obj_data[x].total_cost;
                                    total_profit+=obj_data[x].profit;
                                    total_discount+=obj_data[x].discount;
                          }
                          
                          html += `<tr>
                                    <td colspan="6" align="right"><b>`+cms_lang.title_total+`</b></td>
                                    <td><b>`+total_order+`</b></td>
                                    <td><b>`+formatNumberInput(total_price)+`</b></td>
                                    <td><b>`+formatNumberInput(total_cost)+`</b></td>
                                    
                                    <td><b>`+formatNumberInput(total_profit)+`</b></td>
                               </tr>`;
                        }
                        else
                        {
                            html += `
                                    <tr>
                                        <td colspan="11">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    
    return html;
  }

  function html_finance(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_statistic_revenue+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_amount+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].datefm+`</td>
                                        <td>`+obj_data[x].amount+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="2">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;


      // Data 2
      html +=`
        <div class="col-xl-6">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_statistic_cost+`</h3>
                      </div>
                       `;
                      if(data.link_export_2!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_2+`')" link="`+data.link_export_2+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_amount+`</div></th>
                          </tr>
                        `;  
                        var obj_data = data.data_2;
                        if(obj_data.length > 0)
                        {                        
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td>`+obj_data[x].datefm+`</td>
                                        <td>`+obj_data[x].amount+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="2">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
      `;
    return html;
  }

  function runExport(link)
  {
      $.ajax({
          type: "post",
          url: link,
          beforeSend: function(){
            waitingDialog.show(cms_lang.waiting_dialog_msg);
          },
          success: function(html)
          {
            waitingDialog.hide();
            //console.log(html);return false;
            window.location.href = html;
          }
      });
  }
  
  function update_result(product)
  {
    $('#product_name').val(product);
      
    $("#suggesstion-box").hide();
    $("#suggesstion-box").html("");
  }
  
  function update_assets_result(assets_id,assets_name)
  {
    $('#ass_name').val(assets_name);
    $('#ass_id_data').val(assets_id);
      
    $("#suggesstion-box").hide();
    $("#suggesstion-box").html("");
  }
  
  $("#product_name").keyup(function(){
    if($(this).val().length < 2)
    {
       return false;
    }
    var key = $(this).val();
    
    $.ajax({
    type: "post",
    url: site_root_domain+"/?site=report&subact=product_quick_search",
    data:{ product_keyword : key },
    success: function(data){
              var obj = JSON.parse(data);
               $("#suggesstion-box").html("");
               if(obj.status == "success")
               {
                  output_li = "<ul class='list_goods'>"+obj.data_option+"</ul>"
                  $("#suggesstion-box").show();
                  $("#suggesstion-box").html(output_li);
                  $("#search-box").css("background","#FFF");

               }
               else
               {
                  $("#suggesstion-box").hide();
                  $("#suggesstion-box").html("");
                  $("#search-box").css("background","#FFF");
               }

    }
    });
  });
  
  $("#ass_name").keyup(function(){
    if($(this).val().length < 2)
    {
       return false;
    }
    var key = $(this).val();
    
    $.ajax({
    type: "post",
    url: site_root_domain+"/?site=report&subact=assets_quick_search",
    data:{ assets_keyword : key },
    success: function(data){
              var obj = JSON.parse(data);
              console.log(obj);
               $("#suggesstion-box").html("");
               if(obj.status == "success")
               {
                  output_li = "<ul class='list_goods'>"+obj.data_option+"</ul>"
                  $("#suggesstion-box").show();
                  $("#suggesstion-box").html(output_li);
                  $("#search-box").css("background","#FFF");

               }
               else
               {
                  $("#suggesstion-box").hide();
                  $("#suggesstion-box").html("");
                  $("#search-box").css("background","#FFF");
               }

    }
    });
  });
  
  
  // Load ajax transaction cus type
  // Report finance revenue
  $("#cus_type").change(function(){
    $("#object").html('');
    var type = $(this).val();
    if(type == '')
    {
       $("#object").attr("disabled","disabled"); 
       return false;
    }
    
    $.ajax({
    type: "post",
    url: site_root_domain+"/?site=report&subact=search_type",
    data:{ cus_type : type },
    success: function(data){
              var obj = JSON.parse(data);
               
               $("#object").html(obj);
               $("#object").removeAttr("disabled");
    }
    });
  });
  
  function html_inventory_quantity(data)
  {
    var html = "";

    // Data 1
    html +=`
        <div class="col-xl-12">
          <section class="box-typical box-typical-dashboard mb30">
              <header class="box-typical-header">
                  <div class="tbl-row">
                      <div class="tbl-cell tbl-cell-title">
                          <h3>`+cms_lang.title_inventory_quantity+`</h3>
                      </div>
                      `;
                      if(data.link_export_1!='')
                      {
                        html +=`
                            <div class="tbl-cell tbl-cell-actions">
                                <button class="btn btn_export" onclick="runExport('`+data.link_export_1+`')" link="`+data.link_export_1+`">`+cms_lang.title_export+`</button>
                            </div>`;
                      }
                      html +=`
                  </div>
              </header>
              <div class="box-typical-body" style=" height: 326px;">
                  <table class="tbl-typical report-table-border">
                      <tbody>
                          <tr>
                              <th><div>`+cms_lang.title_time+`</div></th>
                              <th><div>`+cms_lang.title_order+`</div></th>
                              <th><div>`+cms_lang.title_report_sale_total_price+`</div></th>
                              <th><div>`+cms_lang.title_items+`</div></th>
                          </tr>
                        `;
                        var obj_data = data.data_1;
                        if(obj_data.length > 0)
                        {                          
                          for(var x in obj_data)
                          {
                            html += `
                                    <tr>
                                        <td><div>`+obj_data[x].datefm+`<span><a href='`+site_root_domain+obj_data[x].link_search+`'> <img src='/acp/images/icons/icon_search.png'/></span></a></div></td>
                                        <td>`+obj_data[x].quantity+`</td>
                                        <td>`+obj_data[x].amount+`</td>
                                        <td>`+obj_data[x].qty_items+`</td>
                                    </tr>`;
                          }
                        }else
                        {
                            html += `
                                    <tr>
                                        <td colspan="4">`+cms_lang.title_no_data+`</td>
                                    </tr>`;
                        }
                      html += `
                      </tbody>
                  </table>
              </div><!--.box-typical-body-->
          </section><!--.box-typical-dashboard-->
      </div>
    `;
    return html;
  }
  

