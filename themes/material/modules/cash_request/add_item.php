<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Select Cash Payment</h4>

  <form id="form_add_request" class="form" role="form" method="post" action="<?= site_url($module['route'] . '/add_selected_item'); ?>">
    <div class="row">
      <div class="col-sm-12">
        
        <div class="table-responsive">
          <table class="table table-hover" id="tbl_prl">
            <thead>
              <tr>
                <th>
                  No.
                </th>                
                <th>
                  No. Transaksi
                </th>               
                <th>
                  Date
                </th>
                <th>
                  Vendor
                </th>
                <th>
                  Amount
                </th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1;?>
              <?php foreach ($entities_po as $entity_po) : ?>
                <tr>
                  <td>
                    <div class="checkbox">
                      <input type="checkbox" name="payment_id[]" id="request_id_<?= $no; ?>" value="<?= $entity_po['id']; ?>-mrp" <?= (in_array($entity_po['id'].'-mrp', array_keys($_SESSION['cash_request']['items']))) ? 'checked' : ''; ?>>
                      <label for="request_id_<?= $no; ?>">
                        <?= $no; ?>
                      </label>
                    </div>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity_po['document_number']); ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_date($entity_po['tanggal'],'d/m/Y') ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity_po['vendor']); ?>
                    </label>
                  </td>
                  <td><label for="request_id_<?= $no; ?>"><?= print_number($entity_po['amount_paid'], 2) ?></label></td>
                </tr>
                <?php $no++; ?>
              <?php endforeach; ?>
              <?php foreach ($entities_non_po as $entity_no_po) : ?>
                <tr>
                  <td>
                    <div class="checkbox">
                      <input type="checkbox" name="payment_id[]" id="request_id_<?= $no; ?>" value="<?= $entity_no_po['id']; ?>-budgetcontrol" <?= (in_array($entity_no_po['id'].'-budgetcontrol', array_keys($_SESSION['cash_request']['items']))) ? 'checked' : ''; ?>>
                      <label for="request_id_<?= $no; ?>">
                        <?= $no; ?>
                      </label>
                    </div>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity_no_po['document_number']); ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_date($entity_no_po['tanggal'],'d/m/Y') ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity_no_po['vendor']); ?>
                    </label>
                  </td>
                  <td><label for="request_id_<?= $no; ?>"><?= print_number($entity_no_po['amount_paid'], 2) ?></label></td>
                </tr>
                <?php $no++; ?>
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
<?= html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>
<script>
  $(function() {
    $('#submit_button').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('#form_add_request');
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
            // window.location.href = '<?= site_url($module['route'] . '/edit_item'); ?>';
            refreshParent();
            popupClose();
          }
        });
      }

      button.prop('disabled', false);
    });

    
    // $('#submit_button').on('click', function(e) {
    //   e.preventDefault();

    //   var button = $(this);
    //   var form = $('#form_add_request');
    //   var action = form.attr('action');

    //   button.prop('disabled', true);

    //   if (form.valid()) {
    //     $.post(action, form.serialize()).done(function(data) {
    //       var obj = $.parseJSON(data);

    //       if (obj.success == false) {
    //         toastr.options.timeOut = 10000;
    //         toastr.options.positionClass = 'toast-top-right';
    //         toastr.error(obj.message);
    //       } else {
    //         refreshParent();
    //         popupClose();
    //       }
    //     });
    //   }

    //   button.prop('disabled', false);
    // });
  });
</script>
<?php endblock() ?>