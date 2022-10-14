<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Update Item</h4>

  <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/update_selected_item'); ?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-nowrap">
            <thead>
                <tr>
                    <th></th>
                    <th class="middle-alignment">Group</th>
                    <th class="middle-alignment">Description</th>
                    <th class="middle-alignment">P/N</th>
                    <th class="middle-alignment">Alt. P/N</th>
                    <th class="middle-alignment">S/N</th>
                    <th class="middle-alignment">On Hand Stock</th>
                    <th class="middle-alignment">Unit</th>
                    <th class="middle-alignment">Condition</th>
                    <th class="middle-alignment">Stores</th>
                    <th class="middle-alignment">Qty</th>
                    <th class="middle-alignment">Insurance Currency</th>
                    <th class="middle-alignment">Value</th>
                    <th class="middle-alignment">Insurance Unit Value</th>
                    <th class="middle-alignment">Remarks</th>
                    <th class="middle-alignment">AWB Number</th>                          
                </tr>              
            </thead>
            <tbody>
              <?php foreach ($_SESSION['shipping_internal']['items'] as $id => $item) : ?>
                <tr id="row_<?= $id; ?>">
                    <td></td>
                    <td>
                        <?= $item['group']; ?>
                    </td>
                    <td>
                        <?= $item['description']; ?>
                    </td>
                    <td class="no-space">
                        <?= $item['part_number']; ?>
                    </td> 
                    <td>
                        <?= $item['alternate_part_number']; ?>
                    </td>                 
                    <td class="no-space">
                        <?= $item['serial_number']; ?>
                    </td>
                    <td class="no-space">                        
                        <?php if (isset($_SESSION['shipping_internal']['id'])):?>
                        <?= print_number($item['maximum_quantity']+$item['issued_quantity'],2); ?>
                        <?php else:?>
                        <?= print_number($item['maximum_quantity'],2); ?>
                        <?php endif;?>
                    </td>
                    <td>
                        <?= $item['unit']; ?>
                    </td>
                    <td>
                        <?= $item['condition']; ?>
                    </td>
                    <td>
                        <?= $item['stores']; ?>
                    </td>
                    <td>
                        <input type="number" max="<?= (isset($_SESSION['shipping_internal']['id']))? ($item['maximum_quantity']+$item['issued_quantity']):$_SESSION['shipping_internal']['items'][$id]['maximum_quantity']; ?>" rel="quantity" name="item[<?= $id; ?>][issued_quantity]" value="<?= $_SESSION['shipping_internal']['items'][$id]['issued_quantity']; ?>" class="form-control">
                    </td>
                    <td>
                      <select name="item[<?= $id; ?>][insurance_currency]" class="form-control" required>
                        <?php foreach ($this->config->item('currency') as $key => $value) : ?>
                        <option value="<?=$key?>" <?= ($key == $_SESSION['shipping_internal']['items'][$id]['insurance_currency']) ? 'selected' : ''; ?>><?=$value?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                        <input type="number" rel="issued_unit_value" name="item[<?= $id; ?>][issued_unit_value]" value="<?= $_SESSION['shipping_internal']['items'][$id]['issued_unit_value']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="number" rel="insurance_unit_value" name="item[<?= $id; ?>][insurance_unit_value]" value="<?= $_SESSION['shipping_internal']['items'][$id]['insurance_unit_value']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="text" rel="remarks" name="item[<?= $id; ?>][remarks]" value="<?= $_SESSION['shipping_internal']['items'][$id]['remarks']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="text" rel="awb_number" name="item[<?= $id; ?>][awb_number]" value="<?= $_SESSION['shipping_internal']['items'][$id]['awb_number']; ?>" class="form-control">
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