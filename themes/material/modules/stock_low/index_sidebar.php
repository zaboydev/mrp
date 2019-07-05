<div class="form force-padding">
  <div class="form-group">
    <label for="filter_item_group">Category</label>
    <select class="form-control input-sm filter_dropdown" data-column="1" id="filter_item_category">
      <option value="">
        Not filtered
      </option>

      <?php foreach (config_item('auth_inventory') as $category):?>
        <option value="<?=$category;?>">
          <?=$category;?>
        </option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_item_group">Group</label>
    <select class="form-control input-sm filter_dropdown" data-column="2" id="filter_item_group">
      <option value="">
        Not filtered
      </option>

      <?php foreach (available_item_groups(config_item('auth_inventory')) as $group):?>
        <option value="<?=$group;?>">
          <?=$group;?>
        </option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_unit">Unit</label>
    <select class="form-control input-sm filter_dropdown" data-column="7" id="filter_unit">
      <option value="">
        Not filtered
      </option>

      <?php foreach ($units as $unit):?>
        <option value="<?=$unit['unit'];?>">
          <?=$unit['unit'];?>
        </option>
      <?php endforeach;?>
    </select>
  </div>
</div>
