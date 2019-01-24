<div class="box-typical chat-container">
	<section class="chat-list">
		<div class="chat-list-search chat-list-settings-header">
			<div class="row">
				<div class="col-sm-2 col-lg-2 action">
					<a href="javascript:void(0)"><span class="font-icon font-icon-cogwheel"></span></a>
				</div>
				<div class="col-sm-8 col-lg-8 text-center description">
					<?=$CMS->lang['fanpage_message'];?>
				</div>
				<div class="col-sm-2 col-lg-2 text-right action">
					<a href="javascript:void(0)"><span class="font-icon fa fa-pencil"></span></a>
				</div>
			</div>
		</div>
		<div class="chat-list-in scrollable-block" id="get_message_list">
		</div>
	</section>
	<section class="chat-list-info">
		<div class="chat-list-search chat-list-settings-header">
			<a href="#"><span class="fa fa-phone"></span></a>
			<a href="#"><span class="fa fa-video-camera"></span></a>
			<a href="#"><span class="fa fa-info-circle"></span></a>
		</div>
		<div class="chat-list-in">
		</div>
	</section>
	<section class="chat-area">
		<div class="chat-area-in">
			<div class="chat-area-header">
			</div>
			<div class="chat-dialog-area scrollable-block">
				<div class="messenger-dialog-area" id="get_message_user_list">
				</div>
			</div>
			<div class="chat-area-bottom">
				<form class="write-message">
					<div class="form-group">
						<textarea rows="3" class="form-control message"></textarea>
						<div class="dropdown dropdown-typical dropup attach">
							<button type="button" class="btn btn-rounded btn-inline btn-sm send-message"><i class="fa fa-paper-plane-o"></i></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>

