<?php

/*
 * Copyright (C) 2013-2016 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
define('LUNA_SECTION', 'backstage');
define('LUNA_PAGE', 'about');

require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");

require 'header.php';

?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php printf(__('About Luna %s %s', 'luna'), Version::LUNA_VERSION, Version::LUNA_CODE_NAME) ?></h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<h2 class="clearfix"><span class="version-name">Emerald <small>2.0 Beta 1</small></span></h2>
						<ul class="changes">
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Fifteen has received a fully reimagned design based on Airalin', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Sunrise has also been reimagned based on the new Fifteen look', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now add custom CSS to the theme you\'re using', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Luna\'s default placeholder avatar can now be replaced', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Notifications now get marked as read when clicked', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('New notification tools on the Notification page in the profile', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Comments can now have admin notes that are displayed in threads', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now upload a header background to use in your theme and the Backstage', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now manage your boards favicon in Settings', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('You can now respond on Inbox messages from the message view', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Reports now trigger a notifications for admins and moderators', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('New threads in a subscribed board now trigger notifications', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Fifteen, Sunrise and Backstage have new Pink, Dark Red and Beige accents', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Luna now has a Dutch translation included by default', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-new"><?php _e('New', 'luna') ?></em></div><?php _e('Maintenance mode now shows a warning in the Backstage header', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('Luna now uses salts and SHA-512 to save passwords', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('Luna now includes full "Right to Left"-support', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('Components now live in their own folder in the Luna root', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('Debug mode can now be enabled with one line in the config file', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-system"><?php _e('System', 'luna') ?></em></div><?php _e('Bootstrap and jQuery have been updated to the latest version', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-note"><?php _e('Changed', 'luna') ?></em></div><?php _e('Revamped search forms for users and bans, including responsive design', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-note"><?php _e('Changed', 'luna') ?></em></div><?php _e('The Backstage has a whole new structure', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-note"><?php _e('Changed', 'luna') ?></em></div><?php _e('Luna\'s file structure has been updated', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('You can now see what\'s a subforum and edit more settings in board management', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Users can now disable or enable First Run from their profile', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('The editor will now put items under a button if the screen becomes to small', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('When you have a notification, the icon will animate', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Advanced search has an improved UI', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('About can now be translated to other languages', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('You can now directly manage a reported comment from the Backstage', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Improved Backstage UI and night mode', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Multiple improvements on accessibility', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-improved"><?php _e('Improved', 'luna') ?></em></div><?php _e('Luna now provides a description meta tag to prevent weird search results', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('The editor no longer has an emoticon menu', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('The code base no longer supports PHP 5.2', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('Notifications can no longer be marked as read/trashed from the fly-out', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('You can no longer manage the database from the Backstage', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-removed"><?php _e('Removed', 'luna') ?></em></div><?php _e('Tools to add new users have been removed', 'luna') ?></li>
                            <li><div class="stater"><em class="state state-fixed"><?php _e('Fixed', 'luna') ?></em></div><?php _e('Fixes 41 bugs', 'luna') ?></li>
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
__('views', 'luna');

require 'footer.php';
