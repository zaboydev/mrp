
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
                            <input type="text" value="" name="expense_name" id="expense_name" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/expense_duty_name_validation'); ?>" data-validation-exception="" required>
                            <label for="expense_name"><?= $module['label']; ?></label>
                        </div>
                        <div class="form-group">
                            <select name="id_benefit" id="id_benefit" data-placeholder="Pilih Benefit" class="form-control select2" required>
                                <option></option>
                                <?php foreach(getBenefits() as $benefit):?>
                                <option data-spd-number="<?=$spd['document_number'];?>" value="<?=$spd['id'];?>" <?= ($spd['id'] == $_SESSION['sppd']['spd_id']) ? 'selected' : ''; ?>><?=$spd['document_number'];?> | <?=$spd['person_name'];?></option>
                                <option value="<?= $benefit['id']; ?>" data-benefit-id="<?= $benefit['id']; ?>">
                                    <?= $benefit['employee_benefit']; ?>
                                </option>
                                <?php endforeach;?>
                            </select>
                            <label for="id_benefit">Plafond Benefit</label>
                        </div>
                        <div class="form-group">
                            <select name="account_code" id="account_code" class="form-control" required>
                                <option value="">Select Code of Account</option>
                                <?php foreach (master_coa() as $group) : ?>
                                <option value="<?= $group['coa']; ?>" data-coa="<?= $group['coa']; ?>" data-group="<?= $group['group']; ?>">
                                    <?= $group['coa']; ?> - <?= $group['group']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="account_code">Code of Account</label>
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
    $('.select2').select2({ 
        allowClear: true,
        placeholder: "Pilih Benefit2",});
</script>
