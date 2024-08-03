<?=form_open(site_url($module['route'] .'/save'), array(
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
            <input type="text" name="cost_center_name" id="cost_center_name" class="form-control" value="<?=$entity['cost_center_name'];?>" readonly>
            <label for="cost_center_name">Cost Center Name</label>
          </div>
        </div>
        <div class="col-sm-6">
          <label>Person in Charge</label>

              <?php foreach (available_user(array('person_name', 'username')) as $i => $user):?>
                <div class="checkbox">
                  <input type="checkbox" name="user[]" id="user_<?=$i;?>" value="<?=$user['username'];?>" <?=(in_array($user['username'], user_in_annual_cost_centers_list($entity['id']))) ? 'checked' : '';?> >
                  <label for="user_<?=$i;?>">
                    <?=$user['person_name'];?>
                  </label>
                </div>
              <?php endforeach;?>

        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="text" name="id" id="id" value="<?=$entity['id'];?>">
      

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
