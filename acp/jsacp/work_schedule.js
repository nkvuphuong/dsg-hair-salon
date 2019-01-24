let workScheduleTblObj = "#work_schedule_tbl";

function workScheduleTblInit() {
	let tbl = $(workScheduleTblObj);
	tbl.find("tr:gt(0)").each((i, r) => {
        workScheduleRowInit($(r));
	})
}

function workScheduleRowInit(row) {
    let cbx = row.find(":checkbox.cbx_row:first");
    row.find(":checkbox.shift_work_row:enabled").prop("checked", cbx.is(':checked'));
}

function workScheduleAll(checked) {
    let tbl = $(workScheduleTblObj);
    tbl.find(":checkbox:enabled").prop("checked", checked);
}

function workScheduleColInit(id, checked) {
    let tbl = $(workScheduleTblObj);
    tbl.find(":checkbox:enabled.shift_work_col_"+id).prop("checked", checked);
}

function workScheduleSubmit(form) {

    blockLoading(form.parent());

    let data = form.serializeArray();
    $.ajax({
        url: site_root_domain + "/?site=work_schedule&subact=update",
        type: 'post',
        data: data,
        dataType: 'json',
        success: (res) => {

            unblockLoading(form.parent());

            if(res.status == 'success') {
                pNotifyACP(res.msg, 'success');
            } else {
                pNotifyACP(res.msg, 'error');
            }
        }
    })
}

function workScheduleLoadData(date, storeId) {
    let tbl = $(workScheduleTblObj);

    workScheduleFilterByBranch();

    blockLoading(tbl);

    $.ajax({
        type: 'get',
        url: site_root_domain + "/?site=work_schedule&subact=load_data",
        data: {
            date: date
        },
        dataType: 'json',
        success: (res) => {

            unblockLoading(tbl);

            if(res.status == 'success') {
                tbl.find(":checkbox").prop("checked", false);
                tbl.find(":checkbox").prop("disabled", false);
                $.each(res.data, (i, item) => {

                    if(storeId == item.store_id) {
                        $(`#shift_work_${item.staff_id}_${item.shift_work_id}`).prop("checked", true);
                    } else {
                        $(`#shift_work_${item.staff_id}_${item.shift_work_id}`).prop("disabled", true);
                    }

                })
            } else {
                pNotifyACP(res.msg, 'error');
            }
        }
    })
}

function workScheduleLoadStores(cityId, obj) {
    obj.html("");
    obj.prop("disabled", true);
    $.ajax({
        type: "get",
        url: site_root_domain + "/?site=work_schedule&subact=load_stores&city_id="+cityId,
        dataType: 'json',
        success: (res) => {
            obj.prop("disabled", false);
            if(res) {
                res.forEach((store) => {
                    obj.append(`<option value="${store.id}">${store.name}</option>`);
                })
            }

            obj.trigger("change");
        }
    })
}

function workScheduleSetUserIsBusyForm(ele) {
    $("#setBusyModalFrm").find("[name='user_id']").val(ele.attr("uid"));
    $("#setBusyModalFrm").find("#uname").text(ele.attr("uname"));
    $("#setBusyModalFrm").find("[name='user_busy_from']").val(ele.attr("from"));
    $("#setBusyModalFrm").find("[name='user_busy_to']").val(ele.attr("to"));
    $(".busy_item").removeClass("active");
    ele.addClass("active");
}

function workScheduleSetUserIsBusy() {
    $.ajax({
        type: "post",
        url: site_root_domain + "/?site=user&subact=set_busy",
        data: $("#setBusyModalFrm").serialize(),
        dataType: 'json',
        success: res => {
            if(res.status == 'success') {
                $(".busy_item.active .busy_from_display").text(res.payload.user_busy_from || 'N/A');
                $(".busy_item.active .busy_to_display").text(res.payload.user_busy_to || 'N/A');
                $(".busy_item.active").attr("from", res.payload.user_busy_from || '');
                $(".busy_item.active").attr("to", res.payload.user_busy_to || '');
                pNotifyACP(res.msg, "success");
            } else {
                pNotifyACP(res.msg, "error");
            }
        },
        error: err => {
            pNotifyACP("Có lỗi xảy ra", "error");
        }
    })
}

function workScheduleSetSlotForStaff(btn, id, obj) {
    id = +id;
    slots = +obj.val();
    btn.prop("disabled", true);
    $.ajax({
        url: site_root_domain + "/?site=work_schedule&subact=update_slot&id=" + id + "&slots=" + slots,
        dataType: 'json',
        success: res => {
            if(res.status == 'success') {
                pNotifyACP(res.msg, "success");
                obj.removeAttr("style");
            } else {
                pNotifyACP(res.msg, "error");
                obj.attr("style", "border-color:red");
            }
        },
        error: err => {
            pNotifyACP("Có lỗi xảy ra", "error");
            obj.attr("style", "border-color:red");
            console.log(err);
        },
        complete: () => {
            btn.prop("disabled", false);
        }
    })
}

function workScheduleFilterByBranch() {
    let type = $("#filterByBranchSelect").val();

    if(type == "all") {
        $(".ws-item").show();
    } else {
        $(".ws-item").hide();

        let storeId = $("#work_schedule_form #store_id").val();
        $(".ws-item-branch-" + storeId).show();
    }
}

function workScheduleClone() {
    let tbl = $(workScheduleTblObj);

    let fromDate = $("#ws_date_clone").val();
    let fromCity = +$("#ws_city_clone").val();
    let fromStore = +$("#ws_store_clone").val();

    let toDate = $("#work_schedule_date").val();
    let toCity = +$("#city_id").val();
    let toStore = +$("#store_id").val();

    blockLoading($('#cloneWorkScheduleForm'));

    $.ajax({
        type: 'get',
        url: site_root_domain + "/?site=work_schedule&subact=clone",
        data: {
            from_date: fromDate,
            from_store: fromStore,
            to_date: toDate,
            to_store: toStore
        },
        success: function(res) {
            if(res.status == 'success') {
                pNotifyACP("Đã sao chép", "success");
                tbl.find(":checkbox").prop("checked", false);
                tbl.find(":checkbox").prop("disabled", false);

                if(res.data.items.length) {
                    $.each(res.data.items, (i, item) => {
                        if(toStore == item.store_id) {
                            $(`#shift_work_${item.staff_id}_${item.shift_work_id}`).prop("checked", true);
                        } else {
                            $(`#shift_work_${item.staff_id}_${item.shift_work_id}`).prop("disabled", true);
                        }
                    })
                }
                $("#filterByBranchSelect").val('all');
                workScheduleFilterByBranch();
            } else {
                pNotifyACP(res.msg, 'error');
            }
        },
        error: function(err) {
            pNotifyACP("Có lỗi xảy ra", "error");
            console.log(err);
        },
        complete: function() {
            unblockLoading($('#cloneWorkScheduleForm'));
            $('#cloneWorkScheduleModal').modal('hide');
        }
    })
}