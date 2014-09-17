<?php

require_once('assets/top.php');

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
		<div class="col-xs-3 wizard-step complete">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Personalize</div>
		</div>
		<div class="col-xs-3 wizard-step active">
			<div class="progress"><div class="progress-bar"></div></div>
			<a href="#" class="wizard-dot"></a>
			<div class="wizard-info text-center">Have fun</div>
		</div>
	</div>
</div>
<div class="alert alert-success">Luna has been successfully installed!</div>
<div class="alert alert-danger">Don't forget to remove the <code>/install/</code> folder in the forum root, as this is a security risk! The Backstage will keep warning for this until this folder doesn't exist anymore.</div>
<a href="../index.php" class="btn btn-default pull-right">Go to your board</a>

<?php

require_once('assets/bot.php');

?>