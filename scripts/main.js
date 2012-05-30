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

/* Pop error message */
function add_error(args) {
	require(["bootstrap", "jquery"], function(b, jquery) {
		var data, dialog;
		if(args && (typeof args === 'object')) {
			data = args;
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
	}, function (err) {
		alert("Error: " + err);
	});
}

/* Post message to server */
function post_msg(args) {
	var msg = args.msg || '';
	require(["prototype"], function(Ajax) {
		try {
			new Ajax.Request('backend.php', {
			    method:'post',
				parameters: {'send_msg':'1','msg':''.msg},
			    onComplete: function(response){
					try {
						if(response && response.status && (200 === response.status) ) {
							alert("Success!");
						} else if(response && (response.status !== undefined)) {
							add_error({'title':'Connection failed with #' + response.status, 'desc':response.responseText});
						} else {
							add_error('Connection failed');
						}
					} catch(e) {
						add_error('Connection failed');
					}
			    }
			  });
		} catch(e) {
			add_error('Connection failed: ' + e);
		}
	}, function(err) { add_error(err) });
}

/* Post message to server */
function post_msg_form() {
	require(["jquery"], function(jquery) {
		var msg = jquery('#control_form').find('.msg_field');
		post_msg({'msg': ''+msg.value});
	}, function(err) { add_error(err) });
	return false;
}

/* Init everything at onLoad event */
window.onload = function(){
	
	// Load Bootstrap
	require(["bootstrap"], function() {
		
	}, function(err) { add_error(err) });
	
	// TODO: Setup simple clock on control form
	
	// TODO: Setup previous event history
	
	// TODO: Start fetching new events
}

/* EOF */
