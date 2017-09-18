<?php
	session_start(); session_write_close();
	if(isset($_GET['do'])) $do = $_GET['do']; else $do = '';
	if( !isset($_SESSION['tm_law']['uid'],$_SESSION['tm_law']['name'],$_SESSION['tm_law']['ra_id']) ||
		(!$_SESSION['tm_law']['uid'] || !$_SESSION['tm_law']['name'] || !$_SESSION['tm_law']['ra_id']))
	{
		header("Location: ./login.php?do=nosession");
	}
	
	require_once("scripts/connect.php");
	if(isset($_POST)){
		if($do == 'export' && $_POST['submit'] == 'Export'){
			if(isset($_POST['rid'])){
				$rids = implode(',' , $_POST['rid']);
				$sql = "SELECT p.*,o.*,r.*,v.*
						FROM result_dummy r
						INNER JOIN product p ON p.pid = r.pid
						INNER JOIN owner o ON o.oid = p.oid 
						LEFT JOIN vendor v ON v.vid = r.vid 
						WHERE 
							p.active = 1 AND
							r.ra_flag = 1 AND
							o.active = 1 AND 
							o.ra_id = ? AND 
							r.rid IN(".$rids.")";
							
				$stmt = $tmconn->prepare($sql);
				$stmt->execute(array($_SESSION['tm_law']['ra_id']));
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				if($rows){
					header("Content-Type: text/csv");
					header("Content-Disposition: attachment; filename=".$rows[0]['Owner']." Cases.csv");
					header("Cache-Control: no-cache, no-store, must-revalidate"); 
					header("Pragma: no-cache");
					header("Expires: 0");
					
					$data = array();
					foreach($rows AS $k=>$row){
						if($k==0){
							foreach($row AS $ik=>$i){
								$data[$k][] = $ik;
							}
						}
						$data[$k+1]=$row;
					}

					$output = fopen("php://output", "w");
					foreach ($data as $row) {
						fputcsv($output, $row);
					}
					fclose($output);
					
					$sql = "Update result_dummy SET date_filed=? WHERE ra_flag = 1 AND rid IN(".$rids.")";
					$stmt = $tmconn->prepare($sql);
					$stmt->execute(array(date('Y-m-d')));
				}
				exit;
			}
		}
	}
?>
<?php include_once("header.php"); ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>



