<div class="container-fluid" id="blockui-element-container-dark" style="zoom: 1;">
	<section class="add_table main_form">
		<figure class="heading">
			<h3><?=$CMS->lang['title_header_setting_chatbot_facebook']?></h3> 
			<ol class="breadcrumb pull-right">
				<li class="breadcrumb-item"><a href="<?=$CMS->vars['root_domain']?>/?site=addons"><?=$CMS->lang['menu_addons']?></a></li>
				<li class="breadcrumb-item active"><?=$CMS->lang['table_logs']?></li>
			</ol>
		</figure>
		<section class="add_table">
			<div id="expand_logs_container" style="display:block">
				<div class="activity-line">
					<? foreach ($tpl->logs as $log) { ?>
					<article class="activity-line-item box-typical">
						<div class="activity-line-date">
							<?=$CMS->class->date->date_format($logs['log_time'], 1)?>
						</div>
						<div class="activity-line-item-header">
							<?=$log['log_content']?>
						</div>
					</article>
					<? } ?>
				</div>
			</div>  
		</section> 
	</section>
</div>