<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_main>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_main>


// START SUBST - <pun_footer>
ob_start();

?>
<footer>
	<div class="box">
<?php

if (isset($footer_style) && ($footer_style == 'viewforum' || $footer_style == 'viewtopic') && $is_admmod)
{
	echo "\t\t".'<div class="modcontrols">'."\n";

	if ($footer_style == 'viewforum')
	{
		echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;p='.$p.'" class="btn btn-primary btn-mini">'.$lang_common['Moderate forum'].'</a>'."\n";
	}
	else if ($footer_style == 'viewtopic')
	{
		echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;tid='.$id.'&amp;p='.$p.'" class="btn btn-primary btn-mini">'.$lang_common['Moderate topic'].'</a>'."\n";
		echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;move_topics='.$id.'" class="btn btn-primary btn-mini">'.$lang_common['Move topic'].'</a>'."\n";

		if ($cur_topic['closed'] == '1')
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;open='.$id.'" class="btn btn-primary btn-mini">'.$lang_common['Open topic'].'</a>'."\n";
		else
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;close='.$id.'" class="btn btn-primary btn-mini">'.$lang_common['Close topic'].'</a>'."\n";

		if ($cur_topic['sticky'] == '1')
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;unstick='.$id.'" class="btn btn-primary btn-mini">'.$lang_common['Unstick topic'].'</a>'."\n";
		else
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;stick='.$id.'" class="btn btn-primary btn-mini">'.$lang_common['Stick topic'].'</a>'."\n";
	}

	echo "\t\t\t\n\t\t".'</div>'."\n";
}

?>
		<div id="brdfooternav" class="inbox">
			<div class="conr">
<?php

// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : NULL;

if ($footer_style == 'index')
{
	if ($pun_config['o_feed_type'] == '1')
		echo "\t\t\t\t".'<p id="feedlinks"><span class="rss"><a href="extern.php?action=feed&amp;type=rss">'.$lang_common['RSS active topics feed'].'</a></span>'."\n";
	else if ($pun_config['o_feed_type'] == '2')
		echo "\t\t\t\t".'<p id="feedlinks"><span class="atom"><a href="extern.php?action=feed&amp;type=atom">'.$lang_common['Atom active topics feed'].'</a></span>'."\n";
}
else if ($footer_style == 'viewforum')
{
	if ($pun_config['o_feed_type'] == '1')
		echo "\t\t\t\t".'<p id="feedlinks"><span class="rss"><a href="extern.php?action=feed&amp;fid='.$forum_id.'&amp;type=rss">'.$lang_common['RSS forum feed'].'</a></span>'."\n";
	else if ($pun_config['o_feed_type'] == '2')
		echo "\t\t\t\t".'<p id="feedlinks"><span class="atom"><a href="extern.php?action=feed&amp;fid='.$forum_id.'&amp;type=atom">'.$lang_common['Atom forum feed'].'</a></span>'."\n";
}
else if ($footer_style == 'viewtopic')
{
	if ($pun_config['o_feed_type'] == '1')
		echo "\t\t\t\t".'<p id="feedlinks"><span class="rss"><a href="extern.php?action=feed&amp;tid='.$id.'&amp;type=rss">'.$lang_common['RSS topic feed'].'</a></span>'."\n";
	else if ($pun_config['o_feed_type'] == '2')
		echo "\t\t\t\t".'<p id="feedlinks"><span class="atom"><a href="extern.php?action=feed&amp;tid='.$id.'&amp;type=atom">'.$lang_common['Atom topic feed'].'</a></span>'."\n";
}

?>
				<span class="pull-right" id="poweredby"><?php printf($lang_common['Powered by'], '<a href="http://modernbb.be/">ModernBB</a>'.(($pun_config['o_show_version'] == '1') ? ' '.$pun_config['o_cur_version'] : '')) ?></span></p>
			</div>
		</div>
	</div>
    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
    <script src="admin/js/bootstrap.js"></script>
<footer>
<?php

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG'))
{
	echo '<p id="debugtime">[ ';

	// Calculate script generation time
	$time_diff = sprintf('%.3f', get_microtime() - $pun_start);
	echo sprintf($lang_common['Querytime'], $time_diff, $db->get_num_queries());

	if (function_exists('memory_get_usage'))
	{
		echo ' - '.sprintf($lang_common['Memory usage'], file_size(memory_get_usage()));

		if (function_exists('memory_get_peak_usage'))
			echo ' '.sprintf($lang_common['Peak usage'], file_size(memory_get_peak_usage()));
	}

	echo ' ]</p>'."\n";
}


// End the transaction
$db->end_transaction();

// Display executed queries (if enabled)
if (defined('FORUM_SHOW_QUERIES'))
	display_saved_queries();

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_footer>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_footer>


// Close the db connection (and free up any result data)
$db->close();

// Spit out the page
exit($tpl_main);
