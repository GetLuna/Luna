<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="jumbotron profile">
	<div class="container">
		<div class="row">
			<div class="col">
                <h4><?php echo $user['username'] ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="main profile container">
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo get_avatar( $user['id'] ) ?>" alt="Avatar" class="avatar">
			</div>
			<?php load_me_nav('notifications'); ?>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="title-block title-block-primary title-block-nav">
				<h2><i class="far fa-fw fa-circle"></i> <?php _e('Notifications', 'luna') ?></h2>
				<nav class="nav nav-tabs">
					<a class="nav-item nav-link active" href="#new" aria-controls="new" role="tab" data-toggle="tab">
						<i class="fas fa-fw fa-circle"></i><span class="d-none d-lg-inline"> <?php _e('New', 'luna') ?></span>
					</a>
					<a class="nav-item nav-link" href="#seen" aria-controls="seen" role="tab" data-toggle="tab">
						<i class="far fa-fw fa-circle"></i><span class="d-none d-lg-inline"> <?php _e('Seen', 'luna') ?></span>
					</a>
				</nav>
			</div>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="new">
                    <?php if ($num_not_unseen != '0') { ?>
                        <a class="btn btn-primary" href="notifications.php?id=<?php echo $luna_user['id'] ?>&action=readnoti"><span class="fas fa-fw fa-eye"></span> <?php _e('Seen all', 'luna') ?></a>
                    <?php } ?>
                    <?php echo $not; ?>
				</div>
				<div role="tabpanel" class="tab-pane" id="seen">
                    <?php if ($num_not_seen != '0') { ?>
                        <a class="btn btn-danger" href="notifications.php?id=<?php echo $luna_user['id'] ?>&action=delnoti"><span class="fas fa-fw fa-trash"></span> <?php _e('Remove all', 'luna') ?></a>
                    <?php } ?>
                    <?php echo $not_seen; ?>
				</div>
			</div>
		</div>
	</div>
</div>