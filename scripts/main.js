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
function pop_error_dialog(msg) {
	require(["bootstrap"], function() {
		var dialog = $('#error_dialog');
		var full_text = $('#error_dialog .full_text');
		full_text.text(msg);
		dialog.popover('show');
	});
}

/* Post message to server */
function post_message(args) {
	var message = args.message || '';
	require(["prototype"], function(Ajax) {
		new Ajax.Request('backend.php', {
		    method:'post',
		    onSuccess: function(transport){
				var response = transport.responseText || "FAIL";
				if(response.substr(0, 2) !== "OK") {
					pop_error_dialog(response);
				} else {
					alert("Success! \n\n" + response);
				}
		    },
		    onFailure: function(){
				pop_error_dialog('Something went wrong...')
			}
		  });
	});
}

/* Post message to server */
function post_message_form(button) {
	var form = button.form;
	post_message({'message': form.message.value});
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
