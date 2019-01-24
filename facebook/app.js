// lêlong lelong210@gmail.com 010917
process.env.PORT = '8081';
process.env.ADDRESS = '127.0.0.1';
process.env.jwt_token = '@_TOken_123_client';
process.env.verify_token = '@_TOken_123_facebook';

const timestamp = require('console-timestamp');
const bodyParser = require('body-parser');
const request = require('request');
const app = require('express')();
const http = require('http').Server(app);
const io = require('socket.io')(http);
const socketioJwt = require("socketio-jwt");
const mysql = require('mysql');
const async = require('async');
const elasticsearch = require('elasticsearch');
const imageType = require('image-type');
const imageDownloader = require('image-downloader');
const FB = require('fb');
const esClient = new elasticsearch.Client({
    host: [
		{
			host: 'local.elastic.3f.team', // 188.166.233.118 | local.elastic.3f.team
			auth: 'fff:#iEM@o2tzxn*5oUe',
			protocol: 'http',
			port: 9200
		}
	],
    log: 'error'
});
const config = {
	host: "127.0.0.1",
	user: "fff",
	password: "ZF3rWdkrAr%$38@6",
	database: "fff_eco_db"
};

// var con = mysql.createConnection(config);	
// con.connect(function(err) {
	// if (err) {
		// console.log(err);
	// } else {
		// con.end();
	// }
// });

app.use(function (req, res, next) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,contenttype');
    res.setHeader('Access-Control-Allow-Credentials', true);
    next();
});
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
	extended: true
}));
FB.options({version: 'v2.11'});

