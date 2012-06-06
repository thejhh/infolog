/* Main code */

/* RequireJS configurations */
requirejs.config({
	shim: {
		'bootstrap':{
			deps: ['jquery']
		},
		'showdown':{
			deps: [],
			exports: 'Showdown'
		}
	}
});

require(["jquery", "moment", "bootstrap", "showdown", "jquery.cookies"], function(jquery, moment, bootstrap, Showdown, cookies) {

	var SERVER_CONFIG = {};
	var INFODESK_GLOBAL = {};

	/* Pop error message */
	function add_error(args) {
		//alert('error: '+ JSON.stringify(args));
		var data, dialog;
		if(args && (typeof args === 'object')) {
			if(args.error) {
				args.title = args.error;
				delete args.error;
			}
			if(args.title || args.desc) {
				data = args;
			} else {
				data = {'desc': JSON.stringify(args)};
			}
		} else {
			data = {'title':''+args};
		}
		if(!data.date) data.date = new Date();
		dialog = jquery('#elements .error_dialog').clone();
		dialog.find('.date').text(data.date);
		dialog.find('.title').text(data.title);
		if(data.desc && ((data.desc+'').length > 0) ) {
			dialog.find('.desc').text(data.desc);
		} else {
			dialog.find('.desc_container').hide();
		}
		dialog.prependTo('#notifications');
		dialog.alert();
	};
	
	/* Post message to server */
	function remove_msg(log_id, success_fn) {
		var jqxhr = jquery.post('backend.php', {'remove_msg':log_id});
		jqxhr.complete(function(response) {
			try {
				if(response && response.status && (200 === response.status) && (response.responseText.substr(0, 2) === 'OK') ) {
					success_fn();
				} else if(response && (response.status !== undefined)) {
					add_error({'title':'Connection failed with #' + response.status, 'desc':response.responseText});
				} else {
					add_error('Connection failed');
				}
			} catch(e) {
				add_error('Connection failed');
			}
		});
	}

	/* Post message to server */
	function post_msg(args) {
		//alert('In post_msg() with args=' + JSON.stringify(args) );
		var args = args || {};
		var msg = (args && (typeof args === 'object') && args.msg) ? ''+args.msg : '';
		if( (msg.length === 0) || (msg.length >= SERVER_CONFIG.MAX_MSG_LENGTH) ) {
			return;
		}
		var jqxhr = jquery.post('backend.php', {'send_msg':'1', 'msg':''+msg});
		jqxhr.complete(function(response) {
			try {
				if(response && response.status && (200 === response.status) && (response.responseText.substr(0, 2) === 'OK') ) {
					jquery("#control_form .msg_field").val('');
					update_events();
				} else if(response && (response.status !== undefined)) {
					add_error({'title':'Connection failed with #' + response.status, 'desc':response.responseText});
				} else {
					add_error('Connection failed');
				}
			} catch(e) {
				add_error('Connection failed');
			}
		});
	}

	/* Post message to server */
	function post_msg_form() {
		var msg = jquery('#control_form').find('.msg_field').val();
		//alert('Calling post_msg() with msg=' + msg);
		post_msg({'msg':msg});
		return false;
	}

	/* */
	function change_search_string(q) {
	
		if(INFODESK_GLOBAL.updating === true) { return; }
	
		//alert(hashtag);
		INFODESK_GLOBAL.search_string = ''+q;
		jquery('#events .events-header').empty();
		jquery('#events .events-body').empty();
		if(q !== '') {
			var header = jquery('<h3 />').append('Results for ').append(format_msg(''+q)).append(' ');
			var link = jquery('<a class="btn small"/>').html("Close &times;").click(function(event){
				event.preventDefault();
				change_search_string('');
				return false;
			});
			link.appendTo(header);
			header.appendTo('#events .events-header');
		}
		jquery('#search_field').val('');
		INFODESK_GLOBAL.last_id = 0;
		update_events();
	}

	/* */
	function format_msg(msg) {
		msg = jquery('<div/>').text(msg).html();
		
		// Format normal links
		msg = msg.replace(/(https?|ftp):\/\/([^\s]+)/g, function($0, $1, $2) {
			var link = ''+$1+'://'+$2;
			var div = jquery('<div/>');
			var a = jquery('<a class="label" target="_blank" />').attr('href', link).text($2);
			a.appendTo(div);
			return div.html();
		});
	
		// Format hashtags
		msg = msg.replace(/#([a-zA-Zäöå0-9\._]+)/g, function($0, $1) {
			var h = (''+$1).toLowerCase();
			var div = jquery('<div/>');
			var a = jquery('<a href="#" class="hashtag_link label label-info" />').text('#'+$1);
			a.appendTo(div);
			return div.html();
		});

		// Format @-links
		msg = msg.replace(/@([a-zA-Z0-9\._]+)/g, function($0, $1) {
			var h = (''+$1).toLowerCase();
			var div = jquery('<div/>');
			var a = jquery('<a href="http://'+h+'.'+SERVER_CONFIG.TOP_DOMAIN+'" class="label label-success" />').text('@'+$1);
			a.appendTo(div);
			return div.html();
		});

		var elem = jquery('<span/>').html(msg);
		
		// Hashtag click events
		elem.find('.hashtag_link').click(function(e) {
			e.preventDefault();
			change_search_string( jquery(this).text() );
			return false;
		});

		return elem.contents();
	}

	/* */
	function update_events() {
		var next_id, hashtag;
		if(INFODESK_GLOBAL.updating === true) { return; }
		INFODESK_GLOBAL.updating = true;
		next_id = INFODESK_GLOBAL.last_id+1;
		var options = {'msgs':'1', 'start':''+next_id};
		if(INFODESK_GLOBAL.search_string && (INFODESK_GLOBAL.search_string !== '')) {
			options.q = INFODESK_GLOBAL.search_string;
		}
		var jqxhr = jquery.get('backend.php', options, function(data) {
			var response = JSON.parse(data),
			    events = response.events,
			    server_time = response.time,
			    our_user_id = response.user_id;
		
			//alert('got events: ' + events.length);
			for(i in events) if(events.hasOwnProperty(i)) {
				(function() {
					var event = events[i],
					    id = parseInt(event.log_id, 10),
					    seconds_since = server_time - event.updated,
					    updated = new Date();
						
						updated.setTime( event.updated*1000 );

					if(id > INFODESK_GLOBAL.last_id) {
						INFODESK_GLOBAL.last_id = id;
					}
					
					var div = jquery('#elements .event_container').clone();
					div.find('.log_id').text(''+event.log_id);
					div.find('.date').text( moment(updated).format("HH:mm:ss") );
					div.find('.msg').append( format_msg(event.msg) );
					
					var close = div.find('.close');
					if ( (our_user_id === event.user_id) && (seconds_since < 5*60) ) {
						//alert("seconds_since = " + seconds_since);
						close.show();
						setTimeout(function() {
							close.hide();
						}, (5*60 - seconds_since)*1000);
					} else {
						close.hide();
					}
					
					close.click(function(e) {
						e.preventDefault();
						remove_msg(event.log_id, function() {
							div.remove();
						});
						return false;
					});
					
					div.prependTo('#events .events-body');
				}());
			}
			INFODESK_GLOBAL.updating = false;
		});

		jqxhr.complete(function(data, status) {
			if(status === "success") return;
			if(data && data.responseText) {
				try {
					var obj = JSON.parse(data.responseText);
					if(obj && obj.error) {
						add_error(obj.error, jquery);
						return;
					}
					add_error(obj);
				} catch(e) {
					add_error(data);
				}
			} else {
				add_error(status + ' with ' + JSON.stringify(data));
			}
		});
	}

	/* */
	function update_clock() {
		function f(d) { return ((''+d).length===1) ? '0'+d : ''+d; }
		var now = new Date();
		jquery('#clock').val( f(now.getHours()) + ':' + f(now.getMinutes()) );
	}

	/* */
	INFODESK_GLOBAL.timer = undefined;
	INFODESK_GLOBAL.updating = false;
	INFODESK_GLOBAL.last_id = 0;
	function update_events_timer() {
		update_events();
		//update_clock();
		setTimeout(update_events_timer, 1000);
	}

	function get_config(fn) {

		if(SERVER_CONFIG.time) {
			return fn(SERVER_CONFIG);
		}

		// Setup SERVER_CONFIG
		jquery.getJSON('api/config.json', function(data){
			jquery.each(data, function(key, val) {
				SERVER_CONFIG[key] = val;
			});
			fn(SERVER_CONFIG);
		}).error(function() {
			add_error("Failed to load server config");
		});
	}

	/* Init everything at onLoad event */
	window.onload = function() {
	
		// Setup ajax calls
		jquery.ajaxSetup({cache:false});
		
		get_config(function(config) {

			// Setup auth dialog
			jquery("#setup_auth").submit(function(event) {
				// Optional global auth cookie authentication
				if(config.require_global_auth_cookie) {
					var pw = prompt("Please enter AUTH-KEY","");
					var expires = new Date();
					expires.setTime( expires.getTime() + 10*365*24*60*60*1000 );
					$.cookies.set(config.GLOBAL_AUTH_COOKIE_NAME, pw, { expires: expires, path:'/', domain:config.COOKIE_DOMAIN });
				} else {
					alert("Server doesn't use authentication.");
				}
				return false;
			});

			// TODO: Setup simple clock on control form
			
			// TODO: Setup previous event history
			// TODO: Start fetching new events
			//update_events();
			update_events_timer();
		
			// Setup search form
			jquery('.form-search').submit(function() {
				var q = jquery('#search_field').val();
				change_search_string(q);
				return false;
			});
		
			// Setup about modal's body
			jquery.get('README.md', function(data) {
				var converter = new Showdown.converter();
				jquery('#about .modal-body').html(converter.makeHtml(data));
			});
		
			// Focus on message field
			jquery('#control_form .msg_field').focus();
	
			// Update form message size
			var form = jquery('#control_form');
			var field = form.find('.msg_field');
			var field_help = form.find('.msg_field_help');
			var field_max = parseInt(field.attr("maxlength"), 10);
			field.removeAttr("maxlength");
			function field_update() {
				var len = field.val().length;
				if(len < field_max) {
					// Success
					field_help.show();
					if(!form.hasClass('success')) form.toggleClass('success');
					if(form.hasClass('error')) form.toggleClass('error');
					field_help.text('' + (field_max-len));
				} else {
					// Error
					field_help.show();
					if(form.hasClass('success')) form.toggleClass('success');
					if(!form.hasClass('error')) form.toggleClass('error');
					field_help.text('' + (field_max-len));
				}
			}
			field.keydown(field_update);
			field.keyup(field_update);
			field.change(field_update);
			
			// Setup send message button
			jquery("#controls .submit-btn").removeAttr('disabled').click(function(event){
				event.preventDefault();
				post_msg_form();
				return false;
			});
			
			// Setup new channel form
			jquery("#join_channel").submit(function(event) {
				var channel = jquery(this).find('.channel_field').val();
				if(channel.match(/^[a-zA-Z0-9\._]+$/)) {
					window.location = 'http://' + channel + '.' + SERVER_CONFIG.TOP_DOMAIN;
				} else {
					add_error("Channel name invalid: " + channel);
				}
				return false;
			});

		});
	};

// End of require
}, function(err) {
	alert('Error: ' . err);
});
/* EOF */
