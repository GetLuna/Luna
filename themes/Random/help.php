<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron" style="background:#999;">
	<div class="container">
		<h2><?php echo $lang['Help'] ?></h2>
	</div>
</div>
<div class="container">
<?php if ($luna_config['o_rules'] == '1') { ?>
<div class="panel panel-default">
	<div id="rules-block" class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Forum rules'] ?></h3>
	</div>
	<div class="panel-body">
		<?php echo $luna_config['o_rules_message'] ?>
	</div>
</div>
<?php } ?>
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
			</div>
			<div class="tab-pane" id="links">
				<p><?php echo $lang['Links info'] ?></p>
				<p><code>[url=<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>]<?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?>[/url]</code> <?php echo $lang['produces'] ?> <a href="<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?></a></p>
				<p><code>[url]<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>[/url]</code> <?php echo $lang['produces'] ?> <a href="<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo luna_htmlspecialchars(get_base_url(true).'/') ?></a></p>
				<p><code>[email]myname@example.com[/email]</code> <?php echo $lang['produces'] ?> <a href="mailto:myname@example.com">myname@example.com</a></p>
				<p><code>[email=myname@example.com]<?php echo $lang['My email address'] ?>[/email]</code> <?php echo $lang['produces'] ?> <a href="mailto:myname@example.com"><?php echo $lang['My email address'] ?></a></p>
				<p><a name="img"></a><?php echo $lang['Images info'] ?></p>
				<p><code>[img=<?php echo $lang['Luna bbcode test'] ?>]<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/test.png[/img]</code> <?php echo $lang['produces'] ?> <img style="height: 21px" src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/test.png" alt="<?php echo $lang['Luna bbcode test'] ?>" /></p>
				<br />
				<p><?php echo $lang['Video info'] ?></p>
				<p><code>[video][url]<?php echo $lang['Video link'] ?>[/url][/video]</code>
			</div>
			<div class="tab-pane" id="quotes">
				<p><?php echo $lang['Quotes info'] ?></p>
				<p><code>[quote=James]<?php echo $lang['Quote text'] ?>[/quote]</code></p>
				<p><?php echo $lang['produces quote box'] ?></p>
				<blockquote><footer><cite>James <?php echo $lang['wrote'] ?></cite></footer><p><?php echo $lang['Quote text'] ?></p></blockquote>
				<p><?php echo $lang['Quotes info 2'] ?></p>
			</div>
			<div class="tab-pane" id="code">
				<p><?php echo $lang['Code info'] ?></p>
				<p><code>[code]<?php echo $lang['Code text'] ?>[/code]</code></p>
				<p><?php echo $lang['produces code box'] ?></p>
				<pre><code><?php echo $lang['Code text'] ?></code></pre>
				<p><?php echo $lang['Syntax info'] ?></p>
				<p><pre>
[code]
[[php]]	
if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '<li><a href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';
[/code]
				</pre></p>
				<p><?php echo $lang['produces code box'] ?></p>
<div class="codebox"><pre class=" language-php"><code class=" language-php">if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '<li><a href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';
</code></pre></div>
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
			</div>
			<div class="tab-pane" id="smilies">
				<p><a name="smilies"></a><?php echo $lang['Smilies info'] ?></p>
				<div class="row">
<?php

// Display the smiley set
require FORUM_ROOT.'include/parser.php';

$smiley_groups = array();

foreach ($smilies as $smiley_text => $smiley_img)
	$smiley_groups[$smiley_img][] = $smiley_text;

foreach ($smiley_groups as $smiley_img => $smiley_texts) {
	if ($luna_config['o_emoji'] == 1)
		echo "\t\t".'<div class="col-sm-3"><p><code>'.implode('</code> '.$lang['and'].' <code>', $smiley_texts).'</code> <span>'.$lang['produces'].'</span> <span class="emoji">'.$smiley_img.'</span></p></div>'."\n";
	else
		echo "\t\t".'<div class="col-sm-3"><p><code>'.implode('</code> '.$lang['and'].' <code>', $smiley_texts).'</code> <span>'.$lang['produces'].'</span> <img src="'.luna_htmlspecialchars(get_base_url(true)).'/img/smilies/'.$smiley_img.'" width="'.$luna_config['o_emoji_size'].'" height="'.$luna_config['o_emoji_size'].'" alt="'.$smiley_texts[0].'" /></p></div>'."\n";
}

