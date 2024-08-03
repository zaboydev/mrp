<?= form_open(site_url($module['route'] . '/save'), array(
    'autocomplete'  => 'off',
    'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Create New <?= $module['label']; ?></header>

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
                            <input type="text" value="<?= $entity['transportation']; ?>" name="transportation" id="transportation" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/transportation_validation'); ?>" data-validation-exception="" required>
                            <label for="transportation"><?= $module['label']; ?></label>
                        </div>

                        <div class="form-group">
                            <input type="text" value="<?= $entity['contact_name']; ?>" name="contact_name" id="contact_name" class="form-control" required>
                            <label for="contact_name">Contact Person</label>
                        </div>

                        <div class="form-group">
                            <input type="text" value="<?= $entity['contact_number']; ?>" name="contact_number" id="contact_number" class="form-control" required>
                            <label for="contact_number">Phone Number</label>
                        </div>

                        <div class="form-group">
                            <input type="email" value="<?= $entity['contact_email']; ?>" name="contact_email" id="contact_email" class="form-control" required>
                            <label for="contact_email">E-mail</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>
<script type="text/javascript">
    $('.number').number(true, 2, '.', ',');
</script>