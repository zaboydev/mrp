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
  <?php if (is_granted($module, 'create')):?>
    <?php $this->load->view('material/templates/button_create_sd_modal') ?>
  <?php endif ?>
<?php endblock() ?>

<?php startblock('datafilter') ?>
  <div class="form force-padding">
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
      <label for="filter_item_group">Group</label>
      <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_item_group">
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
      <label for="filter_condition">Group</label>
      <select class="form-control input-sm filter_dropdown" data-column="5" id="filter_condition">
        <option value="">
          SERVICEABLE
        </option>

        <?php foreach (config_item('condition') as $key => $label):?>
          <?php if ($key !== 'S/S'):?>
            <option value="<?=$key;?>">
              <?=$label;?>
            </option>
          <?php endif;?>
        <?php endforeach;?>
      </select>
    </div>

    <div class="form-group">
      <label for="filter_quantity">On Hand Quantity</label>
      <select class="form-control input-sm filter_dropdown" data-column="6" id="filter_quantity">
        <option value="">
          Not filtered
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
      <select class="form-control input-sm filter_dropdown" data-column="8" id="filter_unit">
        <option value="">
          Not filtered
        </option>

        <?php foreach (available_units() as $unit):?>
          <option value="<?=$unit;?>">
            <?=$unit;?>
          </option>
        <?php endforeach;?>
      </select>
    </div>
  </div>
<?php endblock() ?>

<?php if (is_granted($module, 'import')):?>
  <?php startblock('offcanvas_left_actions') ?>
    <li class="tile">
      <a class="tile-content ink-reaction" href="#offcanvas-import" data-toggle="offcanvas">
        <div class="tile-icon">
          <i class="fa fa-download"></i>
        </div>
        <div class="tile-text">
          Import Data
          <small>import from csv file</small>
        </div>
      </a>
    </li>
  <?php endblock() ?>

  <?php startblock('offcanvas_left_list') ?>
    <div id="offcanvas-import" class="offcanvas-pane width-8">
      <div class="offcanvas-head style-primary-dark">
        <header>Import</header>
        <div class="offcanvas-tools">
          <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
            <i class="md md-close"></i>
          </a>
          <a class="btn btn-icon-toggle pull-right" href="#offcanvas-datatable-filter" data-toggle="offcanvas">
            <i class="md md-arrow-back"></i>
          </a>
        </div>
      </div>

      <div class="offcanvas-body no-padding">
        <?php $this->load->view('material/modules/stores/import') ?>
      </div>
    </div>
  <?php endblock() ?>
<?php endif;?>
