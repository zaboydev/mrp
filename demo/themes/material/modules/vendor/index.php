<?php include 'themes/material/page.php' ?>


<?php startblock('actions_right') ?>
  <?php if (is_granted($module, 'create')):?>
    <?php $this->load->view('material/templates/button_create_data_modal') ?>
  <?php endif ?>
<?php endblock() ?>


<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>


<?php startblock('page_body') ?>
  <?php $this->load->view('material/templates/datatable') ?>
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
