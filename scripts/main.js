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
function add_error(args) {
	//alert('error: '+ JSON.stringify(args));
	var data, dialog;
	if(args && (typeof args === 'object')) {
		if(args.title || args.desc) {
			data = args;
		} else {
			data = {'desc': JSON.stringify(args)};
		}
	} else {
		data = {'title':''+args};
	}
	if(!data.date) data.date = new Date();
	require(["jquery"], function(jquery) {
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
	}, function(err) {
		alert('Error: ' . err);
	});
};

/* Post message to server */
function post_msg(args) {
		//alert('In post_msg() with args=' + JSON.stringify(args) );
		var args = args || {};
		var msg = (args && (typeof args === 'object') && args.msg) ? ''+args.msg : '';
		if(msg.length === 0) {
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
		jquery('#events').empty();
		jquery('#search_field').val(INFODESK_GLOBAL.search_string);
	}, function(err) { add_error(JSON.stringify(err)); });
	INFODESK_GLOBAL.last_id = 0;
	update_events();
}

/* */
function update_events() {
	var next_id, hashtag;
	if(INFODESK_GLOBAL.updating === true) { return; }
	INFODESK_GLOBAL.updating = true;
	next_id = INFODESK_GLOBAL.last_id+1;
	require(["jquery"], function(jquery) {
		var options = {'msgs':'1', 'start':''+next_id};
		if(INFODESK_GLOBAL.search_string && (INFODESK_GLOBAL.search_string !== '')) {
			options.q = INFODESK_GLOBAL.search_string;
		}
		jquery.get('backend.php', options, function(data) {
			var events = JSON.parse(data), event, div, id, msg;
			//alert('got events: ' + events.length);
			for(i in events) if(events.hasOwnProperty(i)) {
				event = events[i];
				id = parseInt(event.log_id, 10);
				if(id > INFODESK_GLOBAL.last_id) {
					INFODESK_GLOBAL.last_id = id;
				}
				div = jquery('#elements .event_container').clone();
				div.find('.log_id').text(''+event.log_id);
				div.find('.date').text(''+event.updated);
				msg = jquery('<div/>').text(event.msg).html();
				msg = msg.replace(/#([a-zA-Z0-9\.]+)/g, function($0, $1) {
					var h = (''+$1).toLowerCase();
					var div = jquery('<div/>');
					var a = jquery('<a href="javascript:change_search_string(\'#' + escape(h) + '\')" class="label label-info"></a>').text('#'+$1);
					a.appendTo(div);
					return div.html();
				});
				div.find('.msg').html(msg);
				div.prependTo('#events');
			}
			INFODESK_GLOBAL.updating = false;
		});
	}, function(err) { add_error(JSON.stringify(err)); });
}

/* */
function update_clock() {
	function f(d) { return ((''+d).length===1) ? '0'+d : ''+d; }
	require(["jquery"], function(jquery) {
		var now = new Date();
		jquery('#clock').val( f(now.getHours()) + ':' + f(now.getMinutes()) + ':' + f(now.getSeconds()) );
	});
}

/* */
INFODESK_GLOBAL.timer = undefined;
INFODESK_GLOBAL.updating = false;
INFODESK_GLOBAL.last_id = 0;
function update_events_timer() {
	update_events();
	update_clock();
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
	});
};

/* EOF */
