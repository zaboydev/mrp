<header class="main-header">
    <a href="<?=base_url();?>" class="logo">
        <span class="logo-mini">BWD <b>MRP</b></span>
        <span class="logo-lg">BWD <b>MRP</b></span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if (isset($page_nav)):?>
                    <?php foreach ($page_nav as $nav):?>
                        <li><?=$nav;?></li>
                    <?php endforeach;?>
                <?php endif;?>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?=base_url('themes/admin_lte/assets/images/user2_160x160.jpg');?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?=$auth_username;?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="<?=base_url('themes/admin_lte/assets/images/user2_160x160.jpg');?>" class="img-circle" alt="User Image">
                            <p>
                                <?=$auth_username;?> - <?=$auth_role;?>
                                <small><?=$auth_email;?></small>
                            </p>
                        </li>
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
