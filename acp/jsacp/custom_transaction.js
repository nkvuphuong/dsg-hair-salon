// Variant
var count_row = 1;
// var number_last
var module_name = module_name ? module_name : "";
var module_name_store = module_name ? module_name : "";
/*function show_detail_asset(obj, forkey) {
    // console.log(forkey);
    var name_asset = obj.getAttribute("name");
    var description = obj.getAttribute("description");
    var price = obj.getAttribute("price");
    // var forkey = obj.getAttribute("for");
    var id = obj.getAttribute("id_asset");
    // console.log();
    // $("form#edit_asset_form input[name='ass_price']").val('');
    // console.log(description);
    $(".data_subitem_asset[for='" + forkey + "'] .row-item.active").find("input[for='item-2']").val(name_asset);
    $(".data_subitem_asset[for='" + forkey + "'] .row-item.active").find("textarea[for='item-3']").val(description);
    $(".data_subitem_asset[for='" + forkey + "'] .row-item.active").find("input[for='item-4']").val(1);
    $(".data_subitem_asset[for='" + forkey + "'] .row-item.active").find("input[for='item-5']").val(price);
    $(".data_subitem_asset[for='" + forkey + "'] .row-item.active").find("input.asset_id").val(id);
    $(".box_result_find").html("");
    calculate_money_subitem(forkey);
}*/

function loadEmailTpl(type=1, id=0)
{
    $.ajax({
        type: 'get',
        url: site_root_domain + `/?site=transactions&act=send&subact=load-email-tpl&type=${type}&id=${id}`,
        dataType: 'json',
        success: (res) => {
            if(res.status == 'ok')
            {
                let form = $("#send_email_popup");

                form.find("#email_send_to").html(res.data.mail_to == "" ? "" : `<option value="${res.data.mail_to}" selected>${res.data.mail_to}</option>`).trigger("change");
                form.find("#email_cc").html("").trigger("change");
                form.find("#email_bcc").html("").trigger("change");
                form.find("#email_title").val(res.data.title);
                // form.find("#email_content").val(res.data.content);
                tinymce.activeEditor.setContent(res.data.content);
                form.find("#cus_id").val(res.data.cus_id);
                form.find("#email_from").val(res.data.email_from);
                form.find("#email_from_name").val(res.data.email_from_name);

                $('#sendEmailPopup1').modal('show');

                /*$.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#sendEmailPopup'
                    },
                });*/
            }
        }
    })
}

function sendEmailTrx()
{
    blockLoading($("#send_email_popup").parents(".modal-dialog:first"));
    $.ajax({
        type: 'post',
        url: site_root_domain + `/?site=transactions&act=send`,
        dataType: 'json',
        data: $("#send_email_popup").serialize(),
        success: res => {
            unblockLoading($("#send_email_popup").parents(".modal-dialog:first"));
            if(res.status == 'success')
            {
                alertText(res.msg, "success", null, 'bootrap-modal', $('#sendEmailPopup1'));
            }
            else
            {
                pNotifyACP(res.msg);
            }
        },
        error: err => {
            unblockLoading($("#send_email_popup").parents(".modal-dialog:first"));
            pNotifyACP(res.msg);
            console.log(err);
        }
    })
}

function sendEmailInv()
{
    let email_tab = $("#tab_send_email");
    let email_to = email_tab.find("[name='email_to']").val();
    let email_from = email_tab.find("[name='email_from']").val();
    let email_cc = email_tab.find("[name='email_cc']").val();
    let email_bcc = email_tab.find("[name='email_bcc']").val();
    let email_title = email_tab.find("[name='email_title']").val();
    let email_content = tinymce.get('send_file_email_content').getContent();
    let trx_id = $('#box_preview_model #trx_id').val();
    let preview_type = $('#box_preview_model #preview_type').val();
    let backTo = $('#box_preview_model input[name=backTo]:first').val();
    let iFrame = $('#box_preview_model iframe').attr('src');

    $("#sendEmailLoading").show();

    $.ajax({
        type: 'post',
        url: site_root_domain + `/?site=transactions&act=send&mode=inv`,
        dataType: 'json',
        data: {
            email_to: email_to,
            email_from: email_from,
            email_cc: email_cc,
            email_bcc: email_bcc,
            email_title: email_title,
            email_content: email_content,
            trx_id: trx_id,
            type: 1,
            preview_type: preview_type,
            backTo: backTo,
            iFrame: iFrame,
        },
        success: res => {
            $("#sendEmailLoading").hide();
            if(res.status == 'success')
            {
                alertText(res.msg, "success", null, 'bootrap-modal', $('#box_preview_model'));
            }
            else
            {
                alertText(res.msg, "error", null, 'bootrap-modal', $('#box_preview_model'));
            }
        }
    })
}

function autocomplete_quick_search(type=1)
{
    $("#p_quick_search").keyup(function(){

        if($(this).val().length < 2)
        {
            return false;
        }
        var key = $(this).val();
        $.ajax({
            type: "get",
            url: site_root_domain+"/?site=transactions&subact=autocomplete",
            data:{ keyword : key, type: type},
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
    })
}

function chooseCusType(obj, isReset="0")
{
    $("[id^='custype_']").hide();
    $("[id^='custype_']").find("input, select").prop("disabled", true);
    $("#custype_" + obj.val()).show();
    $("#custype_" + obj.val()).find("input, select").prop("disabled", false);

    if(isReset == "1")
    {
        $("#getUnbilledInvoices tbody tr").remove();
        $("#getUnbilledInvoices").hide();
    }

    changeTotalTax();
}

function chooseAccount(obj, objDisplay)
{
    let value = obj.val();
    objDisplay.html(obj.find(`option[value=${value}]`).attr('balance'));
}

function formatNumberInput(number, dec_number)
{
    //dec_number: Phần thập phân
    dec_number = !isNaN(dec_number) || typeof(dec_number) !== "undefined" ? parseInt(dec_number) : 0;
    return number.toFixed(dec_number).replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
    });
}

function hideQuantityCol()
{
    $(".hidden-cols").hide();
    $('#tableTotalProduct').attr('colspan',$('#tblProduct thead tr th:visible').length-2);
    $('#tableTotalAsset').attr('colspan',$('#tblAsset thead tr th:visible').length-2);
}

function show_detail_product_3(obj)
{

    var name_product = obj.getAttribute("name");
    var price = obj.getAttribute("price");
    var id = obj.getAttribute("id_product");
    var sku = obj.getAttribute("sku");

    $(".row-item.active").find("input").val("");

    name_product != 'undefined' ? $(".row-item.active").find("input[name='sub_name[]']").val(name_product) : "";
    price != 'undefined' ? $(".row-item.active").find("input[name='sub_purchase_price[]']").val(price) : "";
    sku != 'undefined' ? $(".row-item.active").find("input[name='sub_code[]']").val(sku) : "";

    $(".row-item.active").find("input.product_id").val(id);
    $(".box_result_find_asset").html("");
    $(".data_subitem_asset .box_result_find_asset").css("display","none");
}

function show_detail_asset(obj)
{
    // Ben phiếu xuất thêm disableprice để disable các trường, price, tax, amount
    // var disableprice = $("input[name='disableprice'").val();
    // if(typeof(disableprice) == "undefined" || disableprice == null)
    // {
    //     disableprice = 0;
    // }
    var name_asset = obj.getAttribute("name");
    var description = obj.getAttribute("description");
    var price = obj.getAttribute("price");
    var forkey = obj.getAttribute("for");
    // var id = obj.getAttribute("id_asset");
    var key = obj.getAttribute("key");
    var tax = obj.getAttribute("tax");
    var item_id = $(".row-grid-asset.active").find("input[name='item_id[]']").val();

    $(".row-grid-asset.active").find("input[for='dgrid-2']").val(name_asset);
    $(".row-grid-asset.active").find("textarea[for='dgrid-3']").val(description);
    $(".row-grid-asset.active").find("input[for='dgrid-4']").val(1);
    $(".row-grid-asset.active").find("input[for='dgrid-5']").val(price);

    $(".row-grid-asset.active").find("input.ass_id").val(key);
    $(".row-grid-asset.active").find("input.ass_key").val(key);
    $(".row-grid-asset.active").find("select[for='dgrid-7'] option[value='" + tax + "']").prop("selected", true);
    $(".row-grid-asset.active").find('td.trash').html("" +
        "<span class='edit_row_asset' id=" + key + " item_id='" + item_id + "'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span>" +
        '<span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>'+
        "<span class='del_row_invoid' item_id='" + item_id + "'><i class='fa fa-trash' aria-hidden='true'></i></span>");
    $(".box_result_search").html("");
    $(".box_result_search").hide();

    // if(disableprice)
    // {
    //     $(".row-grid-asset.active").find("input[for='dgrid-5']").val('');
    //     $(".row-grid-asset.active").find("select[for='dgrid-7'] option[value='0']").prop("selected", true);
    // }else
    // {
        calculate_money_asset();
    // }

    // Save info selected
    $.ajax({
        type: "post",
        url: site_root_domain + "/?site=dashboard&subact=save_to_list_asset",
        data: {asset_id: key, item_id: item_id},
        success: function (html) {
            // console.log(html);
        }
    });

    if(typeof calculate_total_transaction == "function")
    {
        calculate_total_transaction();
    }
}

// Search hang hoa
function search_asset(key, forkey, obj_element)
{
    if($(".choose_store input[name='store_id']").length > 0)
    {
        var store_id = $(".choose_store input[name='store_id']:checked").val();
        if(typeof(store_id) == "undefined" && store_id == null)
        {
            store_id = 0;
        }
    }
    else
    {
      
        var store_id = $(".choose_store select[name='store_id'] option:selected").val();
        if(typeof(store_id) == "undefined" && store_id == null)
        {
            store_id = 0;
        }
    }

    var supplier_id = $("#content_change select[name='supplier_id']").val();
    if(typeof(supplier_id) == "undefined" && supplier_id == null)
    {
        supplier_id = 0;
    }

    // Module form tao don hang
    if(module_name_store != "")
    {
       
        // if(module_name_store == "module_order")
        // {
        //     store_id = $("form#form-signin_v1 input[name='store_id']:checked").val();
        //     if(typeof(store_id) == "undefined" && store_id == null)
        //     {
        //          store_id = $("form#form-signin_v1 select[name='store_id']").val();
        //     }

        // }
    }

    $.ajax({
        type: "post",
        url: site_root_domain + "/?subact=search_asset",
        data: {key_search: key, store_id : store_id, supplier_id: supplier_id},
        success: function (html) {
            // console.log(html);
            var obj = JSON.parse(html);
            var output_li = "";
            if (obj.status == "success") {
                $(".box_result_search").html("");
                $(".box_result_search").hide();
                $.each(obj.data, function (index, value) {
                    var description = value.ass_code === null ? "" : value.ass_code;
                    var price = value.ass_price > 0 ? value.ass_price : value.ass_purchase_price;
                    output_li += `
                    <li onclick="show_detail_asset(this)" for="${forkey}" name="${value.ass_name}" description="${description}" id_asset="${value.ass_key}" key="${value.ass_key}" price="${price}" tax="${value.ass_tax}">
                        <span class="name_show" title="${description}">${value.ass_name} - ${value.ass_price_show} (${value.cnt})</span>             
                    </li>
`;
                });
            }

            var html_show = `
                <ul class="list_asset">
                    <li class="add_new_el">
                        <i class="fa fa-plus"></i>
                        <a class="add_new_asset_transaction" href="#box_asset">Add new</a>
                    </li>
                    ${output_li}
                </ul>
            `;
// console.log(obj);
            obj_element.parent().find(".box_result_search").html(html_show);
            obj_element.parent().find(".box_result_search").show();
        }
    });
}

