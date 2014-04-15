<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the help template
define('FORUM_HELP', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($luna_user['g_read_board'] == '0')
	message($lang['No view'], false, '403 Forbidden');

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Help']);
define('FORUM_ACTIVE_PAGE', 'help');
require FORUM_ROOT.'header.php';

?>
<h2><?php echo $lang['Help'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['BBCode'] ?></h3>
    </div>
    <div class="panel-body">
        <p><a name="bbcode"></a><?php echo $lang['BBCode info'] ?></p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#text" data-toggle="tab"><?php echo $lang['Text style'] ?></a></li>
			<li><a href="#links" data-toggle="tab"><?php echo $lang['Multimedia'] ?></a></li>
			<li><a href="#quotes" data-toggle="tab"><?php echo $lang['Quotes'] ?></a></li>
			<li><a href="#code" data-toggle="tab"><?php echo $lang['Code'] ?></a></li>
			<li><a href="#lists" data-toggle="tab"><?php echo $lang['Lists'] ?></a></li>
			<li><a href="#smilies" data-toggle="tab"><?php echo $lang['Smilies'] ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="text">
                <p><?php echo $lang['Text style info'] ?></p>
                <p><code>[b]<?php echo $lang['Bold text'] ?>[/b]</code> <?php echo $lang['produces'] ?> <strong><?php echo $lang['Bold text'] ?></strong></p>
                <p><code>[u]<?php echo $lang['Underlined text'] ?>[/u]</code> <?php echo $lang['produces'] ?> <span class="bbu"><?php echo $lang['Underlined text'] ?></span></p>
                <p><code>[i]<?php echo $lang['Italic text'] ?>[/i]</code> <?php echo $lang['produces'] ?> <em><?php echo $lang['Italic text'] ?></em></p>
                <p><code>[s]<?php echo $lang['Strike-through text'] ?>[/s]</code> <?php echo $lang['produces'] ?> <span class="bbs"><?php echo $lang['Strike-through text'] ?></span></p>
                <p><code>[ins]<?php echo $lang['Inserted text'] ?>[/ins]</code> <?php echo $lang['produces'] ?> <ins><?php echo $lang['Inserted text'] ?></ins></p>
                <p><code>[color=#FF0000]<?php echo $lang['Red text'] ?>[/color]</code> <?php echo $lang['produces'] ?> <span style="color: #ff0000"><?php echo $lang['Red text'] ?></span></p>
                <p><code>[color=blue]<?php echo $lang['Blue text'] ?>[/color]</code> <?php echo $lang['produces'] ?> <span style="color: blue"><?php echo $lang['Blue text'] ?></span></p>
                <p><code>[sub]<?php echo $lang['Sub text'] ?>[/sub]</code> <?php echo $lang['produces'] ?> <sub><?php echo $lang['Sub text'] ?></sub></p>
                <p><code>[sup]<?php echo $lang['Sup text'] ?>[/sup]</code> <?php echo $lang['produces'] ?> <sup><?php echo $lang['Sup text'] ?></sup></p>
                <p><code>[h]<?php echo $lang['Heading text'] ?>[/h]</code> <?php echo $lang['produces'] ?></p> <h4><?php echo $lang['Heading text'] ?></h4>
                <p><code>[left]<?php echo $lang['Left text'] ?>[/left]</code> <?php echo $lang['produces'] ?></p> <p style="text-align: left"><?php echo $lang['Left text'] ?></p>
                <p><code>[center]<?php echo $lang['Center text'] ?>[/center]</code> <?php echo $lang['produces'] ?></p> <p style="text-align: center"><?php echo $lang['Center text'] ?></p>
                <p><code>[right]<?php echo $lang['Right text'] ?>[/right]</code> <?php echo $lang['produces'] ?></p> <p style="text-align: right"><?php echo $lang['Right text'] ?></p>
                <p><code>[justify]<?php echo $lang['Justify text'] ?>[/justify]</code> <?php echo $lang['produces'] ?></p> <p style="text-align: justify"><?php echo $lang['Justify text'] ?></p>
            </div>
			<div class="tab-pane" id="links">
                <p><?php echo $lang['Links info'] ?></p>
                <p><code>[url=<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>]<?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?>[/url]</code> <?php echo $lang['produces'] ?> <a href="<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?></a></p>
                <p><code>[url]<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>[/url]</code> <?php echo $lang['produces'] ?> <a href="<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo luna_htmlspecialchars(get_base_url(true).'/') ?></a></p>
                <p><code>[email]myname@example.com[/email]</code> <?php echo $lang['produces'] ?> <a href="mailto:myname@example.com">myname@example.com</a></p>
                <p><code>[email=myname@example.com]<?php echo $lang['My email address'] ?>[/email]</code> <?php echo $lang['produces'] ?> <a href="mailto:myname@example.com"><?php echo $lang['My email address'] ?></a></p>
                <p><a name="img"></a><?php echo $lang['Images info'] ?></p>
                <p><code>[img=<?php echo $lang['ModernBB bbcode test'] ?>]<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/test.png[/img]</code> <?php echo $lang['produces'] ?> <img style="height: 21px" src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/test.png" alt="<?php echo $lang['ModernBB bbcode test'] ?>" /></p><br />
                <p><a name="img"></a><?php echo $lang['Video info'] ?></p>
                <p><code>[video=(x,y)][url]<?php echo $lang['Video link'] ?>[/url][/video]</code>
            </div>
			<div class="tab-pane" id="quotes">
                <p><?php echo $lang['Quotes info'] ?></p>
                <p><code>[quote=James]<?php echo $lang['Quote text'] ?>[/quote]</code></p>
                <p><?php echo $lang['produces quote box'] ?></p>
                <div class="postmsg">
                    <div class="quotebox"><cite>James <?php echo $lang['wrote'] ?></cite><blockquote><div><p><?php echo $lang['Quote text'] ?></p></div></blockquote></div>
                </div>
                <p><?php echo $lang['Quotes info 2'] ?></p>
                <p><code>[q]<?php echo $lang['Inline quote'] ?>[/q]</code> <?php echo $lang['produces'] ?> <q><?php echo $lang['Inline quote'] ?></q></p>
            </div>
			<div class="tab-pane" id="code">
                <p><?php echo $lang['Code info'] ?></p>
                <p><code>[code]<?php echo $lang['Code text'] ?>[/code]</code></p>
                <p><?php echo $lang['produces code box'] ?></p>
                <pre><?php echo $lang['Code text'] ?></pre>
                <p><code>[c]<?php echo $lang['Code text'] ?>[/c]</code> <?php echo $lang['produces code box'] ?> <code><?php echo $lang['Code text'] ?></code></p>
            </div>
			<div class="tab-pane" id="lists">
                <p><a name="lists"></a><?php echo $lang['List info'] ?></p>
                <p><code>[list][*]<?php echo $lang['List text 1'] ?>[/*][*]<?php echo $lang['List text 2'] ?>[/*][*]<?php echo $lang['List text 3'] ?>[/*][/list]</code>
                <br /><span><?php echo $lang['produces list'] ?></span></p>
                <div class="postmsg">
                    <ul><li><p><?php echo $lang['List text 1'] ?></p></li><li><p><?php echo $lang['List text 2'] ?></p></li><li><p><?php echo $lang['List text 3'] ?></p></li></ul>
                </div>
                <p><code>[list=1][*]<?php echo $lang['List text 1'] ?>[/*][*]<?php echo $lang['List text 2'] ?>[/*][*]<?php echo $lang['List text 3'] ?>[/*][/list]</code>
                <br /><span><?php echo $lang['produces decimal list'] ?></span></p>
                <div class="postmsg">
                    <ol class="decimal"><li><p><?php echo $lang['List text 1'] ?></p></li><li><p><?php echo $lang['List text 2'] ?></p></li><li><p><?php echo $lang['List text 3'] ?></p></li></ol>
                </div>
                <p><code>[list=a][*]<?php echo $lang['List text 1'] ?>[/*][*]<?php echo $lang['List text 2'] ?>[/*][*]<?php echo $lang['List text 3'] ?>[/*][/list]</code>
                <br /><span><?php echo $lang['produces alpha list'] ?></span></p>
                <div class="postmsg">
                    <ol class="alpha"><li><p><?php echo $lang['List text 1'] ?></p></li><li><p><?php echo $lang['List text 2'] ?></p></li><li><p><?php echo $lang['List text 3'] ?></p></li></ol>
                </div>
            </div>
			<div class="tab-pane" id="smilies">
                <p><a name="smilies"></a><?php echo $lang['Smilies info'] ?></p>
<?php

// Display the smiley set
require FORUM_ROOT.'include/parser.php';

$smiley_groups = array();

foreach ($smilies as $smiley_text => $smiley_img)
	$smiley_groups[$smiley_img][] = $smiley_text;

foreach ($smiley_groups as $smiley_img => $smiley_texts)
	echo "\t\t".'<p><code>'.implode('</code> '.$lang['and'].' <code>', $smiley_texts).'</code> <span>'.$lang['produces'].'</span> <img src="'.luna_htmlspecialchars(get_base_url(true)).'/img/smilies/'.$smiley_img.'" width="15" height="15" alt="'.$smiley_texts[0].'" /></p>'."\n";

?>
            </div>
		</div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">General use</h3>
    </div>
    <div class="panel-body">
        <p>Allow us to explain some of the basics on how to work with this forum software.</p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#forums" data-toggle="tab">Forums and topics</a></li>
			<li><a href="#profile" data-toggle="tab">Profile</a></li>
			<li><a href="#search" data-toggle="tab">Search</a></li>
		</ul>
		<div class="tab-content">
		  <div class="tab-pane active" id="forums">
                <h3>What do those labels in front of topic titles mean?</h3>
                <p>You'll see that some of the topics are labeled, different labels have different meanings.</p>
				<table class="table">
                	<thead>
                        <tr>
                            <th>Label</th>
                            <th>Explenation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="label label-success">sticky</span></td>
                            <td>Sticky topics are important, you are probably suposed to read those topics. It's worth it to take a look here.</td>
                        </tr>
                        <tr>
                            <td><span class="label label-danger">closed</span></td>
                            <td>When a topic is closed, you can't add a new comment to it, except if you have a permission that overwrites this. The topic is still available to read, through.</td>
                        </tr>
                        <tr>
                            <td><span class="label label-info">moved</span></td>
                            <td>This topic is moved to another forum. Admins and moderators can choose to show this notification, or simply not show it at all. The original forum where this topic was located in, won't show any topic stats anymore.</td>
                        </tr>
                        <!--
                        <tr>
                            <td><span class="label label-warning">stared</span></td>
                            <td>You're following this topic, they will show up in you're subscibtion list.</td>
                        </tr>
                        <tr>
                            <td><span class="label label-primary">posted</span></td>
                            <td>Topics labeled with this label contain a comment of you.</td>
                        </tr>
                        -->
                    </tbody>
                </table>
                <h3>I can't see the WYSIWYG editor!</h3>
                <p>You probably did not enable JavaScript in your browser, you can find this under settings in your browser. It could be that you are using an unsupported browser that doesn't hancle the editor properly, too.</p>
                <h3>Smilies, signatures, avatars and images are not visible?</h3>
                <p>You can change the behavior of the topic view in your profiles settings. Here you can enable smilies, signatures, avatars and images in posts. Through by default, those settings are enabled. Your forums admin might have disabled those features. You can see if images and smilies are disabled below the editor. If the labels have a red background, those features aren't available for you.</p>
                <h3>Why can't I see any topics or forums?</h3>
                <p>You might not have the correct permissions to do so, ask the forums administrator for more help.</p>
            </div>
			<div class="tab-pane" id="profile">
                <h3>Why can't I see any profiles?</h3>
                <p>You might not have the correct permissions to do so, ask the forums administrator for more help.</p>
                <h3>My profile doesn't contain as much as others?</h3>
                <p>You're profile will only display fields that are enabled and filled in on your profiles personality page. You might want to take a look over there and check if you missed some fields.</p>
            </div>
			<div class="tab-pane" id="search">
                <h3>Are there more options to search?</h3>
                <p>When you go to the search page, you'll find yourself on a page with 1 searchbox. Below that search box, there is a link to Advanced search, here you can find more search options! Note that this feature isn't available on small devices or is disabled on all devices by the forums admin.</p>
                <h3>I can't search in more then 1 forum at once?</h3>
                <p>You might not have the correct permissions to do so, ask the forums administrator for more help.</p>
            </div>
		</div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Moderating</h3>
    </div>
    <div class="panel-body">
        <p>Admins and moderators like help too, sometimes. So, allow us to explain those basics here.</p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#forums" data-toggle="tab">Forums</a></li>
			<li><a href="#topics" data-toggle="tab">Topics</a></li>
			<li><a href="#users" data-toggle="tab">Users</a></li>
		</ul>
		<div class="tab-content">
		  <div class="tab-pane active" id="forums">
                <h3>How do I moderate a forum?</h3>
                <p>The moderation options are available at the bottom of the page. Those features aren't available for all moderators. When you click this button, you will be send to a page where you can manage the current forum. From there, you can move, delete, merge, close and open multiple topics at once.</p>
            </div>
			<div class="tab-pane" id="topics">
                <h3>How do I moderate a topic?</h3>
                <p>The moderation options are available at the bottom of the page. Those features aren't available for all moderators. When you click this button, you will be send to a page where you can manage the current topic. From there, you can select multiple post to delete or split from the current topic at once.</p>
                <p>Next to the "Moderate topic" button, you can find options to move, open or close the topic. You can also make it a sticky topic from there, or unstick it.</p>
            </div>
			<div class="tab-pane" id="users">
                <h3>How do I moderate an user?</h3>
                <p>Moderator options are available in the users profile. You can find the moderation options under "Administration" in the users profile menu. Those features aren't available for all moderators.</p>
                <p>The Administration page allow you to check if the user has any admin note, and if required, you can change that note. You can also change the post count of this user. At this page, the user can also be given moderator permissions on a per-forum base, through the user must have a moderator account to be able to actualy use those permissions.</p>
                <p>Finaly, you can ban or delete a user from his profile. If you want to ban and/or delete multiple users at once, you're probably better of with the advanced user management features in the Backstage.</p>
            </div>
		</div>
    </div>
</div>
<?php

require FORUM_ROOT.'footer.php';
