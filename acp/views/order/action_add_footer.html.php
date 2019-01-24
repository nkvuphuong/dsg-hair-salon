<section class="add_cart_footer">
 	<?=$tpl->btn_action['footer_back'];?>

 	<? if($_SESSION['is_mobile'] == true) { ?>

 		<div class="btn-group dropup pull-right hidden-xl-up">
				  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-save"></i><?=$CMS->lang['act_save'];?>
				  </button>
				  <div class="dropdown-menu">
				  	<ul>
						<li><a class="act_submit_save" title=""><i class="fa fa-save"></i> <?=$CMS->lang['order_pay_later'];?></a></li>
						<li><a class="act_submit_save" value="confirm_paid" title=""><i class="fa fa-credit-card"></i> <?=$CMS->lang['order_creat_paid']; ?></a></li>
					</ul>
				  </div>
			 </div>

 	<? }else{ ?>

 			<button  class="act_submit_save btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label"><?=$CMS->lang['order_pay_later']?></span><span class="ladda-spinner"></span></button>
			<button  class="act_submit_save btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs" value="confirm_paid"><span class="ladda-label"><?=$CMS->lang['order_creat_paid']?></span><span class="ladda-spinner"></span></button>
		
 	<? } ?>

 		<span  class="pull-right" style="margin-top:10px"><?=$CMS->lang['order_creat_paid_notice']?></span>
	<input type="hidden" name="action_redirect" id="footer_action_redirect" value="">
</section>
 <script>
            $(".act_submit_save").click(function(){
                $("#footer_action_redirect").val($(this).attr("value"));
            });
 </script>