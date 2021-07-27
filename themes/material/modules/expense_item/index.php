<?php include 'themes/material/page.php' ?>

<?php startblock('actions_right') ?>
  <div class="section-floating-action-row">    

    <?php if (is_granted($module, 'create')):?>
      <button class="btn btn-floating-action btn-lg btn-danger ink-reaction" id="btn-create-data" onclick="$(this).popup()" data-source="<?=site_url($module['route'] .'/create')?>" data-target="#data-modal">
        <i class="md md-add"></i>
      </button>
    <?php endif ?>
  </div>
<?php endblock() ?>

<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
  <?php $this->load->view('material/templates/datatable') ?>

  <?php if (is_granted($module, 'import')):?>
    <?php $this->load->view('material/templates/import_modal');?>
  <?php endif;?>
<?php endblock() ?>

<?php startblock('page_modals') ?>
  <?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>

<?php startblock('datafilter') ?>
  <div class="form force-padding">
    <div class="form-group">
      <label for="filter_warehouse">Warehouse</label>
      <select class="form-control filter_dropdown" data-column="2" id="filter_warehouse">
        <option value="">
          Not filtered
        </option>

        <?php foreach (config_item('auth_warehouses') as $base):?>
          <option value="<?=$base;?>">
            <?=$base;?>
          </option>
        <?php endforeach;?>
      </select>
    </div>

    <div class="form-group">
      <label for="filter_item_group">Category</label>
      <select class="form-control input-sm filter_dropdown" data-column="3" id="filter_item_category">
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
  </div>
<?php endblock() ?>
