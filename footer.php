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
		<script id="tmpl-notification-nav" type="text/html">
			<span id="notifications-number">{{ data.number }}</span> <span class="fa fa-fw fa-circle<# if ( ! data.number ) { #>-o<# } #>"></span> <span class="visible-xs-inline"> <?php _e('Notifications', 'luna'); ?></span>
		</script>
		<script id="tmpl-notification-menu" type="text/html">
			<li role="presentation" class="dropdown-header no-hover"><?php _e('Notifications', 'luna'); ?></li>
			<li class="divider"></li>
			<li class="notification-empty"><a href="notifications.php"><?php _e('No new notifications', 'luna'); ?></a></li>
			<li class="divider"></li>
			<li class="dropdown-footer"><a class="pull-right" href="notifications.php"><?php _e('More', 'luna'); ?> <i class="fa fa-fw fa-arrow-right"></i></a></li>
		</script>
		<script id="tmpl-notification-menu-item" type="text/html">
			<a href="{{ data.link }}" class="notification-link"><span class="fa fa-fw {{ data.icon }}"></span> {{ data.message }} <span class="timestamp pull-right">{{ data.time }}</span></a>
			<a href="#" class="notification-action action-check" data-action="read"><span class="fa fa-fw fa-check"></span></a>
			<a href="#" class="notification-action action-delete" data-action="delete"><span class="fa fa-fw fa-trash"></span></a>
		</script>

		<script src="include/js/vendor/underscore-min.js"></script>
		<script src="include/js/vendor/backbone-min.js"></script>
		<script src="include/js/luna-backbone.js"></script>
		<script src="include/js/luna.js"></script>
		<script src="include/js/luna-heartbeat.js"></script>
		<script src="include/js/luna-notifications.js"></script>
		<script type="text/javascript">
			_nonces = {
				heartbeat:  '<?php echo LunaNonces::create('heartbeat-nonce'); ?>',
				fetchNotif: '<?php echo LunaNonces::create('fetch-notifications-nonce'); ?>',
				trashNotif: '<?php echo LunaNonces::create('trash-notification-nonce'); ?>',
				readNotif:  '<?php echo LunaNonces::create('read-notification-nonce'); ?>',
			};
			ajaxurl = '<?php echo get_base_url().'/ajax.php'; ?>';
			l10n = {
				no_notification: '<?php _e('No new notifications', 'luna'); ?>'
			};
				
			// Make it possible to click anywhere within a row to select the checkbox
			$('.table tr').click(function(event) {
				if (event.target.type !== 'checkbox') {
					$(':checkbox', this).trigger('click');
				}
			});
			
			// Highlight checked rows
			$("input[type='checkbox']").change(function (e) {
				if ($(this).is(":checked")) {
					$(this).closest('tbody tr').addClass("active"); 
				} else {
					$(this).closest('tbody tr').removeClass("active");
				}
			});
		</script>