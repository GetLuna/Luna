<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: login.php");
}

// Load the admin_index.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/admin_index.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['About']);
define('PUN_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'admin/header.php';
	generate_admin_menu('about');

//Update checking
$latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/version2.0/version.txt'));
if (preg_match("/^[0-9.-]{1,}$/", $latest_version)) {
	if (FORUM_VERSION < $latest_version) { ?>
		<div class="alert alert-info alert-update">
          <h4>ModernBB v<?php echo $latest_version ?> available</h4>
          We found a new version of ModernBB on the web. Your board is out-of-date and we recommend you to update right away!<br />
          <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>" class="btn btn-primary">Download v<?php echo $latest_version ?></a>
          <a href="http://modernbb.be/changelog.php#modernbb<?php echo $latest_version ?>" class="btn btn-primary">Changelog</a>
          <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>" class="btn">Download v<?php echo FORUM_VERSION ?></a>
        </div>
    <?php }
}
?>
<div class="alert alert-update alert-info">
    <h2>Welcome to ModernBB <?php echo FORUM_VERSION ?></h2>
    <a href="http://modernbb.be/changelog.php#modernbb<?php echo FORUM_VERSION ?>" class="btn btn-primary">Changelog</a>
	<a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>" class="btn btn-primary">Download v<?php echo FORUM_VERSION ?></a>
</div>
<div class="content">
    <h2>What's new in version 2.0-beta.1?</h2>
    <h3>Brand new Dashboard</h3>
    <img src="admin/img/dashboard.png" width="1065" height="250" alt="The new dashboard design" />
	<div class="row-fluid">
      <div class="span6"><p><b>Modern design.</b><br />The dashboard has a brand new Bootstrap-based design, making ModernBB easier to use.</p></div>
      <div class="span6"><p><b>Make it your own.</b><br />The new dashboard gives you the posebility to costumize it as much as you want with Bootstap themes.</p></div>
      <p><b>Modern standards. No dust. More features.</b><br />The revamped dashboard is HTML5 and CSS3 based, instead of XHTML1.1, and doesn't affect the front-end of your forums anymore. We improved lots of features, like creating new forums. But we also added new features, it's now possible to create back-ups out-of-the-box. We use more placeholders and we say "goodbye" to not-functional HTML.</p>
	</div>
	<h3>Checks for updates, always</h3>
    <img src="admin/img/update.png" width="981" height="89" alt="The new update" />
	<p>With the new update system, we moved to a more simple system. It now compares your version with the GitHub repository and warns you for new updates. The update message can't be disabled and is only visible on the index and about page of the dashboard.</p>
    <h3>More improvements</h3>
    <div class="row-fluid">
    	<div class="span4">
        	<p><b>Login with style.</b></p>
        	<img src="admin/img/login.png" width="366" height="318" alt="Login form" />
            <p>ModernBB features a brand new login form. With less clutter, it's straight to the point. A true login experience.</p>
        </div>
    	<div class="span4">
        	<p><b>Modernized styles.</b></p>
        	<img src="admin/img/styles.png" width="366" height="318" alt="Style preview" />
            <p>It was time to modernize the standard themes of ModernBB. We removed unneeded borders, use border-radius instead of images and improved the templates that handel the pages.</p>
        </div>
    </div>
    <h3>Other small improvements</h3>
	<div class="row-fluid">
      <div class="span4"><p><b>1-step forum creation.</b><br />In v1.6.x, you had to create a forum, after that, you could give it a name. With ModernBB 2, you're able to create a forum and give it a name in just 1 step.</p></div>
      <div class="span4"><p><b>Replace PUN_ variables.</b><br />We have replaced some of the PUN_ variables with FORUM_. With this step, we want to get improve the user experience and development. This should break plugins for the 1.x branch, but that doesn't matter, most won't work anyway.</p></div>
      <div class="span4"><p><b>Embed videos.</b><br />We introduce also a new parser. One of the improvements we made is the support for video embedding. This new features supports DaiyMotion, Vimeo and YouTube.</p></div>
	</div>
	<div class="row-fluid">
      <div class="span4"><p><b>Clean registration.</b><br />ModernBB 2 also improves the registration for new users. With a simplified registration form, your future users will be able to start using your forum faster without unneeded information.</p></div>
      <div class="span4"><p><b>Super subscript.</b><br />The new parser supports, beside video embedding, also the use of subscript and superscript text. This makes it easier for your boards users to write to mathematical formulas and more.</p></div>
	</div>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
