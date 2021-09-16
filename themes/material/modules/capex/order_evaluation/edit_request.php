<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Update Request</h4>

  <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/update_request'); ?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th class="middle-alignment" rowspan="2">PR Number</th>
                <th class="middle-alignment" rowspan="2">Description</th>
                <th class="middle-alignment" rowspan="2">Part Number</th>
                <th class="middle-alignment" rowspan="2">Alt. P/N</th>
                <th class="middle-alignment text-right" rowspan="2">Qty</th>
                <th class="middle-alignment text-right" rowspan="2">Price Requested</th>
                <!-- <th class="middle-alignment text-right" rowspan="2">Unit</th> -->
                <th class="middle-alignment text-right hide" rowspan="2">Konversi</th>
                <th class="middle-alignment text-right" rowspan="2">Remarks</th>

                <?php foreach ($_SESSION['capex_poe']['vendors'] as $key => $vendor) : ?>
                  <th class="middle-alignment text-center" colspan="2">
                    <?= $vendor['vendor']; ?>
                  </th>
                <?php endforeach; ?>
              </tr>

              <tr>
                <?php for ($v = 0; $v < count($_SESSION['capex_poe']['vendors']); $v++) : ?>
                  <th class="middle-alignment text-center">Unit Price</th>
                  <th class="middle-alignment text-center">Core Charge</th>
                <?php endfor; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_SESSION['capex_poe']['request'] as $id => $request) : ?>
                <tr id="row_<?= $id; ?>">
                  <td>
                    <?= $request['purchase_request_number']; ?>
                  </td>
                  <td>
                    <input type="text" rel="description" name="request[<?= $id; ?>][description]" value="<?= $_SESSION['capex_poe']['request'][$id]['description']; ?>" class="form-control">
                  </td>
                  <td class="no-space">
                    <input type="text" rel="part_number" name="request[<?= $id; ?>][part_number]" value="<?= $_SESSION['capex_poe']['request'][$id]['part_number']; ?>" class="form-control">
                  </td>
                  <td>
                    <input type="text" rel="alternate_part_number" name="request[<?= $id; ?>][alternate_part_number]" value="<?= $_SESSION['capex_poe']['request'][$id]['alternate_part_number']; ?>" class="form-control">
                  </td>
                  <td>
                    <input type="number" rel="quantity" name="request[<?= $id; ?>][quantity]" value="<?= $_SESSION['capex_poe']['request'][$id]['quantity']; ?>" class="form-control">
                  </td>
                  <td>
                    <input readonly="true" type="number" rel="quantity" name="request[<?= $id; ?>][unit_price_requested]" value="<?= $_SESSION['capex_poe']['request'][$id]['unit_price_requested']; ?>" class="form-control">
                  </td>
                  <td class="hide">
                    <input type="text" rel="unit" name="request[<?= $id; ?>][unit]" value="<?= $_SESSION['capex_poe']['request'][$id]['unit']; ?>" class="form-control">
                  </td>
                  <td class=" hide">
                    <input type="number" rel="konversi" name="request[<?= $id; ?>][konversi]" value="<?= $_SESSION['capex_poe']['request'][$id]['konversi']; ?>" class="form-control">
                  </td>
                  <td>
                    <input type="text" rel="remarks" name="request[<?= $id; ?>][remarks]" value="<?= $_SESSION['capex_poe']['request'][$id]['remarks']; ?>" class="form-control">
                  </td>

                  <?php foreach ($_SESSION['capex_poe']['vendors'] as $key => $vendor) : ?>
                    <td>
                      <input type="text" rel="unit_price" name="request[<?= $id; ?>][vendors][<?= $key; ?>][unit_price]" value="<?= $_SESSION['capex_poe']['request'][$id]['vendors'][$key]['unit_price']; ?>" class="form-control">
                    </td>

                    <td>
                      <input type="text" rel="core_charge" name="request[<?= $id; ?>][vendors][<?= $key; ?>][core_charge]" value="<?= $_SESSION['capex_poe']['request'][$id]['vendors'][$key]['core_charge']; ?>" class="form-control">
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