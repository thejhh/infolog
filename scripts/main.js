/* Main code */

/* RequireJS configurations */
requirejs.config({
	shim: {
		'bootstrap':{
			deps: ['jquery']
		}
	}
});

/* Pop error message */
function add_error(args) {
	alert('error: '+ JSON.stringify(args));
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
		dialog.appendTo('#notifications');
		dialog.alert();
	}, function(err) {
		alert('Error: ' . err);
	});
};

/* Post message to server */
function post_msg(args) {
		alert('In post_msg() with args=' + JSON.stringify(args) );
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
				if(response && response.status && (200 === response.status) ) {
					require(["jquery"], function(jquery) {
						jquery("#control_form .msg_field").val('');
					}, function(err) { add_error(JSON.stringify(err)); });
					alert("Success!");
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
	alert('Calling post_msg() with msg=' + msg);
	post_msg({'msg':msg});
	return false;
}

/* Init everything at onLoad event */
window.onload = function() {
	require(["bootstrap"], function(b) {});
		
	// TODO: Setup simple clock on control form
		
	// TODO: Setup previous event history
		
	// TODO: Start fetching new events
};

/* EOF */
