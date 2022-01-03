<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Select Request</h4>

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
                  Vendor
                </th>               
                <th>
                  Currency
                </th>
                <th>
                  PO#
                </th>
                <th>
                  Status
                </th>
                <th>
                  Due Date
                </th>
                <!-- <th>
                  Received Qty
                </th>
                <th>
                  Received Val.
                </th> -->
                <th>
                  Amount
                </th>
                <th>
                  Remaining Purposed
                </th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1;?>
              <?php foreach ($entities as $entity) : ?>
                <tr>
                  <td>
                    <div class="checkbox">
                      <input type="checkbox" name="po_id[]" id="request_id_<?= $no; ?>" value="<?= $entity['id']; ?>" <?= (in_array($entity['id'], array_keys($_SESSION['payment_request']['po']))) ? 'checked' : ''; ?>>
                      <label for="request_id_<?= $no; ?>">
                        <?= $no; ?>
                      </label>
                    </div>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity['vendor']); ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity['default_currency']); ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity['document_number']); ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_string($entity['status']); ?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?= $no; ?>">
                      <?= print_date($entity['due_date'],'d/m/Y') ?>
                    </label>
                  </td>
                  <td><label for="request_id_<?= $no; ?>"><?= print_number($entity['grand_total'], 2) ?></label></td>
                  <td><label for="request_id_<?= $no; ?>"><?= print_number($entity['remaining_payment_request'], 2) ?></label></td>
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