<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
  <?php $this->load->view('material/templates/datatable') ?>
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add-modal-label" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <?=form_open_multipart(site_url($module['route'] .'/add_cot'), array('autocomplete' => 'off', 'class' => 'form form-validate form-xhr ui-front','id' =>'add_cot_form'));?>
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

              <h4 class="modal-title" id="import-modal-label">Add Proyeksi COT</h4>
            </div>

            <div class="modal-body">
              <div class="form-group">
                  <label for="exampleInputEmail1"> Target Hours</label>
                  <input type="text" class="form-control number" required="" id="hour" value="0" name="hour" placeholder="Ex 6000">
              </div>
              <div class="form-group">
                  <label for="exampleInputEmail1">Year</label>
                  <input type="text" class="form-control year" required="" maxlength="4" value="<?=$this->model->active_year; ?>" id="year" name="year" placeholder="Ex 2017">
              </div>
              <div class="form-group">
                  <label>Kelipatan</label>
                  <select class="form-control" id="kelipatan" name="id_kelipatan">
                    <?php $kelipatan = $this->model->getKelipatan(); 
                      foreach ($kelipatan as $key) {
                        ?>
                        <option value="<?=$key->id?>"><?=$key->kelipatan?></option>
                        <?php
                      }
                    ?>
                  </select>
                </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-block btn-primary ink-reaction">Add Data</button>
            </div>
          <?=form_close();?>
        </div>
      </div>
    </div>
<?php endblock() ?>

<?php startblock('page_modals') ?>
  <?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>

<?php startblock('actions_right') ?>
  <div class="section-floating-action-row">
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-add-data" data-toggle="modal" data-target="#add-modal">
          <i class="md md-add"></i>
          <small class="top right">Add Data</small>
        </button>
      </div>
  </div>
<?php endblock() ?>

