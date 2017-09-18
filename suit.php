<?php
	session_start(); session_write_close();
	if( !isset($_SESSION['tm_law']['access']) )
	{
		header("Location: ./login.php?do=nosession");
	}
	else if( !$_SESSION['tm_law']['ra_id'])
	{
		header("Location: choose_lawfirm.php");
	}
	require_once("scripts/xpconnect.php");
	require_once("scripts/connect.php");
	require_once("perf/perfSuitsGenerateDocuments.php");
	include_once("header.php");
	
	if(isset($_GET['p'])) $p = $_GET['p']; else $p = '';
	
?>
<style>
.smallcontainer{
	margin-right: 3%;
	margin-left: 3%;
}
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
	loadGrid();
	checkGeneratedDocuments();
})
.ajaxComplete(function() {
	$('.loader,.loader2').remove();
});
 function reloadGrid()
 {
    $('#table2_wrapper').load(document.suit.php+ '#table2_wrapper');
    alert("reloaded");

 }
function checkGeneratedDocuments(){
	try{
		$.post("perf/loadCheckGeneratedDocs.php",
		{},
		function(data,status){
			params = data.split("⁞");
			
			if(params.length > 0)
			{
				document.getElementById('notifCountPending').innerHTML = params[0];
				document.getElementById('notifCountGenerated').innerHTML = params[1];
				
				if((eval(params[0]) + eval(params[1])) > 0)
					document.getElementById('notifCountTotal').innerHTML = eval(params[0]) + eval(params[1]);
				else
					document.getElementById('notifCountTotal').innerHTML = '';
			}
		});
	}
	catch(e){}
}
setInterval(function(){checkGeneratedDocuments();}, 10000);

function performExport(){
	var rows = [];
	var templates = [];
	$.each($("#templates tr.selected"),function(){ //get each tr which has selected class
        templates.push($(this).find('td').eq(0).text()); //find its first td and push the value
        //dataArr.push($(this).find('td:first').text()); You can use this too
    });
    console.log(templates);
	/*
	if(document.getElementById('templatesource').innerHTML != '')
	{
		template = document.getElementById('templatesource').innerHTML;
	}
	*/
	//get rows
	var raws = document.getElementsByName("ids[]");
	//parse and collect only the checked rows.
	ctr = 0;
	limit = raws.length;
	
	while(ctr < limit){
		if(raws[ctr].checked == true)
			rows.push(raws[ctr].value);
		ctr++;
	}

	console.log(rows);
	console.log(templates);

	//if(rows.length > 0)
	if(rows.length > 0 && templates.length > 0)
	{
		
		$.post("perf/perfExport.php",
		{
			rows,
			templates,
		},
		function(data,status){
			params = data.split("⁞");
		
			
			/*
			if(params.length > 0)
			{
				document.getElementById('notifCountPending').innerHTML = params[0];
				document.getElementById('notifCountGenerated').innerHTML = params[1];
				
				if((eval(params[0]) + eval(params[1])) > 0)
					document.getElementById('notifCountTotal').innerHTML = eval(params[0]) + eval(params[1]);
				else
					document.getElementById('notifCountTotal').innerHTML = '';
			}
			*/
		})
		.done(function(){
			showAlert(); //display information about document processing on top of the page.
		})
		.fail(function(){
			showAlertError("Error In Data Processing"); //display Error message on top of the page.
		});
	}
	else
	{
		showAlertError("No Template(s) Chosen"); //display Error message on top of the page.
	}
}

function loadGrid(preCallback, postCallback){

	if( typeof preCallback !== 'undefined' )preCallback ();
	
	$('body').prepend('<div id="gm-loader" class="loader">loading..</div>');
	$('#table-w').width( $('#table-w').parent('.box-body').width() - 5 );
	$('#table-details-w').width( $('#table-w').parent('.box-body').width() - 290 );
	$.post('perf/loadSuitGrid.php',
	{
	p: '<?php echo $p; ?>',
	}, function(_res){
		$('#table-w').html(_res);
		
		if(typeof postCallback !== 'undefined')	postCallback ();
		
	});
}

