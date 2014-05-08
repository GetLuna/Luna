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

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache'); // For HTTP/1.0 compatibility

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Load the template
$tpl_file = 'main.tpl';

if (file_exists(FORUM_ROOT.'style/'.$luna_user['style'].'/templates/'.$tpl_file))
{
	$tpl_file = FORUM_ROOT.'style/'.$luna_user['style'].'/templates/'.$tpl_file;
	$tpl_inc_dir = FORUM_ROOT.'style/'.$luna_user['style'].'/';
}
else
{
	$tpl_file = FORUM_ROOT.'style/Core/templates/'.$tpl_file;
	$tpl_inc_dir = FORUM_ROOT.'style/User/';
}

$tpl_main = file_get_contents($tpl_file);

// START SUBST - <luna_include "*">
preg_match_all('%<luna_include "([^"]+)">%i', $tpl_main, $luna_includes, PREG_SET_ORDER);

foreach ($luna_includes as $cur_include)
{
	ob_start();
	$file_info = pathinfo($cur_include[1]);

    if (!in_array($file_info['extension'], array('php', 'php4', 'php5', 'inc', 'html', 'txt'))) // Allow some extensions
       error(sprintf($lang['Pun include extension'], htmlspecialchars($cur_include[0]), basename($tpl_file), htmlspecialchars($file_info['extension'])));

    if (strpos($file_info['dirname'], '..') !== false) // Don't allow directory traversal
       error(sprintf($lang['Pun include directory'], htmlspecialchars($cur_include[0]), basename($tpl_file)));

	// Allow for overriding user includes, too.
	if (file_exists($tpl_inc_dir.$cur_include[1]))
		require $tpl_inc_dir.$cur_include[1];
	else if (file_exists(FORUM_ROOT.'include/user/'.$cur_include[1]))
		require FORUM_ROOT.'include/user/'.$cur_include[1];
	else
		error(sprintf($lang['Pun include error'], luna_htmlspecialchars($cur_include[0]), basename($tpl_file)));

	$tpl_temp = ob_get_contents();
	$tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
	ob_end_clean();
}
// END SUBST - <luna_include "*">


// START SUBST - <luna_language>
$tpl_main = str_replace('<luna_language>', $lang['lang_identifier'], $tpl_main);
// END SUBST - <luna_language>


// START SUBST - <luna_content_direction>
$tpl_main = str_replace('<luna_content_direction>', $lang['lang_direction'], $tpl_main);
// END SUBST - <luna_content_direction>


// START SUBST - <luna_head>
ob_start();

// Define $p if it's not set to avoid a PHP notice
$p = isset($p) ? $p : null;

