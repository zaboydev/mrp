<?= form_open_multipart(site_url($module['route'] . '/save_contract'), array(
    'autocomplete'  => 'off',
    // 'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    // 'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Create New Contract <?= $module['label']; ?></header>

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
                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <input type="text" name="contract_number" id="contract_number" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/contract_number_validation'); ?>" data-validation-exception="" required>
                            <label for="contract_number">Contract Number</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="employee_name" id="employee_name" class="form-control" value="<?=$entity['name'];?>" readonly>
                            <input type="hidden" name="employee_number" id="employee_number" class="form-control" value="<?=$entity['employee_number'];?>" readonly>
                            <label for="employee_name">Name</label>
                        </div>

                        <div class="form-group">
                            <input type="date" name="start_date" id="start_date" class="form-control">
                            <label for="start_date">Start Date</label>
                        </div>

                        <div class="form-group">
                            <input type="date" name="end_date" id="end_date" class="form-control">
                            <label for="end_date">End Date</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="month" id="month" class="form-control">
                            <label for="month">Periode Kontrak Bulan</label>
                        </div>      
                        
                        <div class="form-group">
                            <input type="file" name="contractfile" id="contractfile">
                            <label for="contractfile">File</label>
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