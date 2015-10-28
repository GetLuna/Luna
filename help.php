<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', dirname(__FILE__).'/');
require LUNA_ROOT.'include/common.php';

if ($luna_user['g_read_board'] == '0')
	message(__('You do not have permission to view this page.', 'luna'), false, '403 Forbidden');

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Help', 'luna'));
define('LUNA_ACTIVE_PAGE', 'help');
require load_page('header.php');

require load_page('help.php');

?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#user").focus();
		var hash = location.hash, hashPieces = hash.split('?'), activeTab = $('[href=' + hashPieces[0] + ']');
		activeTab && activeTab.tab('show');
	});
</script>
<?php

require load_page('footer.php');
