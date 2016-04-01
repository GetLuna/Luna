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


if (defined('LUNA_DEBUG')) {
    echo '<div class="row"><div class="col-xs-12">';
	display_saved_queries();
    echo '</div></div>';
}

// End the transaction
$db->end_transaction();

?>
				</div>
			</div>
		</div>
        <footer class="container">
<?php
// Display debug info (if enabled/defined)
if (defined('LUNA_DEBUG')) {
	// Calculate script generation time
	$time_diff = sprintf('%.3f', get_microtime() - $luna_start);
	echo sprintf(__('Generated in %1$s seconds &middot; %2$s queries executed', 'luna'), $time_diff, $db->get_num_queries());

	if (function_exists('memory_get_usage')) {
		echo ' &middot; '.sprintf(__('Memory usage: %1$s', 'luna'), file_size(memory_get_usage()));

		if (function_exists('memory_get_peak_usage'))
			echo ' '.sprintf(__('(Peak: %1$s)', 'luna'), file_size(memory_get_peak_usage()));
	}
}
?>
            <span class="pull-right"><?php printf(__('Powered by %s', 'luna'), ' <a href="http://getluna.org/">Luna '.$luna_config['o_cur_version'].'</a>') ?> '<i><?php echo Version::LUNA_CODE_NAME ?></i>' &middot; <?php echo Version::LUNA_CORE_VERSION ?></span>
        </footer>
		<script src="../vendor/js/jquery.min.js"></script>
		<script src="../vendor/js/bootstrap.min.js"></script>
		<script src="../vendor/js/colours.min.js"></script>
		<script language="javascript">
			var flat_palette = [
				"#1abc9c", "#64b450", "#38a2eb", "#826eb4", "#555555",
				"#16a085", "#338e1c", "#136cab", "#644e9c", "#333333",
				"#ffb900", "#f56e28", "#dc3232", "#e87ece", "#dddddd",
				"#f39c12", "#d35400", "#ab1515", "#b74b9c", "#999999",
			];

			$(document).ready(function(){

				$('.color').colours({
					palette: flat_palette,
					color: '#95a5a6',
					width: 39,
					show_field: true,
					palette_size: 42,
					palette_row_count: 5,
				});

				// Make it possible to click anywhere within a row to select the checkbox
				$('.table-js tr').click(function(event) {
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

				// Check all
				$(".table #checkall").click(function () {
					if ($(".table #checkall").is(':checked')) {
						$(".table input[type=checkbox]").each(function () {
							$(this).prop("checked", true);
							$(this).closest('tbody tr').addClass("active");
						});
					} else {
						$(".table input[type=checkbox]").each(function () {
							$(this).prop("checked", false);
							$(this).closest('tbody tr').removeClass("active");
						});
					}
				});

			});
		</script>
	</body>
</html>
<?php

// Close the db connection (and free up any result data)
$db->close();
