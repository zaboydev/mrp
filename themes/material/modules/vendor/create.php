<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-create-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
  <div class="card-head style-primary-dark">
    <header>Create New <?= $module['label']; ?></header>

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

          <?php foreach (config_item('auth_inventory') as $i => $inventory) : ?>
            <div class="checkbox">
              <input type="checkbox" name="category[]" id="category[<?= $i; ?>]" value="<?= $inventory; ?>">
              <label for="category[<?= $i; ?>]">
                <?= $inventory; ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="form-group">
          <label>Currency</label>
          <?php foreach ($this->config->item('currency') as $key => $value) : ?>
          <div class="checkbox">
            <input type="checkbox" name="currency[]" id="item_currency_<?= $key; ?>" value="<?= $key; ?>">
            <label for="item_currency_<?= $key; ?>">
              <?= $value; ?>
            </label>
          </div>
          <?php endforeach; ?>
          
        </div>
      </div>

      <div class="col-sm-8 col-lg-9">
        <div class="row">
          <div class="col-sm-12 col-lg-6">
            <div class="form-group">
              <input type="text" name="vendor" id="vendor" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/vendor_validation'); ?>" data-validation-exception="" required>
              <label for="vendor"><?= $module['label']; ?></label>
            </div>

            <div class="form-group">
              <input type="text" name="code" id="code" class="form-control">
              <label for="code" required>Code</label>
            </div>

            <div class="form-group">
              <input type="email" name="email" id="email" class="form-control">
              <label for="email" required>Email</label>
            </div>

            <div class="form-group">
              <input type="text" name="phone" id="phone" class="form-control">
              <label for="phone" required>Phone</label>
            </div>

            <div class="form-group">
              <input type="text" name="country" id="country" class="form-control">
              <label for="country" value="INDONESIA" required>Country</label>
            </div>
          </div>

          <div class="col-sm-12 col-lg-6">
            <div class="form-group">
              <textarea name="address" id="address" class="form-control" rows="3"></textarea>
              <label for="address">Address</label>
            </div>

            <div class="form-group">
              <textarea name="notes" id="notes" class="form-control"></textarea>
              <label for="notes">notes</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and update">
      <i class="md md-save"></i>
    </button>
  </div>
</div>

<?= form_close(); ?>