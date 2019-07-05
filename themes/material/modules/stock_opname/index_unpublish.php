<?php include 'themes/material/page.php' ?>

<?php startblock('actions_right') ?>
  <div class="section-floating-action-row">
    <?php if (is_granted($module, 'publish')):?>
      <a href="<?=site_url($module['route']. '/set_qty_actual');?>" type="button" class="btn btn-floating-action btn-success btn-tooltip ink-reaction">
          <i class="md md-assignment-turned-in"></i>
          <small class="top right">Set Qty Actual</small>          
        </a> 
    <?php endif ?>

    <?php if (is_granted($module, 'publish')):?>
      <div class="btn-group dropup">
        <a href="<?=site_url($module['route']. '/publish');?>" type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction">
          <i class="md md-done"></i>
          <small class="top right">Publish Stock Opname & Closing Period</small>          
        </a>        
      </div>
    <?php endif ?>
  </div>
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
      <label for="filter_warehouse">Base</label>
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

    <div class="form-group">
      <label for="filter_item_group">Stores</label>
      <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_item_stores">
        <option value="">
          Not filtered
        </option>
        <?php foreach (available_stores() as $stores):?>
          <option value="<?=$stores['stores'];?>">
            <?=$stores['stores'];?>
          </option>
        <?php endforeach;?>
      </select>
    </div>

    <div class="form-group">
      <label for="filter_item_group">Required Qty</label>
      <select class="form-control input-sm filter_dropdown" data-column="5" id="filter_required_adj">
        <option value="">
          Not filtered
        </option>
        <option value="0">
          QTY = 0
        </option>
        <option value="1">
          QTY != 0
        </option>
      </select>
    </div>
  </div>
<?php endblock() ?>
