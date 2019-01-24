function openAddItemsPopup(discountID)
{
    let frm = $("#addDiscountCodePopupFrm");
    reFillAddItemsFrm(frm, discountID, 1);
}

function reFillAddItemsFrm(frm, discountID, isPopup = 1)
{
    let blockLoadingSelector = isPopup == 1 ? $("#blockui-element-container-dark") : frm.find(".match-height:first").parent();

    $("select.select2-hidden-accessible").html("");
    clearForm(frm);

    blockLoading(blockLoadingSelector);

    //Load discount info
    $.ajax({
        url: site_root_domain + '/?site=discount&subact=load_info&data_type=json&id='+discountID,
        dataType: 'json',
        success: function(res){
            unblockLoading(blockLoadingSelector);
            if(res.status == 'ok')
            {
                $("#addDiscountCodePopupHeader").text(res.data.data_bk.discount_name);
                frm.find("[name='discount_id']").html(`<option selected value="${res.data.data_bk.discount_id}">${res.data.data_bk.discount_name}</option>`).trigger("change");
                frm.find("[name='di_value']").val(res.data.data_bk.discount_value);
                frm.find("[name='di_type']").val(res.data.data_bk.discount_type);
                frm.find("[name='di_num_chars']").val(res.data.data_bk.discount_num_chars);
                frm.find("[name='di_start_time']").val(res.data.discount_start_time);
                frm.find("[name='di_end_time']").val(res.data.discount_end_time);
                frm.find("[name='unlimited']").prop('checked', true).trigger('change');

                if(isPopup)
                {
                    $("#addDiscountCodePopup").modal('show');
                }
            }
            else
            {
                pNotifyACP(res.msg);
            }
        }
    });
}

function addDiscountCodePopupFrmValidate()
{
    let frm = $("#addDiscountCodePopupFrm");
    frm.validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
            },
            callback: {
                onSubmit: function (formObj, formData, btnObj)
                {
                    addDiscountItems(formData);
                }
            }
        }
    })
}

function addDiscountItems(data)
{
    blockLoading($("#addDiscountCodePopupFrm .modal-content"));

    $.ajax({
        url: site_root_domain + "/?site=discount_items&act=add_do&ajax=1",
        data: data,
        type: 'post',
        dataType: 'json',
        success: function(res){
            unblockLoading($("#addDiscountCodePopupFrm .modal-content"));
            if(res.status == 'ok')
            {
                pNotifyACP(res.msg,'success');
                $("#discountCodeNums_"+res.data.discount_id).text(res.data.discount_num_items);
                $("#closeDiscountCodePopupBtn").trigger('click');
            }
            else
            {
                pNotifyACP(res.msg,'error');
            }
        }
    })
}

function setUnlimitedTimesDiscount(obj)
{
    let frm = obj.parents("form:first");
    frm.find("[name='di_times']").prop("disabled", obj.is(':checked'));
}

function setApplyGroupDiscount(obj)
{
    let frm = obj.parents("form:first");

    applyValue = frm.find("[name='di_apply_for']").val();

    $("[apply_for]").hide();
    $("[apply_for]").find("input,select,area").prop("disabled", true);

    $("[apply_for='"+applyValue+"']").show();
    $("[apply_for='"+applyValue+"']").find("input,select,area").prop("disabled", false);
}

function select2MultipleInit(objSelect = $("[name='di_apply_rules[product]']"),remote="?site=discount&subact=load_product")
{
    let selector = objSelect.select2({
        tags: false,
        multiple: true,
        ajax: {
            url: remote,
            dataType: 'json',
            delay: 250,
            allowClear: true,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            createTag: function(params) {
                return undefined;
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data,
                    /*pagination: {
                     more: (params.page * 30) < data.total_count
                     }*/
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });
}

function select2Init(objSelect = $("select[name='discount_id']"),remote="?site=discount&subact=load_discount")
{
    let selector = objSelect.select2({
        ajax: {
            url: remote,
            dataType: 'json',
            delay: 250,
            allowClear: true,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data,
                    /*pagination: {
                     more: (params.page * 30) < data.total_count
                     }*/
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });
}

function formatRepo (repo) {
    if (repo.loading) return repo.text;
    var markup = `
        <div class='select2-result-repository clearfix'>
            <div class='select2-result-repository__title'>${repo.text}</div>
        </div>
    `;

    return markup;
}

function formatRepoSelection (repo) {
    return repo.text;
}

function randomDiscountCode(textInputObj, $numberOfCharacter=8)
{
    textInputObj.prop('disabled',true);

    $.ajax({
        url: site_root_domain + '/?site=discount_items&subact=generate_random_code&number_of_character='+$numberOfCharacter,
        success: function (code) {
            textInputObj.prop('disabled',false);
            textInputObj.val(code);
        }
    })
}

function changeItemsClassify(obj = $("select[name=discount_id]")) {
    let frm = obj.parents("form:first");
    obj.on('select2:select', function (evt) {
        reFillAddItemsFrm(frm, $(this).val(), 0);
    });
}

function changeDiscountType(val, objDisplay)
{
    objDisplay.text(cms_lang['di_type_'+val]);
}