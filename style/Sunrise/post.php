<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_posting['id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_posting['forum_name']) ?></a>
</div>
<?php draw_error_panel($errors); ?>
<?php draw_preview_panel($message); ?>

<?php echo $form."\n" ?>
<?php

if ($luna_user['is_guest']) {
    $email_label = ($luna_config['p_force_guest_email'] == '1') ? '<strong>'.$lang['Email'].'</strong>' : $lang['Email'];
    $email_form_name = ($luna_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';

?>
            <label class="required hidden"><?php echo $lang['Guest name'] ?></label><input class="form-control" type="text" placeholder="<?php echo $lang['Guest name'] ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo luna_htmlspecialchars($username); ?>" maxlength="25" tabindex="<?php echo $cur_index++ ?>" />
            <label class="conl<?php echo ($luna_config['p_force_guest_email'] == '1') ? ' required' : '' ?> hidden"><?php echo $email_label ?></label><input class="form-control" type="text" placeholder="<?php echo $lang['Email'] ?>" name="<?php echo $email_form_name ?>" value="<?php if (isset($_POST[$email_form_name])) echo luna_htmlspecialchars($email); ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php

}

if ($fid): ?>
            <label class="required hidden"><?php echo $lang['Subject'] ?></label><input class="form-control" placeholder="<?php echo $lang['Subject'] ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo luna_htmlspecialchars($subject); ?>" maxlength="70" tabindex="<?php echo $cur_index++ ?>" />
<?php endif; ?>
<?php draw_editor('20'); ?>