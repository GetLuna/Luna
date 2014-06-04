<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

if (isset($_GET['default_style'])) {
	confirm_referrer('backstage/style.php');
	
	$default_style = htmlspecialchars($_GET["default_style"]);

	$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.$default_style.'\' WHERE conf_name = \'o_default_style\'') or error('Unable to update default style', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/style.php?saved=true');
}

if (isset($_GET['force_default'])) {
	confirm_referrer('backstage/style.php');
	
	$force_default = htmlspecialchars($_GET["force_default"]);
	
	$db->query('UPDATE '.$db->prefix.'users SET style=\''.$force_default.'\' WHERE id > 0') or error('Unable to set style settings', __FILE__, __LINE__, $db->error());
}

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Style']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('style');

?>
<h2><?php echo $lang['Style'] ?></h2>
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="permissions.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Default style'] ?></h3>
        </div>
        <div class="panel-body">
			<p><?php echo $lang['Default style help'] ?></p>
            <fieldset>
				<div class="row">
<?php
		$styles = forum_list_styles();

		foreach ($styles as $temp)
		{
?>
					<?php include FORUM_ROOT.'/style/'.$temp.'/information.php'; $style_info = new SimpleXMLElement($xmlstr); ?> 
					<div class="col-xs-12 col-sm-6 col-md-4 style-entry">
						<div class="modal fade" id="<?php echo $temp ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $temp ?>" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title"><?php printf($lang['About style'], $style_info->name) ?></h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-sm-8">
												<div class="thumbnail"><img src="../style/<?php echo $temp ?>/screenshot.png" /></div>
											</div>
											<div class="col-sm-4">
												<h2><?php echo $style_info->name; ?> <small><?php printf($lang['version'], $style_info->version) ?></small></h2>
												<h4><?php echo $style_info->developer; ?></h4>
												<p><?php echo $style_info->description; ?></p>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<span class="pull-left"><?php printf($lang['Released on'], $style_info->date) ?></span><span class="pull-right"><?php printf($lang['Designed for'], $style_info->minversion, $style_info->maxversion) ?></span>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-style">
							<div class="thumbnail"><a data-toggle="modal" href="#" data-target="#<?php echo $temp ?>"><img src="../style/<?php echo $temp ?>/logo.png" /></a></div>
							<div class="panel-footer">
								<span class="h2"><?php echo $style_info->name; ?></span>
								<div class="btn-group pull-right">
									<?php
										if ($luna_config['o_default_style'] == $style_info->name)
											echo '<a class="btn btn-primary disabled">'.$lang['Default'].'</a>';
										else
											echo '<a class="btn btn-primary" href="style.php?default_style='.$style_info->name.'">'.$lang['Set as default'].'</a>';
									?>
									<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
										<span class="caret"></span>
										<span class="sr-only">Toggle Dropdown</span>
									</a>
									<ul class="dropdown-menu" role="menu">
										<?php
											echo '<li><a data-toggle="modal" href="#" data-target="#'.$temp.'">'.$lang['About'].'</a></li>';
											echo '<li><a href="style.php?force_default='.$style_info->name.'">'.$lang['Force style'].'</a></li>';
										?>
									</ul>
								</div>
							</div>
						</div>
					</div>
<?php				
		}
?>
				</div>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
