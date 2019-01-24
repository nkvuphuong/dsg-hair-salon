<section class="add_table main_form">
    <figure class="heading">
		<h3><?=$CMS->lang['header_home'];?></h3>
		<ul class="list-inline pull-right">
			<li class="list-inline-item"><a><i class="fa fa-sign-out"></i></a></li>
			<li class="list-inline-item"><a><i class="fa fa-history"></i></a></li>
			<li class="list-inline-item"><a data-toggle="modal" data-target="#modal-help"><i class="fa fa-question-circle"></i></a></li>
		</ul>
    </figure>
	<figure class="box-typical box-typical-padding border">
		<div class="row">
			<header class="box-typical-header-sm"><?=$CMS->lang['title_list_fanpage'];?></header>
			<div class="table-responsive">          
				<table class="table">
					<tbody>
						<thead>
							<tr>
								<th></th>
								<th>ID</th>
								<th>Name</th>
								<th>Category</th>
								<th><?=$CMS->lang['connect'];?></th>
						  </tr>
						</thead>
						<? foreach ($tpl->listFanpages as $k=>$fanpage) { ?>
						<tr>
							<td>#<?=$k+=1;?></td>
							<td><?=$fanpage['id'];?></td>
							<td><?=$fanpage['name'];?></td>
							<td><?=$fanpage['category'];?></td>
							<td><a href="<?=$CMS->vars['root_domain'];?>/?site=facebook&act=message&page_id=<?=$fanpage['id'];?>"><?=$CMS->lang['connect'];?></a></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
		</div>
	</figure>
</section>

<div id="modal-help" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" link-ajax="<?=$CMS->vars['root_domain'];?>/?site=facebook&act=help">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<br>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.fa.fa-sign-out').click(function() {
			swal({
				title: "<?=$CMS->lang['are_you_sure'];?>",
				text: "<?=$CMS->lang['fanpage_logout_warning'];?>",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-danger",
				confirmButtonText: "<?=$CMS->lang['yes'];?>",
				cancelButtonText: "<?=$CMS->lang['no'];?>",
				closeOnConfirm: false,
				closeOnCancel: false
			}).then(function () {
				$.getJSON(
					'<?=$CMS->vars['root_domain'];?>/?site=facebook&act=logout',
				)
				.always(function() {
				})
				.done(function(json) {
					if (json.status == 'success') {
						swal({
							title: "<?=$CMS->lang['success']?>",
							text: "<?=$CMS->lang['fanpage_logout_success'];?>",
							type: "success",
							confirmButtonClass: "btn-success"
						});
						location.reload();
					}
				})
				.fail(function(jqxhr, textStatus, error) {
					console.log("Request Failed: " + error);
				});
			});
		});
		$('#modal-help').on('show.bs.modal', function (e) {
			var str = $('#modal-help div.modal-content div.modal-body').html().trim();
			if (str == '<br>' || str == '<center><h5>không có dữ liệu</h5></center>') {
				if ($(this).attr('link-ajax') == undefined) {
					$('#modal-help div.modal-content').html('<center><h5>không có dữ liệu</h5></center>');
				} else {
					$('#modal-help div.modal-content').html('<div class="modal-body"><br></div>');
					$.get(
						$(this).attr('link-ajax'),
					)
					.always(function() {
						$('#modal-help div.modal-content div.modal-body').block({
							message: '<div class="blockui-default-message"><i class="fa fa-circle-o-notch fa-spin"></i></div>',
							overlayCSS:  {
								background: 'rgba(155, 155, 155, 0.5)',
								opacity: 1,
								cursor: 'wait'
							},
							css: {
								width: '100%',
							},
							blockMsgClass: 'block-msg-default'
						});
					})
					.done(function(html) {
						$('#modal-help div.modal-content div.modal-body').unblock();
						$('#modal-help div.modal-content').html(html);
					})
					.fail(function(jqxhr, textStatus, error) {
						$('#modal-help div.modal-content div.modal-body').unblock();
						$('#modal-help div.modal-content div.modal-body').html('<center><h5>không có dữ liệu</h5></center>');
						console.log("Request Failed: " + error);
					});
				}
			}
		});
	});
</script>