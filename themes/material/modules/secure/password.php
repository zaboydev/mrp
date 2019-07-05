<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
  <div class="section-body">
    <form class="card style-default-bright" method="post">
      <div class="card-head style-primary-dark">
        <header>Change Password</header>
      </div>

      <div class="card-body">
        <div class="form-group">
          <label for="passwd">Type New Password</label>
          <input type="password" class="form-control" name="passwd" id="passwd" data-validation-rule="match" data-validation-match="passconf" data-validation-label="Password" required>
        </div>

        <div class="form-group">
          <label for="passconf">Retype New Password</label>
          <input type="password" class="form-control" name="passconf" id="passconf">
        </div>

        <div class="form-group">
          <input type="submit" class="btn btn-primary ink-reaction" value="update">
        </div>

        <?php if ( $this->session->flashdata('alert') )
          render_callout($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);?>
      </div>
    </form>
  </div>
</section>
<?php endblock() ?>
