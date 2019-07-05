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
  <div class="section-floating-action-row">
    
    <?php if (is_granted($module, 'adjustment')):?>
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-create-document" data-toggle="dropdown">
          <i class="md md-add"></i>
          <small class="top right">Create Adjustment</small>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" role="menu">
          <?php foreach (config_item('auth_inventory') as $category):?>
            <li>
              <a href="<?=site_url($module['route'] .'/create/'. $category);?>"><?=$category;?></a>
            </li>
          <?php endforeach;?>
        </ul>
      </div>
    <?php endif ?>
  </div>
<?php endblock() ?>

<?php startblock('datafilter') ?>
<div class="form force-padding">
  <div class="form-group">
    <label for="filter_created_at">Date</label>
    <input class="form-control input-sm filter_daterange" data-column="6" id="filter_created_at" readonly>
  </div>

  <div class="form-group">
    <label for="category">Category</label>
    <select class="form-control input-sm filter_dropdown" data-column="3" id="category" name="category">
      <option value="">-- ALL CATEGORIES --</option>
      <?php foreach (config_item('auth_inventory') as $category):?>
        <option value="<?=$category;?>">
          <?=$category;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="warehouse">Base</label>
    <select class="form-control input-sm filter_dropdown" data-column="4" id="warehouse" name="warehouse">
      <option value="">
        ALL BASE
      </option>

      <?php foreach (available_warehouses() as $warehouse):?>
        <option value="<?=$warehouse;?>">
          <?=$warehouse;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="condition">Condition</label>
    <select class="form-control input-sm filter_dropdown" data-column="5" id="condition" name="condition">
      <?php foreach (available_conditions() as $condition):?>
        <option value="<?=$condition;?>">
          <?=$condition;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="as_mix">Mix Item</label>
    <select class="form-control input-sm filter_dropdown" data-column="0" id="as_mix" name="as_mix">
      <option value="">
        INCLUDE MIX ITEM
      </option>
      <option value="FALSE">
        EXCLUDE MIX ITEM
      </option>
      <option value="TRUE">
        MIX ITEM ONLY
      </option>
    </select>
  </div>
  <div class="form-group">
    <label for="as_mix">Status</label>
    <select class="form-control input-sm filter_dropdown" data-column="1" id="as_mix" name="as_mix">
      <option value="APPROVED">
        APPROVED
      </option>
      <option value="PENDING">
        PENDING
      </option>
      <option value="REJECTED">
        REJECTED
      </option>
    </select>
  </div>

  <!-- <button type="submit" class="btn btn-flat btn-danger btn-block ink-reaction">Generate</button> -->
</div>
<?php endblock() ?>