function showSuitDetails(_this, suitid,casenum){
	$('.hideOnViewDetails').hide();
	
	panelEffect('#right-panel','hide');
	
	if($(_this).hasClass('sdbtn-active')){
		$('#table-details-w').hide();
	}
	
	$('#table-w').animate({
		width: ($(_this).hasClass('sdbtn-active')?  $('#table-w').parent('.box-body').width() : 270)
	},'fast', function(){
		if(_this == '')
		{ 
			$.post('perf/loadSuitDetailsGrid.php',
			{
				action: 'suitdetails1',
				id:suitid,
				casenum: casenum
				
			},function(_res){
				$('#table-details-w').html(_res); 
				$('.hideOnViewDetails').hide();
			});
		}
		else if($(_this).hasClass('sdbtn-active')){
			$('.hideOnViewDetails').show();
			
			$('.sdbtn-active').removeClass('sdbtn-active');
			$('.row-active').removeClass('row-active');
		}
		else{
			if( $('.sdbtn-active').length ) $('.sdbtn-active').removeClass('sdbtn-active');
			if( $('.row-active').length ) $('.row-active').removeClass('row-active');
			
			$(_this).addClass('sdbtn-active');
			$(_this).parents('tr:eq(0)').addClass('row-active');
			
			$('#table-details-w').show();
			$('#table-details-w').html('<div>loading..</div>');
			$.post('perf/loadSuitDetailsGrid.php',
			{
				action: 'suitdetails1',
				id:suitid,
				casenum: casenum
				
			},function(_res){
				$('#table-details-w').html(_res); 
			});
		}
	});
}

function showSuitDetailsPanel(opVal,rid){
	opVal = typeof opVal !== 'undefined' ? opVal : '';

	if((document.getElementById('right-panel').style.display == "" || document.getElementById('right-panel').style.display == "block") && currentViewedID == opVal)
	{
		panelEffect('#right-panel','hide');
	}
	else
	{
		try{
			panelLoad(opVal,rid);
		}
		catch(err){}
	}
}

function panelLoad(id,rid){
	currentViewedID = id;
	
	panelEffect('#right-panel','hide');
	document.getElementById('right-panel-content').innerHTML = "<br><br><br><br><center><img src='./imgs/loader-big.gif'/></center>";
	//Show panel
	panelEffect('#right-panel','show');
	
	$.post("perf/loadSidePanelInfoRA.php",
	{
		sdid: id,
		id: rid
	},
	function(data,status){
		$( "#right-panel-content" ).remove();
		document.getElementById('right-panel-wrapper').innerHTML = '<div id="right-panel-content"></div>';
		document.getElementById('right-panel-content').innerHTML = data;
		$('#frm-SDAM .datepicker').datepicker({format:'yyyy-mm-dd'});
	});
}

function panelEffect(objectTarget,toggle) {
	objectTarget = typeof objectTarget !== 'undefined' ? objectTarget : '';
	toggle = typeof toggle !== 'undefined' ? toggle : '';	
	
	// run the effect
	if(toggle == 'show')
	{
		$( objectTarget ).show(300);
	}
	else
	{
		$( objectTarget ).hide(300);
	}
}

