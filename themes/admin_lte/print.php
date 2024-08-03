<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Please set below vars in your controller
 *
 * @var $page_header string
 * @var $page_title string
 * @var $page_desc string
 * @var $page_nav array
 * @var $page_content string
 * @var $page_styles array
 * @var $page_script string
 * @var $message string
 *
 */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$page_title;?> | BWD Material Resource Planning</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/fonts/Lato/lato.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/AdminLTE.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/pdf.css');?>">

  <style>
    @page {
      /* ensure you append the header/footer name with 'html_' */
      header: html_pageHeader; /* sets <htmlpageheader name="MyCustomHeader"> as the header */
      footer: html_pageFooter; /* sets <htmlpagefooter name="MyCustomFooter"> as the footer */
    }
  </style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<div class="wrapper">
  <!-- create HEADER -->
  <htmlpageheader name="pageHeader">
    <header>
      <div class="address logo">
        <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>">
      </div>
      <div class="address central">
        <p>
          <strong>PT. Bali Widya Dirgantara</strong>
          <br>Sovereign Plaza Lantai 11
          <br>Jl. TB Simatupang Kav. 36
          <br>Jakarta, 12430
          <br>Ph. +62 21 294 00123
        </p>
      </div>
      <div class="address branch">
        <p>
          <strong>Bali International Flight Academy</strong>
          <br>Lt. Col. Wisnu Airfield
          <br>Sumber Kima village, Buleleng
          <br>Bali, Indonesia
          <br>Ph. +62 8289 700 6386
        </p>
      </div>
    </header>

    <h1 class="page-header">
      <?=$page_header;?>
      <small><?=$page_desc;?></small>
    </h1>

    <div style="clear: both"></div>
  </htmlpageheader>

  <htmlpagefooter name="pageFooter">
    Page {PAGENO}, on {DATE j/m/Y}
  </htmlpagefooter>

  <section>
    <?php if ($page_content)
      $this->load->view($page_content);?>
  </section>
</div>

</body>
</html>
