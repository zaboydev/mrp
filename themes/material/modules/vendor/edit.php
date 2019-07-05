<?=form_open(site_url($module['route'] .'/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
));?>

  <div class="card style-default-bright">
    <div class="card-head style-primary-dark">
      <header>Edit <?=$module['label'];?></header>

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
        <div class="col-sm-4 col-lg-3">
          <div class="form-group">
            <label>Category</label>

            <?php foreach (config_item('auth_inventory') as $i => $inventory):?>
              <div class="checkbox">
                <input type="checkbox" name="category[]" id="item_category_<?=$i;?>" value="<?=$inventory;?>" <?=(in_array($inventory, category_for_vendor_list($entity['vendor']))) ? 'checked' : '' ;?>>
                <label for="item_category_<?=$i;?>">
                  <?=$inventory;?>
                </label>
              </div>
            <?php endforeach;?>
          </div>
        </div>

        <div class="col-sm-8 col-lg-9">
          <div class="row">
            <div class="col-sm-12 col-lg-6">
              <div class="form-group">
                <input type="text" name="vendor" id="vendor" class="form-control" data-validation-rule="unique" data-validation-url="<?=site_url('ajax/vendor_validation');?>" data-validation-exception="<?=$entity['vendor'];?>" value="<?=$entity['vendor'];?>" required>
                <label for="vendor"><?=$module['label'];?></label>
              </div>

              <div class="form-group">
                <input type="text" name="code" id="code" class="form-control" value="<?=$entity['code'];?>">
                <label for="code" required>Code</label>
              </div>

              <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" value="<?=$entity['email'];?>">
                <label for="email" required>Email</label>
              </div>

              <div class="form-group">
                <input type="text" name="phone" id="phone" class="form-control" value="<?=$entity['phone'];?>">
                <label for="phone" required>Phone</label>
              </div>

              <div class="form-group">
                <input type="text" name="country" id="country" class="form-control" value="<?=$entity['country'];?>">
                <label for="country" required>Country</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-6">
              <div class="form-group">
                <textarea name="address" id="address" class="form-control" rows="3"><?=$entity['address'];?></textarea>
                <label for="address">Address</label>
              </div>

              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control"><?=$entity['notes'];?></textarea>
                <label for="notes">notes</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="vendor_exception" id="vendor_exception" value="<?=$entity['vendor'];?>">

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
