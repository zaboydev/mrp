<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
  <?php $this->load->view('material/templates/datatable') ?>
<?php endblock() ?>

<?php startblock('page_modals') ?>
  <?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>

<?php if (is_granted($module, 'create')):?>
  <?php startblock('actions_right') ?>
    <div class="section-floating-action-row">
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-stock-opname" data-target="<?=site_url($module['route'] .'/create');?>">
          <i class="md md-restore"></i>
          <small class="top right">Close Period Operation</small>
        </button>
      </div>
    </div>
  <?php endblock() ?>
<?php endif ?>

<?php startblock('datafilter') ?>
  <form method="get" class="form force-padding">
    <div class="form-group">
      <label for="month">Month</label>
      <select class="form-control input-sm" id="month" name="month">
        <?php for ($month = 1; $month <= 12; $month++):?>
          <option value="<?=$month;?>" <?=($month == $selected_month) ? 'selected' : '';?>>
            <?=numberToMonthName($month);?>
          </option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="year">Year</label>
      <select class="form-control input-sm" id="year" name="year">
        <?php for ($year = 2017; $year <= config_item('period_year'); $year++):?>
          <option value="<?=$year;?>" <?=($year == $selected_year) ? 'selected' : '';?>><?=$year;?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="warehouse">Base</label>
      <select class="form-control input-sm" id="warehouse" name="warehouse">
        <option value="ALL BASES">-- ALL BASES --</option>
        <?php foreach (config_item('auth_warehouses') as $warehouse):?>
          <option value="<?=$warehouse;?>" <?=($warehouse == $selected_warehouse) ? 'selected' : '';?>>
            <?=$warehouse;?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="category">Category</label>
      <select class="form-control input-sm" id="category" name="category">
        <?php foreach (config_item('auth_inventory') as $category):?>
          <option value="<?=$category;?>" <?=($category == $selected_category) ? 'selected' : '';?>>
            <?=$category;?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="group">Group</label>
      <select class="form-control input-sm" id="group" name="group">
        <?php foreach (available_item_groups(config_item('auth_inventory')) as $group):?>
          <option value="<?=$group;?>" <?=($group == $selected_group) ? 'selected' : '';?>>
            <?=$group;?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="condition">Condition</label>
      <select class="form-control input-sm" id="condition" name="condition">
        <?php foreach (available_conditions() as $condition):?>
          <option value="<?=$condition;?>" <?=($condition == $selected_condition) ? 'selected' : '';?>>
            <?=$condition;?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-flat btn-danger btn-block ink-reaction">Generate</button>
  </form>
<?php endblock() ?>
