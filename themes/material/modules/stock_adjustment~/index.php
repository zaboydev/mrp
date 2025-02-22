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
<form method="post" class="form force-padding">
  <div class="form-group">
    <label for="category">Category</label>
    <select class="form-control input-sm" id="category" name="category">
      <option value="">-- ALL CATEGORIES --</option>
      <?php foreach (config_item('auth_inventory') as $category):?>
        <option value="<?=$category;?>" <?=($category == $selected_category) ? 'selected' : '';?>>
          <?=$category;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="condition">Condition</label>
    <select class="form-control input-sm" id="condition" name="condition">
      <?php foreach (available_conditions() as $condition):?>
        <option value="<?=$condition;?>" <?=($condition == $selected_condition) ? 'selected' : '';?>>
          <?=$condition;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="btn btn-flat btn-danger btn-block ink-reaction">Generate</button>
</form>
<?php endblock() ?>
