<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<luna_main>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <luna_main>


// START SUBST - <luna_footer>
ob_start();

?>
<footer class="col-lg-12">
	<p class="pull-right"><?php printf($lang['Thanks'], '<a href="http://modernbb.be/">ModernBB</a> '.$luna_config['o_cur_version']) ?></p>
    <script src="../include/bootstrap/jquery.js"></script>
    <script src="../include/bootstrap/js/bootstrap.min.js"></script>
</footer>
<?php

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG'))
{
	echo '<p id="debug">[ ';

	// Calculate script generation time
	$time_diff = sprintf('%.3f', get_microtime() - $luna_start);
	echo sprintf($lang['Querytime'], $time_diff, $db->get_num_queries());

	if (function_exists('memory_get_usage'))
	{
		echo ' - '.sprintf($lang['Memory usage'], file_size(memory_get_usage()));

		if (function_exists('memory_get_peak_usage'))
			echo ' '.sprintf($lang['Peak usage'], file_size(memory_get_peak_usage()));
	}

	echo ' ]</p>'."\n";
}


// End the transaction
$db->end_transaction();

// Display executed queries (if enabled)
if (defined('FORUM_SHOW_QUERIES'))
	display_saved_queries();

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<luna_footer>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <luna_footer>


// Close the db connection (and free up any result data)
$db->close();

// Spit out the page
exit($tpl_main);
