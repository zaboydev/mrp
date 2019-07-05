<?php include 'themes/base.php' ?>

<?php startblock('styles') ?>
  <?=link_tag('vendors/pace/flash.css') ?>
  <?=link_tag('themes/material/assets/css/fonts.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/libs/jquery-ui/jquery-ui-theme.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/bootstrap.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/materialadmin.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/font-awesome.min.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/material-design-iconic-font.min.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/libs/toastr/toastr.css') ?>
  <?=link_tag('vendors/DataTables-1.10.12/datatables.min.css') ?>
  <?=link_tag('vendors/bootstrap-daterangepicker/daterangepicker.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css') ?>
  <?=link_tag('themes/material/assets/css/app.css') ?>
  <?=link_tag('themes/material/assets/css/bootstrap.print.css') ?>

  <!--[if lt IE 9]>
    <?=html_script('themes/material/assets/js/libs/utils/html5shiv.js') ?>
    <?=html_script('themes/material/assets/js/libs/utils/respond.min.js') ?>
  <![endif]-->

<?php endblock() ?>

<?php startblock('body') ?>
  <?=html_body('menubar-hoverable header-fixed menubar-first full-content') ?>

  <div class="progress-overlay"></div>

  <header id="header" class="">
    <?php $this->load->view('material/_header') ?>
  </header>

  <div id="base">

    <div class="offcanvas">
      <?php emptyblock('offcanvas_left') ?>
    </div>

    <div id="content">
      <?php emptyblock('content') ?>
    </div>

    <div id="menubar" class="menubar-inverse ">
      <?php $this->load->view('material/_menubar') ?>
    </div>

    <div class="offcanvas">
      <?php emptyblock('offcanvas_right') ?>
    </div>

  </div>
<?php endblock() ?>

<?php startblock('scripts') ?>
  <?=html_script('vendors/pace/pace.min.js') ?>
  <?=html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
  <?=html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
  <?=html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>
  <?=html_script('vendors/bootstrap-daterangepicker/moment.min.js') ?>
  <?=html_script('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>
  <?=html_script('themes/material/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js') ?>

  <?php $this->load->view('material/_script') ?>

  <?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>

<?=html_close() ?>
