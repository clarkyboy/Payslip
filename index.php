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
	include_once("header.php");
?>
<style>
.wordwrap { 
   white-space: pre-wrap;      /* CSS3 */   
   white-space: -moz-pre-wrap; /* Firefox */    
   white-space: -pre-wrap;     /* Opera <7 */   
   white-space: -o-pre-wrap;   /* Opera 7 */    
   word-wrap: break-word;      /* IE */
}
.smallContainer{
	margin-left: 3%;
	margin-right: 3%;
}
div#btnapp-mw {
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
</style>
<script type="text/javascript">
(function($){
	$(document).ready(function(){
		$('div#lf-list > a').each(function(index){
			$.post("perf/loadNewOpenCounter.php",{
			i:$(this).attr('rel'),
			p:$(this).attr('rel2'),
			},function(data,status){
				data = JSON.parse(data);

				$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-loading').hide();
				$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-new').html( 'For Review: ' + data.New + ' | ');
				$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-new-a').html( 'Accepted: ' + data.NewA );
				$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-new-r').html( 'Rejected: ' + data.NewR + ' | ');
				$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-open').html( 'Open: ' + data.Open );
				if(data.New >0)
				{
					$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-new').css({"color":"#ffc61e", "font-weight":"bold","font-size":"14px"});
				}
				if(data.NewA >0)
				{
					$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-new-a').css({"color":"#1cff54", "font-weight":"bold","font-size":"14px"});
				}
				if(data.NewR >0)
				{
					$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-new-r').css({"color":"#f73d3d", "font-weight":"bold","font-size":"14px"});
				}
				if(data.Open >0)
				{
					$('div#lf-list > a > div.btn-app > div.bt > div.ctr-new-wrapper').eq(index).children('.ctr-open').css({"color":"#ffffff", "font-weight":"bold","font-size":"14px"});
				}
			});
		});
	});
})(jQuery);

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
           &nbsp;<?php /*<a target="_blank" href="./suit.php" class="btn btn-primary btn-sm">View Suit</a>*/ ?>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Manage ( <strong><?php echo $_SESSION['tm_law']['ra_name'];?></strong> )</h3>
          </div>
          <div class="box-body">
			<div id="btnapp-wrapper">
				<div class="row">
					<div class="col-xs-12">
						<div class="info-box">
							<div class="info-box-content" style="margin-left:0px;">
								<span class="info-box-text">Welcome to the Trademark Management Panel</span>
								<span class="">
									To start case management, click on any Owner you have access.
								</span>
							</div>
						</div>
						<div class="lf-box">
							<?php
								//$sql = "SELECT * FROM owner WHERE ra_id = ?";
								//$sql = "SELECT * FROM owner o left join product p on p.oid = o.oid WHERE ra_id = ? and o.active = 1";
								//use the query below to sort campaigns with the most new results. Note: Slow Processing
								/*$sql = "SELECT *, 
										(SELECT count(*)
									        FROM product p
									        INNER JOIN result r ON p.pid = r.pid
									        WHERE p.pid = r.pid
											AND (r.ra_flag = 1 OR r.ra_flag = 0 OR r.ra_flag = Null)
											AND r.date_filed is null
											AND r.qflag = 3) AS New 
										FROM owner o 
										left join product p on p.oid = o.oid 
										WHERE ra_id = ? and o.active = 1 AND p.active = 1
										ORDER BY New DESC";
								*/
								//query for products sorted by product name. Note: Faster Processing
								$sql = "SELECT *
								FROM owner o 
								left join product p on p.oid = o.oid 
								WHERE ra_id = ? and o.active = 1 AND p.active = 1
								ORDER BY o.Owner ASC";
								$stmt = $tmconn->prepare($sql);
								$stmt->execute(array($_SESSION['tm_law']['ra_id']));
								$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
							?>
							<?php if($rows): ?>
							<strong style="margin-left: 10px;margin-bottom: 10px;display: block;">Products:</strong>
							<div class="row lf-list text-center " id="lf-list" style="margin-left:6%;">
								<?php foreach($rows AS $k=>$i): ?>
									<a rel="<?php echo $i['oid']; ?>" rel2="<?php echo $i['pid']; ?>" href="manage.php?i=<?php echo $i['oid'];?>&p=<?php echo $i['pid'];?>" title="">
										<div class="btn btn-app animated products" style="width:16%; height:150px; margin-bottom: 2%;margin-right: 2%; position:relative;">
										<i class="fa fa-opencart"></i> <span style=" font-weight:bold;width:100%;white-space:normal; text-align:center !important;" > (<?php echo $i['Owner']; ?>) </span><div style="width:100%;white-space:normal;" ><?php echo $i['Product']; ?></div>
										<div class="bt" style="margin-left:-10px; width:105%; position:absolute; bottom:0;">
											<div class="ctr-new-wrapper" style="display:block; padding:3%; background: #3c8dbc;color: #fff; min-height:50px;" title="New Result for Review"> 
												<span class="ctr-loading" style="color: #fff;" title="New Data"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></span>  
												<span class="ctr-new" style="color: #fff;" title="New Data"></span>  
												<span class="ctr-new-a" style="color: #fff;" title="Accepted Data : Ready for filing"></span> <br />
												<span class="ctr-new-r" style="color: #fff;" title="Rejected Data: Not for Filing"> </span>  
												<span class="ctr-open" style="color: #fff;" title="open"></span>
											</div>
											
										</div>
										
										</div>
									</a>
								<?php endforeach;?>
							</div>
							<?php else: ?>
							<div class="alert alert-warning">No record yet</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
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
<script type="text/javascript">
	
$('.products').hover(function(){
  			$(this).addClass("pulse");
  		}, function(){
  			$(this).removeClass("pulse");
  		});

</script>
  <!-- /.content-wrapper -->
<?php include_once("footer.php");