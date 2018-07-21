<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_SEARCH_MIN_WORD', 3);
define('LUNA_SEARCH_MAX_WORD', 20);

define('LUNA_ROOT', dirname(__FILE__).'/');

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Load the functions script
require LUNA_ROOT.'include/functions.php';
require LUNA_ROOT.'include/draw_functions.php';

// Load Version class
require LUNA_ROOT.'include/version.php';

// Load Installer class
require LUNA_ROOT.'include/install.php';

// Load UTF-8 functions
require LUNA_ROOT.'include/utf8/utf8.php';

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

// It might happen you are redirected to this page from backstage/update.php
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Disable error reporting for uninitialized variables
error_reporting(E_ALL);

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');

// Turn off magic_quotes_runtime
if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(0);
}

// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if (get_magic_quotes_gpc()) {
    function stripslashes_array($array)
    {
        return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
    }

    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
    $_REQUEST = stripslashes_array($_REQUEST);
}

// Turn off PHP time limit
@set_time_limit(0);

// If we've been passed a default language, use it
$install_lang = isset($_REQUEST['install_lang']) ? luna_trim($_REQUEST['install_lang']) : Installer::DEFAULT_LANG;

// Make sure we got a valid language string
$install_lang = preg_replace('%[\.\\\/]%', '', $install_lang);

// If such a language pack doesn't exist, or isn't up-to-date enough to translate this page, default to English
if (!file_exists(LUNA_ROOT.'lang/'.$install_lang.'/luna.mo')) {
    $install_lang = 'English';
}

// Load l10n
require_once LUNA_ROOT.'include/pomo/MO.php';
require_once LUNA_ROOT.'include/l10n.php';

load_textdomain('luna', LUNA_ROOT.'lang/'.$install_lang.'/luna.mo');

// If a config file is in place
if (file_exists(LUNA_ROOT.'config.php')) {
    // Check to see whether Luna is already installed
    include LUNA_ROOT.'config.php';

    // This fixes incorrect defined PUN, FluxBB 1.4 and 1.5 and ModernBB 1.6
    if (defined('PUN')) {
        define('FORUM', PUN);
    }

    // If FORUM is defined, config.php is probably valid and thus the software is installed
    if (defined('FORUM')) {
        draw_wall_error(__('It seems like Luna is already installed.', 'luna'), '<a class="btn btn-default btn-lg" href="index.php">'.__('Continue', 'luna').'</a>');
    }

    exit;
}

// Define FORUM because email.php requires it
define('FORUM', 1);

// If the cache directory is not specified, we use the default setting
if (!defined('LUNA_CACHE_DIR')) {
    define('LUNA_CACHE_DIR', LUNA_ROOT.'cache/');
}

// Make sure we are running at least Version::MIN_PHP_VERSION
if (!Installer::is_supported_php_version()) {
    exit(sprintf(__('You are running %1$s version %2$s. Luna %3$s requires at least %1$s %4$s to run properly. You must upgrade your %1$s installation before you can continue.', 'luna'), 'PHP', PHP_VERSION, Version::LUNA_VERSION, Version::MIN_PHP_VERSION));
}

if (isset($_POST['generate_config'])) {
    header('Content-Type: text/x-delimtext; name="config.php"');
    header('Content-disposition: attachment; filename=config.php');

    echo Installer::generate_config_file($_POST['db_type'], $_POST['db_host'], $_POST['db_name'], $_POST['db_username'], $_POST['db_password'], $_POST['db_prefix']);
    exit;
}

