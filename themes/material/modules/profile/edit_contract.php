<?= form_open_multipart(site_url($module['route'] . '/save_contract'), array(
    'autocomplete'  => 'off',
    // 'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    // 'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Edit Contract <?= $module['label']; ?></header>

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
                            <input type="text" name="contract_number" id="contract_number" class="form-control" value="<?=$entity['contract_number']?>" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/contract_number_validation'); ?>" data-validation-exception="" required>
                            <label for="contract_number">Contract Number</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="employee_name" id="employee_name" class="form-control" value="<?=$entity['name'];?>" readonly>
                            <input type="hidden" name="employee_number" id="employee_number" class="form-control" value="<?=$entity['employee_number'];?>" readonly>
                            <label for="employee_name">Name</label>
                        </div>

                        <div class="form-group">
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?=$entity['start_date']?>">
                            <label for="start_date">Start Date</label>
                        </div>

                        <div class="form-group">
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?=$entity['end_date']?>">
                            <label for="end_date">End Date</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="month" id="month" class="form-control" value="<?=$entity['month']?>">
                            <label for="month">Periode Kontrak Bulan</label>
                        </div>  
                        
                        <div class="form-group">
                            <?php if($entity['file_kontrak']!=null):?>
                            <a href="<?=site_url($entity['file_kontrak'])?>" target="_blank">View Contract File</a>
                            <?php else:?>
                            <p>No COntract File</p>
                            <?php endif;?>
                            <label for="contractfile">File Contract</label>
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
        <input type="hidden" name="id" id="id" class="form-control" value="<?=$entity['id'];?>" readonly>
        <input type="hidden" name="contract_number_rexception" id="contract_number_rexception" class="form-control" value="<?=$entity['contract_number'];?>" readonly>
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>