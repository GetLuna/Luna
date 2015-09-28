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

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG')) {
	echo '<p id="debug">[ ';

	// Calculate script generation time
	$time_diff = sprintf('%.3f', get_microtime() - $luna_start);
	echo sprintf(__('Generated in %1$s seconds, %2$s queries executed', 'luna'), $time_diff, $db->get_num_queries());

	if (function_exists('memory_get_usage')) {
		echo ' - '.sprintf(__('Memory usage: %1$s', 'luna'), file_size(memory_get_usage()));

		if (function_exists('memory_get_peak_usage'))
			echo ' '.sprintf(__('(Peak: %1$s)', 'luna'), file_size(memory_get_peak_usage()));
	}

	echo ' ]</p>'."\n";
}


// End the transaction
$db->end_transaction();

?>
				</div>
			</div>
		</div>
		<script src="../include/js/vendor/jquery.js"></script>
		<script src="../include/js/vendor/bootstrap.min.js"></script>
		<script src="../include/js/vendor/colours.min.js"></script>
		<script language="javascript">
			var flat_palette = [
				"#1abc9c", "#64b450", "#38a2eb", "#826eb4", "#555555",
				"#16a085", "#338e1c", "#136cab", "#644e9c", "#333333",
				"#ffb900", "#f56e28", "#dc3232", "#e87ece", "#dddddd",
				"#f39c12", "#d35400", "#ab1515", "#b74b9c", "#999999",
			];

			$(document).ready(function(){

				$('#color').colours({
					palette: flat_palette,
					color: '#95a5a6',
					width: 39,
					show_field: true,
					palette_size: 42,
					palette_row_count: 5,
				});

			});
		</script>
	</body>
</html>
<?php

// Close the db connection (and free up any result data)
$db->close();
