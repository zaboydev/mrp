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

<?php startblock('datafilter') ?>
  <div class="form force-padding">
    <div class="form-group">
      <label for="filter_date_of_entry">Date</label>
      <input class="form-control input-sm filter_daterange" data-column="1" id="filter_date_of_entry" readonly>
    </div>

    <div class="form-group">
      <label for="filter_warehouse">Base</label>
      <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_warehouse">
        <option value="">
          Not filtered
        </option>

        <?php foreach (config_item('auth_warehouses') as $warehouse):?>
          <option value="<?=$warehouse;?>">
            <?=$warehouse;?>
          </option>
        <?php endforeach;?>
      </select>
    </div>
  </div>
<?php endblock() ?>
