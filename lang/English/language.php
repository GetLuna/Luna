<?php

$lang = array(

// Text orientation and encoding
'lang_direction'					=>	'ltr', // ltr (Left-To-Right) or rtl (Right-To-Left)
'lang_identifier'					=>	'en',

// Number and date formatting
'lang_decimal_point'				=>	'.',
'lang_thousands_sep'				=>	',',
'lang_time'							=>	'H:i',
'lang_date'							=>	'j M Y',

// Notices
'Bad request'						=>	'Bad request. The link you followed is incorrect, outdated or you\'re simply not allowed to hang around here.',
'No view'							=>	'You do not have permission to view this page.',
'Bad referrer'						=>	'Bad HTTP_REFERER. You were referred to this page from an unauthorized source. If the problem persists please make sure that \'Base URL\' is correctly set in Admin/Options and that you are visiting the forum by navigating to that URL. More information regarding the referrer check can be found in the Luna documentation.',
'No permission'						=>	'You do not have permission to access this page.',
'No cookie'							=>	'You appear to have logged in successfully, however a cookie has not been set. Please check your settings and if applicable, enable cookies for this website.',
'Settings saved'					=>  'Your settings have been saved.',
'User deleted'						=>  'The user has been deleted.',
'User failed'						=>  'Failed to create user, no password was given.',
'User created'						=>  'User created',
'Cache cleared'						=>  'The cache files have been removed.',

// Miscellaneous
'Announcement'						=>	'Announcement',
'Options'							=>	'Global settings',
'Features'							=>	'Features',
'Submit'							=>	'Submit', // "Name" of submit buttons
'Search'							=>	'Search',
'Ban message'						=>	'You are banned from this forum.',
'Ban message 2'						=>	'The ban expires at the end of',
'Ban message 3'						=>	'The administrator or moderator that banned you left the following message:',
'Ban message 4'						=>	'Please direct any inquiries to the forum administrator at',
'Never'								=>	'Never',
'Today'								=>	'Today',
'Yesterday'							=>	'Yesterday',
'Info'								=>	'Info', // A common table header
'Maintenance'						=>	'Maintenance',
'Invalid email'						=>	'The email address you entered is invalid.',
'required field'					=>	'is a required field in this form.', // For javascript form validation
'Last post'							=>	'Last post',
'by'								=>	'by', // As in last post by some user
'New posts'							=>	'New posts', // The link that leads to the first new post
'New posts info'					=>	'Go to the first new post in this topic.', // The popup text for new posts links
'Username'							=>	'Username',
'Password'							=>	'Password',
'Send email'						=>	'Send email',
'Registered table'					=>	'Registered',
'Subject'							=>	'Subject',
'Start typing'						=>  'Start typing...',
'Message'							=>	'Message',
'Forum'								=>	'Forum',
'Posts table'						=>	'Posts',
'Page'								=>	'Page %s',
'BBCode'							=>	'BBCode',
'img tag'							=>	'[img] tag',
'Smilies'							=>	'Smilies',
'and'								=>	'and',
'Image link'						=>	'image', // This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'								=>	'wrote', // For [quote]'s
'Mailer'							=>	'%s Mailer', // As in "MyForums Mailer" in the signature of outgoing emails
'Spacer'							=>	'…', // Ellipsis for paginate

// Title
'Title'								=>	'Title',
'Member'							=>	'Member',
'Moderator'							=>	'Moderator',
'Administrator'						=>	'Administrator',
'Banned'							=>	'Banned',
'Guest'								=>	'Guest',

// Stuff for include/parser.php
'BBCode error no opening tag'		=>	'[/%1$s] was found without a matching [%1$s]',
'BBCode error invalid nesting'		=>	'[%1$s] was opened within [%2$s], this is not allowed',
'BBCode error invalid self-nesting'	=>	'[%s] was opened within itself, this is not allowed',
'BBCode error no closing tag'		=>	'[%1$s] was found without a matching [/%1$s]',
'BBCode error empty attribute'		=>	'[%s] tag had an empty attribute section',
'BBCode list size error'			=>	'Your list was too long to parse, please make it smaller!',

// Stuff for the navigator (top of every page)

// User menu
'Support'							=>	'Support',
'Help'								=>	'Help',
'Index'								=>	'Index',
'User list'							=>	'User list',
'Rules'								=>	'Rules',
'Register'							=>	'Register',
'Registered'						=>	'Registered since',
'Login'								=>	'Login',
'Profile'							=>	'Profile',
'Logout'							=>	'Logout',
'Backstage'							=>	'Backstage',
'Mark as read'						=>	'Mark as read',
'Title separator'					=>	' / ',

// Stuff for the page footer
'Moderate topic'					=>	'Moderate topic',
'All'								=>	'Show all posts',
'Move topic'						=>	'Move topic',
'Open topic'						=>	'Open topic',
'Close topic'						=>	'Close topic',
'Unstick topic'						=>	'Unstick topic',
'Stick topic'						=>	'Stick topic',
'Moderate forum'					=>	'Moderate forum',
'Powered by'						=>	'Powered by %s',

// Debug information
'Debug table'						=>	'Debug information',
'Querytime'							=>	'Generated in %1$s seconds, %2$s queries executed',
'Memory usage'						=>	'Memory usage: %1$s',
'Peak usage'						=>	'(Peak: %1$s)',
'Query times'						=>	'Time (s)',
'Query'								=>	'Query',
'Total query time'					=>	'Total query time: %s',

// First run
'First run message'					=>	'Wow, it\'s great to have you here, welcome and thanks for joining us. We\'ve set up your account and you\'re ready to go. Through we like to point out some actions you might want to do first.',
'Hi there'							=>	'Hi there, %s',
'Welcome to'						=>	'Welcome to %s',
'Change your avatar'				=>	'Change your avatar',
'Extend profile'					=>	'Extend your details',
'Get help'							=>	'Get help',
'Do not show again'					=>	'Don\'t show again',

// For extern.php RSS feed
'RSS description'					=>	'The most recent topics at %s.',
'RSS description topic'				=>	'The most recent posts in %s.',
'RSS reply'							=>	'Re: ', // The topic subject will be appended to this string (to signify a reply)
'RSS active topics feed'			=>	'RSS active topics feed',
'Atom active topics feed'			=>	'Atom active topics feed',
'RSS forum feed'					=>	'RSS forum feed',
'Atom forum feed'					=>	'Atom forum feed',
'RSS topic feed'					=>	'RSS topic feed',
'Atom topic feed'					=>	'Atom topic feed',

// Units for file sizes
'Size unit B'						=>	'%s B',
'Size unit KiB'						=>	'%s KiB',
'Size unit MiB'						=>	'%s MiB',
'Size unit GiB'						=>	'%s GiB',
'Size unit TiB'						=>	'%s TiB',
'Size unit PiB'						=>	'%s PiB',
'Size unit EiB'						=>	'%s EiB',

// Language for installation
'Choose install language'		=>	'Choose the install script language',
'Choose install language info'	=>	'The language used for this install script. The default language used for the board itself can be set below.',
'Install language'				=>	'Install language',
'Change language'				=>	'Change language',
'Already installed'				=>	'It seems like Luna is already installed. You should go <a href="index.php">here</a> instead.',
'You are running error'			=>	'You are running %1$s version %2$s. Luna %3$s requires at least %1$s %4$s to run properly. You must upgrade your %1$s installation before you can continue.',
'My Luna Forum'					=>	'My Luna Forum',
'Description'					=>	'You can do anything', // Do not translate this string
'Username 1'					=>	'Usernames must be at least 2 characters long.',
'Username 2'					=>	'Usernames must not be more than 25 characters long.',
'Username 3'					=>	'The username guest is reserved.',
'Username 4'					=>	'Usernames may not be in the form of an IP address.',
'Username 5'					=>	'Usernames may not contain all the characters \', " and [ or ] at once.',
'Username 6'					=>	'Usernames may not contain any of the text formatting tags (BBCode) that the forum uses.',
'Short password'				=>	'Passwords must be at least 6 characters long.',
'Passwords not match'			=>	'Passwords do not match.',
'Wrong email'					=>	'The administrator email address you entered is invalid.',
'No board title'				=>	'You must enter a board title.',
'Error default language'		=>	'The default language chosen doesn\'t seem to exist.',
'Error default style'			=>	'The default style chosen doesn\'t seem to exist.',
'No DB extensions'				=>	'PHP needs to have support for either MySQL or SQLite to run Luna to be installed. Non is available, through',
'Administrator username'		=>	'Username',
'Administrator password 1'		=>	'Administrator password 1',
'Administrator password 2'		=>	'Administrator password 2',
'Administrator email'			=>	'Email',
'Board title'					=>	'Board title',
'Base URL'						=>	'No trailing slash',
'Required field'				=>	'is a required field in this form.',
'Luna Installation'				=>	'Luna Installation',
'Install'						=>	'Install Luna %s',
'Errors'						=>	'The following errors need to be corrected:',
'Database setup'				=>	'Database setup',
'Select database'				=>	'Select your database type',
'Info 1'						=>	'What database do you want to use?',
'Database type'					=>	'Type',
'Info 2'						=>	'Where\'s the server?',
'Info 3'						=>	'The database name',
'Database server hostname'		=>	'Server hostname',
'Database name'					=>	'Name',
'Database username'				=>	'Username',
'Info 4'						=>	'Your database username',
'Info 5'						=>	'Set for more Luna installation in this database',
'Table prefix'					=>	'Table prefix',
'Administration setup'			=>	'Administration setup',
'Info 6'						=>	'2 to 25 characters long',
'Info 7'						=>	'At least 6 characters long',
'Confirm password'				=>	'Confirm password',
'Board setup'					=>	'Board setup',
'Board description'				=>	'Board description',
'Appearance'					=>	'Appearance',
'Default language'				=>	'Default language',
'Default style'					=>	'Default style',
'Start install'					=>	'Start install',
'DB type not valid'				=>	'\'%s\' is not a valid database type',
'Table prefix error'			=>	'The table prefix \'%s\' contains illegal characters or is too long. The prefix may contain the letters a to z, any numbers and the underscore character. They must however not start with a number. The maximum length is 40 characters. Please choose a different prefix',
'Prefix reserved'				=>	'The table prefix \'sqlite_\' is reserved for use by the SQLite engine. Please choose a different prefix',
'Existing table error'			=>	'A table called \'%susers\' is already present in the database \'%s\'. This could mean that Luna is already installed or that another piece of software is installed and is occupying one or more of the table names Luna requires. If you want to install multiple copies of Luna in the same database, you must choose a different table prefix',
'InnoDB off'					=>	'InnoDB does not seem to be enabled. Please choose a database layer that does not have InnoDB support, or enable InnoDB on your MySQL server',
'Administrators'				=>	'Administrators',
'Moderators'					=>	'Moderators',
'Guests'						=>	'Guests',
'Members'						=>	'Members',
'New member'					=>	'New member',
'Maintenance message'			=>	'The forums are temporarily down for maintenance. Please try again in a few minutes.',
'Alert cache'					=>	'<strong>The cache directory is currently not writable!</strong> In order for Luna to function properly, the directory <em>%s</em> must be writable by PHP. Use chmod to set the appropriate directory permissions. If in doubt, chmod to 0777.',
'Alert avatar'					=>	'<strong>The avatar directory is currently not writable!</strong> If you want users to be able to upload their own avatar images you must see to it that the directory <em>%s</em> is writable by PHP. You can later choose to save avatar images in a different directory (see Admin/Options). Use chmod to set the appropriate directory permissions. If in doubt, chmod to 0777.',
'Alert upload'					=>	'<strong>File uploads appear to be disallowed on this server!</strong> If you want users to be able to upload their own avatar images you must enable the file_uploads configuration setting in PHP. Once file uploads have been enabled, avatar uploads can be enabled in Administration/Options/Features.',
'Luna has been installed'		=>	'Luna has been installed. To finalize the installation please follow the instructions below.',
'Info 8'						=>	'To finalize the installation, you need to click on the button below to download a file called config.php. You then need to upload this file to the root directory of your Luna installation.',
'Info 9'						=>	'Once you have uploaded config.php, Luna will be fully installed! At that point, you may <a href="index.php">go to the forum index</a>.',
'Download config.php file'		=>	'Download config.php file',
'Luna fully installed'			=>	'Luna has been fully installed! You may now <a href="index.php">go to the forum index</a>.',

// Language for updating
'Update Luna'					=>	'Update Luna',
'Down'							=>	'The forums are temporarily down for maintenance. Please try again in a few minutes.',

'No update error'				=>	'Your forum is already as up-to-date as this script can make it',

'Start update'					=>	'Start update',
'Correct errors'				=>	'The following errors need to be corrected:',

'Preparsing item'				=>	'Preparsing %1$s %2$s …',
'Rebuilding index item'			=>	'Rebuilding index for %1$s %2$s',

'post'							=>	'post',
'topic'							=>	'topic',
'signature'						=>	'signature',

// Language for frontend

// Language for delete.php
'Delete post'			=>	'Delete post',
'Topic warning'			=>	'Warning! This is the first post in the topic, the whole topic will be permanently deleted.',
'Delete info'			=>	'The post you have chosen to delete is set out below for you to review before proceeding.',
'Reply by'				=>	'Reply by %s - %s',
'Topic by'				=>	'Topic started by %s - %s',
'Delete'				=>	'Delete',

// Language for help.php
'produces'				=>	'produces',

'BBCode info'			=>	'BBCode is a collection of tags that are used to change the look of text in this forum. Below you can find all the available BBCodes and how to use them. Administrators have the ability to disable BBCode. You can tell if BBCode is disabled whenever you post a message or edit your signature.',

'Text style'			=>	'Text style',
'Text style info'		=>	'The following tags change the appearance of text:',
'Bold text'				=>	'Bold text',
'Underlined text'		=>	'Underlined text',
'Italic text'			=>	'Italic text',
'Strike-through text'	=>	'Strike-through text',
'Red text'				=>	'Red text',
'Blue text'				=>	'Blue text',
'Heading text'			=>	'Heading text',
'Inserted text'			=>	'Inserted text',
'Sub text'				=>	'Subscript text',
'Sup text'				=>	'Superscript text',

'Multimedia'			=>  'Multimedia',
'Links info'			=>	'You can create links to other locations or to email addresses using the following tags:',
'My email address'		=>	'My email address',
'Images info'			=>	'If you want to display an image you can use the img tag. The text appearing after the "=" sign in the opening tag is used for the alt attribute and should be included whenever possible.',
'Luna bbcode test'  	=>  'Luna BBCode Test',
'Video info'			=>  'Luna supports embedding from DailyMotion, Vimeo and YouTube. With the BBCode below, you can embed one of those services videos.',
'Video link'			=>  'Put the link to the video here', 

'Quotes'				=>	'Quotes',
'Quotes info'			=>	'If you want to quote someone, you should use the quote tag.',
'Quotes info 2'			=>	'If you don\'t want to quote anyone in particular, you can use the quote tag without specifying a name. If a username contains the characters [ or ] you can enclose it in quote marks.',
'Quote text'			=>	'This is the text I want to quote.',
'produces quote box'	=>	'produces a quote box like this:',

'Code'					=>	'Code',
'Code info'				=>	'When displaying source code you should make sure that you use the code tag. Text displayed with the code tag will use a monospaced font and will not be affected by other tags.',
'Syntax info'			=>	'You can also use syntax highlighting for markup, CSS, PHP and JavaScript. The language has to be noted on the first line inside the codetag and can\'t be on the same line as <code>[code]</code>.',
'Code text'				=>	'This is some code.',
'produces code box'		=>	'produces a code box like this:',

'Lists'					=>	'Lists',
'List info'				=>	'To create a list you can use the list tag. You can create 2 types of lists using the list tag.',
'List text 1'			=>	'Example list item 1.',
'List text 2'			=>	'Example list item 2.',
'List text 3'			=>	'Example list item 3.',
'produces list'			=>	'produces a bulleted list.',
'produces decimal list'	=>	'produces a numbered list.',

'Bold'					=>	'Bold',
'Underline'				=>	'Underline',
'Italic'				=>	'Italic',
'Strike'				=>	'Strike',
'URL'					=>	'URL',
'List'					=>	'List',
'List item'				=>	'List item',
'Heading'				=>	'Heading',
'Inline code'			=>	'Inline code',
'Superscript'			=>	'Superscript',
'Subscript'				=>	'Subscript',
'Video'					=>	'Video',
'Image'					=>	'Image',

'Smilies info'			=>	'If enabled, the forum can convert a series of smilies to graphical representations. The following smilies you can use are:',

'General use'					=>	'General use',
'General use info'				=>	'Explains some of the basics on how to work with this forum software.',
'Forums and topics'				=>	'Forums and topics',
'Labels question'				=>	'What do the labels in front of topic titles mean?',
'Labels info'					=>	'You\'ll see that some of the topics are labeled, different labels have different meanings.',
'Label'							=>	'Label',
'Explanation'					=>	'Explanation',
'Sticky explanation'			=>	'Sticky topics are usually important topics which you should read. It\'s worth it to take a look there.',
'Closed explanation'			=>	'When a you see a closed label, it means you can\'t post on that topic any more, unless you have a permission that overwrites this. The topic is still available to read, through.',
'Moved explanation'				=>	'This topic has been moved to another forum. Admins and moderators can choose to show this notification, or simply not show it. The original forum where this topic was located in, won\'t show any topic stats anymore.',
'Posted explanation'			=>	'This little dot appears when you have made a post in this topic.',
'Content question'				=>	'Smilies, signatures, avatars and images are not visible?',
'Content answer'				=>	'You can change the behavior of the topic view in your profile settings. There you can enable smilies, signatures, avatars and images in posts, but they should be enabled by default unless your forum admin has disabled those features.',
'Topics question'				=>	'Why can\'t I see any topics or forums?',
'Topics answer'					=>	'You might not have the correct permissions to do so, ask the forum administrator for more help.',
'Profile question'				=>	'Why can\'t I see any profiles?',
'Profile answer'				=>	'You might not have the correct permissions to do so, ask the forum administrator for more help.',
'Information question'			=>	'My profile doesn\'t contain as much as others?',
'Information answer'			=>	'You\'re profile will only display fields that are enabled and filled in on your profile personality page. You might want to see if you missed some fields.',
'Advanced search question'		=>	'Are there more options to search?',
'Advanced search answer'		=>	'When you go to the search page, you\'ll find yourself on a page with 1 search box. Below that search box there is a link to Advanced search, here you can find more search options! This feature may not be available on your device, if disabled by the forum admin.',
'More search question'			=>	'Why can\'t search in more then 1 forum at once?',
'More search answer'			=>	'You might not have the correct permissions to do so, ask the forums administrator for more help.',
'Moderating'					=>	'Moderating',
'Moderating info'				=>	'Admins and moderators need help sometimes, too! The basics of moderating are explained here.',
'Moderate forum question'		=>	'How do I moderate a forum?',
'Moderate forum answer'			=>	'The moderation options are available at the bottom of the page. Those features aren\'t available for all moderators. When you click this button, you will be send to a page where you can manage the current forum. From there, you can move, delete, merge, close and open multiple topics at once.',
'Moderate topic question'		=>	'How do I moderate a topic?',
'Moderate topic answer 1'		=>	'The moderation options are available at the bottom of the page. Those features aren\'t available for all moderators. When you click this button, you will be send to a page where you can manage the current topic. From there, you can select multiple post to delete or split from the current topic at once.',
'Moderate topic answer 2'		=>	'Next to the "Moderate topic" button, you can find options to move, open or close the topic. You can also make it a sticky topic from there, or unstick it.',
'Moderate user question'		=>	'How do I moderate an user?',
'Moderate user answer 1'		=>	'Moderating options are available in the users profile. You can find the moderation options under "Administration" in the users profile menu. Those features aren\'t available for all moderators.',
'Moderate user answer 2'		=>	'The Administration page allow you to check if the user has an admin note, and you can also change that note if required. You can also change the post count of the user. At this page, the user can also be given moderator permissions on a per-forum base, through the user must have a moderator account to be able to actually use those permissions.',
'Moderate user answer 3'		=>	'Finally, you can ban or delete a user from his profile. If you want to ban and/or delete multiple users at once, you\'re probably better off with the advanced user management features in the Backstage.',

// Language for index.php
'Topics'		=>	'Topics',
'Empty board'	=>	'Board is empty.',
'Newest user'	=>	'Newest user',
'Users online'	=>	'Users online',
'Guests online'	=>	'Guests online',
'No of users'	=>	'Users',
'No of topics'	=>	'Topics',
'No of post'	=>	'Posts',
'Online'		=>	'Online:', // As in "Online: User A, User B etc."

// Language for login.php
'Wrong user/pass'			=>	'Wrong username and/or password.',
'Forgotten pass'			=>	'Forgotten password',
'No email match'			=>	'There is no user registered with the email address',
'Remember me'				=>	'Remember me',

'Forget mail'				=>	'An email has been sent to the specified address with instructions on how to change your password. If it does not arrive you can contact the forum administrator at',
'Password request flood'	=>  'This account has already requested a password reset in the past hour. Please wait %s minutes before requesting a new password again.',

// Send email
'Form email disabled'			=>	'The user you are trying to send an email to has disabled form email.',
'No email subject'				=>	'You must enter a subject.',
'No email message'				=>	'You must enter a message.',
'Too long email message'		=>	'Messages cannot be longer than 65535 characters (64 KB).',
'Email flood'					=>  'At least %s seconds have to pass between sent emails. Please wait %s seconds and try sending again.',
'Send email to'					=>	'Send email to',

// Report
'No reason'						=>	'You must enter a reason.',
'Reason too long'				=>	'Your message must be under 65535 bytes (~64kb).',
'Report flood'					=>  'At least %s seconds have to pass between reports. Please wait %s seconds and try sending again.',
'Report post'					=>	'Report post',
'Reason'						=>	'Reason',

// Subscriptions
'Not subscribed topic'			=>	'You\'re not subscribed to this topic.',

// General forum and topic moderation
'Moderate'						=>	'Moderate',
'Move'							=>	'Move',
'Split'							=>	'Split',
'Merge'							=>	'Merge',

// Moderate forum
'Open'							=>	'Open',
'Close'							=>	'Close',
'Move topics'					=>	'Move topics',
'Move to'						=>	'Move to',
'Nowhere to move'				=>	'There are no forums into which you can move topics.',
'Leave redirect'				=>	'Leave redirect topic(s)',
'Delete topics comply'			=>	'Are you sure you want to delete the selected topics?',
'No topics selected'			=>	'You must select at least one topic for move/delete/open/close.',
'Not enough topics selected'	=>	'You must select at least two topics for merge.',
'Merge topics'					=>	'Merge topics',
'New subject'					=>	'New subject',

// Split multiple posts in topic
'Split posts'					=>	'Split posts',

// Delete multiple posts in topic
'Delete posts'					=>	'Delete posts',
'Cannot select first'			=>	'First post cannot be selected for split/delete.',
'Delete posts comply'			=>	'Are you sure you want to delete the selected posts?',
'No posts selected'				=>	'You must select at least one post for split/delete.',

// Get host
'Host info 1'					=>	'The IP address is: %s',
'Host info 2'					=>	'The host name is: %s',
'Show more users'				=>	'Show more users for this IP',

// Checked untill this line

// Language for post.php and edit.php
// Post validation stuff (many are similiar to those in edit.php)
'No subject'		=>	'Topics must contain a subject.',
'No subject after censoring'	=>	'Topics must contain a subject. After applying censoring filters, your subject was empty.',
'Too long subject'	=>	'Subjects cannot be longer than 70 characters.',
'No message'		=>	'You must enter a message.',
'No message after censoring'	=>	'You must enter a message. After applying censoring filters, your message was empty.',
'Too long message'	=>	'Posts cannot be longer than %s bytes.',
'All caps subject'	=>	'Subjects cannot contain only capital letters.',
'All caps message'	=>	'Posts cannot contain only capital letters.',
'Empty after strip'	=>	'It seems your post consisted of empty BBCodes only. It is possible that this happened because e.g. the innermost quote was discarded because of the maximum quote depth level.',

// Posting
'Post errors'		=>	'Post errors',
'Post preview'		=>	'Post preview',
'Guest name'		=>	'Name', // For guests (instead of Username)
'Post a reply'		=>	'Post a reply',
'Post new topic'	=>	'Post topic',
'Hide smilies'		=>	'Never show smilies as icons for this post',
'Subscribe topic'	=>	'Subscribe to this topic',
'Stay subscribed'	=>	'Stay subscribed to this topic',
'Topic review'		=>	'Topic review (newest first)',
'Flood start'		=>  'At least %s seconds have to pass between posts. Please wait %s seconds and try posting again.',
'Preview'			=>	'Preview',

// Edit post
'Silent edit'		=>	'Silent edit (don\'t display "Edited by ..." in topic view)',
'Edit post'			=>	'Edit post',

// Language for both profile.php and register.php
'Email legend'				=>	'Enter a valid email address',
'Time zone'					=>	'Time zone',
'DST'						=>	'Advance time by 1 hour for daylight saving.',
'Time format'				=>	'Time format',
'Date format'				=>	'Date format',
'Default'					=>	'Default',
'Language'					=>	'Language',
'Email setting info'		=>	'Email settings',
'Email setting 1'			=>	'Display your email address.',
'Email setting 2'			=>	'Hide your email address but allow form email.',
'Email setting 3'			=>	'Hide your email address and disallow form email.',

'Username too short'		=>	'Usernames must be at least 2 characters long. Please choose another (longer) username.',
'Username too long'			=>	'Usernames must not be more than 25 characters long. Please choose another (shorter) username.',
'Username guest'			=>	'The username guest is reserved. Please choose another username.',
'Username IP'				=>	'Usernames may not be in the form of an IP address. Please choose another username.',
'Username reserved chars'	=>	'Usernames may not contain all the characters \', " and [ or ] at once. Please choose another username.',
'Username BBCode'			=>	'Usernames may not contain any of the text formatting tags (BBCode) that the forum uses. Please choose another username.',
'Banned username'			=>	'The username you entered is banned in this forum. Please choose another username.',
'Pass too short'			=>	'Passwords must be at least 6 characters long. Please choose another (longer) password.',
'Pass not match'			=>	'Passwords do not match.',
'Banned email'				=>	'The email address you entered is banned in this forum. Please choose another email address.',
'Dupe email'				=>	'Someone else is already registered with that email address. Please choose another email address.',
'Sig too long'				=>	'Signatures cannot be longer than %1$s characters. Please reduce your signature by %2$s characters.',
'Sig too many lines'		=>	'Signatures cannot have more than %s lines.',
'Bad ICQ'					=>	'You entered an invalid ICQ UIN. Please go back and correct.',

// Language for profile.php
'Section personality'			=>	'Personality',
'Section admin'					=>	'Administration',

// Miscellaneous
'Personal details legend'		=>	'Contact details',
'User tools'					=>	'User tools',
'Unknown'		  				=>  'Unknown',

// Password stuff
'Pass key bad'					=>	'The specified password activation key was incorrect or has expired. Please re-request a new password. If that fails, contact the forum administrator at',
'Pass updated'					=>	'Your password has been updated. You can now login with your new password.',
'Wrong pass'					=>	'Wrong old password.',
'Change pass'					=>	'Change password',
'Old pass'						=>	'Old password',
'New pass'						=>	'New password',
'Confirm new pass'				=>	'Confirm new password',
'Pass info'						=>	'Passwords must be at least 6 characters long and are case sensitive',

// Email stuff
'Email key bad'					=>	'The specified email activation key was incorrect or has expired. Please re-request change of email address. If that fails, contact the forum administrator at',
'Email updated'					=>	'Your email address has been updated.',
'Activate email sent'			=>	'An email has been sent to the specified address with instructions on how to activate the new email address. If it doesn\'t arrive you can contact the forum administrator at',
'Email instructions'			=>	'An email will be sent to your new address with an activation link. You must click the link in the email you receive to activate the new address.',
'Change email'					=>	'Change email address',
'New email'						=>	'New email',

// Avatar upload stuff
'Avatars disabled'				=>	'The administrator has disabled avatar support.',
'Too large ini'					=>	'The selected file was too large to upload. The server didn\'t allow the upload.',
'Partial upload'				=>	'The selected file was only partially uploaded. Please try again.',
'No tmp directory'				=>	'PHP was unable to save the uploaded file to a temporary location.',
'No file'						=>	'You did not select a file for upload.',
'Bad type'						=>	'The file you tried to upload is not of an allowed type. Allowed types are gif, jpeg and png.',
'Too wide or high'				=>	'The file you tried to upload is wider and/or higher than the maximum allowed',
'Too large'						=>	'The file you tried to upload is larger than the maximum allowed',
'pixels'						=>	'pixels',
'bytes'							=>	'bytes',
'Move failed'					=>	'The server was unable to save the uploaded file. Please contact the forum administrator at',
'Unknown failure'				=>	'An unknown error occurred. Please try again.',
'Avatar desc'					=>	'An avatar is a small image that will be displayed under your username in your posts. It must not be any bigger than',
'Upload avatar'					=>	'Upload avatar',
'Delete avatar'					=>	'Delete avatar', // only for admins
'File'							=>	'File',
'Upload'						=>	'Upload', // submit button

// Form validation stuff
'Forbidden title'				=>	'The title you entered contains a forbidden word. You must choose a different title.',

// Profile display stuff
'Email info'					=>	'Email: %s',
'Last visit info'				=>	'Last visit',
'Show posts'					=>	'Show posts',
'Show topics'					=>	'Show topics',
'Show subscriptions'			=>	'Show subscriptions',
'Contact'						=>	'Contact',
'Realname'						=>	'Real name',
'Location'						=>	'Location',
'Website'						=>	'Website',
'Invalid website URL'			=>	'The website URL you entered is invalid.',
'Microsoft'						=>	'Microsoft Account',
'Facebook'						=>	'Facebook',
'Twitter'						=>	'Twitter',
'Google+'						=>	'Google+',
'Avatar'						=>	'Avatar',
'Sig max size'					=>	'Max length: %s characters / Max lines: %s',
'Avatar info'					=>	'Upload an image to represent you',
'Change avatar'					=>	'Change avatar',
'Signature info'				=>	'Write a small piece to attach to every post you make',
'Sig preview'					=>	'Signature preview',
'No sig'						=>	'No signature currently stored in profile.',
'Signature quote/code/list/h'	=>	'The quote, code, list, and heading BBCodes are not allowed in signatures.',
'Posts per page'				=>	'Posts per page',
'Topics per page'				=>	'Topics per page',
'Leave blank'					=>	'Leave blank to use default',
'Notify full'					=>	'Include a plain text version of new posts in subscription notification emails.',
'Auto notify full'				=>	'Automatically subscribe to every topic you post in.',
'Show smilies'					=>	'Show smilies as graphic icons.',
'Show images'					=>	'Show images in posts.',
'Show images sigs'				=>	'Show images in user signatures.',
'Show avatars'					=>	'Show user avatars in posts.',
'Show sigs'						=>	'Show user signatures.',
'Style'							=>	'Style',
'Backstage Accent'				=>	'Backstage Accent',
'Admin note'					=>	'Admin note',
'Post display'					=>	'Post display',

// Administration stuff
'Group membership legend'		=>	'Choose user group',
'Save'							=>	'Save',
'Set mods legend'				=>	'Set moderator access',
'Moderator in info'				=>	'Choose which forums this user should be allowed to moderate. Note: This only applies to moderators. Administrators always have full permissions in all forums.',
'Update forums'					=>	'Update forums',
'Delete ban legend'				=>	'Delete or ban user',
'Delete user'					=>	'Delete user',
'Ban user'						=>	'Ban user',
'Confirm delete user'			=>	'Confirm delete user',
'Confirmation info'				=>	'Please confirm that you want to delete the user', // the username will be appended to this string
'Delete warning'				=>	'Warning! Deleted users and/or posts cannot be restored. If you choose not to delete the posts made by this user, the posts can only be deleted manually at a later time.',
'Delete all posts'				=>	'Delete any posts and topics this user has made',
'No delete admin message'		=>	'Administrators cannot be deleted. In order to delete this user, you must first move him/her to a different user group.',

// Language for register.php
'No new regs'				=>	'This forum is not accepting new registrations.',
'Forum rules'				=>	'Forum rules',
'Rules legend'				=>	'You must agree to the following in order to register',
'Registration flood'		=>	'A new user was registered with the same IP address as you within the last hour. To prevent registration flooding, at least an hour has to pass between registrations from the same IP. Sorry for the inconvenience.',
'Agree'						=>	'Agree',
'Cancel'					=>	'Cancel',
'Register legend'			=>	'Enter the requested data',

// Form validation stuff (some of these are also used in post.php)
'Registration errors'		=>	'Registration errors',
'Username censor'			=>	'The username you entered contains one or more censored words. Please choose a different username.',
'Username dupe 1'			=>	'Someone is already registered with the username',
'Username dupe 2'			=>	'The username you entered is too similar. The username must differ from that by at least one alphanumerical character (a-z or 0-9). Please choose a different username.',
'Email not match'			=>	'Email addresses do not match.',

// Registration email stuff
'Reg email'					=>	'Thank you for registering. Your password has been sent to the specified address. If it doesn\'t arrive you can contact the forum administrator at',

// Register info
'Username legend'			=>	'Enter a username between 2 and 25 characters long',
'Email help info'			=>	'Your password will be sent to this address, make sure it\'s valid',
'If human'					=>	'If you are human please leave this field blank!',
'Spam catch'				=>	'Unfortunately it looks like your request is spam. If you feel this is a mistake, please direct any inquiries to the forum administrator at',

// Language for search.php
'User search'						=>	'User search',
'No search permission'				=>	'You do not have permission to use the search feature.',
'Search flood'						=>  'At least %s seconds have to pass between searches. Please wait %s seconds and try searching again.',
'Search criteria legend'			=>	'Enter your search criteria',
'Search info'						=>	'To search by keyword, enter a term or terms to search for. Separate terms with spaces. Use AND, OR and NOT to refine your search. To search by author enter the username of the author whose posts you wish to search for. Use wildcard character * for partial matches.',
'Keyword search'					=>	'Keyword search',
'Author search'						=>	'Author search',
'All forums'						=>	'All forums',
'Search in'							=>	'Search in',
'Message and subject'				=>	'Message text and topic subject',
'Message only'						=>	'Message text only',
'Topic only'						=>	'Topic subject only',
'Sort by'							=>	'Sort by',
'Sort order'						=>	'Sort order',
'Search results info'				=>	'You can choose how you wish to sort and show your results.',
'Sort by post time'					=>	'Post time',
'Sort by author'					=>	'Author',
'Ascending'							=>	'Ascending',
'Descending'						=>	'Descending',
'Show as'							=>	'Show as',
'Show as posts'						=>	'Posts',
'Advanced search'					=>	'Advanced search',

// Results
'Search results'					=>	'Search results',
'Quick search show_new'				=>	'New',
'Quick search show_recent'			=>	'Active',
'Quick search show_unanswered'		=>	'Unanswered',
'Quick search show_user_topics'		=>	'Topics by %s',
'Quick search show_user_posts'		=>	'Posts by %s',
'Quick search show_subscriptions'	=>	'Subscribed by %s',
'By keywords show as topics'		=>	'Topics with posts containing \'%s\'',
'By keywords show as posts'			=>	'Posts containing \'%s\'',
'By user show as topics'			=>	'Topics with posts by %s',
'By user show as posts'				=>	'Posts by %s',
'By both show as topics'			=>	'Topics with posts containing \'%s\', by %s',
'By both show as posts'				=>	'Posts containing \'%s\', by %s',
'No terms'							=>	'You have to enter at least one keyword and/or an author to search for.',
'No hits'							=>	'Your search returned no hits.',
'No user posts'						=>	'There are no posts by this user in this forum.',
'No user topics'					=>	'There are no topics by this user in this forum.',
'No subscriptions'					=>	'This user is currently not subscribed to any topics.',
'No new posts'						=>	'There are no topics with new posts since your last visit.',
'No recent posts'					=>	'No new posts have been made within the last 24 hours.',
'No unanswered'						=>	'There are no unanswered posts in this forum.',
'Go to post'						=>	'Go to post',
'Go to topic'						=>	'Go to topic',

// Language for viewtopic.php
'Post reply'		=>	'Post reply',
'Topic closed'		=>	'Topic closed',
'From'				=>	'From:', // User location
'IP address logged'	=>	'IP log',
'Note'				=>	'Note:', // Admin note
'Posts'				=>	'Posts:',
'Replies'			=>	'Replies:',
'Last edit'			=>	'Last edited by',
'Report'			=>	'Report',
'Edit'				=>	'Edit',
'Quote'				=>	'Quote',
'Is subscribed'		=>	'You are subscribed',
'Unsubscribe'		=>	'Unsubscribe',
'Subscribe'			=>	'Subscribe',
'Quick post'		=>	'Quick post',
'New icon'			=>	'New post',
'Re'				=>	'Re:',

// Language for userlist.php
'User search info'	=>	'Enter a username to search for and/or a user group to filter by. Use the wildcard character * for partial matches.',
'User group'		=>	'User group',
'No of posts'		=>	'Posts',
'All users'			=>	'All users',
'Sort no of posts'	=>	'Sort by number of posts',
'Sort username'		=>	'Sort by username',
'Sort registered'	=>	'Sort by registration date',

// Language for viewforum.php
'Views'			=>	'Views',
'Moved'			=>	'Moved',
'Star'			=>	'Star',
'Sticky'		=>	'Sticky',
'Closed'		=>	'Closed',
'Empty forum'	=>	'Forum is empty.',

// Language for Backstage
// Language for bans.php
'No user message'			=>	'No user by that username registered. If you want to add a ban not tied to a specific username just leave the username blank.',
'No user ID message'		=>	'No user by that ID registered.',
'User is admin message'		=>	'The user %s is an administrator and can\'t be banned. If you want to ban an administrator, you must first demote him/her.',
'User is mod message'		=>	'The user %s is a moderator and can\'t be banned. If you want to ban a moderator, you must first demote him/her.',
'Must enter message'		=>	'You must enter either a username, an IP address or an email address (at least).',
'Cannot ban guest message'	=>	'The guest user cannot be banned.',
'Invalid IP message'		=>	'You entered an invalid IP/IP-range.',
'Invalid e-mail message'	=>	'The email address (e.g. user@domain.com) or partial email address domain (e.g. domain.com) you entered is invalid.',
'Invalid date message'		=>	'You entered an invalid expire date.',
'Invalid date reasons'		=>	'The format should be YYYY-MM-DD and the date must be at least one day in the future.',

'New ban head'				=>	'Add ban',
'Username help'				=>	'The username to ban',
'Username advanced help'	=>	'The username you want to ban. If you want to ban a specific IP/IP-range or email, leave it blank.',

'Ban search head'			=>	'Ban search',
'Ban search info'			=>	'Search for bans in the database. You can enter one or more terms to search for. Wildcards in the form of asterisks (*) are accepted. To show all bans leave all fields empty.',
'Date help'					=>	'(yyyy-mm-dd)',
'Expire after label'		=>	'Expire after',
'Expire before label'		=>	'Expire before',
'Order by label'			=>	'Order by',
'Order by ip'				=>	'IP',
'Order by expire'			=>	'Expire date',

'E-mail help'				=>	'The email or email domain you wish to ban',
'IP label'					=>	'IP address/IP-ranges',
'IP help'					=>	'The IP you wish to ban, separate addresses with spaces',
'IP help link'				=>	'Click %s to see IP statistics for this user.',
'Ban advanced head'			=>	'Advanced ban settings',
'Ban advanced subhead'		=>	'Supplement ban with IP and email',
'Ban message label'			=>	'Ban message',
'Ban message help'			=>	'A message for banned users',
'Message expiry subhead'	=>	'Ban message and expiry',
'Expire date label'			=>	'Expire date',
'Expire date help'			=>	'When does the ban expire, blank for manually',

'Results head'				=>	'Search Results',
'Results IP address head'	=>	'IP/IP-ranges',
'Results expire head'		=>	'Expires',
'Results banned by head'	=>	'Banned by',
'No match'					=>	'No match',

// Language for board.php
'Must enter name message'		=>	'You must enter a name',
'Confirm delete cat head'		=>	'Confirm delete category',
'Confirm delete cat info'		=>	'Are you sure that you want to delete the category <strong>%s</strong>?',
'Delete category warn'			=>	'Deleting a category will delete all forums and posts (if any) in this category!',
'Must enter integer message'	=>	'Position must be a positive integer value.',
'Add categories head'			=>	'Add categories',
'Delete categories head'		=>	'Delete categories',
'Edit categories head'			=>	'Edit categories',
'Category position label'		=>	'Position',
'Category name label'			=>	'Name',

// Language fox censoring.php
'Must enter word message'	=>	'You must enter a word to censor.',
'Add word subhead'			=>	'Add word',
'Add word info'				=>	'Enter a word that you want to censor and the replacement text for this word. Wildcards are accepted.',
'Censoring enabled'			=>	'<strong>Censoring is enabled in %s.</strong>',
'Censoring disabled'		=>	'<strong>Censoring is disabled in %s.</strong>',
'Censored word label'		=>	'Censored word',
'Replacement label'			=>	'Replacement word',
'Edit remove words'			=>	'Manage words',
'No words in list'			=>	'No censor words in list.',

// Language fox database.php
'Backup options'		=>	'Backup options',
'Backup type'			=>	'Backup type',
'Full'					=>	'Full',
'Structure only'		=>	'Structure only',
'Data only'				=>	'Data only',
'Gzip compression'		=>	'Gzip compression',
'Start backup'			=>	'Start backup',

'Backup info 1'			=>	'If your server supports it, you may also gzip-compress the file to reduce its size.',

'Restore complete'		=>	'Restore complete',
'Restore options'		=>	'Restore options',
'Start restore'			=>	'Start restore',

'Restore info 1'		=>	'This will perform a full restore of all Luna tables from a saved file. If your server supports it, you may upload a gzip-compressed text file and it will automatically be decompressed. This will overwrite any existing data. The restore may take a long time to process, so please do not move from this page until it is complete.',

'Warning'				=>	'Warning: critical features',

'Additional functions'	=>	'Additional functions',
'Repair all tables'		=>	'Repair all tables',
'Optimise all tables'	=>	'Optimise all tables',

'Additional info 1'		=>	'Additional features to help run a database, optimise and repair both do what they say.',

// Language for appearance.php, settings.php, email.php and features.php
'Bad HTTP Referer message'			=>	'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.',
'Must enter title message'			=>	'You must enter a title.',
'SMTP passwords did not match'		=>	'You need to enter the SMTP password twice exactly the same to change it.',
'Enter announcement here'			=>	'Enter your announcement here.',
'Enter rules here'					=>	'Enter your rules here.',
'Default maintenance message'		=>	'The forums are temporarily down for maintenance. Please try again in a few minutes.',
'Timeout error message'				=>	'The value of "Timeout online" must be smaller than the value of "Timeout visit".',

// Language for appearance.php
'Header appearance'					=>	'Header appearance',
'Footer appearance'					=>	'Footer appearance',
'Footer'							=>	'Footer',
'Display head'						=>	'Display settings',
'Default style help'				=>	'The default style will be used by new users and guests. Users can change the style they use, so changing the default style here won\'t change the design for already existing users. You can also force a style, this will reset the style setting for every user except the guest user.',
'About style'						=>	'About %s',
'version'							=>	'version %s',
'Released on'						=>	'Released on %s',
'Designed for'						=>	'Designed for Luna %s to %s',
'Force style'						=>	'Force style',
'Set as default'					=>	'Set as default',
'About'								=>	'About',
'Version number help'				=>	'Show Luna version number in footer.',
'Info in posts help'				=>	'Show information about the poster under the username in topic view.',
'Post count help'					=>	'Show the number of posts a user has made in topic view, profile and user list.',
'Smilies help'						=>	'Convert smilies to small graphic icons in forum posts.',
'Smilies sigs help'					=>	'Convert smilies to small graphic icons in user signatures.',
'Clickable links help'				=>	'Convert URLs automatically to clickable hyperlinks.',
'Topic review label'				=>	'Topic review',
'Topic review help'					=>	'Maximum amount of posts showed when posting',
'Topics per page help'				=>	'Default amount of topics per page',
'Posts per page label'				=>	'Posts per page',
'Posts per page help'				=>	'Default amount of posts per page',
'Indent label'						=>	'Indent size',
'Index panels head'					=>	'Index settings',
'Moderated by help'				 =>  'Show the "Moderated by" list when moderators are set on a per-forum base.',
'Index statistics help'				=>	'Show the statistics panel on the index.',
'Indent help'						=>	'Amount of spaces that represent a tab',
'Quote depth label'					=>	'Maximum [quote] depth',
'Quote depth help'					=>	'Maximum [quote] can be used in [quote]',
'Video height'					  =>  'Video height',
'Video height help'				 =>  'Height of an embedded video',
'Video width'					   =>  'Video width',
'Video width help'				  =>  'Width of an embedded video',
'Menu items head'					=>	'Additional menu items',
'Menu items help'					=>	'This feature allows you to add more menu items to the navigation bar on every page. The format for adding new links is <code>X = &lt;a href="URL"&gt;LINK&lt;/a&gt;</code> where X is the position at which the link should be inserted. Separate entries with a line break.',
'Default menu'						=>	'Default menu items',
'Menu show index'					=>	'Show the index menu item.',
'Menu show user list'				=>	'Show the user list menu item.',
'Menu show search'					=>	'Show the search menu item.',
'Menu show rules'					=>	'Show the rules menu item.',
'User profile head'					=>	'User profile',
'Title settings head'				=>	'Title settings',
'Title in menu'						=>	'Show board title in menu.',
'Title in header'					=>	'Show board title in header.',
'Description in header'				=>	'Show board description in header.',
'Description settings head'			=>	'Description settings',

// Language for email.php
'Contact head'						=>	'Contact settings',
'Admin e-mail label'				=>	'Admin email',
'Admin e-mail help'					=>	'The admins email',
'Webmaster e-mail label'			=>	'Webmaster email',
'Webmaster e-mail help'				=>	'The email where the boards mails will be addressed from',
'Subscriptions head'				=>	'Subscriptions',
'Forum subscriptions help'			=>	'Enable users to subscribe to forums.',
'Topic subscriptions help'			=>	'Enable users to subscribe to topics.',
'SMTP head'							=>	'SMTP settings',
'SMTP address label'				=>	'SMTP server address',
'SMTP address help'					=>	'The address of an external SMTP server to send emails with',
'SMTP username label'				=>	'SMTP username',
'SMTP username help'				=>	'Username for SMTP server, only if required',
'SMTP password label'				=>	'SMTP password',
'SMTP change password help'			=>	'Check this if you want to change or delete the currently stored password.',
'SMTP password help'				=>	'Password and confirmation for SMTP server, only when required',
'SMTP SSL help'						=>	'Encrypts the connection to the SMTP server using SSL, only when required and supported.',

// Language for features.php
'Features head'						=>	'Features settings',
'General'							=>	'General',
'Topics and posts'					=>	'Topics and posts',
'User features'						=>	'User features',
'Search'							=>	'Search',
'Advanced'							=>	'Advanced',
'Quick post help'					=>	'Show a quick post form so users can post a reaction from the topic view.',
'Responsive post help'			  =>  'Show "Post" and "Preview" button in topic view on small screens, leave quick post enabled when this is disabled to allow small devices to post comments.',
'Users online help'					=>	'Display info on the index page about users currently browsing the board.',
'Censor words help'					=>	'Censor words in posts. See %s for more info.',
'Signatures help'					=>	'Allow users to attach a signature to their posts.',
'User ranks help'					=>	'Use user ranks. See %s for more info.',
'Topic views help'					=>	'Show the number of views for each topic.',
'Has posted help'					=>	'Show a label in front of the topics where users have posted.',
'GZip help'							=>	'Gzip output sent to the browser. This will reduce bandwidth usage, but use some more CPU. This feature requires that PHP is configured with zlib. If you already have one of the Apache modules (mod_gzip/mod_deflate) set up to compress PHP scripts, disable this feature.',
'Enable advanced search'			=>	'Allow users to use the advanced search options.',
'Search all help'					=>	'Allow search only in 1 forum at a time.',

'First run'							=>	'First run',
'General settings'					=>	'General settings',
'Show first run label'				=>	'Show the first run panel when an user logs in for the first time.',
'Show guests label'					=>	'Show the first run panel to guests with login field and registration button.',
'Welcome text'						=>	'Welcome text',
'First run help message'			=>	'The introduction to the forum displayed in the middle of the first run panel',

// Language for forums.php
'Post must be integer message'	=>	'Position must be a positive integer value.',
'New forum'						=>	'New forum',

// Entry page
'Add forum'					=>	'Add forum',
'Update positions'			=>	'Update positions',
'Confirm delete head'		=>	'Confirm delete forum',
'Confirm delete forum info'	=>	'Are you sure that you want to delete the forum <strong>%s</strong>?',
'Confirm delete forum'		=>	'Warning! Deleting a forum will delete all posts (if any) in that forum!',

// Detailed edit page
'Edit forum head'			=>	'Edit forum',
'Edit details subhead'		=>	'Edit forum details',
'Forum name label'			=>	'Forum name',
'Forum description label'	=>	'Description',
'Category label'			=>	'Category',
'Sort by label'				=>	'Sort topics by',
'Topic start'				=>	'Topic start',
'User groups'				=>	'User groups',
'Redirect label'			=>	'Redirect URL',
'Group permissions subhead'	=>	'Edit group permissions',
'Group permissions info'	=>	'In this form, you can set the forum specific permissions for the different user groups. Administrators always have full permissions. Permission settings that differ from the default permissions for the user group are marked red. Some permissions are disabled under some conditions.',
'Read forum label'			=>	'Read forum',
'Post replies label'		=>	'Post replies',
'Post topics label'			=>	'Post topics',
'Revert to default'			=>	'Revert to default',

// Language used in groups.php
'Title already exists message'	=>	'There is already a group with the title <strong>%s</strong>.',
'Cannot remove default message'	=>	'The default group cannot be removed. In order to delete this group, you must first setup a different group as the default.',

'Add group subhead'				=>	'Add new group',
'Create new group'				=>	'Select a group the new group will be based on.',
'Default group subhead'			=>	'Set default group',
'Default group help'			=>	'The default group in which new users will be placed.',
'Existing groups head'			=>	'Manage groups',
'Edit groups info'				=>	'The pre-defined groups can\'t be removed. However, they can be edited. Please note that in some groups, some options are unavailable. Administrators always have full permissions.',
'Group delete head'				=>	'Group delete',
'Confirm delete info'			=>	'Are you sure that you want to delete the group <strong>%s</strong>?',
'Confirm delete warn'			=>	'<b>Warning:</b> After you deleted a group you cannot restore it.',
'Delete group'					=>	'Delete group',
'Move users info'				=>	'The group <strong>%s</strong> currently has <strong>%s</strong> members. Please select a group to which these members will be assigned upon deletion.',
'Move users label'				=>	'Move users to',

'Group settings head'			=>	'Group settings',
'Group settings subhead'		=>	'Setup group options and permissions',
'Group settings info'			=>	'Below options and permissions are the default permissions for the user group. These options apply if no forum specific permissions are in effect.',
'Group title label'				=>	'Group title',
'User title label'				=>	'User title',
'User title help'				=>	'The title will override the user rank',
'Mod privileges label'			=>	'Moderator privileges',
'Mod privileges help'			=>	'In order for a user to have moderator abilities, they must be assigned to moderate one or more forums. This is done via the user administration page of the user\'s profile.',
'Edit profile label'			=>	'Edit user profiles',
'Edit profile help'				=>	'If moderator privileges are enabled, allow users to edit user profiles.',
'Rename users label'			=>	'Rename users',
'Rename users help'				=>	'If moderator privileges are enabled, allow users to rename users.',
'Change passwords label'		=>	'Change passwords',
'Change passwords help'			=>	'If moderator privileges are enabled, allow users to change user passwords.',
'Ban users help'				=>	'If moderator privileges are enabled, allow users to ban users.',
'Read board label'				=>	'Read board',
'Read board help'				=>	'If this is disabled, users will only be able to login and logout.',
'View user info label'			=>	'View user information',
'View user info help'			=>	'Allow users to view the user list and user profiles.',
'Post replies help'				=>	'Allow users to post replies in topics.',
'Post topics help'				=>	'Allow users to post new topics.',
'Edit posts label'				=>	'Edit posts',
'Edit posts help'				=>	'Allow users to edit their own posts.',
'Delete posts help'				=>	'Allow users to delete their own posts.',
'Delete topics help'			=>	'Allow users to delete their own topics (including any replies).',
'Set own title label'			=>	'Set own user title',
'Set own title help'			=>	'Allow users to set their own user title.',
'User search label'				=>	'Use search',
'User search help'				=>	'Allow users to use the search feature.',
'User list search label'		=>	'Search user list',
'User list search help'			=>	'Allow users to search for other users in the user list.',
'Send e-mails label'			=>	'Send e-mails',
'Send e-mails help'				=>	'Allow users to send e-mails to other users.',
'Post flood label'				=>	'Post flood interval',
'Post flood help'				=>	'Time users have to wait between posts',
'Search flood label'			=>	'Search flood interval',
'Search flood help'				=>	'Time users have to wait between searches',
'E-mail flood label'			=>	'Email flood interval',
'E-mail flood help'				=>	'Time users have to wait between emails',
'Report flood label'			=>	'Report flood interval',
'Report flood help'				=>	'Time users have to wait between reports',
'Moderator info'				=>	'Please note that in order for a user to have moderator abilities, they must be assigned to moderate one or more forums. This is done via the user administration page of the user\'s profile.',
'seconds'						=>	'seconds',
'pixels'						=>	'pixels',

// Language used in index.php and update.php for Backstage
'Luna intro'					=>	'Welcome to Luna',
'Backup head'						=>	'Back-up',
'Backup info'						=>	'Create new database backup.',
'Backup button'						=>	'Create new backup',
'New reports head'					=>	'New reports',
'Statistics head'					=>	'Statistics',
'Updates'							=>	'Updates',
'View all'							=>	'View all',
'posts'								=>	'posts',
'replies'							=>	'replies',
'reply'								=>	'reply',
'topics'							=>	'topics',
'views'								=>	'views',
'view'								=>	'view',
'users'								=>	'users',

'Not available'						=>	'Not available',
'NA'								=>	'N/A',
'About head'						=>	'About Luna',
'Luna version label'			=>	'Luna version',
'Luna version data'				=>	'Luna version ',
'Server statistics label'			=>	'Server statistics',
'View server statistics'			=>	'View server statistics',

'Luna software updates'			=>	'Luna software updates',
'Luna updates'					=>	'Luna updates',
'Check for updates'					=>	'Check for updates',
'New version'						=>	'It\'s time to update, a new version is available',
'Latest version'					=>	'Thanks for using the latest version of Luna',
'Development version'				=>	'You\'re using a development release',
'Warning head'						=>	'Warning', 
'Install file exists'				=>	'The file install.php still exists, but should be removed.', 
'Delete install file'				=>	'Delete it', 
'Delete install.php failed'			=>	'Could not remove install.php. Please do so by hand.', 

// Reports
'Date and time'						=>	'Date and time',
'No new reports'					=>	'There are no new reports.',

// Language for maintenance.php
'Rebuild index subhead'			=>	'Rebuild search index',
'Rebuild index info'			=>	'If you changes something about topics and posts in the database you should rebuild the search index. It\'s recommended to activate %s during rebuilding. This can take a while and can increase the server load during the process!',
'Posts per cycle label'			=>	'Posts per cycle',
'Posts per cycle help'			=>	'Number of posts per pageview, this prevents a timeout, 300 recommended',
'Starting post label'			=>	'Starting post ID',
'Starting post help'			=>	'The ID where to start, default is first ID found in database',
'Empty index label'				=>	'Empty index',
'Empty index help'				=>	'Select this if you want the search index to be emptied before rebuilding (see below).',
'Rebuild completed info'		=>	'Be sure to enable JavaScript during the rebuild (to start a new cycle automatically). When you have to abort the rebuilding, remember the last post ID and enter that ID+1 in "Starting post ID" if you want to continue (Uncheck "Empty index").',
'Rebuild index'					=>	'Rebuild index',
'Rebuilding search index'		=>	'Rebuilding search index',
'Rebuilding index info'			=>	'Rebuilding index. This might be a good time to put on some coffee :-)',
'Processing post'				=>	'Processing post <strong>%s</strong> …',
'Click here'					=>	'Click here',
'Javascript redirect failed'	=>	'JavaScript redirect unsuccessful. %s to continue …',
'Posts must be integer message'	=>	'Posts per cycle must be a positive integer value.',
'Days must be integer message'	=>	'Days to prune must be a positive integer value.',
'No old topics message'			=>	'There are no topics that are %s days old. Please decrease the value of "Days old" and try again.',
'Prune subhead'					=>	'Prune old posts',
'Days old label'				=>	'Days old',
'Days old help'					=>	'The number of days old a topic must be to be pruned',
'Prune sticky label'			=>	'Prune sticky topics',
'Prune from label'				=>	'Prune from forum',
'Prune from help'				=>	'What shall we prune?',
'Prune info'					=>	'It\'s recommended to activate %s during pruning.',
'Confirm prune subhead'			=>	'Confirm prune posts',
'Confirm prune info'			=>	'Are you sure that you want to prune all topics older than %s days from %s (%s topics).',
'Confirm prune warn'			=>	'WARNING! Pruning posts deletes them permanently.',

'Prune users head'			=>	'Prune users',
'Prune by'					=>	'Prune by',
'Registed date'				=>	'Registered date',
'Last login'				=>	'Last login',
'Prune by info'				=>	'What should we count to prune?',
'Minimum days'				=>	'Minimum days since registration/last login',
'Minimum days info'			=>	'The minimum amount of days since event specified above',
'Maximum posts'				=>	'Maximum number of posts',
'Maximum posts info'		=>	'How many posts do you require before an users isn\'t pruned',
'Delete admins'				=>	'Delete admins and mods',
'User status'				=>	'User status',
'Delete any'				=>	'Delete any',
'Delete only verified'		=>	'Delete only verified',
'Delete only unverified'	=>	'Delete only unverified',

// Language for settings.php
'Options head'						=>	'Global settings',

// Essentials section
'Essentials subhead'				=>	'Essentials',
'Board desc help'					=>	'What\'s this board about?',
'Base URL label'					=>	'Board URL',
'URL scheme'						=> 'URL scheme',
'Base URL problem'					=>  'Your installation does not support automatic conversion of internationalized domain names. As your base URL contains special characters, you <strong>must</strong> use an online converter.',
'Timezone label'					=>	'Default time zone',
'DST help'							=>	'Advance time by 1 hour for daylight saving.',
'Language help'						=>	'The default language',

// Essentials section timezone options
'UTC-12:00'							=>	'(UTC-12:00) International Date Line West',
'UTC-11:00'							=>	'(UTC-11:00) Niue, Samoa',
'UTC-10:00'							=>	'(UTC-10:00) Hawaii-Aleutian, Cook Island',
'UTC-09:30'							=>	'(UTC-09:30) Marquesas Islands',
'UTC-09:00'							=>	'(UTC-09:00) Alaska, Gambier Island',
'UTC-08:30'							=>	'(UTC-08:30) Pitcairn Islands',
'UTC-08:00'							=>	'(UTC-08:00) Pacific',
'UTC-07:00'							=>	'(UTC-07:00) Mountain',
'UTC-06:00'							=>	'(UTC-06:00) Central',
'UTC-05:00'							=>	'(UTC-05:00) Eastern',
'UTC-04:00'							=>	'(UTC-04:00) Atlantic',
'UTC-03:30'							=>	'(UTC-03:30) Newfoundland',
'UTC-03:00'							=>	'(UTC-03:00) Amazon, Central Greenland',
'UTC-02:00'							=>	'(UTC-02:00) Mid-Atlantic',
'UTC-01:00'							=>	'(UTC-01:00) Azores, Cape Verde, Eastern Greenland',
'UTC'								=>	'(UTC) Western European, Greenwich',
'UTC+01:00'							=>	'(UTC+01:00) Central European, West African',
'UTC+02:00'							=>	'(UTC+02:00) Eastern European, Central African',
'UTC+03:00'							=>	'(UTC+03:00) Eastern African',
'UTC+03:30'							=>	'(UTC+03:30) Iran',
'UTC+04:00'							=>	'(UTC+04:00) Moscow, Gulf, Samara',
'UTC+04:30'							=>	'(UTC+04:30) Afghanistan',
'UTC+05:00'							=>	'(UTC+05:00) Pakistan',
'UTC+05:30'							=>	'(UTC+05:30) India, Sri Lanka',
'UTC+05:45'							=>	'(UTC+05:45) Nepal',
'UTC+06:00'							=>	'(UTC+06:00) Bangladesh, Bhutan, Yekaterinburg',
'UTC+06:30'							=>	'(UTC+06:30) Cocos Islands, Myanmar',
'UTC+07:00'							=>	'(UTC+07:00) Indochina, Novosibirsk',
'UTC+08:00'							=>	'(UTC+08:00) Greater China, Australian Western, Krasnoyarsk',
'UTC+08:45'							=>	'(UTC+08:45) Southeastern Western Australia',
'UTC+09:00'							=>	'(UTC+09:00) Japan, Korea, Chita, Irkutsk',
'UTC+09:30'							=>	'(UTC+09:30) Australian Central',
'UTC+10:00'							=>	'(UTC+10:00) Australian Eastern',
'UTC+10:30'							=>	'(UTC+10:30) Lord Howe',
'UTC+11:00'							=>	'(UTC+11:00) Solomon Island, Vladivostok',
'UTC+11:30'							=>	'(UTC+11:30) Norfolk Island',
'UTC+12:00'							=>	'(UTC+12:00) New Zealand, Fiji, Magadan',
'UTC+12:45'							=>	'(UTC+12:45) Chatham Islands',
'UTC+13:00'							=>	'(UTC+13:00) Tonga, Phoenix Islands, Kamchatka',
'UTC+14:00'							=>	'(UTC+14:00) Line Islands',

// Timeout Section
'Timeouts subhead'					=>	'Time and timeouts',
'PHP manual'						=>	'PHP manual',
'Time format help'					=>	'Now: %s. See %s for more info',
'Date format help'					=>	'Now: %s. See %s for more info',
'Visit timeout label'				=>	'Visit timeout',
'Visit timeout help'				=>	'Time before a visit ends',
'Online timeout label'				=>	'Online timeout',
'Online timeout help'				=>	'Time before someone isn\'t online anymore',

// Feeds section
'Feed subhead'						=>	'Syndication',
'Default feed label'				=>	'Default feed type',
'Default feed help'					=>	'Select a feed',
'None'								=>	'None',
'RSS'								=>	'RSS',
'Atom'								=>	'Atom',
'Feed TTL label'					=>	'Duration to cache feeds',
'Feed TTL help'						=>	'Reduce sources by caching feeds',
'No cache'							=>	'Don\'t cache',
'Minutes'							=>	'%d minutes',

// Reports section
'Reporting method label'			=>	'Reporting method',
'Internal'							=>	'Internal',
'Both'								=>	'Both',
'Reporting method help'				=>	'How should we handle reports?',
'Mailing list label'				=>	'Mailing list',
'Mailing list help'					=>	'A comma separated list of subscribers who get e-mails when new reports are made',

// Avatars section
'Avatars subhead'					=>	'Avatars',
'Use avatars label'					=>	'Use avatars',
'Use avatars help'					=>	'Enable so users can upload avatars.',
'Upload directory label'			=>	'Upload directory',
'Upload directory help'				=>	'Where avatars will be stored relative to Lunas root, write permission required',
'Max width label'					=>	'Max width',
'Max height label'					=>	'Max height',
'Max size label'					=>	'Max size',

// Registration Section
'Allow new label'					=>	'Allow new registrations',
'Allow new help'					=>	'Allow new users to be made by people.',
'Verify label'						=>	'Verify registrations',
'Verify help'						=>	'Send a random password to users to verify their email address.  ',
'Report new label'					=>	'Report new registrations',
'Report new help'					=>	'Notify people on the mailing list when new user registers.  ',
'Use rules label'					=>	'User forum rules',
'Use rules help'					=>	'Require users to agree with the rules. This will also enable a "Rules" link in the navigation bar.',
'Rules label'						=>	'Enter your rules here',
'Rules help'						=>	'Enter rules or useful information, required when rules are enabled',
'E-mail default label'				=>	'Default email setting',
'E-mail default help'				=>	'Default privacy setting for new registrations',
'Display e-mail label'				=>	'Display email address to other users.',
'Hide allow form label'				=>	'Hide email address but allow form e-mail.',
'Hide both label'					=>	'Hide email address and disallow form email.',

// Announcement Section
'Announcements'						=>	'Announcements',
'Display announcement help'			=>	'Enable this to display the below message in the board.',

// Maintenance Section
'Maintenance mode help'				=>	'Enable to activate maintenance mode, the board will only be available for admins. Do not log out when this feature is active!',
'Maintenance message help'			=>	'The message to tell users about the maintenance',
'Cache'								=>	'Cache',
'Cache info'						=>	'Remove all cache files so the database has to return up-to-date values',
'Clear cache'						=>	'Clear cache',

// Language for permissions.php
'All caps'					=>	'All caps',
'Posting subhead'			=>	'Posting',
'BBCode help'				=>	'Allow BBCode in posts (recommended).',
'Image tag help'			=>	'Allow the BBCode [img] tag in posts.',
'All caps message help'		=>	'Allow a message to contain only capital letters.',
'All caps subject help'		=>	'Allow a subject to contain only capital letters.',
'Require e-mail help'		=>	'Require guests to supply an email address when posting.',
'Signatures subhead'		=>	'Signatures',
'BBCode sigs help'			=>	'Allow BBCode in user signatures.',
'Image tag sigs help'		=>	'Allow the BBCode [img] tag in user signatures (not recommended).',
'All caps sigs help'		=>	'Allow a signature to contain only capital letters.',
'Max sig length label'		=>	'Maximum signature length',
'Max sig length help'		=>	'Maximum amount of characters a signature can have',
'Max sig lines label'		=>	'Maximum signature lines',
'Max sig lines help'		=>	'Maximum amount of lines a signature can have',
'Banned e-mail help'		=>	'Allow users to use a banned email address, mailing list will be warned when this happens.',
'Duplicate e-mail help'		=>	'Allow users to use an email address that is already used, mailing list will be warned when this happens.',

// Language for ranks.php
'Must be integer message'	=>	'Minimum posts must be a positive integer value.',
'Dupe min posts message'	=>	'There is already a rank with a minimun posts value of %s.',
'Add rank subhead'			=>	'Add rank',
'Ranks disabled'			=>	'<strong>User ranks is disabled in %s.</strong>',
'Rank title label'			=>	'Rank title',
'Minimum posts label'		=>	'Minimum posts',
'Edit remove subhead'		=>	'Edit/remove ranks',
'No ranks in list'			=>	'No ranks in list',

// Language for reports.php
'Deleted user'				=>	'Deleted user',
'Deleted'					=>	'Deleted',
'Post ID'					=>	'Post #%s',
'Reported by'				=>	'Reported by',
'Actions'					=>	'Actions',
'Zap'						=>	'Mark as read',
'Last 10 head'				=>	'10 last read reports',
'Readed by'					=>	'Marked as read by',
'No zapped reports'			=>	'There are no read reports.',

// Language for statistics.php
'PHPinfo disabled message'			=>	'The PHP function phpinfo() has been disabled on this server.',
'Server statistics head'			=>	'Server statistics',
'Server load label'					=>	'Server load',
'Server load data'					=>	'%s - %s user(s) online',
'Environment label'					=>	'Environment',
'Environment data OS'				=>	'Operating system: %s',
'Show info'							=>	'Show info',
'Environment data version'			=>	'PHP: %s - %s',
'Environment data acc'				=>	'Accelerator: %s',
'Turck MMCache'						=>	'Turck MMCache',
'Turck MMCache link'				=>	'turck-mmcache.sourceforge.net/',
'ionCube PHP Accelerator'			=>	'ionCube PHP Accelerator',
'ionCube PHP Accelerator link'		=>	'www.php-accelerator.co.uk/',
'Alternative PHP Cache (APC)'		=>	'Alternative PHP Cache (APC)',
'Alternative PHP Cache (APC) link'	=>	'www.php.net/apc/',
'Zend Optimizer'					=>	'Zend Optimizer',
'Zend Optimizer link'				=>	'www.zend.com/products/guard/zend-optimizer/',
'eAccelerator'						=>	'eAccelerator',
'eAccelerator link'					=>	'www.eaccelerator.net/',
'XCache'							=>	'XCache',
'XCache link'						=>	'xcache.lighttpd.net/',
'Database label'					=>	'Database',
'Database data rows'				=>	'Rows: %s',
'Database data size'				=>	'Size: %s',

// Language for users.php
'Non numeric message'		=>	'You entered a non-numeric value into a numeric only column.',
'Invalid date time message'	=>	'You entered an invalid date/time.',
'Not verified'				=>	'Not verified',

// Actions: mass delete/ban etc.
'No users selected'			=>	'No users selected.',
'No move admins message'	=>	'For security reasons, you are not allowed to move multiple administrators to another group. If you want to move these administrators, you can do so on their respective user profiles.',
'No delete admins message'	=>	'Administrators cannot be deleted. In order to delete administrators, you must first move them to a different user group.',
'No ban admins message'		=>	'Administrators cannot be banned. In order to ban administrators, you must first move them to a different user group.',
'No ban mods message'		=>	'Moderators cannot be banned. In order to ban moderators, you must first move them to a different user group.',
'Move users'				=>	'Change user group',
'New group label'			=>	'New group',
'New group help'			=>	'Select a new user group',
'Invalid group message'		=>	'Invalid group ID.',
'Delete users'				=>	'Delete users',
'Ban users'					=>	'Ban users',
'Ban IP label'				=>	'Ban IP addresses',
'Ban IP help'				=>	'Also ban the IP addresses of the banned users to make registering a new account more difficult for them.',

'E-mail address label'		=>	'Email address',
'Real name label'			=>	'Real name',
'Signature'					=>	'Signature',
'Posts more than label'		=>	'Number of posts greater than',
'Posts less than label'		=>	'Number of posts less than',
'Last post after label'		=>	'Last post is after',
'Last post before label'	=>	'Last post is before',
'Last visit after label'	=>	'Last visit is after',
'Last visit before label'	=>	'Last visit is before',
'Registered after label'	=>	'Registered after',
'Registered before label'	=>	'Registered before',
'Order by posts'			=>	'Number of posts',
'Order by last visit'		=>	'Last visit',
'Order by registered'		=>	'Registered',
'All groups'				=>	'All groups',
'Unverified users'			=>	'Unverified users',
'IP search head'			=>	'IP search',
'IP address label'			=>	'IP address',
'IP address help'			=>	'The IP address to search for in the post database.',
'Find IP address'			=>	'Find IP address',

'Results title head'		=>	'Title/Status',
'Results posts head'		=>	'Posts',
'Results last used head'	=>	'Last used',
'Results times found head'	=>	'Times found',
'Results find more link'	=>	'Find more users for this ip',
'Results no posts found'	=>	'There are currently no posts by that user in the forum.',
'Ban'						=>	'Ban',
'Change group'				=>	'Change group',
'Bad IP message'			=>	'The supplied IP address is not correctly formatted.',
'Results view IP link'		=>	'IP stats',
'Results no IP found'		=>	'The supplied IP address could not be found in the database.',

// Create new user
'Add user head'				=>	'Add user',
'Random password info'		=>	'Generate a random password, this will be emailed to the above address. When checked, leave "Password" empty.',

// Common language used in /backstage/
// Main menu
'Content'				=>	'Content',
'Forums'				=>	'Forums',
'Forum settings'		=>	'Forum settings',
'Categories'			=>	'Categories',
'Board'					=>	'Board',
'Board structure'		=>	'Board structure',
'Censoring'				=>	'Censoring',
'Reports'				=>	'Reports',
'Users'					=>	'Users',
'Ranks'					=>	'Ranks',
'Groups'				=>	'Groups',
'Permissions'			=>	'Permissions',
'Bans'					=>	'Bans',
'Settings'				=>	'Settings',
'Global'				=>	'Global',
'Registration'			=>	'Registration',
'Email'					=>	'Email',
'Database'				=>	'Database management',
'Extensions'			=>	'Extensions',

// Others
'Prune'					=>	'Prune',
'Server statistics'		=>  'Server statistics',

// Update service
'Available'				=>	'Luna v%s is available, %s!',
'update now'			=>	'update now',
'Development'			=>	'You are running version %s, the latest stable release is version %s.',
'Download'				=>	'Download v%s',
'Changelog'				=>	'Changelog',

// General actions and more
'Admin'					=>	'Admin',
'Update'				=>	'Update',
'Add'					=>	'Add',
'Remove'				=>	'Remove',
'Yes'					=>	'Yes',
'No'					=>	'No',
'here'					=>	'here',
'Action'				=>	'Action',
'Maintenance mode'		=>	'maintenance mode', // Used for link text in more than one file

// Cookie bar
'Cookie bar'			=>	'Cookie bar',
'Cookie info'			=>	'We use cookies to give you the best experience on this board.',
'More info'				=>	'More info',
'Cookie set info'		=>	'Show a bar with information about cookies at the bottom of the page.',

// Admin loader
'No plugin message'		=>	'There is no plugin called %s in the plugin directory.',
'Plugin failed message'	=>	'Loading of the plugin - <strong>%s</strong> - failed.',

// Common
'Login required'		=>	'You must be logged in to use the privates messages system',
'Disabled PM'			=>	'You have disable the privates messages system',
'Private Messages'		=>	'Private Messages',
'PM'					=>	'PM',
'Quick message'			=>	'Send private message',
'Write message'			=>	'Send new message',
'Inbox'					=>	'Inbox',
'Outbox'				=>	'Sent',
'Please confirm'		=>	'Please confirm',
'Full boxes'			=>	'Your private message boxes are full!',
'Empty boxes'			=>	'Your private message boxes are empty.',
'Full to'				=>	'Private message boxes full to %s',
'Select'				=>	'Select',
'For select'			=>	'For the selection:',
'Messages'				=>	'Messages',
'OK'					=>	'OK',
'PM Menu'				=>	'Private messaging',
'Sending lists'			=>	'Sending lists',

// List a box
'Date'					=>	'Date',
'Subject'				=>	'Subject',
'Sender'				=>	'Sender',
'Receiver'				=>	'Receiver(s)',
'Mark as read select'	=>	'Mark as read',
'Mark as unread select'	=>	'Mark as unread',
'Mark all'				=>	'Mark all messages as read',
'Must select'			=>	'You must select some messages',
'No messages'			=>	'No messages',
'Unknown'				=>	'Unknown',

// View a message
'View'					=>	'View a private discussion',
'Reply'					=>	'Reply',
'Quote'					=>	'Quote',
'Deleted User'			=>	'Deleted User',
'Deleted'				=>	'(deleted)',
'With'					=>	'with',

// Send a message
'Send a message'		=>	'Send a message',
'Send to'				=>	'Send to',
'Send multiple'			=>	'You can send the message to several receivers by separating them by commas. Maximum: ',
'Save message'			=>	'Save message in "Sent" box',
'Send'					=>	'Send',
'Sent redirect'			=>	'Messages sent to user, redirecting...',
'No user'				=>	'There\'s no user with the username "%s".',
'Dest full'				=>	'%s inbox is full, you can not send you message to this user.',
'Sender full'			=>	'Can\'t save message, your boxes are full.',
'Flood'					=>	'At least % seconds have to pass between sends. Please wait a little while and try send the message again.',
'Must receiver'			=>	'You must give at least one receiver',
'Too many receiver'		=>	'You can send a message at the same time only to %s receivers maximum.',
'User blocked'			=>	'%s refuses the private messages.',
'User disable PM'		=>	'%s disabled the private messages.',
'User left'				=>	'%s has left the conversation.',

// Multidelete
'Multidelete'			=>	'Delete multiple messages',
'Delete messages comply'=>	'Are you sure you want to delete the selected messages?',

// Delete
'Delete message'		=>	'Delete message',
'Delete message comply'	=>	'Are you sure you want to delete the message?',
'Topic warning info'	=>	'The topic will be deleted from your inbox, but it will stays in the others receivers\' boxes.',
'Delete for everybody'	=>	'If you tick this checkbox, you will delete the message (or the topic) for all the receivers (available only for admins &amp; mods)',

// profile.php
'use_pm_option'			=>	'Enable privates messages system',
'email_option_infos'	=>	'With this enabled, an e-mail will be sent for all new private message.',
'email_option'			=>	'Privates messages notification by e-mail',
'email_option_full'		=>	'Include private messages content',

// Email templtes
// Email - activate_email.tpl
'activate_email.tpl'		  =>
'Subject: Change email address requested

Hello <username>,

You have requested to have a new email address assigned to your account in the discussion forum at <base_url>. If you did not request this or if you do not want to change your email address you should just ignore this message. Only if you visit the activation page below will your email address be changed. In order for the activation page to work, you must be logged in to the forum.

To change your email address, please visit the following page:
<activation_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - activate_password.tpl
'activate_password.tpl'		  =>
'Subject: New password requested

Hello <username>,

You have requested to have a new password assigned to your account in the discussion forum at <base_url>. If you did not request this or if you do not want to change your password you should just ignore this message. Only if you visit the activation page below will your password be changed.

Your new password is: <new_password>

To change your password, please visit the following page:
<activation_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - banned_email_change.tpl
'banned_email_change.tpl'		  =>
'Subject: Alert - Banned email detected

User "<username>" changed to banned email address: <email>

User profile: <profile_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - banned_email_post.tpl
'banned_email_post.tpl'		  =>
'Subject: Alert - Banned email detected

User "<username>" posted with banned email address: <email>

Post URL: <post_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - banned_email_register.tpl
'banned_email_register.tpl'		  =>
'Subject: Alert - Banned email detected

User "<username>" registered with banned email address: <email>

User profile: <profile_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - dupe_email_change.tpl
'dupe_email_change.tpl'		  =>
'Subject: Alert - Duplicate email detected

User "<username>" changed to an email address that also belongs to: <dupe_list>

User profile: <profile_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - dupe_email_register.tpl
'dupe_email_register.tpl'		  =>
'Subject: Alert - Duplicate email detected

User "<username>" registered with an email address that also belongs to: <dupe_list>

User profile: <profile_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - form_email.tpl
'form_email.tpl'		  =>
'Subject: <mail_subject>

<sender> from <board_title> has sent you a message. You can reply to <sender> by replying to this email.

The message reads as follows:
-----------------------------------------------------------------------

<mail_message>

-----------------------------------------------------------------------

--
<board_mailer> Mailer',

// Email - new_reply.tpl
'new_reply.tpl'		  =>
'Subject: Reply to topic: "<topic_subject>"

<replier> has replied to the topic "<topic_subject>" to which you are subscribed. There may be more new replies, but this is the only notification you will receive until you visit the board again.

The post is located at <post_url>

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - new_reply_full.tpl
'new_reply_full.tpl'		  =>
'Subject: Reply to topic: "<topic_subject>"

<replier> has replied to the topic "<topic_subject>" to which you are subscribed. There may be more new replies, but this is the only notification you will receive until you visit the board again.

The post is located at <post_url>

The message reads as follows:
-----------------------------------------------------------------------

<message>

-----------------------------------------------------------------------

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - new_report.tpl
'new_report.tpl'		  =>
'Subject: Report(<forum_id>) - "<topic_subject>"

User "<username>" has reported the following message: <post_url>

Reason: <reason>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - new_topic.tpl
'new_topic.tpl'		  =>
'Subject: New topic in forum: "<forum_name>"

<poster> has posted a new topic "<topic_subject>" in the forum "<forum_name>", to which you are subscribed.

The topic is located at <topic_url>

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - new_topic_full.tpl
'new_topic_full.tpl'		  =>
'Subject: New topic in forum: "<forum_name>"

<poster> has posted a new topic "<topic_subject>" in the forum "<forum_name>", to which you are subscribed.

The topic is located at <topic_url>

The message reads as follows:
-----------------------------------------------------------------------

<message>

-----------------------------------------------------------------------

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - new_user.tpl
'new_user.tpl'		  =>
'Subject: Alert - New registration

User "<username>" registered in the forums at <base_url>

User profile: <profile_url>

To administer this account, please visit the following page:
<admin_url>

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - rename.tpl
'rename.tpl'		  =>
'Subject: User account renamed

During an upgrade to the forums at <base_url> it was determined your username is too similar to an existing user. Your username has been changed accordingly.

Old username: <old_username>
New username: <new_username>

We apologise for any inconvenience caused.

--
<board_mailer> Mailer
(Do not reply to this message)',

// Email - welcome.tpl
'welcome.tpl'		  =>
'Subject: Welcome to <board_title>!

Thank you for registering in the forums at <base_url>. Your account details are:

Username: <username>
Password: <password>

Login at <login_url> to activate the account.

--
<board_mailer> Mailer
(Do not reply to this message)',

);