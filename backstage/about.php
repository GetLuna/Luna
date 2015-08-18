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
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Update']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'about');
	
	?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.0 Aero</h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.0.8</span></p><h2>Aero Update 8</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>The Slow ring is no longer available</li>
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>If the Nightly ring is selected, Luna won't check for updates automaticaly</li>
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>The extended changelog has been removed</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Bootstrap has been updated to version 3.3.5 everywhere</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes an issue that caused config.php to be recognized incorrectly</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.7</span></p><h2>Aero Update 7</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>SQLite 3 support</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>PHP 7 support</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The moderation interface has been redesigned</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Removing posts will cause the postcount to decrease</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Bootstrap has been updated to 3.3.5</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 2 bugs</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.6</span></p><h2>Aero Update 6</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>May 2015 brand update</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes a security issue in the Backstage</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.5</span></p><h2>Aero Update 5</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved deletion interface</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes a bug when posting a new comment</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.4</span></p><h2>Aero Update 4</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>jQuery has been updated to version 2.1.4</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Renames the "Black" color scheme to "Dark grey"</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.3</span></p><h2>Aero Update 3</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Warning when Slow ring is out-of-date</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Changed behavior when loging in</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved Profile Settings interface</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes inconsistent icon usage in Backstage</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue when canceling quotes</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.2</span></p><h2>Aero Update 2</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved search results when searching for topics</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved design for Luna and Sunrise</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Updates installation behavior</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>"Send to" now has the focus when writing a comment</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue that caused empty mails to be send to users</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue that resets theme after updating</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0.1</span></p><h2>Aero Update</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Emoticons now have tooltips</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Removes obsolete queries in the update script</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved design for Luna and Sunrise</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Updates installation behavior</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The index now shows the 30 latest active topics</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improves tab order</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes support for SSL with CDN usage</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes design flaw in the editor</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes design flaw in the Backstage when extensions are installed</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Removes out-of-date info from the Backstage</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes "Do not show again" bug in First Run</li>
						</ul>
					</div>
					<div class="container">
						<p class="meta"><span class="release-version">1.0</span></p><h2>Aero</h2>
						<ul class="changes">
							<li><div class="change-label-container"></div><i>Initial release</i></li>
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
