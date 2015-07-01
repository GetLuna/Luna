<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

function pending_messages($user) {
	global $db;

	$user = intval($user);

	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'messages WHERE owner='.$user.' AND show_message=1') or error('Unable to fetch pending messages', __FILE__, __LINE__, $db->error());
	$pending = $db->result($result);

	return $pending;
}

function get_user_nav_menu_items() {
	global $db, $luna_config, $luna_user;

	$items = array();

	if ($luna_user['is_guest']) {
		$items['guest'] = array(
			'register' => array(
				'url'   => 'register.php',
				'title' => __('Register', 'luna'),
			),
			'login' => array(
				'url'   => '#',
				'title' => __('Login', 'luna'),
			)
		);
	} else {

		if ($luna_user['is_admmod']) {
			$items['backstage'] = array(
				'url'   => 'backstage/',
				'title' => __('Backstage', 'luna'),
			);
		}

		// Check for new notifications
		$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
		$num_notifications = intval($db->result($result));

		$items['notifications'] = array(
			'url'    => 'notifications.php',
			'title'  => $num_notifications > 0 ? __('Notifications', 'luna') : __('No new notifications', 'luna'),
			'num'    => $num_notifications,
			'flyout' => 1 == $luna_config['o_notification_flyout']
		);

		if ($luna_config['o_pms_enabled'] == '1' && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1') {
			// Check for new messages
			$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'messages WHERE showed=0 AND show_message=1 AND owner='.$luna_user['id']) or error('Unable to check the availibility of new messages', __FILE__, __LINE__, $db->error());
			$num_new_pm = intval($db->result($result));

			$items['inbox'] = array(
				'url'   => 'inbox.php',
				'title' => 'Inbox',
				'num'   => $num_new_pm,
			);
		}

		$items['user'] = array(
			'profile'  => array(
				'url'   => 'profile.php?id='.$luna_user['id'],
				'title' => __('Profile', 'luna'),
			),
			'settings' => array(
				'url'   => 'settings.php',
				'title' => __('Settings', 'luna'),
			),
			'help'     => array(
				'url'   => 'help.php',
				'title' => __('Help', 'luna'),
			),
			'logout'   => array(
				'url'   => 'login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())),
				'title' => __('Logout', 'luna'),
			)
		);
	}

	return $items;
}

function required_fields() {
	global $required_fields;

	if (isset($required_fields)) {
	// Output JavaScript to validate form (make sure required fields are filled out)
	
?>
	<script type="text/javascript">
	/* <![CDATA[ */
	function process_form(the_form) {
		var required_fields = {
<?php
		// Output a JavaScript object with localised field names
		$tpl_temp = count($required_fields);
		foreach ($required_fields as $elem_orig => $elem_trans) {
			echo "\t\t\"".$elem_orig.'": "'.addslashes(str_replace('&#160;', ' ', $elem_trans));
			if (--$tpl_temp) echo "\",\n";
			else echo "\"\n\t};\n";
		}
?>
		if (document.all || document.getElementById) {
			for (var i = 0; i < the_form.length; ++i) {
				var elem = the_form.elements[i];
				if (elem.name && required_fields[elem.name] && !elem.value && elem.type && (/^(?:text(?:area)?|password|file)$/i.test(elem.type))) {
					alert('"' + required_fields[elem.name] + '" <?php _e('is a required field in this form.', 'luna') ?>');
					elem.focus();
					return false;
				}
			}
		}
		return true;
	}
	/* ]]> */
	</script>
<?php
	
	}
}

function check_url() {
	$redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	return $redirect_url;
}