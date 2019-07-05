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
      <label for="filter_created_at">Date</label>
      <input class="form-control input-sm filter_daterange" data-column="1" id="filter_created_at" readonly>
    </div>

    <div class="form-group">
      <label for="filter_description">Specific Item Description</label>
      <input class="form-control input-sm filter_input_text" data-column="3" id="filter_description">
    </div>
  </div>
<?php endblock() ?>
