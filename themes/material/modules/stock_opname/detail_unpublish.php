<?=form_open(site_url($module['route'] .'/save_unpublish'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-create-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
));?>

  <div class="card style-default-bright">
    <div class="card-head style-primary-dark">
      <header>Update Unpublish Stock Opname</header>

      <div class="tools">
        <div class="btn-group">
          <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
            <i class="md md-close"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-sm-12 col-lg-8">
          <div class="row">
            <div class="col-sm-6 col-lg-8">            

              <div class="form-group">
                <input type="text" name="part_number" id="part_number" class="form-control" required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/part_number_validation');?>" data-validation-exception="<?=$entity['part_number'];?>" value="<?=$entity['part_number'];?>" readonly>
                <label for="part_number">Part Number</label>
              </div>

              <div class="form-group">
                <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?=$entity['serial_number'];?>" readonly>
                <label for="serial_number">Serial Number</label>
              </div>

              <div class="form-group">
                <input type="text" name="description" id="description" class="form-control" required value="<?=htmlspecialchars($entity['description']);?>" readonly>
                <label for="description">Description</label>
              </div>
              <div class="form-group">
                <input type="text" name="stores" id="stores" class="form-control" required value="<?=htmlspecialchars($entity['stores']);?>" readonly>
                <label for="description">Stores</label>
              </div>
              <?php if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'):?>
              <div class="form-group">
                <input type="text" name="total_value_sistem" id="total_value_sistem" class="form-control" value="<?=$entity['total_value_sistem'];?>" readonly>
                <label for="minimum_quantity">Total Value</label>
              </div>
              <div class="form-group">
                <input type="text" name="average_value_sistem" id="average_value_sistem" class="form-control" value="<?=$entity['average_value_sistem'];?>" readonly>
                <label for="minimum_quantity">Average Value Sistem</label>
              </div>
              <?php endif;?>
              <?php if (config_item('auth_role') == 'SUPERVISOR'):?>
              <div class="form-group">
                <input type="text" name="qty_sistem" id="qty_sistem" class="form-control" value="<?=$entity['qty_sistem'];?>" readonly>
                <label for="minimum_quantity">Stock Qty</label>
              </div>
              <?php endif;?>

              <div class="form-group">
                <input type="text" name="qty_actual" id="qty_actual" class="form-control" value="<?=$entity['qty_actual'];?>" autofocus>
                <label for="minimum_quantity">Qty Actual</label>
              </div>
              <div class="form-group">
                <input type="text" name="qty_actual" id="qty_actual" class="form-control" value="<?=$entity['qty_actual'];?>" autofocus>
                <label for="minimum_quantity">Quantity Balance</label>
              </div>
              <div class="form-group">
                <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                <label for="remarks">Remarks</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
     

      <?php if (is_granted($module, 'delete')):?>
        <a href="<?=site_url($module['route']. '/delete');?>" class="btn btn-floating-action btn-danger btn-xhr-delete ink-reaction" id="modal-delete-data-button" data-title="delete">
          <i class="md md-delete"></i>
        </a>
      <?php endif;?>

      <div class="pull-right">
        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
          <i class="md md-save"></i>
        </button>
      </div>

      <input type="reset" name="reset" class="sr-only">
    </div>
  </div>

<?=form_close();?>
