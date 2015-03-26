<?php
$style = '';

if ($current_id == $cur_forum['fid'])
?>
<a href="<?php echo $page ?>?id=<?php echo $cur_forum['fid'] ?>" class="list-group-item <?php echo $item_status ?>">
	<?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?>
</a>