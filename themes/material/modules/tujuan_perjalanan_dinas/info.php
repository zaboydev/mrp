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
            

            <div class="col-sm-12 col-md-12">
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
                                <?php foreach ($entity['levels'] as $key => $level) : ?>
                                <th class="" style="text-align:right;">
                                    <?= $level['level'] ?>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table_contents">
                        <?php $n = 0;?>
                        <?php $total_expense = array();?>
                        <?php foreach ($entity['items'] as $i => $detail):?>
                            <?php $n++;?>
                            <tr>
                                <td class="no-space">
                                    <?=print_number($n);?>
                                </td>
                                <td>
                                    <?=print_string($detail['expense_name']);?>
                                </td>
                                <?php foreach ($entity['levels'] as $key => $level) : ?>
                                <td style="text-align:right;">
                                    <?php if($entity['items'][$i]['levels'][$key]['amount']>0):?>
                                    <?=number_format($entity['items'][$i]['levels'][$key]['amount'], 2);?>/<?=number_format($entity['items'][$i]['levels'][$key]['day'], 0);?> days
                                    <?php else:?>
                                    <?=$entity['items'][$i]['levels'][$key]['notes'];?>
                                    <?php endif;?>
                                    <?php $total_expense[] = $entity['items'][$i]['levels'][$key]['amount'];?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                        
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <?php if (is_granted($module, 'delete')):?>
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
        <?php if (is_granted($module, 'create')):?>
            <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
            <i class="md md-edit"></i>
            <small class="top right">edit</small>
            </a>
        <?php endif;?>        
        </div>
    </div>
</div>
