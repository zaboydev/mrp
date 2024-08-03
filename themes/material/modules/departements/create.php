<?=form_open(site_url($module['route'] .'/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-create-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
));?>

  <div class="card style-default-bright">
    <div class="card-head style-primary-dark">
      <header>Create New <?=$module['label'];?></header>

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
            <input type="text" name="department_name" id="department_name" class="form-control" autofocus required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/department_name_validation');?>" data-validation-exception="" value="">
            <label for="department_name">Department Name</label>
          </div>

          <div class="form-group">
            <input type="text" name="department_code" id="department_code" class="form-control" required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/department_code_validation');?>" data-validation-exception="" value="">
            <label for="department_code">Department Code</label>
          </div>
          <div class="form-group">
            <select name="division_id" id="division_id" class="form-control" required>
              <?php foreach (get_divisions() as $division) : ?>
                <option value="<?= $division['id']; ?>">
                  <?= $division['division_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <label>Division</label>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <textarea name="notes" id="notes" class="form-control"></textarea>
            <label for="notes">Notes</label>
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

<?=form_close();?>
