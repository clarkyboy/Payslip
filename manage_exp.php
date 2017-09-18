 <?php
	session_start(); session_write_close();
	
	if( !isset($_SESSION['tm_law']['uid'],$_SESSION['tm_law']['name'],$_SESSION['tm_law']['ra_id']) ||
		(!$_SESSION['tm_law']['uid'] || !$_SESSION['tm_law']['name'] || !$_SESSION['tm_law']['ra_id']))
	{
		header("Location: ./login.php?do=nosession");
	}
	
	if(isset($_GET['do'])) $do = $_GET['do']; else $do = '';
	if(isset($_GET['i'])) $i = $_GET['i']; else $i = '';
	if(isset($_GET['p'])) $p = $_GET['p']; else $p = '';

	require_once("scripts/connect.php");

	
	
	//require_once("perf/perfDownloadCsv.php");perfGenZip
	//require_once("perf/perfGenZip.php");

	
	//Get Product information
	$sql = "SELECT * FROM owner o left join product p on p.oid = o.oid	WHERE ra_id=? AND p.oid=? and p.pid = ?";
	$stmt = $tmconn->prepare($sql);
	$stmt->execute(array($_SESSION['tm_law']['ra_id'],$i,$p));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
  
	
	
	
?>
<?php include_once("header.php"); ?>

<!-- DataTables -->
<style>
.selected-tr{background:#e9f4fb;}

/*  */

#preview{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:1px;
	display:none;
	color:#fff;
	width:304px;
	}

/*  */

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
</style>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
var $ = jQuery;
var varstr = '';
$(document).ready(function(){
	varstr = getQueryStrVal('tab');
	if( varstr ){
		$('#manage-tabs > li').removeClass('active');
		$('a[href="#tab_'+varstr+'"]').parent().addClass('active');
		loadGrid((varstr==2?'open':'new'),true,function(){
			if(varstr==2){
				varstr = getQueryStrVal('suit');
				if(varstr){
					loadSuitsDetails(varstr,true,function(){
						varstr = getQueryStrVal('panel');
						$('tr[rel="trpanel-'+varstr+'"]').click();
					});
				}
			}
			else{
				historyPush('manage.php?i='+getQueryStrVal('i')+'&tab=' + varstr);
			}
		});
	}
	else{
		loadGrid('new',true);
	}
	if($('#text').length) textAreainit();
	
	//2017-01-09
	getFilterURLList();
});

function myKeyPress(e){
	if(e.keyCode == 13)
	{
		loadGrid();
	}
}

function rowClicked(opVal){
opVal = typeof opVal !== 'undefined' ? opVal : '';

	if((document.getElementById('right-panel').style.display == "" || document.getElementById('right-panel').style.display == "block") && currentViewedID == opVal)
	{
		panelEffect('#right-panel','hide');
	}
	else
	{
		//buildCaseViewHistoryList(opVal);
		try
		{
			panelLoad(opVal);
		}
		catch(err)
		{
			//panelLoad(opVal);
		}
	}
}

