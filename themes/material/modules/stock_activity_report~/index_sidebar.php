<div class="form force-padding">
  <div class="form-group">
    <label for="filter_item_group">Group</label>
    <select class="form-control input-sm filter_dropdown" data-column="1" id="filter_item_group">
      <option value="">
        All Group
      </option>

      <?php foreach ($item_groups as $group):?>
        <option value="<?=$group['description'];?>">
          <?=$group['description'];?>
        </option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_warehouse">Warehouse</label>
    <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_warehouse">
      <option value="">
        All Warehouse
      </option>

      <?php foreach ($warehouses as $base):?>
        <option value="<?=$base['warehouse'];?>">
          <?=$base['warehouse'];?>
        </option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_condition">Item Condition</label>
    <select class="form-control input-sm filter_dropdown" data-column="6" id="filter_condition">
      <option value="">
        None
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
    <select class="form-control input-sm filter_dropdown" data-column="7" id="filter_quantity">
      <option value="">
        None
      </option>
      <option value="zero">
        0.00 (Quantity is zero)
      </option>
      <option value="gt_zero">
        Not 0 (greater than zero)
      </option>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_unit">Unit</label>
    <select class="form-control input-sm filter_dropdown" data-column="9" id="filter_unit">
      <option value="">
        All Unit
      </option>

      <?php foreach ($units as $unit):?>
        <option value="<?=$unit['unit'];?>">
          <?=$unit['unit'];?>
        </option>
      <?php endforeach;?>
    </select>
  </div>
</div>
