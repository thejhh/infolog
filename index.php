<?php
	require_once('main-config.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title><?php echo htmlspecialchars(CURRENT_DOMAIN); ?></title>
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

    <link rel="alternate" type="application/rss+xml" title="<?php echo htmlspecialchars(CURRENT_DOMAIN); ?>" href="http://<?php echo htmlspecialchars(CURRENT_DOMAIN); ?>/feed.rss" />

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
          <a class="brand" href="http://<?php echo htmlspecialchars(TOP_DOMAIN); ?>"><?php echo htmlspecialchars(TOP_DOMAIN); ?></a>

          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="/">@<?php echo htmlspecialchars(CURRENT_DOMAIN_TAG); ?></a></li>
              <li><a data-toggle="modal" href="#about"><i class="icon-info-sign icon-white"></i> README</a></li>
            </ul>

<ul class="nav nav-pills pull-right">
  <li class="dropdown" id="menu1">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#menu1">
      <i class="icon-th icon-white"></i>
      <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      <li><a data-toggle="modal" href="#join_channel"><i class="icon-edit"></i> Join/Create Channel</a></li>
      <li><a data-toggle="modal" href="#setup_auth"><i class="icon-edit"></i> Set auth key</a></li>
      <li class="divider"></li>
      <li><a data-toggle="modal" href="#about"><i class="icon-info-sign"></i> About</a></li>
      <li><a data-toggle="modal" href="#contact"><i class="icon-question-sign"></i> Contact</a></li>
    </ul>
  </li>
</ul>
			<form class="form-search navbar-search pull-right">
			  <input type="text" class="input-medium search-query" id="search_field"  placeholder="Search..." />
			  <button type="submit" class="btn"><i class="icon-search"></i></button>
			</form>

          </div><!--/.nav-collapse -->

        </div>
      </div>
    </div>

    <div class="container">

		<noscript>
			<div class="alert">
				<button class="close" data-dismiss="modal">&times;</button>
				<strong><i class="icon-warning-sign"></i> Warning!</strong> This site requires JavaScript support.
			</div>
		</noscript>

		<div id="controls">
			<form class="well form-inline control-group success" id="control_form">
				<div class="controls">
					<input name="msg" autocomplete="off" type="text" class="msg_field input-xxlarge" placeholder="What happened?" maxlength="<?php echo MAX_MSG_LENGTH; ?>" />
					<button type="submit" class="btn submit-btn" disabled="disabled"><i class="icon-comment"></i> Send</button>
					<span class="msg_field_help help-inline hide"></span>
				</div>
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

			<div class="contact_modal modal hide" id="join_channel">
				<form class="form-horizontal">
				<div class="modal-header">
					<button class="close" data-dismiss="modal">&times;</button>
					<h3>Join/create a channel</h3>
				</div>
				<div class="modal-body">
					    <div class="control-group">
					      <label class="control-label" for="input01">Channel Name</label>
					      <div class="controls">
					        <input type="text" class="input-xlarge channel_field" />
					        <p class="help-block">Channel named <i>foo</i> will be at address <i>foo</i>.<?php echo htmlspecialchars(TOP_DOMAIN); ?>.<br />
									<br />
									New channels are created when first user joins them.</p>
					      </div>
					    </div>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal"><i class="icon-remove"></i> Close</a>
					<button type="submit" class="btn btn-primary"><i class="icon-ok"></i> Join</button>
				</div>
				</form>
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
					<a class="btn" data-dismiss="modal"><i class="icon-remove"></i> Close</a>
				</div>
			</div>

			<div class="about_modal modal hide" id="about">
				<div class="modal-header">
					<button class="close" data-dismiss="modal">&times;</button>
					<h3>README</h3>
				</div>
				<div class="modal-body">
					<p></p>
				</div>
				<div class="modal-footer">
					<a class="btn" data-dismiss="modal"><i class="icon-remove"></i> Close</a>
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
				<i class="small date"></i> - <span class="msg"></span>
				<a href="#" class="close">&times;</a>
			</div>
		</div>

		<div id="site-generator">
			Proudly powered by <a href="https://github.com/jheusala/infolog#infolog" title="Infolog" rel="generator">Infolog</a>
		</div>

		<a target="_blank" href="https://github.com/jheusala/infolog"><img style="position: absolute; top: 45px; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png" alt="Fork me on GitHub"></a>

    </div> <!-- /container -->

  </body>
</html>
