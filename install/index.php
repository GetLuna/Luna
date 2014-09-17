<?php

session_start();
require_once('assets/top.php');

if( version_compare(PHP_VERSION, '5.3', '<') ) {
    $check['php'] = false;
    $check['php_version'] = PHP_VERSION;
    $check['php_css'] = 'danger';
} else {
    $check['php'] = true;
    $check['php_version'] = PHP_VERSION;
    $check['php_css'] = 'success';
}
$config_chmods = substr(decoct(fileperms("../")), -3);
if($config_chmods < '777') {
    $check['config_chmods'] = false;
    $check['config_chmods_value'] = $config_chmods;
    $check['config_chmods_css'] = 'danger';
} else {
    $check['config_chmods'] = true;
    $check['config_chmods_value'] = $config_chmods;
    $check['config_chmods_css'] = 'success';
}
$cache_chmods = substr(decoct(fileperms("../cache/")), -3);
if($cache_chmods < '777') {
    $check['cache_chmods'] = false;
    $check['cache_chmods_value'] = $cache_chmods;
    $check['cache_chmods_css'] = 'danger';
} else {
    $check['cache_chmods'] = true;
    $check['cache_chmods_value'] = $cache_chmods;
    $check['cache_chmods_css'] = 'success';
}
$avatar_chmods = substr(decoct(fileperms("../img/avatars/")), -3);
if($avatar_chmods < '777') {
    $check['avatar_chmods'] = false;
    $check['avatar_chmods_value'] = $avatar_chmods;
    $check['avatar_chmods_css'] = 'danger';
} else {
    $check['avatar_chmods'] = true;
    $check['avatar_chmods_value'] = $avatar_chmods;
    $check['avatar_chmods_css'] = 'success';
}
?>
<div class="container">
	<div class="row wizard" style="border-bottom:0;">
		<div class="col-xs-3 wizard-step active">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Test the server</div>
		</div>
		<div class="col-xs-3 wizard-step disabled">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Create the database</div>
		</div> 
		<div class="col-xs-3 wizard-step disabled">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Personalize</div>
		</div>
		<div class="col-xs-3 wizard-step disabled">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Have fun</div>
		</div>
	</div>
</div>
<?php

if($check['php'] === true && $check['config_chmods'] && $check['cache_chmods'] && $check['avatar_chmods'] === true) {
     $_SESSION['luna_install_step1'] = true;
     ?>
     <div class="alert alert-success">
        Everything is how it should be. Awesome!
     </div>
     <a class="btn btn-default pull-right" href="step2.php">Next step</a>
    <?php
}
else {
?>
    <div class="alert alert-danger">
    	<h3>Can't install Luna</h3>
        You might want to fix this...
        <?php 
        if($check['php']===false) { echo '<br />Your current PHP version is lower than the recommended version.';}
        if($check['config_chmods']===false) { echo '<br />Please change the chmod of the root folder to 777.';}
        if($check['cache_chmods']===false) { echo '<br />Please change the chmod of the <code>cache/</code> folder to 777.';}
        if($check['avatar_chmods']===false) { echo '<br />Please change the chmod of the <code>img/avatar/</code> folder to 777.';}
        ?>
    </div>
<?php
}
?>

<table class="table">
    <thead>
        <tr>
            <th></th>
            <th class="col-xs-3">Recommended</th>
            <th class="col-xs-3">Status</th>
        </tr>
    </thead>
    <tr>
        <td>PHP version</td>
        <td>5.3.3 +</td>
        <td class="<?php echo $check['php_css']; ?>"><?php echo $check['php_version']; ?></td>
    </tr>
    <tr>
        <td>chmod <code>config.php</code></td>
        <td>777</span></td>
        <td class="<?php echo $check['config_chmods_css']; ?>"><?php echo $check['config_chmods_value']; ?></td>
    </tr>
    <tr>
        <td>chmod <code>/cache/</code></td>
        <td>777</span></td>
        <td class="<?php echo $check['cache_chmods_css']; ?>"><?php echo $check['cache_chmods_value']; ?></td>
    </tr>
    <tr>
        <td>chmod <code>/img/avatars/</code></td>
        <td>777</span></td>
        <td class="<?php echo $check['avatar_chmods_css']; ?>"><?php echo $check['avatar_chmods_value']; ?></td>
    </tr>
</table>

<?php


require_once('assets/bot.php');

?>