<?=form_open(site_url($module['route'] .'/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-create-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
));?>

  <div class="card style-default-bright">
    <div class="card-head style-primary-dark">
      <header>Create Account</header>

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
                <label>Type</label>
                <select name="category" id="category" class="form-control" required>
                <?php foreach ($this->config->item('account_types') as $key => $value):?>
                  <option value="<?= $value;?>"><?= $value;?></option>
                <?php endforeach;?>  
                </select>            
              </div>
              <div class="form-group">
                <input type="text" name="coa" id="coa" class="form-control" data-validation-rule="unique" data-validation-exception="" required>
                <label for="coa">Account Code</label>
              </div>

              <div class="form-group">
                <input type="text" name="group" id="group" class="form-control" data-validation-rule="unique" data-validation-exception="" required>
                <label for="group">Account Name</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-6">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control"></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
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
