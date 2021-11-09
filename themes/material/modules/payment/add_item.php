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
                  PO#
                </th>
                <th></th>
                <th>
                  Status
                </th>
                <th>
                  Due Date
                </th>
                <th>
                  Received Qty
                </th>
                <th>
                  Received Val.
                </th>
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
                      <input type="checkbox" name="po_id[]" id="request_id_<?= $no; ?>" value="<?= $entity['id']; ?>" <?= (in_array($entity['id'], array_keys($_SESSION['payment_request']['items']))) ? 'checked' : ''; ?>>
                      <label for="request_id_<?= $no; ?>">
                        <?= $no; ?>
                      </label>
                    </div>
                  </td>
                  <td colspan="2">
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
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td><label for="request_id_<?= $no; ?>"><?= print_number($entity['grand_total'], 2) ?></label></td>
                  <td><label for="request_id_<?= $no; ?>"><?= print_number($entity['remaining_payment_request'], 2) ?></label></td>
                </tr>
                <?php  $no_item=1; ?>
                <?php foreach ($entity['items'] as $i => $detail_po) : ?>
                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <div class="checkbox">
                      <input type="checkbox" name="item_id[]" id="request_id_<?= $no; ?>_<?= $no_item; ?>" value="<?= $entity['id']; ?>-<?= $detail_po['id']; ?>" <?= (in_array($entity['id'].'-'.$detail_po['id'], array_keys($_SESSION['payment_request']['items']))) ? 'checked' : ''; ?>>
                      <label for="request_id_<?= $no; ?>_<?= $no_item; ?>">
                        <?= $no; ?>-<?= $no_item; ?>
                      </label>
                    </div>
                  </td>
                  <td>
                      <?= print_string($detail_po['part_number']); ?> | <?= print_string($detail_po['description']); ?>
                  </td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>
                      <?= print_number($detail_po['quantity_received'], 2) ?>
                  </td>
                  <td>
                      <?= print_number($detail_po['quantity_received'] * ($detail_po['unit_price'] + $detail_po['core_charge']), 2) ?>
                  
                  </td>
                  <td>
                    <?= print_number($detail_po['total_amount'], 2) ?>
                  </td>
                  <td>
                    <?= print_number($detail_po['left_paid_request'], 2) ?>
                  </td>
                </tr>
                <?php $no_item++;?>
                <?php endforeach; ?>
                <?php if ($entity['additional_price_remaining_request'] != 0) : ?>
                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <div class="checkbox">
                      <input type="checkbox" name="item_id[]" id="request_id_<?= $no; ?>_<?= $no_item; ?>" value="<?= $entity['id']; ?>-0" <?= (in_array($entity['id'].'-0', array_keys($_SESSION['payment_request']['items']))) ? 'checked' : ''; ?>>
                      <label for="request_id_<?= $no; ?>_<?= $no_item; ?>">
                        <?= $no; ?>-<?= $no_item; ?>
                      </label>
                    </div>
                  </td>
                  <td>
                    Additional Price (PPN, DISC, SHIPPING COST)
                  </td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>
                    <?= print_number($entity['additional_price'], 2) ?>
                  </td>
                  <td>
                    <?= print_number($entity['additional_price_remaining_request'], 2) ?>
                  </td>
                </tr>
                <?php endif;?>
                <?php $no++;?>
                <tr>
                  <td colspan="9"></td>
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
            window.location.href = '<?= site_url($module['route'] . '/edit_item'); ?>';
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