// Is this a page that we want search index spiders to index?
if (!defined('FORUM_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";

?>
<title><?php echo generate_page_title($page_title, $p) ?></title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $luna_user['style'] ?>/style.css" />
<?php

if (isset($required_fields))
{
	// Output JavaScript to validate form (make sure required fields are filled out)

?>
<script type="text/javascript">
/* <![CDATA[ */
function process_form(the_form)
{
	var required_fields = {
<?php
	// Output a JavaScript object with localised field names
	$tpl_temp = count($required_fields);
	foreach ($required_fields as $elem_orig => $elem_trans)
	{
		echo "\t\t\"".$elem_orig.'": "'.addslashes(str_replace('&#160;', ' ', $elem_trans));
		if (--$tpl_temp) echo "\",\n";
		else echo "\"\n\t};\n";
	}
?>
	if (document.all || document.getElementById)
	{
		for (var i = 0; i < the_form.length; ++i)
		{
			var elem = the_form.elements[i];
			if (elem.name && required_fields[elem.name] && !elem.value && elem.type && (/^(?:text(?:area)?|password|file)$/i.test(elem.type)))
			{
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

if (isset($page_head))
	echo implode("\n", $page_head)."\n";

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<luna_head>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <luna_head>


// START SUBST - <body>
if (isset($focus_element))
{
	$tpl_main = str_replace('<body onload="', '<body onload="document.getElementById(\''.$focus_element[0].'\').elements[\''.$focus_element[1].'\'].focus();', $tpl_main);
	$tpl_main = str_replace('<body>', '<body onload="document.getElementById(\''.$focus_element[0].'\').elements[\''.$focus_element[1].'\'].focus()">', $tpl_main);
}
// END SUBST - <body>


if (!defined ('FORUM_FORM')) {
// START SUBST - <luna_page>
$tpl_main = str_replace('<luna_page>', htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php')), $tpl_main);
// END SUBST - <luna_page>


// START SUBST - <luna_title>
if (($luna_config['o_header_title'] == 1) && ($luna_config['o_menu_title'] == 1)) {
	$tpl_main = str_replace('<luna_title>', '<h1 class="hidden-xs"><a href="index.php">'.luna_htmlspecialchars($luna_config['o_board_title']).'</a></h1>', $tpl_main);
} elseif (($luna_config['o_header_title'] == 1) && ($luna_config['o_menu_title'] == 0)) {
	$tpl_main = str_replace('<luna_title>', '<h1><a href="index.php">'.luna_htmlspecialchars($luna_config['o_board_title']).'</a></h1>', $tpl_main);
} else {
	$tpl_main = str_replace('<luna_title>', '', $tpl_main);
}

// END SUBST - <luna_title>


// START SUBST - <luna_desc>
if ($luna_config['o_header_desc'] == 1) {
	$tpl_main = str_replace('<luna_desc>', '<div id="brddesc"><p>'.$luna_config['o_board_desc'].'</p></div>', $tpl_main);
} else {
	$tpl_main = str_replace('<luna_desc>', '', $tpl_main);
}

// END SUBST - <luna_desc>


// START SUBST - <luna_navlinks>
$links = array();

// Index should always be displayed
if ($luna_config['o_show_index'] == '1')
	$links[] = '<li id="navindex"'.((FORUM_ACTIVE_PAGE == 'index') ? ' class="active"' : '').'><a href="index.php">'.$lang['Index'].'</a></li>';

if ($luna_user['g_read_board'] == '1' && $luna_user['g_view_users'] == '1' && $luna_config['o_show_userlist'] == '1')
	$links[] = '<li id="navuserlist"'.((FORUM_ACTIVE_PAGE == 'userlist') ? ' class="active"' : '').'><a href="userlist.php">'.$lang['User list'].'</a></li>';

if ($luna_config['o_rules'] == '1' && (!$luna_user['is_guest'] || $luna_user['g_read_board'] == '1' || $luna_config['o_regs_allow'] == '1') && $luna_config['o_show_rules'] == '1')
	$links[] = '<li id="navrules"'.((FORUM_ACTIVE_PAGE == 'rules') ? ' class="active"' : '').'><a href="misc.php?action=rules">'.$lang['Rules'].'</a></li>';

if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1' && $luna_config['o_show_search'] == '1')
	$links[] = '<li id="navsearch"'.((FORUM_ACTIVE_PAGE == 'search') ? ' class="active"' : '').'><a href="search.php">'.$lang['Search'].'</a></li>';

if ($luna_user['is_admmod'])
	$links[] = '<li id="navadmin"'.((FORUM_ACTIVE_PAGE == 'admin') ? ' class="active"' : '').'><a href="backstage/index.php">'.$lang['Backstage'].'</a></li>';

// Are there any additional navlinks we should insert into the array before imploding it?
if ($luna_user['g_read_board'] == '1' && $luna_config['o_additional_navlinks'] != '')
{
	if (preg_match_all('%([0-9]+)\s*=\s*(.*?)\n%s', $luna_config['o_additional_navlinks']."\n", $extra_links))
	{
		// Insert any additional links into the $links array (at the correct index)
		$num_links = count($extra_links[1]);
		for ($i = 0; $i < $num_links; ++$i)
			array_splice($links, $extra_links[1][$i], 0, array('<li id="navextra'.($i + 1).'">'.$extra_links[2][$i].'</li>'));
	}
}

// Generate avatar
$user_avatar = generate_avatar_markup($luna_user['id']);
// The user menu
if ($luna_user['is_guest'])
{
	$usermenu[] = '<li id="navregister"'.((FORUM_ACTIVE_PAGE == 'register') ? ' class="active"' : '').'><a href="register.php">'.$lang['Register'].'</a></li>';
	$usermenu[] = '<li id="navlogin"'.((FORUM_ACTIVE_PAGE == 'login') ? ' class="active"' : '').'><a href="login.php">'.$lang['Login'].'</a></li>';
} else {
	$usermenu[] = '<li class="dropdown">';
	$usermenu[] = '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.(luna_htmlspecialchars($luna_user['username'])).' <b class="caret"></b></a>';
	// Responsive menu
	$usermenu[] = '<ul class="dropdown-menu">';
	$usermenu[] = '<li><a href="profile.php?id='.$luna_user['id'].'">'.$lang['Profile'].'</a></li>';
	$usermenu[] = '<li><a href="help.php">'.$lang['Help'].'</a></li>';
	$usermenu[] = '<li class="divider"></li>';
	$usermenu[] = '<li><a href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">'.$lang['Logout'].'</a></li>';
	$usermenu[] = '</ul>';
	// Default
	$usermenu[] = '<ul class="dropdown-menu hidden-xs">';
	$usermenu[]= '
                                                <div class="navbar-content hidden-xs">
                                                    <div class="row">
                                                        <div class="col-xs-5">
                                                            '.$user_avatar.'
                                                        </div>
                                                        <div class="col-xs-7">
                                                            <span class="userpane-name">'.(luna_htmlspecialchars($luna_user['username'])).'</span>
                                                            <p class="text-muted small">'.(luna_htmlspecialchars($luna_user['email'])).'</p>
                                                            <div class="divider">
                                                            </div>
															<a class="btn btn-primary" href="profile.php?id='.$luna_user['id'].'">'.$lang['Profile'].'</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="navbar-footer">
                                                    <div class="navbar-footer-content">
                                                        <div class="row">
                                                            <div class="col-xs-6">
																<a href="help.php" class="btn btn-primary">Help</a>
                                                            </div>
                                                            <div class="col-xs-6">
																<a class="btn btn-default pull-right" href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">'.$lang['Logout'].'</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
	$usermenu[] = '</ul>';
	$usermenu[] = '</li>';
}

if ($luna_config['o_menu_title'] == 1) {
	$menu_title = '<a href="index.php" class="navbar-brand">'.luna_htmlspecialchars($luna_config['o_board_title']).'</a>';
} else {
	$menu_title = '';
}

$tpl_temp = '<div class="navbar navbar-default navbar-static-top">
	<div class="nav-inner">
		'.$menu_title.'
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">'."\n\t\t\t\t\t\t".implode("\n\t\t\t\t", $links)."\n\t\t\t\t\t\t".'</ul>
            <ul class="nav navbar-nav navbar-right">
				'."\n\t\t\t\t\t\t".implode("\n\t\t\t\t", $usermenu)."\n\t\t\t\t\t\t".'
            </ul>
		</div>
	</div>
</div>';
$tpl_main = str_replace('<luna_navlinks>', $tpl_temp, $tpl_main);
// END SUBST - <luna_navlinks>


// START SUBST - <luna_status>
$page_statusinfo = $page_topicsearches = array();

if (!$luna_user['is_guest'])
{
	if (!empty($forum_actions))
	{
		$page_statusinfo[] = '<li><span>'.implode(' &middot; ', $forum_actions).'</span></li>';
	}

	if (!empty($topic_actions))
	{
		$page_statusinfo[] = '<li><span>'.implode(' &middot; ', $topic_actions).'</span></li>';
	}

	if ($luna_user['is_admmod'])
	{
		if ($luna_config['o_report_method'] == '0' || $luna_config['o_report_method'] == '2')
		{
			$result_header = $db->query('SELECT 1 FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

			if ($db->result($result_header))
				$page_statusinfo[] = '<li class="reportlink"><span><strong><a href="backstage/reports.php">'.$lang['New reports'].'</a></strong></span></li>';
		}

		if ($luna_config['o_maintenance'] == '1')
			$page_statusinfo[] = '<li class="maintenancelink"><span><strong><a href="backstage/settings.php#maintenance">'.$lang['Maintenance mode enabled'].'</a></strong></span></li>';
	}

	if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1')
	{
		$page_topicsearches[] = '<a href="search.php?action=show_replies" title="'.$lang['Show posted topics'].'">'.$lang['Posted topics'].'</a>';
		$page_topicsearches[] = '<a href="search.php?action=show_new" title="'.$lang['Show new posts'].'">'.$lang['New posts header'].'</a>';
	}
}

// Quick searches
if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1')
{
	$page_topicsearches[] = '<a href="search.php?action=show_recent" title="'.$lang['Show active topics'].'">'.$lang['Active topics'].'</a>';
	$page_topicsearches[] = '<a href="search.php?action=show_unanswered" title="'.$lang['Show unanswered topics'].'">'.$lang['Unanswered topics'].'</a>';
}


// Generate all that jazz
$tpl_temp = '<div id="brdwelcome">';

// The status information
if (is_array($page_statusinfo))
{
	$tpl_temp .= "\n\t\t\t".'<ul class="conl">';
	$tpl_temp .= "\n\t\t\t\t".implode("\n\t\t\t\t", $page_statusinfo);
	$tpl_temp .= "\n\t\t\t".'</ul>';
}
else
	$tpl_temp .= "\n\t\t\t".$page_statusinfo;

// Generate quicklinks
if (!empty($page_topicsearches))
{
	$tpl_temp .= "\n\t\t\t".'<ul class="conr">';
	$tpl_temp .= "\n\t\t\t\t".'<li><span>'.implode(' &middot; ', $page_topicsearches).'</span></li>';
	$tpl_temp .= "\n\t\t\t".'</ul>';
}

$tpl_temp .= "\n\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>';

$tpl_main = str_replace('<luna_status>', $tpl_temp, $tpl_main);
// END SUBST - <luna_status>


// START SUBST - <luna_announcement>
if ($luna_user['g_read_board'] == '1' && $luna_config['o_announcement'] == '1')
{
	ob_start();

?>
<div class="alert alert-info announcement">
	<div><?php echo $luna_config['o_announcement_message'] ?></div>
</div>
<?php

	$tpl_temp = trim(ob_get_contents());
	$tpl_main = str_replace('<luna_announcement>', $tpl_temp, $tpl_main);
	ob_end_clean();
}
else
	$tpl_main = str_replace('<luna_announcement>', '', $tpl_main);
// END SUBST - <luna_announcement>
}

// START SUBST - <luna_main>
ob_start();


define('FORUM_HEADER', 1);
