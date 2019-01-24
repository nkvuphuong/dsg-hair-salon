<div class="container-fluid" id="blockui-element-container-dark" style="zoom: 1;">
	<section class="add_table main_form">
		<header class="section-header">
			<div class="tbl">
				<div class="tbl-row">
					<div class="tbl-cell">
						<h3><?=$CMS->lang['title_header_logs_freshdesk']?></h3>
						<ol class="breadcrumb breadcrumb-simple">
							<li><a href="<?=$CMS->vars['root_domain']?>/?site=addons"><?=$CMS->lang['text_breadcrumb_addons'];?></a></li>
							<li class="active"><?=$CMS->lang['text_breadcrumb_logs_freshdesk'];?></li>
						</ol>
					</div>
				</div>
			</div>
		</header>
		<section class="tabs-section">
			<div class="tabs-section-nav tabs-section-nav-inline">
				<ul class="nav" role="tablist">
					<li class="nav-item">
						<a class="nav-link" href="<?=$CMS->vars['root_domain'];?>/?site=addons&act=setting&module=freshdesk">
							<?=$CMS->lang['table_setting']?>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active" role="tab" data-toggle="tab">
							<?=$CMS->lang['table_logs']?>
						</a>
					</li>
				</ul>
			</div>
		</section>
		<br>
		<section class="add_table">
			<div id="expand_logs_container" style="display:block">
				<div class="activity-line">
					<? foreach ($tpl->logs as $log) {
						if (! empty($log['log_content']['form']) || ! empty($log['log_content']['to'])) { ?>
					<article class="activity-line-item box-typical">
						<div class="activity-line-date">
							<?=$log['log_time']?>
						</div>
						<header class="activity-line-item-header">
							<? if(! empty($log['log_content']['form'])) { ?>
							<div><strong><?=$CMS->lang['fresh_sync_customer_from']?>:</strong> <?=$log['log_content']['form']?></div>
							<? } ?>
							<? if(! empty($log['log_content']['to'])) { ?>
							<div><strong><?=$CMS->lang['fresh_sync_customer_to']?>:</strong> <?=$log['log_content']['to']?></div>
							<? } ?>
						</header>
					</article>
						<? }
					} ?>
				</div>
			</div>  
		</section> 
	</section>
</div>