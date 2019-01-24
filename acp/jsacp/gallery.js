function galleryQuickUpdateSortOrder(id){
	let sortInput = $('#sort_input_'+id);
	let sortIcon = $('#sort_icon_'+id);
	let sortValue = sortInput.val();

    sortInput.prop("disabled", true);

    sortIcon.removeClass("glyphicon-sort");
    sortIcon.addClass("glyphicon-refresh").addClass("glyphicon-refresh-animate");

    $.ajax({
		type: 'post',
		dataType: 'json',
		url: site_root_domain + '/?site=gallery&subact=sort',
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
                galleryQuickUpdateSortOrderDone(sortInput, 'success');
			}
			else {
                pNotifyACP(res.msg, 'error');
                galleryQuickUpdateSortOrderDone(sortInput, 'danger');
			}
		}
	});
}

function galleryQuickUpdateSortOrderDone(obj, cls='success') { //success, danger, info
    obj.addClass("alert").addClass("alert-" + cls);

    setTimeout(function(){
        obj.removeClass("alert").removeClass("alert-" + cls);
    },5000);
}

function galleryChangeCategoryShow() {
    var count_check = $(".check_multi_item:checked").length;
    if(count_check > 0)
    {
        $("#modal-change-category").modal('show');
    }
}

function galleryChangeCategoryDo() {
    let cat_id = $("#g-category-options").val();
    let data = [];

    $.each($(".check_multi_item:checked"), function(k,obj){
    	data.push($(obj).val());
	})

	$.ajax({
		type: "post",
		dataType: "json",
		url: site_root_domain + "/?site=gallery&act=edit_do&subact=change_category",
		data: {
			cat_id: cat_id,
			gallery_id: data
		},
		success: function(res){
			if(res.status == 'ok')
			{
                location.reload();
			}
			else
			{
                pNotifyACP(res.msg, "error");
			}
		}
	});
}

$(document).ready(function(){
	
	$(".check_multi_item").click(function(){
		var count_check = $(".check_multi_item:checked").length;
		var count_check_full = $(".check_multi_item").length;
		if(count_check > 0)
		{
			$(".number_check").html("Item(s): "+count_check);
		}else
		{
			$(".number_check").html("");
		}

		if(count_check != count_check_full)
		{
			$(".btn_checkall").prop("checked", false);
		}
	});

	$(".dell_all_checked").click(function(){
		var count_check = $(".check_multi_item:checked").length;
		if(count_check > 0)
		{
			swal({
				  title: 'Are you sure?',
				  // text: "You won't be able to revert this!",
				  type: 'warning',
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, delete it!'
				}).then(function () {
				  // Ajax del image
				  var count_del = 0;
				  waitingDialog.show(cms_lang.waiting_dialog_msg);
				  $(".check_multi_item:checked").each(function(){
				  		var id = $(this).val();
				  		$.ajax({
				  			type: "post",
				  			url: site_root_domain+ "/?site=gallery&act=delete&subact=ajax",
				  			data: {id:id},
				  			success: function(responsive)
				  			{
				  				if(responsive == 1)
				  				{
				  					count_del ++;
				  				}
				  			}
				  		});

				  		// xoa item
				  		$(this).parents(".gallery-col").remove();
				  });

				  var loop_time = setInterval(function(){
						if(count_del == count_check)
						{
							waitingDialog.hide();
							clearInterval(loop_time);
							$(".number_check").html("");
							swal( 'Deleted!', 'Your file has been deleted.', 'success');

						}
				  }, 1000);

				});
		}
	});

	$(".btn_checkall").click(function(){
		var count = $(".check_multi_item").length;
		var count_check = $(".check_multi_item:checked").length;
		if(count != count_check)
		{
			$(".check_multi_item:not(:checked)").each(function(){
				$(this).prop("checked", true);
			});
		}else
		{
			$(".check_multi_item").prop("checked", false);
		}
		
		// show number item
		var count_check = $(".check_multi_item:checked").length;
		if(count_check > 0)
		{
			$(".number_check").html("Item(s): "+count_check);
		}else
		{
			$(".number_check").html("");
		}
	});
});



function uploadTypeShow(type) {
    $('.upload_type_wrap').hide();
    $('.upload_type_' + type).show();
}

function addImgURL()
{
    let clone = $(".url_item:first").clone();
    clone.find("input").val("");
    clone.find(".remove_img_url").prop("disabled", false);

    $(".url_items").append(clone);
}

function removeImgURL(obj)
{
    obj.parents(".url_item").remove();
}

uploadTypeShow($('[name=upload_type]:checked').val());

$(document).ready(function(){
    uploadTypeShow($('[name=upload_type]:checked').val());

    $("[name=upload_type]").change(function(){
        uploadTypeShow($(this).val());
    });
});