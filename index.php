<?php
	$domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'default.infolog.in';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title><?php echo htmlspecialchars($domain); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Infodesk event logger" />
    <meta name="author" content="Jaakko-Heikki Heusala <jhh@sendanor.com>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="copyright" content="&copy; 2012 Jaakko-Heikki Heusala <jheusala@iki.fi>" />
	<meta name="description" content="Simple event journal." />
	<meta name="googlebot" content="noarchive" />
	<meta name="robots" content="noindex,nofollow" />

    <link rel="alternate" type="application/rss+xml" title="infolog.in" href="http://<?php echo htmlspecialchars($domain); ?>/feed.rss" />

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="bootstrap/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="bootstrap/ico/apple-touch-icon-57-precomposed.png">
	<script data-main="scripts/main" src="scripts/require.js"></script>
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Infolog</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a data-toggle="modal" href="#about">About</a></li>
              <li><a data-toggle="modal" href="#contact">Contact</a></li>
            </ul>

			<form class="form-search">
			  <input type="text" class="input-medium search-query" id="search_field"  placeholder="Search..." />
			  <button type="submit" class="btn">Search</button>
			</form>

          </div><!--/.nav-collapse -->

        </div>
      </div>
    </div>

    <div class="container">

		<div id="controls">
			<form class="well form-inline" id="control_form">
			  <input name="time" type="text" class="input-small" readonly="readonly" value="hh:mm:ss" size="8" id="clock" />
			  <input name="msg" autocomplete="off" type="text" class="msg_field input-xxlarge" placeholder="What's happening?">
			  <button type="submit" class="btn" onclick="return post_msg_form(this)">Send</button>
			</form>
		</div>

		<div id="notifications">
		</div>

		<div id="events">
			<div class="events-header">
			</div>
			<div class="events-body">
			</div>
		</div>

			<div class="contact_modal modal hide" id="contact">
				<div class="modal-header">
					<button class="close" data-dismiss="modal">&times;</button>
					<h3>Contact</h3>
				</div>
				<div class="modal-body">
					<p>In case of a problem:</p>
					<ul>
					 <li>You can submit issues at <a href="http://github.com/jheusala/infolog/issues">GitHub</a></li>
					 <li>...or contact author by email <a href="mailto:jheusala@iki.fi">Jaakko-Heikki Heusala &lt;jheusala@iki.fi&gt;</a></li>
					</ul></p>

				</div>
				<div class="modal-footer">
					<a class="btn" data-dismiss="modal">Close</a>
				</div>
			</div>

			<div class="about_modal modal hide" id="about">
				<div class="modal-header">
					<button class="close" data-dismiss="modal">&times;</button>
					<h3>Vectorama Infodesk Logger</h3>
				</div>
				<div class="modal-body">
					<p></p>
				</div>
				<div class="modal-footer">
					<a class="btn" data-dismiss="modal">Close</a>
				</div>
			</div>

		<div class="hidden" id="elements">

			<div class="error_dialog alert alert-error">
				<button class="close" data-dismiss="alert">&times;</button>
				<h4 class="alert-heading">Error!</h4>
				<p class="title"></p>
				<div class="desc_container">
					Detailed info:<br />
					<pre class="desc"></pre>
				</div>
			</div>

			<div class="event_container">
				<p>
				<i class="small date"></i> - 
				<span class="msg"></span>
				</p>
			</div>
		</div>

		<a target="_blank" href="https://github.com/jheusala/infolog"><img style="position: absolute; z-index: 9999; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png" alt="Fork me on GitHub"></a>

    </div> <!-- /container -->

  </body>
</html>