function updateSuit(_this){
	$('#right-panel-content .ldr-hldr').html('<img src="./imgs/bar-loader.gif" class="loader2" />');
	$.post('perf/perfUpdateSuits.php',
		$(_this).serialize(),function(_res){
		_res = _res.split("⁞");
		
		if( _res.length ){
			$(_this).find('.ldr-hldr').html('<img src="./imgs/check.png" class="loader2" />');
			$('#SPP-SD-Wpr').prepend( _res.length > 0? _res[1] : '');
			
			if(parseInt(_res[0])){
				$('.sdbox-ldr-wpr').prepend('<img src="./imgs/bar-loader.gif" class="loader2" />');
				
				
				loadGrid(
					function(){$('#table2_wrapper').html('');},
					function(){
						$('#suitBtn-' + _res[0]).click();
						showSuitDetailsPanel(
							$(_this).children('input[name="id"]').val(),
							$(_this).children('input[name="rid"]').val()
						);
					}
				);
				
				
				/*
				$.post('perf/loadSidePanelPart-SuitDetails.php',
				{
					sdid:parseInt(_res[0]),
					suitid:parseInt(_res[2]),
					
				},function(__res){
					$('#SPP-SD-Wpr').html(__res);
				});
				*/
				
				showSuitDetails('',_res[2],_res[3]);
			}
		}
	});
}
function selectRid()
{
	$(this).parents('tr').toggleClass('selected-tr');
	var count = $("#table2 input[name='ids[]']:checked").length;
	if(count>0)
		panelEffect('#template-container','show');
	else
	{
		panelEffect('#template-container','hide');
	}
}
function viewSComments(rid, rcid){
	//alert("viewSComments is triggered");
	loadRSComment(rid, rcid);
	//$('#subComments-section-'+rcid).show();
	panelEffect('#subComments-section-'+rcid, 'show');
	panelEffect('#showComments-'+rcid, 'hide');
	panelEffect('#hideComments-'+rcid, 'show');
	
}
function hideSComments(rcid){
	//alert("hideSComments is triggered");
	//$('#subComments-section-'+rcid).removeClass('fadeIn');
	$('#subComments-section-'+rcid).hide(200);
	//panelEffect('#subComments-section-'+rcid, 'hide');
	panelEffect('#showComments-'+rcid, 'show');
	panelEffect('#hideComments-'+rcid, 'hide');
}
function addRComment(rid)
{
	var cText = $('#comment-text').val();
	if(cText!="")
	{
		$.post("perf/perfAddRComment.php",{rid,cText},function(data){
			
			}).done(function(){
				//alert('Add Comment Successful' );
				loadRComment(rid);

				//$('#comments-section').load(document.URL +  ' #comments-section');
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Add Comment Failed' );
				//do something when failed
		});

	}
	else
	{
		$('#addComment').addClass(' has-error');
	}
	
}
function addRSComment(rid, rcid)
{
	
	var cText = $('#Scomment-text-'+rcid).val();
	if(cText!="")
	{
		$.post("perf/perfAddRComment.php",{rid,cText,rcid},function(data){
			
			}).done(function(){
				//alert('Add Comment Successful' );
				//$('#subComments-'+rcid).load(document.URL +  ' #subComments-'+rcid);
				loadRSComment(rid,rcid);
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Add Comment Failed' );
				//do something when failed
		});
	}
	else
	{
		$('#addSComment-'+rcid).addClass(' has-error');
	}
	

}
function loadRSComment(rid, rcid)
{
	$.post("perf/loadRSComment.php",{rcid, rid},function(data){
			
			}).done(function(data){
				//alert('Load sub comments Successful' );
				$('#subComments-section-'+rcid).html(data);
				setCommentCount(rcid);
				//$('#comments-section').load(document.URL +  ' #comments-section');
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Failed' );
				//do something when failed
		});

}
function loadRComment(rid)
{
	$.post("perf/loadRComment.php",{rid},function(data){
			
			}).done(function(data){
				//alert('Load sub comments Successful' );
				$('#commentBox-'+rid).html(data);
				$("#comments-section").animate({
				  scrollTop: $('#comments-section')[0].scrollHeight - $('#comments-section')[0].clientHeight
				}, 600);
				//$('#comments-section').load(document.URL +  ' #comments-section');
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Failed' );
				//do something when failed
		});

}
function deleteRComment(rcid, rid, sub)
{
	$.post("perf/deleteRComment.php",{rcid},function(data){
			
			}).done(function(data){
				//alert('Load sub comments Successful' );
				
				if(sub=="yes")
				{
					hideSComments(rcid);
					//viewSComments(rid, rcid);
				}
				else
				{
					loadRComment(rid);
				}
				//$('#comments-section').load(document.URL +  ' #comments-section');
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Failed' );
				//do something when failed
		});
}
function deleteRSComment(rcid, rid, sub)
{
	$.post("perf/deleteRComment.php",{rcid},function(data){
			
			}).done(function(data){
				//alert('Load sub comments Successful' );
				loadRComment(rid);
				viewSComments(rid, rcid);
				//$('#comments-section').load(document.URL +  ' #comments-section');
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Failed' );
				//do something when failed
		});
}
function confirmDelete(rcid,rid,sub)
{
	panelEffect('#confirmDelete','show');
	$('#deleteRID').val(rid);
	$('#deleteRCID').val(rcid);
	$('#deleteSub').val(sub);
	
	
}
function ConfirmDeleteYes()
{
	var rid = $('#deleteRID').val();
	var rcid = $('#deleteRCID').val();
	var sub = $('#deleteSub').val();
	//alert('rid='+rid+' rcid='+rcid);
	deleteRSComment(rcid, rid, sub);
	hideConfirmDelete();
	
}
function hideConfirmDelete(){
	panelEffect('#confirmDelete','hide');
}
function setCommentCount(rcid)
{
	$.post("perf/commentCounter.php",{rcid},function(data){
			
			}).done(function(data){
				var d = JSON.parse(data);
				//alert('Load sub comments Successful' );
				//alert('comments: '+data->count);
				console.log("data: "+d.count);
				$('#commentCounter-'+rcid).html(d.count)
				//$('#comments-section').load(document.URL +  ' #comments-section');
				// //do something when successful
				// panelEffect('#raflag-accept-success','show');
				// panelEffect('#raflag-changeStatus','hide');
				// $('#t3').click();

			}).fail(function(data){
				alert('Failed' );
				//do something when failed
		});
}
</script>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">

