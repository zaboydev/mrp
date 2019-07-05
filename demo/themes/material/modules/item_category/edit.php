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
        <div class="col-sm-12 col-lg-8">
          <div class="row">
            <div class="col-sm-6 col-lg-4">
              <label>Person in Charge</label>

              <?php foreach (available_user(array('person_name', 'username'), array(6, 7, 8)) as $i => $user):?>
                <div class="checkbox">
                  <input type="checkbox" name="user[]" id="user_<?=$i;?>" value="<?=$user['username'];?>" <?=(in_array($user['username'], user_in_category_list($entity['category']))) ? 'checked' : '';?> >
                  <label for="user_<?=$i;?>">
                    <?=$user['person_name'];?>
                  </label>
                </div>
              <?php endforeach;?>
            </div>
            <div class="col-sm-6 col-lg-8">
              <div class="form-group">
                <input type="text" name="category" id="category" class="form-control" autofocus required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/item_category_validation');?>" data-validation-exception="<?=$entity['category'];?>" value="<?=$entity['category'];?>">
                <label for="category"><?=$module['label'];?></label>
              </div>

              <div class="form-group">
                <input type="text" name="code" id="code" class="form-control" autofocus required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/item_category_code_validation');?>" data-validation-exception="<?=$entity['code'];?>" value="<?=$entity['code'];?>">
                <label for="code">Code</label>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-lg-4">
          <div class="form-group">
            <textarea name="notes" id="notes" class="form-control" rows="4"><?=$entity['notes'];?></textarea>
            <label for="notes">Notes</label>
          </div>
        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="item_category_exception" id="item_category_exception" value="<?=$entity['category'];?>">
      <input type="hidden" name="code_exception" id="code_exception" value="<?=$entity['code'];?>">

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
