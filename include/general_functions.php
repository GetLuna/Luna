<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Create a new notification
function new_notification($user, $link, $message, $icon) {
	global $db;
	
	$now = time();
	
	$db->query('INSERT INTO '.$db->prefix.'notifications (user_id, message, icon, link, time) VALUES('.$user.', \''.$message.'\', \''.$icon.'\', \''.$link.'\', '.$now.')') or error('Unable to add new notification', __FILE__, __LINE__, $db->error());

}

function pending_notifications($user) {
	global $db;

	$user = intval($user);

	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'notifications WHERE user_id='.$db->escape($user).' AND viewed=0') or error('Unable to fetch pending notifications', __FILE__, __LINE__, $db->error());
	$pending = $db->result($result);

	return $pending;
}

function pending_messages($user) {
	global $db;

	$user = intval($user);

	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'messages WHERE owner='.$user.' AND show_message=1') or error('Unable to fetch pending messages', __FILE__, __LINE__, $db->error());
	$pending = $db->result($result);

	return $pending;
}

function required_fields() {
	global $required_fields, $lang;

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
					alert('"' + required_fields[elem.name] + '" <?php echo $lang['required field'] ?>');
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