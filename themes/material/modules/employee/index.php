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

<?php startblock('offcanvas_left_actions') ?>
  <?php if (is_granted($module, 'import')):?>
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
  
  <?php endif;?>
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
