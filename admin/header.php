<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache'); // For HTTP/1.0 compatibility

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Load the template
if (defined('PUN_ADMIN_CONSOLE'))
	$tpl_file = 'admin.tpl';
else if (defined('PUN_HELP'))
	$tpl_file = 'help.tpl';
else
	$tpl_file = 'main.tpl';

if (file_exists(FORUM_ROOT.'style/'.$pun_user['style'].'/'.$tpl_file))
{
	$tpl_file = FORUM_ROOT.'style/'.$pun_user['style'].'/'.$tpl_file;
	$tpl_inc_dir = FORUM_ROOT.'style/'.$pun_user['style'].'/';
}
else
{
	$tpl_file = FORUM_ROOT.'include/template/'.$tpl_file;
	$tpl_inc_dir = FORUM_ROOT.'include/user/';
}

$tpl_main = file_get_contents($tpl_file);

// START SUBST - <pun_include "*">
preg_match_all('%<pun_include "([^"]+)">%i', $tpl_main, $pun_includes, PREG_SET_ORDER);

foreach ($pun_includes as $cur_include)
{
	ob_start();
	$file_info = pathinfo($cur_include[1]);
	
    if (!in_array($file_info['extension'], array('php', 'php4', 'php5', 'inc', 'html', 'txt'))) // Allow some extensions  
       error(sprintf($lang_common['Pun include extension'], htmlspecialchars($cur_include[0]), basename($tpl_file), htmlspecialchars($file_info['extension'])));  
         
    if (strpos($file_info['dirname'], '..') !== false) // Don't allow directory traversal  
       error(sprintf($lang_common['Pun include directory'], htmlspecialchars($cur_include[0]), basename($tpl_file))); 

	// Allow for overriding user includes, too.
	if (file_exists($tpl_inc_dir.$cur_include[1]))  
		require $tpl_inc_dir.$cur_include[1];  
	else if (file_exists(FORUM_ROOT.'include/user/'.$cur_include[1]))  
		require FORUM_ROOT.'include/user/'.$cur_include[1];  
	else
		error(sprintf($lang_common['Pun include error'], pun_htmlspecialchars($cur_include[0]), basename($tpl_file)));

	$tpl_temp = ob_get_contents();
	$tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
	ob_end_clean();
}
// END SUBST - <pun_include "*">


// START SUBST - <pun_language>
$tpl_main = str_replace('<pun_language>', $lang_common['lang_identifier'], $tpl_main);
// END SUBST - <pun_language>


// START SUBST - <pun_content_direction>
$tpl_main = str_replace('<pun_content_direction>', $lang_common['lang_direction'], $tpl_main);
// END SUBST - <pun_content_direction>


// START SUBST - <pun_head>
ob_start();

// Define $p if it's not set to avoid a PHP notice
$p = isset($p) ? $p : null;

// Is this a page that we want search index spiders to index?
if (!defined('PUN_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";

?>
<title><?php echo generate_page_title($page_title, $p) ?></title>
<link rel="stylesheet" type="text/css" href="style/Air.css" />
<link rel="stylesheet" type="text/css" href="style/base_admin.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script src="js/bootstrap.js"></script>
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
				alert('"' + required_fields[elem.name] + '" <?php echo $lang_common['required field'] ?>');
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

// JavaScript tricks for IE6 and older
echo '<!--[if lte IE 6]><script type="text/javascript" src="style/imports/minmax.js"></script><![endif]-->'."\n";

if (isset($page_head))
	echo implode("\n", $page_head)."\n";

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_head>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_head>


// START SUBST - <body>
if (isset($focus_element))
{
	$tpl_main = str_replace('<body onload="', '<body onload="document.getElementById(\''.$focus_element[0].'\').elements[\''.$focus_element[1].'\'].focus();', $tpl_main);
	$tpl_main = str_replace('<body>', '<body onload="document.getElementById(\''.$focus_element[0].'\').elements[\''.$focus_element[1].'\'].focus()">', $tpl_main);
}
// END SUBST - <body>

$is_admin = $pun_user['g_id'] == PUN_ADMIN ? true : false;

?>
<div class="navbar navbar-static-top">
  <div class="navbar-inner">
  <div class="container">
    <a class="brand" href="admin_index.php">ModernBB</a>
    <ul class="nav">
      <li><a href="admin_index.php">Dashboard</a></li>
      <li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		  Content <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		  <?php if ($is_admin) { ?><li><a href="admin_forums.php">Forums</a></li><?php }; ?>
		  <?php if ($is_admin) { ?><li><a href="admin_categories.php">Categories</a></li><?php }; ?>
		  <?php if ($is_admin) { ?><li><a href="admin_censoring.php">Censoring</a></li><?php }; ?>
		  <li><a href="admin_reports.php">Reports</a></li>
		</ul>
	  </li>
      <li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		  Users <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		  <li><a href="admin_users.php">Users</a></li>
		  <?php if ($is_admin) { ?><li><a href="admin_ranks.php">Ranks</a></li><?php }; ?>
		  <?php if ($is_admin) { ?><li><a href="admin_groups.php">Groups</a></li><?php }; ?>
		  <?php if ($is_admin) { ?><li><a href="admin_permissions.php">Permissions</a></li><?php }; ?>
		  <li><a href="admin_bans.php">Bans</a></li>
		</ul>
	  </li>
      <?php if ($is_admin) { ?><li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		  Settings <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		  <li><a href="admin_options.php">Global</a></li>
		  <li><a href="admin_email.php">Email</a></li>
		  <li><a href="admin_maintenance.php">Maintenance</a></li>
		</ul>
	  </li><?php }; ?>
      <?php if ($is_admin) { ?><li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		  Extensions <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
<?php

	// See if there are any plugins
	$plugins = forum_list_plugins($is_admin);

	// Did we find any plugins?
	if (!empty($plugins))
	{

		foreach ($plugins as $plugin_name => $plugin)
			echo "\t\t\t\t\t".'<li class="'.(($page == $plugin_name) ? 'active' : '').'"><a href="admin_loader.php?plugin='.$plugin_name.'">'.str_replace('_', ' ', $plugin).'</a></li>'."\n";

	} else {
		echo '<li class="nav-header">No plugins</li>';
	}
}; ?>
        </ul>
      </li>
      </ul>
    </div>
  </div>
</div>
<?php
//Update checking
$latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/version2.0/version.txt'));
if (preg_match("/^[0-9.-]{1,}$/", $latest_version)) {
	if (FORUM_VERSION < $latest_version) { ?>
		<div class="alert alert-info">
          <h4>ModernBB v<?php echo $latest_version ?> available</h4>
          We found a new version of ModernBB on the web. Your board is out-of-date and we recommend you to update right away!<br />
          <a class="btn btn-success">Download v<?php echo $latest_version ?></a>
          <a class="btn btn-success">Changelog</a>
        </div>
<?php
	}
}

// START SUBST - <pun_page>
$tpl_main = str_replace('<pun_page>', htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php')), $tpl_main);
// END SUBST - <pun_page>


// START SUBST - <pun_main>
ob_start();


define('PUN_HEADER', 1);
