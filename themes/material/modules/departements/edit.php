c <?=form_open(site_url($module['route'] .'/save'), array(
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
        <div class="col-sm-6">
          <div class="form-group">
            <input type="text" name="department_name" id="department_name" class="form-control" autofocus required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/department_name_validation');?>" data-validation-exception="<?=$entity['department_name'];?>" value="<?=$entity['department_name'];?>">
            <label for="department_name">Department Name</label>
          </div>

          <div class="form-group">
            <input type="text" name="department_code" id="department_code" class="form-control" required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/department_code_validation');?>" data-validation-exception="<?=$entity['department_code'];?>" value="<?=$entity['department_code'];?>">
            <label for="department_code">Department Code</label>
          </div>
          <div class="form-group">
            <select name="division_id" id="division_id" class="form-control" required>
              <?php foreach (get_divisions() as $division) : ?>
                <option value="<?= $division['id']; ?>" <?= ($division['id'] == $entity['division_id']) ? 'selected' : ''; ?>>
                  <?= $division['division_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <label>Division</label>
          </div>
          <div class="form-group">
            <textarea name="notes" id="notes" class="form-control"><?=$entity['notes'];?></textarea>
            <label for="notes">Notes</label>
          </div>
          
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label>Head Department</label>
            <select name="head" id="head" class="form-control hide">
              <option value="">Not Set</option>
              <?php foreach (available_user(array('person_name', 'username')) as $i => $user) : ?>
                <option value="<?= $user['username']; ?>" <?= ($user['username'] == user_in_head_department($entity['id'])) ? 'selected' : ''; ?>>
                  <?= $user['person_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php foreach (available_user(array('person_name', 'username')) as $i => $user):?>
            <div class="checkbox">
              <input type="checkbox" name="head_departments[]" id="user[<?=$i;?>]" value="<?=$user['username'];?>" <?=(in_array($user['username'], user_in_head_department_list($entity['id']))) ? 'checked' : '';?> >
              <label for="user[<?=$i;?>]">
                <?=$user['person_name'];?>
              </label>
            </div>
            <?php endforeach;?>
          </div>

        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="department_name_exception" id="department_name_exception" value="<?=$entity['department_name'];?>">
      <input type="hidden" name="department_code_exception" id="department_code_exception" value="<?=$entity['department_code'];?>">

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
