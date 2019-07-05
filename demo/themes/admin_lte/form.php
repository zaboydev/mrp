<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('LINK_PROTOCOL', USE_SSL ? 'https' : NULL);

/**
 * Please set below vars in your controller
 *
 * @var $page_header string
 * @var $page_title string
 * @var $page_desc string
 * @var $page_nav array
 * @var $page_content string
 * @var $page_script string
 * @var $message string
 */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$page_title;?> | BWD Material Resource Planning</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/plugins/bootstrap/css/bootstrap.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/plugins/font-awesome/css/font-awesome.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/plugins/ionicons/css/ionicons.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/AdminLTE.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/skins/skin-blue.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/fonts/Lato/lato.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/layout.css');?>">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
<div class="wrapper">
  <?php $this->load->view('admin_lte/partial/header');?>
  <?php $this->load->view('admin_lte/partial/aside');?>

  <div class="content-wrapper">
    <section class="content-header visible-xs">
      <h1>
        <?=$page_header;?>
      </h1>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-sm-12">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$page_title;?></h3>

              <div class="box-tools pull-right">
                <a href="<?=$close_url;?>" class="btn btn-box-tool">
                  <i class="fa fa-times"></i>
                </a>
              </div>
            </div>

            <?php if ($page_content)
              $this->load->view($page_content);?>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php $this->load->view('admin_lte/partial/footer');?>
  <?php $this->load->view('admin_lte/partial/right-sidebar.php');?>
</div>

<script src="<?=base_url('themes/admin_lte/plugins/jQuery/jQuery-2.2.0.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/bootstrap/js/bootstrap.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/slimScroll/jquery.slimscroll.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/fastclick/fastclick.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/assets/js/app.min.js');?>"></script>

<?php if (isset($page_script))
  $this->load->view($page_script);?>
</body>
</html>