<script src="https://hooks.3f.team:3000/socket.io/socket.io.js"></script>
<script>
	$(document).ready(function() {
		var url = 'https://hooks.3f.team:3000';
		var page_id = '<?=$CMS->input['page_id']?>';
		$('#get_message_list').block({
			message: '<div class="blockui-default-message"><i class="fa fa-circle-o-notch fa-spin"></i></div>',
			overlayCSS:  {
				background: 'rgba(50, 50, 50, 0.5)',
				opacity: 1,
				cursor: 'wait'
			},
			blockMsgClass: 'block-msg-default'
		});
		$.getJSON(url + '/token',  { 
			page_id : page_id
		})
		.done(function(json) {
			var socket = io.connect(url);
			socket.on('connect', function () {
			socket
				.emit('authenticate', {token: json.token})
				.on('authenticated', function () {
					// socket.emit || socket.on
					var page_token = json.page_token;
					var user = {};
					socket.emit('get_message', {});
					socket.on('get_message', function(array) {
						var arr = {};
						$(array).each(function(i) {
							var e = $(this)[0];
							var id = e.type == 1 ? e.post_id : e.user_id;
							if (arr[id] == undefined) {
								arr[id] = {count_message : 1, name : e.user_name, avartar : e.user_avartar, message : e.user_message, type : e.type, post_id : e.post_id};
							} else {
								arr[id]['count_message']++;
								arr[id]['message'] = e.user_message;
							}
						});
						$('#get_message_list').empty();
						$.each(arr, function(k, v) {
							var html = '<div class="chat-list-item online">';
								html += '<input type="hidden" name="type" value="' + v['type'] + '">';
								html += '<input type="hidden" name="user_id" value="' + k + '">';
								html += '<div class="chat-list-item-photo">';
									html += '<img src="' + v['avartar'] + '">';
								html += '</div>';
								html += '<div class="chat-list-item-header">';
									html += '<div class="chat-list-item-name">';
										html += '<span class="name">' + v['name'] + '</span>';
									html += '</div>';
								html += '</div>';
								html += '<div class="chat-list-item-cont">';
									html += '<div class="chat-list-item-txt writing">';
										html += '<div class="icon">';
											html += '<i class="font-icon font-icon-pencil-thin"></i>';
										html += '</div>';
											html += v['message'];
									html += '</div>';
									
									html += '<div class="chat-list-item-count">';
										html += v['count_message'] + ' ';
										if (v['type'] == 1) {
											html += '<i class="font-icon font-icon-comments"></i>';
										} else {
											html += '<i class="font-icon font-icon-mail"></i>';
										}
									html += '</div>';
								html += '</div>';
							html += '</div>';
							$('#get_message_list').append(html);
						});
						$('#get_message_list').unblock();
						$("div.chat-list-item").on('click',function() {
							$('div.chat-area-in').block({
								message: '<div class="blockui-default-message"><i class="fa fa-circle-o-notch fa-spin"></i></div>',
								overlayCSS:  {
									background: 'rgba(50, 50, 50, 0.5)',
									opacity: 1,
									cursor: 'wait'
								},
								blockMsgClass: 'block-msg-default'
							});
							socket.emit('get_message_user', {user_id: $(this).children('input[name="user_id"]').val(), type : $(this).children('input[name="type"]').val()});
						});
						$("button.send-message").on('click',function() {
							var message_type = $('input[name="message_type"]').val();
							var message_user_id = $('input[name="message_user_id"]').val();
							var message_post_id = $('input[name="message_post_id"]').val();
							var message = $('textarea.message').val();
							if (message_type == 1) {
								if (message_post_id != '' && message != '') {
									$("button.send-message").attr('disabled', 'disabled');
									$.post(
										'https://graph.facebook.com/v2.10/' + message_post_id + '/comments?access_token=' + page_token,
										{message: message},
										{ func: "getNameAndTime" }, "json"
									)
									.done(function(json) {
										if (typeof json.id != 'underfined') {
											$('textarea.message').val('');
											socket.emit('get_message_user', {user_id: message_post_id, type : 1});
										} else {
											swal('<?=$CMS->lang['fanpage_send_message_error']?>');
										}
										$("button.send-message").removeAttr('disabled');
									})
									.fail(function(jqxhr, textStatus, error) {
										$("button.send-message").removeAttr('disabled');
										console.log("Request Failed: " + error);
									});
								}
							} else {
								if (message_user_id != '' && message != '') {
									$("button.send-message").attr('disabled', 'disabled');
									$.post(
										"https://graph.facebook.com/v2.10/me/messages?access_token=" + page_token,
										{recipient : {id : message_user_id}, message : {text : message}},
										{ func: "getNameAndTime" }, "json"
									)
									.done(function(json) {
										if (typeof json.message_id != 'underfined' && typeof json.recipient_id != 'underfined') {
											$('textarea.message').val('');
											socket.emit('get_message_user', {user_id: json.recipient_id, type : 0});
										} else {
											swal('<?=$CMS->lang['fanpage_send_message_error']?>');
										}
										$("button.send-message").removeAttr('disabled');
									})
									.fail(function(jqxhr, textStatus, error) {
										$("button.send-message").removeAttr('disabled');
										console.log("Request Failed: " + error);
									});
								}
							}
						});
						socket.on('get_message_user', function(obj) {
							var array = [];
							$.each(obj, function(i,n) {
								array[i] = n;
							});
							if (array.length > 0) {
								$('#get_message_user_list').empty();
								$(array).each(function(i) {
									var e = $(this)[0];
									if (typeof e.user_id == 'string') {
										var html;
										if (e.user_id == page_id) {
											html = '<div class="messenger-message-container from bg-blue">';
												html += '<div class="messages">';
													html += '<ul>';
														html += '<li>';
															html += '<div class="message">';
																html += '<div>';
																	html += e.user_message;
																html += '</div>';
															html += '</div>';
														html += '</li>';
													html += '</ul>';
												html += '</div>';
												html += '<div class="avatar chat-list-item-photo">';
													html += '<img src="' + e.user_avartar + '">';
												html += '</div>';
											html += '</div>';
										} else {
											html = '<div class="messenger-message-container">';
												html += '<div class="avatar">';
													html += '<img src="' + e.user_avartar + '">';
												html += '</div>';
												html += '<div class="messages">';
													html += '<ul>';
														html += '<li>';
															html += '<div class="message">';
																html += '<div>';
																	html += e.user_message;
																html += '</div>';
															html += '</div>';
														html += '</li>';
													html += '</ul>';
												html += '</div>';
											html += '</div>';
											$('div.chat-area-header').html('<div class="chat-list-item online"><div class="chat-list-item-name"><span class="name">' + e.user_name + '</span></div></div>');
											if ($('input[name="message_type"]').val() == undefined) {
												$('form.write-message').append('<input type="hidden" name="message_type" value="' + e.type + '">');
											}
											if ($('input[name="message_user_id"]').val() == undefined) {
												$('form.write-message').append('<input type="hidden" name="message_user_id" value="' + e.user_id + '">');
											}
											if ($('input[name="message_post_id"]').val() == undefined) {
												$('form.write-message').append('<input type="hidden" name="message_post_id" value="' + e.post_id + '">');
											}
										}
										$('#get_message_user_list').append(html);
									}
								});
								$('div.chat-area-in').unblock();
							}
						});
						socket.on('facebook_webhook', function(obj) {
							socket.emit('get_message', {});
							if (typeof obj.user_id != 'underfined' && $('textarea.message').val() == '') {
								socket.emit('get_message_user', {user_id : obj.user_id, type : obj.type});
							}
						});
					});
				})
				.on('unauthorized', function(msg) {
					console.log("unauthorized: " + JSON.stringify(msg.data));
				})
			});
		})
		.fail(function( jqxhr, textStatus, error ) {
			var err = textStatus + ", " + error;
		});
	});
</script>