<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body id="" class="case.new.view hold-transition skin-blue layout-top-nav">
<div class="wrapper">

  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <span class="navbar-brand"><b>Trademark</b>| Management</span>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
          </ul>
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <!-- User Account Menu -->
            <li class="dropdown user user-menu" style="display:none">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="../../dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs">Alexander Pierce</span>
              </a>
              <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header">
                  <img src="../../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                  <p>
                    Alexander Pierce - Web Developer
                    <small>Member since Nov. 2012</small>
                  </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                  <div class="row">
                    <div class="col-xs-4 text-center">
                      <a href="#">Followers</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Sales</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Friends</a>
                    </div>
                  </div>
                  <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                  </div>
                  <div class="pull-right">
                    <a href="#" class="btn btn-default btn-flat">Sign out</a>
                  </div>
                </li>
              </ul>
            </li>
			<li>
				<a href="login.php?do=logout">Sign out</a>
			</li>
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container">
	<?php
		$sql = "SELECT *
				FROM result_dummy r
				INNER JOIN product p ON p.pid = r.pid
				INNER JOIN owner o ON o.oid = p.oid 
				LEFT JOIN vendor v ON v.vid = r.vid 
				WHERE 
					p.active = 1 AND
					r.ra_flag = 1 AND
					o.active = 1 AND 
					o.oid = ? AND
					o.ra_id = ?";
				
		$stmt = $tmconn->prepare($sql);
		$stmt->execute(array(isset($_GET,$_GET['i'])? $_GET['i'] : 0,$_SESSION['tm_law']['ra_id']));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$data = array('Owner'=>'','oid'=>0);
		if($rows){
			$data['Owner'] = $rows[0]['Owner'];
			$data['oid'] = $rows[0]['oid'];
			foreach($rows as $row){
				$data[$row['pid']] = isset($data[$row['pid']])? $data[$row['pid']]: array(); 
				$data[$row['pid']][$row['rid']] = $row ;
				$data[$row['pid']]['Product'] = $row['Product'];
			}
		}
	?>
      <!-- Content Header (Page header) -->
      <section class="content-header">
	  	<h1>
          <a target="_blank" href="./suit.php" class="btn btn-primary btn-sm">View Suit</a>
        </h1>
        <ol class="breadcrumb">
          <li><a href="./"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="Case.php">Case</a></li>
		  <li><a href="#">Case: New - <?php echo $data['Owner']; ?></a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title"><label style="width:100px;font-weight:normal;">Case State</label>: New </h3><br/>
			<h3 class="box-title"><label style="width:100px;font-weight:normal;">Owner</label>: <?php echo $data['Owner']; ?></h3>
     	  </div>
          <div class="box-body">
			<div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-info"></i> Click product name to toggle cases result.
            </div>
			<form action="Case.new.view.php?do=export&i=<?php echo isset($_GET,$_GET['i'])? $_GET['i'] : 0; ?>" method="POST">
				<div class="form-action-btn" style="margin-bottom:-33px;">
					<input type="submit" name="submit" value="Export" class="btn btn-primary"/>
				</div>
				<div>
				<table id="table1" class="table table-bordered">
					<thead>
					<tr>
						<th style="width:10px;padding-right:0px;"><input onClick="jQuery('.chkallchldbox,.chkchldbox').removeAttr('checked');if(jQuery(this).is(':checked'))jQuery('.chkallchldbox').click();" type="checkbox" class="chkallbox"></input></th>
						<th>Product</th>
					</tr>
					</thead>
					<tbody>
				<?php
					$ctr = 1;
					foreach($data as $di=>$d)
					{
						if(!is_integer($di) || $di=='oid')continue;
						echo "<tr class='parent-row'>";
						echo '<td><input type="checkbox" class="chkbox chkallchldbox" onClick="jQuery(\'.chkchldbox\').removeAttr(\'checked\');if(jQuery(this).is(\':checked\'))jQuery(\'.chkchldbox\').click();"></input></td>';
						echo '<td><a href="#" onClick="jQuery(this).parent().children(\'.tdchld\').toggle();return false;">'.$d['Product'].'</a><div style="display:none;max-width:1038px;" class="tdchld">';
						echo '<div class="table-responsive"><table class="table table-bordered table-striped" style="widthx:2500px;">';
						echo '<thead>
								<tr>
									<th></th>
									<th style="white-space:nowrap;">Vendor</th>
									<th class="hidden-xs" style="white-space:nowrap;">Url</th>
									<th style="white-space:nowrap;">Item Name</th>
									<th class="hidden-xs" style="white-space:nowrap;">Location</th>
									<th class="hidden-xs" style="white-space:nowrap;">Vendor Contact</th>
								</tr>
							  </thead>';
						echo '<tbody>';
						foreach($d AS $k=>$i){
							if(!is_integer($k))continue;
							echo '<tr>';
							echo '<td><input type="checkbox" name="rid[]|" class="chkbox chkchldbox" value="'.$i['rid'].'"></td>';
							echo '<td style="white-space:nowrap;">'.$i['vendor_name'].'</td>';
							echo '<td class="hidden-xs" style="white-space:nowrap;">'.$i['url'].'</td>';
							echo '<td style="white-space:nowrap;">'.$i['itemname'].'</td>';
							echo '<td class="hidden-xs" style="white-space:nowrap;">'.$i['location'].'</td>';
							echo '<td class="hidden-xs" style="white-space:nowrap;">'.$i['vendor_contact'].'</td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table></div>';
						echo "</div></td>";
						echo "</tr>";
						$ctr++;
					}
				?>	
					</tbody>
				</table>
				</div>
				<div class="form-action-btn">
					<input type="submit" name="submit" value="Export" class="btn btn-primary" style="margin-top: -70px;"/>
				</div>
			</form>
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
<script>
  $(function () {
    $('#table1').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": false,
      "autoWidth": false
    });
  });
</script>
  
<?php include_once("footer.php"); 