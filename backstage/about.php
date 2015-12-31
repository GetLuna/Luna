<?php

/*
 * Copyright (C) 2013-2016 Luna
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
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>You can now access your subscriptions from your profile</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Labels now appear in a consistent order everywhere</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improves strings in the language files</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Small visual improvements to Sunrise 2</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Removed multiple instances of trailing whitespaces</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved behavior on small viewports</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The notification fly-out now show notifications from new to old</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The profile will now hide avatar and signature settings when disabled</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Luna now hides search fields when an user doesn't have permission to use them</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Subscriptions settings won't show up when subscriptions aren't available</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>The "solved" and "important" label now appear search results</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>New installations will no longer assign a wrong announcement type</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Closed and moved thread will now show both icons</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>The leading zero for minutes will now be displayed in notifications</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>The updater will no longer attempt to create the same column twice</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes multiple issue with labels in the moderation view</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>When logging in with a banned account, the error will be displayed correctly</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Changing a threads state won't cause an error anymore in some occasions</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Breadcrumbs will now have a correct markup in reports</li>
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
				<p>Luna is developed by the <a href="http://getluna.org/">Luna Group</a>. Copyright 2013-2016. Released under the GPLv2 license.</p>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
