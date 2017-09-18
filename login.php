<?php
session_start();

if(isset($_GET['do'])) $do = $_GET['do']; else $do = '';

//check session
if(isset($_SESSION['tm_law']['access']) && $do != "logout")
{
	header("Location: ./");
	die();
}

require_once("scripts/connect.php");




//defaults
$calloutVisibility = "style='display:none'";

if($do == 'login' && $_POST['submit'] == 'Sign In')
{
	if(isset($_POST['user'])) $u = $_POST['user']; else $u = '';
	if(isset($_POST['pass'])) $p = $_POST['pass']; else $p = '';
	
	$sql = "SELECT * FROM ra_users WHERE userid=? AND pw=?";
	//query just to check if user as an account
	
	$stmt = $tmconn->prepare($sql);
	$stmt->execute(array($u, $p));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$count = count($row);
	
	if($count > 0)
	{
		//verify that the password matches the password saved from the db. 
		if($p == $row['pw'])
		{
			$_SESSION['tm_law'] = array();
			$_SESSION['tm_law']['access'] = '1';
			$_SESSION['tm_law']['uid'] = $row['rauser_id'];
			$_SESSION['tm_law']['name'] = $row['user'];
			$_SESSION['tm_law']['email'] = $row['email'];
			$_SESSION['tm_law']['db'] = array('result'=>'result');
			//$_SESSION['tm_law']['db'] = array('result'=>'result');
			session_write_close();
			
			//Redirect back to index page
			header("Location: choose_lawfirm.php");
		}
		else
		{
			header("Location: login.php?do=lfail");
		}
	}
	else
	{
		header("Location: login.php?do=lfail");
	}
}
else
if($do == 'logout')
{
	unset($_SESSION['tm_law']);
	session_write_close();
	
	$calloutVisibility = "";
	$calloutType = "callout-success";
	$calloutMessage = "<h4>Sign out successful!</h4>
      <p>You have been logged out. Feel free to log back in any time.</p>";
	
}
if($do == 'lfail')
{
	$calloutVisibility = "";
	$calloutType = "callout-warning";
	$calloutMessage = "<h4>Sign in failed!</h4>
      <p>Please check your User ID and Password and try again.</p>";
}
if($do == 'nosession')
{
	$calloutVisibility = "";
	$calloutType = "callout-danger";
	$calloutMessage = "<h4>No session detected!</h4>
      <p>You need to Sign In. Please Provide your login credentials.</p>";
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Trademark Management | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo" style="font-size:33px;">
    <b>Trademark</b> | Management
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <form action="./login.php?do=login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="user" placeholder="User ID">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="pass" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck"></div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat" name="submit" value="Sign In">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <!-- /.login-box-body -->
  
	<br>
	<div <?php echo $calloutVisibility; ?> class="callout <?php echo $calloutType;?>">
		<?php echo $calloutMessage;?>
	</div>
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
