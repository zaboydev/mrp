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
            <input type="text" name="year" id="year" class="form-control" value="<?=$entity['year'];?>" readonly>
            <label for="year">Year</label>
          </div>
        </div>
        <div class="col-sm-6">
          <label>Person in Charge</label>

              <?php foreach (available_cost_centers(array('cost_center_name','id')) as $i => $cost_center):?>
                <div class="checkbox">
                  <input type="checkbox" name="cost_center_id[]" id="cost_center_id_<?=$i;?>" value="<?=$cost_center['id'];?>" <?=(in_array($cost_center['id'], annual_cost_centers($entity['year']))) ? 'checked' : '';?> >
                  <label for="cost_center_id_<?=$i;?>">
                    <?=$cost_center['cost_center_name'];?>
                  </label>
                </div>
              <?php endforeach;?>

        </div>
      </div>
    </div>

    <div class="card-foot">

      <div class="pull-right">
        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
          <i class="md md-save"></i>
        </button>
      </div>

      <input type="reset" name="reset" class="sr-only">
    </div>
  </div>

<?=form_close();?>
