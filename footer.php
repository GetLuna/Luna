<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

?>
		<script id="tmpl-notification-menu" type="text/html">
			<li role="presentation" class="dropdown-header"><?php echo $lang['Notifications']; ?></li>
			<li class="divider"></li>
			<# _.each( data, function( notification ) { #>
			<li><a href="{{ notification.link }}"><span class="fa fa-fw luni luni-fw {{ notification.icon }}"></span> {{ notification.message }} <span class="timestamp pull-right">{{ notification.time }}</span></a></li>
			<li class="divider"></li>
			<# } ); #>
			<li class="dropdown-footer"><a class="pull-right" href="notifications.php"><?php echo $lang['More']; ?> <i class="fa fa-fw fa-arrow-right"></i></a></li>
		</script>

		<script src="include/js/vendor/underscore-min.js"></script>
		<script src="include/js/vendor/backbone-min.js"></script>
		<script src="include/js/luna.js"></script>
		<script src="include/js/luna-heartbeat.js"></script>
		<script src="include/js/luna-notifications.js"></script>
		<script type="text/javascript">
			heartbeatnonce = '<?php echo luna_create_nonce( 'heartbeat-nonce' ); ?>';
			ajaxurl        = '<?php echo get_base_url() . '/ajax.php'; ?>';
		</script>
