<!-- you_web . client -->
<div id="websModal" class="panel panel-default" style="display: none;">
	<div class="panel-heading ">
		<div class="row">
			<div class="col-md-6 text-left">
				<h4>Trang web của bạn</h4>
			</div>
			<div class="col-md-6">
				<ul class="nav navbar-nav pull-right">	     
					<li class="circle-button circle-button-green">
						<a href="/dang-ky-dung-thu/webstep1"><span><i class="fa fa-plus"></i></span>Thêm trang web</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="panel-body web-modal-items-wrap">
		<?=\core\ezy::render('you_web_data', 'client');?>
	</div>
</div>