function calculate_money_asset() 
{
    var check_active = $(".row-grid-asset.active").length;

    if(check_active == 1)
    {
        var qty = $(".row-grid-asset.active").find("input[for='dgrid-4']").val();
        var price = $(".row-grid-asset.active").find("input[for='dgrid-5']").val();
        var tax = $(".row-grid-asset.active").find("select[for='dgrid-7']").val();
        var discountType = $(".row-grid-asset.active").find("[name='ass_discount_type[]']").val();
        var discountValue = $(".row-grid-asset.active").find("[name='ass_discount_value[]']").val();

        // Check quantity
        if (qty <= 0 && qty != '') {
            $(".row-grid-asset.active").find("input[for='dgrid-4']").val(1);
            qty = 1;
        }

        let calResult = calculateItem(price, discountType, discountValue, tax, qty, 1);

        $(".row-grid-asset.active").find("input[for='dgrid-6']").val(calResult.total.toFixed(2));

        // Total show
        show_total_assets();
        // Update asset list
        var item_id = $(".row-grid-asset.active").find("input[name='item_id[]']").val();
        // console.log(item_id);
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
       show_total_assets();
    }

    if(typeof calculate_total_transaction == "function" )
    {
        calculate_total_transaction();
    }
    
}

function show_total_assets() 
{
    var total_money_asset = 0;
      if($("form#formorder-signin_v1 #total_price").length > 0)
       {
          $("#data_table_asset .row-grid-asset").each(function(){
                var qty = $(this).find("input[for='dgrid-4']").val();
                var price = $(this).find("input[for='dgrid-5']").val();
                var tax = $(this).find("select[for='dgrid-7']").val();

                var discountType = $(this).find("[name='ass_discount_type[]']").val();
                var discountValue = $(this).find("[name='ass_discount_value[]']").val();

                let calResult = calculateItem(price, discountType, discountValue, tax, qty, 1);
                $(this).find("input[for='dgrid-6']").val(calResult.total);
            total_money_asset += calResult.total;
             });  
             var total_show = formatNumberInput(total_money_asset);
              $("#total_show_asset").html(total_show);
             $("#total_show_asset").attr("total_asset",total_money_asset);
              var total_asset_xxx = $("#total_show_asset").attr("total_asset");
              var total_product_xxx = $("#total_show").attr("total_product");
              var temp_total = parseFloat(total_product_xxx) + parseFloat(total_asset_xxx);
              var total_price_xxx =    formatNumberInput(temp_total);
              $("#total_price").html(total_price_xxx  );
       }
       else
       {
            $(".total_amount_asset").each(function () {
                var total = parseFloat($(this).val());
                total = isNaN(total) ? 0 : total;
                total_money_asset += total;
                // console.log(total_money_asset);
            });
            var total_show = formatNumberInput(total_money_asset);
            $("#total_show_asset").html(total_show);
            //Dùng cho phiếu trả hàng sẽ không ảnh hưởng đến cái khác dù không có html - TanLv
            $("#total_refund").html(total_show );
       }


}

function calculate_money_subitem_asset(forkey) {
    var total = 0;
    $(".data_subitem_asset[for='" + forkey + "'] .row-item").each(function () {
        // console.log($(this));
        var qty = $(this).find("input[for='item-4']").val();
        var price = $(this).find("input[for='item-5']").val();
        var tax = $(this).find("input[for='item-6']").val();
        // Check quantity
        if (qty <= 0 && qty != '') {
            $(this).find("input[for='item-4']").val(1);
            qty = 1;
        }
        qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 0;
        price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
        tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;
        total += qty * price;
    });

    // var price_crr = $("form#edit_asset_form input[name='ass_price']").val();
    // price_crr = !isNaN(parseFloat(price_crr)) ? parseFloat(price_crr) : 0;
    // console.log(price_crr);
    // if(price_crr == 0)
    // {
    price_crr = total;
    $("form.form_data[for='" + forkey + "'] input[name='ass_price']").val(price_crr);
    // }
    var tax_asset = $("form.form_data[for='" + forkey + "'] select[name='ass_tax']").val();
    var tax_crr = !isNaN(parseFloat(tax_asset)) ? parseFloat(tax_asset) : 0;
    var price_tax = Math.round((tax_crr * price_crr) / 100);
    var asset_price = price_crr + price_tax;
    var total_show = formatNumberInput(asset_price);
    $("form.form_data[for='" + forkey + "'] span#asset_total").html(total_show);
}

function calculate_subitem_asset() {
    var price = $("form.form_data input[name='ass_price']").val();
    var tax = $("form.form_data select[name='ass_tax']").val();
    price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
    tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;
    var total = parseFloat(price + (price * tax) / 100);
    // console.log(total);
    var total_show = formatNumberInput(total);
    $("form.form_data #asset_total").html(total_show);
}

function calculate_money_subitem_asset() {
    var total = 0;
    $("#data_table_asset .grid-td-asset").each(function () {

        var qty = $(this).find("input[for='dgrid-5']").val();
        var price = $(this).find("input[for='dgrid-6']").val();
        var tax = $(this).find("input[for='dgrid-7']").val();
        qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 1;
        price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
        tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;


        total += qty * price;


    });

    var p_price = $("#p_price").val();

    $("#p_price").val(total);

}

function searchInvoice(type=1, cus_type=1) {

    console.log(cus_type);

    if(cus_type == 1)
    {
        var id = $('#cus_id').val();

        if (!id) {
            notie.alert(3, cms_lang.trx_cus_err);
            return false;
        }
    }

    if(cus_type == 2)
    {
        var id = $('#supplier_id').val();

        if (!id) {
            notie.alert(3, cms_lang.trx_supplier_err);
            return false;
        }
    }

    if(cus_type == 3)
    {
        var id = $('#user_assign').val();

        if (!id) {
            notie.alert(3, cms_lang.trx_user_assign_err);
            return false;
        }
    }


    v = $("#search_invoice_id").val();
    if (isNaN(v)) {
        notie.alert(3, cms_lang.not_data);
    } else {
        getInvoice(id, v, type, cus_type);
    }


    $("#search_invoice_id").val("");
}

var calculateTotalTransactionResult = {};

function calculate_total_transaction()
{
    let rows = $(".row-grid, .row-grid-asset");
    let data = {
        'subtotal' : 0,
        'discount' : 0,
        'tax' : 0,
        'total' : 0,
        'commission' : 0,
    };

    let totalDiscountType = +$("#total_discount_type").val()*1;
    let totalDiscountValue = +$("#total-discount-show").val()*1;

    let checkTax = true; //Kiểm tra giá trị thuế giữa các row có đồng bộ không
    let taxValue;

    $.each(rows, (index, row) => {
        let product_id = $(row).find("[name='product_id[]']:first, [name='ass_id[]']:first").val();
        if(product_id) {
            let price = +$(row).find("[name='product_price[]']:first, [name='ass_price[]']:first").val();
            let oldPrice = +$(row).find("[name='product_old_price[]']:first, [name='ass_old_price[]']:first").val();
            let quantity = +$(row).find("[name='product_quantity[]']:first, [name='ass_quantity[]']:first").val();
            let cycle = +$(row).find("[name='product_cycle[]']:first").val();
            let tax_percent = +$(row).find("[name='product_tax[]']:first, [name='ass_tax[]']:first").val();

            let discountType = +$(row).find("[name='product_discount_type[]']:first,[name='ass_discount_type[]']:first").val();
            let discountValue = $(row).find("[name='product_discount_value[]']:first, [name='ass_discount_value[]']:first").val();

            let commissionType = +$(row).find("[name='product_commission_type[]']:first").length ? $(row).find("[name='product_commission_type[]']:first").val()*1 : 0;
            let commissionValue = +$(row).find("[name='product_commission_value[]']:first").length ? $(row).find("[name='product_commission_value[]']:first").val()*1 : 0;
            let calResult = calculateItem(oldPrice > price ? oldPrice : price, discountType, discountValue, tax_percent, quantity, cycle);

            data.subtotal += calResult.subTotal - calResult.totalDiscount;
            data.tax += isNaN(calResult.totalTax) ? 0 : calResult.totalTax;
            data.total += isNaN(calResult.total) ? 0 : calResult.total;
            data.commission += !commissionType ? (calResult.subTotal-calResult.totalDiscount)*commissionValue/100 : commissionValue*1*quantity;

            if(index == 0)
            {
                taxValue = tax_percent;
            }
            else
            {
                if(taxValue != tax_percent)
                {
                    checkTax = false;
                }
            }
        }
    })


    data.discount = totalDiscountType == 0 ? (data.subtotal * totalDiscountValue)/100 : totalDiscountValue;
    if(checkTax)
    {
        data.tax = ((data.subtotal-data.discount) * taxValue) / 100;
        $("#total-discount-show").attr("disabled", false);
    }
    else
    {
        data.tax = isNaN(data.tax) ? 0 : data.tax;
        data.discount = 0;
        $("#total-discount-show").attr("disabled", true);
        $("#total-discount-show").val(data.discount);
    }

    data.subtotal = isNaN(data.subtotal) ? 0 : data.subtotal;
    data.discount = isNaN(data.discount) ? 0 : data.discount;

    data.total = data.subtotal - data.discount + data.tax;
    data.commission = isNaN(data.commission) ? 0 : data.commission;

    $(".subtotal-show").html(formatNumberInput(data.subtotal));
    $(".discount-show").html(formatNumberInput(data.discount));

    if($("#total_discount_type").val() == 0){//Percent
        var datasub = data.subtotal == 0 ? 0 : (data.discount*100)/data.subtotal;
            datasub = datasub.toFixed(2);
        $(".total-discount-show").val(datasub);
    } else if($("#total_discount_type").val() == 1){
        $(".total-discount-show").val(data.discount.toFixed(2));
    }
    
    $(".tax-show").html(formatNumberInput(data.tax));
    $(".total-show").html(formatNumberInput(data.total));
    $(".commission-show").html(formatNumberInput(data.commission));

    if($("#total_commission_type").val() == 0){//Percent
        $(".total-commission-show").val((data.subtotal-data.discount) == 0 ? 0 : (data.commission*100)/(data.subtotal-data.discount));
    } else if($("#total_commission_type").val() == 1) {
        $(".total-commission-show").val(data.commission);
    }

    // Check neu module order thi do du lieu tong tien moi vao => Plong
    if($("form#formorder-signin_v1 #total_price").length > 0)
    {
      $("#total_price").html(formatNumberInput(data.total) );
    }

    // Check neu module order thi tinh shipping fee
    if($("form#formorder-signin_v1").length > 0)
    {
        calculateShipFee();
    }

    calculateTotalTransactionResult = data;

    return data;
}

