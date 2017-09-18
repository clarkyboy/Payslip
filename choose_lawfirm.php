<?php
	session_start(); session_write_close();
	if( !isset($_SESSION['tm_law']['access']) )
	{
		header("Location: ./login.php?do=nosession");
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
.active
{
	background-color: #99ecff;
}
</style>
<script type="text/javascript">

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
		<div >
			<?php
				if(isset($_SESSION['tm_law']['ra_id']))
				{
					$sqlLF = "select ra_name from ra inner join ra_access on ra.ra_id = ra_access.ra_id where rauser_id = ?";
					$stmt = $tmconn->prepare($sqlLF);
					$stmt->execute(array($_SESSION['tm_law']['uid']));
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$lawfirm = $rows[0]['ra_name'];
				}
				else
				{
					$lawfirm = "No Lawfirm Selected Yet";
				}
			?>
			
		</div>
      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">LAWFIRM SELECTION (<strong title="Current Lawfirm"> <?php echo $lawfirm;?> </strong>)</h3>
          </div>
          <div class="box-body">
			<div id="btnapp-wrapper">
				<div class="row">
					<div class="col-xs-12">

						<div class="info-box">
							<div class="info-box-content" style="margin-left:0px;">
								<span class="info-box-text">Below are the lawfirms you have access to.</span>
								<span class="">
									To start case management, click on any Lawfirm and it will redirect you to the Products selection page.
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
								$stmt=null;
								$sql = "select ra.ra_name, ra_access.* from ra inner join ra_access on ra.ra_id = ra_access.ra_id where rauser_id = ?";
								$stmt = $tmconn->prepare($sql);
								$stmt->execute(array($_SESSION['tm_law']['uid']));
								$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
							?>
							<?php if($rows): ?>
							<div class="row lf-list text-center " id="lf-list" style="margin-left:6%;">
								<?php foreach($rows AS $k=>$i): ?>
									<a  href="setLawfirm.php?ra_id=<?php echo $i['ra_id'];?>&lf=<?php echo $i['ra_name'];?>">
									<div class="btn btn-app animated products <?php echo ($_SESSION['tm_law']['ra_id']==$i['ra_id'])?'active':'';?>" style="width:16%; height:150px; margin-bottom: 2%;margin-right: 2%; position:relative;">
										<i  style="font-size:40px;" class="fa fa-balance-scale"></i> <span style=" font-weight:bold;font-size:18px;;white-space:normal; text-align:center !important;" > (<?php echo $i['ra_name'];?>) </span><div style="width:100%;white-space:normal;" ></div>
										<div style="display:block; background-color:#086faa; color:white; font-size:15px; padding-top:5%; min-height:50px; width:105%; margin-left:-5%; position:absolute; bottom:0px;">
											Choose Lawfirm
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