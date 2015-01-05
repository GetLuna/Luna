<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

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
				<p>Welcome to Luna Preview 2. It's great that you are using this software. This preview is ment to show what's coming next to Luna.</p>
				<p>Feedback is very important for us, it would be great if you give us some. Feedback can be about everything: bugs that need to be fixed, features you would like to see, etc. Be sure to check our shiplist (see links below) before you request a feature or fill a bug, as it might be noted already.</p>
			</div>
			<div class="panel-footer">
				<div class="btn-group">
					<a class="btn btn-info" href="https://github.com/ModernBB/Luna/issues/863">Luna 1.0 "Aero" on GitHub</a>
				</div>
			</div>
		</div>
		<div class="list-group">
			<a href="#brand" class="list-group-item"><span class="fa fa-fw fa-moon-o"></span> New brand</a>
			<a href="#users" class="list-group-item"><span class="fa fa-fw fa-user"></span> Users</a>
			<a href="#inbox" class="list-group-item"><span class="fa fa-fw fa-paper-plane-o"></span> Inbox</a>
			<a href="#board" class="list-group-item"><span class="fa fa-fw fa-align-justify"></span> Board</a>
			<a href="#management" class="list-group-item"><span class="fa fa-fw fa-coffee"></span> Management</a>
			<a href="#backstage" class="list-group-item"><span class="fa fa-fw fa-dashboard"></span> Backstage</a>
			<a href="#theme" class="list-group-item"><span class="fa fa-fw fa-paint-brush"></span> Theme engine</a>
			<a href="#themes" class="list-group-item"><span class="fa fa-fw fa-pencil"></span> Themes</a>
		</div>
		<div class="list-group">
			<a href="#others" class="list-group-item">Other improvements and notes</a>
			<a href="#updates" class="list-group-item">Updates</a>
		</div>
	</div>
	<div class="col-sm-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">About Luna 1.0 Preview 2</h3>
			</div>
			<div class="panel-body panel-about">
				<a id="brand"><h3><span class="fa fa-fw fa-moon-o"></span>New brand</h3></a>
				<img class="img-responsive" src="../img/about/brand.png" />
				<p>Welcome to the first stable release of the third generation of our board software! This release officialy rebrands ModernBB to Luna. We've also decided to use version 1.0 again, instead of 4.0. Now, this is everything but an intresting feature, so read on to the more awesome parts of our giant changelog:</p>
				<a id="users"><h3><span class="fa fa-fw fa-user"></span> User features</h3></a>
				<div class="row">
					<div class="col-sm-6">
						<h4>Profile</h4>
						<p>The profile has been completely revamped with a new design and a new codebase. Personality settings and board settings can now be saved all at once, these pages are also more to-the-point then ever before. The profile has been extended with a notification system, too!</p>
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
				<a id="inbox"><h3><span class="fa fa-fw fa-paper-plane-o"></span> Luna Inbox</h3></a>
				<img class="img-responsive" src="../img/about/inbox.png" />
				<p>Inbox is the new private messaging system included in Luna. It allows user to connect to other users through Luna without the need to exchange an email address or any other personal data.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>Contacts</h4>
						<p>Users can create contacts, this is list to make sending Inbox messages easier. With Contacts, users can choose to block messages from other users.</p>
					</div>
					<div class="col-sm-6">
						<h4>Lists</h4>
						<p>Lists are groups of users, it will allow users to send a message to more then 1 person much easier then needing to find them one by one.</p>
					</div>
				</div>
				<a id="board"><h3><span class="fa fa-fw fa-align-justify"></span> Board</h3></a>
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
				<p>The emoticons we introduced in ModernBB 2.0 have served well, but now, it's time to move on to the next generation: emojis. Unlike the regular emoticons, these icons don't take any bandwhite as they are a font and not an image, which makes them also ready for high DPI screens and improve performance. The Emojis also change according to your device. If you're using Windows or Windows Phone, they look like the emoticons shown above. Unlike emoticons, you can change the size of the emojis across your whole board (and they'll stay nice).</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>Smarter editor</h4>
						<img class="img-responsive" src="../img/about/editor.png" />
						<p>The editor will act smarther than it did before now. When adding a list, for example, it will also add the first list item. For code boxes, it adds an additional white line.</p>
					</div>
					<div class="col-sm-6">
						<h4>Sharing code</h4>
						<img class="img-responsive" src="../img/about/syntax.png" />
						<p>Do your users want to share some HTML, PHP, CSS or JavaScript? Well, Luna will show these languages nicely with a brand new syntax highlighter based on PrismJS.</p>
					</div>
				</div>
				<a id="management"><h3><span class="fa fa-fw fa-coffee"></span> Management</h3></a>
				<div class="row">
					<div class="col-sm-6">
						<h4>Moderation tools</h4>
						<p>The moderation tools have been improved with a brand new design and additional improvements.</p>
					</div>
				</div>
				<a id="backstage"><h3><span class="fa fa-fw fa-dashboard"></span> Backstage</h3></a>
				<img class="img-responsive" src="../img/about/backstage.png" />
				<p>The Backstage has been redesigned from scratch with an all new design and more focus on management. The Backstage has now more visual appeal due to icons. New features have jumped into the Backstage, like <i>Admin Notes</i> and more. However, we did remove the Backstage Accent feature. Sorry.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>New menu management</h4>
						<p>The old "Additional menu items" feature had to give its position up to our new, more advanced and easier to use "Menu" settings page under settings. Here, you can manage your boards menu easier then ever before.</p>
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
						<p>While to Backstage from ModernBB was already responsive and ready for your phone, we've made improvements to make your experience even beter.</p>
					</div>
					<div class="col-sm-6">
						<h4>..and ready for you</h4>
						<img class="img-responsive" src="../img/about/backstagefirstrun.png" />
						<p>New to Luna? We'll give you a hand with the important settings you need to get your community ready to kick off and grow beyond! Of course, you can remove it too.</p>
					</div>
				</div>
				<a id="theme"><h3><span class="fa fa-fw fa-paint-brush"></span> Theme engine</h3></a>
				<div class="row">
					<div class="col-sm-6">
						<h4>Theme Engine 6</h4>
						<p>Luna includes, what we like to call "Theme Engine 6", which is the successor to ModernBB 3.6' "Style Engine 5.2". The new core of Luna allows extended customization, without losing the ability to upgrade.</p>
					</div>
					<div class="col-sm-6">
						<h4>New developer tools</h4>
						<p>The possibilities for developing your own theme have been extended drasticaly! You can do whatever you want now. Luna won't force you to use Bootstrap anymore, as the choise is now up to you.</p>
					</div>
				</div>
				<a id="themes"><h3><span class="fa fa-fw fa-pencil"></span> Themes</h3></a>
				<h4>Sunrise</h4>
				<img class="img-responsive" src="../img/about/sunrise.png" />
				<p>Due to the new Theme Engine, we had to rebuild our styles anyway, so why not throw in something new and fresh? That's why you're now free to use our brand new default theme, Sunrise, which will replace Random. Sunrise uses new features from Luna to show of it's capabilities. For example, Sunrise doesn't replace just Random, but also Awesome, Kind, Luna (the theme from ModernBB that is), Pinkie, Magic, Radical, Happy and Shy. In 1 theme, you get 12 different colorschemes available to you and your users.</p>
				<div class="row">
					<div class="col-sm-6">
						<h4>Revamped index</h4>
						<p>The index has been redesigned to replace not only the orignal index, but also the forum view. This is a Sunrise-thing, and thus, other themes can use the classic Index > Forum > Topic structure. Sunrise provides this all on one page, through. Also taking a step down from categories.</p>
					</div>
					<div class="col-sm-6">
						<h4>Fresh ideas</h4>
						<p>Sunrise will give you a refreshed experience from the ground up. Because not just the index has been redone, every page has. The result is a beautiful native experience that uses all power Luna has to provide. And as it is a first version, expect more in later updates.</p>
					</div>
				</div>
				<a id="others"><h3>Ohter improvements and notes</h3></a>
				<div class="row">
					<div class="col-sm-6">
						<h4>Installation</h4>
						<p>We've revamped the code behind the installer to be more modern (using fancy PHP 5 stuff like classes), while you won't notice a lot in these changes, it is a step forward.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
					<h4>Packages</h4>
						<p><b>Bootstrap</b> has been updated from version 3.2.0 to 3.3.1.<br />
						<b>Font Awesome</b> has been updated from version 4.1.0 to 4.2.0.<br />
						<b>jQuery</b> has been updated from version 2.1.1 to 2.1.3.<br />
						<b>PrismJS</b> has been added.<br />
						<b>Core</b> has been updated from version 0.0.36.2563 to 0.2.36xx.</p>
					</div>
					<div class="col-sm-6">
						<h4>Bugfixes</h4>
						<p>45 bugs have been fixed.</p>
						<h4>Security fixes</h4>
						<p>3 security issue has been fixed.</p>
					</div>
				</div>
				<hr />
				<a id="updates"><h3>Luna 1.0 Updates</h3></a>
				<span class="change-list">
					<h4>Preview 0 (version 0.0.40.2900-0.0.3232)</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-info">3112</span> <i>Initial release</i>
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-success">3136</span> The index now displays the latest topic<br />
								<span class="label label-success">3136</span> The "Board stats" have been updated with a new design<br />
								<span class="label label-success">3136</span> Fixes a security vulnerability in redirects
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-success">3136</span> The index now displays the amount of topics and posts<br />
								<span class="label label-success">3136</span> 1 bugfix
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-danger">3137</span> An issue with the installer has been fixed
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-warning">3231</span> Drasticaly improved design on all pages<br />
								<span class="label label-warning">3231</span> Advanced Search has been improved with a new interface<br />
								<span class="label label-warning">3231</span> First Run now acts like a sidebar and control panel<br />
								<span class="label label-warning">3231</span> New zFeatures have been added, and are disabled<br />
								<span class="label label-warning">3231</span> Start of developiment of Reading List
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-warning">3231</span> Founcations for Profile and Me have been added<br />
								<span class="label label-warning">3231</span> First Run now acts like a sidebar and control panel<br />
								<span class="label label-warning">3231</span> Small improvements to the editor<br />
								<span class="label label-warning">3231</span> Bootstrap has been updated to version 3.3.0<br />
								<span class="label label-warning">3231</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-default">3232</span> Fixes a security issue
							</p>
						</div>
					</div>
					<h4>Preview 1 (version 0.0.3233-0.1.3361)</h4>
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
								<span class="label label-primary">3361</span> The description of mutliple fields have been improved
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-primary">3361</span> You can now set a color for your profile<br />
								<span class="label label-primary">3361</span> Backstage pages are updated for interface guidelines<br />
								<span class="label label-primary">3361</span> You can now save all ranks at once instead of one by one<br />
								<span class="label label-primary">3361</span> Inbox has been added as a private messaging system<br />
								<span class="label label-primary">3361</span> Some obsolete features have been removed<br />
								<span class="label label-primary">3361</span> Redesigned experminental index page<br />
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
					<h4>Preview 2 (version 0.1.3362-0.2.36xx)</h4>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-info">3478</span> Revamped interface<br />
								<span class="label label-info">3478</span> The navbar has been split in 2 new navbars<br />
								<span class="label label-info">3478</span> The board stats are now displayed on every page<br />
								<span class="label label-info">3478</span> The index has been redesigned with new features<br />
								<span class="label label-info">3478</span> The footer now displays the board's copyright<br />
								<span class="label label-info">3478</span> Support for sub sections has been added<br />
								<span class="label label-info">3478</span> Improved Backstage interface for small screens<br />
								<span class="label label-info">3478</span> The Notification icon now works<br />
								<span class="label label-info">3478</span> Backstage now has release notes included<br />
								<span class="label label-info">3478</span> Emoticons have been replaced with Emojis
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-info">3478</span> The main navbar now contains a search box<br />
								<span class="label label-info">3478</span> Online list is now hidden under "users online"<br />
								<span class="label label-info">3478</span> The notification button is now always visible<br />
								<span class="label label-info">3478</span> Profile and Me have been improved with better UX<br />
								<span class="label label-info">3478</span> First Run has been added to the Backstage<br />
								<span class="label label-info">3478</span> zFeatures clean up<br />
								<span class="label label-info">3478</span> Notifications has been added to Me<br />
								<span class="label label-info">3478</span> The code behind the installer has been revamped<br />
								<span class="label label-info">3478</span> Emojis list in Help has been improved<br />
								<span class="label label-info">3478</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-success">3573</span> Major changes to the profile system<br />
								<span class="label label-success">3573</span> Actitiy has been removed<br />
								<span class="label label-success">3573</span> "Tools" is a new page under "Users"<br />
								<span class="label label-success">3573</span> User settings has been completely revamped<br />
								<span class="label label-success">3573</span> Brand update<br />
								<span class="label label-success">3573</span> You can now disable the "Back to top" link<br />
								<span class="label label-success">3573</span> Emoji's are now part of the Editor interface<br />
								<span class="label label-success">3573</span> Mainstage First Run has new actions<br />
								<span class="label label-success">3573</span> Responsive footer has been improved<br />
								<span class="label label-success">3573</span> zSettings has been dropped
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-success">3573</span> "Settings" is now an option in the user menu<br />
								<span class="label label-success">3573</span> jQuery has been updated to version 2.1.3<br />
								<span class="label label-success">3573</span> All user settings can be saved at once<br />
								<span class="label label-success">3573</span> More coding conventions<br />
								<span class="label label-success">3573</span> New interface for profile, settings and notifications<br />
								<span class="label label-success">3573</span> Inbox now has it's own icon in the menubar<br />
								<span class="label label-success">3573</span> Improved visual appearance of editor<br />
								<span class="label label-success">3573</span> Backstage's First Run can be disabled<br />
								<span class="label label-success">3573</span> Multiple bugfixes
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p>
								<span class="label label-danger">36xx</span> Backstage will check of config is writeable<br />
								<span class="label label-danger">36xx</span> More improvements to Backstage for small screens<br />
								<span class="label label-danger">36xx</span> The notification fly-out is now optional<br />
								<span class="label label-danger">36xx</span> You can now change the size of emojis<br />
								<span class="label label-danger">36xx</span> The copyright notice now can be altered by admins
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<span class="label label-danger">36xx</span> Optimalization of multiple code snippets<br />
								<span class="label label-danger">36xx</span> The search bar in themes can be disabled now<br />
								<span class="label label-danger">36xx</span> The emoji dropdown in the editor has been improved<br />
								<span class="label label-danger">36xx</span> Statistics in the footer can be disabled<br />
								<span class="label label-danger">36xx</span> Multiple bugfixes
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
