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
				<h3 class="panel-title">About Luna 1.1 Bittersweet Shimmer</h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.1.4</span></p><h2>Bittersweet Shimmer Update 4</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes a bug with the accent function</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issues with responsive and night mode in Fifteen and Sunrise</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issues with plural translations</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issues with Inbox not marking messages as read</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes a security issue</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.1.3</span></p><h2>Bittersweet Shimmer Update 3</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved logic for load_css() function</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes accent resetting when saving profile and accents disabled</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes wrong link in "Forgotten password" mails</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.1.2</span></p><h2>Bittersweet Shimmer Update 2</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>The Slow ring is no longer available</li>
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>If the Nightly ring is selected, Luna won't check for updates automaticaly</li>
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>The extended changelog has been removed</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Bootstrap has been updated to version 3.3.5 everywhere</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Error walls now have a style again</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes a corrupt translation for the "Upload avatar" modal</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes an issue that caused config.php to be recognized incorrectly</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.1.1</span></p><h2>Bittersweet Shimmer Update</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>SQLite 3 support</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The moderation interface has been redesigned</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>You can now disable the night mode system</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Removing posts will cause the postcount to decrease</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>You can now force the accent color</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Bootstrap has been updated to 3.3.5</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 3 bugs</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.1</span></p><h2>Bittersweet Shimmer</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Night mode</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Backstage now supports accents</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Translations now use gettext</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>PHP 7 support</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>You can now click on contact links</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Fifteen and Sunrise 1.1</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Notifications are now real-time</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>You can now force the accent color</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Announcements has been extended with many new features</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Errors have been improved</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 13 bugs</li>
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
