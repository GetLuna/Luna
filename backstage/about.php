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
				<h3 class="panel-title">About Luna 1.4 Emerald</h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.4 Preview 1</span></p><h2>Emerald</h2>
						<ul class="changes">
							<li><div class="change-label-container"><em class="change-label change-new">New</em></div>Fifteen has received a fully reimagned design based on Airalin</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>The editor will now put items under a button if the screen becomes to small</li>
							<li><div class="change-label-container"><em class="change-label change-improved">Improved</em></div>Advanced search has an improved UI</li>
							<li><div class="change-label-container"><em class="change-label change-removed">Removed</em></div>The editor no longer has an emoticon menu</li>
							<li><div class="change-label-container"><em class="change-label change-fixed">Fixed</em></div>Fixes 2 bugs</li>
							<hr />
							<li><div class="change-label-container"><em class="change-label change-note">Note</em></div>Night mode has yet to be implemented in Fifteen</li>
							<li><div class="change-label-container"><em class="change-label change-note">Note</em></div>The light grey accent has yet to be implemented correctly in Fifteen</li>
							<li><div class="change-label-container"><em class="change-label change-note">Note</em></div>"Forces accents" has yet to be implemented in Fifteen</li>
							<li><div class="change-label-container"><em class="change-label change-note">Note</em></div>Sunrise has multiple visual issues in this Preview</li>
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
