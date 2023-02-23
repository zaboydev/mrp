<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

    <h4 class="page-header">Update Request</h4>

    <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/update_expense'); ?>">
        <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-nowrap">
                    <thead>
                    <tr>
                        <th class="middle-alignment" rowspan="2" width="20%">Level</th>
                        <th class="middle-alignment" rowspan="2" width="20%">Value</th>

                    </tr>
                    </thead>
                    <tbody>
                      
                        <?php foreach ($_SESSION['tujuan_dinas']['levels'] as $key => $level) : ?>
                        <tr id="row_<?= $key; ?>">
                        <td>
                            <input type="text" rel="employee_benefit" name="request[<?= $key; ?>][level]" value="<?= $_SESSION['benefit']['levels'][$key]['level']; ?>" class="form-control" readonly>
                        </td>
                        <td>
                          <input type="text" rel="amount" name="request[<?= $key; ?>][amount]" value="<?= $_SESSION['benefit']['levels'][$key]['amount']; ?>" class="form-control number" style="text-align:right;">
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
<?= html_script('themes/script/jquery.number.js') ?>
<script>
  $('.number').number(true, 2, '.', ',');
  $(function() {
    $('#submit_button').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('#form_update_request');
      var action = form.attr('action');

      button.prop('disabled', true);

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.success == false) {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.message);
          } else {
            refreshParent();
            popupClose();
          }
        });
      }

      button.prop('disabled', false);
    });
  });
</script>
<?php endblock() ?>