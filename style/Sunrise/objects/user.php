<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 user-entry">
    <div class="media">
        <a class="pull-left" href="#">
            <?php echo $user_avatar; ?>
        </a>
        <div class="media-body">
            <h2 class="media-heading"><?php echo '<a href="me.php?id='.$user_data['id'].'">'.luna_htmlspecialchars($user_data['username']).'</a>' ?> <small><?php echo $user_title_field ?></small></h2>
            <?php echo forum_number_format($user_data['num_posts']) ?> posts since <?php echo format_time($user_data['registered'], true) ?>
        </div>
    </div>
</div>