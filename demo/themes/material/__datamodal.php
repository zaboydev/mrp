<div id="data-modal" class="modal fade-scale" tabindex="-1" role="dialog" aria-labelledby="modal-edit-data-label" aria-hidden="true">
  <div class="modal-dialog modal-fs" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal-edit-data-label"><?=strtoupper($module['parent']);?></h4>
      </div>

      <div class="modal-body no-padding">
      </div>

      <div class="modal-footer">
        <?php if (is_granted($module, 'delete')):?>
          <a href="" class="btn btn-icon-toggle btn-lg btn-danger ink-reaction pull-left" id="modal-delete-data-button" data-toggle="tooltip" data-original-title="delete">
            <i class="md md-delete"></i>
          </a>
        <?php endif;?>

        <?php if ($module['name'] === 'item_in_stores'):?>
          <div class="pull-left">
            <?php if (is_granted($modules['doc_usage'], 'create')):?>
              <a href="" class="btn btn-floating-action btn-primary-dark ink-reaction" id="modal-create-material-slip-data-button" data-toggle="tooltip" data-original-title="add to material slip">
                <i class="md md-launch"></i>
              </a>
            <?php endif;?>

            <?php if (is_granted($modules['doc_delivery'], 'create')):?>
              <a href="" class="btn btn-floating-action btn-primary-dark ink-reaction" id="modal-create-internal-delivery-data-button" data-toggle="tooltip" data-original-title="add to internal delivery">
                <i class="md md-assignment-returned"></i>
              </a>
            <?php endif;?>

            <?php if (is_granted($modules['doc_shipment'], 'create')):?>
              <a href="" class="btn btn-floating-action btn-primary-dark ink-reaction" id="modal-create-shipping-document-data-button" data-toggle="tooltip" data-original-title="add to shipping document">
                <i class="md md-local-shipping"></i>
              </a>
            <?php endif;?>

            <?php if (is_granted($modules['doc_return'], 'create')):?>
              <a href="" class="btn btn-floating-action btn-primary-dark ink-reaction" id="modal-create-commercial-invoice-data-button" data-toggle="tooltip" data-original-title="add to commercial invoice">
                <i class="md md-send"></i>
              </a>
            <?php endif;?>
          </div>
        <?php endif;?>

        <?php if (is_granted($module, 'print')):?>
        <a href="" class="btn btn-floating-action btn-primary ink-reaction" id="modal-print-data-button" target="_blank" data-toggle="tooltip" data-original-title="print">
          <i class="md md-print"></i>
        </a>
        <?php endif;?>

        <?php if (is_granted($module, 'mixing')):?>
          <a href="" class="btn btn-floating-action btn-primary ink-reaction" id="modal-mixing-data-button" data-toggle="tooltip" data-original-title="mixed">
            <i class="md md-loop"></i>
          </a>
        <?php endif;?>

        <?php if (is_granted($module, 'adjustment')):?>
          <a href="" class="btn btn-floating-action btn-primary ink-reaction" id="modal-adjustment-data-button" data-toggle="tooltip" data-original-title="adjustment">
            <i class="md md-swap-vert"></i>
          </a>
        <?php endif;?>

        <?php if (is_granted($module, 'edit')):?>
          <a href="" class="btn btn-floating-action btn-primary ink-reaction" id="modal-edit-data-button" data-toggle="tooltip" data-original-title="edit">
            <i class="md md-edit"></i>
          </a>
        <?php endif;?>
      </div>
    </div>
  </div>
</div>
