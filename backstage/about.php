<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
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
				<h3 class="panel-title">About Luna 1.3 Denim Preview</h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.3 Preview 3</span></p><h2>Denim</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Sunrise is a new default theme</li>
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>You can now mark a thread as important</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>Major naming convention updates</li>
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>Timezone settings have been reworked</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Daylight Saving is now handled by Luna</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Fifteen now has an updated sidebar</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Improved mobile interface for Fifteen and Backstage</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>CSS files have been rewritten</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 4 bugs</li>
							<hr />
							<li><div class="change-label-container"><em class="change-label change-system">System</em></div>Support for Luna 2.0 Fallow build 6126</li>
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
