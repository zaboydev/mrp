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
      <div class="col-sm-3">
        <div class="form-group">
          <label>Role as</label>

          <?php foreach ($this->config->item('levels_and_roles') as $key => $value) : ?>
            <?php if (config_item('auth_role')=='SUPER ADMIN') : ?>
              <div class="radio">
                <input type="radio" name="auth_level" id="auth_level[<?= $key; ?>]" value="<?= $key; ?>">
                <label for="auth_level[<?= $key; ?>]">
                  <?= str_replace('_', ' ', strtoupper($value)); ?>
                </label>
              </div>
            <?php else : ?>
              <?php if ($value != 'SUPER ADMIN') : ?>
                <div class="radio">
                  <input type="radio" name="auth_level" id="auth_level[<?= $key; ?>]" value="<?= $key; ?>">
                  <label for="auth_level[<?= $key; ?>]">
                    <?= str_replace('_', ' ', strtoupper($value)); ?>
                  </label>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <div class="form-group">
          <label>Category</label>

          <?php foreach (available_categories_for_user() as $i => $inventory) : ?>
            <div class="checkbox">
              <input type="checkbox" name="category[]" id="category[<?= $i; ?>]" value="<?= $inventory['category']; ?>">
              <label for="category[<?= $i; ?>]">
                <?= $inventory['category']; ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="col-sm-5">
        <div class="form-group">
          <input type="text" class="form-control" name="person_name" id="person_name" required>
          <label for="person_name">Person Name</label>
        </div>

        <div class="form-group">
          <input type="text" class="form-control" name="username" id="username" maxlength="30" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/username_validation'); ?>" data-validation-exception="" required>
          <label for="username">Username</label>
        </div>

        <div class="form-group">
          <input type="email" class="form-control" name="email" id="email" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/user_email_validation'); ?>" data-validation-exception="" required>
          <label for="email">Email</label>
        </div>

        <div class="form-group">
          <select name="warehouse" id="warehouse" class="form-control" required>
            <?php foreach (available_warehouses() as $base) : ?>
              <option value="<?= $base; ?>">
                <?= $base; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <label for="warehouse">Warehouse</label>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="form-group">
          <input type="password" class="form-control input-sm" name="passwd" id="passwd" data-validation-rule="match" data-validation-match="passconf" data-validation-label="Password" required>
          <label for="passwd">Password</label>
        </div>

        <div class="form-group">
          <input type="password" class="form-control input-sm" name="passconf" id="passconf">
          <label for="passconf">Retype Password</label>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
      <i class="md md-save"></i>
    </button>
  </div>
</div>

<?= form_close(); ?>