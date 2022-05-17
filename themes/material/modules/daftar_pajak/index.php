<?php include 'themes/material/page.php' ?>

<?php startblock('actions_right') ?>
  <div class="section-floating-action-row">
    <?php if (is_granted($module, 'import')):?>
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-import-data" data-toggle="modal" data-target="#import-modal">
          <i class="md md-attach-file"></i>
          <small class="top right">Import Data</small>
        </button>
      </div>
    <?php endif ?>

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
      <label for="filter_item_group">Status</label>
      <select class="form-control input-sm filter_dropdown" data-column="1" id="filter_daftar_pajak_status">
        <option value="AVAILABLE">
          Available
        </option>
        <option value="NOT AVAILABLE">
          Not Available
        </option>        
      </select>
    </div>
  </div>
<?php endblock() ?>
