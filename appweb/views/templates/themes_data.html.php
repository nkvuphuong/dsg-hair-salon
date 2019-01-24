<!-- Load theme via ajax 20-10-2017 -->
<div class="row themes_content"></div>
<div class="row text-center">
	<div class="col-md-12 themes_loading" style="display: none;">
		<img style="float:unset;display:block;margin:0px auto;" src="images/icon-loading.gif">
	</div>
	<div class="col-md-12 themes_loadmore"></div>
</div>
<script>
function loadMoreThemesDataByCategory( page ) 
{
	page = ( typeof page == "undefined" || page < 1 ) ? 1 : page;

	$.ajax({
        type: "post",
        url: "/templates/getthemesbycategoryviaajax",
        data: {
        	page: page, 
            <?=isset($tpl->themesCategoryIdList) ? "id_list: '".$tpl->themesCategoryIdList."', " : '';?> 
            <?=isset($tpl->themesSort) ? "sort: '".$tpl->themesSort."', " : '';?> 
        	<?=isset($tpl->themesLimit) ? "limit: '".$tpl->themesLimit."', " : '';?> 
        },
        beforeSend: function() {
        	// Show loading
        	$('.themes_loading').show();
        },
        success: function(html) {
            var obj = JSON.parse(html);

			var html_themes = '<p>Đang cập nhật...</p>';
			if ( obj.themes.length > 0 )
			{
				html_themes = '';

				// Temp check align center
				var style = '';
				var classExtends = '';
				if ( page == 1 )
				{
					if ( obj.themes.length == 1 )
					{
						style = 'float: none; margin-left: auto; margin-right: auto';
					}
					else if ( obj.themes.length == 2 )
					{
						classExtends = 'col-md-offset-3';
					}
					else if ( obj.themes.length == 3 )
					{
						classExtends = 'col-md-offset-1-and-half';
					}
				}

				for(var x in obj.themes)
                {
                	classExtends = x == 0 ? classExtends : '';
                    html_themes += `
                    <div class="col-xs-6 col-sm-6 col-md-3 `+classExtends+` theme-item-outer delay-0 scroll-to-show bottom-in ajax-append" style="`+style+`">
						<div class="theme-item shadow">
							<div class="image-top toggle-state">
								<img class="theme-img img-responsive ani" src="`+obj.themes[x].gallery.url_img_preview+`" alt="`+obj.themes[x].name+`">
								<div class="iphone ani">
									<div class="screen">
										<a target="_blank" href="`+obj.themes[x].link_demo_short+`">
											<img class="theme-mobile img-responsive ani" src="`+obj.themes[x].gallery.url_img_mobile+`" alt="`+obj.themes[x].name+`">
										</a>
									</div>
								</div>
								<div class="detail-btn ani">
									<a target="_blank" href="`+obj.themes[x].link_demo_short+`">
										<input type="button" class="btn btn-block btn-opacity ani" value="Xem giao diện thực tế">
									</a>
								</div>
								<div class="buy-now-btn ani">
									<a href="/dang-ky-dung-thu/webstep1/?theme=`+obj.themes[x].code+`">
										<input type="button" class="btn green-big ani" value="Đăng ký">
									</a>
									<a href="/dang-ky-dung-thu-website.html/?theme=`+obj.themes[x].code+`&warehouse=0">
										<input type="button" class="btn green-big outline ani" value="Dùng thử">
									</a>
								</div>
							</div>
							<div class="info-bottom">	
								<div class="info-left ani">
									<h5>Giao diện: <span class="text-capitalize">`+obj.themes[x].name+`</span></h5>
								</div>
								<div class="icon-right ani">
									<a class="btn-view-demo-outner" target="_blank" href="`+obj.themes[x].link_demo_short+`">
										<div class="btn-view-demo">Xem Demo</div>
									</a>
								</div>
							</div>
						</div>
					</div>
                    `;
                }
			}
			$('.themes_content').append(html_themes);

			var html_loadmore = '';
			if ( obj.next_page > 0 ) 
			{
				html_loadmore += `
				<input style="margin:10px 0 30px;" class="btn btn-outline-1 btn-orange cta ani block-in-xs" value="XEM THÊM" type="button" onclick="loadMoreThemesDataByCategory('`+obj.next_page+`');">
				`;
			}
			$('.themes_loadmore').html(html_loadmore);

            // Hidden loading
            $('.themes_loading').hide();
        },
        complete: function(){
            $('.themes_content .ajax-append').addClass('in-view');
        }
    });	
}
loadMoreThemesDataByCategory( 1 );
</script>
<style>
@media (min-width: 992px) {
    .col-md-offset-1-and-half {
        margin-left: 12.499999995%; 
        /*
        col-md-offset-1 has 8.33333333
        so you multiply 8.33333333 * 1.5 = 12.499999995% 
        */
    }
}
</style>