function getInvoice(c, v, t=1, cus_type=1) {

    $.getJSON(
        site_root_domain + "/?site=transactions&subact=get-invoice",
        {
            id : c,
            code_id : v,
            type: t,
            cus_type: cus_type,
        }
    )
        .done(function(json) {
            if (json.status == 'success') {

                /*$("#form-signin_v1 #trx_cus").val(json.cus_info.name);
                $("#form-signin_v1 #cus_id").val(json.cus_info.id);
                $("#form-signin_v1 #trx_email").val(json.cus_info.email);
                $("#form-signin_v1 #trx_address").val(json.cus_info.address);*/

                if($("#invoice_" + json.data.invoice_no).length){
                    $("#invoice_" + json.data.invoice_no).prop("checked", true).trigger("change");
                    return false;
                }

                var str = ""
                    + "<tr>"
                    + "<td>"
                    + "<input type='checkbox' checked id ='invoice_" + json.data.invoice_no + "' name='item[" + json.data.invoice_no + "]' onchange='changeCheckInvoiceParent(this, " + json.data.invoice_no + ")' class='parent' >"
                    + "</td>"
                    + "<td>"
                    + json.data.invoice_no_as
                    + "</td>"
                    + "<td>"
                    + json.data.due
                    + "</td>"
                    + "<td>"
                    + json.data.original
                    + "</td>"
                    + "<td>"
                    + json.data.payment
                    + "</td>"
                    + "<td>"
                    + "<div class='form-group'>"
                    + "<div style='position: relative;'>"
                    + "<input type='number' class='form-control' name='tri_payment[" + json.data.invoice_no + "]' value='" + json.data.payment + "' onchange='changeTotalTax()' onkeypress='return check_enter_number(event,this);' onfocusout='check_enter_number_2(this,0);'>"
                    + "</div>"
                    + "</div>"
                    + "</td>"
                    + "</tr>";

                $("#item_line > tbody" ).append(str);
                $("input[name='item[]'").change(function() {
                    changeTotalTax();
                });
                changeTotalTax();
            } else {
                notie.alert(3, json.message);
            }
        })
        .fail(function(jqxhr, textStatus, error) {
            console.log(error);
        });
}
function changeCheckInvoiceParent(i, e) {
    if($(i).prop('checked')) {
        $(".class_item_" + e).prop("checked", true);
    } else {
        $(".class_item_" + e).prop("checked", false);
    }
    changeTotalTax();
}
function changeCheckInvoiceChildren(e) {
    var check = true;
    $(".class_item_" + e).each(function() {
        if(! $(this).prop('checked')) {
            check = false;
        }
    });
    if (check) {
        $("#invoice_" + e).prop("checked", true);
    } else {
        $("#invoice_" + e).prop("checked", false);
    }
    changeTotalTax();
}
function changeTotalItem(e) {
    var tr = $(e).closest('tr');
    var quantity = parseFloat(tr.find('td:eq(2) > input').val());
    var total = parseFloat(tr.find('td:eq(3) > input').val());
    var tax = parseFloat(tr.find('td:eq(4) > input').val());
    var total_tax = (quantity * total) + (quantity * total * tax / 100);
    tr.find('td:eq(5)').html((total_tax).toFixed(0));
    changeTotalTax();
}
function changeTotalTax() {
    var total_tax = 0;
    $(".parent").each(function() {
        $(this).parent().parent().find("td:nth-last-child(1)").find('input').prop("disabled", true);

        if ($(this).is(':checked')) {
            $(this).parent().parent().find("td:nth-last-child(1)").find('input').prop("disabled", false);
            total_tax += parseFloat($(this).parent().parent().find("td:nth-last-child(1)").find('input').val());
        } else {
            var tmp = this.id.split('_');
            $("#item_" + tmp[1]).find('tbody').find('tr').each(function() {

                $(this).find("td:nth-last-child(1)").find('input').prop("disabled", true);

                if($(this).find("td:first-child").find('input').is(':checked')) {
                    $(this).find("td:nth-last-child(1)").find('input').prop("disabled", false);
                    total_tax += parseFloat($(this).find("td:nth-last-child(1)").find('input').val());
                }
            });
        }
    });
    $(".total_tax_show").val(total_tax.toFixed(2));
    $(".total_tax_show").html(formatNumberInput(total_tax));

    // if(+$(".amount_received_show").val() == 0 || +$(".amount_received_show").val() < total_tax)
    {
        $(".amount_received_show").val(total_tax.toFixed(2));
    }
}
function deleteInvoice(e, i) {
    $("#item_" + i).parent().parent().remove()
    $(e).parents("tr").remove();
    changeTotalTax();
}

function inputLoading() {
    var str = "<div class=\"cssload-container\" id=\"input_loading\">"
        + "<div class=\"cssload-progress cssload-float cssload-shadow\">"
        + "<div class=\"cssload-progress-item\"></div>"
        + "</div>"
        + "</div>";
    return str;
}

function changeCusAjax(e, sub) {
    var tmp = e.value.split(' |-| ');
    $('#trx_cus').val(tmp[0]);
    $('#trx_email').val(tmp[1]);
    $('#trx_address').val(tmp[2]);
    $('#cus_id').val(tmp[3]);
    action_customer(tmp[3]);
    $('#trx_cus_ajax').hide();
    $('#item_line > thead > tr > td > button').prop('disabled', false);
    $('#item_line > tbody').empty();
    $("#item_line > tfoot > tr > td:nth-last-child(2) > input" ).val('0');

    if(sub == 7)
    {
        getExpenseBill(tmp[3]);
    }
}

function getExpenseBill(cus_id)
{
    // console.log(cus_id);
    $.getJSON(
        site_root_domain + "/?site=transactions&type=2&subact=get-expense-bill",
        {
            cus_id : cus_id,
        }
    )
        .done(function(json) {
            if (json.status == 'success') {

                let str = "";

                $.each(json.data, function(k, data){

                        if(!$("#invoice_" + data.invoice_no).length)
                        {
                            str += ""
                                + "<tr>"
                                + "<td>"
                                + "<input type='checkbox' checked id ='invoice_" + data.invoice_no + "' name='item[" + data.invoice_no + "]' onchange='changeCheckInvoiceParent(this, " + data.invoice_no + ")' class='parent' >"
                                + "</td>"
                                + "<td>"
                                + "#"+data.invoice_no
                                + "</td>"
                                + "<td>"
                                + data.due
                                + "</td>"
                                + "<td>"
                                + data.original
                                + "</td>"
                                + "<td>"
                                + data.open
                                + "</td>"
                                + "<td>"
                                + "<input type='number' class='form-control' name='tri_payment[" + data.invoice_no + "]' value='" + data.payment + "' onchange='changeTotalTax()'>"
                                + "</td>"
                                + "</tr>";
                        }
                    })

                $("#item_line > tbody" ).append(str);
                $("input[name='item[]'").change(function() {
                    changeTotalTax();
                });
                changeTotalTax();
            } else {
                notie.alert(3, json.message);
            }
        })
        .fail(function(jqxhr, textStatus, error) {
            console.log(error);
        });
}

function getUnbilledByCustomer(id, sub, cus_type=1)
{
    $("#getUnbilledInvoices").hide();

    $.getJSON(
        site_root_domain + "/?site=transactions&type=2&subact=get-unbilled-by-customer",
        {
            id : id,
            sub : sub,
            cus_type : cus_type,
        }
    )
        .done(function(json) {
            if (json.status == 'success') {

                let str = "";

                if(json.data.length)
                {
                    $("#getUnbilledInvoices").show();
                }

                $.each(json.data, function(k, data){

                    if(!$("#invoice_" + data.invoice_no).length)
                    {
                        str += `
                        <tr>
                            <td>
                                <input type="checkbox" id="invoice_${data.invoice_no}" name="item[${data.invoice_no}]" onchange="changeCheckInvoiceParent(this, ${data.invoice_no})" class="parent">
                            </td>
                            <td>${data.invoice_no_as}</td>
                            <td>${data.due}</td>
                            <td>${data.original}</td>
                            <td>
                                ${data.remain}
                            </td>
                            <td>
                                <div class="form-group">
                                    <div style="position: relative">
                                        <input type="number" class="form-control" name="tri_payment[${data.invoice_no}]" value="${data.payment}" onchange="changeTotalTax()" data-validation="[V<=${data.remain}]" data-validation-message="${cms_lang.error_max_payment_amount}" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        `;
                    }
                })

                $("#item_line > tbody" ).append(str);
                $("input[name='item[]'").change(function() {
                    changeTotalTax();
                });

                $("#item_line [id^='invoice_']:first").trigger("change");

            } else {
                changeTotalTax();
                notie.alert(3, json.message);
            }
        })
        .fail(function(jqxhr, textStatus, error) {
            console.log(error);
        });
}

function trDue(dateFormat='d/m/Y', separator='/') {

    var dateFormatO = dateFormat;
    var dateFormat = dateFormat.split(separator);

    var dIndex = dateFormat.indexOf('d');
    var mIndex = dateFormat.indexOf('m');
    var yIndex = dateFormat.indexOf('Y');

    var date = $('#trx_payment_date').val();
    var days = $('#trx_terms').val();
    if (date != '') {
        var tmp = date.split(separator);
        date = new Date(tmp[yIndex], tmp[mIndex], tmp[dIndex]);
        tmp = new Date(date.getTime() + parseInt(days)*24*60*60*1000);
        var d = tmp.getDate() < 10 ? '0' + tmp.getDate() : tmp.getDate();
        var m = tmp.getMonth() < 10 ? '0' + tmp.getMonth() : tmp.getMonth();
        var y = tmp.getFullYear();

        $('#trx_due_date').val(dateFormatO.replace(/d|m|Y/gi, function(x){
            console.log(y);
            if(x == 'd')
            {
                return d;
            }
            else if(x == 'm')
            {
                return m;
            }
            else if(x == 'Y')
            {
                return y;
            }
        }));
    }
}

function hideShowAccount() {
    if ($('#trx_method').val() == 1) {
        $('.hide_show_account').prop("disabled", false);
    } else {
        $('.hide_show_account').prop("disabled", true);
    }
}

function selectEstimateStatus(obj = "#trx_estimate_status", displayObj = "#estimate_status_text")
{
    let value = $(obj).val();

    let text = $(obj).find(`option[value='${value}']`).text();

    let classCss = [];
    classCss[0] = 'label-default';
    classCss[5] = 'label-success';
    classCss[3] = 'label-warning';
    classCss[4] = 'label-danger';

    $(displayObj).text(text).removeClass("label-default label-success label-warning label-danger label-primary").addClass(classCss[value]);

    $(".est_status_display").hide();
    $(".est_status_display input").prop("disabled",true);

    if(value != "0")
    {
        $(".est_status_display").show();
        $(".est_status_display input").prop("disabled",false);
    }
}

