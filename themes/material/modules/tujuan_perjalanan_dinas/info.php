<div class="card card-underline style-default-bright">
    <div class="card-head style-primary-dark">
        <header><?=strtoupper($module['label']);?></header>

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
            <div class="col-sm-12 col-md-4 col-md-push-8">
                <div class="well">
                    <div class="clearfix">
                        <div class="pull-left">DOCUMENT NO.: </div>
                        <div class="pull-right"><?=print_string($entity['document_number']);?></div>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left">DATE: </div>
                        <div class="pull-right"><?=print_date($entity['received_date']);?></div>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left">BASE: </div>
                        <div class="pull-right"><?=print_string($entity['warehouse']);?></div>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left">INVENTORY: </div>
                        <div class="pull-right"><?=print_string($entity['category']);?></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-8 col-md-pull-4">
                <dl class="dl-inline">
                    <dt>Business Trip Destination</dt>
                    <dd><?=$entity['business_trip_destination'];?></dd>

                    <dt>Notes</dt>
                    <dd><?=($entity['notes'])? $entity['notes']:'n/b';?></dd>

                    <dt>Modified at</dt>
                    <dd><?=print_date($entity['updated_at']);?></dd>
                </dl>
            </div>
        </div>

        <div class="row" id="document_details">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-nowrap">
                        <thead id="table_header">
                            <tr>
                                <th>No</th>
                                <th>Expense Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="table_contents">
                        <?php $n = 0;?>
                        <?php $total_expense = array();?>
                        <?php foreach ($entity['expense'] as $i => $detail):?>
                            <?php $n++;?>
                            <tr>
                                <td class="no-space">
                                    <?=print_number($n);?>
                                </td>
                                <td>
                                    <?=print_string($detail['expense_name']);?>
                                </td>
                                
                                <td>
                                    <?=print_number($detail['amount'], 2);?>
                                    <?php $total_expense[] = $detail['amount'];?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th><?=print_number(array_sum($total_expense), 2);?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <?php
            $today    = date('Y-m-d');
            $date     = strtotime('-2 day',strtotime($today));
            $data     = date('Y-m-d',$date);
        ?>
        <?php if (is_granted($module, 'delete') && $entity['received_date'] >= $data):?>
        <?=form_open(current_url(), array(
            'class' => 'form-xhr pull-left',
        ));?>
            <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">

            <a href="<?=site_url($module['route'] .'/delete_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-delete btn-tooltip ink-reaction" id="modal-delete-data-button">
            <i class="md md-delete"></i>
            <small class="top left">delete</small>
            </a>
        <?=form_close();?>
        <?php endif;?>

        <div class="pull-right">
        <?php if (is_granted($module, 'document') && $entity['received_date'] >= $data):?>
            <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
            <i class="md md-edit"></i>
            <small class="top right">edit</small>
            </a>
        <?php endif;?>        
        </div>
    </div>
</div>
