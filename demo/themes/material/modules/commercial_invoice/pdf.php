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
  <title><?=$page['title'];?> | BWD Material Resource Planning</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/fonts/Lato/lato.css');?>">
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
<div class="container">
  <!-- create HEADER -->
  <htmlpageheader name="pageHeader">
    <header>
      <div class="address logo">
        <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>">
      </div>
      <div class="address central">
        <p>
          <strong>PT. Bali Widya Dirgantara</strong>
          <br />
          Graha Niaga, Floor 25
          <br />
          Jl. Jend. Sudirman Kav. 58
          <br />
          Kebayoran Baru, Jakarta Selatan
          <br />
          DKI Jakarta Raya 12190
        </p>
      </div>
      <div class="address branch">
        <p>
          Shipping Document Number :
          <strong><?=$entity['document_number'];?></strong>
          <br />
          Date:
          <?=print_date($entity['issued_date']);?>
          <br />
          Attn. Umar S.
          <br />
          Mobile +62 0813 33312392
          <br />
          Email: umar.bifa@gmail.com
        </p>
      </div>
    </header>

    <h1 class="page-header">
      <?=$page['title'];?>
    </h1>

    <div style="clear: both"></div>
  </htmlpageheader>

  <htmlpagefooter name="pageFooter">
    <small class="text-muted">
      Page {PAGENO}, printed/saved on {DATE j/m/Y}
    </small>
  </htmlpagefooter>

  <section>
    <?php if ($page['content'])
      $this->load->view($page['content']);?>
  </section>
</div>

</body>
</html>
