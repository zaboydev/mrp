<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
   <?php $this->load->view('material/templates/datatable') ?>
   <input type="hidden" id="baselink" value="<?=base_url() ?>" name="">
   <div id="approve-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add-modal-label" aria-hidden="true">
    <div class="newoverlay" style="" id="loadingScreen" style="display: none;">
              <i class="fa fa-refresh fa-spin"></i>
        </div>
      <div class="modal-dialog" role="document">
        <div class="modal-content">          
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

              <h4 class="modal-title" id="import-modal-label">Approve Budget Data</h4>
            </div>

            <div class="modal-body">
              <h4 class="text-center">Are you sure?</h4>
              <h5 class="text-center">Once you approve it, the data will be locked. You will not able to edited in the future.</h5>
            </div>
            <div class="modal-footer">
              <button type="button" data-dismiss="modal" class="btn btn-default ink-reaction pull-left">Cancel</button>
              <button type="button" class="btn btn-primary ink-reaction pull-right" id="yes_btn">Yes, I'm sure</button>
            </div>
        </div>
      </div>
    </div>
<?php endblock() ?>

<?php startblock('page_modals') ?>
  <?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>
<?php if(config_item('auth_role') === "CHIEF OF MAINTANCE" || config_item('auth_role') === "SUPER ADMIN"):?>
<?php startblock('actions_right') ?>
  <div class="section-floating-action-row">
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-approve-data">
          <i class="md md-check"></i>
          <small class="top right">Approve</small>
        </button>
        <a href="<?=site_url($module['route'] .'/print_budget/'. $year);?>" type="button" class="btn btn-floating-action btn-lg btn-primary btn-tooltip ink-reaction" id="btn-approve-data" target="_blank">
          <i class="md md-print"></i>
          <small class="top right">Print Budget <?=$year;?></small>
        </a>
      </div>
  </div>
<?php endblock() ?>
<?php startblock('datafilter') ?>
  <form method="post" class="form force-padding">
  <div class="form-group">    
    <label for="start_date">Budget Year</label>
    <select class="form-control input-sm" id="year" name="year">
      <?php foreach (budget_year() as $year):?>
      <option value="<?=$year;?>" <?=($year == $year) ? 'selected' : '';?>>
        <?=$year;?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="btn btn-flat btn-danger btn-block ink-reaction">Generate</button>
</form>
<?php endif; ?>



