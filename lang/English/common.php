<?php

// Language definitions for frequently used strings
$lang_common = array(

// Text orientation and encoding
'lang_direction'					=>	'ltr', // ltr (Left-To-Right) or rtl (Right-To-Left)
'lang_identifier'					=>	'en',

// Number formatting
'lang_decimal_point'				=>	'.',
'lang_thousands_sep'				=>	',',

// Notices
'Bad request'						=>	'Bad request. The link you followed is incorrect or outdated.',
'No view'							=>	'You do not have permission to view these forums.',
'No permission'						=>	'You do not have permission to access this page.',
'Bad referrer'						=>	'Bad HTTP_REFERER. You were referred to this page from an unauthorized source. If the problem persists please make sure that \'Base URL\' is correctly set in Admin/Options and that you are visiting the forum by navigating to that URL. More information regarding the referrer check can be found in the ModernBB documentation.',
'No cookie'							=>	'You appear to have logged in successfully, however a cookie has not been set. Please check your settings and if applicable, enable cookies for this website.',
'Pun include extension'				=>  'Unable to process user include %s from template %s. "%s" files are not allowed',  
'Pun include directory'				=>  'Unable to process user include %s from template %s. Directory traversal is not allowed',  
'Pun include error'					=>  'Unable to process user include %s from template %s. There is no such file in neither the template directory nor in the user include directory',  

// Miscellaneous
'Announcement'						=>	'Announcement',
'Options'							=>	'Options',
'Submit'							=>	'Submit', // "Name" of submit buttons
'Ban message'						=>	'You are banned from this forum.',
'Ban message 2'						=>	'The ban expires at the end of',
'Ban message 3'						=>	'The administrator or moderator that banned you left the following message:',
'Ban message 4'						=>	'Please direct any inquiries to the forum administrator at',
'Never'								=>	'Never',
'Today'								=>	'Today',
'Yesterday'							=>	'Yesterday',
'Info'								=>	'Info', // A common table header
'Go back'							=>	'Go back',
'Maintenance'						=>	'Maintenance',
'Redirecting'						=>	'Redirecting',
'Click redirect'					=>	'Click here if you do not want to wait any longer (or if your browser does not automatically forward you)',
'on'								=>	'on', // As in "BBCode is on"
'off'								=>	'off',
'Invalid email'						=>	'The email address you entered is invalid.',
'Required'							=>	'(Required)',
'required field'					=>	'is a required field in this form.', // For javascript form validation
'Last post'							=>	'Last post',
'by'								=>	'by', // As in last post by some user
'New posts'							=>	'New posts', // The link that leads to the first new post
'New posts info'					=>	'Go to the first new post in this topic.', // The popup text for new posts links
'Username'							=>	'Username',
'Password'							=>	'Password',
'Email'								=>	'Email',
'Send email'						=>	'Send email',
'Moderated by'						=>	'Moderated by',
'Registered'						=>	'Registered',
'Subject'							=>	'Subject',
'Message'							=>	'Message',
'Topic'								=>	'Topic',
'Forum'								=>	'Forum',
'Posts'								=>	'Posts',
'Replies'							=>	'Replies',
'Pages'								=>	'Pages:',
'Page'								=>	'Page %s',
'BBCode'							=>	'BBCode:', // You probably shouldn't change this
'img tag'							=>	'[img] tag:',
'Smilies'							=>	'Smilies:',
'and'								=>	'and',
'Image link'						=>	'image', // This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'								=>	'wrote:', // For [quote]'s
'Mailer'							=>	'%s Mailer', // As in "MyForums Mailer" in the signature of outgoing emails
'Important information'				=>	'Important information',
'Write message legend'				=>	'Write your message and submit',
'Previous'							=>	'Previous',
'Next'								=>	'Next',
'Spacer'							=>	'…', // Ellipsis for paginate

// Title
'Title'								=>	'Title',
'Member'							=>	'Member', // Default title
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
'BBCode code problem'				=>	'There is a problem with your [code] tags',
'BBCode list size error'			=>	'Your list was too long to parse, please make it smaller!',

// Stuff for the navigator (top of every page)
'Index'								=>	'Index',
'User list'							=>	'User list',
'Rules'								=>	'Rules',
'Search'							=>	'Search',
'Register'							=>	'Register',
'Login'								=>	'Login',
'Not logged in'						=>	'You are not logged in.',
'Profile'							=>	'Profile',
'Logout'							=>	'Logout',
'Logged in as'						=>	'Logged in as',
'Admin'								=>	'Backstage',
'Last visit'						=>	'Last visit: %s',
'Topic searches'					=>	'Topics:',
'New posts header'					=>	'New',
'Active topics'						=>	'Active',
'Unanswered topics'					=>	'Unanswered',
'Posted topics'						=>	'Posted',
'Show new posts'					=>	'Find topics with new posts since your last visit.',
'Show active topics'				=>	'Find topics with recent posts.',
'Show unanswered topics'			=>	'Find topics with no replies.',
'Show posted topics'				=>	'Find topics you have posted to.',
'Mark all as read'					=>	'Mark all topics as read',
'Mark forum read'					=>	'Mark this forum as read',
'Title separator'					=>	' / ',

// Stuff for the page footer
'Board footer'						=>	'Board footer',
'Jump to'							=>	'Jump to',
'Go'								=>	' Go ', // Submit button in forum jump
'Moderate topic'					=>	'Moderate topic',
'Move topic'						=>	'Move topic',
'Open topic'						=>	'Open topic',
'Close topic'						=>	'Close topic',
'Unstick topic'						=>	'Unstick topic',
'Stick topic'						=>	'Stick topic',
'Moderate forum'					=>	'Moderate forum',
'Powered by'						=>	'Powered by %s',
'Thanks'							=>	'Thanks for using %s',
'Version'							=>	'Version',

// Debug information
'Debug table'						=>	'Debug information',
'Querytime'							=>	'Generated in %1$s seconds, %2$s queries executed',
'Memory usage'						=>	'Memory usage: %1$s',
'Peak usage'						=>	'(Peak: %1$s)',
'Query times'						=>	'Time (s)',
'Query'								=>	'Query',
'Total query time'					=>	'Total query time: %s',

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

// Admin related stuff in the header
'New reports'						=>	'There are new reports',
'Maintenance mode enabled'			=>	'Maintenance mode is enabled!',

// Units for file sizes
'Size unit B'						=>	'%s B',
'Size unit KiB'						=>	'%s KiB',
'Size unit MiB'						=>	'%s MiB',
'Size unit GiB'						=>	'%s GiB',
'Size unit TiB'						=>	'%s TiB',
'Size unit PiB'						=>	'%s PiB',
'Size unit EiB'						=>	'%s EiB',

// Toolbar
'enable_js'					=>	'Please enable javascript to activate the text formatting tools.',
'bt_smilies'				=>	'Click here to display the smilies toolbar',
'all_smilies'				=>	'All smilies',
'Cancel'					=>	'Cancel',
'Signature balises'			=>	'Text alignment and video BBCode tags are not allowed in signatures. Please go back and correct.',

// Buttons
'bt_bold'			=>	'Bold text: [b]text[/b]',
'bt_italic'			=>	'Italic text: [i]text[/i]',
'bt_underline'		=>	'Underlined text: [u]text[/u]',
'bt_strike'			=>	'Strike-through text: [s]text[/s]',
'bt_color'			=>	'Text color: [color=#000000]text[/color]',
'bt_heading'		=>	'Heading text: [h]text[/h]',
'bt_quote'			=>	'Blockquote: [quote=user]text[/quote]',
'bt_quote_msg_1'	=>	'Please enter the name of the quoted user (or leave blank):',
'bt_code'			=>	'Pre-formatted text: [code]text[/code]',
'bt_link'			=>	'Link: [url=http://www.website.ltd/]a website[/url]',
'bt_link_msg_1'		=>	'Please enter the URL of your link:',
'bt_link_msg_2'		=>	'Please enter a descriptive name of your link:',
'bt_img'			=>	'Image: [img=text]http://www.website.ltd/url-image.png[/img]',
'bt_img_msg_1'		=>	'Please enter the URL of your image:',
'bt_img_msg_2'		=>	'Please enter a descriptive name for your image (alt attribute):',
'bt_email'			=>	'E-mail address: [email=name@host.ltd]email[/email]',
'bt_email_msg_1'	=>	'Please enter a valid e-mail address:',
'bt_email_msg_2'	=>	'Please enter a descriptive name for your e-mail link:',
'bt_list'			=>	'List: [list=*,1,a]text[/list]',
'bt_list_msg_1'		=>	'Please enter the type of list: * = bulleted list, 1 = numbered list, a = alphabetically labelled list',
'bt_li'				=>	'List element: [*]text[/*]',

'bt_acronym'		=>	'Acronym: [acronym=World Wide Web Consortium]W3C[/acronym]',
'bt_acronym_msg_1'	=>	'This acronym stands for:',
'bt_q'				=>	'Inline quote: [q]text[/q]',
'bt_sup'			=>	'Superscript: [sup]text[/sup]',
'bt_sub'			=>	'Subscript: [sub]text[/sub]',
'bt_left'			=>	'Align to left: [left]text[/left]',
'bt_right'			=>	'Align to right: [right]text[/right]',
'bt_center'			=>	'Center: [center]text[/center]',
'bt_justify'		=>	'Justify: [justify]text[/justify]',
'bt_video'			=>	'Video: [video]video[/video]',
'bt_video_msg_1'	=>	'Please enter the URL of your video (youtube or dailymotion, WITHOUT http://www part):\n(youtube.com/watch?v=xxxxxxxx or dailymotion.com/video/xxxxxxxx)'

);
