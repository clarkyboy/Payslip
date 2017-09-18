<?php
	session_start(); session_write_close();
	if( !isset($_SESSION['tm_law']['access']) )
	{
		header("Location: ./login.php?do=nosession");
	}
	
	if( isset($_GET['i'])){ $i = $_GET['i']; } else {$errMessage ="";}
	
	require_once("scripts/xpconnect.php");
	require_once("scripts/connect.php");
	include_once("header.php");
?>
<style>
.relative{position:relative;}
.clear{clear:both;}
#btnapp-mw {
    text-align: center;
    padding: 20px 0px;
    border: 2px solid #ffffff;
    background: rgb(236, 240, 245);
    margin-left: -9px;
    margin-top: -11px;
    margin-bottom: -10px;
	box-shadow: inset -1px 1px 4px #d2d6de;
	-moz-box-shadow: inset -1px 1px 4px #d2d6de;
	-webkit-box-shadow: inset -1px 1px 4px #d2d6de;
}
#table-w{
	float: left;
}
#table-details-w{
	float: right;
	background:#fff;
}
tr.row-active > td {
    background: rgba(17, 197, 135, 0.5);
    color: #fff;
}
tr.row-active > td a{
	color: #fff;
}

selected-tr{background:#e9f4fb;}

.right-pane {
	position: absolute;
	right: 0px;
	top: 50px;
	background: #eee;
	height: 100%;
	min-width: 489px;
	padding: 10px;
	box-shadow: 0px 1px 20px rgba(50, 50, 50, 0.50);
}

#panel-options {
	display: table;
}

#panel-options div {
	background:#FFF;
	border: thin solid #ccc;
	border-radius:3px;
	height: 25px;
	width: 25px;
	vertical-align:middle;
	text-align:center;
	display:table-cell;
	
	-moz-transition-property: background;  
	-moz-transition-duration: 0.5s;
	-webkit-transition-property: background;  
	-webkit-transition-duration: 0.5s;
	-o-transition-property: background;  
	-o-transition-duration: 0.5s;
	-ms-transition-property: background;  
	-ms-transition-duration: 0.5s;
	transition-property: background;  
	transition-duration: 0.5s;
}

#panel-options div:hover {
	background:#ddd;
}

.panel-table {
	display: table;
	color:#333;

	/*border: solid thin #000;*/
	width:100%;
}
.selected-tr {
    background: #e9f4fb;
}
.form-group{margin-bottom:5px;}

</style>
<script type="text/javascript">
var $ = jQuery;

$(document)
.ready(function(){
	loadGrid('<?php echo $i;?>');
	checkGeneratedDocuments();
})
.ajaxComplete(function() {
	$('.loader,.loader2').remove();
});

function checkGeneratedDocuments(){
	try{
		$.post("perf/loadCheckGeneratedDocs.php",
		{},
		function(data,status){
			params = data.split("⁞");
			
			if(params.length > 0)
			{
				document.getElementById('notifCountPending').innerHTML = params[0];
				document.getElementById('countPending').innerHTML = '('+params[0]+')';
				
				document.getElementById('notifCountGenerated').innerHTML = params[1];
				document.getElementById('countNew').innerHTML = '('+params[1]+')';
				
				document.getElementById('countArchive').innerHTML = '('+params[2]+')';
				
				if((eval(params[0]) + eval(params[1])) > 0) {total = eval(params[0]) + eval(params[1]);} else {total ='';}
				
				document.getElementById('notifCountTotal').innerHTML = total;
			}
		});
	}
	catch(e){}
}
setInterval(function(){checkGeneratedDocuments();}, 10000);

function loadGrid(mode){
if(typeof mode === 'undefined' )mode = 'pending'; else mode = mode;

	$('body').prepend('<div id="gm-loader" class="loader">loading..</div>');
	//$('#table-w').width( $('#table-w').parent('.box-body').width() - 5 );
	//$('#table-details-w').width( $('#table-w').parent('.box-body').width() - 275 );
	$.post('perf/loadExportGrid.php',
	{
		i: mode,
	}, function(data){
		params = data.split("⁞");
		
		$('#table-w').html(params[1]);
		
		document.getElementById('tab-head-new').classList.remove('active');
		document.getElementById('tab-head-pending').classList.remove('active');
		document.getElementById('tab-head-archive').classList.remove('active');
		document.getElementById('tab-head-'+params[0]).classList.add('active');

	});
}


</script>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
<?php require_once("navbar-suit.php");?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container">
      <!-- Content Header (Page header) -->
      <section class="content-header">
		<h1>&nbsp;</h1>
        <ol class="breadcrumb">
          <li><a href="./"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="suit.php">Suit</a></li>
		  <li><a href="#">Suit Exports</a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Suit Exports:</h3>
          </div>
          <div class="box-body relative">
			<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li id="tab-head-pending" onclick="javascript:loadGrid('pending');checkGeneratedDocuments();"><a href="#tab_1" data-toggle="tab">Pending <span id="countPending">(0)</span></a></li>
              <li id="tab-head-new" onclick="javascript:loadGrid('new');checkGeneratedDocuments();"><a href="#tab_2" data-toggle="tab">New <span id="countNew">(0)</span></a></li>
              <li id="tab-head-archive" onclick="javascript:loadGrid('archive');checkGeneratedDocuments();"><a href="#tab_3" data-toggle="tab">Archive <span id="countArchive">(0)</span></a></li>
            </ul>
			<div id="table-w" style='width:100%'></div>
            <!-- /.tab-content -->
          </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
<?php include_once("footer.php"); 