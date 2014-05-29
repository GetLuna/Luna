<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="../include/bootstrap/css/bootstrap.min.css" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<luna_head>
	</head>
	<body>
    	<div class="container">
            <div class="row">
                <luna_main>
            </div>
            <div class="row">
                <luna_footer>
            </div>
		</div>
        <!-- Javascript start -->
        <script src="../include/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
		tinymce.init({
			plugins: [
				["autolink link image lists code media paste hr table textcolor"]
			],
			selector: 'textarea.tinymce',
			skins: 'lightgrey',
			toolbar:[
				"newdocument | cut copy paste removeformat | undo redo | formatselect | fontselect | fontsizeselect | forecolor | styleselect",
				"bold italic underline strikethrough | outdent indent alignleft aligncenter alignright alignjustify | table | subscript superscript | bullist numlist | hr link image blockquote | code"
			],
			menubar: false,
			relative_urls: false,
			convert_urls: false,
		});
        $(document).ready(function(){
            $("#user").focus();
        });
        </script>
        <!-- Javascript end -->
	</body>
</html>