<div class="wrapper">
<?php require_once("navbar-suit.php");?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="smallcontainer">
      <!-- Content Header (Page header) -->
      <!-- Alert: information on document processing -->
      	<div id="dlalert" class="alert alert-info alert-dismissible" role="alert" style="display:none;">
		  <button type="button" class="close" onclick="javascript:panelEffect('#dlalert','hide');" aria-label="Close">
		    <span aria-hidden="true"> <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></span>
		  </button>
		  <strong>Document Processing: </strong> you will be notified as soon as your document is ready for download.
		</div>
	  <!-- End of ALert: Information -->
	  <!-- Alert: Error on document processing -->
      	<div id="dlalert2" class="alert alert-danger alert-dismissible" role="alert" style="display:none;">
		  <button type="button" class="close"  aria-label="Close" onclick="javascript:panelEffect('#dlalert2','hide');">
		    <span aria-hidden="true"> <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></span>
		  </button>
		  <strong>Document Processing Error: </strong> Something's wrong in processing your requested document. Please contact system administrator.
		  <div> <strong>Cause of Error:</strong> <span id="msg-span"></span></div>
		</div>
	  <!-- End of ALert: Error -->
      <section class="content-header">
		<h1>&nbsp;</h1>
        <ol class="breadcrumb">
          <li><a href="./"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="#">Suit</a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
<?php
	//Get Product information
	$sql = "SELECT * FROM product WHERE pid = ?";
	$stmt = $tmconn->prepare($sql);
	$stmt->execute(array($p));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>		  
            <h3 class="box-title">Manage: <strong><?php echo $_SESSION['tm_law']['ra_name'];?></strong> |  <b><?php echo $row['Product']; ?></b> Suits</h3>
          </div>
          <div class="box-body relative">
			<div class="alert alert-info">
				<p>Please click row to view details.</p>
				<p>To <strong>Export Documents</strong>, be sure you have template(s) uploaded.</p>
			</div>
			<div id="table-w"></div>
			<div id="table-details-w" class="relative" style="display:none;"></div>
			<div class="clear"></div>
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
<div class="right-pane" id="right-panel" style="max-width:500px; display:none; overflow:scroll; z-index:3;">
	<div id="panel-options">
		<a href="javascript:panelEffect('#right-panel','hide');" id="panel-hide" title="Close panel">
		<div>
			<img src="./imgs/panel-close.png" />
		</div>
		</a>
	</div>
	<div style="max-width:494px;" id="right-panel-wrapper">
		<div id="right-panel-content"></div>
	</div>
</div>
<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
	function showAlert()
	{
		
		//$('#dlalert').addClass("animated");
		panelEffect('#dlalert','show');
		$('#dlalert').show();
	}
	function showAlertError(msg)
	{
		
		//$('#dlalert2').addClass("animated");
		//$('#dlalert2').addClass("slideInDown");
		panelEffect('#dlalert2','show');
		$('#dlalert2 #msg-span').html(msg);
		$('#dlalert2').show();
	}
	function loadTemps()
	{
		$('#settlement-template-wrapper').load('perf/loadSettlementAgreementDoc.php/'); 
	}
</script>
<?php include_once("footer.php"); 