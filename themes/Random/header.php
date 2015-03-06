<?php

require ('header.php');

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo generate_page_title($page_title, $p) ?></title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="include/css/prism.css" />
		<script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<script src="include/js/prism.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
// Allow childs
load_css();
include ('themes/'.$luna_config['o_default_style'].'/style.php');

if (!defined('FORUM_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";
?>
	</head>
	<body>
		<?php if ($luna_user['is_guest']): require load_page('login.php'); endif; ?>
		<div id="main">
			<div id="header">
				<div class="navbar navbar-inverse navbar-static-top">
					<div class="container">
						<a class="navbar-brand" href="index.php"><?php echo $menu_title ?></a>
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-primary-collapse">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<div class="navbar-primary-collapse navbar-collapse collapse">
							<ul class="nav navbar-nav"><?php echo implode("\n\t\t\t\t", $links); ?></ul>
							<?php if ($luna_config['o_header_search']): ?>
							<ul class="nav navbar-nav hidden-xs">
								<form id="search" class="navbar-form" method="get" action="search.php?section=simple">
									<fieldset>
										<input type="hidden" name="action" value="search" />
										<div class="input-group">
											<input class="form-control" type="text" name="keywords" placeholder="Search in posts" maxlength="100" />
											<span class="input-group-btn">
												<button class="btn btn-default btn-search" type="submit" name="search" accesskey="s" />
													<span class="fa fa-fw fa-search"></span>
												</button>
											</span>
										</div>
									</fieldset>
								</form>
							</ul>
							<?php endif; ?>
							<ul class="nav navbar-nav navbar-right">
								<?php echo $usermenu; ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="container">
					<h1 class="hidden-xs board-title"><a href="index.php"><?php echo $menu_title ?></a></h1>
					<div class="description">
						<p><?php echo $luna_config['o_board_desc'] ?></p>
					</div>
<?php
if (!$luna_user['is_guest']) {
	if (!empty($forum_actions)) {
		$page_statusinfo[] = '<li><span>'.implode(' &middot; ', $forum_actions).'</span></li>';
	}

	if (!empty($topic_actions)) {
		$page_statusinfo[] = '<li><span>'.implode(' &middot; ', $topic_actions).'</span></li>';
	}

	if ($luna_user['is_admmod']) {
		if ($luna_config['o_report_method'] == '0' || $luna_config['o_report_method'] == '2') {
			$result_header = $db->query('SELECT 1 FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

			if ($db->result($result_header))
				$page_statusinfo[] = '<li class="reportlink"><span><strong><a href="backstage/reports.php">'.$lang['New reports'].'</a></strong></span></li>';
		}

		if ($luna_config['o_maintenance'] == '1')
			$page_statusinfo[] = '<li class="maintenancelink"><span><strong><a href="backstage/settings.php#maintenance">'.$lang['Maintenance mode enabled'].'</a></strong></span></li>';
	}

	if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1') {
		$page_topicsearches[] = '<a href="search.php?action=show_new" title="'.$lang['Show new posts'].'">New</a>';
	}
}

// Quick searches
if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1') {
	$page_topicsearches[] = '<a href="search.php?action=show_recent" title="'.$lang['Show active topics'].'">Active</a>';
	$page_topicsearches[] = '<a href="search.php?action=show_unanswered" title="'.$lang['Show unanswered topics'].'">Unanswered</a>';
}

?><div class="page-status"><?php

// The status information
if (is_array($page_statusinfo)) { ?>
	<ul class="conl">
		<?php echo implode("\n\t\t\t\t", $page_statusinfo) ?>
	</ul>
<?php }

// Generate quicklinks
if (!empty($page_topicsearches)) { ?>
	<ul class="conr">
		<li><span><?php echo implode(' &middot; ', $page_topicsearches) ?></span></li>
	</ul>
<?php }

?></div><?php

if ($luna_user['g_read_board'] == '1' && $luna_config['o_announcement'] == '1') {
?>
					<div class="alert alert-info announcement">
						<div><?php echo $luna_config['o_announcement_message'] ?></div>
					</div>
<?php } ?>
				</div>
			</div>
			<div class="container">