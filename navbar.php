  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <span class="navbar-brand"><b>Trademark</b>| Case Management</span>
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
            <li class="dropdown user user-menu" style="display">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs"><?php echo $_SESSION['tm_law']['name'];?></span>
              </a>
              <ul class="dropdown-menu" >
                <!-- Menu Footer-->
                  <li class="user-footer"><div class="pull-right" style="width:100%;"><a  style="width:100%;" href="login.php?do=logout" class="btn btn-default btn-flat">Sign out</a></div></li>
                  <li class="user-footer"><div class="pull-right" style="width:100%;"><a style="width:100%;" href="choose_lawfirm.php" class="btn btn-default btn-flat">Select Lawfirm</a></div></li>
              </ul>
            </li>
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
