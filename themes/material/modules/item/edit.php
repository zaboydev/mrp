<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
  <div class="card-head style-primary-dark">
    <header>Edit <?= $module['label']; ?></header>

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
          <div class="col-sm-6 col-lg-4">
            <!-- <div class="form-group">
                <label>Group</label>

                <?php foreach (available_item_groups(config_item('auth_inventory')) as $i => $group) : ?>
                  <div class="radio">
                    <input type="radio" name="group" id="item_group_<?= $i; ?>" value="<?= $group; ?>" <?= ($group === $entity['group']) ? 'checked' : ''; ?> required>
                    <label for="item_group_<?= $i; ?>">
                      <?= $group; ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div> -->
          </div>
          <div class="col-sm-6 col-lg-8">
            <div class="form-group">
              <input type="text" name="description" id="description" class="form-control" autofocus required value="<?= htmlspecialchars($entity['description']); ?>">
              <label for="description">Description</label>
            </div>

            <div class="form-group">
              <input type="text" name="part_number" id="part_number" class="form-control" required data-validation-rule="unique" data-validation-url="<?= site_url('ajax/part_number_validation'); ?>" data-validation-exception="<?= $entity['part_number']; ?>" value="<?= $entity['part_number']; ?>">
              <label for="part_number">Part Number</label>
            </div>

            <div class="form-group hide">
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?= $entity['serial_number']; ?>">
              <label for="serial_number">Serial Number</label>
            </div>

            <div class="form-group">
              <input type="text" name="alternate_part_number" id="alternate_part_number" class="form-control" value="<?= $entity['alternate_part_number']; ?>">
              <label for="alternate_part_number">Alternate Part Number</label>
            </div>

            <div class="form-group">
              <input type="text" name="minimum_quantity" id="minimum_quantity" class="form-control" value="<?= $entity['min_qty']; ?>" required>
              <label for="minimum_quantity">Minimum Quantity</label>
            </div>

            <div class="form-group">
              <input type="text" name="kode_stok" id="kode_stok" class="form-control" value="<?= $entity['kode_stok']; ?>">
              <label for="minimum_quantity">Kode Stok</label>
            </div>
            <div class="form-group">
              <select name="kode_pemakaian" id="kode_pemakaian" class="form-control" required>
                <option value="">Pilih Kode Pemakaian</option>
                <?php foreach (master_coa() as $group) : ?>
                  <option value="<?= $group['coa']; ?>" <?= ($group['coa'] == $entity['kode_pemakaian']) ? 'selected' : ''; ?>>
                    <?= $group['coa']; ?> - <?= $group['group']; ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <label for="minimum_quantity">Kode Pemakaian</label>
            </div>
            <div class="form-group">
              <select name="unit" id="unit" class="form-control" required>
                <?php foreach (available_units() as $unit) : ?>
                  <option value="<?= $unit; ?>" <?= ($unit == $entity['unit']) ? 'selected' : ''; ?>>
                    <?= $unit; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="unit">Unit of Measurement</label>
            </div>

            <div class="form-group">
              <select name="group" id="group" class="form-control" required>
                <option value="">Pilih Group</option>
                <?php foreach (available_item_groups(config_item('auth_inventory')) as $i => $group) : ?>
                  <option value="<?= $group; ?>" <?= ($group == $entity['group']) ? 'selected' : ''; ?>>
                    <?= $group; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="group">Group</label>
            </div>

            <div class="form-group hide">
              <label>Mixable</label>

              <div class="radio">
                <input type="checkbox" name="mixable" id="mixable" <?= ('t' === $entity['mixable']) ? 'checked' : ''; ?>>
                <label for="mixable">
                  Item can be mixed with other
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-lg-4">
        <div class="form-group">
          <textarea name="notes" id="notes" class="form-control" rows="4"><?= $entity['notes']; ?></textarea>
          <label for="notes">Notes</label>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <input type="hidden" name="id" id="id" value="<?= $entity['id']; ?>">
    <input type="hidden" name="part_number_exception" id="part_number_exception" value="<?= $entity['part_number']; ?>">
    <input type="hidden" name="serial_number_exception" id="serial_number_exception" value="<?= $entity['serial_number']; ?>">
    <input type="hidden" name="description_exception" id="description_exception" value="<?= htmlspecialchars($entity['description']); ?>">

    <?php if (is_granted($module, 'delete')) : ?>
      <a href="<?= site_url($module['route'] . '/delete'); ?>" class="btn btn-floating-action btn-danger btn-xhr-delete ink-reaction" id="modal-delete-data-button" data-title="delete">
        <i class="md md-delete"></i>
      </a>
    <?php endif; ?>

    <div class="pull-right">
      <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
        <i class="md md-save"></i>
      </button>
    </div>

    <input type="reset" name="reset" class="sr-only">
  </div>
</div>

<?= form_close(); ?>