<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('LINK_PROTOCOL', USE_SSL ? 'https' : NULL);

/**
 * Please set below vars in your controller
 *
 * @var $login_url string
 */
?>
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
      <div class="col-lg-8 col-md-8 col-sm-7">
        <?php if (isset($_GET['keywords'])):?>
          <?php if ($entities):?>
            <div class="well">
              <h2 class="page-header text-muted">Search Result for "<span class="text-warning"><?=$_GET['keywords'];?></span>"</h2>

              <div class="table-responsive" style="height: 250px; overflow: auto;">
                <table class="table table-striped">
                  <thead>
                  <tr>
                    <th class="text-right">No.</th>
                    <th>Description</th>
                    <th>Part No.</th>
                    <th>Serial No.</th>
                    <th>Warehouse</th>
                    <th>Stores</th>
                    <th>Condition</th>
                    <th class="text-right">Quantity</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php $no = 1;?>
                  <?php foreach ($entities as $key => $value):?>
                    <tr>
                      <th class="text-right"><?=$no;?></th>
                      <th><?=$value['description'];?></th>
                      <th><?=$value['part_number'];?></th>
                      <th><?=$value['serial_number'];?></th>
                      <th><?=$value['warehouse'];?></th>
                      <th><?=$value['stores'];?></th>
                      <th><?=$value['item_condition'];?></th>
                      <th class="text-right">
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
        <?php else:?>
          <div class="divider"></div>
        <?php endif;?>

        <form id="form_search" class="form-search form-inline" method="get" action="<?=site_url('search');?>">
          <div class="form-group">
            <label for="warehouse" class="sr-only">Warehouse</label>
            <select class="form-control input-lg" id="warehouse" name="warehouse">
              <option value="ALL BASE">ALL BASE</option>
              <?php foreach ($warehouses as $base):?>
                <option value="<?=$base['warehouse'];?>" <?=($base['warehouse'] === $warehouse) ? 'selected' : '';?>><?=$base['warehouse'];?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label for="keywords" class="sr-only">Keywords</label>
            <div class="input-group">
              <input type="text" name="keywords" id="keywords" class="form-control input-lg" maxlength="255" placeholder="Part No / Serial No / Description" required autocomplete="off" autofocus>
              <div class="input-group-addon" id="search-button">SEARCH ITEMS</div>
            </div>
          </div>
        </form>
      </div>

      <div class="col-lg-4 col-md-4 col-sm-5 color-white">
        <?php if( ! isset( $on_hold_message ) ):?>
          <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>" class="img-responsive" id="logo">

          <?=form_open($login_url, array('class' => 'form-signin'));?>

          <?php
          if ( $this->input->get('logout') )
            render_alert('You have successfully logged out.');

          if ( isset( $login_error_mesg ) )
            render_alert('Login Error #' . $this->authentication->login_errors_count . '/' . config_item('max_allowed_attempts') . ': Invalid Username, Email Address, or Password.', 'danger');
          ?>

          <label for="login_string" class="sr-only">Username</label>
          <input type="text" name="login_string" id="login_string" class="form-control input-lg" maxlength="255" placeholder="username or email">

          <label for="login_pass" class="sr-only">Password</label>
          <input type="password" name="login_pass" id="login_pass" class="form-control input-lg" maxlength="255" readonly="readonly" onfocus="this.removeAttribute('readonly');" placeholder="password">

          <?php if( config_item('allow_remember_me') ):?>
            <label for="remember_me" class="checkbox">
              <input type="checkbox" id="remember_me" name="remember_me" value="yes" />
              Remember Me
            </label>
          <?php endif;?>

          <?=form_submit('submit', 'LOGIN', array(
            'class' => 'btn btn-lg btn-primary btn-block'
          ));?>

          <?=form_close();?>
        <?php else:?>
          <div class="well well-sm">
            <h3>Excessive Login Attempts</h3>

            <p>
              You have exceeded the maximum number of failed login<br />
              attempts that this website will allow.
            <p>

            <p>
              Your access to login and account recovery has been blocked for <?=( (int) config_item('seconds_on_hold') / 60 );?> minutes.
            </p>
          </div>
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

<script type="text/javascript" src="<?=base_url('vendors/jQuery/jquery-2.2.1.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('vendors/bootstrap-3.3.6/js/bootstrap.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('vendors/jquery-match-height/jquery.matchHeight-min.js');?>"></script>

<script>
function popup(mylink, windowname){
  var height = window.innerHeight;
  var widht;
  var href;

  if (screen.availWidth > 768){
    width = 769;
  } else {
    width = screen.availWidth;
  }

  var left = (screen.availWidth / 2) - (width / 2);
  var top = 0;
  // var top = (screen.availHeight / 2) - (height / 2);

  if (typeof(mylink) == 'string') href = mylink;
  else href = mylink.href;

  window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left);

  if (! window.focus) return true;
  else return false;
}

$( document ).ready(function(){
  var description = <?=$json_description;?>;
  var url = $('#form_search').attr('action');

  $( '#keywords' ).autocomplete({
    minLength: 3,
    autoFocus: true,
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(description, request.term);
      response(results.slice(0, 10));
    },
    select: function( event, ui ) {
      $(this).val(ui.item.value);

      popup(url, 'search')
      // $('#form-search').submit();
    }
  });

  $('#search-button').on('click', function(){
    if ($('#keywords').val() != ''){
      // $('#form-search').submit();

      popup(url, 'search');
    }
  });
});
</script>

</body>

</html>
