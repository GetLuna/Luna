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
	header("Location: ../login.php");
$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Update']);
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
				<a href="#brand" class="list-group-item"><span class="fa fa-fw fa-moon-o"></span> New brand</a>
				<a href="#users" class="list-group-item"><span class="fa fa-fw fa-user"></span> Users</a>
				<a href="#inbox" class="list-group-item"><span class="fa fa-fw fa-paper-plane-o"></span> Inbox</a>
				<a href="#board" class="list-group-item"><span class="fa fa-fw fa-align-justify"></span> Board</a>
				<a href="#management" class="list-group-item"><span class="fa fa-fw fa-coffee"></span> Management</a>
				<a href="#backstage" class="list-group-item"><span class="fa fa-fw fa-dashboard"></span> Backstage</a>
				<a href="#theme" class="list-group-item"><span class="fa fa-fw fa-paint-brush"></span> Themes</a>
				<a href="#others" class="list-group-item">Other improvements and notes</a>
			</div>
		</div>
	</div>
	<div class="col-sm-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.0 Beta</h3>
			</div>
			<div class="panel-body panel-about">
				<a id="brand"></a><h3><span class="fa fa-fw fa-moon-o"></span>New brand</h3>
				<img class="img-responsive" src="../img/about/brand.png" />
				<p>Welcome to the first stable release of the third generation of our board software! This release officially rebrands ModernBB to Luna. We've also decided to use version 1.0 again, instead of 4.0. Now, this is everything but an interesting feature, so read on to the more awesome parts of our giant changelog:</p>
				<a id="users"></a><h3><span class="fa fa-fw fa-user"></span> User features</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Settings</h4>
						<p>Personality settings and board settings can now be saved all at once, these pages are also more to-the-point than ever before. The profile has been extended with a notification system, too!</p>
					</div>
					<div class="col-sm-6">
						<h4>A more fun design</h4>
						<p>The design of the profile has been improved to give a little bit more color to your users' profile. They need to fill in most of the fields for it, though.</p>
					</div>
					<div class="col-sm-6">
						<h4>Make it yours</h4>
						<p>As a user, you can now select your own color in the Me settings. When a theme is compatible with this feature, it can use this color throughout the board to reflect your preferences.</p>
					</div>
					<div class="col-sm-6">
						<h4>Notifications</h4>
						<p>Comments on your thread? Stuff you have to know? Notifications will help you out. Luna now shows notifications in the main interface and under Me, we have a full view of notifications.</p>
					</div>
				</div>
				<a id="inbox"></a><h3><span class="fa fa-fw fa-paper-plane-o"></span> Inbox</h3>
				<img class="img-responsive" src="../img/about/inbox.png" />
				<p>Inbox is the new private messaging system included in Luna. It allows user to connect to other users through Luna without the need to exchange an email address or any other personal data.</p>
				<a id="board"></a><h3><span class="fa fa-fw fa-align-justify"></span> Board</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Sub sections</h4>
						<p>Luna provides support for sub sections. You can add an unlimited amount of sections to a parent sections, making the structure more clear.</p>
					</div>
					<div class="col-sm-6">
						<h4>Section colors</h4>
						<p>When you're setting up a section, you can give it a color to make it stand out of the crowd, which are the other sections, in this case.</p>
					</div>
				</div>
				<h4>Emojis</h4>
				<img class="img-responsive" src="../img/about/emoji.png" />
				<p>The emoticons we introduced in ModernBB 2.0 have served well, but now, it's time to move on to the next generation: emojis. Unlike the regular emoticons, these icons don't take any bandwidth as they are a font and not an image, which makes them also ready for high DPI screens and improve performance. The Emojis also change according to your device. If you're using Windows or Windows Phone, they look like the emoticons shown above. Unlike emoticons, you can change the size of the emojis across your whole board (and they'll stay nice). Emojis will be, however, optional and are disabled by default due to old platforms like Windows 7 not completely supporting them.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>Smarter editor</h4>
						<img class="img-responsive" src="../img/about/editor.png" />
						<p>The editor will act smarter than it did before now. When adding a list, for example, it will also add the first list item. For code boxes, it adds an additional white line.</p>
					</div>
					<div class="col-sm-6">
						<h4>Sharing code</h4>
						<img class="img-responsive" src="../img/about/syntax.png" />
						<p>Do your users want to share some HTML, PHP, CSS or JavaScript? Well, Luna will show these languages nicely with a brand new syntax highlighter based on PrismJS.</p>
					</div>
				</div>
				<a id="management"></a><h3><span class="fa fa-fw fa-coffee"></span> Management</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Mainstage independent</h4>
						<p>The moderation tools are now part of the Backstage and thus no longer depending on the Mainstage. This makes the way they work more unified and theme developers don't need to worry about them, either. In the future, this will allow us to add new features more easily without disturbing theme developers.</p>
					</div>
					<div class="col-sm-6">
						<h4>Moderation tools</h4>
						<p>The moderation tools have been improved with a brand new design and additional improvements. Most of the changes were done due to the move from Mainstage to Backstage, but they also contain (a lot of) usability improvements.</p>
					</div>
					<div class="col-sm-12">
						<h4>Soft delete</h4>
						<p>Ever had to delete a post but didn't want to delete it for ever? Now you don't have to anymore. Luna introduces soft delete support. Posts or topics will be hidden from user's view and only visible for admins and moderators. They'll be marked in a different color (similar to reported posts).</p>
					</div>
				</div>
				<a id="backstage"></a><h3><span class="fa fa-fw fa-dashboard"></span> Backstage</h3>
				<img class="img-responsive" src="../img/about/backstage.png" />
				<p>The Backstage has been redesigned from scratch with an all new design and more focus on management. The Backstage has now more visual appeal due to icons. New features have jumped into the Backstage, like <i>Admin Notes</i> and more. However, we did remove the Backstage Accent feature. Sorry.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>New menu management</h4>
						<p>The old "Additional menu items" feature had to give its position up to our new, more advanced and easier to use "Menu" settings page under settings. Here, you can manage your boards menu easier than ever before.</p>
					</div>
					<div class="col-sm-6">
						<h4>Admin Notes</h4>
						<p>The redesigned Backstage index doesn't come with just a simple redesign, but with a new feature, we call "Admin notes". Here, admins can write down some important things to remember for the next time they visit.</p>
					</div>
					<div class="col-sm-6">
						<h4>Improved workflow</h4>
						<p>You can now save ranks all at once instead of having to update them one by one. Other pages have got a redesign to bring a more uniform Backstage.</p>
					</div>
					<div class="col-sm-6">
						<h4>Forum colors</h4>
						<p>Forums can now be given a color, when the theme is compatible with this, the color can be used throughout the design to give the forum its own personality.</p>
					</div>
					<div class="col-sm-6">
						<h4>Ready for smallness...</h4>
						<img class="img-responsive" src="../img/about/smallness.png" />
						<p>While the Backstage from ModernBB was already responsive and ready for your phone, we've made improvements to make your experience even better.</p>
					</div>
					<div class="col-sm-6">
						<h4>...and ready for you</h4>
						<img class="img-responsive" src="../img/about/backstagefirstrun.png" />
						<p>New to Luna? We'll give you a hand with the important settings you need to get your community ready to kick off and grow beyond! Of course, you can remove it too.</p>
					</div>
					<div class="col-sm-5">
						<h4>Backstage login</h4>
						<p>Did your theme break the login form? No worries, there now is a login form build into the Backstage, so you can change the theme back or manage your forum through there.</p>
					</div>
					<div class="col-sm-7">
						<h4>Maintenance options</h4>
						<p>We're adding a couple of new maintenance options to Luna under the new category "Maintenance" in the Backstage. You can find the contents from the Maintenance-page that used to be under Settings here, but also manage your database and other features to clean up a bit.</p>
					</div>
				</div>
				<a id="theme"></a><h3><span class="fa fa-fw fa-paint-brush"></span> Themes</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Theme Engine 6</h4>
						<p>Luna includes, what we like to call "Theme Engine 6", which is the successor to ModernBB 3.6' "Style Engine 5.2". The new core of Luna allows extended customization, without losing the ability to upgrade.</p>
					</div>
					<div class="col-sm-6">
						<h4>New developer tools</h4>
						<p>The possibilities for developing your own theme have been extended drastically! You can do whatever you want now. Luna won't force you to use Bootstrap anymore, as the choice is now up to you.</p>
					</div>
					<div class="col-sm-6">
						<h4>Child themes</h4>
						<p>Luna comes with support for child-themes. Thanks to this, themes can be based upon one another without the need to hae duplicated files for 2 themes. Luna's own Luna (parent) and Sunrise (child) themes are an example of this new behavior.</p>
					</div>
				</div>
				<h4>Luna</h4>
				<img class="img-responsive" src="../img/about/sunrise.png" />
				<p>Due to the new Theme Engine, we had to rebuild our styles anyway, so why not throw in something new and fresh? That's why you're now free to use our brand new default theme, Luna, which will replace Random. Luna uses new features from Luna to show off its capabilities. For example, Luna doesn't replace just Random, but also Awesome, Kind, Luna (the theme from ModernBB that is), Pinkie, Magic, Radical, Happy and Shy. In 1 theme, you get 12 different colorschemes available to you and your users.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>Revamped index</h4>
						<p>The index has been redesigned to replace not only the original index, but also the forum view. This is a Luna-thing, and thus, other themes can use the classic Index > Forum > Topic structure. Luna provides this all on one page, though. Also taking a step down from categories.</p>
					</div>
					<div class="col-sm-6">
						<h4>Fresh ideas</h4>
						<p>Luna will give you a refreshed experience from the ground up. Because not just the index has been redone, every page has. The result is a beautiful native experience that uses all power Luna has to provide. And as it is a first version, expect more in later updates.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<h4>Sunrise</h4>
						<p>Sunrise is a theme based on Luna, however, it uses a more classic view on forum software. This is a nice team if you want to kick off with a fresh design, yet like the old way of working with forum software better.</p>
					</div>
				</div>
				<a id="others"></a><h3>Other improvements and notes</h3>
				<div class="row">
					<div class="col-sm-6">
						<h4>Installation</h4>
						<p>We've revamped the code behind the installer to be more modern (using fancy PHP 5 stuff like classes), while you won't notice a lot in these changes, it is a step forward.</p>
					</div>
					<div class="col-sm-6">
						<h4>Security first</h4>
						<p>Not only does Luna come with security fixes, it also has some features to warn you for possible security threaths like the <code>install.php</code> file after installation and a writeabe <code>config.php</code>.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<h4>Packages</h4>
						<p><b>Bootstrap</b> has been updated from version 3.2.0 to 3.3.4.<br />
						<b>Font Awesome</b> has been updated from version 4.1.0 to 4.3.0.<br />
						<b>jQuery</b> has been updated from version 2.1.1 to 2.1.3.<br />
						<b>PrismJS</b> has been added.<br />
						<b>Core</b> has been updated from version 0.0.37.2592 to 0.7.4069.</p>
					</div>
					<div class="col-sm-6">
						<h4>Bugfixes</h4>
						<p>165 bugs have been fixed.</p>
						<h4>Security fixes</h4>
						<p>5 security issue has been fixed.</p>
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
