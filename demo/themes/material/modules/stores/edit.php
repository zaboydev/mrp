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

            <?php foreach (config_item('auth_inventory') as $i => $category):?>
              <div class="radio">
                <input type="radio" name="category" id="item_category_<?=$i;?>" value="<?=$category;?>" <?=($category == $entity['category']) ? 'checked' : '';?> required>
                <label for="item_category_<?=$i;?>">
                  <?=$category;?>
                </label>
              </div>
            <?php endforeach;?>
          </div>
        </div>

        <div class="col-sm-8 col-lg-9">
          <div class="row">
            <div class="col-sm-12 col-lg-6">
              <div class="form-group">
                <input type="text" name="stores" id="stores" class="form-control" data-validation-rule="unique" data-validation-url="<?=site_url('ajax/stores_validation');?>" data-validation-exception="<?=$entity['stores'];?>" value="<?=$entity['stores'];?>" required>
                <label for="stores"><?=$module['label'];?></label>
              </div>

              <div class="form-group">
                <select name="warehouse" id="warehouse" class="form-control input-sm" required>
                  <?php foreach (config_item('auth_warehouses') as $base):?>
                    <option value="<?=$base;?>" <?=($base == $entity['warehouse']) ? 'selected' : '';?> >
                      <?=$base;?>
                    </option>
                  <?php endforeach;?>
                </select>
                <label for="warehouse">Warehouse</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-6">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control"><?=$entity['notes'];?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="stores_exception" id="stores_exception" value="<?=$entity['stores'];?>">

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
