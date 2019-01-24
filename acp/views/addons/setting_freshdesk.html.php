<div class="container-fluid" id="blockui-element-container-dark" style="zoom: 1;">
	<section class="add_table main_form">
		<header class="section-header">
			<div class="tbl">
				<div class="tbl-row">
					<div class="tbl-cell">
						<h3><?=$CMS->lang['title_header_setting_freshdesk']?></h3>
						<ol class="breadcrumb breadcrumb-simple">
							<li><a href="<?=$CMS->vars['root_domain']?>/?site=addons"><?=$CMS->lang['text_breadcrumb_addons'];?></a></li>
							<li class="active"><?=$CMS->lang['text_breadcrumb_setting_freshdesk'];?></li>
						</ol>
					</div>
				</div>
			</div>
		</header>
		<section class="tabs-section">
			<div class="tabs-section-nav tabs-section-nav-inline">
				<ul class="nav" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" role="tab" data-toggle="tab">
							<?=$CMS->lang['table_setting']?>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?=$CMS->vars['root_domain'];?>/?site=addons&act=logs&module=freshdesk">
							<?=$CMS->lang['table_logs']?>
						</a>
					</li>
				</ul>
			</div>
		</section>
		<form action="<?=$CMS->vars['root_domain'];?>/?site=addons&act=setting&module=freshdesk" method="post">
		<section class="tabs-section">
			<div class="tab-content">
				<div>
					<h5><?=$CMS->lang['fresh_connect']?></h5>
					<div class="form-group row">
						<label class="col-xl-2 form-control-label">Domain</label>
						<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
							<input class="form-control" type="text" id="fd_domain" name="config[input][fd_domain]" value="<?=$CMS->vars['fd_domain']?>" onchange="test_connect()">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-xl-2 form-control-label">Key</label>
						<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
							<input class="form-control" type="text" id="fd_key" name="config[input][fd_key]" value="<?=$CMS->vars['fd_key']?>" onchange="test_connect()">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-xl-2 form-control-label"></label>
						<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
							<button type="button" class="btn btn-success btn-sm" onclick="alert_test_connect()"><?=$CMS->lang['fresh_test_connect']?></button>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#fresh_guide"><?=$CMS->lang['fresh_guide_title']?></button>
						</div>
					</div>
				</div>
				<div id="fresh-sync">
					<? if($tpl->fd) { ?>
					<h5><?=$CMS->lang['fresh_sync']?></h5>
					<div class="form-group row">
						<label class="col-xl-2 form-control-label"><?=$CMS->lang['fresh_daily']?></label>
						<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12 checkbox-toggle">
							<input type="checkbox" id="fd_sync_daily" onchange="if($(this).is(':checked')){$('#config_input_fd_sync_daily').val(1);}else{$('#config_input_fd_sync_daily').val(0);}" <?=$CMS->vars['fd_sync_daily'] == 1 ? 'checked' : '';?>>
							<label for="fd_sync_daily"></label>
							<input type="hidden" id="config_input_fd_sync_daily" name="config[input][fd_sync_daily]" value="<?=$CMS->vars['fd_sync_daily'];?>">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-xl-2 form-control-label"><?=$CMS->lang['fresh_handmade']?></label>
						<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
							<a href="javascript:void(0)" onclick="alert_sync('<?=$CMS->vars['root_domain'];?>/?site=addons&act=sync&module=freshdesk')"><button type="button" class="btn btn-sm"><?=$CMS->lang['fresh_sync_now']?></button></a>
						</div>
					</div>
					<? } ?>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
					<button class="btn" name="submit" type="submit" style="    margin: 15px 0px;" <?=($tpl->fd) ? '' : 'disabled';?>><?=$CMS->lang['save_config']?></button>
				</div>
			</div>
		</section>
		</form>
	</section>
</div>
<div id="fresh_guide" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title"><?=$CMS->lang['fresh_guide_title']?></h3>
			</div>
			<div class="modal-body">
				<p><?=$CMS->lang['fresh_guide_text_1']?></p>
				<p><?=$CMS->lang['fresh_guide_text_2']?></p>
				<p><?=$CMS->lang['fresh_guide_text_3']?></p>
				<img src="images/fresh_guide.png" class="img-responsive" style="max-width: 100%;"> 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?=$CMS->lang['close']?></button>
			</div>
		</div>
	</div>
</div>

<script>
function alert_sync(theURL) {
	swal({
	   title: confirm_alert_title,
	   text: '<?=$CMS->lang['fresh_sync_text']?>',
	   type: "warning",
	   showCancelButton: true,
	   confirmButtonClass: "btn-danger",
	   confirmButtonText: cms_lang['gnotice_ok'],
	   cancelButtonText: cms_lang['gnotice_cancel'],
	   closeOnConfirm: true,
	   closeOnCancel: true
	}).then(function () {
		window.location.href=theURL;                                         
	});
}
function alert_test_connect() {
	waitingDialog.show('<?=$CMS->lang['fresh_test_connect']?>');
	$.getJSON(
		'<?=$CMS->vars['root_domain']?>?site=addons&act=test-sync&module=freshdesk',
		{ 
			fd_domain: $('#fd_domain').val(),
			fd_key: $('#fd_key').val() 
		}
	)
	.done(function( json ) {
		if (json.status) {
			swal('<?=$CMS->lang['fresh_test_connect_success']?>');
			$('#fresh-sync').html(json.html);
			$('form button[type="submit"]').removeAttr("disabled");
		} else {
			swal('<?=$CMS->lang['fresh_test_connect_error']?>');
			$('#fresh-sync').empty();
			$('form button[type="submit"]').attr('disabled', 'disabled');
		}
		waitingDialog.hide();
	})
	.fail(function(jqxhr, textStatus, error) {
		waitingDialog.hide();
		console.log("Request Failed: " + err);
	});
}
function test_connect() {
	$.getJSON(
		'<?=$CMS->vars['root_domain']?>?site=addons&act=test-sync&module=freshdesk',
		{ 
			fd_domain: $('#fd_domain').val(),
			fd_key: $('#fd_key').val() 
		}
	)
	.done(function( json ) {
		if (json.status) {
			$('#fresh-sync').html(json.html);
			$('form button[type="submit"]').removeAttr("disabled");
		} else {
			$('#fresh-sync').empty();
			$('form button[type="submit"]').attr('disabled', 'disabled');
		}
		waitingDialog.hide();
	})
	.fail(function(jqxhr, textStatus, error) {
		console.log("Request Failed: " + err);
	});
}
</script>