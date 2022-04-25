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
  <?=link_tag('vendors/select2-4.0.3/dist/css/select2.min.css') ?>
  <?=link_tag('vendors/select2-pmd/css/pmd-select2.css') ?>  

  <!--[if lt IE 9]>
    <?=html_script('themes/material/assets/js/libs/utils/html5shiv.js') ?>
    <?=html_script('themes/material/assets/js/libs/utils/respond.min.js') ?>
  <![endif]-->

  <style type="text/css">
                .tg  {border-collapse:collapse;border-spacing:0;border-color:#ccc;width: 100%; }
                .tg td{font-family:Arial;font-size:10px;padding:5px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;}
                .tg th{font-family:Arial;font-size:12px;font-weight:normal;padding:5px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#f0f0f0;}
                .tg .tg-3wr7{font-weight:bold;font-size:12px;font-family:"Arial", Helvetica, sans-serif !important;;text-align:center}
                .tg .tg-ti5e{font-size:10px;font-family:"Arial", Helvetica, sans-serif !important;;text-align:center}
                .tg .tg-rv4w{font-size:10px;font-family:"Arial", Helvetica, sans-serif !important;}
    </style>

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
  <?=html_script('vendors/twbs-pagination-master/jquery.twbsPagination.js') ?>
  <?= html_script('vendors/select2-pmd/js/pmd-select2.js') ?>
  <?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
  
  <?php $this->load->view('material/_script') ?>

  <?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
  <?php if($this->uri->segment(1) === "budget_cot"): ?>
      <?=html_script('themes/script/budget_cot.js') ?>
  <?php endif ?>
<?php endblock() ?>

<?=html_close() ?>
