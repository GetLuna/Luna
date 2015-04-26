<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

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