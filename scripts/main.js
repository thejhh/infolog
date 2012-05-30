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

/* Post message to server */
function post_message(args) {
	var message = args.message || '';
	require(["prototype"], function(Ajax) {
		new Ajax.Request('backend.php', {
		    method:'post',
		    onSuccess: function(transport){
				var response = transport.responseText || "FAIL";
				if(response.substr(0, 2) !== "OK") {
					alert('Something went wrong...');
				} else {
					alert("Success! \n\n" + response);
				}
		    },
		    onFailure: function(){
				alert('Something went wrong...')
			}
		  });
	});
}

/* Post message to server */
function post_message_form(button) {
	var form = button.form;
	post_message({'message': form.message.value});
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
