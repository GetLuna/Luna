<?php

// Language definitions used in admin_options.php
$lang_admin_email = array(

'Bad HTTP Referer message'			=>	'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.',
'Must enter title message'			=>	'You must enter a board title.',
'Invalid e-mail message'			=>	'The admin email address you entered is invalid.',
'Invalid webmaster e-mail message'	=>	'The webmaster email address you entered is invalid.',
'SMTP passwords did not match'		=>	'You need to enter the SMTP password twice exactly the same to change it.',
'Enter announcement here'			=>	'Enter your announcement here.',
'Enter rules here'					=>	'Enter your rules here.',
'Default maintenance message'		=>	'The forums are temporarily down for maintenance. Please try again in a few minutes.',
'Timeout error message'				=>	'The value of "Timeout online" must be smaller than the value of "Timeout visit".',
'Options updated redirect'			=>	'Options updated. Redirecting …',
'E-mail head'						=>	'Email settings',

'E-mail subhead'					=>	'Email',
'Admin e-mail label'				=>	'Admin email',
'Admin e-mail help'					=>	'The email address of the board administrator.',
'Webmaster e-mail label'			=>	'Webmaster email',
'Webmaster e-mail help'				=>	'This is the address that all emails sent by the board will be addressed from.',
'Forum subscriptions label'			=>	'Forum subscriptions',
'Forum subscriptions help'			=>	'Enable users to subscribe to forums (receive email when someone creates a new topic).',
'Topic subscriptions label'			=>	'Topic subscriptions',
'Topic subscriptions help'			=>	'Enable users to subscribe to topics (receive email when someone replies).',
'SMTP address label'				=>	'SMTP server address',
'SMTP address help'					=>	'The address of an external SMTP server to send emails with. You can specify a custom port number if the SMTP server doesn\'t run on the default port 25 (example: mail.myhost.com:3580). Leave blank to use the local mail program.',
'SMTP username label'				=>	'SMTP username',
'SMTP username help'				=>	'Username for SMTP server. Only enter a username if it is required by the SMTP server (most servers <strong>do not</strong> require authentication).',
'SMTP password label'				=>	'SMTP password',
'SMTP change password help'			=>	'Check this if you want to change or delete the currently stored password.',
'SMTP password help'				=>	'Password for SMTP server. Only enter a password if it is required by the SMTP server (most servers <strong>do not</strong> require authentication). Please enter your password twice to confirm.',
'SMTP SSL label'					=>	'Encrypt SMTP using SSL',
'SMTP SSL help'						=>	'Encrypts the connection to the SMTP server using SSL. Should only be used if your SMTP server requires it and your version of PHP supports SSL.',
);
