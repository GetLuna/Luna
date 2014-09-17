<?php

session_start();

if( !isset($_SESSION['luna_install_step1']) ) {
	die('Installation access denied.');
}

function generate_config_file()
{
	global $db_host, $db_name, $db_username, $db_password, $db_prefix;

	return '<?php'."\n\n".'$db_host = \''.$db_host."';\n".'$db_name = \''.addslashes($db_name)."';\n".'$db_username = \''.addslashes($db_username)."';\n".'$db_password = \''.addslashes($db_password)."';\n".'$db_prefix = \''.addslashes($db_prefix)."';\n".'$p_connect = false;'."\n\ndefine('LUNA', 1);\n".'?>';
}

  require_once('assets/top.php');

  if(isset($_POST['mysql'])){

		try {

			$db_host		= $_POST['db_h'];//MySQL Host.
    	    $db_username	= $_POST['db_u'];//MySQL Username
		    $db_password	= (!$_POST['db_p'])? '' : $_POST['db_p'];//MySQL Password
		    $db_name		= $_POST['db_d'];//MySQL Database
		    $db_prefix		= $_POST['db_pr'];//MySQL Prefix.

    	    if(!$db_host or !$db_username or !$db_name or !$db_prefix){//Check if all values are there.
    		    throw new Exception('All fields are required!');//If not, error.
		    }elseif(!$conn = @mysqli_connect($db_host, $db_username, $db_password)){//Checks if MySQL connection could be established.
			    throw new Exception('MySQL Server connection could not be established.');//If not, error.
		    }elseif(!@mysqli_select_db($conn, $db_name)){//Checks for connection to database.
			    throw new Exception('MySQL Database connection could not be established.');//If not, error.
    	    }else{
				
				header('Content-Type: text/x-delimtext; name="config.php"');
				header('Content-disposition: attachment; filename=config.php');
				
				echo generate_config_file();

				// Generate the config.php file data
				$config = generate_config_file();
			
				// Attempt to write config.php
				$written = false;
				$file_name = @fopen('../config.php', 'wb');
				if ($file_name) {
					fwrite($file_name, $config);
					fclose($file_name);
		
					$written = true;
				}
				
				// Make an educated guess regarding base_url
				$base_url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';	// protocol
				$base_url .= preg_replace('%:(80|443)$%', '', $_SERVER['HTTP_HOST']);							// host[:port]
				$base_url .= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));							// path
			
				if (substr($base_url, -1) == '/')
					$base_url = substr($base_url, 0, -1);

			    // Running SQL on Database
				$_SESSION['luna_install_step2'] = true;

                $MYSQL = new mysqli($db_host, $db_username, $db_password, $db_name);
                               
// Add database content here

                header("Location: step3.php");

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
		<div class="col-xs-3 wizard-step active">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Create the database</div>
		</div> 
		<div class="col-xs-3 wizard-step disabled">
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
            <label for="host" class="col-sm-3 control-label">Host</label>
            <div class="col-sm-9">
                <input type="text" name="db_h" id="host" class="form-control" value="localhost" />
            </div>
        </div>
        <div class="form-group">
            <label for="username" class="col-sm-3 control-label">Username</label>
            <div class="col-sm-9">
                <input type="text" name="db_u" id="username" class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-3 control-label">Password</label>
            <div class="col-sm-9">
                <input type="password" name="db_p" id="password" class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for="database" class="col-sm-3 control-label">Database</label>
            <div class="col-sm-9">
                <input type="text" name="db_d" id="database" class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for="prefix" class="col-sm-3 control-label">Prefix</label>
            <div class="col-sm-9">
                <input type="text" name="db_pr" id="prefix" class="form-control" value="luna_" />
            </div>
        </div>
        <br />
        <input type="submit" name="mysql" value="Test and continue" class="btn btn-default pull-right" />
    </form>

<?php
  require_once('assets/bot.php');

?>