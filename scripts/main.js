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
function add_error(msg) {
	require(["bootstrap", "jquery"], function() {
		var dialog = $('#elements .error_dialog').clone().appendTo('#history');
		dialog.alert();
		var full_text = dialog.$('.full_text');
		full_text.text(msg);
		alert(msg);
	});
}

/* Post message to server */
function post_message(args) {
	var message = args.message || '';
	require(["prototype"], function(Ajax) {
		try {
			new Ajax.Request('backend.php', {
			    method:'post',
				parameters: {'send_msg': '1','message':message},
			    onComplete: function(response){
					try {
						if(response && response.status && (200 === response.status) ) {
							alert("Success!");
						} else if(response && (response.status !== undefined)) {
							add_error('Connection failed with #' + response.status);
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
	});
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
		
	});
	
	// TODO: Setup simple clock on control form
	
	// TODO: Setup previous event history
	
	// TODO: Start fetching new events
}

/* EOF */
