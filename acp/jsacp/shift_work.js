function shiftWorkQuickUpdateSortOrder(id){
	let sortInput = $('#sort_input_'+id);
	let sortIcon = $('#sort_icon_'+id);
	let sortValue = sortInput.val();

    sortInput.prop("disabled", true);

    sortIcon.removeClass("glyphicon-sort");
    sortIcon.addClass("glyphicon-refresh").addClass("glyphicon-refresh-animate");
	console.log(sortValue);
    $.ajax({
		type: 'post',
		dataType: 'json',
		url: site_root_domain + '/?site=shift_work&subact=sort',
		data: {
			'id': id,
			'sort_value': sortValue,
		},
		success: function(res){

            sortInput.prop("disabled", false);
            sortIcon.addClass("glyphicon-sort");
            sortIcon.removeClass("glyphicon-refresh").removeClass("glyphicon-refresh-animate");

			if(res.status == 'ok') {
                pNotifyACP(res.msg, 'success');
                shiftWorkQuickUpdateSortOrderDone(sortInput, 'success');
			}
			else {
                pNotifyACP(res.msg, 'error');
                shiftWorkQuickUpdateSortOrderDone(sortInput, 'danger');
			}
		},
		error: function(err){
			console.log(err);
		}
	});
}

function shiftWorkQuickUpdateSortOrderDone(obj, cls='success') { //success, danger, info
    obj.addClass("alert").addClass("alert-" + cls);

    setTimeout(function(){
        obj.removeClass("alert").removeClass("alert-" + cls);
    },5000);
}