<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>
    <div class="forum-box">
        <div class="row forum-header">
			<div class="col-md-6 col-sm-6 col-xs-7"><?php echo $lang['Topic'] ?></div>
            <div class="col-md-2 hidden-sm hidden-xs"><?php echo $lang['Forum'] ?></div>
			<div class="col-md-1 col-sm-2 hidden-xs"><p class="text-center"><?php echo $lang['Replies forum'] ?></p></div>
			<div class="col-md-3 col-sm-4 col-xs-5"><?php echo $lang['Last post'] ?></div>
        </div>