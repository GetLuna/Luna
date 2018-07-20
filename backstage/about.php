<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
define('LUNA_SECTION', 'backstage');
define('LUNA_PAGE', 'about');

require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: login.php");
    exit;
}

require 'header.php';

?>
<div class="row">
	<div class="col-12">
        <div class="card">
            <h5 class="card-header">
                <?php printf(__('About Luna %s %s', 'luna'), Version::LUNA_VERSION, Version::LUNA_CODE_NAME) ?>
            </h5>
            <div class="card-body">
                <section class="release-notes">
                    <h2><span class="version-name">Fluorescent Blue Preview 5 <small>2.1-alpha.5</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Typography has been added as a new theme', 'luna') ?></p>
                    <p><?php _e('All censored words can now be edited and saved at once', 'luna') ?></p>
                    <p><?php _e('Emoji can now be edited and saved at once', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Emoji shortcodes are now case insensitive', 'luna') ?></p>
                    <p><?php _e('Luna now uses modern emoji shortcodes for new installations and adds them to old installations', 'luna') ?></p>
                    <p><?php _e('Maintenance mode now throws a HTTP/503 code instead of HTTP/200', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Bootstrap has been updated from version 4.1.1 to 4.1.2', 'luna') ?></p>
                    <p><?php _e('Font Awesome has been updated from version 5.1.0 to 5.1.1', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('Typography is now the default theme for new installations', 'luna') ?></p>
                    <p><?php _e('More emoji have been remapped to better represent their text-version', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Luna no longer returns the password when a registration error occures', 'luna') ?></p>
                    <p><?php _e('<b>ALPHA</b> Fixes a number of issues with Fifteen and Sunrise', 'luna') ?></p>
                    <p><?php _e('<b>ALPHA</b> Bootstrap Javascript is now loaded correctly locally', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exclamation-triangle"></i> <?php _e('Known issues', 'luna') ?></h4>
                    <p><?php _e('Fifteen and Sunrise have multiple visual issues', 'luna') ?></p>
                    <p><?php _e('Fifteen and Sunrise do not properly work due to mismatching APIs', 'luna') ?></p>
                    <p><?php _e('Luna will reset the theme to Typography', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Fluorescent Blue Preview 4 <small>2.1-alpha.4</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Known issues are now listed in the Backstage under "About"', 'luna') ?></p>
                    <p><?php _e('Syntax highlighting now supports JSON and TypeScript and extended support for PHP', 'luna') ?></p>
                    <p><?php _e('Backstage now defines its accent colors in a JSON file', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Font Awesome 5 will now also be loaded from a CDN when CDN load is enabled', 'luna') ?></p>
                    <p><?php _e('The Backstage and installer now use Bootstrap 4.1', 'luna') ?></p>
                    <p><?php _e('Fifteen and Sunrise now use Bootstrap 4.1', 'luna') ?></p>
                    <p><?php _e('Upgrading will update the cookie bar url if the old default value is still used', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Bootstrap has been updated from version 3.3.7 to 4.1.1', 'luna') ?></p>
                    <p><?php _e('Prism has been updated from version 14.0 to 15.0', 'luna') ?></p>
                    <p><?php _e('Font Awesome has been updated from version 5.0.13 to 5.1.0', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('Some emoji have been remapped to better represent their text-version', 'luna') ?></p>
                    <p><?php _e('When pruning threads, pruning pinned threads is no longer enabled by default', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('The default user group can now be changed again', 'luna') ?></p>
                    <p><?php _e('Enabling debug mode now works correctly for PostgreSQL', 'luna') ?></p>
                    <p><?php _e('Updating to Luna 2.0 no longer causes \'o_custom_css\' to start with "NULL" as value', 'luna') ?></p>
                    <p><?php _e('Fixes a PHP error with receivers in Inbox not being countable', 'luna') ?></p>
                    <p><?php _e('The paper plane icons in Inbox are now properly alligned', 'luna') ?></p>
                    <p><?php _e('<b>ALPHA</b> Bootstrap\'s JavaScript is now loaded correctly', 'luna') ?></p>
                    <p><?php _e('<b>ALPHA</b> Enabling CDN loading does no longer break the Backstage', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Fluorescent Blue Preview 3 <small>2.1-alpha.3</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Custom emoji can now be added under Settings > Emoji', 'luna') ?></p>
                    <p><?php _e('Themes now have to be installed through Settings > Theme', 'luna') ?></p>
                    <p><?php _e('You can now remove a theme from the Backstage', 'luna') ?></p>
                    <p><?php _e('Themes can now declare which features they support', 'luna') ?></p>
                    <p><?php _e('Features that are not supported by the current theme show a warning', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Themes now use theme.json to define their details', 'luna') ?></p>
                    <p><?php _e('Improved consistency in the use of "email"', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Backstage now uses the page name in the page title', 'luna') ?></p>
                    <p><?php _e('The LESS Backstage styling has been moved to SCSS', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('"Theme" has been renamed "Appearance"', 'luna') ?></p>
                    <p><?php _e('Theme selection has been moved from Appearance to Theme', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-trash-alt"></i> <?php _e('Removed', 'luna') ?></h4>
                    <p><?php _e('You can no longer change the size of emoji', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('<b>ALPHA</b> Accents in Fifteen and Sunrise now work again', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Fluorescent Blue Preview 2 <small>2.1-alpha.2</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('You can now pick a Font Awesome style for forum icons', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Spoilers now use the Bootstrap\'s collapse plugin', 'luna') ?></p>
                    <p><?php _e('Improved database management', 'luna') ?></p>
                    <p><?php _e('Further refinements to the new Backstage design', 'luna') ?></p>
                    <p><?php _e('Night mode has better contrast and darker design', 'luna') ?></p>
                    <p><?php _e('The sidebar in threads now takes less vertical space on small screens', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Fifteen and Sunrise are now written in SCSS', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('<b>ALPHA</b> Backstage accent settings no longer trigger a warning', 'luna') ?></p>
                    <p><?php _e('<b>ALPHA</b> $luna_config is now used instead of $config for some settings, as it should be', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Fluorescent Blue Preview 1 <small>2.1-alpha.1</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Luna will now get Bootstrap and jQuery from a CDN by default', 'luna') ?></p>
                    <p><?php _e('Font Awesome Pro 5 is now supported', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Luna now uses your native system font instead of Segoe UI and Open Sans', 'luna') ?></p>
                    <p><?php _e('Improved navbar for small devices', 'luna') ?></p>
                    <p><?php _e('Tables will now work better on small devices', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Font Awesome has been updated to version 5.0.13', 'luna') ?></p>
                    <p><?php _e('jQuery has been updated to version 3.3.1', 'luna') ?></p>
                    <p><?php _e('Rewrites Backstage style in LESS', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('Improved Backstage design with faster navigation', 'luna') ?></p>
                    <p><?php _e('Improvements to the Luna coding conventions', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-trash-alt"></i> <?php _e('Removed', 'luna') ?></h4>
                    <p><?php _e('It is no longer possible to enable smilies', 'luna') ?></p>
                    <p><?php _e('Support for update rings has been removed', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 12 <small>2.0.12</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Support for more modern emoji shortcode like Luna 2.1', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Maintenance mode now throws a HTTP/503 code instead of HTTP/200', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('Emoji have been remapped to better represent their text-version and match Luna 2.1', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Luna no longer returns the password when a registration error occures', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 11 <small>2.0.11</small></span></h2>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes the incorrect language file versions being included', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 10 <small>2.0.10</small></span></h2>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Upgrading will update the cookie bar url if the old default value is still used', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('The default user group can now be changed again', 'luna') ?></p>
                    <p><?php _e('Enabling debug mode now works correctly for PostgreSQL', 'luna') ?></p>
                    <p><?php _e('Updating to Luna 2.0 no longer causes \'o_custom_css\' to start with "NULL" as value', 'luna') ?></p>
                    <p><?php _e('Fixes a PHP error with receivers in Inbox not being countable', 'luna') ?></p>
                    <p><?php _e('The paper plane icons in Inbox are now properly alligned', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 9 <small>2.0.9</small></span></h2>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Site descriptions can now be up to 300 characters long instead of 255', 'luna') ?></p>
                    <p><?php _e('Improved errors for search queries', 'luna') ?></p>
                    <p><?php _e('Adds a number of files to Git that GitHub likes you to have', 'luna') ?></p>
                    <p><?php _e('Generated downloads no longer contain files you don\'t need', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Further improvements for PHP 7.2 support', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('Updates some string to reflect current situations', 'luna') ?></p>
                    <p><?php _e('"Check for updates" has been updated to match the new site and repo structure', 'luna') ?></p>
                    <p><?php _e('Updates references to GetLuna.org to match new site', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Underscores are now properly escaped in LIKE-queries', 'luna') ?></p>
                    <p><?php _e('Fixes an issue where SMTP data could not be set due to a character limit', 'luna') ?></p>
                    <p><?php _e('Calculations now work properly in debug mode regardless of localization', 'luna') ?></p>
                    <p><?php _e('There is no longer a hardcoded "said" string', 'luna') ?></p>
                    <p><?php _e('The forum title will no longer appear to far to the left when active', 'luna') ?></p>
                    <p><?php _e('A space has been added to quote titles before "wrote"', 'luna') ?></p>
                    <p><?php _e('"spoiler" is now properly quoted in the parser', 'luna') ?></p>
                    <p><?php _e('Night mode now correctly colors the border in the release notes', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 8 <small>2.0.8</small></span></h2>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Font Awesome has been updated to version 4.7.0', 'luna') ?></p>
                    <p><?php _e('Prism has been updated to version 14.0', 'luna') ?></p>
                    <p><?php _e('Add support for PHP 7.2', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes an issue where splitting a thread would reset solved to null', 'luna') ?></p>
                    <p><?php _e('Fixes an issue where Backstage would use Mainstage errors', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 7 <small>2.0.7</small></span></h2>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes an issue where resetting the password would fail', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 6 <small>2.0.6</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Hidden threads now have a yellow indicator on the right hand side', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('The Mainstage won\'t show notifications twice anymore', 'luna') ?></p>
                    <p><?php _e('Some BBCodes would break with certain uses of hard returns', 'luna') ?></p>
                    <p><?php _e('Fixes content of spoiler-tags being unreadable in night mode', 'luna') ?></p>
                    <p><?php _e('Editing a user will no longer change the accent color of that user', 'luna') ?></p>
                    <p><?php _e('Fixes sorting of the userlist on the second page', 'luna') ?></p>
                    <p><?php _e('Fixes moderator forums being unavailable in the profile admin settings', 'luna') ?></p>
                    <p><?php _e('Fixes an undisclosed security issue', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 5 <small>2.0.5</small></span></h2>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Simple searches now list results descending', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes notification links being broken', 'luna') ?></p>
                    <p><?php _e('Fixing missing ids for threads in the bell icon in search results', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 4 <small>2.0.4</small></span></h2>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Bootstrap 3.3.6 has been updated to version 3.3.7', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes an SQL injection with notifications', 'luna') ?></p>
                    <p><?php _e('Fixes a memory leak in PHP 5.6 on XAMPP', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 3 <small>2.0.3</small></span></h2>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Password reset would fail to reset the password in some situations', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 2 <small>2.0.2</small></span></h2>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('Info messages no longer feel like an error if they\'re not', 'luna') ?></p>
                    <p><?php _e('Custom CSS is now loaded before emoji CSS to allow custom web fonts', 'luna') ?></p>
                    <p><?php _e('Improved error design in Inbox', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('jQuery has been updated to version 2.2.4', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-trash-alt"></i> <?php _e('Removed', 'luna') ?></h4>
                    <p><?php _e('The quick reply box in Inbox has been replaced with a Reply-button', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('An issue with signing in has been resolved', 'luna') ?></p>
                    <p><?php _e('Fixes the password reset link which was broken in certain situations', 'luna') ?></p>
                    <p><?php _e('Fixes wrong avatars in Inbox conversations', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald Update 1 <small>2.0.1</small></span></h2>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('When no threads or forums exist, the messages are shown in a more polished style', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Font Awesome has been updated to version 4.6.3', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-trash-alt"></i> <?php _e('Removed', 'luna') ?></h4>
                    <p><?php _e('Some of the left over Luna Preview elements hav been removed', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes an issue where an URL in a mail would be mising', 'luna') ?></p>
                    <p><?php _e('Fixes an issue where removing a forum would fail', 'luna') ?></p>
                    <p><?php _e('Fixes a number of issues with plugins', 'luna') ?></p>
                    <p><?php _e('Fixes a nesting bug in the board page', 'luna') ?></p>
                </section>
                <section class="release-notes">
                    <h2><span class="version-name">Emerald <small>2.0</small></span></h2>
                    <h4><i class="fas fa-fw fa-plus"></i> <?php _e('New', 'luna') ?></h4>
                    <p><?php _e('Fifteen has received a fully reimagned design based on Airalin', 'luna') ?></p>
                    <p><?php _e('Sunrise has also been reimagned based on the new Fifteen look', 'luna') ?></p>
                    <p><?php _e('You can now add custom CSS to the theme you\'re using', 'luna') ?></p>
                    <p><?php _e('Luna\'s default placeholder avatar can now be replaced', 'luna') ?></p>
                    <p><?php _e('Notifications now get marked as read when clicked', 'luna') ?></p>
                    <p><?php _e('New notification tools on the Notification page in the profile', 'luna') ?></p>
                    <p><?php _e('Comments can now have admin notes that are displayed in threads', 'luna') ?></p>
                    <p><?php _e('You can now upload a header background to use in your theme and the Backstage', 'luna') ?></p>
                    <p><?php _e('You can now manage your boards favicon in Settings', 'luna') ?></p>
                    <p><?php _e('You can now respond on Inbox messages from the message view', 'luna') ?></p>
                    <p><?php _e('Reports now trigger a notifications for admins and moderators', 'luna') ?></p>
                    <p><?php _e('New threads in a subscribed board now trigger notifications', 'luna') ?></p>
                    <p><?php _e('Fifteen, Sunrise and Backstage have new Pink, Dark Red and Beige accents', 'luna') ?></p>
                    <p><?php _e('The parser now supports the spoiler-tag optionally', 'luna') ?></p>
                    <p><?php _e('Luna now has a Dutch translation included by default', 'luna') ?></p>
                    <p><?php _e('Maintenance mode now shows a warning in the Backstage header', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-wrench"></i> <?php _e('Improved', 'luna') ?></h4>
                    <p><?php _e('You can now see what\'s a subforum and edit more settings in board management', 'luna') ?></p>
                    <p><?php _e('Users can now disable or enable First Run from their profile', 'luna') ?></p>
                    <p><?php _e('The editor will now put items under a button if the screen becomes to small', 'luna') ?></p>
                    <p><?php _e('When you have a notification, the icon will animate', 'luna') ?></p>
                    <p><?php _e('Advanced search has an improved UI', 'luna') ?></p>
                    <p><?php _e('About can now be translated to other languages', 'luna') ?></p>
                    <p><?php _e('You can now directly manage a reported comment from the Backstage', 'luna') ?></p>
                    <p><?php _e('Improved Backstage UI and night mode', 'luna') ?></p>
                    <p><?php _e('Multiple improvements on accessibility', 'luna') ?></p>
                    <p><?php _e('Luna now provides a description meta tag to prevent weird search results', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-server"></i> <?php _e('System', 'luna') ?></h4>
                    <p><?php _e('Luna now uses salts and SHA-512 to save passwords', 'luna') ?></p>
                    <p><?php _e('Luna now includes full "Right to Left"-support', 'luna') ?></p>
                    <p><?php _e('Components now live in their own folder in the Luna root', 'luna') ?></p>
                    <p><?php _e('Debug mode can now be enabled with one line in the config file', 'luna') ?></p>
                    <p><?php _e('Bootstrap, Font Awesome and jQuery have been updated to the latest version', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-exchange-alt"></i> <?php _e('Changed', 'luna') ?></h4>
                    <p><?php _e('Revamped search forms for users and bans, including responsive design', 'luna') ?></p>
                    <p><?php _e('The Backstage has a whole new structure', 'luna') ?></p>
                    <p><?php _e('Luna\'s file structure has been updated', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-trash-alt"></i> <?php _e('Removed', 'luna') ?></h4>
                    <p><?php _e('The editor no longer has an emoticon menu', 'luna') ?></p>
                    <p><?php _e('The code base no longer supports PHP 5.2', 'luna') ?></p>
                    <p><?php _e('Notifications can no longer be marked as read/trashed from the fly-out', 'luna') ?></p>
                    <p><?php _e('You can no longer manage the database from the Backstage', 'luna') ?></p>
                    <p><?php _e('Tools to add new users have been removed', 'luna') ?></p>
                    <h4><i class="fas fa-fw fa-bug"></i> <?php _e('Fixed', 'luna') ?></h4>
                    <p><?php _e('Fixes 63 bugs', 'luna') ?></p>
                </section>
            </div>
            <div class="card-footer">
                <?php printf(__('Luna is developed by the %s. Copyright %s. Released under the GPLv2 license.', 'luna'), '<a href="http://getluna.org/">Luna team</a>', '2013-2018') ?>
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
