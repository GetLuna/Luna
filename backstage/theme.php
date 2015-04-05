<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");
if (isset($_GET['default_style'])) {
	confirm_referrer('backstage/theme.php');
	
	$default_style = htmlspecialchars($_GET["default_style"]);

	$db->query('UPDATE '.$db->prefix.'users SET style=\''.$default_style.'\' WHERE id > 0') or error('Unable to set style settings', __FILE__, __LINE__, $db->error());
	$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.$default_style.'\' WHERE conf_name = \'o_default_style\'') or error('Unable to update default style', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/theme.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Style']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('settings', 'theme');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default panel-current">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $lang['Current theme'] ?></h3>
			</div>
<?php

$current_theme = $luna_config['o_default_style'];
include FORUM_ROOT.'/themes/'.$current_theme.'/information.php';
$theme_info = new SimpleXMLElement($xmlstr);

?>
			<div class="thumbnail"><a data-toggle="modal" href="#" data-target="#<?php echo $current_theme ?>"><img src="../themes/<?php echo $current_theme ?>/logo.png" /></a></div>
			<div class="panel-footer">
				<span class="h2"><?php echo $theme_info->name; ?></span>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php printf($lang['Theme settings for'], $luna_config['o_default_style'].' '.$theme_info->version) ?></h3>
			</div>
			<div class="panel-body">
<?php

if (file_exists(FORUM_ROOT.'/themes/'.$current_theme.'/theme_settings.php')) {
	include FORUM_ROOT.'/themes/'.$current_theme.'/theme_settings.php';
} else {
	echo 'This theme has no settings available...';
}

?>
			</div>
		</div>
	</div>
</div>
<form class="form-horizontal" method="post" action="permissions.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Default style'] ?></h3>
		</div>
		<div class="panel-body">
			<p><?php echo $lang['Available themes'] ?></p>
			<fieldset>
				<div class="row">
<?php
		$styles = forum_list_styles();

		foreach ($styles as $temp) {
?>
					<?php include FORUM_ROOT.'/themes/'.$temp.'/information.php'; $theme_info = new SimpleXMLElement($xmlstr); ?> 
					<div class="col-xs-12 col-sm-6 col-md-4 style-entry">
						<div class="modal fade" id="<?php echo $temp ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $temp ?>" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title"><?php printf($lang['About style'], $theme_info->name) ?></h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-sm-8">
												<div class="thumbnail"><img src="../themes/<?php echo $temp ?>/screenshot.png" /></div>
											</div>
											<div class="col-sm-4">
												<h2><?php echo $theme_info->name; ?> <small><?php printf($lang['version'], $theme_info->version) ?></small></h2>
												<h4><?php echo $theme_info->developer; ?></h4>
												<p><?php echo $theme_info->description; ?></p>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<span class="pull-left"><?php printf($lang['Released on'], $theme_info->date) ?></span>
										<span class="pull-right">
											<?php printf($lang['Designed for'], $theme_info->minversion, $theme_info->maxversion); ?>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-style">
							<div class="thumbnail"><a data-toggle="modal" href="#" data-target="#<?php echo $temp ?>"><img src="../themes/<?php echo $temp ?>/logo.png" /></a></div>
							<div class="panel-footer">
								<span class="h2"><?php echo $theme_info->name; ?></span>
								<div class="btn-group pull-right">
									<?php
										if ($luna_config['o_default_style'] == $theme_info->name)
											echo '<a class="btn btn-primary disabled">In use</a>';
										else
											echo '<a class="btn btn-primary" href="theme.php?default_style='.$theme_info->name.'">'.$lang['Use'].'</a>';
										
										echo '<a class="btn btn-primary" data-toggle="modal" href="#" data-target="#'.$temp.'"><span class="fa fa-fw fa-info-circle"></span></a>';
									?>
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

require 'footer.php';
