<!-- paginf . layouts -->
<ul class="pagination page-pagination">
<?php foreach (\core\ezy::$page['data'] as $data => $value) { ?>
	<?php if ($value['status'] == "first" ) { ?>
		<li><a href="<?=isset($tpl->modLink)?$tpl->modLink:'';?>/page-<?=$value['page'];?>" aria-label="First"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>
	<?php } else if ( $value['status'] == "last" ) { ?>
		<li><a href="<?=isset($tpl->modLink)?$tpl->modLink:'';?>/page-<?=$value['page'];?>"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>
	<?php } else if ( $value['status'] == "active" ) { ?>
		<li class="disabled"><a href="javascript:void(0)"><?=$value['page'];?></a></li>
	<?php } else { ?>
		<li><a href="<?=isset($tpl->modLink)?$tpl->modLink:'';?>/page-<?=$value['page'];?>"><?=$value['page'];?></a></li>
	<?php } ?>
<?php } ?>
</ul>