<?php

// Language definitions used in admin_options.php
$lang_admin_features = array(

'Bad HTTP Referer message'			=>	'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.',
'Must enter title message'			=>	'You must enter a board title.',
'Invalid e-mail message'			=>	'The admin email address you entered is invalid.',
'Invalid webmaster e-mail message'	=>	'The webmaster email address you entered is invalid.',
'SMTP passwords did not match'		=>	'You need to enter the SMTP password twice exactly the same to change it.',
'Enter announcement here'			=>	'Enter your announcement here.',
'Enter rules here'					=>	'Enter your rules here.',
'Default maintenance message'		=>	'The forums are temporarily down for maintenance. Please try again in a few minutes.',
'Timeout error message'				=>	'The value of "Timeout online" must be smaller than the value of "Timeout visit".',
'Options updated redirect'			=>	'Settings updated. Redirecting …',
'Features head'						=>	'Features settings',

'Features subhead'					=>	'Features',
'Quick post label'					=>	'Quick post',
'Quick post help'					=>	'When enabled, ModernBB will add a quick post form at the bottom of topics. This way users can post directly from the topic view.',
'Users online label'				=>	'Users online',
'Users online help'					=>	'Display info on the index page about guests and registered users currently browsing the board.',
'Censor words label'				=>	'Censor words',
'Censor words help'					=>	'Enable this to censor specific words in the board. See %s for more info.',
'Signatures label'					=>	'Signatures',
'Signatures help'					=>	'Allow users to attach a signature to their posts.',
'User ranks label'					=>	'User ranks',
'User ranks help'					=>	'Enable this to use user ranks. See %s for more info.',
'User has posted label'				=>	'User has posted earlier',
'User has posted help'				=>	'This feature displays a dot in front of topics in viewforum.php in case the currently logged in user has posted in that topic earlier. Disable if you are experiencing high server load.',
'Topic views label'					=>	'Topic views',
'Topic views help'					=>	'Keep track of the number of views a topic has. Disable if you are experiencing high server load in a busy forum.',
'Quick jump label'					=>	'Quick jump',
'Quick jump help'					=>	'Enable the quick jump (jump to forum) drop list.',
'GZip label'						=>	'GZip output',
'GZip help'							=>	'If enabled, ModernBB will gzip the output sent to browsers. This will reduce bandwidth usage, but use a little more CPU. This feature requires that PHP is configured with zlib (--with-zlib). Note: If you already have one of the Apache modules mod_gzip or mod_deflate set up to compress PHP scripts, you should disable this feature.',
'Search all label'					=>	'Search all forums',
'Search all help'					=>	'When disabled, searches will only be allowed in one forum at a time. Disable if server load is high due to excessive searching.',
'Menu items label'					=>	'Additional menu items',
'Menu items help'					=>	'By entering HTML hyperlinks into this textbox, any number of items can be added to the navigation menu at the top of all pages. The format for adding new links is X = &lt;a href="URL"&gt;LINK&lt;/a&gt; where X is the position at which the link should be inserted (e.g. 0 to insert at the beginning and 2 to insert after "User list"). Separate entries with a linebreak.'

);
