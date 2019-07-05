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
            <input type="text" name="unit" id="unit" class="form-control" data-validation-rule="unique" data-validation-url="<?=site_url('ajax/unit_validation');?>" data-validation-exception="" autofocus required>
            <label for="unit">Unit</label>
          </div>

          <div class="form-group">
            <input type="text" name="description" id="description" class="form-control" required>
            <label for="description">Description</label>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            <label for="description">Notes</label>
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
