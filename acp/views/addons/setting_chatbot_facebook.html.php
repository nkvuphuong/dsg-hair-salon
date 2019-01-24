<div class="container-fluid" id="blockui-element-container-dark" style="zoom: 1;">
	<section class="add_table main_form">
		<figure class="heading">
			<h3><?=$CMS->lang['title_header_setting_chatbot_facebook']?></h3> 
			<ol class="breadcrumb pull-right">
				<li class="breadcrumb-item"><a href="<?=$CMS->vars['root_domain']?>/?site=addons"><?=$CMS->lang['menu_addons']?></a></li>
				<li class="breadcrumb-item active"><?=$CMS->lang['table_setting']?></li>
			</ol>
		</figure>
		<form action="<?=$CMS->vars['root_domain'];?>/?site=addons&act=setting&module=chatbot_facebook" method="post" id="change_key">
			<section class="tabs-section">
				<div class="tab-content">
					<? if (isset($_SESSION['chatbot_facebook_status'])) { ?>
						<? if ($_SESSION['chatbot_facebook_status'] === true) { ?>
					<div class="row">
						<div class="alert alert-success">
							<strong><?=$CMS->lang['success'];?></strong> <?=$CMS->lang['label_chatbot_facebook_success_key'];?>
						</div>
					</div>
						<? } ?>
						<? if ($_SESSION['chatbot_facebook_status'] === false) { ?>
					<div class="row">
						<div class="alert alert-danger">
							<strong><?=$CMS->lang['error'];?></strong> <?=$CMS->lang['label_chatbot_facebook_error_key'];?>
						</div>
					</div>
						<? } ?>
						<? unset($_SESSION['chatbot_facebook_status']);?>
					<? } ?>
					<div class="form-group row">
						<label class="col-xl-2 form-control-label">URL:</label>
						<div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 input-group">
							<input type="hidden" name="change_key" value="0">
							<input class="form-control" type="text" value="<?=$tpl->url?>" readonly onclick="copyToClipboard($(this))">
							<div class="input-group-addon">
								<span type="submit" class="fa fa-refresh" onclick="changeKey()"></span>
							</div>
						</div>
						<label class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 form-control-label" id="click-copy"><?=$CMS->lang['chatbot_facebook_click_copy']?></label>
					</div>
					<div class="row">
						<div class="form-control-label">
							<div class="checkbox-toggle">
								<input type="hidden" name="config[input][addon_chatbot_facebook_override]" value="<?=$CMS->vars['addon_chatbot_facebook_override']?>">
								<input type="checkbox" id="facebook_override" <?=($CMS->vars['addon_chatbot_facebook_override'] == 1) ? 'checked' : ''?>>
								<label for="facebook_override"><?=$CMS->lang['label_chatbot_facebook_override']?></label>
							</div>
						</div>
					</div>
				</div>
			</section>
		</form>
	</section>
</div>
<script>
	function changeKey() {
		swal({
			title: "<?=$CMS->lang['are_you_sure']?>",
			text: "<?=$CMS->lang['chatbot_facebook_change_key_warning']?>",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-success",
			confirmButtonText: "<?=$CMS->lang['yes']?>",
			cancelButtonText: "<?=$CMS->lang['no']?>",
		})
		.then( function(isConfirm) {
			if (isConfirm) {
				$("input[name='change_key']").val('1');
				$("form#change_key").submit();
			} else {
			}
		});
	}
	function copyToClipboard(e) {
		var aux = document.createElement("input");
		aux.setAttribute("value", e.val());
		document.body.appendChild(aux);
		aux.select();
		document.execCommand("copy");
		document.body.removeChild(aux);
		e.select();
		$('#click-copy').html("<?=$CMS->lang['chatbot_facebook_click_copy_success']?>").css("color", "#4CAF50");
	}
	$("#facebook_override").change(function() {
		if ($("#facebook_override").is(":checked")) {
			$('input[name="config[input][addon_chatbot_facebook_override]"]').val('1');
		} else {
			$('input[name="config[input][addon_chatbot_facebook_override]"]').val('0');
		}
		$("form#change_key").submit();
	});
</script>