if (!isset($_POST['form_sent'])) {
    // Make an educated guess regarding base_url
    $base_url = Installer::guess_base_url();

    // Make sure base_url doesn't end with a slash
    if (substr($base_url, -1) == '/') {
        $base_url = substr($base_url, 0, -1);
    }

    $db_type = $db_name = $db_username = $db_prefix = $username = $email = '';
    $db_host = 'localhost';
    $title = Version::LUNA_CODE_NAME;
    $slogan = __('You can do anything', 'luna');
    $description = null;
    $default_lang = $install_lang;
    $default_style = Installer::DEFAULT_STYLE;
} else {
    $db_type = $_POST['req_db_type'];
    $db_host = luna_trim($_POST['req_db_host']);
    $db_name = luna_trim($_POST['req_db_name']);
    $db_username = luna_trim($_POST['db_username']);
    $db_password = luna_trim($_POST['db_password']);
    $db_prefix = luna_trim($_POST['db_prefix']);
    $username = luna_trim($_POST['req_username']);
    $email = strtolower(luna_trim($_POST['req_email']));
    $password1 = luna_trim($_POST['req_password1']);
    $password2 = luna_trim($_POST['req_password2']);
    $title = luna_trim($_POST['req_title']);
    $slogan = luna_trim($_POST['slogan']);
    $description = luna_trim($_POST['description']);
    $base_url = luna_trim($_POST['req_base_url']);
    $default_lang = luna_trim($_POST['req_default_lang']);
    $default_style = luna_trim($_POST['req_default_style']);

    // Make sure base_url doesn't end with a slash
    if (substr($base_url, -1) == '/') {
        $base_url = substr($base_url, 0, -1);
    }

    $alerts = Installer::validate_config($username, $password1, $password2, $email, $title, $default_lang, $default_style);
}

// Check if the cache directory is writable
if (!forum_is_writable(LUNA_CACHE_DIR)) {
    $alerts[] = sprintf(__('<strong>The cache directory is currently not writable!</strong> In order for Luna to function properly, the directory <em>%s</em> must be writable by PHP. Use chmod to set the appropriate directory permissions. If in doubt, chmod to 0777.', 'luna'), LUNA_CACHE_DIR);
}

// Check if default avatar directory is writable
if (!forum_is_writable(LUNA_ROOT.'img/avatars/')) {
    $alerts[] = sprintf(__('<strong>The avatar directory is currently not writable!</strong> If you want users to be able to upload their own avatar images you must see to it that the directory <em>%s</em> is writable by PHP. You can later choose to save avatar images in a different directory (see Backstage > Settings). Use chmod to set the appropriate directory permissions. If in doubt, chmod to 0777.', 'luna'), LUNA_ROOT.'img/avatars/');
}

