<?php
if (luna_strlen(luna_htmlspecialchars($user_data['username'])) > 14)
	$cur_user_name = utf8_substr(luna_htmlspecialchars($user_data['username']), 0, 12).'...';
else
	$cur_user_name = luna_htmlspecialchars($user_data['username']);
?>
<div class="col-xl-4 col-lg-6 col-md-6 col-12">
    <div class="user-entry">
        <div class="media">
            <a href="<?php echo 'profile.php?id='.$user_data['id'] ?>">
                <img class="img-fluid" src="<?php echo $user_avatar ?>" alt="">
            </a>
            <div class="media-body">
                <h5 class="mt-0 mb-0">
                    <a title="<?php echo luna_htmlspecialchars($user_data['username']) ?>" href="profile.php?id=<?php echo $user_data['id'] ?>"><?php echo $cur_user_name ?></a>
                </h5>
                <h6><?php echo $user_title_field ?></h6>
                <?php echo forum_number_format($user_data['num_comments']).' '._n('comment since', 'comments since', $user_data['num_comments'], 'luna').' '.format_time($user_data['registered'], true); ?>
            </div>
        </div>
    </div>
</div>