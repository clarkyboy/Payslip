 <?php
	session_start(); 
	
	if( !isset($_SESSION['tm_law']['uid'],$_SESSION['tm_law']['name'],$_SESSION['tm_law']['ra_id']) ||
		(!$_SESSION['tm_law']['uid'] || !$_SESSION['tm_law']['name']))
	{
		header("Location: ./login.php?do=nosession");
	}
	else if( !$_SESSION['tm_law']['ra_id'])
	{
		header("Location: choose_lawfirm.php");
	}
	
	if(isset($_GET['do'])) $do = $_GET['do']; else $do = '';
	if(isset($_GET['i'])) $i = $_GET['i']; else $i = '';
	if(isset($_GET['p'])) $p = $_GET['p']; else $p = '';

	require_once("scripts/connect.php");
	//require_once("perf/perfDownloadCsv.php");perfGenZip
	//Get Product information
	$sql = "SELECT * FROM owner o left join product p on p.oid = o.oid	WHERE ra_id=? AND p.oid=? and p.pid = ?";
	$stmt = $tmconn->prepare($sql);
	$stmt->execute(array($_SESSION['tm_law']['ra_id'],$i,$p));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	
?>
<?php include_once("header.php"); ?>

<!-- DataTables -->
<style>
.smallContainer{
	margin-left: 3%;
	margin-right: 3%;
}
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
		var cTab ="";
		if(varstr==1)
			cTab = 'new';
		else if(varstr==2)
			cTab = 'open';
		else if(varstr == 3)
			cTab = 'open-a';
		else if (varstr==4)
			cTab = 'open-r';
		loadGrid(cTab,true,function(){
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
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
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
		$('#lbl-new-a').html( data.NewA );
		$('#lbl-new-r').html( data.NewR );
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
	else if(mode == 'open-a')
		currentTab = 3;
	else if(mode == 'open-r')
		currentTab = 4;
	else if(mode == 'recheck')
		currentTab = 5;

	//historyPush('manage.php?i='+getQueryStrVal('i')+'&p='+getQueryStrVal('p')+'&tab=' + (mode=='open'?2:1) + (typeof ld !== 'undefined'?(getQueryStr(['suit','panel'],'','&')):''));
	
	$('body').prepend('<div id="gm-loader" class="loader">loading..</div>');
	$('#table-w').html('');
	loadCounter();
	$.post("perf/loadManageGrid.php",
	{
		mode,
		p:'<?php echo $_GET['p'];?>',
		i:'<?php echo $_GET['i'];?>',
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
function firePerfGenZip(rows, does, p, i){//executes when extract data button is clicked
	var loc = window.location.pathname; // get the current location/directory
	var newLoc = loc.split("/"); // separate the location/directory by the character "/"
	var host = "https://securedservice.net/"+newLoc[1]+"/"+newLoc[2]+"/"+newLoc[3]+"/"; // set the current host of the system
	var ctr = 0;
	while(ctr<rows.length) //hides the selected rows for data extract to prevent multiple extraction of the same data/result
	{
		$("#"+rows[ctr]).toggle('slow');
		ctr++;
	}
	//generate zip file for download
	$.post("perf/perfGenZip.php",
			{
				rows,
				does,
				p,
				i,
			},
			function(data){
			var d = JSON.parse(data);
			loadCounter();
			var genId = d.lastGenId; // gets the last genId
			var authStr = d.authStr; // gets the authStr
			var email = d.email; //gets the email
			sendExportEmail(d.genId, d.authStr, 1, email, host);
			window.open("perf/perfDownloadGenZip.php?fileName="+d.file+"&ra_id="+d.ra_id); //open download page/tab
			})
			.error(function(data){
				console.log("Sonething's wrong with perfGenzip. Cannot process zipped file");
			});
}

function sendExportEmail(genId, authStr, tablemode, email, host) //sends email to the lawyer containing the download link of the zipped file
{
	$.post("emailAPI/",
			{
				genId,
				authStr,
				tablemode,
				email,
				host,
			},
			function(data){
			console.log("Email successfully Sent");
			})
			.error(function(data){
				console.log("Error in Sending Email for Exported data");
			});
}

/*Added July 11, 2017*/ //Readded September 14, 2017
function exportExcel(id){
var page='perf/perfSummaryExport.php?suit=';
	$.ajax({
	    url: page,
	    type: 'POST',
	    data:{suit:id},
	    success: function(data) {
	        // window.location = page+id;// you can use window.open also
	        //alert(data);
	        // window.open = (data,'_blank');
	        window.location = data;
	        // window.open(data);
	    }
	});
}
function acceptData(rid)// accepts data for filing suit. Changes ra_flag to 98
{
	$.post("perf/perfAcceptData.php",{rid},function(data){
			
			}).done(function(){
				panelEffect('#raflag-accept-success','show'); //show notification for successful data acceptance
				panelEffect('#raflag-changeStatus','hide'); //hides buttons to change ra_flag status of data to prevent data update
				$('#t3').click(); //make accepted data current tab

			}).fail(function(data){
				console.log("Error in ACCEPT DATA Process");
			});
}
function rejectData(rid)// rejects data for filing suit. Changes ra_flag to 99. Can't be extracted anymore
{
	$.post("perf/perfRejectData.php",{rid},function(data){
			}).done(function(){
				panelEffect('#raflag-reject-success','show');// show notification for successful data rejection
				panelEffect('#raflag-changeStatus','hide');//hide buttons to prevent changing of data ra_flag status
				$('#t4').click(); //make rejected data current tab

			}).error(function(data){
				console.log("Error in REJECT DATA Process");
			});
}
function viewSComments(rid, rcid){
	loadRSComment(rid, rcid); //loads Subcomments 
	panelEffect('#subComments-section-'+rcid, 'show');//shows subcomments section
	panelEffect('#showComments-'+rcid, 'hide');//hide "show comments" link
	panelEffect('#hideComments-'+rcid, 'show');//show "hide comments" link
	
}
function hideSComments(rcid){
	$('#subComments-section-'+rcid).hide(200); //hides subcomments section
	panelEffect('#showComments-'+rcid, 'show');//show "show comments" link
	panelEffect('#hideComments-'+rcid, 'hide');//hide "hide comments" link
}
function addRComment(rid)
{
	var cText = $('#comment-text').val(); //get comment text from text area
	if(cText!="") //only perform add comment if text area is not empty
	{
		$.post("perf/perfAddRComment.php",{rid,cText},function(data){
			
			}).done(function(){
				loadRComment(rid);//loads comments after adding a new comment to update comments list
			}).fail(function(data){
				alert('Add Comment Failed' );
				//do something when failed
		});

	}
	else //change text-area border color to red if the text area is empty and send button is clicked
	{
		$('#addComment').addClass(' has-error');
	}
	
}
function addRSComment(rid, rcid) //adds sub comments to the database
{
	var cText = $('#Scomment-text-'+rcid).val(); //get sub comment text from the text area
	if(cText!="")//only execute adding of comments if text area is not empty
	{
		$.post("perf/perfAddRComment.php",{rid,cText,rcid},function(data){
			
			}).done(function(){
				loadRSComment(rid,rcid); //loads sub comments below the parent comments
			}).fail(function(data){
				$('#comment-notiff').html("Can't add comment. Contact Administrator."); //set comment notification message
				panelEffect('#comment-notiff-wrapper','show'); //show comment notification
		});
	}
	else // change text area border to red to indicate error in text area content. Must not be empty
		$('#addSComment-'+rcid).addClass(' has-error');
}
function loadRSComment(rid, rcid) //loads sub comments 
{
	$.post("perf/loadRSComment.php",{rcid, rid},function(data){
			$('#subComments-section-'+rcid).html('Loading...');
			}).done(function(data){
				$('#subComments-section-'+rcid).html(data); //display sub comments under the main comments.
				setCommentCount(rcid);
			}).fail(function(data){
				panelEffect('#comment-notiff-wrapper','show'); //show comments notification
		});
}
function loadRComment(rid) //load main comments into the comments area
{
	$.post("perf/loadRComment.php",{rid},function(data){
			$('#commentBox-'+rid).html('Loading...');
			}).done(function(data){
				$('#commentBox-'+rid).html(data); //load comments/elements in the commentBox div
				$("#comments-section").animate({
				  scrollTop: $('#comments-section')[0].scrollHeight - $('#comments-section')[0].clientHeight
				}, 600); //animates scrolling to the bottom most comment
			}).fail(function(data){
				panelEffect('#comment-notiff-wrapper','show');// show comment notiffication
		});
}
function deleteRComment(rcid, rid, sub) //deletes parent comments in the comment area - triggered by clicking the x on the upper-right area of the comment
{
	$.post("perf/deleteRComment.php",{rcid},function(data){
			
			}).done(function(data){
				if(sub=="yes") //if it is a subcomment, hide Subcomments and display all parent comments
					hideSComments(rcid);
				else
					loadRComment(rid);
			}).fail(function(data){
				$('#comment-notiff').html("Can't delete comment. Contact Administrator."); //set comment notification message
				panelEffect('#comment-notiff-wrapper','show'); //show comment notification
		});
}
function deleteRSComment(rcid, rid, sub){ //deletes sub comments
	$.post("perf/deleteRComment.php",{rcid},function(data){
			
			}).done(function(data){
				loadRComment(rid); //loads parent comments
			}).fail(function(data){
				$('#comment-notiff').html("Can't delete comment. Contact Administrator.");
				panelEffect('#comment-notiff-wrapper','show');
		});
}
function confirmDelete(rcid,rid,sub) // confirms the deletion of comment
{
	panelEffect('#confirmDelete','show'); //shows delete comment confirmation area/div
	$('#deleteRID').val(rid); // set the rid of the selected comment to be deleted
	$('#deleteRCID').val(rcid); //set the rcid of the selected comment to be deleted
	$('#deleteSub').val(sub); // set the type of comment to be deleted - yes: sub comment ;  no: parent comment
}
function ConfirmDeleteYes() // triggered when YES is selected on the delete confirmation area/div/dialog
{
	var rid = $('#deleteRID').val(); //get the rid of the comment to be deleted
	var rcid = $('#deleteRCID').val(); // get the rcid of the comment to be deleted
	var sub = $('#deleteSub').val(); // get the type of comment to be deleted
	deleteRSComment(rcid, rid, sub); // deletes sub comment
				viewSComments(rid, rcid);
	hideConfirmDelete(); // hides the delete confirmation div/area/dialog
	
}
function hideConfirmDelete(){ // hides the delete confirmation div/area/dialog
	panelEffect('#confirmDelete','hide');
}
function setCommentCount(rcid) //sets the number of sub comments per parent comment
{
	$.post("perf/commentCounter.php",{rcid},function(data){
			
			}).done(function(data){
				var d = JSON.parse(data);
				$('#commentCounter-'+rcid).html(d.count) //set the number of sub comments in the View Comments link
			}).fail(function(data){
				console.log("Error in comments counter."); //log error in console
		});
}
function hideCommentNotiff(){ //hides comment notification area/div/dialog
	panelEffect('#comment-notiff-wrapper','hide');
}

function acceptDataForFiling(rows, p, i)
{
	var ctr = 0;
	while(ctr<rows.length) //hides the selected rows for data extract to prevent multiple extraction of the same data/result
	{
		$("#"+rows[ctr]).toggle('slow');
		ctr++;
	}
	//generate zip file for download
	$.post("perf/acceptMoreData.php",
			{
				rows,
				p,
				i,
			},
			function(data){
				console.log('Data acceptance Successful');
				$('#t3').click();
				panelEffect('#left-pane','hide');

			})
			.error(function(data){
				console.log("Error in accepting the data");
			});
}

</script>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
<?php include_once("navbar.php");?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="smallContainer">
      <!-- Content Header (Page header) -->
      <section class="content-header">
	  	<h1>
          <a target="_blank" href="./suit.php?p=<?php echo $p;?>" class="btn btn-primary btn-sm">View <?php echo $row['Product']; ?> Suits</a>
        </h1>
        <ol class="breadcrumb">
          <li><a href="./"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="#">Manage:  <strong><?php echo $_SESSION['tm_law']['ra_name'];?></strong> | (<?php echo $row['Owner']; ?>) <?php echo $row['Product']; ?></a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Manage: <strong><?php echo $_SESSION['tm_law']['ra_name'];?></strong> |  (<?php echo $row['Owner']; ?>) <?php echo $row['Product']; ?></h3> 
     	  </div>
          <div class="box-body">
			<div class="nav-tabs-custom">
				<input type="hidden" id="lastGridStatus" value="new" readonly>
				<ul id="manage-tabs" class="nav nav-tabs">
				  <li rel="new" class="active" onclick="javascript:loadGrid('new');"><a id="t1" href="#tab_1" data-toggle="tab" aria-expanded="true">New (Review)<span id="lbl-new-status" class="label label-warning">0</span></a></li>
				  <li rel="new-accepted" class="" onclick="javascript:loadGrid('new-a');"><a id="t3" href="#tab_3" data-toggle="tab" aria-expanded="false">New (Accepted) <span id="lbl-new-a" class="label label-success">0</span></a></li>
				  <li rel="new-rejected" class="" onclick="javascript:loadGrid('new-r');"><a id="t4" href="#tab_4" data-toggle="tab" aria-expanded="false">New (Rejected) <span id="lbl-new-r" class="label label-danger">0</span></a></li>
				  <li rel="open" class="" onclick="javascript:loadGrid('open');"><a id="t2" href="#tab_2"  data-toggle="tab" aria-expanded="false">Open <span id="lbl-open-status" class="label label-primary">0</span></a></li>
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
  <?php 
  ?>
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
<div hidden id="left-pane" style="width:20%; min-height:100px; background-color:white; position:absolute; padding:1%; top:600px; left:0px;">
	<blockquote style="font-size:15px; padding:5px;">Select Action for the data chosen</blockquote>
	<button class="btn btn-sm btn-primary hidden" style="width:98%;">Extract Data</button>
	<button onclick="acceptData(<?php echo $_GET['p'].','.$_GET['i'];?>)" class="btn btn-sm btn-primary" style="width:49%;">Accept Data</button>
	<button onclick="rejectData()" class="btn btn-sm btn-danger" style="width:49%;">Reject Data</button>
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
