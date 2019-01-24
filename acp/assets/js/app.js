 
$(document).ready(function(){

	/***Del favicon**/

	$(".btn_del_favicon").click(function(){
		var obj = $(this);
		swal({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then(function () {
            // Ajax del image
            waitingDialog.show(cms_lang.waiting_dialog_msg);
            $.ajax({
                type: "post",
                url: site_root_domain+ "/?site=config_general&subact=del_favicon",
                // data: {id:page_id},
                success: function(responsive)
                {
                    waitingDialog.hide();
                    if(responsive == 1)
                    {
                        swal(
                          'Successful!',
                          'Delete page successful!',
                          'success'
                        ).then(function () {
                        // window.location.href = "/acp/?site=config_general";
                        })
                    }
                }
            });

            // xoa item
            $(obj).parents(".box_favicon").remove();
          });
	});

/* ==========================================================================
	Scroll
	========================================================================== */

	if (!("ontouchstart" in document.documentElement)) {

		document.documentElement.className += " no-touch";

		var jScrollOptions = {
			autoReinitialise: true,
			autoReinitialiseDelay: 100
		};

		// $('.box-typical-body').jScrollPane(jScrollOptions);
		$('.side-menu').jScrollPane(jScrollOptions);
		//$('.side-menu-addl').jScrollPane(jScrollOptions);
		scrollable_block =  $('.scrollable-block').jScrollPane(jScrollOptions);
		api_scrollable_block = scrollable_block.data('jsp');
	}

/* ==========================================================================
    Header search
    ========================================================================== */

	$('.site-header .site-header-search').each(function(){
		var parent = $(this),
			overlay = parent.find('.overlay');

		overlay.click(function(){
			parent.removeClass('closed');
		});

		parent.clickoutside(function(){
			if (!parent.hasClass('closed')) {
				parent.addClass('closed');
			}
		});
	});

/* ==========================================================================
    Header mobile menu
    ========================================================================== */

	// Dropdowns
	$('.site-header-collapsed .dropdown').each(function(){
		var parent = $(this),
			btn = parent.find('.dropdown-toggle');

		btn.click(function(){
			if (parent.hasClass('mobile-opened')) {
				parent.removeClass('mobile-opened');
			} else {
				parent.addClass('mobile-opened');
			}
		});
	});

	$('.dropdown-more').each(function(){
		var parent = $(this),
			more = parent.find('.dropdown-more-caption'),
			classOpen = 'opened';

		more.click(function(){
			if (parent.hasClass(classOpen)) {
				parent.removeClass(classOpen);
			} else {
				parent.addClass(classOpen);
			}
		});
	});

	// Left mobile menu
	$('.hamburger').click(function(){
		if ($('body').hasClass('menu-left-opened')) {
			$(this).removeClass('is-active');
			$('body').removeClass('menu-left-opened');
			$('html').css('overflow','auto');
		} else {
			$(this).addClass('is-active');
			$('body').addClass('menu-left-opened');
			$('html').css('overflow','hidden');
		}
	});

	$('.mobile-menu-left-overlay').click(function(){
		$('.hamburger').removeClass('is-active');
		$('body').removeClass('menu-left-opened');
		$('html').css('overflow','auto');
	});

	// Right mobile menu
	$('.site-header .burger-right').click(function(){
		if ($('body').hasClass('menu-right-opened')) {
			$('body').removeClass('menu-right-opened');
			$('html').css('overflow','auto');
		} else {
			$('.hamburger').removeClass('is-active');
			$('body').removeClass('menu-left-opened');
			$('body').addClass('menu-right-opened');
			$('html').css('overflow','hidden');
		}
	});

	$('.mobile-menu-right-overlay').click(function(){
		$('body').removeClass('menu-right-opened');
		$('html').css('overflow','auto');
	});

/* ==========================================================================
    Header help
    ========================================================================== */

	$('.help-dropdown').each(function(){
		var parent = $(this),
			btn = parent.find('>button'),
			popup = parent.find('.help-dropdown-popup'),
			jscroll;

		btn.click(function(){
			if (parent.hasClass('opened')) {
				parent.removeClass('opened');
				jscroll.destroy();
			} else {
				parent.addClass('opened');

				$('.help-dropdown-popup-cont, .help-dropdown-popup-side').matchHeight();

				if (!("ontouchstart" in document.documentElement)) {
					setTimeout(function(){
						jscroll = parent.find('.jscroll').jScrollPane(jScrollOptions).data().jsp;
						//jscroll.reinitialise();
					},0);
				}
			}
		});

		$('html').click(function(event) {
		    if (
		        !$(event.target).closest('.help-dropdown-popup').length
		        &&
		        !$(event.target).closest('.help-dropdown>button').length
		        &&
		        !$(event.target).is('.help-dropdown-popup')
		        &&
		        !$(event.target).is('.help-dropdown>button')
		    ) {
				if (parent.hasClass('opened')) {
					parent.removeClass('opened');
					jscroll.destroy();
		        }
		    }
		});

	});

/* ==========================================================================
    Side menu list
    ========================================================================== */

	$('.side-menu-list li.with-sub').each(function(){
		var parent = $(this),
			clickLink = parent.find('>span'),
			subMenu = parent.find('ul');

		clickLink.click(function(){
			if (parent.hasClass('opened')) {
				parent.removeClass('opened');
				subMenu.slideUp();
			} else {
				$('.side-menu-list li.with-sub').not(this).removeClass('opened').find('ul').slideUp();
				parent.addClass('opened');
				subMenu.slideDown();
			}
		});
	});


/* ==========================================================================
    Dashboard
    ========================================================================== */

	// Calculate height
	function dashboardBoxHeight() {
		$('.box-typical-dashboard').each(function(){
			var parent = $(this),
				header = parent.find('.box-typical-header'),
				body = parent.find('.box-typical-body');
			body.height(parent.outerHeight() - header.outerHeight());
		});
	}

	dashboardBoxHeight();

	$(window).resize(function(){
		dashboardBoxHeight();
	});

	// Collapse box
	$('.box-typical-dashboard').each(function(){
		var parent = $(this),
			btnCollapse = parent.find('.action-btn-collapse');

		btnCollapse.click(function(){
			if (parent.hasClass('box-typical-collapsed')) {
				parent.removeClass('box-typical-collapsed');
			} else {
				parent.addClass('box-typical-collapsed');
			}
		});
	});

	// Full screen box
	$('.box-typical-dashboard').each(function(){
		var parent = $(this),
			btnExpand = parent.find('.action-btn-expand'),
			classExpand = 'box-typical-full-screen';

		btnExpand.click(function(){
			if (parent.hasClass(classExpand)) {
				parent.removeClass(classExpand);
				$('html').css('overflow','auto');
			} else {
				parent.addClass(classExpand);
				$('html').css('overflow','hidden');
			}
			dashboardBoxHeight();
		});
	});

/* ==========================================================================
    Circle progress bar
    ========================================================================== */

	$(".circle-progress-bar").asPieProgress({
		namespace: 'asPieProgress',
		speed: 500
	});

	$(".circle-progress-bar").asPieProgress("start");


	$(".circle-progress-bar-typical").asPieProgress({
		namespace: 'asPieProgress',
		speed: 25
	});

	$(".circle-progress-bar-typical").asPieProgress("start");

 /* ==========================================================================
	Select
	========================================================================== */

	if ($('.bootstrap-select').size()) {
		// Bootstrap-select
		$('.bootstrap-select').selectpicker({
			style: '',
			width: '100%',
			size: 8
		});
	}

	if ($('.select2').size()) {

		// Select2
		//$.fn.select2.defaults.set("minimumResultsForSearch", "Infinity");

		$('.select2').not('.manual').select2();

		$(".select2-icon").not('.manual').select2({
			templateSelection: select2Icons,
			templateResult: select2Icons
		});

		$(".select2-arrow").not('.manual').select2({
			theme: "arrow"
		});

		$('.select2-no-search-arrow').select2({
			minimumResultsForSearch: "Infinity",
			theme: "arrow"
		});

		$('.select2-no-search-default').select2({
			minimumResultsForSearch: "Infinity"
		});

		$(".select2-white").not('.manual').select2({
			theme: "white"
		});

		example_select2_photo = $(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		});
		$(".select2-container--default").css("width","100%");
	}
   // Select2

	function select2Icons (state) {
		if (!state.id) { return state.text; }
		var $state = $(
			'<span class="font-icon ' + state.element.getAttribute('data-icon') + '"></span><span>' + state.text + '</span>'
		);
		return $state;
	}

	function select2Photos (state) {
		if (!state.id) { return state.text; }
		var $state = $(
			'<span class="user-item"><img src="' + state.element.getAttribute('data-photo') + '"/>' + state.text + '</span>'
		);
		return $state;
	}
    
/* ==========================================================================
	Search
	========================================================================== */

	$.typeahead({
		input: "#typeahead-search",
		order: "asc",
		minLength: 1,
		source: {
			data: [
				"Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda",
				"Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh",
				"Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia",
				"Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burma",
				"Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad",
				"Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic", "Congo, Republic of the",
				"Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti",
				"Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador",
				"Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon",
				"Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guatemala", "Guinea",
				"Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India",
				"Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan",
				"Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos",
				"Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
				"Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands",
				"Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Mongolia", "Morocco", "Monaco",
				"Mozambique", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger",
				"Nigeria", "Norway", "Oman", "Pakistan", "Panama", "Papua New Guinea", "Paraguay", "Peru",
				"Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Samoa", "San Marino",
				"Sao Tome", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone",
				"Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain",
				"Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan",
				"Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey",
				"Turkmenistan", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States",
				"Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
			]
		}
	});

/* ==========================================================================
	Datepicker
	========================================================================== */

	if ( typeof dateFormatBooking != "undefined" )
	{
		$('.datetimepicker-1').datetimepicker({
            widgetPositioning: {
                horizontal: 'right'
            },
            format:dateFormatBooking,
            debug: false
        });
        $('.datetimepicker-2').datetimepicker({
            widgetPositioning: {
                horizontal: 'right'
            },
            format: 'LT',
            debug: false
        });

        $('.booking_date').datetimepicker({
            format: dateFormatBooking,
            minDate: checktimebooking == 1 ? new Date().setHours(0,0,0,0) : new Date(),//new Date(new Date().setDate(new Date().getDate()-1)),
        });

        $('.datetimepicker-3').datetimepicker({
            widgetPositioning: {
                horizontal: 'right'
            },
            format:dateFormatBooking + ' hh:mm a',
            debug: false
        });
        
        $('.daterange-1').daterangepicker({
        	format: dateFormatBooking,
        	autoUpdateInput: false,
        	showDropdowns: true,
        	locale: {
		        cancelLabel: 'Clear',
		    }
        }, function(start, end, label) {
        	var start_end = start.format(dateFormatBooking)+'-'+end.format(dateFormatBooking);
        	$('.daterange-1').find('input').val(start_end).attr('value', start_end);
		});
        $('.daterange-1').on('apply.daterangepicker', function(ev, picker) {
        	var start_end = picker.startDate.format(dateFormatBooking)+'-'+picker.startDate.format(dateFormatBooking);
        	$('.daterange-1').find('input').val(start_end).attr('value', start_end);
		});
		$('.daterange-1').on('cancel.daterangepicker', function(ev, picker) {
			$('.daterange-1').find('input').val('').attr('value', '');
		});
    }

/* ==========================================================================
	Tooltips
	========================================================================== */

	// Tooltip
	$('[data-toggle="tooltip"]').tooltip({
		html: true
	});

	// Popovers
	$('[data-toggle="popover"]').popover({
		trigger: 'focus'
	});

/* ==========================================================================
	Validation
	========================================================================== */

	// $('#form-signin_v1').validate({
	// 	submit: {
	// 		settings: {
	// 			inputContainer: '.form-group'
	// 		}
	// 	}
	// });

	// $('#form-signin_v2').validate({
	// 	submit: {
	// 		settings: {
	// 			inputContainer: '.form-group',
	// 			errorListClass: 'form-error-text-block',
	// 			display: 'block',
	// 			insertion: 'prepend'
	// 		}
	// 	}
	// });

	$('#form-signup_v1, #form-signin_v1').validate({
		submit: {
			settings: {
				inputContainer: '.form-group',
				errorListClass: 'form-tooltip-error'
			},
                callback: {

                	onError: function (node, globalError) {
                        //console.log(node, globalError);
                        //$(".error").focus();
                        var container = $("html,body");
                        var scrollTo = $('.error:eq(0)');

                        container.animate({scrollTop: scrollTo.offset().top - container.offset().top - 100, scrollLeft: 0},20); 
                    }
              }
		}
	});

	$('#formorder-signin_v1,#formsite-signin_v1').validate({
		    submit: {
		      settings: {
		        inputContainer: '.form-group',
		        errorListClass: 'form-tooltip-error'
		      },
                callback: {

                	onError: function (node, globalError) {
                        //console.log(node, globalError);
                        //$(".error").focus();
                        var container = $("html,body");
                        var scrollTo = $('.error:eq(0)');

                        container.animate({scrollTop: scrollTo.offset().top - container.offset().top - 100, scrollLeft: 0},20); 
                    }
              }


		    }
		  });


	// $('#form-signup_v2').validate({
	// 	submit: {
	// 		settings: {
	// 			inputContainer: '.form-group',
	// 			errorListClass: 'form-tooltip-error'
	// 		}
	// 	}
	// });
 

/* ==========================================================================
	Bar chart
	========================================================================== */

	$(".bar-chart").peity("bar",{
		delimiter: ",",
		fill: ["#919fa9"],
		height: 16,
		max: null,
		min: 0,
		padding: 0.1,
		width: 384
	});

/* ==========================================================================
	Full height box
	========================================================================== */

	function boxFullHeight() {
		var sectionHeader = $('.section-header');
		var sectionHeaderHeight = 0;

		if (sectionHeader.size()) {
			sectionHeaderHeight = parseInt(sectionHeader.height()) + parseInt(sectionHeader.css('padding-bottom'));
		}

		$('.box-typical-full-height').css('min-height',
			$(window).height() -
			parseInt($('.page-content').css('padding-top')) -
			parseInt($('.page-content').css('padding-bottom')) -
			sectionHeaderHeight -
			parseInt($('.box-typical-full-height').css('margin-bottom')) - 2
		);
		$('.box-typical-full-height>.tbl, .box-typical-full-height>.box-typical-center').height(parseInt($('.box-typical-full-height').css('min-height')));
	}

	boxFullHeight();

	$(window).resize(function(){
		boxFullHeight();
	});

/* ==========================================================================
	Chat
	========================================================================== */

	function chatHeights() {
		$('.chat-dialog-area').height(
			$(window).height() -
			parseInt($('.page-content').css('padding-top')) -
			parseInt($('.page-content').css('padding-bottom')) -
			parseInt($('.chat-container').css('margin-bottom')) - 2 -
			$('.chat-area-header').outerHeight() -
			$('.chat-area-bottom').outerHeight()
		);
		$('.chat-list-in')
			.height(
				$(window).height() -
				parseInt($('.page-content').css('padding-top')) -
				parseInt($('.page-content').css('padding-bottom')) -
				parseInt($('.chat-container').css('margin-bottom')) - 2 -
				$('.chat-area-header').outerHeight()
			)
			.css('min-height', parseInt($('.chat-dialog-area').css('min-height')) + $('.chat-area-bottom').outerHeight());
	}

	chatHeights();

	$(window).resize(function(){
		chatHeights();
	});

/* ==========================================================================
	Auto size for textarea
	========================================================================== */

	autosize($('textarea[data-autosize]'));

/* ==========================================================================
	Pages center
	========================================================================== */

	$('.page-center').matchHeight({
		target: $('html')
	});

	$(window).resize(function(){
		setTimeout(function(){
			$('.page-center').matchHeight({ remove: true });
			$('.page-center').matchHeight({
				target: $('html')
			});
		},100);
	});

/* ==========================================================================
	Cards user
	========================================================================== */

	$('.card-user').matchHeight();

/* ==========================================================================
	Fancybox
	========================================================================== */

	$(".fancybox").fancybox({
		padding: 0,
		openEffect	: 'none',
		closeEffect	: 'none'
	});

/* ==========================================================================
	Profile slider
	========================================================================== */

	$(".profile-card-slider").slick({
		slidesToShow: 1,
		adaptiveHeight: true,
		prevArrow: '<i class="slick-arrow font-icon-arrow-left"></i>',
		nextArrow: '<i class="slick-arrow font-icon-arrow-right"></i>'
	});

/* ==========================================================================
	Posts slider
	========================================================================== */

	var postsSlider = $(".posts-slider");

	postsSlider.slick({
		slidesToShow: 4,
		adaptiveHeight: true,
		arrows: false,
		responsive: [
			{
				breakpoint: 1700,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 1350,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 500,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});

	$('.posts-slider-prev').click(function(){
		postsSlider.slick('slickPrev');
	});

	$('.posts-slider-next').click(function(){
		postsSlider.slick('slickNext');
	});

/* ==========================================================================
	Recomendations slider
	========================================================================== */

	var recomendationsSlider = $(".recomendations-slider");

	recomendationsSlider.slick({
		slidesToShow: 4,
		adaptiveHeight: true,
		arrows: false,
		responsive: [
			{
				breakpoint: 1700,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 1350,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 500,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});

	$('.recomendations-slider-prev').click(function(){
		recomendationsSlider.slick('slickPrev');
	});

	$('.recomendations-slider-next').click(function(){
		recomendationsSlider.slick('slickNext');
	});

/* ==========================================================================
	Pnotify
	========================================================================== */

	PNotify.prototype.options.styling = "bootstrap3";


/* ==========================================================================
	Box typical full height with header
	========================================================================== */

	function boxWithHeaderFullHeight() {
		$('.box-typical-full-height-with-header').each(function(){
			var box = $(this),
				boxHeader = box.find('.box-typical-header'),
				boxBody = box.find('.box-typical-body');

			boxBody.height(
				$(window).height() -
				parseInt($('.page-content').css('padding-top')) -
				parseInt($('.page-content').css('padding-bottom')) -
				parseInt(box.css('margin-bottom')) - 2 -
				boxHeader.outerHeight()
			);
		});
	}

	boxWithHeaderFullHeight();

	$(window).resize(function(){
		boxWithHeaderFullHeight();
	});

/* ==========================================================================
	Gallery
	========================================================================== */

	$('.gallery-item').matchHeight({
		target: $('.gallery-item .gallery-picture')
	});

/* ==========================================================================
	File manager
	========================================================================== */

	function fileManagerHeight() {
		$('.files-manager').each(function(){
			var box = $(this),
				boxColLeft = box.find('.files-manager-side'),
				boxSubHeader = box.find('.files-manager-header'),
				boxCont = box.find('.files-manager-content-in'),
				boxColRight = box.find('.files-manager-aside');

			var paddings = parseInt($('.page-content').css('padding-top')) +
							parseInt($('.page-content').css('padding-bottom')) +
							parseInt(box.css('margin-bottom')) + 2;

			boxColLeft.height('auto');
			boxCont.height('auto');
			boxColRight.height('auto');

			if ( boxColLeft.height() <= ($(window).height() - paddings) ) {
				boxColLeft.height(
					$(window).height() - paddings
				);
			}

			if ( boxColRight.height() <= ($(window).height() - paddings - boxSubHeader.outerHeight()) ) {
				boxColRight.height(
					$(window).height() -
					paddings -
					boxSubHeader.outerHeight()
				);
			}

			boxCont.height(
				boxColRight.height()
			);
		});
	}

	fileManagerHeight();

	$(window).resize(function(){
		fileManagerHeight();
	});

/* ==========================================================================
	Mail
	========================================================================== */

	function mailBoxHeight() {
		$('.mail-box').each(function(){
			var box = $(this),
				boxHeader = box.find('.mail-box-header'),
				boxColLeft = box.find('.mail-box-list'),
				boxSubHeader = box.find('.mail-box-work-area-header'),
				boxColRight = box.find('.mail-box-work-area-cont');

			boxColLeft.height(
				$(window).height() -
				parseInt($('.page-content').css('padding-top')) -
				parseInt($('.page-content').css('padding-bottom')) -
				parseInt(box.css('margin-bottom')) - 2 -
				boxHeader.outerHeight()
			);

			boxColRight.height(
				$(window).height() -
				parseInt($('.page-content').css('padding-top')) -
				parseInt($('.page-content').css('padding-bottom')) -
				parseInt(box.css('margin-bottom')) - 2 -
				boxHeader.outerHeight() -
				boxSubHeader.outerHeight()
			);
		});
	}

	mailBoxHeight();

	$(window).resize(function(){
		mailBoxHeight();
	});

/* ==========================================================================
	Nestable
	========================================================================== */

	$('.dd-handle').hover(function(){
		$(this).prev('button').addClass('hover');
		$(this).prev('button').prev('button').addClass('hover');
	}, function(){
		$(this).prev('button').removeClass('hover');
		$(this).prev('button').prev('button').removeClass('hover');
	});

/* ==========================================================================
	Widget weather slider
	========================================================================== */

	$('.widget-weather-slider').slick({
		arrows: false,
		dots: true,
		infinite: false,
		slidesToShow: 4,
		slidesToScroll: 4
	});

/* ==========================================================================
	Addl side menu
	========================================================================== */

	setTimeout(function(){
		if (!("ontouchstart" in document.documentElement)) {
			$('.side-menu-addl').jScrollPane(jScrollOptions);
		}
	},1000);

/* ==========================================================================
	Widget chart combo
	========================================================================== */

	$('.widget-chart-combo-content-in, .widget-chart-combo-side').matchHeight();


/* ==========================================================================
	Header notifications
	========================================================================== */

	// Tabs hack
	$('.dropdown-menu-messages a[data-toggle="tab"]').click(function (e) {
		e.stopPropagation();
		e.preventDefault();
		$(this).tab('show');

		// Scroll
		if (!("ontouchstart" in document.documentElement)) {
			jspMessNotif = $('.dropdown-notification.messages .tab-pane.active').jScrollPane(jScrollOptions).data().jsp;
		}
	});

	// Scroll
	var jspMessNotif,
		jspNotif;

	$('.dropdown-notification.messages').on('show.bs.dropdown', function () {
		if (!("ontouchstart" in document.documentElement)) {
			jspMessNotif = $('.dropdown-notification.messages .tab-pane.active').jScrollPane(jScrollOptions).data().jsp;
		}
	});

	$('.dropdown-notification.messages').on('hide.bs.dropdown', function () {
		if (!("ontouchstart" in document.documentElement)) {
			jspMessNotif.destroy();
		}
	});

	$('.dropdown-notification.notif').on('show.bs.dropdown', function () {
		if (!("ontouchstart" in document.documentElement)) {
			jspNotif = $('.dropdown-notification.notif .dropdown-menu-notif-list').jScrollPane(jScrollOptions).data().jsp;
		}
	});

	$('.dropdown-notification.notif').on('hide.bs.dropdown', function () {
		if (!("ontouchstart" in document.documentElement)) {
			jspNotif.destroy();
		}
	});

/* ==========================================================================
	Steps progress
	========================================================================== */

	function stepsProgresMarkup() {
		$('.steps-icon-progress').each(function(){
			var parent = $(this),
				cont = parent.find('ul'),
				padding = 0,
				padLeft = (parent.find('li:first-child').width() - parent.find('li:first-child .caption').width())/2,
				padRight = (parent.find('li:last-child').width() - parent.find('li:last-child .caption').width())/2;

			padding = padLeft;

			if (padLeft > padRight) padding = padRight;

			cont.css({
				marginLeft: -padding,
				marginRight: -padding
			});

		 
		});
	}

	stepsProgresMarkup();

	$(window).resize(function(){
		stepsProgresMarkup();
	});

/* ========================================================================== */

	$('.control-panel-toggle').on('click', function() {
		var self = $(this);
		
		if (self.hasClass('open')) {
			self.removeClass('open');
			$('.control-panel').removeClass('open');
		} else {
			self.addClass('open');
			$('.control-panel').addClass('open');
		}
	});

	$('.control-item-header .icon-toggle, .control-item-header .text').on('click', function() {
		var content = $(this).closest('li').find('.control-item-content');

		if (content.hasClass('open')) {
			content.removeClass('open');
		} else {
			$('.control-item-content.open').removeClass('open');
			content.addClass('open');
		}
	});

	$.browser = {};
	$.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
	$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
	$.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());

	if ($.browser.chrome) {
		$('body').addClass('chrome-browser');
	} else if ($.browser.msie) {
		$('body').addClass('msie-browser');
	} else if ($.browser.mozilla) {
		$('body').addClass('mozilla-browser');
	}

	$('#show-hide-sidebar-toggle').on('click', function() {
		if (!$('body').hasClass('sidebar-hidden')) {
			$('body').addClass('sidebar-hidden');
		} else {
			$('body').removeClass('sidebar-hidden');
		}
	});
});




 

function changeLangTab(className = 'change-language-tab', langCode = 'en')
{
    $("." + className).hide();
    $("." + className + "[lang='" + langCode + "']").show();

    $("#" + className + " [lang]").removeClass('active');
    $("#" + className + " [lang='" + langCode + "']").addClass('active');
    $("#" + className + " [name='lang_code']").val(langCode);
}

function blockLoading(obj = $('#blockui-element-container-dark')) {
    obj.block({
        message: '<div class="blockui-default-message"><i class="fa fa-circle-o-notch fa-spin"></i><h6>Please Wait</h6></div>',
        overlayCSS:  {
            background: 'rgba(24, 44, 68, 0.8)',
            opacity: 1,
            cursor: 'wait'
        },
        css: {
            width: '50%'
        },
        blockMsgClass: 'block-msg-default'
    });
}

function unblockLoading(obj = $('#blockui-element-container-dark')) {
    obj.unblock()
}

function setAllPermission(labelObj=$('.permission_inactive,.permission_active'), checkboxObj=$('.permission_block :checkbox')) {
    labelObj.removeAttr('style');
    labelObj.addClass('permission_active');
    labelObj.removeClass('permission_inactive');
    checkboxObj.prop('checked', true);
}

function unsetAllPermission(labelObj=$('.permission_inactive,.permission_active'), checkboxObj=$('.permission_block :checkbox')) {
    labelObj.removeAttr('style');
    labelObj.addClass('permission_inactive');
    labelObj.removeClass('permission_active');
    checkboxObj.prop('checked', false);
}

function revertPermission(labelObj=$('.permission_inactive,.permission_active')) {
    labelObj.trigger("click");
}

function stickGroupPermisstion(obj)
{
	let groupKey = obj.val();
	if(obj.is(':checked'))
	{
        obj.parents('.permission_block:first').find('[id^="per_' + groupKey + '_"]:not([id^="per_' + groupKey + '_delete"])').prop('checked',true);
        obj.parents('.permission_block:first').find('[id^="per_' + groupKey + '_"]:not([id^="per_' + groupKey + '_delete"])').parent().addClass('permission_active');
        obj.parents('.permission_block:first').find('[id^="per_' + groupKey + '_"]:not([id^="per_' + groupKey + '_delete"])').parent().removeClass('permission_inactive');
	}
	else
	{
        obj.parents('.permission_block:first').find('[id^="per_' + groupKey + '_"]').prop('checked',false);
        obj.parents('.permission_block:first').find('[id^="per_' + groupKey + '_"]').parent().addClass('permission_inactive');
        obj.parents('.permission_block:first').find('[id^="per_' + groupKey + '_"]').parent().removeClass('permission_active');
	}
}

function headerQuickSearch(obj, url, loader)
{
	obj.autocomplete({
        source: url,
        minLength: 2,
        search: function( event, ui ) {
            if(loader)
            {
                loader.show();
            }
        },
        response: function( event, ui ) {
            if(loader)
            {
                loader.hide();
            }
		},
        select: function( event, ui ) {
            window.location.href = ui.item.redirect;
        }
    });
}

function setFrmExport(btnSelector, frmSelector, loadingSelector)
{
    frmSelector.find("[name=export]").val(1);
	downloadConfirm(btnSelector, frmSelector, loadingSelector);
}

function unSetFrmExport(frmSelector)
{
    frmSelector.find("[name=export]").val(0);
}

function sortTheme(theme_id=0, theme_order=0, input=null)
{
    let successStyle = 'border-color: green;';
    let errorStyle = 'border-color: red;';

	if(typeof input != 'undefined' && input)
	{
        input.prop("disabled", true);
	}

	$.ajax({
		type: 'get',
		url: site_root_domain + "/?site=themes&act=edit&subact=sort",
		data: {
            theme_id: theme_id,
            theme_order: theme_order
		},
		dataType: 'json',
		success: function(res) {
            if(typeof input != 'undefined' && input) {
                input.prop("disabled", false);
				if(res.status != 'ok')
                {
                    input.attr('style', errorStyle);
                }
                else
				{
                    input.attr('style', successStyle);
				}

				setTimeout(function(){
                    input.removeAttr('style');
				},1000);
            }

            if(res.status != 'ok')
            {
                alert("Corrupt error while updating !");
            }
		}
	});
}

function dropdownInput(dropdown, input)
{
    let value = input.val();
    let text = dropdown.find(".dropdown-item[value=" + value + "]").text();
    dropdown.find("button").text(text);

    dropdown.find(".dropdown-item").click(function(){
    	let value = $(this).attr("value");
    	let text = $(this).text();
        dropdown.find("button").text(text);

        if(input.val() != value) {
            input.val(value).trigger("change");
		}
	})
}

function changeDiscountItems(){

	let totalDiscountType = $("#total_discount_type").val();
    let totalDiscountValue = $("#total-discount-show").val();

    //Quy hết về %
	if(totalDiscountType == 1) {
        totalDiscountValue = calculateTotalTransactionResult.subtotal == 0 ? 0 : (totalDiscountValue/calculateTotalTransactionResult.subtotal)*100;
        totalDiscountValue = parseFloat(totalDiscountValue.toFixed(2));
	}

    let rows = $(".row-grid, .row-grid-asset");

    $.each(rows, function(index, row){
        let price = $(row).find("[name='product_price[]']:first, [name='ass_price[]']:first").val();
        let quantity = $(row).find("[name='product_quantity[]']:first, [name='ass_quantity[]']:first").val();
        let cycle = $(row).find("[name='product_cycle[]']:first").val();
        let tax_percent = $(row).find("[name='product_tax[]']:first, [name='ass_tax[]']:first").val();

        let discountType = $(row).find("[name='product_discount_type[]']:first,[name='ass_discount_type[]']:first").val();
        let discountValue = $(row).find("[name='product_discount_value[]']:first, [name='ass_discount_value[]']:first").val();

        let calResult = calculateItem(price, 0, totalDiscountValue, tax_percent, quantity, cycle);
        if(discountType == 0){ //percent
            $(row).find("[name='product_discount_value[]']:first, [name='ass_discount_value[]']:first").val(totalDiscountValue);
		} else { //amount
			var total_discount = parseFloat(calResult.totalDiscount.toFixed(2));
            $(row).find("[name='product_discount_value[]']:first, [name='ass_discount_value[]']:first").val(total_discount/quantity);
		}
		var total_final = parseFloat(calResult.total.toFixed(2));
        $(row).find("[name='product_amount[]']").val(total_final);
    })

    calculate_money();
}

function changeCommissionItems(){
    let totalCommissionType = $("#total_commission_type").val();
    let totalCommissionValue = $("#total-commission-show").val();

    // Quy hết về %
    if(totalCommissionType == 1){
        totalCommissionValue = calculateTotalTransactionResult.subtotal-calculateTotalTransactionResult.discount == 0 ? 0 : (totalCommissionValue/calculateTotalTransactionResult.subtotal-calculateTotalTransactionResult.discount)*100;
    }

    let rows = $(".row-grid, .row-grid-asset");

    $.each(rows, function(index, row){
        let commissionType = $(row).find("[name='product_commission_type[]']:first").val()*1;
        let commissionValue = $(row).find("[name='product_commission_value[]']:first").val()*1;
        let commissionShowObj = $(row).find('.commission_show:first');

        let price = $(row).find("[name='product_price[]']:first, [name='ass_price[]']:first").val();
        let quantity = $(row).find("[name='product_quantity[]']:first, [name='ass_quantity[]']:first").val();
        let cycle = $(row).find("[name='product_cycle[]']:first").val();
        let tax_percent = $(row).find("[name='product_tax[]']:first, [name='ass_tax[]']:first").val();

        let discountType = $(row).find("[name='product_discount_type[]']:first,[name='ass_discount_type[]']:first").val();
        let discountValue = $(row).find("[name='product_discount_value[]']:first, [name='ass_discount_value[]']:first").val();

        let calResult = calculateItem(price, discountType, discountValue, tax_percent, quantity, cycle);

        if(commissionType == 0){ //percent
            $(row).find("[name='product_commission_value[]']:first").val(totalCommissionValue);
        } else { //amount
            $(row).find("[name='product_commission_value[]']:first").val(((totalCommissionValue*(calResult.subTotal-calResult.totalDiscount))/quantity)/100);
        }

        showCommissionItem(commissionType, $(row).find("[name='product_commission_value[]']:first").val(), commissionShowObj);
    })
}



