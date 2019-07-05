<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
  <?php $this->load->view('material/templates/datatable') ?>

  <?php if (is_granted($module, 'import')):?>
    <div id="import-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="import-modal-label" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <?=form_open_multipart(site_url($module['route'] .'/import'), array('autocomplete' => 'off', 'class' => 'form form-validate form-xhr ui-front'));?>
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

              <h4 class="modal-title" id="import-modal-label">Import Data</h4>
            </div>

            <div class="modal-body">
              <div class="form-group">
                <label for="userfile">CSV File</label>

                <input type="file" name="userfile" id="userfile" required>
              </div>

              <div class="form-group">
                <label>Value Delimiter</label>

                <div class="radio">
                  <input type="radio" name="delimiter" id="delimiter_2" value=";" checked>
                  <label for="delimiter_2">Semicolon ( ; )</label>
                </div>

                <div class="radio">
                  <input type="radio" name="delimiter" id="delimiter_1" value=",">
                  <label for="delimiter_1">Comma ( , )</label>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-block btn-primary ink-reaction">Import Data</button>
            </div>
          <?=form_close();?>
        </div>
      </div>
    </div>
  <?php endif;?>
<?php endblock() ?>

<?php startblock('page_modals') ?>
  <?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>

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
  </div>
<?php endblock() ?>

<?php startblock('datafilter') ?>
<form method="post" class="form force-padding">
  <div class="form-group">
    <label for="warehouse">Base</label>
    <select class="form-control input-sm" id="warehouse" name="warehouse">
      <?php foreach (config_item('auth_warehouses') as $warehouse):?>
        <option value="<?=$warehouse;?>" <?=($warehouse == $selected_warehouse) ? 'selected' : '';?>>
          <?=$warehouse;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

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
