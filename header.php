<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Trademark | Case Management</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  
  
  <!-- Bootstrap datepicker -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap-datepicker.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Animate CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
  <!-- loader -->
  <link rel="stylesheet" href="css/loader.css">
  <!-- custom style -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.2/css/select.dataTables.min.css">
  
    <!-- jQuery 2.2.3 -->
	<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
	<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- Bootstrap datepicker -->
	<script src="bootstrap/js/bootstrap-datepicker.min.js"></script>
	<script src="plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
	<script src="https://cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
	
	<script src="js/thumbPreviewMain.js" type="text/javascript"></script>
	
	<!-- AdminLTE App -->
	<script src="dist/js/app.min.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="dist/js/demo.js"></script>
	<!-- Custom JS -->
	<script src="js/manage.js"></script>
  
  <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">
<script>
	function checkSession(){
		try{
			$.post("perf/loadCheckSession.php",
			{},
			function(data,status){
				if(data == "no"){
					//get current page before redirect to login page
					curPage = encodeURI(window.location.href);
					
					//now redirect to login page
					window.location.assign("./login.php?do=NoSession"+"&next="+curPage);
				}
			});
		}
		catch(e){}
	}
	setInterval(function(){checkSession();}, 10000);
</script>  
  <style>
	html, body
		{
		  height: 100%;
		}
  </style>
</head>