function panelLoad(id){
	currentViewedID = id;
	
	panelEffect('#right-panel','hide');
	document.getElementById('right-panel-content').innerHTML = "<br><br><br><br><center><img src='./imgs/loader-big.gif'/></center>";
	//Show panel
	panelEffect('#right-panel','show');
	
	$.post("perf/loadSidePanelInfoRA.php",
	{
		id: id,
	},
	function(data,status){
		var params = data.split("⁞");
			
		if(params[0] == currentViewedID){
			//data contains the response
			$( "#right-panel-content" ).remove();
			document.getElementById('right-panel-wrapper').innerHTML = '<div id="right-panel-content"></div>';
			
			document.getElementById('right-panel-content').innerHTML = params[1];
		} 
		
		getSidePanelAttachments(params[0]);
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

function submitToPopup() {
	$('#frmManage-dummy .selected-tr').each(function(i){
		$(this).find('.hrid').attr('checked',true);
	});
	setTimeout(function(){
		$('.loader').remove();
		$('#frmManage-dummy .submit').click();
		loadGrid( $('#manage-tabs').children('li.active').attr('rel') );
	},500);
}
function submitFrmManager(i,p){
	$("body").append('<form target="" method="post" id="frmManage-dummy" class="hide" action="manage.php?do=export&i='+i+'&p='+p+'">'+$('#frmManage').html()+'</form>');
	$('body').prepend('<div id="gm-loader" class="loader">loading..</div>');
	setTimeout(function(){submitToPopup(i);},500);
}

function loadCounter(callback){
	$.post("perf/loadNewOpenCounter.php",{
		//i:getQueryStrVal('i'),
		i:'<?php echo $i; ?>',
		p:'<?php echo $p; ?>',
	},function(data,status){
		data = JSON.parse(data);
		$('#lbl-new-status').html( data.New );
		$('#lbl-open-status').html( data.Open );
		if (typeof callback === "function") {
			callback();
		}
	});
}

function getSidePanelAttachments(i){
i = typeof i !== 'undefined' ? i : '';
if(i == '') return;
	document.getElementById('SidePanelAttachments').innerHTML = "<center><img src='./imgs/loader-bars.gif'/></center>";
	$.post("perf/loadSidePanelAttachments.php",
	{
		i,
	},
	function(data,status){
		params = data.split("⁞");
		
		document.getElementById('SidePanelAttachmentsCount').innerHTML = params[0];
		document.getElementById('SidePanelAttachments').innerHTML = params[1];
		
	}).fail(function(response) {
		alert("Error: Could not retrieve attachments")
	});
}

function loadGrid(mode,ld,callback){
//mode = typeof mode !== 'undefined' ? mode : 'new';
try{
mode = typeof mode !== 'undefined' ? mode : document.getElementById('lastGridStatus').value;
}catch(err){
mode = 'new';
}

	document.getElementById('lastGridStatus').value = mode;


	rid = document.getElementById('filterRID').value;
	
	try{
	filterURL = document.getElementById('filterURL').value;
	}
	catch(err){
		filterURL = '0';
	}
	
	filterOrderCol = document.getElementById('filterOrderCol').value;
	filterOrderBy = document.getElementById('filterOrderBy').value;
	
	if(mode == 'new')
		currentTab = 1;
	else if(mode == 'open')
		currentTab = 2;
	else if(mode == 'recheck')
		currentTab = 3;

	//historyPush('manage.php?i='+getQueryStrVal('i')+'&p='+getQueryStrVal('p')+'&tab=' + (mode=='open'?2:1) + (typeof ld !== 'undefined'?(getQueryStr(['suit','panel'],'','&')):''));
	
	$('body').prepend('<div id="gm-loader" class="loader">loading..</div>');
	$('#table-w').html('');
	loadCounter();
	$.post("perf/loadManageGrid.php",
	{
		mode,
		p:'<?php echo $_GET['p'];?>',
		i:'<?php echo $_GET['i'];?>',
		raid:'<?php echo json_encode($_POST['rid']);?>',
		currentTab,
		rid,
		filterURL,
		filterOrderCol,
		filterOrderBy,
	},
	function(data,status){
		$('#table-w').html(data);
		if (typeof callback === "function") {
			callback();
		}
		imagePreview();
		$('.loader').remove();
	}).fail(function(response) {
		$('#table-w').html("Error 500: No response coming.");
		$('.loader').remove();
	});
}

function doPerfGenZip()
		 {
			 
					 var raids = $('input:checked').map(function(){
				      return $(this).val();
				    }).toArray();
					 var does = "export";
					 var i = <?php echo $i;?>;
					 var p = <?php echo $p;?>;
					console.log(raids);
					$.ajax({
				     url: 'perf/perfGenZip.php?do='+does+'&i='+i+'&p='+p, //This is the current doc
				     type: "POST",
				     dataType:'json', // add json datatype to get json
				     data: {
				     	'rid': raids,
				     },
				     success: function(data){
						     
						     	alert("success");
						     
				     	},
				     error: function(data)
						     {
						     	alert("Error");
						     	alert("do: "+data.do);
						     	alert("i: "+data.i);
						     	alert("p: "+data.p);
						     }
					}); 
				
		}

function getFilterURLList(){
	$.post("perf/loadURLFilterList.php",
	{
		p:'<?php echo $p;?>',
	},
	function(data,status){
		document.getElementById('filterURLArea').innerHTML = data;
	});
}

function loadSuitsDetails(suit,ld,callback){
	$('body').prepend('<div id="gm-loader" class="loader">loading..</div>');

	historyPush('manage.php?i='+getQueryStrVal('i')+'&p='+getQueryStrVal('p')+'&tab=' + getQueryStrVal('tab') + '&suit=' + suit + (typeof ld !== 'undefined'?(getQueryStr(['panel'],'','&')):''));
	
	$.post("perf/loadSuits.php",
	{
		suit:suit,
		p:'<?php echo $p;?>',
	},function(data,status){
		$('.loader').remove();
		$('body').append('<div class="mdwrapper">' + data + '</div>');
		$('#suit-list-wrapper').modal('show');
		$('#suit-list-wrapper').on('shown.bs.modal',function(e){
			$('#suit-list-wrapper .modal-body').height(  $(window).height() - 206 );
		});
		$('#suit-list-wrapper').on('hidden.bs.modal', function (e) {
			$('.mdwrapper').remove();
			loadGrid('open');
		});
		if (typeof callback === "function") {
			callback();
		}
	}).fail(function(response){
		$('.loader').remove();
		alert('Error 500: No response coming.');
	});
}

function opensidepanel( panelwrapper, _el, rid ){
	if( $(panelwrapper).children('.sidepanel').length ) $(panelwrapper).children('.sidepanel').remove();
	
	historyPush('manage.php?i='+getQueryStrVal('i')+'&p='+getQueryStrVal('p')+'&tab=' + getQueryStrVal('tab') + '&suit=' + getQueryStrVal('suit') + '&panel=' + rid);
	
	$(panelwrapper).append('<div class="sidepanel" ><a class="close-btn"><img src="imgs/panel-close.png"></a><div class="sp-content"><div class="loader">loading..</div></div></div>');
	$(panelwrapper).children('.sidepanel').css({right: ($(panelwrapper).children('.sidepanel').width()+2) * -1});
	
	var _rid = rid;
	var _panelw = panelwrapper;
	openpanel( $(panelwrapper).children('.sidepanel'),function(){
		$.post('perf/loadSidePanel.php',
		{
			rid:_rid
		},function(_res){
			$(_panelw).children('.sidepanel').children('.sp-content').html(_res);
		}).fail(function(response){
			alert('Server encounter an error.');
			$(_panelw).children('.sidepanel').children('.close-btn').click();
		});
		
		$(_panelw).children('.sidepanel').children('.close-btn').click(function(e){
			closepanel( $(_panelw).children('.sidepanel'), function(e){
				$(_panelw).children('.sidepanel').remove();
				historyPush('manage.php?i='+getQueryStrVal('i')+'&tab=' + getQueryStrVal('tab') + '&suit=' + getQueryStrVal('suit'));
			});
		});
	});
}

function addAmount(el,loader){
	var msg = '';
	if(!$(el).find('input[name="rid"]').val()){msg += 'Result ID is required.<br/>';}
	if(!$(el).find('input[name="amount"]').val()){msg += 'Amount is required.';}
	
	$(loader).prepend('<span class="bar-loader"></span>');
	if(msg){
		msg = '<div class="alert alert-danger">' + msg + '</div>';
		$(el).find('.message:eq(0)').html(msg);
		$(el).closest('.loader-wrapper').find('.bar-loader').remove();
	}
	else{
		$.post('perf/perfAddAmount.php',$(el).serialize(),function(_res){
			$(loader).find('.bar-loader').remove();
			$(el).find('.message:eq(0)').html('<div class="alert alert-success">Amount successfully updated</div>');
			$('tr[rel="trpanel-'+ $(el).find('input[name="rid"]').val() + '"]').children('td.amt').html( parseFloat($(el).find('input[name="amount"]').val()).toFixed(2) );
			loadResult( $(el).find('input[name="rid"]').val() );
		}).fail(function(){
			$(loader).find('.bar-loader').remove();
			alert('Server encounter an error.');
		});
	}
	return false;
}

function addDategranted(el,loader){
	var msg = '';
	if(!$(el).find('input[name="rid"]').val()){msg += 'Result ID is required.<br/>';}
	if(!$(el).find('input[name="dategranted"]').val()){msg += 'Date is required.';}
	
	$(loader).prepend('<span class="bar-loader"></span>');
	if(msg){
		msg = '<div class="alert alert-danger">' + msg + '</div>';
		$(el).find('.message:eq(0)').html(msg);
		$(el).closest('.loader-wrapper').find('.bar-loader').remove();
	}
	else{
		$.post('perf/perfAddDateGranted.php',$(el).serialize(),function(_res){
			$(loader).find('.bar-loader').remove();
			$(el).find('.message:eq(0)').html('<div class="alert alert-success">Date successfully updated</div>');
			$('tr[rel="trpanel-'+ $(el).find('input[name="rid"]').val() + '"]').children('td.dgranted').html( $(el).find('input[name="dategranted"]').val() );
			loadResult( $(el).find('input[name="rid"]').val() );
		}).fail(function(){
			$(loader).find('.bar-loader').remove();
			alert('Server encounter an error.');
		});
	}
	return false;
}
function addComment(el){
	var msg = '';
	if(!$(el).find('input[name="rid"]').val()){msg += 'Result ID is required.<br/>';}
	if(!$(el).find('input[name="uid"]').val()){msg += 'User ID is required.<br/>';}
	if(!$(el).find('textarea[name="comment"]').val()){msg += 'Comment is required.';}
	
	$(el).find('.loader-wrapper').prepend('<span class="bar-loader"></span>');
	if(msg){
		showMessage(el,'Error',msg);
	}
	else{
		$.post('perf/perfAddComment.php',$(el).serialize(),function(_res){
			$(el).find('.bar-loader').remove();
			loadComment($(el).find('input[name="rid"]').val());
		}).fail(function(){
			$(el).find('.bar-loader').remove();
			alert('Server encounter an error.');
		});
	}
	return false;
}

function loadResult(rid){
	$('#result-wrapper').html('<span class="bar-loader"></span>');
	$.post('perf/loadResult.php',{rid,action:'load'},function(_res){
		$('#result-wrapper').html(_res);
		$('#result-wrapper').find('.bar-loader').remove();
	}).fail(function(){
		$('#result-wrapper').find('.bar-loader').remove();
		alert('Server encounter an error.');
	});
}
function loadComment(rid){
	$('#comment-wrapper').html('<span class="bar-loader"></span>');
	$.post('perf/loadComment.php',{rid},function(_res){
		$('#comment-wrapper').html(_res);
		$('#comment-wrapper').find('.bar-loader').remove();
	}).fail(function(){
		$('#comment-wrapper').find('.bar-loader').remove();
		alert('Server encounter an error.');
	});
}

function updateAmount(el,val,rid){
	$(el).parent('.loader-wrapper').append('<span class="bar-loader"></span>');
	$.post('perf/loadFrmAmount.php',{rid,val},function(_res){
		$('body').append(_res);
		$(el).parent('.loader-wrapper').find('.bar-loader').remove();
		
		$('#popupmessage').modal('show');
		$('#popupmessage').on('hidden.bs.modal', function (e) {
			$('#pmsg-wrapper').remove();
		});
	}).fail(function(){
		$(el).parent('.loader-wrapper').find('.bar-loader').remove();
		alert('Server encounter an error.');
	});
}

function updateDate(el,val,rid){
	$(el).parent('.loader-wrapper').append('<span class="bar-loader"></span>');
	$.post('perf/loadFrmDateGranted.php',{rid,val},function(_res){
		$('body').append(_res);
		$(el).parent('.loader-wrapper').find('.bar-loader').remove();
		
		$('#popupmessage').modal('show');
		$('#popupmessage').on('shown.bs.modal',function(e){
			$('#frmdategranted .datepicker').datepicker({format:'yyyy-mm-dd'});
		});
		$('#popupmessage').on('hidden.bs.modal', function (e) {
			$('#pmsg-wrapper').remove();
		});
	}).fail(function(){
		$(el).parent('.loader-wrapper').find('.bar-loader').remove();
		alert('Server encounter an error.');
	});
}
</script>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
<?php include_once("navbar.php");?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container">
      <!-- Content Header (Page header) -->
      <section class="content-header">
	  	<h1>
          <a target="_blank" href="./suit.php?p=<?php echo $p;?>" class="btn btn-primary btn-sm">View <?php echo $row['Product']; ?> Suits</a>
        </h1>
        <ol class="breadcrumb">
          <li><a href="./"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="#">Manage: (<?php echo $row['Owner']; ?>) <?php echo $row['Product']; ?></a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Manage: (<?php echo $row['Owner']; ?>) <?php echo $row['Product']; ?></h3> 
     	  </div>
          <div class="box-body">
			<div class="nav-tabs-custom">
				<input type="hidden" id="lastGridStatus" value="new" readonly>
				<ul id="manage-tabs" class="nav nav-tabs">
				  <li rel="new" class="active" onclick="javascript:loadGrid('new');"><a href="#tab_1" data-toggle="tab" aria-expanded="true">New <span id="lbl-new-status" class="label label-success">0</span></a></li>
				  <li rel="open" class="" onclick="javascript:loadGrid('open');"><a href="#tab_2"  data-toggle="tab" aria-expanded="false">Open <span id="lbl-open-status" class="label label-success">0</span></a></li>
				</ul>
				<div class="tab-content">
<input type="text" id ="filterRID" size="5" onkeypress="myKeyPress(event);" placeholder="rid"></input>
 <span id="filterURLArea"></span> 
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;order by:
<select id="filterOrderCol">
	<option value="r.rid">rid</option>
	<option value="itemnumber">Product ID</option>
	<option value="url">URL</option>
	<option value="itempricetext">Price</option>
</select>
<select id="filterOrderBy">
	<option value="desc">Descending</option>
	<option value="asc">Ascending</option>
</select>				
					<div id="table-w">
							
					</div>
				</div>
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
<!-- Right Panel -->
<!-- max-width:489px; max-width:483px;-->
<div class="right-pane" id="right-panel" style="max-width:500px; min-width: 500px; display:none; overflow:scroll; z-index:3;">
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
<!-- End of Right Panel -->    
<script>
  // $(function () {
    // $('#table1').DataTable({
      // "paging": true,
      // "lengthChange": false,
      // "searching": true,
      // "ordering": false,
      // "info": true,
      // "autoWidth": false
    // });
  // });
</script>
  
<?php include_once("footer.php");
