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
								<li class="dropdown notifications-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					  <i class="fa fa-bell-o"></i>
					  <span class="label label-warning" id='notifCountTotal'></span>
					</a>
					<ul class="dropdown-menu">
					  <li>
						<!-- inner menu: contains the actual data -->
						<ul class="menu">
						  <li>
							<a href="suit.exports.php?i=pending" target="_blank">
							  <i class="fa fa-print"></i> <span id='notifCountPending'>0</span> Pending Generate Exports
							</a>
						  </li>
						  <li>
							<a href="suit.exports.php?i=new" target="_blank">
							  <i class="fa fa-file-text-o"></i> <span id='notifCountGenerated'>0</span> Generated Exports
							</a>
						  </li>
						  <li>
							<a href="suit.exports.php?i=archive" target="_blank">
							  <i class="fa fa-file-text-o"></i> <span id='notifCountArchive'>&nbsp;</span> Archived Exports
							</a>
						  </li>
						</ul>
					  </li>
					</ul>
					</li>
		  
            <!-- User Account Menu -->
            <li class="dropdown user user-menu" style="display">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs"><?php echo $_SESSION['tm_law']['name'];?></span>
              </a> 
               <ul class="dropdown-menu" style="width:120px;">
                <!-- Menu Footer-->
                  <li ><a  style="width:100%;" href="login.php?do=logout" class="nav">Sign out</a></li>
                  <li><a style="width:100%;" href="choose_lawfirm.php" class="nav">Select Lawfirm</a></li>
              </ul>
            </li>
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