$(document).ready(() => {

    $('body').click(function() {
        $('#suggesstion-box').html("");
        $("#suggesstion-box").hide();

    });

    $('#suggesstion-box').click(function(event){
        event.stopPropagation();
    });


    /*$('#trx_cus').keyup( function() {
        if ($('#trx_cus').val() != '') {
            $('#trx_cus_ajax').show();
            $('#trx_cus_ajax').html(inputLoading());
            let sub = $(this).attr("sub");
            $.get(
                site_root_domain + "/?site=transactions&type=1&act=receipt-add-search-cus",
                { key: $('#trx_cus').val() },
                function(html) {
                    $('#trx_cus_ajax').html("<select multiple class=\"form-control\" onchange=\"changeCusAjax(this, " + sub + ")\">" + html + "</select>");
                }
            );
        } else {
            $('#trx_cus_ajax').hide();
        }
    });*/

    $('#data_table_asset').click(function () {
        $('.box_result_find, .box_result_search').html("");
        $("#data_table_asset .box_result_find, #data_table_asset .box_result_search").css("display", "none");
    });

    $("#data_table_asset").on("select", ".grid-td-asset", function (e) {
        $(this).trigger("click");
    });

    $("#data_table_asset").on("click", ".grid-td-asset", function (e) {
        var echeck = e.target.getAttribute('check');
        // Ben phiếu xuất thêm disableprice để disable các trường, price, tax, amount
        // var disableprice = $("input[name='disableprice'").val();
        // if(typeof(disableprice) == "undefined" || disableprice == null)
        // {
        //     disableprice = 0;
        // }

        if (echeck == "chtd") {
            var html_clone = $(this).parent().html();
            var focus_column = $(this).attr("for");
            // Check clone row
            var check = $(this).parent().attr("rowtr");
            count_row++;
            if (check == "last-row") /// Tang row
            {
                // console.log(html_clone);
                // count_row ++;

                $(this).parent().attr("rowtr", "");
                $("#data_table_asset").append("<tr keyrow='" + count_row + "' class='row-grid-asset' rowtr='last-row'>" + html_clone + "</tr>");


                $("#data_table_asset tr[rowtr='last-row']").find("input,textarea,select").val('');

                // if(disableprice == 0)
                // {
                    // Plong: gán giá trị mặc định quantity = 1, thue - 10% khi them row mới
                    $("#data_table_asset tr[rowtr='last-row']").find(".quan_list").val(1);
                    $("#data_table_asset tr[rowtr='last-row']").find(".tax_list option[value=10]").prop("selected", true);

                    $("#data_table_asset tr[rowtr='last-row']").find("[name='ass_discount_value[]']").val("0");
                    $("#data_table_asset tr[rowtr='last-row']").find("[name='ass_discount_type[]']").val("0");

                // }else
                // {
                //     $("#data_table_asset tr[rowtr='last-row']").find("input[name='ass_price[]']").prop("disabled","disabled");
                //     $("#data_table_asset tr[rowtr='last-row']").find("select[name='ass_tax[]']").prop("disabled","disabled");
                //     $("#data_table_asset tr[rowtr='last-row']").find("input[name='ass_amount[]']").prop("disabled","disabled");
                //     $("#data_table_asset tr[rowtr='last-row']").find(".tax_list option[value=0]").prop("selected", true);
                // }


                // insert number
                var count_check = 1;
                var inc_item = 0;
                $("#data_table_asset tr").each(function () {
                    $(this).find(".number").html("#" + count_check);
                    $(this).find(".item_id").val(inc_item);
                    $(this).find(".del_row_invoid").attr("item_id", inc_item);
                    count_check++;
                    inc_item++;
                });
            }
            // console.log(html_clone);
            // $(".grid-td-asset").children().show();
            var row_tr = $(this).parent();
            var keyrow = row_tr.attr("keyrow");
            if (typeof keyrow !== typeof undefined && keyrow !== false) {
            }
            else {
                keyrow = 1;
            }

            // Dong cac row khong active
            if (is_mobile == 0) {
                // Check trên mobile thi ẩn border input
                $(this).parent().parent().find("input, textarea, select").addClass("hidden_border");
            }

            $(this).parent().parent().find("textarea").css("resize", "none");
            $(this).parent().parent().find("span.dropdown_btn").hide();


            $("#keyrow_active_asset").val(keyrow);
            $(".row-grid-asset").removeClass("active");
            $(this).parent().addClass("active");
            var check_row = $(this).parent().attr("class");
            if (check_row == "row-grid-asset active") {
                $(this).children("[for='" + focus_column + "']").focus();

                $(this).parent().find("input, textarea, select").removeClass("hidden_border");
                $(this).parent().find("textarea").css("resize", "vertical");
                $(this).parent().find("span.dropdown_btn").show();
                $(".box_result_search").html("");
                $(".box_result_search").hide();

            } else {

            }


        } else if (echeck == "chspan") {
            var forkey = $(this).attr("for");
            search_asset("", forkey, $(this));

        }

        var module_name;
        // Remove check total price if module is assets
        if (module_name != 'assets') {
            $("#data_table_asset tr").each(function () {
                // Check quantity
                var numberC = $(this).find("input[for='dgrid-5']").val();
                // console.log(numberC);
                if (numberC == 0 || numberC == '') {
                    // $(this).find("input[for='dgrid-5']").val(1); Tanlv tạm thời đóng vì không muốn set don giá bằng 1 bên phiếu yêu cầu.
                    var qty = 1;
                    var tax = $(this).find("select[for='dgrid-7']").val();
                    var price = $(this).find("input[for='dgrid-6']").val();

                    qty = !isNaN(parseInt(qty)) ? parseInt(qty) : 0;
                    price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0;
                    tax = !isNaN(parseFloat(tax)) ? parseFloat(tax) : 0;

                    var total = qty * price;
                    var price_tax = Math.round((tax * total) / 100);
                    total = total + price_tax;

                    $(this).find("input[for='dgrid-6']").val(total);

                    // Total show
                    show_total_assets();
                }
            });
        }


    });

    // show info asset
    $("#data_table_asset").on("keyup", ".search_asset", function (e) {
        var key_search = $(this).val();
        var forkey = $(this).attr("for");
        search_asset(key_search, forkey, $(this));
        // Xoá info asset
        // $(this).parent().find("input[name='ass_id[]']").val('');
        // var item_id = $(this).parent().find("input[name='item_id[]']").val();
        // $.ajax({
        //     type: "post",
        //     url: site_root_domain + "/?site=store_request&subact=remove_asset",
        //     data: {item_id: item_id},
        //     success: function (html) {
        //         // console.log(html);
        //     }
        // });

    });

    // Xoá row
    $("#data_table_asset").on("click", ".del_row_invoid", function () {
        var obj = $(this).parent().parent();
        var count_del = $("#data_table_asset tr").length;
        // console.log(count_del);
        if (count_del > 1) {
            $(this).parent().parent().remove();
            show_total_assets();
            // insert number
            var count_check = 1;
            var inc_item = 0;
            $("#data_table_asset tr").each(function () {
                $(this).find(".number").html("#" + count_check);
                $(this).find(".item_id").val(inc_item);
                $(this).find(".del_row_invoid").attr("item_id", inc_item);
                count_check++;
                inc_item++;
                if (count_check == count_del) {
                    $(this).attr("rowtr", "last-row");
                }

            });

            // remove asset
            var item_id = $(this).attr("item_id");
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=store_request&subact=remove_product",
                data: {item_id: item_id, type: 1},
                success: function (html) {
                    // console.log(html);
                }
            });

            if(typeof calculate_total_transaction == "function")
            {
                calculate_total_transaction();
            }
        }

    });

    $("#data_table_asset").on("click", ".add_new_asset", function () {

        var row_main = $(this).parents(".row-grid-asset.active");
        var asset_name = row_main.find("input[for='dgrid-2']").val();
        var asset_description = row_main.find("textarea[for='dgrid-3']").val();
        var asset_price = row_main.find("input[for='dgrid-5']").val();
        var asset_tax = row_main.find("select[for='dgrid-7']").val();
        var item_id = row_main.find("input[name='item_id[]']").val();
        var sup_id = $("#form_add_request select[name='supplier_id']").val();
        // console.log($(this).parent());
        // console.log(row_main);
        // $("#edit_asset_form")[0].reset();
        clearForm($("#edit_asset_form"));
        var html_line = $("#edit_asset_form .data_subitem_asset tr:last-child").html();
        $("#edit_asset_form .data_subitem_asset").html("<tr class='row-item' rowtr='last-row'>" + html_line + "</tr>");
        $("#edit_asset_form .data_subitem_asset").find(".number").html("#1");
        // Đỗ dữ liệu
        $("#box_asset input[name='ass_name']").val(asset_name);
        $("#box_asset textarea[name='ass_description']").val(asset_description);
        $("#box_asset input[name='ass_price']").val(asset_price);
        $("#box_asset input[name='ass_tax']").val(asset_tax);
        $("#box_asset input[name='item_id']").val(item_id);
        $("#box_asset select[name='sup_id'] option[value='" + sup_id + "']").prop("selected", true);
        $("#box_asset select[name='sup_id'] option[value='" + sup_id + "']").trigger("change");

        $(".box_result_search").html("");
        $(".box_result_search").hide();
        $(".form_data .box_edit").html('');
        calculate_subitem();
        // Change title asset and button asset
        $(".change_title_asset").html(lang_asset_add);
        $(".box_control").html("<input class='btn btn_add_asset' type='button' value='" + lang_btn_add_save + "'/>");
        $.magnificPopup.open({
            type: 'inline',
            items: {
                src: '#box_asset'
            },
        });

    });

    $("#edit_asset_form").on("click", ".btn_add_asset", function () {
        $("#box_asset #ufile_output_b64").val('');
        var data = $("#edit_asset_form").serialize();
        var file_data = $("input[name='ass_image']").prop("files")[0];
        var item_id = $("input[name='item_id']").val();
        var form_data = new FormData();
        form_data.append("upload_img", file_data);
        $(".error_msg").hide();
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=assets&subact=ajax_add_asset&item_id=" + item_id + "&" + data,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
            },
            success: function (html) {
                // console.log(html);
                var obj = JSON.parse(html);

                $("#edit_asset_form .error_msg").html('');
                if (obj.status == "error") {
                    $("#edit_asset_form .error_msg").css("color", "red");
                    $("#edit_asset_form .error_msg").css("font-style", "italic");
                    $("#edit_asset_form .error_msg").css("font-size", "14px");
                    $("#edit_asset_form .error_msg").css("margin", "0px");
                    $("#edit_asset_form .error_msg").show();
                    $("#edit_asset_form .error_msg").html(obj.msg);
                    $("#edit_asset_form input[name=ass_name]").focus();

                } else {
                    // $("#edit_asset_form")[0].reset();
                    clearForm($("#edit_asset_form"));

                    // Insert data just add new
                    var cur_row_active = $("#keyrow_active").val();

                    $("#data_table_asset tr").each(function (i, row) {
                        var cur_key = i + 1;

                        if (cur_key == cur_row_active) {

                            $(row).find('input[name="ass_name[]"]').val(obj.data.asset_name);
                            $(row).find('input[name="ass_id[]"]').val(obj.data.asset_id);
                            $(row).find('textarea[name="ass_description[]"]').val(obj.data.asset_description);
                            $(row).find('input[name="ass_quantity[]"]').val(1);
                            $(row).find('input[name="ass_price[]"]').val(obj.data.asset_price);
                            $(row).find('select[name="ass_tax[]"]').val(obj.data.asset_tax);
                            $(row).find('td.trash').html("<span class='edit_row_asset' id=" + obj.data.asset_id + " item_id='" + item_id + "'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span>" +
                                "<span class='del_row_invoid' item_id='" + item_id + "'><i class='fa fa-trash' aria-hidden='true'></i></span>");
                            calculate_money_asset();
                        }


                    });
                    $.magnificPopup.close();
                   // alertText(obj.msg, 'success');

                    call_notify("Tài sản",obj.msg,"success","");

                }
            }
        });
    });

     // Validate NSX
    var valid_assets = $("form#edit_asset_form").validatenew({
          focusInvalid: true,
          rules: {
            // simple rule, converted to {required:true}
            store_id: "required",
            ass_purchase_price: "required", 
            pgroup_id: "required",
            ass_name: "required",
            ass_quantity: "required"
          },
          messages:{
            store_id: lang_field_required,
            ass_purchase_price: lang_field_required,
            pgroup_id: lang_field_required,
            ass_name: lang_field_required,
            ass_quantity: lang_field_required

          }
        });


    // open popup edit asset
    $("#data_table_asset").on("click", ".edit_row_asset", function () {

        waitingDialog.show(cms_lang.waiting_dialog_msg);
        $("#asset_submit_type").val("update");
        $("#data_table_asset #ass_key").val("");
        $(".grid-td-asset").removeClass("active");
        $(".row-grid-asset").removeClass("active");
        $(this).parent().parent().addClass("active");

        // Dong cac row khong active
        $(this).parents("#data_table_asset").find("input, textarea, select").addClass("hidden_border");
        $(this).parents("#data_table_asset").find("textarea").css("resize", "none");
        $(this).parents("#data_table_asset").find("span.dropdown_btn").hide();

        // Active dòng hiện tại
        $(this).parent().parent().find("input, textarea, select").removeClass("hidden_border");
        $(this).parent().parent().find("textarea").css("resize", "vertical");
        $(this).parent().parent().find("span.dropdown_btn").show();

        $(".box_result_search").html("");
        $(".box_result_search").hide();
        // Change title asset and button asset
        $(".change_title_asset").html(lang_asset_edit);
        $(".box_control").html("<input class='btn btn_edit_asset' type='button' etype='0' value='" + lang_btn_edit_save + "'/> <input class='btn btn_add_to_request' type='button' value='" + lang_btn_save_request + "'/> <input class='btn btn_edit_asset' type='button' etype='1' value='" + lang_btn_add_save + "'/>");

        var key = $(this).attr("id");
        var item_id = $(this).attr("item_id");
        var html = '';

        var i = 1;

        // Ajax get data
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=assets&subact=ajax_edit_asset",
            data: {key: key},
            dataType: 'json',
            success: function (data) {
                waitingDialog.hide();
                if(data.status == 'error')
                {
                    alert(data.msg);
                    return false;
                }

                let parentSelector = $("#edit_asset_form");
 
                parentSelector.find("#pgroup_id").val(data.pgroup_id);
                // parentSelector.find(`[name='store_id']`, false);
                // parentSelector.find(`[name='store_id'][value='${data.store_id}']`).attr("checked", true);
                parentSelector.find(`[name='store_id']`).val(data.store_id);
 


                parentSelector.find("#ass_name").val(data.ass_name);
                parentSelector.find("#ass_quantity").val(data.ass_quantity);
                parentSelector.find("#ass_warranty").val(data.ass_warranty);
                parentSelector.find("#supplier_id").val(data.supplier_id);
                parentSelector.find("#ass_original_price").val(data.ass_original_price);
                parentSelector.find("#ass_purchase_price").val(data.ass_purchase_price);
                parentSelector.find("#ass_price").val(data.ass_price);
                parentSelector.find("#shi_id").val(data.shi_id);
                parentSelector.find("#shi_name").val(data.shi_id ? data.shipment_info.shi_name : "");
                parentSelector.find("#ass_code").val(data.ass_code);
                parentSelector.find("#ass_desc").val(data.ass_desc);
                parentSelector.find("#ass_key").val(data.ass_key);
                parentSelector.find("#item_id").val(item_id);



                $.each(data.sub_items, (key, item) => {

                    let taxSeletected = [];
                    taxSeletected[0] = item.ass_tax == 0 ? "selected" : "";
                    taxSeletected[10] = item.ass_tax == 10 ? "selected" : "";

                    html += `
                <tr class="row-item">
                        <td class="item-td" for="dgrid-1" check="chtd"><span class="number">#${i}</span></td>
                        <td class="item-td" for="dgrid-2" check="chtd">
                            <div class="box_container">
                                <figure class="text_r">
                                    <input type="text" for="dgrid-2" name="sub_name[]" check="chtd" class="form-control find_product tr_name hidden_border" autocomplete="off" value="${item.ass_name}">
                                    <div class="box_result_find_asset" style="display:none"></div>
                                </figure>                                    
                            </div>
                            <input type="hidden" class="product_id tr_id" name="sub_id[]" value="${item.product_id}">
                        </td>

                    <td class="item-td" for="dgrid-3" check="chtd">
                        <input type="text" for="dgrid-3" name="sub_quantity[]" value="${item.cnt}" class="form-control quan_list tr_quantity hidden_border" onkeypress="return check_enter_number(event,this);">
                    </td>

                    <td class="item-td" for="dgrid-4" check="chtd">
                        <input type="text" for="dgrid-4" name="sub_purchase_price[]" class="form-control tr_pprice hidden_border" onkeypress="return check_enter_number(event,this);" value="${item.ass_purchase_price}">
                    </td>
                                            
                    <td class="item-td" for="dgrid-5" check="chtd">
                        <input type="text" for="dgrid-5" name="sub_price[]" class="form-control tr_price hidden_border" onkeypress="return check_enter_number(event,this);" value="${item.ass_price}">
                    </td>
                    
                    <td class="item-td" for="dgrid-6" check="{$chtd}">
                        <select for="dgrid-6" name="sub_tax[]" class="form-control tr_tax hidden_border">
                                <option ${taxSeletected[0]} value="0">0%</option>
                                <option ${taxSeletected[10]} value="10">10%</option>
                        </select>
                    </td>
                    <td class="item-td" for="dgrid-7" check="chtd">
                        <input for="dgrid-7" name="sub_warranty[]" id="sub_warranty" type="text" class="form-control tr_warranty datetimepicker-1 hidden_border" value="${item.ass_warranty}">
                    </td>

                    <td class="trash" for="dgrid-8" check="chtd">
                    <span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                </tr>
`;
                    i++;
                })

                html += `
                <tr class="row-item" rowtr="last-row">
                        <td class="item-td" for="dgrid-1" check="chtd"><span class="number">#${i}</span></td>
                        <td class="item-td" for="dgrid-2" check="chtd">
                            <div class="box_container">
                                <figure class="text_r">
                                    <input type="text" for="dgrid-2" name="sub_name[]" check="chtd" class="form-control find_product tr_name hidden_border" autocomplete="off">
                                    <div class="box_result_find_asset" style="display:none"></div>
                                </figure>                                    
                            </div>
                            <input type="hidden" class="product_id tr_id" name="sub_id[]" value="">
                        </td>

                    <td class="item-td" for="dgrid-3" check="chtd">
                        <input type="text" for="dgrid-3" name="sub_quantity[]" value="1" class="form-control quan_list tr_quantity hidden_border" onkeypress="return check_enter_number(event,this);">
                    </td>

                    <td class="item-td" for="dgrid-4" check="chtd">
                        <input type="text" for="dgrid-4" name="sub_purchase_price[]" class="form-control tr_pprice hidden_border" onkeypress="return check_enter_number(event,this);" "="">
                    </td>
                                            
                    <td class="item-td" for="dgrid-5" check="chtd">
                        <input type="text" for="dgrid-5" name="sub_price[]" class="form-control tr_price hidden_border" onkeypress="return check_enter_number(event,this);">
                    </td>
                    
                    <td class="item-td" for="dgrid-6" check="{$chtd}">
                        <select for="dgrid-6" name="sub_tax[]" class="form-control tr_tax hidden_border">
                                <option value="0">0%</option>
                                <option value="10" selected>10%</option>
                        </select>
                    </td>

                    <td class="item-td" for="dgrid-7" check="chtd">
                        <input for="dgrid-7" name="sub_warranty[]" id="sub_warranty" type="text" class="form-control tr_warranty datetimepicker-1 hidden_border">
                    </td>

                    <td class="trash" for="dgrid-8" check="chtd">
                        <span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span>
                    </td>
                </tr>
`;

                $(".data_subitem_asset").html(html);

                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#box_asset'
                    },
                });

            }
        });
        
    });

    $("#edit_asset_form").on("click", ".btn_edit_asset", function () {
        if($("form#edit_asset_form").validnew())
        {
            $("#box_asset #ufile_output_b64").val('');
            var data = $("#edit_asset_form").serialize();
            var form_data = new FormData();

            $(".error_msg").hide();

            if($("#asset_submit_type").val() == "update")
            {
                var url = site_root_domain + "/?site=assets&act=edit_do&subact=ajax&by_key=1";
            }
            else
            {
                var url = site_root_domain + "/?site=assets&act=add_do&subact=ajax";
            }


            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function () {
                },
                success: function (obj) {
                    csrf_token();
                    $("#edit_asset_form .error_msg").html('');
                    if (obj.status == "error") {
                        /*$("#edit_asset_form .error_msg").css("color", "red");
                        $("#edit_asset_form .error_msg").css("font-style", "italic");
                        $("#edit_asset_form .error_msg").css("font-size", "14px");
                        $("#edit_asset_form .error_msg").css("margin", "0px");
                        $("#edit_asset_form .error_msg").show();
                        $("#edit_asset_form .error_msg").html(obj.msg);*/
                        pNotifyACP(obj.msg);
                        $("#edit_asset_form input[name=ass_name]").focus();

                    } else {
                        // $("#edit_asset_form")[0].reset();
                        clearForm($("#edit_asset_form"));
                        // Insert data just add new
                        var cur_row_active = $("#keyrow_active").val();

                        /*$("#data_table_asset tr").each(function (i, row) {
                            var cur_key = i + 1;

                            if (cur_key == cur_row_active) {
                                // console.log(cur_row_active);
                                $(row).find('input[name="ass_name[]"]').val(obj.data.asset_name);
                                $(row).find('input[name="ass_id[]"]').val(obj.data.asset_id);
                                $(row).find('textarea[name="ass_description[]"]').val(obj.data.asset_description);
                                $(row).find('input[name="ass_quantity[]"]').val(1);
                                $(row).find('input[name="ass_price[]"]').val(obj.data.asset_price);
                                $(row).find('select[name="ass_tax[]"] option[value="' + obj.data.asset_tax + '"]').prop("selected", true);

                                calculate_money_asset();
                            }


                        });*/

                        var parentSelector = $(".row-grid-asset.active");
                        var price = parseInt(obj.data.ass_price) > 0 ? parseInt(obj.data.ass_price) : parseInt(obj.data.ass_purchase_price);
                        parentSelector.find('input[name="ass_name[]"]').val(obj.data.ass_name);
                        parentSelector.find('input[name="ass_id[]"]').val(obj.data.ass_key);
                        parentSelector.find('input[name="ass_key[]"]').val(obj.data.ass_key);
                        parentSelector.find('textarea[name="ass_code[]"]').val(obj.data.ass_code);
                        parentSelector.find('input[name="ass_quantity[]"]').val(1);
                        parentSelector.find('input[name="ass_price[]"]').val(price);
                        parentSelector.find('.edit_row_asset').attr("id", obj.data.ass_key);
                        parentSelector.find('select[name="ass_tax[]"] option[value="10"]').prop("selected", true);

                        calculate_money_asset();

                        $.magnificPopup.close();
                       // alertText(obj.msg, 'success');
                        call_notify("Tài sản",obj.msg,"success","");
                    }
                }
            });
        }else
        {
            valid_assets.focusInvalid();
            return false;
        }
    });

    $(".data_subitem_asset").on("select", ".item-td", function (e) {
        $(this).trigger("click");
    });

    $(".data_subitem_asset").on("click", ".item-td", function (e) {
        var echeck = e.target.getAttribute('check');
        var cform = $(this).parent().parent().attr("for");
        // console.log(cform);
        // if (echeck == "chtd")
        {
            var html_clone = $(this).parent().html();
            var focus_column = $(this).attr("for");
            // Check clone row
            var check = $(this).parent().attr("rowtr");
            // console.log(check);
            if (check == "last-row") {
                // console.log(html_clone);
                // count_row ++;
                // console.log(html_clone.html());
                $(this).parent().attr("rowtr", "");
                $(".data_subitem_asset[for='" + cform + "']").append("<tr class='row-item' rowtr='last-row'>" + html_clone + "</tr>");
                $(".data_subitem_asset[for='" + cform + "'] tr[rowtr='last-row']").find("input, textarea").val('');
                $(".data_subitem_asset[for='" + cform + "'] tr[rowtr='last-row']").find(".quan_list").val(1);
                $(".data_subitem_asset[for='" + cform + "'] tr[rowtr='last-row']").find(".tax_list option[value=10]").prop("selected", true);
                // insert number
                var count_check = 1;
                $(".data_subitem_asset[for='" + cform + "'] tr").each(function () {
                    $(this).find(".number").html("#" + count_check);
                    count_check++;

                });
            }
            // console.log(html_clone);
            // $(".item-td").children().show();

            $(".data_subitem_asset[for='" + cform + "'] .row-item").removeClass("active");
            $(this).parent().addClass("active");
            var check_row = $(this).parent().attr("class");
            // console.log(check_row);
            if (check_row == "row-item active") {
                $(this).parent().parent().find("input, textarea, select").addClass("hidden_border");
                $(this).parent().parent().find("textarea").css("resize", "none");
                $(this).parent().parent().find("span.dropdown_btn").hide();
                $(".box_result_find").html("");

            }

            $(this).children("[for='" + focus_column + "']").focus();
            $(this).parent().find("input, textarea, select").removeClass("hidden_border");
            $(this).parent().find("textarea").css("resize", "vertical");
            $(this).parent().find("span.dropdown_btn").show();


        }

        $(".data_subitem_asset[for='" + cform + "'] tr").each(function () {
            // Check quantity
            var numberC = $(this).find("input[for='item-4']").val();
            // console.log(numberC);
            if (numberC == 0 || numberC == '') {
                $(this).find("input[for='item-4']").val(1);
                var qty = 1;
                // Total show
                calculate_money_subitem(cform);
            }
        });

    });

    // show info asset
    $(".data_subitem_asset").on("keyup",".find_product",function(){
        var key_search = $(this).val();
        var forkey = $(this).attr("for");
        var cform = $(this).parents(".data_subitem_asset").attr("for");
        // console.log(cform);
        var obj_element = $(this);

        // reset
        var checkreset = $(this).next().val();

        if(checkreset)
        {
            $(this).parent().parent().find("input[name='sub_product_price[]'], input[name='sub_product_id[]'], textarea").val('');
            $(this).parent().parent().find("input[name='sub_product_quantity[]']").val(1);
            $(this).parent().parent().find("select[name='product_tax[]'] option[value=10]").prop("selected",true);
            calculate_money_subitem(cform);
        }

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
                    $(".box_result_find_asset").html("");
                    $.each(obj.data, function(index, value)
                    {
                        var description = value.product_description === null ?  "" : value.product_description;
                        // var func = 'show_detail_product(this,"'+cform+'")';
                        var func = 'show_detail_product_3(this)';
                        output_li += "<li class='typeahead-item' onclick='"+func+"' for='"+forkey+"' name='"+value.product_name+"' description='"+description+"' id_product='"+value.product_id+"'  price='"+value.product_price+"'><span class='name_show' title='"+description+"'>"+value.product_name+"</span></li>";


                    });
                }

                // console.log(output_li);
                var html_show = "<ul class='list_goods typeahead-list'>"
                    +   output_li
                    + "</ul>";
                // console.log(obj);
                obj_element.parent().find(".box_result_find_asset").html(html_show);
                obj_element.parent().find(".box_result_find_asset").show();

            }
        });
    });


    // Xoá row
    $(".data_subitem_asset").on("click", ".del_row_invoid", function () {
        var obj = $(this).parent().parent();
        var cform = $(this).parent().parent().parent().attr("for");
        var count_del = $(".data_subitem_asset[for='" + cform + "'] tr").length;
        // console.log(cform);
        // console.log(count_del);
        if (count_del > 1) {
            $(this).parent().parent().remove();
            calculate_money_subitem(cform);
            // insert number
            var count_check = 1;
            $(".data_subitem_asset[for='" + cform + "'] tr").each(function () {
                $(this).find(".number").html("#" + count_check);
                count_check++;
                if (count_check == count_del) {
                    $(this).attr("rowtr", "last-row");
                }

            });
        }

    });

    $("body").on("click", ".clone_row_invoid", function(){
        let cloneRow = $(this).parents("tr:first").clone();
        cloneRow.removeClass('active');
        cloneRow.find("input, textarea, select").addClass("hidden_border");
        cloneRow.find(".dropdown_btn").hide();
        $(this).parents("tr:first").after(cloneRow);

        //set row no
        $.each($(this).parents("table:first").find("tr"), function(no, row){
            if($(row).is('[keyrow]'))
            {
                $(row).attr('keyrow',no);
            }
            $(row).find(".number").html("#"+no);
            $(row).find("[item_id]").attr('item_id',no-1);
        })

        show_total_assets();
        show_total_goods();

        if(typeof calculate_total_transaction == "function")
        {
            calculate_total_transaction();
        }
    })

    /**********************************************************************************
     *   END ADD SUBITEM PRODUCT
     ***********************************************************************************/

    $(".select_asset_category").change(function () {

        var asset_category_id = $(this).val();

        if (asset_category_id) {
            if (pms['asset_category_edit'] == 1) {
                $(".box_asset_category").html("<a id='" + asset_category_id + "' class='btn_gen btn_edit_asset_category pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
            }
        } else {
            $(".box_asset_category").html("");
        }
    });

    // Add Nhóm sp
    $(".add_new_asset_category").click(function (e) {
        e.stopPropagation();

        $(".title_group_asset").html("Thêm nhóm sản phẩm");
        $(".change_action_asset_category").html("<input class='btn btn_add_asset_category' type='button' value='Thêm nhóm sản phẩm'/>");

        //   $(".error").html('');
        // $("form#add_group_asset_form")[0].reset();
        clearForm($("form#add_group_asset_form"));
        var p_type = $("form#form-signin_v1 [name='p_type']").val();

        $("form#add_group_asset_form input[name='pg_type'][value='" + p_type + "']").prop("checked", "checked");
        $("form#add_group_asset_form #upload_img_show").attr("src", "");
        $("form#add_group_asset_form input[name='base64_image']").val("");
        get_group_asset_2(p_type);

        $.magnificPopup.open({
            type: 'inline',
            items: {
                src: '#box_add_group_asset'
            }
        });
    });

    // Add line
    $(".btn_add_line_asset").click(function () {
        var html_line = $("#data_table_asset tr[rowtr='last-row']").html();
        // Clear last-row
        $("#data_table_asset tr").attr("rowtr", "");
        // var check = 4;
        for (var i = 1; i <= 4; i++) {
            $("#data_table_asset").append("<tr class='grid-td-asset' rowtr=''>" + html_line + "</tr>");
        }

        // Add last-row
        $("#data_table_asset tr:last-child").attr("rowtr", 'last-row');
        // insert number
        var count_check = 1;
        var inc_item = 0;
        $("#data_table_asset tr").each(function () {
            $(this).find(".number").html("#" + count_check);
            $(this).find(".item_id").val(inc_item);
            $(this).find(".del_row_invoid").attr("item_id", inc_item);
            count_check++;
            inc_item++;
        });
    });

    // Clear all line
    $(".btn_del_line_asset").click(function () {
        var html_line = $("#data_table_asset tr[rowtr='last-row']").html();
        // Clear last-row
        // $("#data_table_asset tr").attr("rowtr","");
        var html_row = "";
        for (var i = 1; i <= 2; i++) {
            if(i == 1)
            {
                html_row += "<tr class='row-grid-asset active' rowtr=''>" + html_line + "</tr>";
            }
            else
            {
                html_row += "<tr class='row-grid-asset' rowtr=''>" + html_line + "</tr>";
            }
            
        }

        // Add line moi
        $("#data_table_asset").html(html_row);
        // Add last-row
        $("#data_table_asset tr:last-child").attr("rowtr", 'last-row');
        // insert number
        var count_check = 1;
        var inc_item = 0;
        $("#data_table_asset tr").each(function () {
            $(this).find(".number").html("#" + count_check);
            $(this).find(".item_id").val(inc_item);
            $(this).find(".del_row_invoid").attr("item_id", inc_item);
            count_check++;
            inc_item++;
        });
        // remove asset
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=store_request&subact=remove_product",
            data: {type: "all"},
            success: function (html) {
                
            }
        });
        calculate_money_asset();
    });

    // load trang thi tính toán tổng tiền
    calculate_money_asset();

    $("#box_asset").on("click", ".btn_add_to_request", function () {
        if($("form#edit_asset_form").validnew())
        {
            var data = $(this).parents(".form_data").serialize();
            // var file_data = $("input[name='ass_image']").prop("files")[0];
            var item_id = $("input[name='item_id']").val();
            // var form_data = new FormData();
            // form_data.append("upload_img", file_data);
            $(".error_msg").hide();
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=assets&subact=ajax_add_for_bill",
                data: data,
                // cache: false,
                // contentType: false,
                // processData: false,
                beforeSend: function () {
                },
                success: function (html) {
                    // console.log(html); return false;
                    var obj = JSON.parse(html);

                    $("#edit_asset_form .error_msg").html('');
                    if (obj.status == "error") {
                        $("#edit_asset_form .error_msg").css("color", "red");
                        $("#edit_asset_form .error_msg").css("font-style", "italic");
                        $("#edit_asset_form .error_msg").css("font-size", "14px");
                        $("#edit_asset_form .error_msg").css("margin", "0px");
                        $("#edit_asset_form .error_msg").show();
                        $("#edit_asset_form .error_msg").html(obj.msg);
                        $("#edit_asset_form input[name=ass_name]").focus();

                    } else {
                        // $("#edit_asset_form")[0].reset();
                        clearForm($("#edit_asset_form"));
                        // Insert data just add new
                        var cur_row_active = item_id;

                        $("#data_table_asset tr").each(function (i, row) {
                            var cur_key = i;

                            if (cur_key == cur_row_active) {
                                var price = parseInt(obj.data.ass_price) ? parseInt(obj.data.ass_price) :  parseInt(obj.data.ass_purchase_price); 
                                $(row).find('input[name="ass_name[]"]').val(obj.data.ass_name);
                                $(row).find('input[name="ass_id[]"]').val(obj.data.ass_id);
                                $(row).find('textarea[name="ass_code[]"]').val(obj.data.ass_code);
                                // $(row).find('input[name="ass_quantity[]"]').val(1);
                                $(row).find('input[name="ass_price[]"]').val(price);
                                $(row).find('select[name="ass_tax[]"]').val(obj.data.ass_tax);
                                $(row).find('td.trash').html("<span class='edit_row_asset' id=" + obj.data.ass_id + " item_id='" + item_id + "'><i class='fa fa-pencil-square-o' aria-hidden='true'></i>" +
                                    "</span><span class='del_row_invoid' item_id='" + item_id + "'><i class='fa fa-trash' aria-hidden='true'></i></span>");
                                calculate_money_asset();
                            }


                        });
                        $.magnificPopup.close();
                        // alertText(obj.msg, 'success');
                        call_notify("Tài sản",obj.msg, 'success','')

                    }
                }
            });
        }else
        {
            valid_assets.focusInvalid();
            return false; 
        }
    });

    /**********************************************************************************
     *   ADD PRODUCT GROUP
     ***********************************************************************************/
    $(".add_asset_category").click(function (e) {
        e.stopPropagation();
        var val_return = $(this).parent().attr("checkreturn");
        $(".title_change_pg").html(lang_pg_add);
        $(".change_action_pg").html("<input class='btn btn_add_pg' type='button' value='" + lang_pg_add + "'/>");
        if (typeof(checkreturn) == undefined || val_return == "") {
            $("form#form_asset_category input[name='checkReturn']").val("");
        } else {
            $("form#form_asset_category input[name='checkReturn']").val(val_return);
        }
        $(".error").html('');
        // $("form#form_asset_category")[0].reset();
        clearForm($("form#form_asset_category"));
        $("form#form_asset_category #upload_img_show").attr("src", "");
        // $("form#add_manufacture_form textarea").val("");
        $.magnificPopup.open({
            type: 'inline',
            items: {
                src: '#box_asset_category'
            }
        });
    });

    $("form#form_asset_category").on("click", ".btn_add_pg", function () {
        if ($("form#form_asset_category").valid()) {
            $("form#form_asset_category #ufile_output_b64").val('');
            var data = $("form#form_asset_category").serialize();
            var file_data = $("input[name='pg_avartar']").prop("files")[0];
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.pg_error_msg").hide();
            $("p.pg_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=asset_category&subact=ajax_add_pg&" + data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (html) {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    if (obj.status == "success") {
                        // console.log(obj.asset_category_id)
                        // console.log($("#form_asset_category select[name='pg_parent']"));
                        $(".select_pg").html(obj.data.data_option);
                        $(".select_pg option[value='" + obj.data.asset_category_id + "']").prop("selected", true);
                        $(".select_pg").trigger("change");

                        var checkreturn = $("form#form_asset_category input[name='checkReturn']").val();
                        $.magnificPopup.close();
                        if (checkreturn) {
                            // alertText(obj.msg, "success", checkreturn);
                            $("p.error_msg").show();
                            $("p.error_msg").css("color", "green");
                            $("p.error_msg").css("font-size", "14px");
                            setTimeout(function () {
                                $("p.error_msg").fadeOut();
                                $("p.error_msg").css("color", "red");
                            }, 2000);
                            $("p.error_msg").html(obj.msg);
                        } else {
                            alertText(obj.msg, "success");
                        }
                    } else {
                        $("p.pg_error_msg").show();
                        $("p.pg_error_msg").css("color", "red");
                        $("p.pg_error_msg").css("font-size", "14px");
                        $("p.pg_error_msg").html(obj.msg);
                        setTimeout(function () {
                            $("p.pg_error_msg").fadeOut();
                        }, 2000);
                    }
                }
            });
        } else {
            valid_shipment.focusInvalid();
            return false;
        }
    });

    var valid_pg = $("form#form_asset_category").validatenew({
        focusInvalid: true,
        rules: {
            pg_name: "required"
        },
        messages: {
            pg_name: lang_field_required,
        }
    });

    $(".select_pg").change(function () {
        var pg_id = $(this).val();

        $(".select_pg option[value='" + pg_id + "']").prop("selected", true);
        if (pg_id) {
            if (pms['asset_category_edit'] == 1) {
                $(".action_pg .box_edit").html("<a id='" + pg_id + "' class='edit_asset_category btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
            }
        } else {
            $(".action_pg .box_edit").html("");
        }
    });


    $("#box_asset").on("click", ".edit_asset_category", function (e) {
        e.stopPropagation();
        var pg_id = $(this).attr("id");
        var val_return = $(this).parents(".box_action").attr("checkreturn");
        $(".title_change_pg").html(lang_pg_edit);
        $(".change_action_pg").html("<input class='btn btn_edit_pg' type='button' value='" + lang_pg_edit + "'/>");
        if (typeof(checkreturn) == undefined || val_return == "") {
            $("form#form_asset_category input[name='checkReturn']").val("");
        } else {
            $("form#form_asset_category input[name='checkReturn']").val(val_return);
        }
        $(".error").html('');
        // $("form#form_asset_category")[0].reset();
        clearForm($("form#form_asset_category"));
        $("form#form_asset_category #upload_img_show").attr("src", "");

        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=asset_category&subact=ajax_getinfo_pg",
            data: {pg_id: pg_id},
            success: function (html) {
                // console.log(html);
                var obj = JSON.parse(html);

                $("form#form_asset_category select[name='pg_parent']").html(obj.data.data_option);
                $("form#form_asset_category input[name='pg_name']").val(obj.data.asset_category_name);
                $("form#form_asset_category input[name='pg_id']").val(obj.data.asset_category_id);
                $("form#form_asset_category input[name='pg_code']").val(obj.data.asset_category_code);
                $("form#form_asset_category textarea[name='pg_description']").val(obj.data.asset_category_description);
                $("form#form_asset_category input[name='pg_type'][value='" + obj.data.asset_category_type + "'] ").prop("checked", true);
                var link_img = "";
                if (obj.data.asset_category_avartar) {
                    link_img = site_parent_domain + "/uploads/asset/" + obj.data.asset_category_avartar;
                }

                $("form#form_asset_category #upload_img_show").attr("src", link_img);

                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#box_asset_category'
                    }
                });
            }
        });


    });

    $("#form_asset_category").on("click", ".btn_edit_pg", function (e) {
        if ($("form#form_asset_category").valid()) {
            var pg_id = $("form#form_asset_category input[name='pg_id']").val();
            $("form#form_asset_category #ufile_output_b64").val('');
            var data = $("form#form_asset_category").serialize();
            var file_data = $("input[name='pg_avartar']").prop("files")[0];
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.pg_error_msg").hide();
            $("p.pg_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=asset_category&subact=ajax_edit_pg&" + data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (html) {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    if (obj.status == "success") {
                        // console.log(obj.data.data_option);
                        // console.log($("#form_asset_category select[name='pg_parent']"));
                        $(".select_pg").html(obj.data.data_option);
                        $(".select_pg option[value='" + pg_id + "']").prop("selected", true);
                        $(".select_pg").trigger("change");

                        var checkreturn = $("form#form_asset_category input[name='checkReturn']").val();
                        $.magnificPopup.close();
                        if (checkreturn) {
                            // alertText(obj.msg, "success", checkreturn);
                            $("p.error_msg").show();
                            $("p.error_msg").css("color", "green");
                            $("p.error_msg").css("font-size", "14px");
                            setTimeout(function () {
                                $("p.error_msg").fadeOut();
                                $("p.error_msg").css("color", "red");
                            }, 2000);
                            $("p.error_msg").html(obj.msg);
                        } else {
                            alertText(obj.msg, "success");
                        }
                    } else {
                        $("p.pg_error_msg").css("color", "red");
                        $("p.pg_error_msg").css("font-size", "14px");
                        $("p.pg_error_msg").show();
                        $("p.pg_error_msg").html(obj.msg);
                        setTimeout(function () {
                            $("p.pg_error_msg").fadeOut();
                        }, 2000);
                    }
                }
            });
        } else {
            valid_shipment.focusInvalid();
            return false;
        }
    });

    $("#box_add_group_asset").on("click", ".btn_add_asset_category", function () {

        if ($("form#add_group_asset_form").valid()) {

            $("form#add_group_asset_form input[name='base64_image']").val("");
            var data = $("form#add_group_asset_form").serialize();
            var file_data = $("input[name='pg_avartar']").prop("files")[0];
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.pgmanu_error_msg").hide();
            $("p.pgmanu_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=asset_category&subact=ajax_add_asset_category&" + data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (html) {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    if (obj.status == "success") {

                        var p_type = $("form#form-signin_v1 [name='p_type']").val();
                        get_group_asset(p_type);
                        // $("form#form-signin_v1 #p_asset_category").html(obj.data_option);


                        setTimeout(function () {
                            $("form#form-signin_v1 select[name='p_asset_category'] option[value='" + obj.pg_id + "']").prop("selected", true);
                        }, 2000);

                        if (pms['asset_category_edit'] == 1) {
                            if ($("form#form-signin_v1 [name='p_type']").val() == obj.pg_type) {

                                $(".box_asset_category").html("<a id='" + obj.pg_id + "' class='btn_edit_asset_category btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                            }

                        }
                        $.magnificPopup.close();

                        alertText(obj.msg, "success");


                    } else {
                        $("p.pgmanu_error_msg").show();
                        $("p.pgmanu_error_msg").html(obj.msg);
                    }
                }
            })
        }
        else {
            return false;

        }
    });

    // Edit NHom sp
    $("form#form-signin_v1").on("click", ".btn_edit_asset_category", function (e) {
        // $("form#add_group_asset_form")[0].reset();
        clearForm($("form#add_group_asset_form"));
        var group_asset_id = $(this).attr("id");
        $(".pgmanu_error_msg").html('');
        // console.log(man_id);
        $(".title_group_asset  ").html("Chỉnh sửa nhóm sản phẩm");
        $(".change_action_asset_category").html("<input class='btn btn_edit_do_group_asset' type='button' value='Lưu lại'/>");
        e.stopPropagation();
        // Ajax get data
        $.ajax({
            type: "post",
            url: site_root_domain + "/?site=asset_category&subact=ajax_get_data_asset_category",
            data: {group_asset_id: group_asset_id},
            success: function (html) {
                // console.log(html);
                var obj = JSON.parse(html);
                // console.log(obj);
                setTimeout(function () {
                    $("#add_group_asset_form select[name='pg_parent'] option[value='" + obj.asset_category_parent + "']").prop("selected", true);
                }, 1000);
                $("#add_group_asset_form input[name='ass_category_id']").val(obj.asset_category_id);

                $("#add_group_asset_form input[name='pg_name']").val(obj.asset_category_name);
                $("#add_group_asset_form input[name='pg_code']").val(obj.asset_category_code);
                $("#add_group_asset_form textarea[name='pg_description']").html(obj.asset_category_description);


                $("#add_group_asset_form input[name='pg_type'][value='" + obj.asset_category_type + "']").prop("checked", true);
                $("#add_group_asset_form input[name='pg_status'][value='" + obj.asset_category_status + "']").prop("checked", true);


                if (obj.asset_category_avartar) {
                    var url_img = site_parent_domain + "/uploads/category/thumbnail/" + obj.asset_category_avartar;
                    $("#add_group_asset_form #upload_img_show").attr("src", url_img);
                }

                $.magnificPopup.open({
                    type: 'inline',
                    items: {
                        src: '#box_add_group_asset'
                    },
                });

            }
        });

    });

    // Edir nhom sp
    $("#box_add_group_asset").on("click", ".btn_edit_do_group_asset", function () {

        if ($("form#add_group_asset_form").valid()) {

            $("form#add_group_asset_form input[name='base64_image']").val("");
            var data = $("form#add_group_asset_form").serialize();
            var file_data = $("input[name='pg_avartar']").prop("files")[0];
            var form_data = new FormData();
            form_data.append("upload_img", file_data);
            $("p.pgmanu_error_msg").hide();
            $("p.pgmanu_error_msg").html('');
            $.ajax({
                type: "post",
                url: site_root_domain + "/?site=asset_category&subact=ajax_edit_asset_category&" + data,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (html) {
                    // console.log(html);
                    var obj = JSON.parse(html);
                    if (obj.status == "success") {

                        var p_type = $("form#form-signin_v1 [name='p_type']").val();
                        get_group_asset(p_type);
                        // $("form#form-signin_v1 #p_asset_category").html(obj.data_option);


                        setTimeout(function () {
                            $("form#form-signin_v1 select[name='p_asset_category'] option[value='" + obj.pg_id + "']").prop("selected", true);
                        }, 2000);

                        if (pms['asset_category_edit'] == 1) {
                            if ($("form#form-signin_v1 [name='p_type']").val() == obj.pg_type) {

                                $(".box_asset_category").html("<a id='" + obj.pg_id + "' class='btn_edit_asset_category btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                            }

                        }
                        $.magnificPopup.close();

                        alertText(obj.msg, "success");


                    } else {
                        alertText(obj.msg, "obj.msg");

                    }
                }
            })
        }
        else {
            return false;

        }
    });

    $("#data_table_asset").on("click", ".add_new_asset_transaction", () => {
        $("#asset_submit_type").val("add");
        $.magnificPopup.open({
            type: 'inline',
            closeOnContentClick: false,
            items: {
                src: '#box_asset'
            },
        });
    })

    $(".select_supplier_asset").change(function(){
        var sup_id = $(this).val();
        var actfor = $(this).attr("for");

        if(actfor == "change")
        {
            $(".select_supplier_asset option[value='"+sup_id+"']").prop("selected", true);
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
                    $("#box_asset .box_edit_supplier").html("<a id='"+sup_id+"' class='btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>");
                }
            }
        }else
        {
            $(".box_edit_supplier").html("");
        }
    });
})

