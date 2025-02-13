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
                    <dt>Benefit Code</dt>
                    <dd><?=$entity['benefit_code'];?></dd>

                    <dt>Benefit Name</dt>
                    <dd><?=$entity['benefit_name'];?></dd>
                    <dt>Status</dt>
                    <dd><?=$entity['status'];?></dd>
                    <dt>Notes</dt>
                    <dd><?=$entity['notes'];?></dd>
                </dl>
            </div>
        </div>

        <!-- <div class="row" id="document_details">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-nowrap">
                        <thead id="table_header">
                            <tr>
                                <th>No</th>
                                <th>Level</th>
                                <th>Amount</th>
                                
                            </tr>
                        </thead>
                        <tbody id="table_contents">
                        <?php $n = 0;?>
                        <?php $total_expense = array();?>
                        <?php if (!empty($entity['items'])): ?>
                        <?php foreach ($entity['levels'] as $i => $detail):?>
                            <?php $n++;?>
                            <tr>
                                <td class="no-space">
                                    <?=print_number($n);?>
                                </td>
                                <td>
                                    <?=print_string($detail['level']);?>
                                </td>
                                <td style="text-align:right;">
                                    <?=print_number($detail['amount'], 2);?>
                                    <?php $total_expense[] = $detail['amount'];?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No items available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        
                        </tfoot>
                    </table>
                </div>
            </div>
        </div> -->
    </div>

    <div class="card-foot">
        <!-- <?php if (is_granted($module, 'delete')):?>
        <?=form_open(current_url(), array(
            'class' => 'form-xhr pull-left',
        ));?>
            <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">

            <a href="<?=site_url($module['route'] .'/delete_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-delete btn-tooltip ink-reaction" id="modal-delete-data-button">
            <i class="md md-delete"></i>
            <small class="top left">delete</small>
            </a>
        <?=form_close();?>
        <?php endif;?> -->

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
