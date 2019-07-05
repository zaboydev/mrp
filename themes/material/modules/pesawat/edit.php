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
            <input type="text" name="nama_pesawat" id="nama_pesawat" class="form-control" value="<?=$entity['nama_pesawat'];?>" data-validation-exception="<?=$entity['nama_pesawat'];?>"  autofocus>
            <label for="nama_pesawat">Nama Pesawat</label>
          </div>
        </div>        
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <textarea name="keterangan" id="keterangan" class="form-control" rows="4" data-validation-exception="<?=$entity['keterangan'];?>"><?=$entity['keterangan'];?></textarea>
            <label for="keterangan">Keterangan</label>
          </div>
        </div>        
      </div>      
    </div>

    <div class="card-foot">
      <input type="text" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="nama_pesawat_exception" id="nama_pesawat_exception" value="<?=$entity['nama_pesawat'];?>">

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
