<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

</div>
<div class="jumbotron" style="background:#999;">
	<div class="container">
		<h2><?php echo $lang['Search'] ?></h2>
        <span class="pull-right">
            <input class="btn btn-primary" type="submit" name="search" value="<?php echo $lang['Search'] ?>" accesskey="s" />
        </span>
	</div>
</div>
<div class="container">
<form id="search" method="get" action="search.php?section=advanced">
    <div class="panel panel-default">
        <div class="panel-body">
            <fieldset class="form-inline">
                <input type="hidden"  name="action" value="search" />
                <input placeholder="Keyword..." class="form-control" type="text" name="keywords" maxlength="100" />
                <input placeholder="Author..."  class="form-control" id="author" type="text" name="author" maxlength="25" />
				<select class="form-control" id="search_in" name="search_in">
					<option value="0"><?php echo $lang['Message and subject'] ?></option>
					<option value="1"><?php echo $lang['Message only'] ?></option>
					<option value="-1"><?php echo $lang['Topic only'] ?></option>
				</select>
                <select class="form-control" name="sort_by">
                    <option value="0"><?php echo $lang['Sort by post time'] ?></option>
                    <option value="1"><?php echo $lang['Sort by author'] ?></option>
                    <option value="2"><?php echo $lang['Subject'] ?></option>
                    <option value="3"><?php echo $lang['Forum'] ?></option>
                </select>
                <select class="form-control" name="sort_dir">
                    <option value="DESC"><?php echo $lang['Descending'] ?></option>
                    <option value="ASC"><?php echo $lang['Ascending'] ?></option>
                </select>
                <select class="form-control" name="show_as">
                    <option value="topics"><?php echo $lang['Topics'] ?></option>
                    <option value="posts"><?php echo $lang['Show as posts'] ?></option>
                </select>
            </fieldset>
            <fieldset>
                <div class="row">
<?php

$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

// We either show a list of forums of which multiple can be selected
if ($luna_config['o_search_all_forums'] == '1' || $luna_user['is_admmod']) {
    echo "\t\t\t\t\t\t".'<div class="col-xs-4"><div class="conl multiselect"><b>'.$lang['Forum'].'</b>'."\n";
    echo "\t\t\t\t\t\t".'<br />'."\n";
    echo "\t\t\t\t\t\t".'<div>'."\n";

    $cur_category = 0;
    while ($cur_forum = $db->fetch_assoc($result)) {
        if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
            if ($cur_category) {
                echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
                echo "\t\t\t\t\t\t\t".'</fieldset>'."\n";
            }
            echo "\t\t\t\t\t\t\t".'<fieldset><h3><span>'.luna_htmlspecialchars($cur_forum['cat_name']).'</span></h3>'."\n";
            echo "\t\t\t\t\t\t\t\t".'<div>';
            $cur_category = $cur_forum['cid'];
        }
        echo "\t\t\t\t\t\t\t\t".'<input type="checkbox" name="forums[]" id="forum-'.$cur_forum['fid'].'" value="'.$cur_forum['fid'].'" /> '.luna_htmlspecialchars($cur_forum['forum_name']).'<br />'."\n";
    }

    if ($cur_category) {
        echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
        echo "\t\t\t\t\t\t\t".'</fieldset>'."\n";
    }

    echo "\t\t\t\t\t\t".'</div>'."\n";
    echo "\t\t\t\t\t\t".'</div></div>'."\n";
}
// ... or a simple select list for one forum only
else {
    echo "\t\t\t\t\t\t".'<div class="col-xs-4"><label class="conl">'.$lang['Forum']."\n";
    echo "\t\t\t\t\t\t".'<br />'."\n";
    echo "\t\t\t\t\t\t".'<select id="forum" name="forum">'."\n";

    $cur_category = 0;
    while ($cur_forum = $db->fetch_assoc($result)) {
        if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
            if ($cur_category)
                echo "\t\t\t\t\t\t\t".'</optgroup>'."\n";

            echo "\t\t\t\t\t\t\t".'<optgroup label="'.luna_htmlspecialchars($cur_forum['cat_name']).'">'."\n";
            $cur_category = $cur_forum['cid'];
        }

        echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_forum['fid'].'">'.($cur_forum['parent_forum_id'] == 0 ? '' : '&nbsp;&nbsp;&nbsp;').luna_htmlspecialchars($cur_forum['forum_name']).'</option>'."\n";
    }

    echo "\t\t\t\t\t\t\t".'</optgroup>'."\n";
    echo "\t\t\t\t\t\t".'</select>'."\n";
    echo "\t\t\t\t\t\t".'<br /></label></div>'."\n";
}

?>
                </div>
            </fieldset>
        </div>
    </div>
</form>

<?php

    require load_page('footer.php');