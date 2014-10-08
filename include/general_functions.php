<?php

/*
 * Copyright (C) 2013-2014 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Show errors that occured when there are errors
function draw_error_panel($errors) {
	global $lang, $cur_error;

	if (!empty($errors)) {
?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Post errors'] ?></h3>
        </div>
        <div class="panel-body">
<?php
    foreach ($errors as $cur_error)
        echo $cur_error;
?>
        </div>
    </div>
<?php
	}

}

// Show the preview panel
function draw_preview_panel($message) {
	global $lang, $hide_smilies, $message;

	if (!empty($message)) {
		require_once FORUM_ROOT.'include/parser.php';
		$preview_message = parse_message($message, $hide_smilies);
	
?>
<div class="panel panel-default panel-border">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Post preview'] ?></h3>
	</div>
	<div class="panel-body">
		<?php echo $preview_message ?>
	</div>
</div>
<?php
	}
}

// Show the preview panel
function draw_editor($height) {
	global $lang, $orig_message, $quote, $fid, $is_admmod;

	if ($fid && $is_admmod)
		$pin_button = '<div class="btn-group" data-toggle="buttons"><label class="btn btn-success"><input type="checkbox" name="stick_topic" value="1" '.(isset($_POST['stick_topic']) ? ' checked="checked"' : '').' /><span class="fa fa-thumb-tack"></span></label></div>';

?>
<div class="panel panel-border panel-default">
	<fieldset class="postfield">
		<input type="hidden" name="form_sent" value="1" />
		<div class="btn-toolbar textarea-toolbar">
			<?php echo $pin_button ?>
			<div class="btn-group">
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('b');" title="<?php echo $lang['Bold']; ?>"><span class="fa fa-bold fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('u');" title="<?php echo $lang['Underline']; ?>"><span class="fa fa-underline fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('i');" title="<?php echo $lang['Italic']; ?>"><span class="fa fa-italic fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('s');" title="<?php echo $lang['Strike']; ?>"><span class="fa fa-strikethrough fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('h');" title="<?php echo $lang['Heading']; ?>"><span class="fa fa-header fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('sub');" title="<?php echo $lang['Subscript']; ?>"><span class="fa fa-subscript fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('sup');" title="<?php echo $lang['Superscript']; ?>"><span class="fa fa-superscript fa-fw"></span></a>
			</div>
			<div class="btn-group hidden-xs">
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('quote');" title="<?php echo $lang['Quote']; ?>"><span class="fa fa-quote-left fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('code');" title="<?php echo $lang['Code']; ?>"><span class="fa fa-code fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('c');" title="<?php echo $lang['Inline code']; ?>"><span class="fa fa-file-code-o fa-fw"></span></a>
			</div>
			<div class="btn-group hidden-xs">
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('url');" title="<?php echo $lang['URL']; ?>"><span class="fa fa-link fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('img');" title="<?php echo $lang['Image']; ?>"><span class="fa fa-image fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('video');" title="<?php echo $lang['Video']; ?>"><span class="fa fa-play-circle fa-fw"></span></a>
			</div>
			<div class="btn-group hidden-xs">
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('list');" title="<?php echo $lang['List']; ?>"><span class="fa fa-list-ul fa-fw"></span></a>
				<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('*');" title="<?php echo $lang['List item']; ?>"><span class="fa fa-asterisk fa-fw"></span></a>
			</div>
			<div class="btn-group pull-right">
				<button class="btn btn-primary" type="submit" name="submit" accesskey="s"><span class="fa fa-plus"></span><span class="hidden-xs"> <?php echo $lang['Submit'] ?></span></button>
				<button class="btn btn-default<?php if ($luna_config['o_post_responsive'] == 0) echo ' hidden-sm hidden-xs'; ?>" type="submit" name="preview" accesskey="p"><span class="fa fa-eye"></span><span class="hidden-xs"> <?php echo $lang['Preview'] ?></span></button>
			</div>
		</div>
		<textarea class="form-control"  placeholder="<?php echo $lang['Start typing'] ?>" name="req_message" id="post_field" rows="<?php echo $height ?>"><?php echo isset($_POST['req_message']) ? luna_htmlspecialchars($orig_message) : (isset($quote) ? $quote : ''); ?></textarea>
	</fieldset>
</div>
<script>
function AddTag(tag) {
   var Field = document.getElementById('post_field');
   var val = Field.value;
   var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
   var before_txt = val.substring(0, Field.selectionStart);
   var after_txt = val.substring(Field.selectionEnd, val.length);
   Field.value = before_txt + '[' + tag + ']' + selected_txt + '[/' + tag + ']' + after_txt;
}
</script>
<?php
}