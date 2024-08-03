<?=form_open(site_url($module['route'] .'/save_change_item'), array(
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
            <div class="col-sm-6 col-lg-8">
              <div class="form-group">
                <input type="text" name="pr_number" id="pr_number" class="form-control" required value="<?=($entity['pr_number']);?>" readonly>
                <label for="description">Document No.</label>
              </div>

              <div class="form-group">
                <input type="text" name="status" id="status" class="form-control" required value="<?=$entity['status'];?>" readonly>
                <label for="part_number">Status</label>
              </div>
            </div>
            <div class="col-sm-6 col-lg-8">
              <div class="form-group">
                <input type="text" name="description" id="description" class="form-control" autofocus required value="<?=htmlspecialchars($entity['description']);?>">
                <label for="description">Description</label>
              </div>

              <div class="form-group">
                <input type="text" name="part_number" id="part_number" class="form-control" required data-validation-rule="unique" data-validation-url="<?=site_url('ajax/part_number_validation');?>" data-validation-exception="<?=$entity['part_number'];?>" value="<?=$entity['part_number'];?>">
                <label for="part_number">Part Number</label>
              </div>

              <div class="form-group">
                <input type="text" name="serial_number" id="serial_number" class="form-control" value="">

                <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
                <label for="serial_number">Serial Number</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>   


    <div class="card-foot">

      <div class="pull-right">
        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
          <i class="md md-save"></i>
        </button>
        
      </div>
    </div>
  </div>


<?=form_close();?>
