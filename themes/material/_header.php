<div class="headerbar">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="headerbar-left">
    <ul class="header-nav header-nav-options">
      <li class="header-nav-brand" >
        <div class="brand-holder">
          <a href="<?=site_url();?>">
            <span class="text-lg text-bold text-primary"><?=strtoupper($module['parent']);?></span>
          </a>
        </div>
      </li>
      <li>
        <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
          <i class="fa fa-bars"></i>
        </a>
      </li>
    </ul>
  </div>
  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="headerbar-right">
    <ul class="header-nav header-nav-options">
      <li>
        <!-- Search form -->
        <form class="navbar-search" role="search">
          <div class="form-group">
            <input type="text" class="form-control" id="navbar-search-box" name="headerSearch" placeholder="item, P/N, S/N">
          </div>
          <button type="submit" id="navbar-search-button" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
        </form>
      </li>
    </ul><!--end .header-nav-options -->
    <ul class="header-nav header-nav-profile">
      <li class="dropdown">
        <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
          <img src="<?=base_url('themes/material/');?>/assets/img/user.png" alt="" />
          <span class="profile-info">
            <strong class="text-bold"><?=config_item('auth_person_name');?></strong>
            <small class="text-default-dark"><?=config_item('auth_role');?></small>
            <!-- -->
          </span>
        </a>
        <ul class="dropdown-menu animation-dock">
          <li>
            <a href="<?=site_url('secure/password');?>">
              <i class="fa fa-fw fa-lock"></i>
              Change Password
            </a>
          </li>
          <li>
            <a href="<?=site_url('secure/logout');?>">
              <i class="fa fa-fw fa-power-off text-danger"></i>
              Logout
            </a>
          </li>
        </ul><!--end .dropdown-menu -->
      </li><!--end .dropdown -->
    </ul><!--end .header-nav-profile -->
    <ul class="header-nav header-nav-toggle">
      <li>
        <a class="btn btn-icon-toggle btn-default" href="#offcanvas-right" data-toggle="offcanvas">
          <i class="fa fa-ellipsis-v"></i>
        </a>
      </li>
    </ul><!--end .header-nav-toggle -->
  </div><!--end #header-navbar-collapse -->
</div>
<input type="hidden" name="" id="baselink" value="<?=base_url().$this->uri->segment(1)?>">
