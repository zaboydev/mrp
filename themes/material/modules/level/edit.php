<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Edit <?= $module['label']; ?></header>

        <div class="tools">
            <div class="btn-group">
                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
                <i class="md md-close"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <input type="text" value="<?= $entity['level']; ?>" name="level" id="level" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/level_validation'); ?>" data-validation-exception="" required>
                            <label for="level"><?= $module['label']; ?></label>
                        </div>

                        <div class="form-group">
                            <input type="text" value="<?= $entity['code']; ?>" name="code" id="code" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/level_code_validation'); ?>" data-validation-exception="" required>
                            <label for="code">Code</label>
                        </div>

                        <div class="form-group">
                            <textarea name="notes" id="notes" class="form-control"><?= $entity['plafon_biaya_dinas']; ?></textarea>
                            <label for="notes">Notes</label>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <input type="hidden" name="id" id="id" value="<?= $entity['id']; ?>">
        <input type="hidden" name="level_exception" id="level_exception" value="<?= $entity['level']; ?>">
        <input type="hidden" name="code_exception" id="code_exception" value="<?= $entity['code']; ?>">

        <?php if (is_granted($module, 'delete')) : ?>
        <a href="<?= site_url($module['route'] . '/delete'); ?>" class="btn btn-floating-action btn-danger btn-xhr-delete ink-reaction" id="modal-delete-data-button" data-title="delete">
            <i class="md md-delete"></i>
        </a>
        <?php endif; ?>

        <div class="pull-right">
        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
            <i class="md md-save"></i>
        </button>
        </div>

        <input type="reset" name="reset" class="sr-only">
    </div>
</div>

<?= form_close(); ?>
<script type="text/javascript">
    $('.number').number(true, 2, '.', ',');
</script>