<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" type="text/css" href="include/bootstrap/css/bootstrap.min.css" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <luna_head>
	</head>
	<body>
        <div id="header">
            <luna_navlinks>
            <div class="container">
                <div class="box">
                    <div id="brdtitle">
                        <luna_title>
                        <luna_desc>
                    </div>
                    <luna_status>
                </div>
                <luna_announcement>
            </div>
        </div>
        <div class="container">
            <div id="brdmain">
                <luna_main>
            </div>
            <luna_footer>
        </div>
        <!-- Javascript start -->
        <script src="include/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
		tinymce.init({
			plugins: [
				["bbcode autolink link image lists code media paste"]
			],
			selector: 'textarea.tinymce',
			skins: 'lightgrey',
			toolbar: "undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | subscript superscript | bullist numlist | link image | code",
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