?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['General use'] ?></h3>
	</div>
	<div class="panel-body">
		<p><?php echo $lang['General use info'] ?></p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#forum" data-toggle="tab"><?php echo $lang['Forums and topics'] ?></a></li>
			<li><a href="#profile" data-toggle="tab"><?php echo $lang['Profile'] ?></a></li>
			<li><a href="#searching" data-toggle="tab"><?php echo $lang['Search'] ?></a></li>
		</ul>
		<div class="tab-content">
		  <div class="tab-pane active" id="forum">
                <h3><?php echo $lang['Labels question'] ?></h3>
                <p><?php echo $lang['Labels info'] ?></p>
				<table class="table">
                	<thead>
                        <tr>
                            <th><?php echo $lang['Label'] ?></th>
                            <th><?php echo $lang['Explanation'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="label label-success"><?php echo $lang['Sticky'] ?></span></td>
                            <td><?php echo $lang['Sticky explanation'] ?></td>
                        </tr>
                        <tr>
                            <td><span class="label label-danger"><?php echo $lang['Closed'] ?></span></td>
                            <td><?php echo $lang['Closed explanation'] ?></td>
                        </tr>
                        <tr>
                            <td><span class="label label-info"><?php echo $lang['Moved'] ?></span></td>
                            <td><?php echo $lang['Moved explanation'] ?></td>
                        </tr>
                        <?php if (!$luna_user['is_guest'] && $luna_config['o_has_posted'] == '1') { ?>
                        <tr>
                            <td>&middot;</td>
                            <td><?php echo $lang['Posted explanation'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
				<h3><?php echo $lang['Content question'] ?></h3>
				<p><?php echo $lang['Content answer'] ?></p>
				<h3><?php echo $lang['Topics question'] ?></h3>
				<p><?php echo $lang['Topics answer'] ?></p>
			</div>
			<div class="tab-pane" id="profile">
				<h3><?php echo $lang['Profile question'] ?></h3>
				<p><?php echo $lang['Profile answer'] ?></p>
				<h3><?php echo $lang['Information question'] ?></h3>
				<p><?php echo $lang['Information answer'] ?></p>
			</div>
			<div class="tab-pane" id="searching">
				<h3><?php echo $lang['Advanced search question'] ?></h3>
				<p><?php echo $lang['Advanced search answer'] ?></p>
				<h3><?php echo $lang['More search question'] ?></h3>
				<p><?php echo $lang['More search answer'] ?></p>
			</div>
		</div>
	</div>
</div>
<?php
if ($luna_user['is_admmod']) {
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Moderating'] ?></h3>
	</div>
	<div class="panel-body">
		<p><?php echo $lang['Moderating info'] ?></p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#forums" data-toggle="tab"><?php echo $lang['Forums'] ?></a></li>
			<li><a href="#topics" data-toggle="tab"><?php echo $lang['Topics'] ?></a></li>
			<li><a href="#users" data-toggle="tab"><?php echo $lang['Users'] ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="forums">
				<h3><?php echo $lang['Moderate forum question'] ?></h3>
				<p><?php echo $lang['Moderate forum answer'] ?></p>
			</div>
			<div class="tab-pane" id="topics">
				<h3><?php echo $lang['Moderate topic question'] ?></h3>
				<p><?php echo $lang['Moderate topic answer 1'] ?></p>
				<p><?php echo $lang['Moderate topic answer 2'] ?></p>
			</div>
			<div class="tab-pane" id="users">
				<h3><?php echo $lang['Moderate user question'] ?></h3>
				<p><?php echo $lang['Moderate user answer 1'] ?></p>
				<p><?php echo $lang['Moderate user answer 2'] ?></p>
				<p><?php echo $lang['Moderate user answer 3'] ?></p>
			</div>
		</div>
	</div>
</div>
<?php } ?>