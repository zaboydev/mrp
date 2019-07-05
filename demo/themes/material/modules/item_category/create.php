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
        <div class="col-sm-12 col-lg-8">
          <div class="row">
            <div class="col-sm-6 col-lg-4">
              <label>Person in Charge</label>

              <?php foreach (available_user(array('person_name', 'username'), array(6, 7, 8)) as $i => $user):?>
                <div class="checkbox">
                  <input type="checkbox" name="user[]" id="user[<?=$i;?>]" value="<?=$user['username'];?>">
                  <label for="user[<?=$i;?>]">
                    <?=$user['person_name'];?>
                  </label>
                </div>
              <?php endforeach;?>
            </div>
            <div class="col-sm-6 col-lg-8">
              <div class="form-group">
                <input type="text" name="category" id="category" class="form-control" data-validation-rule="unique" data-validation-url="<?=site_url('ajax/item_category_validation');?>" data-validation-exception="" required>
                <label for="category"><?=$module['label'];?></label>
              </div>

              <div class="form-group">
                <input type="text" name="code" id="code" class="form-control" data-validation-rule="unique" data-validation-url="<?=site_url('ajax/item_category_code_validation');?>" data-validation-exception="" required>
                <label for="code">Code</label>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-lg-4">
          <div class="form-group">
            <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
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
