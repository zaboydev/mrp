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
    
  </div>
<?php endblock() ?>
