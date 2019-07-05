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
    <div class="row">
      <div class="col-sm-6 col-lg-8">
        <h2 class="text-light">
          <img src="<?=base_url('themes/material/assets/img/logo.png');?>" height="72">
          <?=strtoupper($module['label']);?>
        </h2>
      </div>

      <div class="col-sm-6 col-lg-4">
        <div class="well">
          <div class="clearfix">
            <div class="pull-left">DOCUMENT NO.: </div>
            <div class="pull-right"><?=print_string($entity['document_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right"><?=print_date($entity['issued_date']);?></div>
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
    </div>

    <div class="row">
      <div class="col-sm-12">
        <dl class="dl-inline">
          <dt>Sent To</dt>
          <dd><?=$entity['issued_to'];?></dd>

          <dt>Released By</dt>
          <dd><?=$entity['issued_by'];?></dd>

          <dt>Packed By</dt>
          <dd><?=$entity['sent_by'];?></dd>

          <dt>Known By</dt>
          <dd><?=$entity['known_by'];?></dd>

          <dt>Approved By</dt>
          <dd><?=$entity['approved_by'];?></dd>

          <dt>Received Date</dt>
          <dd><?=print_date($entity['received_date']);?></dd>

          <dt>Received By</dt>
          <dd><?=$entity['received_by'];?></dd>

          <dt>Notes</dt>
          <dd><?=$entity['notes'];?></dd>
        </dl>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th>No</th>
                <th>Group</th>
                <th>Description</th>
                <th>P/N</th>
                <th>Alt. P/N</th>
                <th>S/N</th>
                <th>Condition</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Received Quantity</th>
                <th class="text-center">Left Quantity</th>
                <th class="text-center">Unit Value</th>
                <th>AWB Number</th>
                <th>Remark</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0;?>
              <?php $issued_quantity = array();?>
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php $n++;?>
                <tr>
                  <td class="no-space">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    <?=print_string($detail['group']);?>
                  </td>
                  <td>
                    <?=print_string($detail['description']);?>
                  </td>
                  <td>
                    <?=print_string($detail['part_number']);?>
                  </td>
                  <td>
                    <?=print_string($detail['alternate_part_number']);?>
                  </td>
                  <td>
                    <?=print_string($detail['serial_number']);?>
                  </td>
                  <td>
                    <?=print_string($detail['condition']);?>
                  </td>
                  <td>
                    <?=print_number($detail['issued_quantity'], 2);?>
                    <?php $issued_quantity[] = $detail['issued_quantity'];?>
                  </td>
                  <td>
                    <?=print_number($detail['issued_quantity']-$detail['left_received_quantity'], 2);?>
					<?php $left_received_quantity[] = $detail['left_received_quantity'];?>                    
                  </td>
                  <td>
                    <?=print_number($detail['left_received_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_string($detail['unit']);?>
                  </td>
                  <td>
                    <?=print_string($detail['insurance_currency'], 'IDR');?>
                  </td>
                  <td>
                    <?=print_number($detail['insurance_unit_value'], 2);?>
                    <?php $insurance_unit_value[] = $detail['insurance_unit_value'];?>
                  </td>
                  <td>
                    <?=print_string($detail['awb_number']);?>
                  </td>
                  <td>
                    <?=$detail['remarks'];?>
                  </td>
                </tr>
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($issued_quantity), 2);?></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
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
      <?php if (is_granted($module, 'document') && empty($entity['received_by']) && array_sum($left_received_quantity)>0):?>
        <a href="<?=site_url($module['route'] .'/receive/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-file-download"></i>
          <small class="top right">receive items</small>
        </a>
      <?php endif;?>

      <?php if (is_granted($module, 'print')):?>
        <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
          <i class="md md-print"></i>
          <small class="top right">print</small>
        </a>
      <?php endif;?>
    </div>
  </div>
</div>
