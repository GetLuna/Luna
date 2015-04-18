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
			<li class="dropdown-footer"><a class="pull-right" href="notifications.php"><?php echo $lang['More']; ?> <i class="fa fa-fw fa-arrow-right"></i></a></li>
		</script>
		<script id="tmpl-notification-menu-item" type="text/html">
			<a href="{{ data.link }}" class="notification-link"><span class="fa fa-fw luni luni-fw {{ data.icon }}"></span> {{ data.message }} <span class="timestamp pull-right">{{ data.time }}</span></a>
			<a href="#" class="notification-action action-check" data-action="markread"><span class="fa fa-fw fa-check"></span></a>
			<a href="#" class="notification-action action-delete" data-action="delete"><span class="fa fa-fw fa-trash"></span></a>
		</script>

		<script src="include/js/vendor/underscore-min.js"></script>
		<script src="include/js/vendor/backbone-min.js"></script>
		<script src="include/js/luna-backbone.js"></script>
		<script src="include/js/luna.js"></script>
		<script src="include/js/luna-heartbeat.js"></script>
		<script src="include/js/luna-notifications.js"></script>
		<script type="text/javascript">
			heartbeatnonce = '<?php echo luna_create_nonce( 'heartbeat-nonce' ); ?>';
			ajaxurl        = '<?php echo get_base_url() . '/ajax.php'; ?>';
		</script>
