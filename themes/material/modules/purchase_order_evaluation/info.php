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
            <div class="pull-right"><?=print_string($entity['evaluation_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right"><?=print_date($entity['document_date']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">BASE: </div>
            <div class="pull-right"><?=strtoupper($entity['warehouse']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">INVENTORY: </div>
            <div class="pull-right"><?=print_string($entity['category']);?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
          <dt>Created By</dt>
          <dd><?=$entity['created_by'];?></dd>

          <dt>Status</dt>
          <dd><?=strtoupper($entity['status']);?></dd>

          <dt>Approved/Rejected By</dt>
          <dd><?=$entity['approved_by'];?></dd>

          <dt>Notes</dt>
          <dd><?=$entity['notes'];?></dd>
        </dl>
      </div>
    </div>

    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th class="middle-alignment" rowspan="2"></th>
                <th class="middle-alignment" rowspan="2">Description</th>
                <th class="middle-alignment" rowspan="2">P/N</th>                
                <th class="middle-alignment" rowspan="2">Alt. P/N</th>
                <th class="middle-alignment" rowspan="2">S/N</th> 
                <th class="middle-alignment" rowspan="2">Remarks</th>
                <th class="middle-alignment" rowspan="2">PR Number</th>
                <th class="middle-alignment" rowspan="2">Qty</th>
                <th class="middle-alignment text-center" rowspan="2" colspan="4">Vendor Detail</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0;?>
              <?php foreach ($entity['request'] as $i => $detail):?>
                <?php $n++; $rowspan=count($entity['vendors'])+1;?>
                <tr id="row_<?=$i;?>">
                  <td width="1" rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=$n;?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_string($detail['description']);?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_string($detail['part_number']);?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_string($detail['alternate_part_number']);?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_string($detail['serial_number']);?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_string($detail['remarks']);?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_string($detail['purchase_request_number']);?>
                  </td>
                  <td rowspan="<?=$rowspan;?>" class="middle-alignment">
                    <?=print_number($detail['quantity'], 2);?>
                  </td>
                  <td style="font-weight: bold;">Vendor</td>
                  <td style="font-weight: bold;">Price</td>
                  <td style="font-weight: bold;">Core Charge</td>
                  <td style="font-weight: bold;">Total</td>
                </tr>
                
                <?php foreach ($entity['vendors'] as $key => $vendor):?>
                <tr>
                    <?php
                    if ($detail['vendors'][$key]['is_selected'] == 't'){
                      $style = 'background-color: green; color: white';
                      $label = print_string($vendor['vendor']);
                    } else {
                      $style = '';
                      $label = print_string($vendor['vendor']);
                    }
                    ?>
                    <td style="<?=$style;?>">
                      <?=print_string($vendor['vendor']);?>
                    </td>
                    <td style="<?=$style;?>">
                      <?=number_format($detail['vendors'][$key]['unit_price'], 2);?>
                    </td>
                    <td style="<?=$style;?>">
                      <?=number_format($detail['vendors'][$key]['core_charge'], 2);?>
                    </td>
                    <td style="<?=$style;?>">
                      <?=number_format($detail['vendors'][$key]['total'], 2);?>
                    </td>
                </tr>
                <?php endforeach;?>
              <?php endforeach;?>
            </tbody>
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
      <?php if ((config_item('auth_role') == 'CHIEF OF MAINTANCE') && $entity['status'] == 'evaluation'):?>
        <a href="<?=site_url($module['route'] .'/approve/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-approve-data-button">
          <i class="md md-spellcheck"></i>
          <small class="top right">approve</small>
        </a>
      <?php endif;?>

      <?php if ($entity['status_edit']) : ?>
      <?php if (is_granted($module, 'document')) : ?>
        <a href="<?= site_url($module['route'] . '/edit/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-edit"></i>
          <small class="top right">edit</small>
        </a>
      <?php endif; ?>
      <?php endif; ?>

      <?php if (is_granted($module, 'print')):?>
      <?php if ($entity['status'] == 'evaluation'):?>
        <a href="<?=site_url($modules['purchase_order_evaluation']['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
          <i class="md md-print"></i>
          <small class="top right">print</small>
        </a>
      <?php else:?>
        <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
          <i class="md md-print"></i>
          <small class="top right">print</small>
        </a>
      <?php endif;?>
      <?php endif;?>
    </div>
  </div>
</div>
