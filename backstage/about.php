<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");
$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Update', 'luna'));
define('LUNA_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'about');

	?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.3 Denim</h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.3.1</span></p><h2>Denim Update 1</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Labels now appear in a consistent order everywhere</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improves strings in the language files</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Small visual improvements to Sunrise 2</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Removed multiple instances of trailing whitespaces</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue that caused the "solved" and "important" label to not appear in search results</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue where new installations would assign a wrong announcement type</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue where a closed and moved thread would only show one of both icons</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue where the leading zero for minutes wouldn't be displayed in notifications</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes issue where the updater would attempt twice to create the same column</li>
						</ul>
					</div>
				</section>

				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.3</span></p><h2>Denim</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Sunrise has received a fully reimagned design</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>You can now mark a thread as important</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Optional center and size markup tags</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>Major naming convention updates</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>Timezone settings have been reworked</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Daylight Saving is now handled by Luna</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Fifteen now has an updated sidebar</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved mobile interface for Fifteen and Backstage</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The behavior of some markup tags have been changed</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Embedded videos are now fully responsive</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>CSS files have been rewritten to reflect our new coding conventions</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Multiple improvement for High-DPI screens have been applied</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Bootstrap and Font Awesome have been updated to their latest versions</li>
							<li><div class="change-label-container"><em class="change-label change-note">Changed</em></div>The license has been changed from GPLv3 to GPLv2</li>
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>You can no longer determine the size of embedded videos</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 21 bugs</li>
						</ul>
					</div>
				</section>
			</div>
			<div class="panel-footer">
				<p>Luna is developed by the <a href="http://getluna.org/">Luna Group</a>. Copyright 2013-2015. Released under the GPLv2 license.</p>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
