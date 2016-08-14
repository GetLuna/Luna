<?php

/*
 * Copyright (C) 2013-2016 Luna
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
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php printf(__('About Luna %s %s', 'luna'), Version::LUNA_VERSION, Version::LUNA_CODE_NAME) ?></h3>
			</div>
			<div class="panel-body">
				<section class="release-notes">
					<div class="container">
						<h2 class="clearfix"><span class="version-name">Glitter Preview <small>3.0-alpha.0</small></span></h2>
						<ul class="changes">
                            <li><div class="stater"><em class="state state-note"><?php _e('Note', 'luna') ?></em></div><?php _e('Changelog not available', 'luna') ?></li>
						</ul>
					</div>
				</section>
			</div>
			<div class="panel-footer">
				<p><?php printf(__('Luna is developed by the %s. Copyright %s. Released under the GPLv2 license.', 'luna'), '<a href="http://getluna.org/">Luna Group</a>', '2013-2016') ?></p>
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