if (!isset($_POST['form_sent']) || !empty($alerts)) {
    // Determine available database extensions
    $db_extensions = Installer::determine_database_extensions();

    if (empty($db_extensions)) {
        error(__('PHP needs to have support for either MySQL or SQLite to run Luna to be installed. Non is available, though', 'luna'));
    }

    // Fetch a list of installed languages
    $languages = forum_list_langs();

    ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php _e('Luna Installation', 'luna')?></title>
		<link rel="stylesheet" type="text/css" href="vendor/css/bootstrap.min.css" />
		<link rel="stylesheet" href="vendor/css/fontawesome-all.min.css">
		<link rel="stylesheet" type="text/css" href="backstage/css/style.css" />
        <?php
			if (__('Direction of language', 'luna') == 'rtl') {
				echo '<link rel="stylesheet" type="text/css" href="vendor/css/bidirect.css" />';
			}
		?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function process_form(the_form) {
			var required_fields = {
				"req_db_type": "<?php _e('Type', 'luna')?>",
				"req_db_host": "<?php _e('Server hostname', 'luna')?>",
				"req_db_name": "<?php _e('Name', 'luna')?>",
				"req_username": "<?php _e('Username', 'luna')?>",
				"req_password1": "<?php _e('Administrator password 1', 'luna')?>",
				"req_password2": "<?php _e('Administrator password 2', 'luna')?>",
				"req_email": "<?php _e('Email', 'luna')?>",
				"req_title": "<?php _e('Board title', 'luna')?>",
				"req_base_url": "<?php _e('No trailing slash', 'luna')?>",
			};
			if (document.all || document.getElementById) {
				for (var i = 0; i < the_form.length; ++i) {
					var elem = the_form.elements[i];
					if (elem.name && required_fields[elem.name] && !elem.value && elem.type && (/^(?:text(?:area)?|password|file)$/i.test(elem.type))) {
						alert('"' + required_fields[elem.name] + '" <?php _e('is a required field in this form.', 'luna')?>');
						elem.focus();
						return false;
					}
				}
			}
			return true;
		}
		/* ]]> */
		</script>
		<style>
		.container {
			margin: 0 auto 30px;
		}
		</style>
	</head>
	<body class="installer accent-2" onload="document.getElementById('install').start.disabled=false;" onunload="">
        <div class="jumbotron jumbotron-brand text-center">
			<h1><?php echo _e('Install<span class="brand">Luna</span>', 'luna') ?></h1>
            <h2>You can do anything</h2>
		</div>
		<div class="content">
			<div class="container main">
				<div class="row">
					<div class="col">
						<?php if (count($languages) > 1): ?>
						<form class="form-horizontal" id="install" method="post" action="install.php">
							<div class="card">
								<h5 class="card-header">
									<?php _e('Install language', 'luna')?>
									<span class="float-right">
										<button type="submit" class="btn btn-link" name="start"><i class="fas fa-fw fa-language"></i> <?php _e('Change language', 'luna')?></button>
									</span>
								</h5>
								<div class="card-body">
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Language', 'luna')?><span class="help-block"><?php _e('The language used for the installer, the default language for the board can be set below', 'luna')?></span></label>
										<div class="col-md-9">
											<select class="form-control" name="install_lang">
<?php
    foreach ($languages as $temp) {
        if ($temp == $install_lang) {
            echo '<option value="'.$temp.'" selected>'.$temp.'</option>';
        } else {
            echo '<option value="'.$temp.'">'.$temp.'</option>';
        }
    }
?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</form>
<?php endif;?>
<?php if (!empty($alerts)): ?>
						<div class="card">
							<h5 class="card-header"><?php _e('Errors', 'luna')?></h5>
							<div class="card-body">
								<div class="forminfo error-info">
<?php
    foreach ($alerts as $cur_alert) {
        echo $cur_alert.'<br />';
    }
?>
								</div>
							</div>
						</div>
<?php endif;?>
						<form  class="form-horizontal" id="install" method="post" action="install.php" onsubmit="this.start.disabled=true;if(process_form(this)){return true;}else{this.start.disabled=false;return false;}">
							<div><input type="hidden" name="form_sent" value="1" /><input type="hidden" name="install_lang" value="<?php echo luna_htmlspecialchars($install_lang) ?>" /></div>
							<div class="card">
								<h5 class="card-header"><?php _e('Database', 'luna')?></h5>
								<div class="card-body">
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Type', 'luna')?><span class="help-block"><?php _e('We\'ve listed everything this server knows', 'luna')?></span></label>
										<div class="col-md-9">
											<select class="form-control" name="req_db_type">
