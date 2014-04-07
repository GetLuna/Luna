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
<footer>
<?php

if (isset($footer_style) && ($footer_style == 'viewforum' || $footer_style == 'viewtopic') && $is_admmod)
{
	echo "\t\t".'<div class="modcontrols">'."\n";

	if ($footer_style == 'viewforum')
	{
		echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;p='.$p.'" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span> '.$lang['Moderate forum'].'</a>'."\n";
	}
	else if ($footer_style == 'viewtopic')
	{
		echo "\t\t\t\t".'<div class="btn-toolbar"><div class="btn-group"><a href="moderate.php?fid='.$forum_id.'&amp;tid='.$id.'&amp;p='.$p.'" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span> '.$lang['Moderate topic'].'</a></div>';
		
		if($num_pages > 1)
			echo '<div class="btn-group"><a href="moderate.php?fid='.$forum_id.'&amp;tid='.$id.'&amp;action=all" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-list"></span> '.$lang['All'].'</a></div>'."\n";
		
		echo "\t\t\t\t".'<div class="btn-group"><a href="moderate.php?fid='.$forum_id.'&amp;move_topics='.$id.'" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-share-alt"></span> '.$lang['Move topic'].'</a>'."\n";

		if ($cur_topic['closed'] == '1')
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;open='.$id.'" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok"></span> '.$lang['Open topic'].'</a>'."\n";
		else
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;close='.$id.'" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> '.$lang['Close topic'].'</a>'."\n";

		if ($cur_topic['sticky'] == '1')
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;unstick='.$id.'" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pushpin"></span> '.$lang['Unstick topic'].'</a></div></div>'."\n";
		else
			echo "\t\t\t\t".'<a href="moderate.php?fid='.$forum_id.'&amp;stick='.$id.'" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-pushpin"></span> '.$lang['Stick topic'].'</a></div></div>'."\n";
	}

	echo "\t\t\t\n\t\t".'</div>'."\n";
}

// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : NULL;

if ($footer_style == 'index')
{
	if ($luna_config['o_feed_type'] == '1')
		echo "\t\t\t\t".'<span class="rss"><a href="extern.php?action=feed&amp;type=rss">'.$lang['RSS active topics feed'].'</a></span>'."\n";
	else if ($luna_config['o_feed_type'] == '2')
		echo "\t\t\t\t".'<span class="atom"><a href="extern.php?action=feed&amp;type=atom">'.$lang['Atom active topics feed'].'</a></span>'."\n";
}
else if ($footer_style == 'viewforum')
{
	if ($luna_config['o_feed_type'] == '1')
		echo "\t\t\t\t".'<span class="rss"><a href="extern.php?action=feed&amp;fid='.$forum_id.'&amp;type=rss">'.$lang['RSS forum feed'].'</a></span>'."\n";
	else if ($luna_config['o_feed_type'] == '2')
		echo "\t\t\t\t".'<span class="atom"><a href="extern.php?action=feed&amp;fid='.$forum_id.'&amp;type=atom">'.$lang['Atom forum feed'].'</a></span>'."\n";
}
else if ($footer_style == 'viewtopic')
{
	if ($luna_config['o_feed_type'] == '1')
		echo "\t\t\t\t".'<span class="rss"><a href="extern.php?action=feed&amp;tid='.$id.'&amp;type=rss">'.$lang['RSS topic feed'].'</a></span>'."\n";
	else if ($luna_config['o_feed_type'] == '2')
		echo "\t\t\t\t".'<span class="atom"><a href="extern.php?action=feed&amp;tid='.$id.'&amp;type=atom">'.$lang['Atom topic feed'].'</a></span>'."\n";
}


if (!defined('FORUM_FORM'))
{ ?>
	<span class="pull-right" id="poweredby"><?php printf($lang['Powered by'], '<a href="http://modernbb.be/">ModernBB</a>'.(($luna_config['o_show_version'] == '1') ? ' '.$luna_config['o_cur_version'] : '')) ?></span>
    <script src="include/bootstrap/jquery.js"></script>
    <script src="include/bootstrap/bootstrap.js"></script>
<?php
}
?>
</footer>
<?php

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG'))
{
	echo '<p id="debugtime">[ ';

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
