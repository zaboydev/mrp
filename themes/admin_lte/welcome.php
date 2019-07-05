<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="57x57" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-57x57.png');?>">
  <link rel="apple-touch-icon" sizes="60x60" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-60x60.png');?>">
  <link rel="apple-touch-icon" sizes="72x72" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-72x72.png');?>">
  <link rel="apple-touch-icon" sizes="76x76" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-76x76.png');?>">
  <link rel="apple-touch-icon" sizes="114x114" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-114x114.png');?>">
  <link rel="apple-touch-icon" sizes="120x120" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-120x120.png');?>">
  <link rel="apple-touch-icon" sizes="144x144" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-144x144.png');?>">
  <link rel="apple-touch-icon" sizes="152x152" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-152x152.png');?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?=base_url('themes/admin_lte/assets/favicons/apple-icon-180x180.png');?>">
  <link rel="icon" type="image/png" sizes="192x192"  href="<?=base_url('themes/admin_lte/assets/favicons/android-icon-192x192.png');?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?=base_url('themes/admin_lte/assets/favicons/favicon-32x32.png');?>">
  <link rel="icon" type="image/png" sizes="96x96" href="<?=base_url('themes/admin_lte/assets/favicons/favicon-96x96.png');?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url('themes/admin_lte/assets/favicons/favicon-16x16.png');?>">
  <link rel="manifest" href="<?=base_url('themes/admin_lte/assets/favicons/manifest.json');?>">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="<?=base_url('themes/admin_lte/assets/favicons/ms-icon-144x144.png');?>">
  <meta name="theme-color" content="#ffffff">

  <title>
    BWD Material Resource Planning
  </title>

  <link rel="stylesheet" href="<?=base_url('themes/admin_lte/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/fonts/Lato/lato.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('vendors/bootstrap-3.3.6/css/bootstrap.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('vendors/animate.css/animate.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/themes/flat-blue.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/login.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/landing-page.css');?>">
</head>

<body class="flat-blue" id="landing-page">

<div class="container-fluid app-content-b feature-1">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-md-push-8 col-sm-5 color-white">
        <img src="<?php echo base_url('themes/admin_lte/assets/images/logo.png');?>" class="img-responsive" id="logo">

        <form class="form-signin" method="get" action="<?=site_url();?>">
          <?php
          if( $this->input->get('logout') )
            _render_alert('You have successfully logged out.');

          if( isset( $login_error_mesg ) )
            _render_alert('Login Error #' . $this->authentication->login_errors_count . '/' . config_item('max_allowed_attempts') . ': Invalid Username, Email Address, or Password.', 'danger');
          ?>

          <label for="keywords" class="sr-only">Keywords</label>
          <input type="text" name="keywords" id="keywords" class="form-control input-lg" maxlength="255" placeholder="Part No / Serial No / Description" autofocus>

          <input type="submit" value="SEARCH" class="btn btn-lg btn-primary btn-block">

          <p class="text-center" style="margin-top: 20px;">
            <a href="<?=site_url('login');?>">
              <span style="color: #fff">LOGIN USER</span>
            </a>
          </p>
        </form>
      </div>
      <div class="col-lg-8 col-md-8 col-md-pull-4 col-sm-7">
        <?php if (isset($_GET['keywords'])):?>
          <?php if ($entities):?>
            <div class="well">
              <h2 class="page-header text-muted">Search Result for "<span class="text-warning"><?=$_GET['keywords'];?></span>"</h2>

              <div class="table-responsive" style="height: 300px; overflow: scroll;">
                <table class="table">
                  <thead>
                  <tr>
                    <th>No.</th>
                    <th>Description</th>
                    <th>Part No.</th>
                    <th>Serial No.</th>
                    <th>Warehouse</th>
                    <th>Stores</th>
                    <th>Condition</th>
                    <th>Quantity</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php $no = 1;?>
                  <?php foreach ($entities as $key => $value):?>
                    <tr>
                      <th><?=$no;?></th>
                      <th><?=$value['description'];?></th>
                      <th><?=$value['part_number'];?></th>
                      <th><?=$value['serial_number'];?></th>
                      <th><?=$value['warehouse'];?></th>
                      <th><?=$value['stores'];?></th>
                      <th><?=$value['item_condition'];?></th>
                      <th>
                        <?=$value['quantity'];?>
                        <?=$value['uom'];?>
                      </th>
                    </tr>
                    <?php $no++;?>
                  <?php endforeach;?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php else:?>
            <div class="alert alert-info">
              <p>No Result for "<span class="text-warning"><?=$_GET['keywords'];?></span>"</p>
            </div>
          <?php endif;?>
        <?php endif;?>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid app-content-a">
  <div class="container">
    <h1 class="text-center app-content-header">
      MATERIAL RESOURCE PLANNING
    </h1>
    <p class="app-content-company text-center"><span>PT BALI WIDYA DIRGANTARA</span></p>
  </div>
</div>

<script type="text/javascript" src="<?php echo base_url('vendors/jQuery/jquery-2.2.1.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('vendors/bootstrap-3.3.6/js/bootstrap.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('vendors/jquery-match-height/jquery.matchHeight-min.js');?>"></script>

<script>
  $( document ).ready(function(){
    var description = <?=$json_description;?>;
    $( '#keywords' ).autocomplete({
      minLength: 3,
      source: function(request, response) {
        var results = $.ui.autocomplete.filter(description, request.term);
        response(results.slice(0, 10));
      }
    });
  });
</script>

</body>

</html>