<?php

    foreach ($db_extensions as $temp) {
        if ($temp[0] == $db_type) {
            echo '<option value="'.$temp[0].'" selected>'.$temp[1].'</option>';
        } else {
            echo '<option value="'.$temp[0].'">'.$temp[1].'</option>';
        }

    }

    ?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Server hostname', 'luna')?></label>
										<div class="col-md-9">
											<input type="text" class="form-control" name="req_db_host" value="<?php echo luna_htmlspecialchars($db_host) ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Name', 'luna')?></label>
										<div class="col-md-9">
											<input id="req_db_name" type="text" class="form-control" name="req_db_name" value="<?php echo luna_htmlspecialchars($db_name) ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Username', 'luna')?></label>
										<div class="col-md-9">
											<input type="text" class="form-control" name="db_username" value="<?php echo luna_htmlspecialchars($db_username) ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Password', 'luna')?></label>
										<div class="col-md-9">
											<input type="password" class="form-control" name="db_password" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Table prefix', 'luna')?><span class="help-block"><?php _e('Set for more Luna installation in this database', 'luna')?></span></label>
										<div class="col-md-9">
											<input id="db_prefix" type="text" class="form-control" name="db_prefix" value="<?php echo luna_htmlspecialchars($db_prefix) ?>" maxlength="30" />
										</div>
									</div>
								</div>
							</div>
							<div class="card">
								<h5 class="card-header"><?php _e('Administration', 'luna')?></h5>
								<div class="card-body">
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Username', 'luna')?><span class="help-block"><?php _e('2 to 25 characters long', 'luna')?></span></label>
										<div class="col-md-9">
											<input type="text" class="form-control" name="req_username" value="<?php echo luna_htmlspecialchars($username) ?>" maxlength="25" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Password', 'luna')?><span class="help-block"><?php _e('At least 6 characters long', 'luna')?></span></label>
										<div class="col-md-9">
											<div class="row">
												<div class="col-md-6">
													<input id="req_password1" type="password" class="form-control" name="req_password1" />
												</div>
												<div class="col-md-6">
													<input type="password" class="form-control" name="req_password2" />
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Email', 'luna')?></label>
										<div class="col-md-9">
											<input id="req_email" type="text" class="form-control" name="req_email" value="<?php echo luna_htmlspecialchars($email) ?>" maxlength="80" />
										</div>
									</div>
								</div>
							</div>
							<div class="card">
								<h5 class="card-header"><?php _e('Board', 'luna')?></h5>
								<div class="card-body">
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Title', 'luna')?></label>
										<div class="col-md-9">
											<input id="req_title" type="text" class="form-control" name="req_title" value="<?php echo luna_htmlspecialchars($title) ?>" maxlength="255" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Slogan', 'luna')?></label>
										<div class="col-md-9">
											<input id="slogan" type="text" class="form-control" name="slogan" value="<?php echo luna_htmlspecialchars($slogan) ?>" maxlength="255" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Description', 'luna')?></label>
										<div class="col-md-9">
											<input id="description" type="text" class="form-control" name="description" value="<?php echo luna_htmlspecialchars($description) ?>" maxlength="255" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Board URL', 'luna')?><span class="help-block"><?php _e('No trailing slash', 'luna')?></span></label>
										<div class="col-md-9">
											<input id="req_base_url" type="text" class="form-control" name="req_base_url" value="<?php echo luna_htmlspecialchars($base_url) ?>" maxlength="100" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Default language', 'luna')?></label>
										<div class="col-md-9">
											<select id="req_default_lang" class="form-control" name="req_default_lang">
<?php
    $languages = forum_list_langs();
    foreach ($languages as $temp) {
        if ($temp == $default_lang) {
            echo '<option value="'.$temp.'" selected>'.$temp.'</option>';
        } else {
            echo '<option value="'.$temp.'">'.$temp.'</option>';
        }
    }
?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-3 col-form-label"><?php _e('Default theme', 'luna')?></label>
										<div class="col-md-9">
											<select id="req_default_style" class="form-control" name="req_default_style">
<?php
    $themes = forum_list_themes();
    foreach ($themes as $theme) {
        if ($theme == $default_style) {
            echo '<option value="'.$theme->name.'" selected>'.$theme->name.'</option>';
        } else {
            echo '<option value="'.$theme->name.'">'.$theme->name.'</option>';
        }

    }
