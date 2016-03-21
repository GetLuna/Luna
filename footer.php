<?php

/*
 * Copyright (C) 2013-2016 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

?>
		<script type="text/javascript">
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
