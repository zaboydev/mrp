<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container">

  <h4 class="page-header">Select Level</h4>

  <form id="form_add_vendor" class="form" role="form" method="post" action="<?= site_url($module['route'] . '/add_selected_level'); ?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="form-group">
          <label>Available Levels</label>

          <?php foreach (available_levels() as $i => $avail_level) : ?>
            <?php
              $levels = array();
              if(isset($_SESSION['tujuan_dinas']['levels'])){
                foreach ($_SESSION['tujuan_dinas']['levels'] as $key => $level) {
                  $levels[$key] = $level['level'];
                }
              }              
            ?>

            <div class="checkbox">
              <input type="checkbox" name="level[<?= $i; ?>]" id="level_<?= $i; ?>" value="<?= $avail_level; ?>" <?= (in_array($avail_level, $levels)) ? 'checked' : ''; ?>>
              <label for="level_<?= $i; ?>">
                <?= $avail_level; ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="clearfix">
      <div class="pull-right">
        <button type="submit" id="submit_button" class="btn btn-primary">Next</button>
      </div>

      <button type="button" class="btn btn-default" onclick="popupClose()">Cancel</button>
    </div>
  </form>

  <div class="clearfix"></div>
  <hr>

  <p>
    Material Resource Planning - PT Bali Widya Dirgantara
  </p>
</div>
<?php endblock() ?>

<?php startblock('simple_styles') ?>
<?= link_tag('themes/material/assets/css/theme-default/libs/toastr/toastr.css') ?>
<?php endblock() ?>

<?php startblock('simple_scripts') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<script>
  $(function() {
    $('#submit_button').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('#form_add_vendor');
      var action = form.attr('action');

      button.prop('disabled', true);

      if (form.valid()) {
        console.log('form is valid!');
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.success == false) {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.message);
          } else {
            window.location.href = '<?= site_url($module['route'] . '/edit_expense'); ?>';
            // refreshParent();
            // popupClose();
          }
        });
      }

      button.prop('disabled', false);
    });
  });
</script>
<?php endblock() ?>