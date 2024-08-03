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

<?php if (is_granted($module, 'create')):?>
  <?php startblock('actions_right') ?>
    <div class="section-floating-action-row">
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-stock-opname" data-target="<?=site_url($module['route'] .'/create');?>">
          <i class="md md-restore"></i>
          <small class="top right">Close Period Operation</small>
        </button>
      </div>
    </div>
  <?php endblock() ?>
<?php endif ?>

<?php startblock('datafilter') ?>
  <form method="get" class="form force-padding">

    <div class="form-group">
      <label for="condition">Stores</label>
      <select class="form-control input-sm filter_dropdown" data-column="1" id="stores" name="stores">
        <option value="">ALL</option>
        <?php foreach (get_stores($stock_id) as $stores):?>
          <option value="<?=$stores;?>" <?=($stores == $selected_stores) ? 'selected' : '';?>>
            <?=$stores;?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- <button type="submit" class="btn btn-flat btn-danger btn-block ink-reaction">Generate</button> -->
  </form>
<?php endblock() ?>