app.get('/', function(req, res) {
	res.send('lelong210@gmail.com');
});
app.get('/ping', function(req, res) {
	esClient.ping({
		requestTimeout: 1000
	}, function (error) {
		var arr = {status : ""};
		if (error) {
			arr["status"] = false;
		} else {
			arr["status"] = true;
		}
		res.status(200).json(arr);
	});
});
app.get('/token', function(req, res) {
	var jwt = require('jsonwebtoken');
	var profile = new Array();
	var page_id = "";
	req.param('page_id').forEach(function(e) {
		page_id += ((page_id == "") ? "" : " ") + e;
	});
	profile['page_id'] = page_id;
	res.json({token: jwt.sign(profile, process.env.jwt_token, {expiresIn : 60*60})});
});
app.get('/get_message', function(req, res) {
	let body = {
		query : {
			bool : {
				should : [ ]
			}
		},
		sort : [
			{ fb_content_timestamp : { order : "asc" } }
		],
		size : 10000,
	};
	req.param('page_id').forEach(function(e) {
		body.query.bool.should.push({ query_string: { query: '(fanpage_id:' + e + ') AND (NOT fb_content_sender_id:' + e + ')' } });
	});
	var array = [];
	esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
		if (results.hits.total > 0) {
			results.hits.hits.forEach(function(e) {
				array.push({
					fanpage_id : e._source.fanpage_id,
					user_id : e._source.fb_content_sender_id,
					parent : e._source.fb_content_parent,
					sender_view_id : e._source.fb_content_sender_view_id,
					recipient_id : e._source.fb_content_recipient_id,
					attachment_type : e._source.fb_content_attachment_type,
					user_message : e._source.fb_content_message,
					post_id : e._source.fb_content_post_id,
					type : e._source.fb_content_type,
					tags : e._source.tags,
					phone : e._source.phone,
					read : e._source.fb_content_read,
					reply : e._source.fb_content_reply,
					time : e._source.fb_content_timestamp
				});
			});
		}
		res.status(200).json(array);
		console.log(`io.emit get_message ${results.hits.total} items in ${results.took}ms`);
	}).catch(console.error);
});
app.get('/get_message_user', function(req, res) {
	let body = {
		query : {
			bool : {
				must : [
					{ match : { fanpage_id : req.param('page_id') } }
				]
			}
		},
		sort : [
			{ fb_content_timestamp : { order : "asc" } }
		],
		size : 10000,
	};
	if (req.param('type') == 1) {
		body.query.bool.must.push({ query_string: { query: '(fb_content_post_id:' + req.param('user_id') + ') OR (fb_content_parent:' + req.param('user_id') + ')' } });
	} else {
		body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
	}
	var array = [];
	esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
		if (results.hits.total > 0) {
			results.hits.hits.forEach(function(e) {
				array.push({
					fanpage_id : e._source.fanpage_id,
					type : e._source.fb_content_type,
					post_id : e._source.fb_content_post_id,
					sender_id : e._source.fb_content_sender_id,
					parent : e._source.fb_content_parent,
					sender_view_id : e._source.fb_content_sender_view_id,
					recipient_id : e._source.fb_content_recipient_id,
					timestamp : e._source.fb_content_timestamp,
					attachment_type : e._source.fb_content_attachment_type,
					message : e._source.fb_content_message,
					read : e._source.fb_content_read,
					like : e._source.fb_content_like,
					deleted : e._source.fb_content_deleted,
					tags : e._source.tags,
				});
				esClient.update({
					index: 'facebook',
					type: 'webhook',
					id: e._id,
					body: {
						doc: {
							fb_content_read : 1
						}
					}
				}).catch(console.error);
			});
		}
		res.status(200).json(array);
		console.log(`io.emit get_message_user ${results.hits.total} items in ${results.took}ms`);
	}).catch(console.error);
});
app.get('/get_profile', function(req, res) {
	var con = mysql.createConnection(config);
	con.connect(function(err) {
		if (err) {
			console.log(err);
		} else {
			con.query("SELECT * FROM `nh_customer_fb` WHERE `cus_id` = '" + req.param('cus_id') + "' AND `user_fb_id` = '" + req.param('user_fb_id') + "'", function (err, result, fields) {
				if (err) {
					console.log(err);
				} else {
					var array = [];
					if (result.length > 0) {
						async.forEach(result, function(e, callback) {
							array = e;
							callback();
						}, function(err) {
							if(err) { throw err; };
							res.status(200).json(array);
						});
					} else {
						res.status(200).json(array);
					}
				}
			});
			con.end();
			console.log(req.param('user_fb_id') + ' GET /get_profile');
		}
	});
});
app.get('/get_profile_all', function(req, res) {
	var con = mysql.createConnection(config);
	con.connect(function(err) {
		if (err) {
			console.log(err);
		} else {
			con.query("SELECT * FROM `nh_customer_fb` WHERE `cus_id` = '" + req.param('cus_id') + "'", function (err, result, fields) {
				if (err) {
					console.log(err);
				} else {
					var array = [];
					if (result.length > 0) {
						async.forEach(result, function(e, callback) {
							array.push(e);
							callback();
						}, function(err) {
							if(err) { throw err; };
							res.status(200).json(array);
						});
					} else {
						res.status(200).json(array);
					}
				}
			});
			con.end();
			console.log(req.param('user_fb_id') + ' GET /get_profile');
		}
	});
});
app.get('/get_tags', function(req, res) {
	if (req.param('user_fb_id') != "") {
		var con = mysql.createConnection(config);
		con.connect(function(err) {
			if (err) {
				console.log(err);
			} else {
				con.query("SELECT * FROM `nh_customer_fb_tags` WHERE `cus_tags_deleted`='0' AND `user_fb_id`='" + req.param('user_fb_id') + "'", function (err, result, fields) {
					if (err) {
						console.log(err);
					} else {
						res.status(200).json(result);
					}
				});
				con.end();
				console.log(req.param('user_fb_id') + ' GET /get_tags');
			}
		});
	}
});
app.get('/user_fb_id', function(req, res) {
	if (req.param('key') != "") {
		var con = mysql.createConnection(config);
		con.connect(function(err) {
			if (err) {
				console.log(err);
			} else {
				con.query("SELECT `user_fb_id` FROM `nh_customer_fb` WHERE (`cus_fb_email` LIKE '%" + req.param('key') + "%' OR `cus_fb_phone` LIKE '%" + req.param('key') + "%' OR `cus_fb_address` LIKE '%" + req.param('key') + "%')", function (err, result, fields) {
					if (err) {
						console.log(err);
					} else {
						res.status(200).json(result);
					}
				});
				con.end();
				console.log(req.param('user_fb_id') + ' GET /user_fb_id');
			}
		});
	}
});
app.post('/set_profile', function(req, res) {
	var email = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var phone = /^\d{1,15}$/;
	var checked = true;
	if (req.param('name').length > 0 && (req.param('email').length > 255)) {
		checked = false;
	}
	if (req.param('email').length > 0 && (req.param('email').length > 75 || ! email.test(req.param('email')))) {
		checked = false;
	}
	if (req.param('phone').length > 0 && (req.param('phone').length > 75 || ! phone.test(req.param('phone')))) {
		checked = false;
	}
	if (req.param('address').length > 0 && (req.param('address').length > 75)) {
		checked = false;
	}
	if (req.param('note').length > 0 && (req.param('note').length > 75)) {
		checked = false;
	}
	if (checked) {
		var con = mysql.createConnection(config);
		con.connect(function(err) {
			if (err) {
				console.log(err);
			} else {
				con.query("SELECT 0 FROM `nh_customer_fb` WHERE `cus_id` = '" + req.param('cus_id') + "' AND `user_fb_id` = '" + req.param('user_fb_id') + "'", function (err, result, fields) {
					if (err) {
						console.log(err);
					} else {
						var con2 = mysql.createConnection(config);
						con2.connect(function(err) {
							if (err) {
								console.log(err);
							} else {
								if (result.length > 0) {
									con2.query("UPDATE `nh_customer_fb` SET `cus_fb_name`='" + req.param('name') + "', `cus_fb_email` = '" + req.param('email') + "', `cus_fb_phone` = '" + req.param('phone') + "', `cus_fb_address` = '" + req.param('address') + "', `cus_fb_note` = '" + req.param('note') + "', `cus_fb_time` = '" + new Date().getTime() + "' WHERE `cus_id` = '" + req.param('cus_id') + "' AND `user_fb_id` = '" + req.param('user_fb_id') + "'");
								} else {
									con2.query("INSERT INTO `nh_customer_fb` (`cus_id`, `user_fb_id`, `cus_fb_name`, `cus_fb_email`, `cus_fb_phone`, `cus_fb_address`, `cus_fb_note`, `cus_fb_time`) VALUES ('" + req.param('cus_id') + "', '" + req.param('user_fb_id') + "', '" + req.param('email') + "', '" + req.param('email') + "', '" + req.param('phone') + "', '" + req.param('address') + "', '" + req.param('note') + "', '" + new Date().getTime() + "')");
								}
								con2.end();
							}
						});
					}
				});
				con.end();
				
				if (req.param('phone').length > 0) {
					let body = {
						query : {
							bool : {
								must : [
									{ match : { fanpage_id : req.param('page_id') } },
									{ match : { fb_content_type : req.param('type') } },
									{ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_sender_view_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } }
								]
							}
						},
						sort : [
							{ fb_content_timestamp : { order : "asc" } }
						],
						size : 10000,
					};
					esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
						if (results.hits.total > 0) {
							results.hits.hits.forEach(function(e) {
								esClient.update({
									index: 'facebook',
									type: 'webhook',
									id: e._id,
									body: {
										doc: {
											phone : 1
										}
									}
								}).catch(console.error);
							});
						}
					}).catch(console.error);
				}
				
				console.log(req.param('user_fb_id') + ' POST /set_profile');
				res.status(200).json({ status : "success" });
			}
		});
	}
});
app.post('/set_tags', function(req, res) {
	if (req.param('tags_name') != "" && req.param('tags_name').length < 75) {
		var con = mysql.createConnection(config);
		con.connect(function(err) {
			if (err) {
				console.log(err);
			} else {
				con.query("SELECT 0 FROM `nh_customer_fb_tags` WHERE `cus_tags_name` = '" + req.param('tags_name') + "' AND `user_fb_id`='" + req.param('user_fb_id') + "'", function (err, result, fields) {
					if (err) {
						console.log(err);
					} else {
						if (result.length > 0) {
						} else {
							var con2 = mysql.createConnection(config);
							con2.connect(function(err) {
								if (err) {
									console.log(err);
								} else {
									con2.query("INSERT INTO `nh_customer_fb_tags` (`user_fb_id`, `cus_tags_name`, `cus_tags_time`) VALUES ('" + req.param('user_fb_id') + "', '" + req.param('tags_name') + "', '" + new Date().getTime() + "')");
									con2.end();
								}
							});
						}
					}
				});
				con.end();
				
				console.log(req.param('user_fb_id') + ' POST /set_tags');
				res.status(200).json({ status : "success" });
			}
		});
	}
});
app.post('/select_tags', function(req, res) {
	if (req.param('tags_id') != "") {
		let body = {
			query : {
				bool : {
					must : [
						{ match : { fanpage_id : req.param('page_id') } }
					]
				}
			},
			sort : [
				{ fb_content_timestamp : { order : "asc" } }
			],
			size : 10000,
        };
		if (req.param('type') == 1) {
			body.query.bool.must.push({ match : { fb_content_post_id : req.param('user_id') } });
		} else {
			body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
		}
		esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
			if (results.hits.total > 0) {
				results.hits.hits.forEach(function(e) {
					var tags = [];
					var checked = true;
					e._source.tags.forEach(function(tag) {
						if (parseInt(tag) == parseInt(req.param('tags_id'))) {
							checked = false;
						} else {
							if (parseInt(tag) > 0) {
								tags.push(tag);
							}
						}
					});
					if (checked) {
						tags.push(req.param('tags_id'));
					}
					esClient.update({
						index: 'facebook',
						type: 'webhook',
						id: e._id,
						body: {
							doc: {
								tags : tags
							}
						}
					}).catch(console.error);
				});
			}
			
			console.log(req.param('post_id') + ' POST /select_tags');
			res.status(200).json({ status : "success" });
		}).catch(console.error);
	}
});
app.post('/change_read_0', function(req, res) {
	if (req.param('page_id') != "") {
		let body = {
			query : {
				bool : {
					must : [
						{ match : { fanpage_id : req.param('page_id') } }
					]
				}
			},
			sort : [
				{ fb_content_timestamp : { order : "asc" } }
			],
			size : 10000,
		};
		if (req.param('type') == 1) {
			body.query.bool.must.push({ match : { fb_content_post_id : req.param('user_id') } });
		} else {
			body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
		}
		esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
			if (results.hits.total > 0) {
				results.hits.hits.forEach(function(e) {
					esClient.update({
						index: 'facebook',
						type: 'webhook',
						id: e._id,
						body: {
							doc: {
								fb_content_read : 0
							}
						}
					}).catch(console.error);
				});
			}
		}).catch(console.error);
		
		console.log(req.param('page_id') + ' POST /change_read_0');
		res.status(200).json({ status : "success" });
	}
});
app.post('/change_read_1', function(req, res) {
	if (req.param('page_id') != "") {
		let body = {
			query : {
				bool : {
					must : [
						{ match : { fanpage_id : req.param('page_id') } }
					]
				}
			},
			sort : [
				{ fb_content_timestamp : { order : "asc" } }
			],
			size : 10000,
		};
		if (req.param('type') == 1) {
			body.query.bool.must.push({ match : { fb_content_post_id : req.param('user_id') } });
		} else {
			body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
		}
		esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
			if (results.hits.total > 0) {
				results.hits.hits.forEach(function(e) {
					esClient.update({
						index: 'facebook',
						type: 'webhook',
						id: e._id,
						body: {
							doc: {
								fb_content_read : 1
							}
						}
					}).catch(console.error);
				});
			}
		}).catch(console.error);
		
		console.log(req.param('page_id') + ' POST /change_read_1');
		res.status(200).json({ status : "success" });
	}
});
app.post('/change_reply_1', function(req, res) {
	if (req.param('page_id') != "") {
		let body = {
			query : {
				bool : {
					must : [
						{ match : { fanpage_id : req.param('page_id') } }
					]
				}
			},
			sort : [
				{ fb_content_timestamp : { order : "asc" } }
			],
			size : 10000,
		};
		if (req.param('type') == 1) {
			body.query.bool.must.push({ match : { fb_content_post_id : req.param('user_id') } });
		} else {
			body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
		}
		esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
			if (results.hits.total > 0) {
				results.hits.hits.forEach(function(e) {
					esClient.update({
						index: 'facebook',
						type: 'webhook',
						id: e._id,
						body: {
							doc: {
								fb_content_reply : 1
							}
						}
					}).catch(console.error);
				});
			}
		}).catch(console.error);
		
		console.log(req.param('page_id') + ' POST /change_read_1');
		res.status(200).json({ status : "success" });
	}
});
app.post('/change_like_1', function(req, res) {
	if (req.param('page_id') != "") {
		let body = {
			query : {
				bool : {
					must : [
						{ match : { fanpage_id : req.param('page_id') } }
					]
				}
			},
			sort : [
				{ fb_content_timestamp : { order : "asc" } }
			],
			size : 10000,
		};
		if (req.param('type') == 1) {
			body.query.bool.must.push({ match : { fb_content_post_id : req.param('user_id') } });
		} else {
			body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
		}
		esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
			if (results.hits.total > 0) {
				results.hits.hits.forEach(function(e) {
					esClient.update({
						index: 'facebook',
						type: 'webhook',
						id: e._id,
						body: {
							doc: {
								fb_content_like : 1
							}
						}
					}).catch(console.error);
				});
			}
		}).catch(console.error);
		
		console.log(req.param('page_id') + ' POST /change_like_1');
		res.status(200).json({ status : "success" });
	}
});
app.post('/change_deleted_1', function(req, res) {
	if (req.param('page_id') != "") {
		let body = {
			query : {
				bool : {
					must : [
						{ match : { fanpage_id : req.param('page_id') } }
					]
				}
			},
			sort : [
				{ fb_content_timestamp : { order : "asc" } }
			],
			size : 10000,
		};
		if (req.param('type') == 1) {
			body.query.bool.must.push({ match : { fb_content_post_id : req.param('user_id') } });
		} else {
			body.query.bool.must.push({ query_string: { query: '(fb_content_sender_id:' + req.param('user_id') + ') OR (fb_content_recipient_id:' + req.param('user_id') + ')' } });
		}
		esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
			if (results.hits.total > 0) {
				results.hits.hits.forEach(function(e) {
					esClient.update({
						index: 'facebook',
						type: 'webhook',
						id: e._id,
						body: {
							doc: {
								fb_content_deleted : 1
							}
						}
					}).catch(console.error);
				});
			}
		}).catch(console.error);
		
		console.log(req.param('page_id') + ' POST /change_deleted_1');
		res.status(200).json({ status : "success" });
	}
});
app.post('/add_page_token', function(req, res) {
	var con = mysql.createConnection(config);	
	con.connect(function(err) {
		if (err) {
			console.log(err);
		} else {
			con.query("SELECT `id` FROM `nh_fanpage_token` WHERE `fanpage_id`='" + req.param('page_id') + "'", function (err, result, fields) {
				if (err) {
					console.log(err);
				} else {
					var con2 = mysql.createConnection(config);	
					con2.connect(function(err) {
						if (err) {
							console.log(err);
						} else {
							if (result.length > 0) {
								async.forEach(result, function(e, callback) {
									con2.query("UPDATE `nh_fanpage_token` SET `token`='" + req.param('token') + "', `time`='" + new Date().getTime() + "' WHERE (`id`='" + e.id + "')");
									callback();
								});
							} else {
								var arr = {};
								var body_from = [];
								var bulkBody = [];
								FB.setAccessToken(req.param('token'));
								FB.api(req.param('page_id') + "/feed", 'get', function (res) {
									if(!res || res.error) {
										throw (!res ? 'error occurred' : res.error);
									}
									if (res.data.length > 0) {
										res.data.forEach(function(data) {
											if (typeof data.id == "undefined") {
											} else {
												FB.api(data.id + "/comments", 'get', { fields: 'attachment,message,from,created_time' }, function (res) {
													if(!res || res.error) {
														throw (!res ? 'error occurred' : res.error);
													}
													if (res.data.length > 0) {
														res.data.forEach(function(dt) {
															body_from = (dt.from != undefined) ? ((dt.from.data != undefined) ? dt.from.data[0] : dt.from) : [];
															body_from.id = (body_from.id != undefined) ? body_from.id : 0;
															arr = {
																fanpage_id : req.param('page_id'),
																fb_content_type : 1,
																fb_content_post_id : data.id,
																fb_content_parent : 0,
																fb_content_sender_id : body_from.id,
																fb_content_sender_view_id : body_from.id,
																fb_content_recipient_id : 0,
																fb_content_attachment_type : 0,
																fb_content_message : "",
																fb_content_timestamp : new Date().getTime(dt.created_time),
																fb_content_read : 0,
																fb_content_reply : (req.param('page_id') == body_from.id) ? 1 : 0,
																fb_content_like : 0,
																fb_content_deleted : 0,
																phone : 0,
																tags : [],
															};
															if (typeof dt.attachment == "undefined") {
																arr.fb_content_attachment_type = 0;
																arr.fb_content_message = dt.message;
															} else {
																arr.fb_content_attachment_type = 1;
																arr.fb_content_message = dt.attachment.media.image.src;
															}
															if (arr.fb_content_message != "") {
																bulkBody = [];					
																bulkBody.push({
																	index: {
																		_index: 'facebook',
																		_type: 'webhook',
																	}
																});
																bulkBody.push(arr);
																esClient.bulk({body: bulkBody}).then(response => {
																	let errorCount = 0;
																	response.items.forEach(item => {
																		if (item.index && item.index.error) {
																			console.log(++errorCount, item.index.error);
																		}
																		console.log(`Successfully! Insert ` + item.create._id);
																	});
																}) .catch(console.err);
															}
														});
													}
												});
											}
										});
									}
								});
								FB.api(req.param('page_id') + "/conversations", 'get', function (res) {
									if(!res || res.error) {
										throw (!res ? 'error occurred' : res.error);
									}
									if (res.data.length > 0) {
										res.data.forEach(function(data) {
											if (typeof data.id == "undefined") {
											} else {
												FB.api(data.id + "/messages", 'get', {}, function (res) {
													if(!res || res.error) {
														throw (!res ? 'error occurred' : res.error);
													}
													if (res.data.length > 0) {
														res.data.forEach(function(dt) {
															if (typeof dt.id == "undefined") {
															} else {
																FB.api(dt.id, 'get', { fields: 'message,from,to,created_time,attachments' }, function (res) {
																	if(!res || res.error) {
																		throw (!res ? 'error occurred' : res.error);
																	}
																	body_from = (res.from != undefined) ? ((res.from.data != undefined) ? res.from.data[0] : res.from) : [];
																	body_from.id = (body_from.id != undefined) ? body_from.id : 0;
																	arr = {
																		fanpage_id : req.param('page_id'),
																		fb_content_type : 0,
																		fb_content_post_id : res.id,
																		fb_content_parent : 0,
																		fb_content_sender_id : body_from.id,
																		fb_content_sender_view_id : body_from.id,
																		fb_content_recipient_id : 0,
																		fb_content_attachment_type : 0,
																		fb_content_message : "",
																		fb_content_timestamp : new Date().getTime(res.created_time),
																		fb_content_read : 0,
																		fb_content_reply : (req.param('page_id') == body_from.id) ? 1 : 0,
																		fb_content_like : 0,
																		fb_content_deleted : 0,
																		phone : 0,
																		tags : [],
																	};
																	if (typeof res.attachment == "undefined") {
																		arr.fb_content_attachment_type = 0;
																		arr.fb_content_message = res.message;
																	} else {
																		arr.fb_content_attachment_type = 1;
																		arr.fb_content_message = res.attachment.media.image.src;
																	}
																	if (arr.fb_content_message != "") {
																		let bulkBody = [];					
																		bulkBody.push({
																			index: {
																				_index: 'facebook',
																				_type: 'webhook',
																			}
																		});
																		bulkBody.push(arr);
																		esClient.bulk({body: bulkBody}).then(response => {
																			let errorCount = 0;
																			response.items.forEach(item => {
																				if (item.index && item.index.error) {
																					console.log(++errorCount, item.index.error);
																				}
																				console.log(`Successfully! Insert ` + item.create._id);
																			});
																		}) .catch(console.err);
																	}
																});
															}
														});
													}
												});
											}
										});
									}
								});
								con2.query("INSERT INTO `nh_fanpage_token` (`fanpage_id`, `token`, `time`) VALUES ('" + req.param('page_id') + "', '" + req.param('token') + "', '" + new Date().getTime() + "')");
							}
							con2.end();
						}
					});
				}
			});
			con.end();
			res.status(200).json({ status : "success" });
		}
	});
});
app.post('/add_keywords', function(req, res) {
	var con = mysql.createConnection(config);	
	con.connect(function(err) {
		if (err) {
			console.log(err);
		} else {
			con.query("SELECT `id` FROM `nh_keywords_hide` WHERE (`fanpage_id`='" + req.param('fanpage') + "' AND `keywords`='" + req.param('keywords') + "') LIMIT 1", function (err, result, fields) {
				if (err) {
					console.log(err);
				} else {
					var con2 = mysql.createConnection(config);	
					con2.connect(function(err) {
						if (err) {
							console.log(err);
						} else {
							if (result.length <= 0) {
								con2.query("INSERT INTO `nh_keywords_hide` (`fanpage_id`, `keywords`, `time`) VALUES ('" + req.param('fanpage') + "', '" + req.param('keywords') + "', '" + new Date().getTime() + "')");
							}
							con2.end();
						}
					});
				}
			});
			con.end();
		}
	});
	res.status(200).json({ status : "success" });
});
app.post('/get_keywords', function(req, res) {
	var con = mysql.createConnection(config);
	con.query("SELECT DISTINCT `keywords` FROM `nh_keywords_hide` WHERE (`fanpage_id`='" + req.param('fanpage') + "')", function (err, result, fields) {
		if (err) {
			console.log(err);
		} else {
			var array = [];
			if (result.length > 0) {
				async.forEach(result, function(e, callback) {
					array.push(e['keywords']);
					callback();
				}, function(err) {
					if(err) { throw err; };
					res.status(200).json(array);
				});
			} else {
				res.status(200).json(array);
			}
		}
	});
	con.end();
});
app.post('/del_keywords', function(req, res) {
	var con = mysql.createConnection(config);
	con.query("DELETE FROM `nh_keywords_hide` WHERE (`fanpage_id`='" + req.param('fanpage') + "' AND `keywords` = '" + req.param('key') + "')", function (err, result, fields) {
		if (err) {
			console.log(err);
		} else {
			res.status(200).json({ status : "success" });
		}
	});
	con.end();
});
app.post('/set_config', function(req, res) {
	var con = mysql.createConnection(config);	
	con.connect(function(err) {
		if (err) {
			console.log(err);
		} else {
			con.query("SELECT `id` FROM `nh_fanpage_config` WHERE (`fanpage_id`='" + req.param('fanpage') + "' AND `key`='" + req.param('key') + "') LIMIT 1", function (err, result, fields) {
				if (err) {
					console.log(err);
				} else {
					var con2 = mysql.createConnection(config);	
					con2.connect(function(err) {
						if (err) {
							console.log(err);
						} else {
							if (result.length > 0) {
								async.forEach(result, function(e, callback) {
									con2.query("UPDATE `nh_fanpage_config` SET `value`='" + req.param('value') + "', `time`='" + new Date().getTime() + "' WHERE (`id`='" + e.id + "')");
									callback();
								});
							} else {
								con2.query("INSERT INTO `nh_fanpage_config` (`fanpage_id`, `key`, `value`, `time`) VALUES ('" + req.param('fanpage') + "', '" + req.param('key') + "', '" + req.param('value') + "', '" + new Date().getTime() + "')");
							}
							con2.end();
						}
					});
				}
			});
			con.end();
		}
	});
	res.status(200).json({ status : "success" });
});
app.post('/get_config', function(req, res) {
	var con = mysql.createConnection(config);
	con.query("SELECT DISTINCT `value` FROM `nh_fanpage_config` WHERE (`fanpage_id`='" + req.param('fanpage') + "' AND `key`='" + req.param('key') + "')", function (err, result, fields) {
		if (err) {
			console.log(err);
		} else {
			var array = [];
			if (result.length > 0) {
				async.forEach(result, function(e, callback) {
					array.push(e['value']);
					callback();
				}, function(err) {
					if(err) { throw err; };
					res.status(200).json(array);
				});
			} else {
				res.status(200).json(array);
			}
		}
	});
	con.end();
});
io.on('connection', socketioJwt.authorize({
    secret: process.env.jwt_token,
    timeout: 60*60*1000
}))
.on('authenticated', function(socket) {
	var page_id = socket.decoded_token.page_id;
	console.log(page_id + ' connection');
	socket.on('disconnect', function() {
		console.log(page_id + ' disconnected');
		page_id = null;
	});
});

