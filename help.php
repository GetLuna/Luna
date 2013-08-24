<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the help template
define('FORUM_HELP', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');


// Load the frontend.php language file
require FORUM_ROOT.'lang/'.$pun_user['language'].'/frontend.php';


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_front['Help']);
define('FORUM_ACTIVE_PAGE', 'help');
require FORUM_ROOT.'header.php';

?>
<h2><?php echo $lang_front['BBCode'] ?></h2>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['BBCode'] ?></h3>
    </div>
    <div class="panel-body">
        <p><a name="bbcode"></a><?php echo $lang_front['BBCode info 1'] ?></p>
        <p><?php echo $lang_front['BBCode info 2'] ?></p>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Text style'] ?></h3>
    </div>
    <div class="panel-body">
		<p><?php echo $lang_front['Text style info'] ?></p>
		<p><code>[b]<?php echo $lang_front['Bold text'] ?>[/b]</code> <?php echo $lang_front['produces'] ?> <strong><?php echo $lang_front['Bold text'] ?></strong></p>
		<p><code>[u]<?php echo $lang_front['Underlined text'] ?>[/u]</code> <?php echo $lang_front['produces'] ?> <span class="bbu"><?php echo $lang_front['Underlined text'] ?></span></p>
		<p><code>[i]<?php echo $lang_front['Italic text'] ?>[/i]</code> <?php echo $lang_front['produces'] ?> <em><?php echo $lang_front['Italic text'] ?></em></p>
		<p><code>[s]<?php echo $lang_front['Strike-through text'] ?>[/s]</code> <?php echo $lang_front['produces'] ?> <span class="bbs"><?php echo $lang_front['Strike-through text'] ?></span></p>
		<p><code>[del]<?php echo $lang_front['Deleted text'] ?>[/del]</code> <?php echo $lang_front['produces'] ?> <del><?php echo $lang_front['Deleted text'] ?></del></p>
		<p><code>[ins]<?php echo $lang_front['Inserted text'] ?>[/ins]</code> <?php echo $lang_front['produces'] ?> <ins><?php echo $lang_front['Inserted text'] ?></ins></p>
		<p><code>[em]<?php echo $lang_front['Emphasised text'] ?>[/em]</code> <?php echo $lang_front['produces'] ?> <em><?php echo $lang_front['Emphasised text'] ?></em></p>
		<p><code>[color=#FF0000]<?php echo $lang_front['Red text'] ?>[/color]</code> <?php echo $lang_front['produces'] ?> <span style="color: #ff0000"><?php echo $lang_front['Red text'] ?></span></p>
		<p><code>[color=blue]<?php echo $lang_front['Blue text'] ?>[/color]</code> <?php echo $lang_front['produces'] ?> <span style="color: blue"><?php echo $lang_front['Blue text'] ?></span></p>
		<p><code>[sub]<?php echo $lang_front['Sub text'] ?>[/sub]</code> <?php echo $lang_front['produces'] ?> <sub><?php echo $lang_front['Sub text'] ?></sub></p>
		<p><code>[sup]<?php echo $lang_front['Sup text'] ?>[/sup]</code> <?php echo $lang_front['produces'] ?> <sup><?php echo $lang_front['Sup text'] ?></sup></p>
		<p><code>[h]<?php echo $lang_front['Heading text'] ?>[/h]</code> <?php echo $lang_front['produces'] ?></p> <div class="postmsg"><h5><?php echo $lang_front['Heading text'] ?></h5></div>
	</div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Links, images and video'] ?></h3>
    </div>
    <div class="panel-body">
		<p><?php echo $lang_front['Links info'] ?></p>
		<p><code>[url=<?php echo pun_htmlspecialchars(get_base_url(true).'/') ?>]<?php echo pun_htmlspecialchars($pun_config['o_board_title']) ?>[/url]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo pun_htmlspecialchars($pun_config['o_board_title']) ?></a></p>
		<p><code>[url]<?php echo pun_htmlspecialchars(get_base_url(true).'/') ?>[/url]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo pun_htmlspecialchars(get_base_url(true).'/') ?></a></p>
		<p><code>[url=/help.php]<?php echo $lang_front['This help page'] ?>[/url]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/help.php') ?>"><?php echo $lang_front['This help page'] ?></a></p>
		<p><code>[email]myname@example.com[/email]</code> <?php echo $lang_front['produces'] ?> <a href="mailto:myname@example.com">myname@example.com</a></p>
		<p><code>[email=myname@example.com]<?php echo $lang_front['My email address'] ?>[/email]</code> <?php echo $lang_front['produces'] ?> <a href="mailto:myname@example.com"><?php echo $lang_front['My email address'] ?></a></p>
<p><code>[topic=1]<?php echo $lang_front['Test topic'] ?>[/topic]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/viewtopic.php?id=1') ?>"><?php echo $lang_front['Test topic'] ?></a></p>
		<p><code>[topic]1[/topic]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/viewtopic.php?id=1') ?>"><?php echo pun_htmlspecialchars(get_base_url(true).'/viewtopic.php?id=1') ?></a></p>
		<p><code>[post=1]<?php echo $lang_front['Test post'] ?>[/post]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/viewtopic.php?pid=1#p1') ?>"><?php echo $lang_front['Test post'] ?></a></p>
		<p><code>[post]1[/post]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/viewtopic.php?pid=1#p1') ?>"><?php echo pun_htmlspecialchars(get_base_url(true).'/viewtopic.php?pid=1#p1') ?></a></p>
		<p><code>[forum=1]<?php echo $lang_front['Test forum'] ?>[/forum]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/viewforum.php?id=1') ?>"><?php echo $lang_front['Test forum'] ?></a></p>
		<p><code>[forum]1[/forum]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/viewforum.php?id=1') ?>"><?php echo pun_htmlspecialchars(get_base_url(true).'/viewforum.php?id=1') ?></a></p>
		<p><code>[user=2]<?php echo $lang_front['Test user'] ?>[/user]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/profile.php?id=2') ?>"><?php echo $lang_front['Test user'] ?></a></p>
		<p><code>[user]2[/user]</code> <?php echo $lang_front['produces'] ?> <a href="<?php echo pun_htmlspecialchars(get_base_url(true).'/profile.php?id=2') ?>"><?php echo pun_htmlspecialchars(get_base_url(true).'/profile.php?id=2') ?></a></p><br />
		<p><a name="img"></a><?php echo $lang_front['Images info'] ?></p>
		<p><code>[img=<?php echo $lang_front['ModernBB bbcode test'] ?>]<?php echo pun_htmlspecialchars(get_base_url(true)) ?>/img/test.png[/img]</code> <?php echo $lang_front['produces'] ?> <img style="height: 21px" src="<?php echo pun_htmlspecialchars(get_base_url(true)) ?>/img/test.png" alt="<?php echo $lang_front['ModernBB bbcode test'] ?>" /></p><br />
		<p><a name="img"></a><?php echo $lang_front['Video info'] ?></p>
		<p><code>[video=(x,y)][url]<?php echo $lang_front['Video link'] ?>[/url][/video]</code>
	</div> 
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Quotes'] ?></h3>
    </div>
    <div class="panel-body">
		<p><?php echo $lang_front['Quotes info'] ?></p>
		<p><code>[quote=James]<?php echo $lang_front['Quote text'] ?>[/quote]</code></p>
		<p><?php echo $lang_front['produces quote box'] ?></p>
		<div class="postmsg">
			<div class="quotebox"><cite>James <?php echo $lang_common['wrote'] ?></cite><blockquote><div><p><?php echo $lang_front['Quote text'] ?></p></div></blockquote></div>
		</div>
		<p><?php echo $lang_front['Quotes info 2'] ?></p>
		<p><code>[quote]<?php echo $lang_front['Quote text'] ?>[/quote]</code></p>
		<p><?php echo $lang_front['produces quote box'] ?></p>
		<div class="postmsg">
			<div class="quotebox"><blockquote><div><p><?php echo $lang_front['Quote text'] ?></p></div></blockquote></div>
		</div>
		<p><?php echo $lang_front['quote note'] ?></p>
	</div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Code'] ?></h3>
    </div>
    <div class="panel-body">
		<p><?php echo $lang_front['Code info'] ?></p>
		<p><code>[code]<?php echo $lang_front['Code text'] ?>[/code]</code></p>
		<p><?php echo $lang_front['produces code box'] ?></p>
		<div class="postmsg">
			<div class="codebox"><pre><code><?php echo $lang_front['Code text'] ?></code></pre></div>
		</div>
	</div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Lists'] ?></h3>
    </div>
    <div class="panel-body">
		<p><a name="lists"></a><?php echo $lang_front['List info'] ?></p>
		<p><code>[list][*]<?php echo $lang_front['List text 1'] ?>[/*][*]<?php echo $lang_front['List text 2'] ?>[/*][*]<?php echo $lang_front['List text 3'] ?>[/*][/list]</code>
		<br /><span><?php echo $lang_front['produces list'] ?></span></p>
		<div class="postmsg">
			<ul><li><p><?php echo $lang_front['List text 1'] ?></p></li><li><p><?php echo $lang_front['List text 2'] ?></p></li><li><p><?php echo $lang_front['List text 3'] ?></p></li></ul>
		</div>
		<p><code>[list=1][*]<?php echo $lang_front['List text 1'] ?>[/*][*]<?php echo $lang_front['List text 2'] ?>[/*][*]<?php echo $lang_front['List text 3'] ?>[/*][/list]</code>
		<br /><span><?php echo $lang_front['produces decimal list'] ?></span></p>
		<div class="postmsg">
			<ol class="decimal"><li><p><?php echo $lang_front['List text 1'] ?></p></li><li><p><?php echo $lang_front['List text 2'] ?></p></li><li><p><?php echo $lang_front['List text 3'] ?></p></li></ol>
		</div>
		<p><code>[list=a][*]<?php echo $lang_front['List text 1'] ?>[/*][*]<?php echo $lang_front['List text 2'] ?>[/*][*]<?php echo $lang_front['List text 3'] ?>[/*][/list]</code>
		<br /><span><?php echo $lang_front['produces alpha list'] ?></span></p>
		<div class="postmsg">
			<ol class="alpha"><li><p><?php echo $lang_front['List text 1'] ?></p></li><li><p><?php echo $lang_front['List text 2'] ?></p></li><li><p><?php echo $lang_front['List text 3'] ?></p></li></ol>
		</div>
	</div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Nested tags'] ?></h3>
    </div>
    <div class="panel-body">
		<p><?php echo $lang_front['Nested tags info'] ?></p>
		<p><code>[b][u]<?php echo $lang_front['Bold, underlined text'] ?>[/u][/b]</code> <?php echo $lang_front['produces'] ?> <strong><span class="bbu"><?php echo $lang_front['Bold, underlined text'] ?></span></strong></p>
	</div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_front['Smilies'] ?></h3>
    </div>
    <div class="panel-body">
		<p><a name="smilies"></a><?php echo $lang_front['Smilies info'] ?></p>
<?php

// Display the smiley set
require FORUM_ROOT.'include/parser.php';

$smiley_groups = array();

foreach ($smilies as $smiley_text => $smiley_img)
	$smiley_groups[$smiley_img][] = $smiley_text;

foreach ($smiley_groups as $smiley_img => $smiley_texts)
	echo "\t\t".'<p><code>'.implode('</code> '.$lang_common['and'].' <code>', $smiley_texts).'</code> <span>'.$lang_front['produces'].'</span> <img src="'.pun_htmlspecialchars(get_base_url(true)).'/img/smilies/'.$smiley_img.'" width="15" height="15" alt="'.$smiley_texts[0].'" /></p>'."\n";

?>
	</div>
</div>
<?php

require FORUM_ROOT.'footer.php';
