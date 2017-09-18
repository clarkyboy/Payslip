<?php
	session_start(); session_write_close();
	if( !isset($_SESSION['tm_law']['uid'],$_SESSION['tm_law']['name'],$_SESSION['tm_law']['ra_id']) ||
		(!$_SESSION['tm_law']['uid'] || !$_SESSION['tm_law']['name'] || !$_SESSION['tm_law']['ra_id']))
	{
		header("Location: ./login.php?do=nosession");
	}

	require_once("scripts/connect.php");
?>
<?php include_once("header.php"); ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>



<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
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
      <!-- Content Header (Page header) -->
      <section class="content-header">
	  	<h1>
          <a target="_blank" href="./suit.php" class="btn btn-primary btn-sm">View Suit</a>
        </h1>
        <ol class="breadcrumb">
          <li><a href="./"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="#">Case Management</a></li>
		</ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Case Management</h3> 
     	  </div>
          <div class="box-body">
			<table id="table1" class="table table-bordered table-hover">
				<thead>
				<tr>
					<th>#</th>
					<th>Owner</th>
					<th>New</th>
					<th>Open</th>
				</tr>
				</thead>
				<tbody>
			<?php
				//$sql = "SELECT * FROM owner o left join ra on ra.ra_id = o.ra_id;";
				$sql = "SELECT
							oid,
							Owner, 
							(SELECT count(*) FROM product p INNER JOIN result_dummy r ON p.pid = r.pid WHERE p.oid = o.oid AND r.ra_flag = 1 AND r.qflag=3) AS New,
							(SELECT count(*) FROM product p INNER JOIN result_dummy r ON p.pid = r.pid WHERE p.oid = o.oid AND r.ra_flag = 2) AS Open
						FROM owner o 
						WHERE o.ra_id=?";
						
				$stmt = $tmconn->prepare($sql);
				$stmt->execute(array($_SESSION['tm_law']['ra_id']));
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				if(count($rows) > 0)
				{
					$ctr = 1;
					foreach($rows as $row)
					{
						echo "<tr>";
						echo "<td>$ctr</td>";
						echo "<td>$row[Owner]</td>";
						echo "<td><a href='Case.new.view.php?i=$row[oid]'>$row[New]</a></td>";
						echo  "<td><a href='Case.open.view.php?i=$row[oid]'>$row[Open]</a></td>";
						echo "</tr>";
						$ctr++;
					}
				}
				else
				{
					// echo "<tr>";
					// echo "<td></td>";
					// echo "<td colspan=2>No Lawfirms found</td>";
					// echo "</tr>";
				}
				
			?>	
				</tbody>
			</table>
			
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
      "info": true,
      "autoWidth": false
    });
  });
</script>
  
<?php include_once("footer.php");