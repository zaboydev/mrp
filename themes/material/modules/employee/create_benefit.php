<?= form_open_multipart(site_url($module['route'] . '/save_benefit'), array(
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
                    <div class="col-sm-12 col-md-4 col-md-offset-4">

                        <div class="form-group">
                            <input type="text" name="employee_name" id="employee_name" class="form-control" value="<?=$entity['name'];?>" readonly>
                            <input type="hidden" name="employee_number" id="employee_number" class="form-control" value="<?=$entity['employee_number'];?>" readonly>
                            <label for="employee_name">Name</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="jabatan" id="jabatan" class="form-control" value="<?=$entity['position'];?>" readonly>
                            <label for="jabatan">Jabatan</label>
                        </div>

                        <div class="form-group">
                            <p style="font-size:14px;"><?= print_string($kontrak_active['contract_number']) ?> (<?= print_date($kontrak_active['start_date']) ?> sd <?= print_date($kontrak_active['end_date']) ?>)</p>
                            <input type="hidden" name="employee_contract_id" id="employee_contract_id" class="form-control" value="<?=$kontrak_active['id'];?>" readonly>
                            <label for="jabatan">Periode Kontrak</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="employee_benefit_id" id="employee_benefit_id" class="form-control select2" style="width: 100%" data-placeholder="Select Benefit" required>
                                <option value="">Select Benefit</option>
                                <?php foreach(benefit_list() as $benefit):?>
                                <option value="<?=$benefit['id'];?>"><?=$benefit['employee_benefit'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="start_date">Benefit Name</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="amount_plafond" id="amount_plafond" class="form-control number" value="0" step=".01">
                            <label for="amount_plafond">Amount Plafond</label>
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
    $('.select2').select2();
</script>