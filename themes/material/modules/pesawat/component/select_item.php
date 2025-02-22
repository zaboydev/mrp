<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Select Item</h4>

  <form id="form_add_request" class="form" role="form" method="post" action="<?= site_url($module['route'] . '/add_selected_item'); ?>">
    <div class="row">
      <div class="col-sm-12">
        
        <div class="table-responsive">
          <table class="table table-hover" id="tbl_prl">
            <thead>
              <tr>
                <th>No.</th>
                <?php if($type=='change'):?>
                <th>Base</th>
                <th>Issued to</th>
                <th>MS#</th>
                <th>Date</th>
                <?php endif;?>
                <th>Group</th>
                <th>Part Number</th>
                <th>Alt. P/N</th>
                <th>Serial Number</th>
                <th>Description</th>
                <?php if($type=='change'):?>
                <th>Quantity</th>
                <?php endif;?>
                <th>Unit</th>                
              </tr>
            </thead>
            <tbody>
              <?php foreach ($entities as $e => $entity) : ?>
                <tr>
                    <td>
                        <div class="checkbox">

                        <input type="checkbox" name="issuance_item_id[]" id="issuance_item_id_<?= $e; ?>" value="<?= $entity['id']; ?>" <?= (in_array($entity['id'], array_keys($_SESSION['component']['items']))) ? 'checked' : ''; ?>>
                        <label for="issuance_item_id_<?= $e; ?>">
                            <?= $e + 1; ?>
                        </label>
                        </div>
                    </td>
                    <?php if($type=='change'):?>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['warehouse']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['issued_to']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['document_number']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_date($entity['issued_date']); ?>
                        </label>
                    </td>
                    <?php endif;?>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['group']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['part_number']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['alternate_part_number']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['serial_number']); ?>
                        </label>
                    </td>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['description']); ?>
                        </label>
                    </td>
                    <?php if($type=='change'):?>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_number($entity['issued_quantity'], 2); ?>
                        </label>
                    </td>
                    <?php endif;?>
                    <td>
                        <label for="issuance_item_id_<?= $e; ?>">
                        <?= print_string($entity['unit']); ?>
                        </label>
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
            // refreshParent();
            // popupClose();
            window.location.href = '<?= site_url($module['route'] . '/edit_selected_item'); ?>';
          }
        });
      }

      button.prop('disabled', false);
    });
    
  });
</script>
<?php endblock() ?>