<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

    <h4 class="page-header">Update Request</h4>

    <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/update_expense'); ?>">
        <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table-hover table-bordered">
                    <thead>
                    <tr>
                        <th class="middle-alignment" rowspan="2" width="15%">Expense Name</th>

                        <?php foreach ($_SESSION['tujuan_dinas']['levels'] as $key => $level) : ?>
                        <th class="middle-alignment text-center" colspan="3">
                            <?= $level['level']; ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>

                    <tr>
                        <?php for ($v = 0; $v < count($_SESSION['tujuan_dinas']['levels']); $v++) : ?>
                        <th class="middle-alignment text-center">Amount</th>
                        <th class="middle-alignment text-center">Day</th>
                        <th class="middle-alignment text-center">Notes</th>
                        <?php endfor; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($_SESSION['tujuan_dinas']['items'] as $id => $request) : ?>
                        <tr id="row_<?= $id; ?>">
                          <td>
                              <input type="text" rel="expense_name" name="request[<?= $id; ?>][expense_name]" value="<?= $_SESSION['tujuan_dinas']['items'][$id]['expense_name']; ?>" class="form-control">
                          </td>

                        <?php foreach ($_SESSION['tujuan_dinas']['levels'] as $key => $level) : ?>
                            <td>
                            <input type="text" rel="amount" name="request[<?= $id; ?>][levels][<?= $key; ?>][amount]" value="<?= $_SESSION['tujuan_dinas']['items'][$id]['levels'][$key]['amount']; ?>" class="form-control number" style="text-align:right;">
                            </td>
                            <td>
                            <input type="number" rel="day" name="request[<?= $id; ?>][levels][<?= $key; ?>][day]" value="<?= $_SESSION['tujuan_dinas']['items'][$id]['levels'][$key]['day']; ?>" class="form-control">
                            </td>
                            <td>
                            <input type="text" rel="notes" name="request[<?= $id; ?>][levels][<?= $key; ?>][notes]" value="<?= $_SESSION['tujuan_dinas']['items'][$id]['levels'][$key]['notes']; ?>" class="form-control">
                            </td>
                        <?php endforeach; ?>
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
<style>
  .form-control{
    font-size:12px;
    padding:4.5px 4px;
  }
</style>
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