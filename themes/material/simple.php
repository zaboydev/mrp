<?php include 'themes/base.php' ?>

<?php startblock('styles') ?>
  <?=link_tag('themes/material/assets/css/fonts.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/libs/jquery-ui/jquery-ui-theme.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/bootstrap.css') ?>
  <?=link_tag('themes/material/assets/css/theme-default/font-awesome.min.css') ?>
  <?=link_tag('themes/material/assets/css/app.css') ?>

  <?php emptyblock('simple_styles') ?>

  <!--[if lt IE 9]>
    <?=html_script('themes/material/assets/js/libs/utils/html5shiv.js') ?>
    <?=html_script('themes/material/assets/js/libs/utils/respond.min.js') ?>
  <![endif]-->

<?php endblock() ?>

<?php startblock('body') ?>
<?php endblock() ?>

<?php startblock('scripts') ?>
  <?=html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>

  <script language="Javascript">
  function popupOpen(destination, windowname){
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

    if (typeof(mylink) == 'string') href = destination;
    else href = destination.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left);

    if (! window.focus) return true;
    else return false;
  }

  function refreshParent(){
    window.opener.location.reload();
  }

  function popupClose(){
    window.close();
  }
  </script>

  <?php emptyblock('simple_scripts') ?>
<?php endblock() ?>
