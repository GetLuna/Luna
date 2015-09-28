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
$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Update', 'luna'));
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'about');
	
	?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.2 Cornflower Blue</h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.2 Preview 1</span></p><h2>Cornflower Blue Preview 1</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Sixteen is the new default theme</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Mark topics as solved</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>You can now set any color as forum colors</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>Support for non-Latin characters</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Extended language support for syntax highlighter</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>New tools to clean up notifications</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improvements for non-Javascript usage</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Updated and incorporated components</li>
							<li><div class="change-label-container"><em class="change-label change-note">Changed</em></div>Default avatar settings have been updated</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 3 bugs</li>
						</ul>
					</div>
				</section>
			</div>
			<div class="panel-footer">
				<p>Luna is developed by the <a href="http://getluna.org/">Luna Group</a>. Copyright 2013-2015. Released under the GPLv3 license.</p>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
