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
            <input type="text" name="kode_akunting" id="kode_akunting" class="form-control" autofocus required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/warehouse_validation');?>" data-validation-exception="<?=$entity['kode_akunting'];?>" value="<?=$entity['kode_akunting'];?>">
            <label for="kode_akunting"><?=$module['label'];?></label>
          </div>

          <div class="form-group">
            <input type="text" name="description" id="description" class="form-control" autofocus required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/warehouse_code_validation');?>" data-validation-exception="<?=$entity['code'];?>" value="<?=$entity['description'];?>">
            <label for="description">Description</label>
          </div>

          <div class="form-group">
            <textarea name="remarks" id="remarks" class="form-control"><?=$entity['remarks'];?></textarea>
            <label for="remarks">Remarks</label>
          </div>
        </div>
        
      </div>
    </div>

    <div class="card-foot">
      <input type="text" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="kodeAkunting_exception" id="kodeAkunting_exception" value="<?=$entity['kode_akunting'];?>">

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
