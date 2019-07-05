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
            <input type="text" name="date" id="date" class="form-control" data-validation-exception="<?=$entity['date'];?>" value="<?=$entity['date'];?>" readonly>
            <label for="kurs_dollar">Tanggal Aktif</label>
          </div>
        </div>        
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <input type="text" name="kurs_dollar" id="kurs_dollar" class="form-control" autofocus data-validation-exception="<?=$entity['kurs_dollar'];?>" value="<?=$entity['kurs_dollar'];?>">
            <label for="kurs_dollar"><?=$module['label'];?></label>
          </div>
        </div>        
      </div>

    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
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
