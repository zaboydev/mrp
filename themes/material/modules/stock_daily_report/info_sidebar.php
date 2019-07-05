<div class="form force-padding">
  <div class="form-group">
    <label for="filter_warehouse">Warehouse</label>
    <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_warehouse">
      <option value="">
        Not Filtered
      </option>

      <?php foreach (available_warehouses(config_item('auth_warehouses')) as $base):?>
        <option value="<?=$base;?>">
          <?=$base;?>
        </option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_condition">Item Condition</label>
    <select class="form-control input-sm filter_dropdown" data-column="6" id="filter_condition">
      <option value="">
        Not Filtered
      </option>

      <?php foreach (config_item('condition') as $key => $value):?>
        <option value="<?=$key;?>">
          <?=$value;?>
        </option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_quantity">Quantity</label>
    <select class="form-control input-sm filter_dropdown" data-column="11" id="filter_quantity">
      <option value="">
        Not Filtered
      </option>
      <option value="minus">
        Minus (Issued)
      </option>
      <option value="plus">
        Plus (Receipt)
      </option>
    </select>
  </div>
</div>
