/* Main code */

/* RequireJS configurations */
requirejs.config({
	shim: {
		'prototype':{
			deps: [],
			exports: 'Ajax'
		},
		'bootstrap':{
			deps: ['jquery']
		}
	}
});

var ui = {};

/* Pop error message */
ui.add_error = function add_error(args) {
	require(["jquery"], function(jquery) {
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
		dialog = jquery('#elements .error_dialog').clone();
		dialog.find('.date').text(data.date);
		dialog.find('.title').text(data.title);
		if(data.desc && ((data.desc+'').length > 0) ) {
			dialog.find('.desc').text(data.desc);
		} else {
			dialog.find('.desc_container').hide();
		}
		dialog.appendTo('#notifications');
		dialog.alert();
	}, function(err) {
		alert('Error: ' . err);
	});
}

/* Post message to server */
function post_msg(args) {
	require(["jquery"], function(jquery) {
		var args = args || {};
		var msg = (args && (typeof args === 'object') && args.msg) ? ''.args.msg : '';
		if(msg.length === 0) {
			return;
		}
		var jqxhr = jquery.post('backend.php', {'send_msg':'1', 'msg':''.msg});
		jqxhr.complete(function(response) {
			try {
				if(response && response.status && (200 === response.status) ) {
					jquery("#control_form .msg_field").val('');
					alert("Success!");
				} else if(response && (response.status !== undefined)) {
					ui.add_error({'title':'Connection failed with #' + response.status, 'desc':response.responseText});
				} else {
					ui.add_error('Connection failed');
				}
			} catch(e) {
				ui.add_error('Connection failed');
			}
		});
	}, function(err) {
		ui.add_error({'title':'Connection failed', 'desc':JSON.stringify(err)});
	});
}

/* Post message to server */
ui.post_msg_form = function post_msg_form() {
	var msg;
	require(["jquery"], function(jquery) {
		msg = jquery('#control_form').find('.msg_field').val();
	}, function(err) {
		ui.add_error({'title':'Clearing form failed', 'desc':JSON.stringify(err)});
	});
	post_msg({'msg':msg});
	return false;
};

/* Init everything at onLoad event */
require(["jquery"], function(jquery) {
	jquery.ready(function(){
		// TODO: Setup simple clock on control form
		
		// TODO: Setup previous event history
		
		// TODO: Start fetching new events
	});
}, function(err) {
	ui.add_error({'title':'Exception at main', 'desc':JSON.stringify(err)});
});
		
/* EOF */