function attachFilesTrx(obj, event)
{
    //configuration
    var max_file_size       = 2048576; //allowed file size. (1 MB = 1048576)
    var allowed_file_types    = ['image/png', 'image/gif', 'image/jpeg', 'image/pjpeg', ]; //allowed file types
    var result_output       = '#output'; //ID of an element for response output
    var my_form_id        = $(obj).attr("id"); //ID of an element for response output
    var progress_bar_id     = '.list_upload'; //ID of an element for response output
    var total_files_allowed   = 3; //Number files allowed to upload

    var proceed = true; //set proceed flag
    var error = []; //errors
    var total_files_size = 0;

    if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
        error.push("Your browser does not support new File API! Please upgrade."); //push error text
    }else{
        var total_selected_files = $(obj)[0].elements['upload_file[]'].files.length; //number of files
;
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

        if(!total_selected_files) return true;

        $($(obj)[0].elements['upload_file[]'].files).each(function(i, ifile)
        {
            // console.log(i);
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
                    form_data.append("attach_files", ifile);

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
                        // location.reload();
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

    $(result_output).html(""); //reset output
    $(error).each(function(i){ //output any error to output element
        $(result_output).append('<div class="error">'+error[i]+"</div>");
    });

    return false;
}

function deleteAttach(id = 0)
{
    $.ajax({
        url: site_root_domain + "/?site=attach&act=delete&ajax=1&id=" +  id,
        dataType: 'json',
        success: (res) => {
            if(res.status == 'ok')
            {
                $("#attachFiles_"+id).remove();
                call_notify("Notification", res.msg, "success", "");
            }
            else
            {
                call_notify("Notification", res.msg, "danger", "");
            }
        }
    });
}

function removeAttachFile(obj, key)
{
    var form = $(obj).parents("form");
    $(obj).parents(".uploading-list-item").remove();
    console.log($(form)[0].elements['upload_file[]'].files);
}

function confirmWhenEditTrx(trx_id,is_changed,callback)
{
    if(is_changed != '1')
    {
        callback();
        return true;
    }

    $.ajax({
        type: 'get',
        url: '/?site=transactions&subact=check_linked&id='+trx_id,
        success: function(res){
            if(res == 'valid')
            {
                callback();
            }
            else
            {
                swal({
                    text: "The transaction you are editing is linked to others. Are you sure you want to modify it?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result) {
                        callback();
                    }
                }).catch(swal.noop)
            }
        }
    })
}

function changeAmountReceive(amountReceive = 0)
{
    let total = +$("#total_tax.total_tax_show").val();

    let container = $("#getUnbilledInvoices #item_line");

    if(amountReceive < total)
    {
        let checkedCheckbox = container.find("[type=checkbox].parent:checked");

        checkedCheckbox.each(function(k,v){
            let row = $(v).parents('tr:first');
            let paymentInput = row.find('[name^=tri_payment]:first');
            let paymentAmount = +paymentInput.val();

            if(amountReceive <= 0)
            {
                $(this).prop("checked", false);
                paymentInput.val(0);
            }
            else
            {
                if(amountReceive < paymentAmount)
                {
                    paymentAmount = amountReceive;
                }

                paymentInput.val(paymentAmount);

                amountReceive -= paymentAmount;
            }
        })
    }
}

function changePriceItem(e) {
    let row = e.parents("tr:first");
    let price  = +row.find("[name='product_price[]']").val()*1;
    let oldPrice = +row.find("[name='product_old_price[]']").val()*1;
    let discountType = +row.find("[name='product_discount_type[]']").val()*1;
    let discountValue = discountType==0 ? 100-(price/oldPrice)*100 : oldPrice-price;
    discountValue = discountValue < 0 ? 0 : discountValue;
    row.find("[name='product_discount_value[]']").val(discountValue);
    this.calculate_money();
}

function changeDiscountValueItem(e) {
    let row = e.parents("tr:first");
    let oldPrice = +row.find("[name='product_old_price[]']").val()*1;
    let discountType = +row.find("[name='product_discount_type[]']").val()*1;
    let discountValue = +row.find("[name='product_discount_value[]']").val()*1;
    let price = discountType == 0 ? oldPrice - (oldPrice * discountValue)/100 : oldPrice - discountValue;
    row.find("[name='product_price[]']").val(price);
    this.calculate_money();
}

// ThamLV-D8M9Y2018: Calculate Shipping Fee for simple
// Edit page then calculate based followed by the value entered in ord_fee_shipping input
function calculateShipFee( type )
{
    if( typeof type == 'undefined' )
    {
        type = $("#ord_fee_shipping").attr('checktype');
    }
    type = type == 1 ? 1 : 0;

    $.ajax({
        type: 'post',
        url: site_root_domain+'/?site=order&subact=calculate_ship_fee&type='+type,
        dataType: 'json',
        data: $('form#formorder-signin_v1').serialize(),
        beforeSend: function(){
            $('#ord_fee_shipping').prop('selected', true);
            $('#ord_fee_shipping').css({
                "background-image": "url('/acp/images/fb-loading.gif')", 
                "background-position-x": "right", 
                "background-position-y": "center", 
                "background-repeat": "no-repeat", 
                "background-attachment": "scroll", 
                "background-size": "auto auto", 
                "background-origin": "padding-box", 
                "background-clip": "border-box", 
            });
        }, 
        success: function (res)
        {
            $('#ord_fee_shipping').prop('selected', false);
            $('#ord_fee_shipping').css({
                "background-image": "", 
                "background-position-x": "", 
                "background-position-y": "", 
                "background-repeat": "", 
                "background-attachment": "", 
                "background-size": "", 
                "background-origin": "", 
                "background-clip": "", 
            });

            $("#ord_fee_shipping").val(res.fee_shipping);
            $(".total-show").html(formatNumberInput(res.total));
        }, 
        error: function(xhr, ajaxOptions, thrownError){
            $('#ord_fee_shipping').prop('selected', false);
            $('#ord_fee_shipping').css({
                "background-image": "", 
                "background-position-x": "", 
                "background-position-y": "", 
                "background-repeat": "", 
                "background-attachment": "", 
                "background-size": "", 
                "background-origin": "", 
                "background-clip": "", 
            });

            $('#ord_fee_shipping').val('N/A');
        }
    });
}

function changeShipFee()
{
    calculateShipFee(1);
}