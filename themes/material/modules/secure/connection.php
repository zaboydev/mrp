<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
  <div class="section-body">
    <div class="card">
      <div class="card-head style-danger">
        <header><i class="fa fa-exclamation-circle"></i> CONNECTION REFUSED</header>
      </div>
      <div class="card-body">
        <p class="lead">
          Connection to Budget Control server failed. Please check if server is alive. The page you requested need to access data to server.
        </p>
        <p class="lead">
          Meanwhile, you may <a href="<?=site_url();?>">return to dashboard</a>.
        </p>
      </div>
    </div>
  </div>
</section>
<?php endblock() ?>
