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
				<h3 class="panel-title"><?php printf(__('About Luna %s %s', 'luna'), Version::LUNA_VERSION, Version::LUNA_CODE_NAME_SEM) ?></h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<p class="meta"><span class="release-version">1.4 Preview 3</span></p><h2>Emerald</h2>
						<ul class="changes">
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Fifteen has received a fully reimagned design based on Airalin', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Sunrise has also been reimagned based on the new Fifteen look', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now add custom CSS to the theme you\'re using', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Luna\'s default placeholder avatar can now be replaced', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now upload a header background to use in your theme and the Backstage', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now respond on Inbox messages from the message view', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Fifteen, Sunrise and Backstage have new Pink, Dark Red and Beige accents', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Luna now has a Dutch translation included by default', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('Luna now uses salts and SHA-512 to save passwords', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('We now include animate.css with Luna', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Users can now disable or enable First Run from their profile', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('The editor will now put items under a button if the screen becomes to small', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('When you have a notification, the icon will animate', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Advanced search has an improved UI', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('About can now be translated to other languages', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Improved Backstage UI and night mode', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('The editor no longer has an emoticon menu', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('The code base no longer supports PHP 5.2 anymore', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-fixed"><?php _e('Fixed', 'luna') ?></em></div><?php _e('Fixes 7 bugs', 'luna') ?></li>
							<hr />
							<li><div class="stater"><em class="state state-note"><?php _e('Note', 'luna') ?></em></div><?php _e('Sunrise has multiple visual issues in this Preview', 'luna') ?></li>
						</ul>
					</div>
				</section>
			</div>
			<div class="panel-footer">
				<p><?php printf(__('Luna is developed by the %s. Copyright %s. Released under the GPLv2 license.', 'luna'), '<a href="http://getluna.org/">Luna Group</a>', '2013-2016') ?></p>
			</div>
		</div>
	</div>
</div>
<?php
__('users', 'luna');
__('threads', 'luna');
__('comments', 'luna');

require 'footer.php';
