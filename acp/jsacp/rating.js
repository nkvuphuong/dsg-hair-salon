function ratingQuickUpdateSortOrder(id){
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
		url: site_root_domain + '/?site=rating&subact=sort',
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
                ratingQuickUpdateSortOrderDone(sortInput, 'success');
			}
			else {
                pNotifyACP(res.msg, 'error');
                ratingQuickUpdateSortOrderDone(sortInput, 'danger');
			}
		},
		error: function(err){
			console.log(err);
		}
	});
}

function ratingQuickUpdateSortOrderDone(obj, cls='success') { //success, danger, info
    obj.addClass("alert").addClass("alert-" + cls);

    setTimeout(function(){
        obj.removeClass("alert").removeClass("alert-" + cls);
    },5000);
}

function orderChooseRating(val) {
	let container = $("#choose_rating");
    container.find(".feebback-wrap").removeClass("active");
    container.find(".rate-val-"+val).addClass("active");
    $("#order_rating_value").val(val);
}

function orderChooseRatingDo(ord_id) {
    let rating_val = $("#order_rating_value").val();
    blockLoading($("#FeedbackChecksWrap"));
    $.ajax({
		url: site_root_domain + "/?site=order&act=rating_do&id="+ord_id+"&commission_rating="+rating_val,
		dataType: 'json',
		success: function (res) {
            unblockLoading($("#FeedbackChecksWrap"));
			if(res.status == 'success') {
                $("#FeedbackChecksWrap").hide();
                $("#FeedbackthankyouWrap").show();
			} else {
                pNotifyACP(res.msg, 'error');
			}
		},
		error: function (err) {
            unblockLoading($("#FeedbackChecksWrap"));
            pNotifyACP("Error", 'error');
		}
	});
}