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
				<h3 class="panel-title">Welcome to the Luna Preview</h3>
			</div>
			<div class="panel-body">
				<p>Welcome to the Luna Preview. It's great that you are using this software. This preview is meant to show what's coming next to Luna.</p>
				<p>Feedback is very important for us, it would be great if you give us some. Feedback can be about everything: bugs that need to be fixed, features you would like to see, etc. Be sure to check our shiplist (see links below) before you request a feature or fill a bug, as it might be noted already.</p>
			</div>
			<div class="panel-footer">
				<div class="btn-group">
					<a class="btn btn-info" href="https://github.com/ModernBB/Luna/issues/863">Luna 1.0 "Aero" on GitHub</a>
				</div>
			</div>
		</div>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Updates</h3>
			</div>
			<div class="list-group">
				<a href="#updates" class="list-group-item">Updates</a>
				<a href="#p0" class="list-group-item">Preview 0</a>
				<a href="#p0u1" class="list-group-item">Preview 0 Update 1</a>
				<a href="#p0u2" class="list-group-item">Preview 0 Update 2</a>
				<a href="#p0u3" class="list-group-item">Preview 0 Update 3</a>
				<a href="#p0u4" class="list-group-item">Preview 0 Update 4</a>
				<a href="#p1" class="list-group-item">Preview 1</a>
				<a href="#p2" class="list-group-item">Preview 2</a>
				<a href="#p2u1" class="list-group-item">Preview 2 Update 1</a>
				<a href="#p2u2" class="list-group-item">Preview 2 Update 2</a>
				<a href="#p3" class="list-group-item">Preview 3</a>
				<a href="#p3u1" class="list-group-item">Preview 3 Update 1</a>
			</div>
		</div>
	</div>
	<div class="col-sm-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.0 Preview</h3>
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
				<p>The emoticons we introduced in ModernBB 2.0 have served well, but now, it's time to move on to the next generation: emojis. Unlike the regular emoticons, these icons don't take any bandwidth as they are a font and not an image, which makes them also ready for high DPI screens and improve performance. The Emojis also change according to your device. If you're using Windows or Windows Phone, they look like the emoticons shown above. Unlike emoticons, you can change the size of the emojis across your whole board (and they'll stay nice).</p>
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
						<p>Luna comes with support for child-themes. Thanks to this, themes can be based upon one another without the need to hae duplicated files for 2 themes. Luna's own Sunrise (parent) and Sunset (child) themes are an example of this new behavior.</p>
					</div>
				</div>
				<h4>Sunrise</h4>
				<img class="img-responsive" src="../img/about/sunrise.png" />
				<p>Due to the new Theme Engine, we had to rebuild our styles anyway, so why not throw in something new and fresh? That's why you're now free to use our brand new default theme, Sunrise, which will replace Random. Sunrise uses new features from Luna to show off its capabilities. For example, Sunrise doesn't replace just Random, but also Awesome, Kind, Luna (the theme from ModernBB that is), Pinkie, Magic, Radical, Happy and Shy. In 1 theme, you get 12 different colorschemes available to you and your users.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>Revamped index</h4>
						<p>The index has been redesigned to replace not only the original index, but also the forum view. This is a Sunrise-thing, and thus, other themes can use the classic Index > Forum > Topic structure. Sunrise provides this all on one page, though. Also taking a step down from categories.</p>
					</div>
					<div class="col-sm-6">
						<h4>Fresh ideas</h4>
						<p>Sunrise will give you a refreshed experience from the ground up. Because not just the index has been redone, every page has. The result is a beautiful native experience that uses all power Luna has to provide. And as it is a first version, expect more in later updates.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<h4>Sunset</h4>
						<p>Sunset is a theme based on Sunrise, however, it uses a more classic view on forum software. This is a nice team if you want to kick off with a fresh design, yet like the old way of working with forum software better.</p>
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
						<p><b>Bootstrap</b> has been updated from version 3.2.0 to 3.3.2.<br />
						<b>Font Awesome</b> has been updated from version 4.1.0 to 4.3.0.<br />
						<b>jQuery</b> has been updated from version 2.1.1 to 2.1.3.<br />
						<b>PrismJS</b> has been added.<br />
						<b>Core</b> has been updated from version 0.0.37.2592 to 0.3.3802.</p>
					</div>
					<div class="col-sm-6">
						<h4>Bugfixes</h4>
						<p>70 bugs have been fixed.</p>
						<h4>Security fixes</h4>
						<p>5 security issue has been fixed.</p>
					</div>
				</div>
				<hr />
				<a id="updates"></a><h3>Luna 1.0 Updates</h3>
				<span class="change-list">
					<h4><a id="p0"></a>Preview 0 &middot; Build 3112</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3112</span> <i>Initial release</i>
							</p>
						</div>
					</div>
					<h4><a id="p0u1"></a>Preview 0 Update 1 &middot; Build 3136</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3136</span> The index now displays the latest topic<br />
								<span class="label label-primary">3136</span> The "Board stats" have been updated with a new design<br />
								<span class="label label-primary">3136</span> Fixes a security vulnerability in redirects
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3136</span> The index now displays the amount of topics and posts<br />
								<span class="label label-primary">3136</span> 1 bugfix
							</p>
						</div>
					</div>
					<h4><a id="p0u2"></a>Preview 0 Update 2 &middot; Build 3137</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3137</span> An issue with the installer has been fixed
							</p>
						</div>
					</div>
					<h4><a id="p0u3"></a>Preview 0 Update 3 &middot; Build 3231</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3231</span> Drastically improved design on all pages<br />
								<span class="label label-primary">3231</span> Advanced Search has been improved with a new interface<br />
								<span class="label label-primary">3231</span> First Run now acts like a sidebar and control panel<br />
								<span class="label label-primary">3231</span> New zFeatures have been added, and are disabled<br />
								<span class="label label-primary">3231</span> Start of development of Reading List
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3231</span> Foundations for Profile and Me have been added<br />
								<span class="label label-primary">3231</span> First Run now acts like a sidebar and control panel<br />
								<span class="label label-primary">3231</span> Small improvements to the editor<br />
								<span class="label label-primary">3231</span> Bootstrap has been updated to version 3.3.0<br />
								<span class="label label-primary">3231</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<h4><a id="p0u4"></a>Preview 0 Update 4 &middot; Build 3132</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3232</span> Fixes a security issue
							</p>
						</div>
					</div>
					<h4><a id="p1"></a>Preview 1 &middot; Build 3361</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3361</span> The Me Personality settings have been redesigned<br />
								<span class="label label-primary">3361</span> The editor has been improved for lists and codeboxes<br />
								<span class="label label-primary">3361</span> Panels in the Me section are accessible again (avatar, etc.)<br />
								<span class="label label-primary">3361</span> Backstage has been given a small redesign<br />
								<span class="label label-primary">3361</span> The moderation tools have been improved<br />
								<span class="label label-primary">3361</span> Any action the updater does can give an error message<br />
								<span class="label label-primary">3361</span> Icons provide visual aide in the Backstage<br />
								<span class="label label-primary">3361</span> First Run is now back to its previous panel design<br />
								<span class="label label-primary">3361</span> Start of development for support of notifications<br />
								<span class="label label-primary">3361</span> Improvements to the Theme engine have been made<br />
								<span class="label label-primary">3361</span> Improvements to Sunrise<br />
								<span class="label label-primary">3361</span> Improved support for large touchscreens<br />
								<span class="label label-primary">3361</span> The description of multiple fields have been improved
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3361</span> You can now set a color for your profile<br />
								<span class="label label-primary">3361</span> Backstage pages are updated for interface guidelines<br />
								<span class="label label-primary">3361</span> You can now save all ranks at once instead of one by one<br />
								<span class="label label-primary">3361</span> Inbox has been added as a private messaging system<br />
								<span class="label label-primary">3361</span> Some obsolete features have been removed<br />
								<span class="label label-primary">3361</span> Redesigned experimental index page<br />
								<span class="label label-primary">3361</span> Luna supports Syntax Highlighting<br />
								<span class="label label-primary">3361</span> The Activity tracker has been added to Me<br />
								<span class="label label-primary">3361</span> A new installation and update system<br />
								<span class="label label-primary">3361</span> Bootstrap has been updated to version 3.3.1<br />
								<span class="label label-primary">3361</span> Multiple interface improvements<br />
								<span class="label label-primary">3361</span> Luna will now generate longer passwords<br />
								<span class="label label-primary">3361</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<h4><a id="p2"></a>Preview 2 &middot; Build 3478</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3478</span> Revamped interface<br />
								<span class="label label-primary">3478</span> The navbar has been split in 2 new navbars<br />
								<span class="label label-primary">3478</span> The board stats are now displayed on every page<br />
								<span class="label label-primary">3478</span> The index has been redesigned with new features<br />
								<span class="label label-primary">3478</span> The footer now displays the board's copyright<br />
								<span class="label label-primary">3478</span> Support for sub sections has been added<br />
								<span class="label label-primary">3478</span> Improved Backstage interface for small screens<br />
								<span class="label label-primary">3478</span> The Notification icon now works<br />
								<span class="label label-primary">3478</span> Backstage now has release notes included<br />
								<span class="label label-primary">3478</span> Emoticons have been replaced with Emojis
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3478</span> The main navbar now contains a search box<br />
								<span class="label label-primary">3478</span> Online list is now hidden under "users online"<br />
								<span class="label label-primary">3478</span> The notification button is now always visible<br />
								<span class="label label-primary">3478</span> Profile and Me have been improved with better UX<br />
								<span class="label label-primary">3478</span> First Run has been added to the Backstage<br />
								<span class="label label-primary">3478</span> zFeatures clean up<br />
								<span class="label label-primary">3478</span> Notifications has been added to Me<br />
								<span class="label label-primary">3478</span> The code behind the installer has been revamped<br />
								<span class="label label-primary">3478</span> Emojis list in Help has been improved<br />
								<span class="label label-primary">3478</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<h4><a id="p2u1"></a>Preview 2 Update 1 &middot; Build 3573</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3573</span> Major changes to the profile system<br />
								<span class="label label-primary">3573</span> Activity has been removed<br />
								<span class="label label-primary">3573</span> "Tools" is a new page under "Users"<br />
								<span class="label label-primary">3573</span> User settings has been completely revamped<br />
								<span class="label label-primary">3573</span> Brand update<br />
								<span class="label label-primary">3573</span> You can now disable the "Back to top" link<br />
								<span class="label label-primary">3573</span> Emoji's are now part of the Editor interface<br />
								<span class="label label-primary">3573</span> Mainstage First Run has new actions<br />
								<span class="label label-primary">3573</span> Responsive footer has been improved<br />
								<span class="label label-primary">3573</span> zSettings has been dropped
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3573</span> "Settings" is now an option in the user menu<br />
								<span class="label label-primary">3573</span> jQuery has been updated to version 2.1.3<br />
								<span class="label label-primary">3573</span> All user settings can be saved at once<br />
								<span class="label label-primary">3573</span> A lot more coding conventions<br />
								<span class="label label-primary">3573</span> New interface for profile, settings and notifications<br />
								<span class="label label-primary">3573</span> Inbox now has it's own icon in the menubar<br />
								<span class="label label-primary">3573</span> Improved visual appearance of editor<br />
								<span class="label label-primary">3573</span> Backstage's First Run can be disabled<br />
								<span class="label label-primary">3573</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<h4><a id="p2u2"></a>Preview 2 Update 2 &middot; Build 3660</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3660</span> Backstage will check of config is writeable<br />
								<span class="label label-primary">3660</span> More improvements to Backstage for small screens<br />
								<span class="label label-primary">3660</span> The notification fly-out is now optional<br />
								<span class="label label-primary">3660</span> You can now change the size of emojis<br />
								<span class="label label-primary">3660</span> The copyright notice now can be altered by admins<br />
								<span class="label label-primary">3660</span> Moderation tools are now a Backstage feature<br />
								<span class="label label-primary">3660</span> The profile now shows the user's URL again<br />
								<span class="label label-primary">3660</span> Multiple design improvements to Sunrise<br />
								<span class="label label-primary">3660</span> Help page design has been improved<br />
								<span class="label label-primary">3660</span> Improved design for the "Mark as read" button<br />
								<span class="label label-primary">3660</span> The thread design has been majorly improved<br />
								<span class="label label-primary">3660</span> Index no longer sorts on sticky when no forum is set<br />
								<span class="label label-primary">3660</span> More work on coding conventions has been done<br />
								<span class="label label-primary">3660</span> Improved design for the moderation tools<br />
								<span class="label label-primary">3660</span> Dummy notifications have been removed
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3660</span> Optimization of multiple code snippets<br />
								<span class="label label-primary">3660</span> The search bar in themes can be disabled now<br />
								<span class="label label-primary">3660</span> The emoji dropdown in the editor has been improved<br />
								<span class="label label-primary">3660</span> Statistics in the footer can be disabled<br />
								<span class="label label-primary">3660</span> Core has been removed from the theme engine<br />
								<span class="label label-primary">3660</span> Improvements to the new profile settings<br />
								<span class="label label-primary">3660</span> Notifications have been split from the profile file<br />
								<span class="label label-primary">3660</span> The index no longer shows invalid messages<br />
								<span class="label label-primary">3660</span> Post.php now shows the correct title for the page<br />
								<span class="label label-primary">3660</span> "Moderate" link is added to forum view<br />
								<span class="label label-primary">3660</span> The language files have been cleaned up<br />
								<span class="label label-primary">3660</span> General code cleanup<br />
								<span class="label label-primary">3660</span> New user menu in the Backstage to match the Mainstage's<br />
								<span class="label label-primary">3660</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<h4><a id="p3"></a>Preview 3 &middot; Build 3754</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3754</span> Moved topics aren't displayed on the index anymore<br />
								<span class="label label-primary">3754</span> Improved forum display for moved topics<br />
								<span class="label label-primary">3754</span> The update dialogue now has a similar design as login<br />
								<span class="label label-primary">3754</span> Bootstrap has been updated to version 3.3.2<br />
								<span class="label label-primary">3754</span> Updated design for the user list<br />
								<span class="label label-primary">3754</span> Font Awesome has been updated to version 4.3.0<br />
								<span class="label label-primary">3754</span> Users can no longer select a per-user style<br />
								<span class="label label-primary">3754</span> Emojis have a default size of 16px instead of 14px<br />
								<span class="label label-primary">3754</span> Themes now can set a parent to inherit their design<br />
								<span class="label label-primary">3754</span> Support to display categories in themes<br />
								<span class="label label-primary">3754</span> Threads now have visual help for their status<br />
								<span class="label label-primary">3754</span> Inbox has been revamped<br />
								<span class="label label-primary">3754</span> A first implementation of soft delete has been added<br />
								<span class="label label-primary">3754</span> Component clean-up<br />
								<span class="label label-primary">3754</span> Improved performance
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3754</span> Backstage now has it's own login form<br />
								<span class="label label-primary">3754</span> Improved edit interface<br />
								<span class="label label-primary">3754</span> A security issue with the installer has been fixed<br />
								<span class="label label-primary">3754</span> Major improvements to the responsive design<br />
								<span class="label label-primary">3754</span> Sunset is added as a build-in style<br />
								<span class="label label-primary">3754</span> The Theme settings have been improved<br />
								<span class="label label-primary">3754</span> Improvements to the topic view have been made<br />
								<span class="label label-primary">3754</span> More improvements to Theme engine 6<br />
								<span class="label label-primary">3754</span> Luna now asks to remove install.php after installation<br />
								<span class="label label-primary">3754</span> Major improvements to the database<br />
								<span class="label label-primary">3754</span> New notifications have been added<br />
								<span class="label label-primary">3754</span> A lot of coding convention improvements<br />
								<span class="label label-primary">3754</span> More extensive use of icons throughout the interface<br />
								<span class="label label-primary">3754</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<h4><a id="p3u1"></a>Preview 3 Update 1 &middot; Build 3802</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3802</span> You can now undo a soft delete<br />
								<span class="label label-primary">3802</span> Managing notifications is now possible again<br />
								<span class="label label-primary">3802</span> Maintenance and Prune are split from each other<br />
								<span class="label label-primary">3802</span> The notifications page has been improved<br />
								<span class="label label-primary">3802</span> Split logic from markup for help and Inbox<br />
								<span class="label label-primary">3802</span> Revamped conversation managment in Inbox<br />
								<span class="label label-primary">3802</span> Search results have an improved design
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3802</span> Maintenance is a new category in the Backstage<br />
								<span class="label label-primary">3802</span> Database has been added under "Maintenance"<br />
								<span class="label label-primary">3802</span> Further usability improvements to Inbox<br />
								<span class="label label-primary">3802</span> More parts of Inbox are now part of the Theme Engine<br />
								<span class="label label-primary">3802</span> Design improvements to Sunrise and Sunset<br />
								<span class="label label-primary">3802</span> Multiple bugfixes
							</p>
						</div>
					</div>
				</span>
			</div>
			<div class="panel-footer">
				<p>Luna is developed by the <a href="http://getluna.org/">Luna Group</a>. Copyright 2013-2015. Released under the GPLv3 license.</p>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
