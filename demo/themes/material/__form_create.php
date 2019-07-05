<div id="modal-create-data" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-create-data-label" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header style-primary-dark">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal-create-data-label">Create Form</h4>
      </div>

    <?=form_open(site_url($module['route'] .'/save'), array(
      'autocomplete' => 'off',
      'id'    => 'form-create-data',
      'class' => 'form form-validate floating-label ui-front',
      'role'  => 'form'
    ));?>

      <div class="modal-body">
        <?php $this->load->view($module['view'] .'/create');?>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-icon-toggle" data-dismiss="modal" data-toggle="tooltip" data-original-title="cancel and close">
          <i class="md md-close"></i>
        </button>

        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-update ink-reaction" data-toggle="tooltip" data-original-title="save and create">
          <i class="md md-save"></i>
        </button>

        <input type="reset" name="reset" class="sr-only">
      </div>

    <?=form_close();?>
    </div>
  </div>
</div>
