<?= form_open_multipart(site_url($module['route'] . '/save_benefit'), array(
    'autocomplete'  => 'off',
    // 'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    // 'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Info</header>

        <div class="tools">
        <div class="btn-group">
            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
            <i class="md md-close"></i>
            </a>
        </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row" id="document_master">
            <div class="col-sm-12 col-md-8">
                <dl class="dl-inline">
                <dt>Benefit</dt>
                <dd><?=print_string($entity['employee_benefit']);?></dd>

                <dt>Periode Kontrak</dt>
                <dd><?=print_string($entity['contract_number']);?> <?=print_date($entity['start_date']);?> sd <?=print_date($entity['end_date']);?></dd>

                <dt>Name</dt>
                <dd><?=print_string($entity['name']);?></dd>

                <dt>Position</dt>
                <dd><?=print_string($entity['position']);?></dd>

                <dt>Plafon</dt>
                <dd><?=print_number($entity['amount_plafond']);?></dd>

                </dl>
            </div>
        </div>
        <div class="row" id="document_details">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-nowrap">
                        <thead id="table_header">
                            <th>No</th>
                            <th>Date</th>
                            <th>No Transaction</th>
                            <th>Amount</th>
                        </thead>
                        <tbody id="table_contents">
                            <?php $n = 0; $open=0;?>
                            <?php $total = array();?>
                            <?php if(count($entity['itemUseds'])):?>
                            <?php foreach ($entity['itemUseds'] as $i => $detail):?>
                            <tr>
                                <?php 
                                    $n++;
                                    $total[] = $detail['amount'];
                                ?>
                                <td style="text-align:center;">
                                    <?=print_number($n);?>
                                </td>
                                <td>
                                    <?=print_date($detail['date']);?>
                                </td>
                                <td>
                                    <?=print_string($detail['document_number']);?>
                                </td>
                                <td style="text-align:right;">
                                    <?=print_number($detail['amount'], 2);?>
                                </td>
                            </tr>
                                
                            <?php endforeach;?>
                            <?php else:?>
                            <tr>
                                <td colspan="4" style="text-align:center;">No Data Available</td>
                            </tr>                            
                            <?php endif;?>    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th><?=print_number(array_sum($total), 2);?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div id="view-form-edit" class="row hide">
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
                            <p style="font-size:14px;"><?= print_string($entity['contract_number']) ?> (<?= print_date($entity['start_date']) ?> sd <?= print_date($entity['end_date']) ?>)</p>
                            <input type="hidden" name="employee_contract_id" id="employee_contract_id" class="form-control" value="<?=$entity['employee_contract_id'];?>" readonly>
                            <input type="hidden" name="employee_contract_id_exception" id="employee_contract_id_exception" class="form-control" value="<?=$entity['employee_contract_id'];?>" readonly>
                            <label for="jabatan">Periode Kontrak</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="employee_benefit_id" id="employee_benefit_id" class="form-control select2" style="width: 100%" data-placeholder="Select Benefit" required>
                                <option value="">Select Benefit</option>
                                <?php foreach(benefit_list() as $benefit):?>
                                <option value="<?=$benefit['id'];?>" <?= ($benefit['id']==$entity['employee_benefit_id'])? 'selected':'';?>><?=$benefit['employee_benefit'];?></option>
                                <?php endforeach;?>
                            </select>
                            <input type="hidden" name="employee_benefit_id_exception" id="employee_benefit_id_exception" class="form-control" value="<?=$entity['employee_benefit_id'];?>" readonly>
                            <label for="start_date">Benefit Name</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="amount_plafond" id="amount_plafond" class="form-control number" value="<?=$entity['amount_plafond']?>" step=".01">
                            <label for="amount_plafond">Amount Plafond</label>
                            <input type="hidden" name="id" id="id" class="form-control" value="<?=$entity['id'];?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <?php if(count($entity['itemUseds'])==0):?>
        <button type="button" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
            <i class="md md-edit"></i>
            <small class="top right">Edit</small>
        </button>
        <?php endif;?> 
        <button type="submit" id="modal-edit-data-submit" class="hide btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
            <i class="md md-save"></i>
        </button>        
    </div>
</div>

<?= form_close(); ?>
<script type="text/javascript">
    $('.number').number(true, 2, '.', ',');
    $('.select2').select2();
    $('#modal-edit-data-button').click(function() {
        if($('#view-form-edit').hasClass('hide')){
            $('#view-form-edit').removeClass('hide');
            $('#modal-edit-data-submit').removeClass('hide');
            $('#modal-delete-data-button').removeClass('hide');
            $('#document_master').addClass('hide');
            $('#document_details').addClass('hide');
            $('#modal-edit-data-button').addClass('hide');
            $('#header_text').html('Edit');
        }
    });
</script>