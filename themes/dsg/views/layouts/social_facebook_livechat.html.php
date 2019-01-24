<style>
	#facebook_livechat {
		position: fixed;
		z-index: 9999999;
		bottom: 0px;
		right: 0px;
		
		display: none;
		width: auto;
		height: auto;
		overflow: hidden;

		background-color: #fff;
	}
	#facebook_livechat .header {
		cursor: pointer;

		display: block;
		height: 40px;

		background-color: #3a5795;

		color: #fff;
		font-size: 18px;
		line-height: 40px;
		text-shadow: 0 1px 0 rgba(0,0,0,0.1);
		text-indent: 15px;
		text-align: left;
	}
	#facebook_livechat .header:hover {
		color: #E94E75;
	}
	#facebook_livechat .content {
		display: none;
		width: 100%;
		overflow: hidden;
	}


	/* Use for set width and height live chat */
	#facebook_livechat .header {
		width: 350px;
	}
	#facebook_livechat .content {
		height: 350px;
	}
	/* End Use for set width and height live chat */
</style>

<div id="facebook_livechat">
	<div class="header"><i class="fa fa-facebook-square"></i> Live chat</div>
	<div class="content"></div>
</div>

<script>
	$(document).ready(function () {
		// Load Live chat
    	$(window).on('load', function(){

    		// Render iframe
    		function load_facebook_livechat () {
    			if ( typeof facebook_id_fanpage != 'undefined' && facebook_id_fanpage ) {

	    			// calculator width + height
			        var width = $('#facebook_livechat .content').width();
			        var height = $('#facebook_livechat .content').height();
			        if ( width > 500 ) { width = 500; }
			        if ( height < 70 ) { height = 70; }
	    			// console.log('on resize:'+width+'px'+'=='+height+'px');

	    			$('#facebook_livechat .content').html('<iframe src="https://www.facebook.com/plugins/page.php?href='+facebook_id_fanpage+'&width='+width+'&height='+height+'&tabs=messages&hide_cover=true&show_facepile=true&hide_cta=false&small_header=true&adapt_container_width=true&appId" width="'+width+'" height="'+height+'" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>');
	    		}
    		}

    		// Show chat div
    		if ( typeof facebook_id_fanpage != 'undefined' && facebook_id_fanpage ) {
    			$('#facebook_livechat').css('display', 'inline-block');
    		}

    		// Event click
    		$('#facebook_livechat .header').click(function() {
				$('#facebook_livechat .content').toggle("slow","swing", function(){
		            if ( $('#facebook_livechat .content').hasClass('facebook_livechat_installed') != true ) {
						// Call Render iframe
		        		load_facebook_livechat();

		        		// Add class
		        		$('#facebook_livechat .content').addClass('facebook_livechat_installed');
					}
		        });
			});

	        // When resized then Call Again Render iframe
	        // $(window).on('resize', function(){

	        //     // Firing resize event only when resizing is finished
	        //     clearTimeout(window.resized_load_facebook_livechat);
	        //     window.resized_load_facebook_livechat = setTimeout(function(){

	        //         // Re Call Again Render iframe
	        //         // load_facebook_livechat();
	        //     }, 500);
	        // });
		});
	});
</script>