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
                        <?php
                            if ( $this->session->flashdata('alert') )
                                render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
                        ?>

                        <div class="hide form-group <?php echo (form_error('main_warehouse')) ? 'has-error' : '';?>">
                            <label for="main_warehouse">Main Base</label>
                            <!-- <div class="col-sm-9"> -->
                                <select name="main_warehouse" id="main_warehouse" class="form-control" required="required">
                                    <option value="" <?=($main_warehouse == null) ? 'selected' : '';?>>-- Select Warehouse</option>

                                    <?php foreach (available_warehouses() as $warehouse):?>
                                        <option value="<?=$warehouse;?>" <?=($warehouse == $main_warehouse) ? 'selected' : '';?>>
                                            <?=$warehouse;?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                                <?php echo form_error('main_warehouse', '<span class="help-block text-danger">', '</span>'); ?>
                            <!-- </div> -->
                        </div>

                        <div class="form-group">
                            <label for="expense_from_spd"><?=$entity[1]['setting_name']?></label>
                            <div class="radio">
                                <input type="radio" name="expense_from_spd" id="full_approval_expense_spd" value="full_approval" <?=('FULL APPROVAL' == $entity[1]['setting_value']) ? 'checked' : '';?>>
                                <label for="full_approval_expense_spd">
                                Full Approval
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="expense_from_spd" id="not_full_approval_expense_spd" value="not_full_approval" <?=('NOT FULL APPROVAL' == $entity[1]['setting_value']) ? 'checked' : '';?>>
                                <label for="not_full_approval_expense_spd">
                                Not Full Approval
                                </label>
                            </div>
                            <input type="hidden" name="old_value_expense_from_spd" value="<?=$entity[1]['setting_value']?>">
                                    
                        </div>

                        <div class="form-group">
                            <label for="expense_from_sppd"><?=$entity[2]['setting_name']?></label>
                            <div class="radio">
                                <input type="radio" name="expense_from_sppd" id="full_approval_expense_sppd" value="full_approval" <?=('FULL APPROVAL' == $entity[2]['setting_value']) ? 'checked' : '';?>>
                                <label for="full_approval_expense_sppd">
                                Full Approval
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="expense_from_sppd" id="not_full_approval_expense_sppd" value="not_full_approval" <?=('NOT FULL APPROVAL' == $entity[2]['setting_value']) ? 'checked' : '';?>>
                                <label for="not_full_approval_expense_sppd">
                                Not Full Approval
                                </label>
                            </div>
                            <input type="hidden" name="old_value_expense_from_sppd" value="<?=$entity[2]['setting_value']?>">
                            <input type="hidden" name="old_value_main_warehouse" value="<?=$main_warehouse?>">
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
<?=form_close();?>
