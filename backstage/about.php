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
	<div class="col-sm-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Navigation</h3>
			</div>
			<div class="list-group">
				<a href="#board" class="list-group-item"><span class="fa fa-fw fa-align-justify"></span> Board</a>
				<a href="#backstage" class="list-group-item"><span class="fa fa-fw fa-dashboard"></span> Backstage</a>
				<a href="#theme" class="list-group-item"><span class="fa fa-fw fa-paint-brush"></span> Themes</a>
				<a href="#dev" class="list-group-item"><span class="fa fa-fw fa-terminal"></span> Developers</a>
				<a href="#others" class="list-group-item">Other improvements and notes</a>
			</div>
		</div>
	</div>
	<div class="col-sm-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.1 Bittersweet Shimmer</h3>
			</div>
			<div class="panel-body panel-about">
				<a id="board"></a><h3><span class="fa fa-fw fa-align-justify"></span> Board</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Night mode</h4>
						<p>Reading in a dark environment can be a pain when the design of the website you're looking at is bright. And Luna's default design is bright. So we're introducing a "Night mode", which allows your users to make the interface darker permanent, or depending on the time of day. This also works for the Backstage.</p>
					</div>
					<div class="col-sm-6">
						<h4>Contact links</h4>
						<p>When an user adds a Twitter, Facebook, Microsoft Account, e-mail, website or Google+ account to his/here profile, the add-ons in his/here profile are now clickable to give you and your users easy access to your user's accounts on these social networks.</p>
					</div>
					<div class="col-sm-6">
						<h4>Notifications 2.0</h4>
						<p>The notification system, new since Luna 1.0, has received a major revamp. The new system checks every minute for new notifications. It also allows you to mark a notification as readed or remove it completely right from the fly-out.</p>
					</div>
				</div>
				<a id="backstage"></a><h3><span class="fa fa-fw fa-dashboard"></span> Backstage</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>A personal touch</h4>
						<p>When a theme supports more then one accent color, you can choose the default color your board should have. This applies to guests and new users.</p>
					</div>
					<div class="col-sm-6">
						<h4>Backstage accents</h4>
						<p>Similar to the Mainstage design, you can now change the accent color of the Backstage. We also took the time to make a small update to the design.</p>
					</div>
					<div class="col-sm-6">
						<h4>Announcements 2.0</h4>
						<p>Announcements have been improved with a couple of new options. When a theme supports it, you can choose between 5 styles for the announcement to be displayed in. Further, you can now set a title to announcements.</p>
					</div>
					<div class="col-sm-6">
						<h4>Errors 2.0</h4>
						<p>Some errors, mostly the ones your users could run upon or the ones that simply don't need to be a problem, have been revamped to look better and give you a direct action to solve the issue, or just continue if possible.</p>
					</div>
				</div>
				<a id="theme"></a><h3><span class="fa fa-fw fa-paint-brush"></span> Themes</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Fifteen/Sunrise 1.1</h4>
						<p>We've refined our default themes based on user feedback and they look better then ever. Luna and Sunrise have been optimized to support the newest features from Luna 1.1.</p>
					</div>
					<div class="col-sm-6">
						<h4>Theme Engine <s>2.0</s> 6.1</h4>
						<p>After the major revamp from Aero, Bittersweet Shimmer continues to improve our themes with a more simplified system, better support for accents, night mode and so much more.</p>
					</div>
				</div>
				<a id="dev"></a><h3><span class="fa fa-fw fa-terminal"></span> Developers</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Notification API</h4>
						<p>We're introducing a brand new Notification API. This API will allow you to create new notification more easly from different locations. You can read all about it in the <a href="http://getluna.org/docs/notification.php">documentation</a>.</p>
					</div>
					<div class="col-sm-6">
						<h4>Language 2.0</h4>
						<p>This new version of Luna introduces a new way to translate Luna, you can now use applications like Poedit to translate Luna more easly. This means that previous translations won't work at all, and we're sorry for that, but it is for the better.</p>
					</div>
				</div>
				<a id="others"></a><h3>Other improvements and notes</h3>
				<div class="row">
					<div class="col-sm-12">
						<h4>And a lot of other small changes</h4>
						<p>However, since Luna 1.1 has its focus on refinements, there are also a lot of other small improvements all over the board (no pun intended). This includes better performance, more developer options, small visual changes and fixes, PHP 7 support, and so much more.</p>
					</div>
					<div class="col-sm-6">
						<h4>Packages</h4>
						<p><b>Core</b> has been updated from version 1.0.4275 to 1.1.4768.<br />
						<b>Bootstrap</b> has been updated from version 3.3.4 to 3.3.5.<br />
						<b>Lunicons</b> version 0.0.1 has been removed.</p>
					</div>
					<div class="col-sm-6">
						<h4>Bug fixes</h4>
						<p>16 bugs have been fixed.</p>
						<h4>Security fixes</h4>
						<p>1 security issue has been fixed.</p>
					</div>
				</div>
				<h4>Luna 1.1.1 &middot Build 4768</h4>
				<div class="row">
					<div class="col-sm-6">
						<p>
							<span class="label label-primary">Update 1</span> The forum moderation interface has been improved<br />
							<span class="label label-primary">Update 1</span> Luna now supports SQLite 3<br />
							<span class="label label-primary">Update 1</span> Post counts now decreases when posts are removed<br />
							<span class="label label-primary">Update 1</span> You can now disable night mode system wide<br />
							<span class="label label-primary">Update 1</span> The language files have been cleaned up
						</p>
					</div>
					<div class="col-sm-6">
						<p>
							<span class="label label-primary">Update 1</span> Bootstrap has been update to version 3.3.5<br />
							<span class="label label-primary">Update 1</span> The login modal is now centered on small screens<br />
							<span class="label label-primary">Update 1</span> You can now accent colors mode system wide<br />
							<span class="label label-primary">Update 1</span> 3 additional bug fixes
						</p>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<p>Luna is developed by the <a href="http://getluna.org/">Luna Group</a>. Copyright 2013-2015. Released under the GPLv3 license.</p>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
