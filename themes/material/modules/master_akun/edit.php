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
      <div class="col-sm-4 col-lg-3">

      </div>

      <div class="col-sm-8 col-lg-9">
        <div class="row">
          <div class="col-sm-12 col-lg-6">

            <div class="form-group">
              <input type="text" name="description_akun" id="description_akun" class="form-control" autofocus required value="<?= $entity['description_akun']; ?>" readonly>
              <label for="code">Account Setting Name</label>
            </div>

            <div class="form-group">
              <select name="group_id" id="group_id" class="form-control" required>
                <option value="">Select Code of Account</option>
                <?php foreach (available_item_groups_2() as $group) : ?>
                  <option value="<?= $group['id']; ?>" data-coa="<?= $group['coa']; ?>" data-group="<?= $group['group']; ?>" <?= ($group['id'] == $entity['group_id']) ? 'selected' : ''; ?>>
                    <?= $group['coa']; ?> - <?= $group['group']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="minimum_quantity">Code of Account</label>
            </div>
            <!-- <div class="col-sm-6"> -->
              <div class="form-group">
                <textarea name="description" id="description" class="form-control"><?= $entity['description']; ?></textarea>
                <label for="description">Notes</label>
              </div>
            <!-- </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <input type="hidden" name="id" id="id" value="<?= $entity['id']; ?>">
    <input type="hidden" name="group" id="group" value="<?= $entity['group']; ?>">
    <input type="hidden" name="coa" id="coa" value="<?= $entity['coa']; ?>">

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