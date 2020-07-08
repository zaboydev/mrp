<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form',
  'method'        => 'post',
  'enctype'      => 'multipart/form-data'
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
      <div class="col-sm-3">
        <div class="form-group">
          <label>Role as</label>

          <?php foreach ($this->config->item('levels_and_roles') as $key => $value) : ?>
            <?php if (config_item('auth_role') == 'SUPER ADMIN') : ?>
              <div class="radio">
                <input type="radio" name="auth_level" id="auth_level[<?= $key; ?>]" value="<?= $key; ?>" <?= ($entity['auth_level'] == $key) ? 'checked' : ''; ?>>
                <label for="auth_level[<?= $key; ?>]">
                  <?= str_replace('_', ' ', strtoupper($value)); ?>
                </label>
              </div>
            <?php else : ?>
              <?php if ($value != 'SUPER ADMIN') : ?>
                <div class="radio">
                  <input type="radio" name="auth_level" id="auth_level_<?= $key; ?>" value="<?= $key; ?>" <?= ($entity['auth_level'] == $key) ? 'checked' : ''; ?>>
                  <label for="auth_level_<?= $key; ?>">
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
              <input type="checkbox" name="category[]" id="category[<?= $i; ?>]" value="<?= $inventory['category']; ?>" <?= (in_array($inventory['category'], category_for_user_list($entity['username']))) ? 'checked' : ''; ?>>
              <label for="category[<?= $i; ?>]">
                <?= $inventory['category']; ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="form-group">
          <label>Status</label>

          <div class="radio">
            <input type="radio" name="banned" id="banned_0" value="0" <?= ($entity['banned'] == '0') ? 'checked' : ''; ?>>
            <label for="banned_0">Active</label>
          </div>

          <div class="radio">
            <input type="radio" name="banned" id="banned_1" value="1" <?= ($entity['banned'] == '1') ? 'checked' : ''; ?>>
            <label for="banned_1">Banned</label>
          </div>
        </div>
      </div>

      <div class="col-sm-5">
        <div class="form-group">
          <input type="text" class="form-control" name="person_name" id="person_name" value="<?= $entity['person_name']; ?>" required>
          <label for="person_name">Person Name</label>
        </div>

        <div class="form-group">
          <input type="text" class="form-control" name="username" id="username" value="<?= $entity['username']; ?>" placeholder="Username" maxlength="30" required data-validation-rule="unique" data-validation-url="<?= site_url('ajax/username_validation'); ?>" data-validation-exception="<?= $entity['username']; ?>">
          <label for="username">Username</label>
        </div>

        <div class="form-group">
          <input type="email" class="form-control" name="email" id="email" value="<?= $entity['email']; ?>" placeholder="Email" required data-validation-rule="unique" data-validation-url="<?= site_url('ajax/user_email_validation'); ?>" data-validation-exception="<?= $entity['email']; ?>">
          <label for="email">Email</label>
        </div>

        <div class="form-group">
          <?php if (config_item('levels_and_roles')==13 || config_item('levels_and_roles')==6 || config_item('levels_and_roles')==5) : ?>
            <select name="warehouse" id="warehouse" class="form-control" required="required">
              <?php foreach (available_warehouses(config_item('auth_warehouses')) as $base) : ?>
                <option value="<?= $base; ?>" <?= ($base == $entity['warehouse']) ? 'selected' : ''; ?>>
                  <?= $base; ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else : ?>
            <input type="text" class="form-control" name="warehouse" id="warehouse" value="<?= config_item('auth_warehouse'); ?>" readonly>
          <?php endif; ?>
          <label for="warehouse">Warehouse</label>
        </div>

        <div class="form-group">
          <a href="<?= site_url($module['route'] . '/upload_ttd/' . $entity['user_id']); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary btn-tooltip ink-reaction">
            <i class="md md-attach-file"></i> Upload Tanda Tangan
            <small class="top right">Upload Tanda Tangan</small>
          </a>
        </div>

      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <input type="password" class="form-control" name="passwd" id="passwd" data-validation-rule="match" data-validation-match="passconf" data-validation-label="Password" data-toggle="tooltip" data-placement="top" data-trigger="focus" data-original-title="fill this input will change the current password">
          <label for="passwd">New Password</label>
        </div>

        <div class="form-group">
          <input type="password" class="form-control" name="passconf" id="passconf" data-toggle="tooltip" data-placement="top" data-trigger="focus" data-original-title="this input must match with password above">
          <label for="passconf">Retype New Password</label>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <input type="hidden" name="id" id="id" value="<?= $entity['user_id']; ?>">
    <input type="hidden" name="username_exception" id="username_exception" value="<?= $entity['username']; ?>">
    <input type="hidden" name="email_exception" id="email_exception" value="<?= $entity['email']; ?>">

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
<script type="text/javascript">
  function popup(mylink, windowname) {
    var height = window.innerHeight;
    var widht;
    var href;

    if (screen.availWidth > 768) {
      width = 769;
    } else {
      width = screen.availWidth;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    var top = 0;
    // var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

    if (!window.focus) return true;
    else return false;
  }
</script>