<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron">
	<div class="container">
		<h2 class="forum-title"><span class="fa fa-fw fa-info-circle"></span> <?php _e('Help', 'luna') ?></h2>
		<span class="pull-right naviton">
			<a class="btn btn-default" href="index.php"><?php _e('Index', 'luna') ?></a>
		</span>
	</div>
</div>
<div class="container">
<?php if ($luna_config['o_rules'] == '1') { ?>
<div class="panel panel-default">
	<div id="rules-block" class="panel-heading">
		<h3 class="panel-title"><?php _e('Forum rules', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<?php echo $luna_config['o_rules_message'] ?>
	</div>
</div>
<?php } ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('BBCode', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<p><a id="bbcode"></a><?php _e('BBCode is a collection of tags that are used to change the look of text in this forum. Below you can find all the available BBCodes and how to use them. Administrators have the ability to disable BBCode. You can tell if BBCode is disabled whenever you comment a message or edit your signature.', 'luna') ?></p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#text" data-toggle="tab"><?php _e('Text style', 'luna') ?></a></li>
			<li><a href="#links" data-toggle="tab"><?php _e('Multimedia', 'luna') ?></a></li>
			<li><a href="#quotes" data-toggle="tab"><?php _e('Quotes', 'luna') ?></a></li>
			<li><a href="#code" data-toggle="tab"><?php _e('Code', 'luna') ?></a></li>
			<li><a href="#lists" data-toggle="tab"><?php _e('Lists', 'luna') ?></a></li>
			<li><a href="#smilies" data-toggle="tab"><?php _e('Smilies', 'luna') ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="text">
				<p><?php _e('The following tags change the appearance of text:', 'luna') ?></p>
				<p><code>[b]<?php _e('Bold text', 'luna') ?>[/b]</code> <?php _e('produces', 'luna') ?> <strong><?php _e('Bold text', 'luna') ?></strong></p>
				<p><code>[u]<?php _e('Underlined text', 'luna') ?>[/u]</code> <?php _e('produces', 'luna') ?> <span class="bbu"><?php _e('Underlined text', 'luna') ?></span></p>
				<p><code>[i]<?php _e('Italic text', 'luna') ?>[/i]</code> <?php _e('produces', 'luna') ?> <em><?php _e('Italic text', 'luna') ?></em></p>
				<p><code>[s]<?php _e('Strike-through text', 'luna') ?>[/s]</code> <?php _e('produces', 'luna') ?> <span class="bbs"><?php _e('Strike-through text', 'luna') ?></span></p>
				<p><code>[ins]<?php _e('Inserted text', 'luna') ?>[/ins]</code> <?php _e('produces', 'luna') ?> <ins><?php _e('Inserted text', 'luna') ?></ins></p>
				<p><code>[color=#FF0000]<?php _e('Red text', 'luna') ?>[/color]</code> <?php _e('produces', 'luna') ?> <span style="color: #ff0000"><?php _e('Red text', 'luna') ?></span></p>
				<p><code>[color=blue]<?php _e('Blue text', 'luna') ?>[/color]</code> <?php _e('produces', 'luna') ?> <span style="color: blue"><?php _e('Blue text', 'luna') ?></span></p>
				<p><code>[sub]<?php _e('Subscript text', 'luna') ?>[/sub]</code> <?php _e('produces', 'luna') ?> <sub><?php _e('Subscript text', 'luna') ?></sub></p>
				<p><code>[sup]<?php _e('Superscript text', 'luna') ?>[/sup]</code> <?php _e('produces', 'luna') ?> <sup><?php _e('Superscript text', 'luna') ?></sup></p>
				<p><code>[h]<?php _e('Heading text', 'luna') ?>[/h]</code> <?php _e('produces', 'luna') ?></p> <h4><?php _e('Heading text', 'luna') ?></h4>
			</div>
			<div class="tab-pane" id="links">
				<p><?php _e('You can create links to other locations or to email addresses using the following tags:', 'luna') ?></p>
				<p><code>[url=<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>]<?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?>[/url]</code> <?php _e('produces', 'luna') ?> <a href="<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?></a></p>
				<p><code>[url]<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>[/url]</code> <?php _e('produces', 'luna') ?> <a href="<?php echo luna_htmlspecialchars(get_base_url(true).'/') ?>"><?php echo luna_htmlspecialchars(get_base_url(true).'/') ?></a></p>
				<p><code>[email]myname@example.com[/email]</code> <?php _e('produces', 'luna') ?> <a href="mailto:myname@example.com">myname@example.com</a></p>
				<p><code>[email=myname@example.com]<?php _e('My email address', 'luna') ?>[/email]</code> <?php _e('produces', 'luna') ?> <a href="mailto:myname@example.com"><?php _e('My email address', 'luna') ?></a></p>
				<p><a id="img"></a><?php _e('If you want to display an image you can use the img tag. The text appearing after the "=" sign in the opening tag is used for the alt attribute and should be included whenever possible.', 'luna') ?></p>
				<p><code>[img=<?php _e('Luna BBCode Test', 'luna') ?>]<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/test.png[/img]</code> <?php _e('produces', 'luna') ?> <img style="height: 21px" src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/test.png" alt="<?php _e('Luna BBCode Test', 'luna') ?>" /></p>
				<br />
				<p><?php _e('Luna supports embedding from DailyMotion, Vimeo and YouTube. With the BBCode below, you can embed one of those services videos.', 'luna') ?></p>
				<p><code>[video][url]<?php _e('Put the link to the video here', 'luna') ?>[/url][/video]</code>
			</div>
			<div class="tab-pane" id="quotes">
				<p><?php _e('If you want to quote someone, you should use the quote tag.', 'luna') ?></p>
				<p><code>[quote=James]<?php _e('This is the text I want to quote.', 'luna') ?>[/quote]</code></p>
				<p><?php _e('produces a quote box like this:', 'luna') ?></p>
				<blockquote><footer><cite>James <?php _e('wrote', 'luna') ?></cite></footer><p><?php _e('This is the text I want to quote.', 'luna') ?></p></blockquote>
				<p><?php _e('If you don\'t want to quote anyone in particular, you can use the quote tag without specifying a name. If a username contains the characters [ or ] you can enclose it in quote marks.', 'luna') ?></p>
			</div>
			<div class="tab-pane" id="code">
				<p><?php _e('When displaying source code you should make sure that you use the code tag. Text displayed with the code tag will use a monospaced font and will not be affected by other tags.', 'luna') ?></p>
				<p><code>[code]<?php _e('This is some code.', 'luna') ?>[/code]</code></p>
				<p><?php _e('produces a code box like this:', 'luna') ?></p>
				<pre><code><?php _e('This is some code.', 'luna') ?></code></pre>
				<p><?php _e('You can also use syntax highlighting for C, C#, C++, HTML, Java, JavaScript, Markdown, Pascal, PHP, Python, SQL, XHTML and XML. The language has to be noted on the first line inside the codetag and can\'t be on the same line as <code>[code]</code>.', 'luna') ?></p>
				<pre>
[code]
[[php]]	
if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '&lt;li&gt;&lt;a href="'.$cur_item['url'].'"&gt;'.$cur_item['name'].'&lt;/a&gt;&lt;/li&gt;';
[/code]
				</pre>
				<p><?php _e('produces a code box like this:', 'luna') ?></p>
				<div class="codebox"><pre><code class="language-php">if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '&lt;li&gt;&lt;a href="'.$cur_item['url'].'"&gt;'.$cur_item['name'].'&lt;/a&gt;&lt;/li&gt;'; </code></pre></div>
				<p><code>[c]<?php _e('This is some code.', 'luna') ?>[/c]</code> <?php _e('produces a code box like this:', 'luna') ?> <code><?php _e('This is some code.', 'luna') ?></code></p>
			</div>
			<div class="tab-pane" id="lists">
				<p><a id="list"></a><?php _e('To create a list you can use the list tag. You can create 2 types of lists using the list tag.', 'luna') ?></p>
				<p><code>[list][*]<?php _e('Example list item 1.', 'luna') ?>[/*][*]<?php _e('Example list item 2.', 'luna') ?>[/*][*]<?php _e('Example list item 3.', 'luna') ?>[/*][/list]</code>
				<br /><span><?php _e('produces a bulleted list.', 'luna') ?></span></p>
				<div>
					<ul><li><p><?php _e('Example list item 1.', 'luna') ?></p></li><li><p><?php _e('Example list item 2.', 'luna') ?></p></li><li><p><?php _e('Example list item 3.', 'luna') ?></p></li></ul>
				</div>
				<p><code>[list=1][*]<?php _e('Example list item 1.', 'luna') ?>[/*][*]<?php _e('Example list item 2.', 'luna') ?>[/*][*]<?php _e('Example list item 3.', 'luna') ?>[/*][/list]</code>
				<br /><span><?php _e('produces a numbered list.', 'luna') ?></span></p>
				<div>
					<ol class="decimal"><li><p><?php _e('Example list item 1.', 'luna') ?></p></li><li><p><?php _e('Example list item 2.', 'luna') ?></p></li><li><p><?php _e('Example list item 3.', 'luna') ?></p></li></ol>
				</div>
			</div>
			<div class="tab-pane" id="smilies">
				<p><a id="emoticons"></a><?php _e('If enabled, the forum can convert a series of smilies to graphical representations. The following smilies you can use are:', 'luna') ?></p>
				<div class="row">
<?php

// Display the smiley set
require LUNA_ROOT.'include/parser.php';

$smilies = get_smilies();

$smiley_groups = array();

foreach ($smilies as $smiley_text => $smiley_img)
	$smiley_groups[$smiley_img][] = $smiley_text;

foreach ($smiley_groups as $smiley_img => $smiley_texts) {
	if ($luna_config['o_emoji'] == 1)
		echo "\t\t".'<div class="col-sm-3"><p><code>'.implode('</code> '.__('and', 'luna').' <code>', $smiley_texts).'</code> <span>'.__('produces', 'luna').'</span> <span class="emoji">'.$smiley_img.'</span></p></div>'."\n";
	else
		echo "\t\t".'<div class="col-sm-3"><p><code>'.implode('</code> '.__('and', 'luna').' <code>', $smiley_texts).'</code> <span>'.__('produces', 'luna').'</span> <img src="'.luna_htmlspecialchars(get_base_url(true)).'/img/smilies/'.$smiley_img.'" width="'.$luna_config['o_emoji_size'].'" height="'.$luna_config['o_emoji_size'].'" alt="'.$smiley_texts[0].'" /></p></div>'."\n";
}

?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('General use', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<p><?php _e('Explains some of the basics on how to work with this forum software.', 'luna') ?></p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#forum" data-toggle="tab"><?php _e('Forums and threads', 'luna') ?></a></li>
			<li><a href="#profile" data-toggle="tab"><?php _e('Profile', 'luna') ?></a></li>
			<li><a href="#searching" data-toggle="tab"><?php _e('Search', 'luna') ?></a></li>
		</ul>
		<div class="tab-content">
		  <div class="tab-pane active" id="forum">
                <h3><?php _e('What do the labels in front of thread titles mean?', 'luna') ?></h3>
                <p><?php _e('You\'ll see that some of the threads are labeled, different labels have different meanings.', 'luna') ?></p>
				<table class="table">
                	<thead>
                        <tr>
                            <th><?php _e('Label', 'luna') ?></th>
                            <th><?php _e('Explanation', 'luna') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="label label-success"><span class="fa fa-fw fa-check"></span></span></td>
                            <td><?php _e('Threads marked with a check are solved according to the author.', 'luna') ?></td>
                        </tr>
                        <tr>
                            <td><span class="label label-warning"><span class="fa fa-fw fa-thumb-tack"></span></span></td>
                            <td><?php _e('Pinned threads are usually important to read. It\'s worth it to take a look there.', 'luna') ?></td>
                        </tr>
						<tr>
							<td><span class="label label-primary"><span class="fa fa-fw fa-map-marker"></span></span></td>
							<td><?php _e('Much like pinned threads, these are important, however, they do not stay on the top of the list.', 'luna') ?></td>
						</tr>
                        <tr>
                            <td><span class="label label-danger"><span class="fa fa-fw fa-lock"></span></span></td>
                            <td><?php _e('When a you see a closed label, it means you can\'t comment on that thread any more, unless you have a permission that overwrites this. The thread is still available to read, though.', 'luna') ?></td>
                        </tr>
                        <tr>
                            <td><span class="label label-info"><span class="fa fa-fw fa-arrows-alt"></span></span></td>
                            <td><?php _e('This thread has been moved to another forum. Admins and moderators can choose to show this notification, or simply not show it. The original forum where this thread was located in, won\'t show and thread stats anymore.', 'luna') ?></td>
                        </tr>
						<tr>
							<td><span class="label label-default label-new"><span class="fa fa-fw fa-bell"></span></span></td>
							<td><?php _e('This thread has received a new comment since you last visited the board..', 'luna') ?></td>
						</tr>
                        <?php if (!$luna_user['is_guest'] && $luna_config['o_has_commented'] == '1') { ?>
                        <tr>
                            <td>&middot;</td>
                            <td><?php _e('This little dot appears when you have made a comment in this thread.', 'luna') ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
				<h3><?php _e('Smilies, signatures, avatars and images are not visible?', 'luna') ?></h3>
				<p><?php _e('You can change the behavior of the thread view in your profile settings. There you can enable smilies, signatures, avatars and images in comments, but they should be enabled by default unless your forum admin has disabled those features.', 'luna') ?></p>
				<h3><?php _e('Why can\'t I see and threads or forums?', 'luna') ?></h3>
				<p><?php _e('You might not have the correct permissions to do so, ask the forum administrator for more help.', 'luna') ?></p>
			</div>
			<div class="tab-pane" id="profile">
				<h3><?php _e('Why can\'t I see any profiles?', 'luna') ?></h3>
				<p><?php _e('You might not have the correct permissions to do so, ask the forum administrator for more help.', 'luna') ?></p>
				<h3><?php _e('My profile doesn\'t contain as much as others?', 'luna') ?></h3>
				<p><?php _e('You\'re profile will only display fields that are enabled and filled in on your profile personality page. You might want to see if you missed some fields.', 'luna') ?></p>
			</div>
			<div class="tab-pane" id="searching">
				<h3><?php _e('Are there more options to search?', 'luna') ?></h3>
				<p><?php _e('When you go to the search page, you\'ll find yourself on a page with 1 search box. Below that search box there is a link to Advanced search, here you can find more search options! This feature may not be available on your device, if disabled by the forum admin.', 'luna') ?></p>
				<h3><?php _e('Why can\'t search in more then 1 forum at once?', 'luna') ?></h3>
				<p><?php _e('You might not have the correct permissions to do so, ask the forums administrator for more help.', 'luna') ?></p>
			</div>
		</div>
	</div>
</div>
<?php
if ($luna_user['is_admmod']) {
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Moderating', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<p><?php _e('Admins and moderators need help sometimes, too! The basics of moderating are explained here.', 'luna') ?></p>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#forums" data-toggle="tab"><?php _e('Forums', 'luna') ?></a></li>
			<li><a href="#threads" data-toggle="tab"><?php _e('Threads', 'luna') ?></a></li>
			<li><a href="#users" data-toggle="tab"><?php _e('Users', 'luna') ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="forums">
				<h3><?php _e('How do I moderate a forum?', 'luna') ?></h3>
				<p><?php _e('The moderation options are available at the bottom of the page. Those features aren\'t available for all moderators. When you click this button, you will be send to a page where you can manage the current forum. From there, you can move, delete, merge, close and open multiple threads at once.', 'luna') ?></p>
			</div>
			<div class="tab-pane" id="threads">
				<h3><?php _e('How do I moderate a thread?', 'luna') ?></h3>
				<p><?php _e('The moderation options are available at the bottom of the page. Those features aren\'t available for all moderators. When you click this button, you will be send to a page where you can manage the current thread from there, you can select multiple comment to delete or split from the current thread at once.', 'luna') ?></p>
				<p><?php _e('Next to the "Moderate thread" button, you can find options to move, open or close the thread. You can also pin the thread from there, or unpin it.', 'luna') ?></p>
			</div>
			<div class="tab-pane" id="users">
				<h3><?php _e('How do I moderate an user?', 'luna') ?></h3>
				<p><?php _e('Moderating options are available in the users profile. You can find the moderation options under "Administration" in the users profile menu. Those features aren\'t available for all moderators.', 'luna') ?></p>
				<p><?php _e('The Administration page allow you to check if the user has an admin note, and you can also change that note if required. You can also change the comment count of the user. At this page, the user can also be given moderator permissions on a per-forum base, though the user must have a moderator account to be able to actually use those permissions.', 'luna') ?></p>
				<p><?php _e('Finally, you can ban or delete a user from his profile. If you want to ban and/or delete multiple users at once, you\'re probably better off with the advanced user management features in the Backstage.', 'luna') ?></p>
			</div>
		</div>
	</div>
</div>
<?php } ?>