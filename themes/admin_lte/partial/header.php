<header class="main-header">
  <a href="<?=base_url();?>" class="logo">
    <!-- <span class="logo-mini">BWD <b>MRP</b></span>
    <span class="logo-lg">BWD <b>MRP</b></span> -->
    <span class="logo-mini"><i class="fa fa-space-shuttle"></i></span>
        <span class="logo-lg">
            <i class="fa fa-plane"></i>
          <?=config_item('auth_warehouse');?>
        </span>
  </a>

  <nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <h2 class="navbar-text hidden-xs"><?=$page_header;?></h2>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li>
          <a href="#" data-toggle="control-sidebar">
            <i class="fa fa-list-ul"></i>
            <span class="hidden-xs">OPTIONS</span>
          </a>
        </li>

        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <!-- <img src="<?=base_url('themes/admin_lte/assets/images/user2_160x160.jpg');?>" class="user-image" alt="User Image"> -->
            <?=$auth_role;?>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="<?=base_url('themes/admin_lte/assets/images/user2_160x160.jpg');?>" class="img-circle" alt="User Image">
              <p>
                <?=$auth_fullname;?>
                <small><?=$auth_email;?></small>
              </p>
            </li>
            <li class="user-footer">
              <div class="pull-left">
                <a href="<?=site_url('secure/change_password');?>" class="btn btn-default btn-flat">
                  Change Password
                </a>
              </div>
              <div class="pull-right">
                <a href="<?=site_url('secure/logout');?>" class="btn btn-default btn-flat">
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