/*** facebook ***/
app.get('/webhook', function(req, res) {
	if (req.query['hub.mode'] === 'subscribe' &&
		req.query['hub.verify_token'] === process.env.verify_token) {
		console.log("Validating webhook");
		res.status(200).send(req.query['hub.challenge']);
	} else {
		console.error("Failed validation webhook");
		res.sendStatus(403);          
	}
});
app.get('/webhook', function(req, res) {
	if (req.query['hub.mode'] === 'subscribe' &&
		req.query['hub.verify_token'] === process.env.verify_token) {
		console.log("Validating webhook");
		res.status(200).send(req.query['hub.challenge']);
	} else {
		console.error("Failed validation webhook");
		res.sendStatus(403);          
	}
});
app.post('/webhook', function (req, res) {
	console.log('DD/MM/YYYY hh:mm:ss'.timestamp);
	var data = req.body;
	if (data.object === 'page') {
		if (data.entry != undefined) {
			var arr_facebook = {};
			data.entry.forEach(function(entry) {
				arr_facebook = {
					"fanpage_id" : entry.id,
					"fb_content_type" : null,
					"fb_content_post_id" : null,
					"fb_content_parent" : null,
					"fb_content_sender_id" : null,
					"fb_content_sender_view_id" : null,
					"fb_content_recipient_id" : null,
					"fb_content_attachment_type" : null,
					"fb_content_message" : "",
					"fb_content_timestamp" : new Date().getTime(),
					"fb_content_read" : 0,
					"fb_content_reply" : 0,
					"fb_content_like" : 0,
					"fb_content_deleted" : 0,
					"phone" : 0,
					"tags" : [],
				};	
				if (entry.messaging != undefined) {
					// entry.messaging.forEach(function(item) {
						// console.log(item);
					// });
					arr_facebook["fb_content_type"] = 0;
					arr_facebook["fb_content_sender_id"] = entry.messaging[0].sender.id > 0 ? entry.messaging[0].sender.id : 0;
					arr_facebook["fb_content_recipient_id"] = entry.messaging[0].recipient.id > 0 ? entry.messaging[0].recipient.id : 0;
					if (entry.messaging[0].message != undefined) {
						arr_facebook["fb_content_post_id"] = entry.messaging[0].message.mid == undefined ? "" : 'm_' + entry.messaging[0].message.mid;
						arr_facebook["fb_content_parent"] = 0;
						if (entry.messaging[0].message.attachments != undefined && entry.messaging[0].message.attachments[0].payload != undefined) {
							arr_facebook["fb_content_attachment_type"] = 1
							arr_facebook["fb_content_message"] = entry.messaging[0].message.attachments[0].payload.url == undefined ? "" : entry.messaging[0].message.attachments[0].payload.url;
						} else {
							arr_facebook["fb_content_attachment_type"] = 0
							arr_facebook["fb_content_message"] = entry.messaging[0].message.text == undefined ? "" : entry.messaging[0].message.text;
						}
					}
				} else if (entry.changes != undefined) {
					// entry.changes.forEach(function(item) {
						// console.log(item);
					// });
					arr_facebook["fb_content_type"] = 1;
					if (entry.changes[0].value != undefined) {
						arr_facebook["fb_content_sender_id"] = entry.changes[0].value.sender_id > 0 ? entry.changes[0].value.sender_id : 0;
						arr_facebook["fb_content_recipient_id"] = 0;
						arr_facebook["fb_content_post_id"] = entry.changes[0].value.comment_id == undefined ? 0 : entry.changes[0].value.comment_id;
						arr_facebook["fb_content_parent"] = entry.changes[0].value.parent_id == undefined ? 0 : entry.changes[0].value.parent_id;
						if (entry.changes[0].value.photo != undefined) {
							arr_facebook["fb_content_attachment_type"] = 1
							arr_facebook["fb_content_message"] = entry.changes[0].value.photo;
						} else {
							arr_facebook["fb_content_attachment_type"] = 0
							arr_facebook["fb_content_message"] = entry.changes[0].value.message == undefined ? "" : entry.changes[0].value.message;
						}
					}
				} else {
					console.log("entry messaging || changes undefined");
				}
				if (arr_facebook["fb_content_attachment_type"] == 1) {
					require('https').get(arr_facebook["fb_content_message"], res => {
						res.once('data', chunk => {
							res.destroy();
							imageDownloader.image({
								url: arr_facebook["fb_content_message"],
								dest: "../cdn/" + arr_facebook["fb_content_post_id"].split(".").pop() + "." + imageType(chunk)["ext"]
							})
							.then(({ filename, image }) => {
								arr_facebook["fb_content_message"] = filename.replace("../", "https://hooks.3f.team/");
								webhookFacebook(arr_facebook);
							});
						});
					});
				} else {
					webhookFacebook(arr_facebook);
				}
			});
		} else {
			console.log('data entry undefined');
		}
		res.sendStatus(200);
	} else {
		console.log(data.object);
	}
});
function webhookFacebook(arr = null) {
	if (arr["fanpage_id"] != "" && arr["fb_content_message"] != "" && arr["fb_content_post_id"] != undefined) {
		var con = mysql.createConnection(config);
		con.connect(function(err) {
			if (err) {
				console.log(err);
			} else {
				con.query("SELECT `token` FROM `nh_fanpage_token` WHERE `fanpage_id`='" + arr["fanpage_id"] + "'", function (err, result, fields) {
					if (err) {
						console.log(err);
					} else {
						if (result.length > 0) {
							async.forEach(result, function(e, callback) {
								if (arr["fb_content_type"] == 1) {
									var con2 = mysql.createConnection(config);
									con2.query("SELECT DISTINCT `keywords` FROM `nh_keywords_hide` WHERE (`fanpage_id` = '" + arr["fanpage_id"] + "')", function (err2, result2, fields2) {
										if (err2) {
											console.log(err2);
										} else {
											var array = [];
											if (result2.length > 0) {
												async.forEach(result2, function(e2, callback2) {
													if (arr["fb_content_message"].indexOf(e2.keywords) > -1) {
														request({
															url: "https://graph.facebook.com/" + arr["fb_content_post_id"],
															qs: {access_token : e.token},
															method: 'POST',
															json: {is_hidden : true}
														}, function(error, response, body) {
															if (error) {
																console.log('Error: ', error);
															} else if (response.body.error) {
																console.log('Error: ', response.body.error);
															} else {
																console.log(body);
															}
														});
													}
													callback2();
												}, function(err) {
													if(err) { throw err; };
												});
											}
										}
									});
									con2.end();
									var con3 = mysql.createConnection(config);
									con3.query("SELECT DISTINCT `value` FROM `nh_fanpage_config` WHERE (`fanpage_id` = '" + arr["fanpage_id"] + "' AND `key`='auto_like_comment')", function (err3, result3, fields3) {
										if (err3) {
											console.log(err3);
										} else {
											var array = [];
											if (result3.length > 0) {
												async.forEach(result3, function(e3, callback3) {
													if (e3.value == 1) {
														request({
															url: "https://graph.facebook.com/" + arr["fb_content_post_id"] + "/likes",
															qs: {access_token : e.token},
															method: 'POST',
															json: {}
														}, function(error, response, body) {
															if (error) {
																console.log('Error: ', error);
															} else if (response.body.error) {
																console.log('Error: ', response.body.error);
															} else {
																console.log(body);
															}
														});
													}
													callback3();
												}, function(err) {
													if(err) { throw err; };
												});
											}
										}
									});
									con3.end();
									var con4 = mysql.createConnection(config);
									con4.query("SELECT DISTINCT `value` FROM `nh_fanpage_config` WHERE (`fanpage_id` = '" + arr["fanpage_id"] + "' AND `key`='auto_hide_phone')", function (err4, result4, fields4) {
										if (err4) {
											console.log(err4);
										} else {
											var array = [];
											if (result4.length > 0) {
												async.forEach(result4, function(e4, callback4) {
													if (e4.value == 1) {
														if (/^.*[0]{1}\d{7,11}.*$/.test(arr["fb_content_message"])) {
															request({
																url: "https://graph.facebook.com/" + arr["fb_content_post_id"],
																qs: {access_token : e.token},
																method: 'POST',
																json: {is_hidden : true}
															}, function(error, response, body) {
																if (error) {
																	console.log('Error: ', error);
																} else if (response.body.error) {
																	console.log('Error: ', response.body.error);
																} else {
																	console.log(body);
																}
															});
														}
													}
													callback4();
												}, function(err) {
													if(err) { throw err; };
												});
											}
										}
									});
									con4.end();
								}
								request({
									url: "https://graph.facebook.com/" + arr["fb_content_post_id"] + "?fields=message,from",
									qs: {access_token : e.token},
									method: 'GET',
									json: {}
								}, function(error, response, body) {
									if (error) {
										console.log('Error: ', error);
									} else if (response.body.error) {
										console.log('Error: ', response.body.error);
									} else {
										var body_from = (body.from != undefined) ? ((body.from.data != undefined) ? body.from.data[0] : body.from) : new Array();
										io.emit('facebook_webhook_message', {
											fanpage_id : arr["fanpage_id"],
											type : arr["fb_content_type"],
											post_id : arr["fb_content_post_id"],
											user_id : arr["fb_content_sender_id"],
											parent : arr["fb_content_parent"],
											sender_view_id : (body_from.id != undefined) ? body_from.id : 0,
											recipient_id : arr["fb_content_recipient_id"],
											attachment_type : arr["fb_content_attachment_type"],
											user_message : arr["fb_content_message"],
											time : arr["fb_content_timestamp"],
											read : 0,
											reply : (arr["fanpage_id"] == arr["fb_content_sender_id"]) ? 1 : 0,
											like : 0,
											phone : 0,
											tags : arr["tags"],
										});
										console.log(`io.facebook_webhook ${arr.fb_content_post_id}`);
										io.emit('facebook_webhook_message_user', {
											fanpage_id : arr["fanpage_id"],
											type : arr["fb_content_type"],
											post_id : arr["fb_content_post_id"],
											sender_id : arr["fb_content_sender_id"],
											parent : arr["fb_content_parent"],
											sender_view_id : (body_from.id != undefined) ? body_from.id : 0,
											recipient_id : arr["fb_content_recipient_id"],
											timestamp : arr["fb_content_timestamp"],
											attachment_type : arr["fb_content_attachment_type"],
											message : arr["fb_content_message"],
											read : 0,
											reply : (arr["fanpage_id"] == arr["fb_content_sender_id"]) ? 1 : 0,
											like : 0,
											phone : 0,
											tags : arr["tags"],
										});
										console.log(`io.facebook_webhook_message_user ${arr["fb_content_post_id"]}`);
										if (arr["fb_content_message"] != "") {
											let bulkBody = [];					
											bulkBody.push({
												index: {
													_index: 'facebook',
													_type: 'webhook',
												}
											});
											bulkBody.push({
												fanpage_id : arr["fanpage_id"],
												fb_content_type : arr["fb_content_type"],
												fb_content_post_id : arr["fb_content_post_id"],
												fb_content_parent : arr["fb_content_parent"],
												fb_content_sender_id : arr["fb_content_sender_id"],
												fb_content_sender_view_id : (body_from.id != undefined) ? body_from.id : 0,
												fb_content_recipient_id : arr["fb_content_recipient_id"],
												fb_content_attachment_type : arr["fb_content_attachment_type"],
												fb_content_message : arr["fb_content_message"],
												fb_content_timestamp : arr["fb_content_timestamp"],
												fb_content_read : 0,
												fb_content_reply : (arr["fanpage_id"] == arr["fb_content_sender_id"]) ? 1 : 0,
												fb_content_like : 0,
												fb_content_deleted : 0,
												phone : 0,
												tags : arr["tags"],
											});
											esClient.bulk({body: bulkBody}).then(response => {
												let errorCount = 0;
												response.items.forEach(item => {
													if (item.index && item.index.error) {
														console.log(++errorCount, item.index.error);
													}
													console.log(`Successfully! Insert ` + item.create._id);
												});
											}) .catch(console.err);
										}
									}
								});
								callback();
							});
						}
					}
				});
				con.end();
			}
		});
	}
}


/*** server ***/
http.listen(process.env.PORT, process.env.ADDRESS, function() {
	console.log('listening on: ' + process.env.PORT);
});