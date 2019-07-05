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
          <div class="col-sm-6 col-lg-4">
            <div class="form-group">
              <label>Group</label>

              <?php foreach (available_item_groups(config_item('auth_inventory')) as $i => $group):?>
                <div class="radio">
                  <input type="radio" name="group" id="group_<?=$i;?>" value="<?=$group['group'];?>" required>
                  <label for="group_<?=$i;?>">
                    <?=$group['group'];?>
                  </label>
                </div>
              <?php endforeach;?>
            </div>
          </div>
          <div class="col-sm-6 col-lg-8">
            <div class="form-group">
              <input type="text" name="description" id="description" class="form-control" required>
              <label for="description">Description</label>
            </div>

            <div class="form-group">
              <input type="text" name="part_number" id="part_number" class="form-control" data-validation-rule="unique" data-validation-url="<?=site_url('ajax/part_number_validation');?>" data-validation-exception="" required>
              <label for="part_number">Part Number</label>
            </div>

            <div class="form-group">
              <input type="text" name="alternate_part_number" id="alternate_part_number" class="form-control">
              <label for="alternate_part_number">Alternate Part Number</label>
            </div>

            <div class="form-group">
              <input type="text" name="minimum_quantity" id="minimum_quantity" class="form-control" required>
              <label for="minimum_quantity">Minimum Quantity</label>
            </div>

            <div class="form-group">
              <select name="unit" id="unit" class="form-control" required>
                <?php foreach (available_units() as $unit):?>
                  <option value="<?=$unit;?>">
                    <?=$unit;?>
                  </option>
                <?php endforeach;?>
              </select>
              <label for="unit">Unit of Measurement</label>
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
