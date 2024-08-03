<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
  <div class="section-body">
    <div class="card">
      <div class="card-head style-danger">
        <header><i class="fa fa-exclamation-circle"></i> File Not Found</header>
      </div>
      <div class="card-body">
        <p class="lead">
          The File you were looking for has Not Found
        </p>
        <p class="lead">
          Meanwhile, you may <a href="<?=site_url();?>">return to dashboard</a>.
        </p>
      </div>
    </div>
  </div>
</section>
<?php endblock() ?>
