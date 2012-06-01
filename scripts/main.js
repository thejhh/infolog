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

var INFODESK_GLOBAL = {};

/* Pop error message */
function add_error(args, jquery) {
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
	function do_jquery(jquery) {
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
	}
	if(jquery) {
		do_jquery(jquery);
	} else {
		require(["jquery"], function(jquery) {
			do_jquery(jquery);
		}, function(err) {
			alert('Error: ' . err);
		});
	}
};

/* Post message to server */
function remove_msg(log_id, success_fn) {
		var jqxhr;
		require(["jquery"], function(jquery) {
			jqxhr = jquery.post('backend.php', {'remove_msg':log_id});
		}, function(err) { add_error(JSON.stringify(err)); });
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
		if( (msg.length === 0) || (msg.length >= 1024) ) {
			return;
		}
		var jqxhr;
		require(["jquery"], function(jquery) {
			jqxhr = jquery.post('backend.php', {'send_msg':'1', 'msg':''+msg});
		}, function(err) { add_error(JSON.stringify(err)); });
		jqxhr.complete(function(response) {
			try {
				if(response && response.status && (200 === response.status) && (response.responseText.substr(0, 2) === 'OK') ) {
					require(["jquery"], function(jquery) {
						jquery("#control_form .msg_field").val('');
					}, function(err) { add_error(JSON.stringify(err)); });
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
	/*
	require(["jquery"], function(jquery) {
	}, function(err) {
		add_error({'title':'Connection failed', 'desc':JSON.stringify(err)});
	});
	*/
}

/* Post message to server */
function post_msg_form() {
	var msg;
	require(["jquery"], function(jquery) {
		msg = jquery('#control_form').find('.msg_field').val();
	}, function(err) {
		add_error({'title':'Clearing form failed', 'desc':JSON.stringify(err)});
	});
	//alert('Calling post_msg() with msg=' + msg);
	post_msg({'msg':msg});
	return false;
}

/* */
function change_search_string(q) {
	
	if(INFODESK_GLOBAL.updating === true) { return; }
	
	//alert(hashtag);
	INFODESK_GLOBAL.search_string = ''+q;
	require(["jquery"], function(jquery) {
		jquery('#events .events-header').empty();
		jquery('#events .events-body').empty();
		if(q !== '') {
			var header = jquery('<h3 />').html( 'Results for '+format_msg(jquery, ''+q) + ' ' );
			var link = jquery('<a class="btn small"/>').html("Close &times;").click(function(){ change_search_string(''); });
			link.appendTo(header);
			header.appendTo('#events .events-header');
		}
		jquery('#search_field').val('');
	}, function(err) { add_error(JSON.stringify(err)); });
	INFODESK_GLOBAL.last_id = 0;
	update_events();
}

/* */
function format_msg(jquery, msg) {
	msg = jquery('<div/>').text(msg).html();
	
	// Format hashtags
	msg = msg.replace(/#([a-zA-Z0-9\.]+)/g, function($0, $1) {
		var h = (''+$1).toLowerCase();
		var div = jquery('<div/>');
		var a = jquery('<a href="javascript:change_search_string(\'#' + escape(h) + '\')" class="label label-info"></a>').text('#'+$1);
		a.appendTo(div);
		return div.html();
	});
	
	return msg;
}

/* */
function update_events() {
	var next_id, hashtag;
	if(INFODESK_GLOBAL.updating === true) { return; }
	INFODESK_GLOBAL.updating = true;
	next_id = INFODESK_GLOBAL.last_id+1;
	require(["jquery", "moment"], function(jquery, moment) {
		var options = {'msgs':'1', 'start':''+next_id};
		if(INFODESK_GLOBAL.search_string && (INFODESK_GLOBAL.search_string !== '')) {
			options.q = INFODESK_GLOBAL.search_string;
		}
		var jqxhr = jquery.get('backend.php', options, function(data) {
			var response = JSON.parse(data),
			    events = response.events,
			    server_time = response.time;
			
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
					div.find('.msg').html( format_msg(jquery, event.msg) );

					var close = div.find('.close');
					if(seconds_since < 5*60) {
						//alert("seconds_since = " + seconds_since);
						close.show();
						setTimeout(function() {
							close.hide();
						}, (5*60 - seconds_since)*1000);
					} else {
						close.hide();
					}

					close.click(function() {
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
				add_error(status + ' with ' + data);
			}
		});
	}, function(err) { add_error(JSON.stringify(err)); });
}

/* */
function update_clock() {
	function f(d) { return ((''+d).length===1) ? '0'+d : ''+d; }
	require(["jquery"], function(jquery) {
		var now = new Date();
		jquery('#clock').val( f(now.getHours()) + ':' + f(now.getMinutes()) );
	});
}

/* */
INFODESK_GLOBAL.timer = undefined;
INFODESK_GLOBAL.updating = false;
INFODESK_GLOBAL.last_id = 0;
function update_events_timer() {
	update_events();
	//update_clock();
	setTimeout('update_events_timer()', 1000);
}

/* Init everything at onLoad event */
window.onload = function() {
	require(["bootstrap"], function(b) {});
	
	// TODO: Setup simple clock on control form
		
	// TODO: Setup previous event history
	// TODO: Start fetching new events
	//update_events();
	update_events_timer();
	
	// 
	require(['jquery'], function(jquery) {
		
		// Setup ajax calls
		jquery.ajaxSetup({cache:false});
		
		// Setup search form
		jquery('.form-search').submit(function() {
			var q = jquery('#search_field').val();
			change_search_string(q);
			return false;
		});
		
		// Setup about modal's body
		require(['showdown'], function(Showdown) {
			jquery.get('README.md', function(data) {
				var converter = new Showdown.converter();
				jquery('#about .modal-body').html(converter.makeHtml(data));
			});
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
	});
};

/* EOF */
