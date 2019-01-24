function print_barcode_popup(id)
{
    $("#preview_barcode_button").attr("onclick", `preview_barcode_popup('${id}')`);
    $("#preview_barcode_button1").attr("onclick", `preview_barcode_popup('${id}',1)`);

    $("#print_to_excel_button").attr("href",`?site=product&act=export&id=${id}` )
    $.magnificPopup.open({
        type: 'inline',
        items: {
            src: '#print_barcode_popup'
        },
    });
}

function preview_barcode_popup(id, type=0)
{
    $.ajax({
        type: 'get',
        url: site_root_domain + '/?site=product&act=get_barcode_info&id=' + id,
        dataType: 'json',
        success: (res) => {

            if(res.status == 'ok')
            {
                $("#preview_barcode_popup").find("iframe").attr("src", site_root_domain + `?site=product&act=preview_barcode&name=${res.data.name}&group=${res.data.group}&barcode=${res.data.barcode}&skucode=${res.data.skucode}&price=${res.data.price}&type=${type}`);
                $("#preview_barcode_popup").find("a#print_barcode").attr("href", site_root_domain + `?site=product&act=preview_barcode&name=${res.data.name}&group=${res.data.group}&barcode=${res.data.barcode}&skucode=${res.data.skucode}&price=${res.data.price}&type=${type}`);
                $("#preview_barcode_popup").find("a#export_barcode_to_excel").attr("href", site_root_domain + `?site=product&act=preview_barcode&name=${res.data.name}&group=${res.data.group}&barcode=${res.data.barcode}&skucode=${res.data.skucode}&price=${res.data.price}&save_as=excel&type=${type}`);
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

function productQuickAction(form = $("#quickActionForm #form-signin_v1")){
    form.find("[name=base64_image]").val("");
    let site = $("#quickActionForm").attr("module");
    let act = $("#quickActionForm").attr("act");
    let id = act=='edit_do' ? $("#quickActionForm").attr("product_id") : "0";
    tinymce.triggerSave();
    var formData = new FormData(form[0]);
    blockLoading($("#quickActionForm"));
    $.ajax({
        url: site_root_domain + "/?site="+site+"&act="+act+"&ajax=1&id="+id,
        type: "post",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,   // tell jQuery not to set contentType
        dataType: 'json',
        success: function(res){
            unblockLoading($("#quickActionForm"));
            if(res.status == "ok") {
                let subact = $("#quickActionForm").attr("subact");

                if(act=="add_do" && subact=="continue") {
                    clearProductQuickAction(form);
                }

                if(subact == 'close') {
                    $.magnificPopup.close();
                }

                pNotifyACP(res.msg, "success");
                reloadProductListing(site);
            } else {
                pNotifyACP(res.msg);
            }
        },
        error: function(res) {
            unblockLoading($("#quickActionForm"));
            pNotifyACP("Error !");
        }
    })
}

function reloadTinyMCEProductQuickAction() {
    tinymce.remove();
    let desSettings = {
        height: 200,
        selector: '.texarea_des',
        toolbar: "newdocument | bold | italic | underline | strikethrough | alignleft | aligncenter | alignright | alignjustify | styleselect | formatselect | fontselect | fontsizeselect | cut | copy | paste | bullist | numlist | outdent | indent | blockquote | undo | redo | removeformat | subscript | superscript | code",
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 40px",
        menubar: false,
        convert_urls: true,
        relative_urls: false,
    };
    tinyMCEInit(desSettings);
    tinyMCEInit();
}

function clearProductQuickAction(form = $("#quickActionForm #form-signin_v1")){
    form.find("input[type!='radio'], select, area").val("");
    form.find("input[name*='_price_'], input[name='p_order']").val("0");
    form.find(".select2").val(null).trigger("change");
    form.find("#upload_img_show").attr("src","");
    form.find("input[name=p_show]").prop("checked",false);
    form.find("input#radio-show-1").prop("checked",true);

    reloadTinyMCEProductQuickAction();
    for (edId in tinyMCE.editors) {
        tinyMCE.editors[edId].setContent('');
    }

    $("#upload_img_show").attr({"src": "", "style": "display: none; width: 100%; height: auto; margin: 0px auto;"});
    form.find(".font-icon-cloud-upload-2, .drop-zone-caption").show();
}

function loadProductQuickAction(id, form = $("#quickActionForm #form-signin_v1")){

    $.magnificPopup.open({
        items: {
            src: '#quickActionForm'
        },
        type: 'inline',
            midClick: true,
        callbacks: {
            open: function() {
                form.find(".btn-save-close").text(cms_lang.btn_update_and_close);
                form.find(".btn-save-continue").text(cms_lang.btn_update_and_continue);
                clearProductQuickAction();
            }
        }
    });

    blockLoading(form);
    $.ajax({
        url: site_root_domain + "/?site=product&subact=load_quick_data&id="+id,
        type: "get",
        dataType: "json",
        success: function(res){
            unblockLoading(form);
            reloadTinyMCEProductQuickAction();
            $.each(res, function(k, v){
                if (k == 'product_image') {
                    if(v != "")
                    {
                        $("#upload_img_show").attr({"src": v, "style": "display: block; width: 100%; height: auto; margin: 0px auto;"});
                        form.find(".font-icon-cloud-upload-2, .drop-zone-caption").hide();
                    }
                    else
                    {
                        $("#upload_img_show").attr({"src": "", "style": "display: none; width: 100%; height: auto; margin: 0px auto;"});
                        form.find(".font-icon-cloud-upload-2, .drop-zone-caption").show();
                    }
                }

                if(k == "staff_id") {
                    form.find("#staff_id").html(v).trigger("change");
                }

                input = form.find('[name="'+k+'"]');
// console.log('[name="'+k+'"]');
                if(input.length)
                {
                    var reg = /^(p_description|p_information_)/;
                    if(reg.test(k)) {
                        tinymce.get(input.prop("id")).setContent(v);
                    }
                    else if(k == 'p_show') {
                        $.each(input, function(k1, v1){
                            if($(v1).val() == v) {
                                $(v1).prop("checked",true);
                            }
                            else
                            {
                                $(v1).prop("checked",false);
                            }
                        })
                    }
                    else {
                        input.val(v);
                        
                        if(input.hasClass("select2")){
                            input.trigger("change");
                        };
                    }
                }
            })
        },
        error: function(err){
            unblockLoading(form);
            $.magnificPopup.close();
            pNotifyACP("Error !");
        }
    })
}

function reloadProductListing(site = "product"){
    blockLoading($("#reloadProductListing"));
    $.ajax({
        url: site_root_domain + "/?site="+site+"&ajax=1",
        success: function(res){
            unblockLoading(form);
            $("#reloadProductListing").html(res);
        },
        error: function(err){
            unblockLoading($("#reloadProductListing"));
            $.magnificPopup.close();
            pNotifyACP("Error !");
        }
    })
}


