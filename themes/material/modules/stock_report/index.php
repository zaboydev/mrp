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

<?php startblock('actions_right') ?>
  
    <div class="section-floating-action-row">
      <div class="btn-group dropup">
      <?php if (is_granted($module, 'document_non_shipping')):?>
        <a type="button" href="<?=site_url($module['route'] .'/'.$document);?>" class="btn btn-floating-action btn-lg <?php if($document=='index'){echo 'btn-info';}else{echo 'btn-danger';}?> btn-tooltip ink-reaction" id="btn-create-document"><i class="md md-description"></i><small class="top right"><?php if($document=='index'){echo 'Stock Report';}else{echo 'Accounting Stock Report';}?> </small>
        </a>
      <?php endif ?>
      <?php if (is_granted($module, 'document_super_admin')):?>
        <a type="button" href="<?=site_url($module['route'] .'/'.$document_super_admin);?>" class="btn btn-floating-action btn-lg <?php if($document_super_admin=='index'){echo 'btn-danger';}else{echo 'btn-info';}?> btn-tooltip ink-reaction"><i class="md md-description"></i><small class="top right"><?php if($document_super_admin=='index'){echo 'Stock Report';}else{echo 'Super Admin Stock Report';}?> </small>
        </a>
      <?php endif ?>
      </div>
    </div>  
<?php endblock() ?>

<?php startblock('datafilter') ?>
  <form method="post" class="form force-padding">
  <div class="form-group">
    <input type="text" name="start_date" id="start_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control">
    <input type="text" name="end_date" id="end_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control">
    <label for="start_date">Date</label>
  </div>
  <div class="form-group">
      <label for="warehouse">Base</label>
      <select class="form-control input-sm" id="warehouse" name="warehouse">
        <option value="ALL BASES">-- ALL BASES --</option>
        <option value="all base rekondisi" <?=($selected_warehouse == 'all base rekondisi') ? 'selected' : '';?>>- ALL BASE REKONDISI-</option>
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
      <option value="all" <?=($selected_category == 'all') ? 'selected' : '';?>>-- ALL CATEGORIES --</option>
      <?php foreach (config_item('auth_inventory') as $category):?>
        <option value="<?=$category;?>" <?=($category == $selected_category) ? 'selected' : '';?>>
          <?=$category;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="condition">Condition</label>
    <select class="form-control input-sm" id="condition" name="condition">
      <option value="all" <?=($selected_condition == 'all') ? 'selected' : '';?>>-- ALL Condition --</option>
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
