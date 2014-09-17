<?php

session_start();

if( !isset($_SESSION['luna_install_step2']) ) {
	die('Installation access denied.');
}

  require_once('assets/top.php');
  require_once('../config.php');
  
function luna_hash($str)
{
	return sha1($str);
}

  if(isset($_POST['continue'])){
    	try {

            foreach( $_POST as $parent => $child ) {
                $_POST[$parent] = htmlentities($child);
            }

			$title		= $_POST['title'];
			$email		= $_POST['email'];
			$username	= $_POST['username'];
			$password	= luna_hash($_POST['password']);
			$user_email = $_POST['user_email'];
			$date		= time();

			if(!$title or !$email or !$username or !$password or !$user_email){
				throw new Exception('All fields are required!');
			}else{
				
				$MYSQL = new mysqli($db_host, $db_username, $db_password, $db_name);
				
                $MYSQL->query("UPDATE `".$db_prefix."config` SET `conf_value` = '".$title."' WHERE `conf_name` = 'c_board_title';");
				
                $MYSQL->query("UPDATE `".$db_prefix."config` SET `conf_value` = '".$email."' WHERE `conf_name` = 'c_webmaster_email';");
				
                $MYSQL->query("UPDATE `".$db_prefix."config` SET `conf_value` = '".$user_email."' WHERE `conf_name` = 'c_admin_email';");
				
                $MYSQL->query("INSERT INTO `".$db_prefix."users` (`id`, `group_id`, `username`, `password`, `email`, `title`, `realname`, `url`, `facebook`, `msn`, `twitter`, `google`, `location`, `signature`, `disp_topics`, `disp_posts`, `email_setting`, `notify_with_post`, `auto_notify`, `show_smilies`, `show_img`, `show_img_sig`, `show_avatars`, `show_sig`, `timezone`, `dst`, `time_format`, `date_format`, `language`, `style`, `backstage_color`, `num_posts`, `last_post`, `last_search`, `last_email_sent`, `last_report_sent`, `registered`, `registration_ip`, `last_visit`, `admin_note`, `activate_string`, `activate_key`) VALUES
(2, 1, '".$username."', '".$password."', '".$user_email."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 'English', 'Random', '#14a3ff', 1, ".$date.", NULL, NULL, NULL, ".$date.", '0.0.0.0', 0, NULL, NULL, NULL);");

                header("Location: step4.php");

			}

    	}catch(Exception $e){
    		echo '<div class="alert alert-danger">'.$e->getMessage().'</div>';
    	}
    }

?>
<div class="container">
	<div class="row wizard" style="border-bottom:0;">
		<div class="col-xs-3 wizard-step complete">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Test the server</div>
		</div>
		<div class="col-xs-3 wizard-step complete">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Create the database</div>
		</div> 
		<div class="col-xs-3 wizard-step active">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Personalize</div>
		</div>
		<div class="col-xs-3 wizard-step disabled">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Have fun</div>
		</div>
	</div>
</div>
    <form class="form-horizontal" role="form" method="POST">
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Board title</label>
            <div class="col-sm-9">
               <input type="text" name="title" id="title" class="form-control" />
            </div>
        </div>
        <div class="form-group">
			<label for="email" class="col-sm-3 control-label">Administrator email</label>
            <div class="col-sm-9">
                <input type="text" name="email" id="email" class="form-control" />
            </div>
        </div>
        <hr />
        <div class="form-group">
            <label for="username" class="col-sm-3 control-label">Username</label>
            <div class="col-sm-9">
				<input type="text" name="username" id="username" class="form-control" />
            </div>
        </div>
        <div class="form-group">
			<label for="password" class="col-sm-3 control-label">Password</label>
            <div class="col-sm-9">
				<input type="password" name="password" id="password" class="form-control" />
            </div>
        </div>
        <div class="form-group">
			<label for="user_email" class="col-sm-3 control-label">Email</label>
            <div class="col-sm-9">
				<input type="text" name="user_email" id="user_email" class="form-control" />
            </div>
        </div>
        <br />
        <input type="submit" name="continue" value="Continue" class="btn btn-default pull-right" />
    </form>
<?php

  require_once('assets/bot.php');

?>