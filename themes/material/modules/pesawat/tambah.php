<?=form_open(site_url($module['route'] .'/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
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
            <input type="text" name="nama_pesawat" id="nama_pesawat" class="form-control" autofocus>
            <label for="nama_pesawat">Aircraft Register Number</label>
          </div>

          <div class="form-group">
            <input type="date" name="date_of_manufacture" id="date_of_manufacture" class="form-control">
            <label for="date_of_manufacture">Date of Manufacture</label>
          </div>

          <div class="form-group">
            <input type="text" name="aircraft_serial_number" id="aircraft_serial_number" class="form-control">
            <label for="aircraft_serial_number">Aircraft Serial Number</label>
          </div>

          <div class="form-group">
            <input type="text" name="aircraft_type" id="aircraft_type" class="form-control">
            <label for="aircraft_type">Type</label>
          </div>

          <div class="form-group">
            <select name="engine_type" id="engine_type" class="form-control" required>
              <option value="single">Single Engine</option>
              <option value="multi">Multi Engine</option>
            </select>
            <label for="base">Engine Type</label>
          </div>

          <div class="form-group">
            <input type="text" name="engine_serial_number" id="engine_serial_number" class="form-control">
            <label for="engine_serial_number">Engine Serial Number</label>
          </div>

          <div class="form-group hide multi_engine">
            <input type="text" name="engine_serial_number_2" id="engine_serial_number_2" class="form-control">
            <label for="engine_serial_number_2">Engine Serial Number 2</label>
          </div>

          <div class="form-group">
            <input type="text" name="propeler_serial_number" id="propeler_serial_number" class="form-control">
            <label for="propeler_serial_number">Prepoller Serial Number</label>
          </div>

          <div class="form-group hide multi_engine">
            <input type="text" name="propeler_serial_number_2" id="propeler_serial_number_2" class="form-control">
            <label for="propeler_serial_number">Prepoller Serial Number 2</label>
          </div>

          <div class="form-group">
            <select name="base" id="select_base" class="form-control" required>
              <option value=""></option>
              <?php foreach (available_warehouses_alternate_name() as $base) : ?>
                <option value="<?= $base['warehouse']; ?>">
                  <?= $base['warehouse']; ?> 
                  <?php if($base['alternate_warehouse_name']!=NULL): ?>
                  (<?= $base['alternate_warehouse_name']; ?>)
                  <?php endif;?>
                </option>
              <?php endforeach; ?>
            </select>
            <label for="select_base">Base</label>
          </div>
          <div class="form-group">
            <textarea name="keterangan" id="keterangan" class="form-control" rows="4"></textarea>
            <label for="keterangan">Keterangan</label>
          </div>
        </div>        
        <div class="col-sm-6">
          <div class="form-group">
            <input type="text" name="fuel_capacity_usage" id="fuel_capacity_usage" class="form-control">
            <label for="fuel_capacity_usage">Fuel Capacity Usage</label>
          </div>

          <div class="form-group">
            <label for="fuel_capacity_mix">Fuel Capacity Mix</label>
            <div class="radio">
              <input type="radio" name="fuel_capacity_mix" id="mix" value="mix">
              <label for="mix">
                MIX
              </label>
            </div>
            <div class="radio">
              <input type="radio" name="fuel_capacity_mix" id="avgas" value="avgas">
              <label for="avgas">
                AVGAS
              </label>
            </div>
          </div>
          <div class="form-group" style="padding-top: 25px;">
            <label for="instrument_nf">Instrument NF</label>
            <select style="width: 100%" multiple="multiple" name="instrument_nf[]" id="instrument_nf" class="form-control select2" required>
            <?php foreach ($this->config->item('instrument_nf') as $instrument_nf) : ?>
              <option value="<?= $instrument_nf; ?>">
                <?= $instrument_nf; ?>
              </option>
            <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group" style="padding-top: 25px;">
            <label for="instrument_avionic">Instrument Avionic</label>
            <select style="width: 100%" multiple="multiple" name="instrument_avionic[]" id="instrument_avionic" class="form-control select2" required>
            <?php foreach ($this->config->item('instrument_avionic') as $instrument_avionic) : ?>
              <option value="<?= $instrument_avionic; ?>">
                <?= $instrument_avionic; ?>
              </option>
            <?php endforeach; ?>
            </select>
          </div>

        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          
        </div>        
      </div>      
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
      <input type="hidden" name="kodeAkunting_exception" id="kodeAkunting_exception" value="<?=$entity['kode_akunting'];?>">

      <div class="pull-right">
        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
          <i class="md md-save"></i>
        </button>
      </div>

      <input type="reset" name="reset" class="sr-only">
    </div>
  </div>

<?=form_close();?>

<script>
  $('.select2').select2();
  $('#engine_type').on("change", function(e) {
    var value = $(this).val();

    if(value=='multi'){
      if($('.multi_engine').hasClass('hide')==true){
        $('.multi_engine').removeClass('hide');
        $('[name="engine_serial_number_2"]').attr('required',true);
        $('[name="propeler_serial_number_2"]').attr('required',true);
      }
    }else{
      if($('.multi_engine').hasClass('hide')==false){
        $('.multi_engine').addClass('hide');
        $('[name="engine_serial_number_2"]').attr('required',false);
        $('[name="propeler_serial_number_2"]').attr('required',false);
      }
    }
    
  });
</script>
