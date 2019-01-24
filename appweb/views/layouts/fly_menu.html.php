<div id="fly-menu" class="hidden-xs hidden-sm">
	<ul  class="fly-menu nav nav-stacked">
		<?if( ! in_array(\core\ezy::$site, array('bang-bao-gia-thiet-ke-web', 'bang-bao-gia-thiet-ke-web.html')) ){?>
		<li><a class="uudiem scroll_jumpto" data-jumpto="#uudiem" href="#uudiem">Ưu điểm</a></li>
		
		<?}?>
		<li><a class="banggia scroll_jumpto" data-jumpto="#banggia" href="#banggia">Bảng giá</a></li>
		<li><a class="giaodien scroll_jumpto" data-jumpto="#giaodien" href="#giaodien">Giao diện</a></li>
		<li><a class="lienhe scroll_jumpto" data-jumpto="#lienhe" href="#lienhe">Liên hệ</a></li>
	</ul>
</div>
<script>
	$(document).ready(function(){
		$('.scroll_jumpto').click(function(event){
			event.preventDefault();

			var jumpto = $(this).data('jumpto');
			if ( $(jumpto).length > 0 ) {
                $('html, body').animate({
                    scrollTop: $(jumpto).offset().top
                }, 1000);
			}
		});
	});
</script>