?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-primary btn-lg btn-block" name="start"><i class="fas fa-fw fa-check"></i> <?php _e('Start install', 'luna')?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>
<?php

} else {
    // Enable/disable avatars depending on file_uploads setting in PHP configuration
    $avatars = in_array(strtolower(@ini_get('file_uploads')), array('on', 'true', '1'));

    // Create the tables
    $db = Installer::create_database($db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix, $title, $slogan, $description, $default_lang, $default_style, $email, $avatars, $base_url);

    // Insert some other default data
    Installer::insert_default_groups(); // groups
    Installer::insert_default_users($username, $password1, $email, $default_lang); // users
    Installer::insert_default_menu(); // menus
    Installer::insert_default_data(); // other stuff, like ranks

    $alerts = array();

    // Check if we disabled uploading avatars because file_uploads was disabled
    if (!$avatars) {
        $alerts[] = __('<strong>File uploads appear to be disallowed on this server!</strong> If you want users to be able to upload their own avatar images you must enable the file_uploads configuration setting in PHP. Once file uploads have been enabled, avatar uploads can be enabled in Backstage > Settings > Features.', 'luna');
    }

    // Generate the config.php file data
    $config = Installer::generate_config_file($db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix);

    // Attempt to write config.php and serve it up for download if writing fails
    $written = false;
    if (forum_is_writable(LUNA_ROOT)) {
        $fh = @fopen(LUNA_ROOT.'config.php', 'wb');
        if ($fh) {
            fwrite($fh, $config);
            fclose($fh);

            $written = true;
        }
    }

    ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php _e('Luna Installation', 'luna')?></title>
		<link rel="stylesheet" type="text/css" href="vendor/css/bootstrap.min.css" />
		<link rel="stylesheet" href="vendor/css/fontawesome-all.min.css">
		<link rel="stylesheet" type="text/css" href="backstage/css/style.css" />
	</head>
	<body class="installer" onload="document.getElementById('install').start.disabled=false;" onunload="">
        <div class="jumbotron jumbotron-brand text-center">
			<h1><?php echo _e('Install<span class="brand">Luna</span>', 'luna') ?></h1>
            <h2>You can do anything</h2>
        </div>
		<div class="content">
			<div class="container main">
				<div class="row">
					<div class="col">
						<form method="post" class="card" action="install.php">
							<h5 class="card-header">
								<?php _e('Luna Installation', 'luna')?>
								<span class="float-right">
									<button type="submit" class="btn btn-primary"><i class="fas fa-fw file-down"></i> <?php _e('Download config.php file', 'luna')?></button>
								</span>
							</h5>
							<div class="card-body">
								<p><?php _e('Luna has been installed. To finalize the installation please follow the instructions below.', 'luna')?></p>
<?php if (!$written) { ?>
								<p><?php _e('To finalize the installation, you need to click on the button below to download a file called config.php. You then need to upload this file to the root directory of your Luna installation.', 'luna')?></p>
								<p><?php _e('Once you have uploaded config.php, Luna will be fully installed! At that point, you may <a href="index.php">go to the forum index</a>.', 'luna')?></p>
								<input type="hidden" name="generate_config" value="1" />
								<input type="hidden" name="db_type" value="<?php echo $db_type; ?>" />
								<input type="hidden" name="db_host" value="<?php echo $db_host; ?>" />
								<input type="hidden" name="db_name" value="<?php echo luna_htmlspecialchars($db_name); ?>" />
								<input type="hidden" name="db_username" value="<?php echo luna_htmlspecialchars($db_username); ?>" />
								<input type="hidden" name="db_password" value="<?php echo luna_htmlspecialchars($db_password); ?>" />
								<input type="hidden" name="db_prefix" value="<?php echo luna_htmlspecialchars($db_prefix); ?>" />
<?php } else { ?>
								<p><?php _e('Luna has been fully installed! You may now <a href="index.php">go to the forum index</a>.', 'luna')?></p>
<?php } ?>
							</div>
<?php if (!empty($alerts)): ?>
							<div class="alert alert-danger">
								<ul>
<?php
        foreach ($alerts as $cur_alert) {
            echo '<li>'.$cur_alert.'</li>';
        }
?>
								</ul>
							</div>
<?php endif;?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php

}
