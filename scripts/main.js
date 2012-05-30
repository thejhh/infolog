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
		dialog.appendTo('#history');
		dialog.alert();
	}, function (err) {
		alert("Error: " + err);
	});
}

/* Post message to server */
function post_message(args) {
	var message = args.message || '';
	require(["prototype"], function(Ajax) {
		try {
			new Ajax.Request('backend.php', {
			    method:'post',
				parameters: {'send_msg': '1','msg':message},
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
function post_message_form(button) {
	var form = button.form;
	post_message({'message': ''+form.message.value});
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
