<div id="modal-edit-data" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-edit-data-label" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header style-primary-dark">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal-edit-data-label">Edit Form</h4>
      </div>

    <?=form_open(site_url($module['route'] .'/save'), array(
      'autocomplete' => 'off',
      'id'    => 'form-edit-data',
      'class' => 'form form-validate ui-front',
      'role'  => 'form'
    ));?>

      <div class="modal-body">
      </div>

      <div class="modal-footer">
        <?php if (is_granted($module, 'delete')):?>
          <a href="<?=site_url($module['route']. '/delete');?>" class="btn btn-floating-action btn-danger ink-reaction pull-left" id="modal-delete-data-button" data-toggle="tooltip" data-original-title="delete this data">
            <i class="md md-delete"></i>
          </a>
        <?php endif;?>

        <button type="button" class="btn btn-icon-toggle" data-dismiss="modal" data-toggle="tooltip" data-original-title="close this form">
          <i class="md md-close"></i>
        </button>

        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-update ink-reaction" data-toggle="tooltip" data-original-title="save and update">
          <i class="md md-save"></i>
        </button>
        <input type="reset" name="reset" class="sr-only">
      </div>

    <?=form_close();?>
    </div>
  </div>
</div>
