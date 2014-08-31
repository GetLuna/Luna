<?php

/**
 * Copyright (C) 2013-2014 Luna
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require '../include/common.php';

if (!$luna_user['is_admmod'])
    header("Location: ../login.php");

require 'header.php';
load_admin_nav('settings', 'menu');

?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Menu</h3>
			</div>
			<div class="panel-body">
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
