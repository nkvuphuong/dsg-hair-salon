<section class="add_cart_footer">
	<?=$tpl->btn_action['footer_back'];?>
	 
	<? if($_SESSION['is_mobile'] == true) { ?>
			<div class="btn-group dropup pull-right hidden-xl-up">
				  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-save"></i><?=$CMS->lang['act_save'];?>
				  </button>
				  <div class="dropdown-menu">
				  	<ul>
						<li><a id="form_submit_re_de_m"  title=""><i class="fa fa-file-text-o"></i> <?=$CMS->lang['order_edit_button_detail'];?></a></li>
						<li><a id="form_submit_re_ls_m" title=""><i class="fa fa-mail-reply"></i> <?=$CMS->lang['order_edit_button_list']; ?></a></li>
					</ul>
				  </div>
			 </div>

			
	<? }else{ ?>
		 
			<button id="form_submit_re_de" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label"><i class="fa fa-file-text-o"></i> <?=$CMS->lang['order_edit_button_detail'];?></span><span class="ladda-spinner"></span></button>
 
			<button id="form_submit_re_ls" type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label"><i class="fa fa-mail-reply"></i> <?=$CMS->lang['order_edit_button_list'];?></span><span class="ladda-spinner"></span></button>
 
	<? } ?>
		<input type="submit" id="trigger_submit" style="display:none" />
	    <input type="hidden" name="redirect" value="0" />
</section>
 