<!--
<div class="container-fluid" id="blockui-element-container-dark" style="zoom: 1;">
	<section class="add_table main_form">
		<figure class="heading">
			<h3><?=$CMS->lang['title_header_setting_freshdesk']?></h3> 
			<ol class="breadcrumb pull-right">
				<li class="breadcrumb-item"><a href="<?=$CMS->vars['root_domain']?>/?site=addons"><?=$CMS->lang['menu_addons']?></a></li>
				<li class="breadcrumb-item active"><?=$CMS->lang['table_setting']?></li>
			</ol>
		</figure>
		<form action="<?=$CMS->vars['root_domain'];?>/?site=addons&act=setting&module=facebook" method="post" enctype="multipart/form-data">
		<section class="tabs-section">
			<div class="tab-content">
				<? if (! $tpl->fb['status']) { ?>
				<div class="row">
					<div class="alert alert-danger">
						<strong><?=$CMS->lang['error'];?></strong> <?=$CMS->lang['label_graph_facebook_error_connect'];?>
					</div>
				</div>
				<div id="myModal" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<strong class="modal-title"><?=$CMS->lang['label_graph_facebook_error_connect_header'];?></strong>
							</div>
							<div class="modal-body">
								<p><?=$CMS->lang['label_graph_facebook_error_connect_body'];?></p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><?=$CMS->lang['close']?></button>
							</div>
						</div>
					</div>
				</div>
				<? } ?>
				<div class="form-group row">
					<label class="col-xl-2 form-control-label">App ID</label>
					<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
						<input class="form-control" type="text" name="config[input][graph_facebook_id]" value="<?=$CMS->vars['graph_facebook_id']?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-2 form-control-label">App Secret</label>
					<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
						<input class="form-control" type="text" name="config[input][graph_facebook_secret]" value="<?=$CMS->vars['graph_facebook_secret']?>">
					</div>
				</div>
				<? if ($tpl->fb['status'] === 'not_login') { ?>
				<div class="form-group row">
					<div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
						<a href="<?=$tpl->fb['messages'];?>"><button class="btn btn-success" type="button"><?=$CMS->lang['connect'];?></button></a>
					</div>
				</div>	
				<? } ?>
			</div>
			<div class="form-group row">
				<div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
					<button class="btn" name="submit" type="submit" style="    margin: 15px 0px;"><?=$CMS->lang['save_config']?></button>
				</div>
			</div>
		</section>
		</form>
	</section>
</div>
-->