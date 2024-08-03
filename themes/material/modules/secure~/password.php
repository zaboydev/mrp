<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
  <div class="section-body">
    <div class="form-group">
      <input type="password" class="form-control" name="passwd" id="passwd" data-validation-rule="match" data-validation-match="passconf" data-validation-label="Password" required>
      <label for="passwd">Type New Password</label>
    </div>

    <div class="form-group">
      <input type="password" class="form-control" name="passconf" id="passconf" disabled>
      <label for="passconf">Retype New Password</label>
    </div>
  </div>
</section>
<?php endblock() ?>
